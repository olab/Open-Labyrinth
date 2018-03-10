<?php

/**
 * Open Labyrinth [ http://www.openlabyrinth.ca ]
 *
 * Open Labyrinth is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Open Labyrinth is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Open Labyrinth.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @copyright Copyright 2012 Open Labyrinth. All Rights Reserved.
 *
 */
defined('SYSPATH') or die('No direct script access.');

class Controller_UserManager extends Controller_Base
{

    public function before()
    {
        parent::before();

        $userType = Auth::instance()->get_user()->type->name;

        if ($userType == 'remote service' OR $userType == 'reviewer') {
            Request::initial()->redirect(URL::base());
        }

        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Manage Users'))->set_url(URL::base() . 'usermanager'));

        $this->template->set('templateData', $this->templateData);
    }

    public function action_index()
    {
        $this->templateData['users'] = DB_ORM::model('user')->getAllUsersAndAuth();
        $this->templateData['userCount'] = count($this->templateData['users']);
        $this->templateData['currentUserId'] = Auth::instance()->get_user()->id;
        $this->templateData['groups'] = DB_ORM::model('group')->getAllGroups();
        $this->templateData['center'] = View::factory('usermanager/view')->set('templateData', $this->templateData);

        $this->template->set('templateData', $this->templateData);
    }

    public function action_addUser()
    {
        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Add User'))->set_url(URL::base() . 'usermanager/addUser'));

        $this->templateData['languages'] = Model_Leap_Language::all();
        $this->templateData['types'] = DB_ORM::model('user_type')->getAllTypes();
        $this->templateData['post'] = Session::instance()->get('newUser');
        $array = array('uid', 'upw', 'uname', 'uemail', 'usertype', 'langID');
        foreach ($array as $value) {
            if (!isset($this->templateData['post'][$value])) {
                $this->templateData['post'][$value] = null;
            }
        }
        $this->templateData['errorMsg'] = Session::instance()->get('errorMsg');
        Session::instance()->delete('errorMsg');
        $addUserView = View::factory('usermanager/addUser');
        $addUserView->set('templateData', $this->templateData);

        $this->templateData['center'] = $addUserView;
        $this->template->set('templateData', $this->templateData);
    }

    private function sendNewUserMail($userData)
    {
        $URL = URL::base('http', true);
        $typeName = DB_ORM::model('user_type', array($userData['usertype']));
        $langName = DB_ORM::model('language', array($userData['langID']));

        require_once(DOCROOT . 'application/classes/class.phpmailer.php');
        $mail = new PHPMailer;

        $mail->From = 'no.reply@' . $_SERVER['HTTP_HOST'];
        $mail->FromName = 'OpenLabyrinth';

        $mail->Subject = 'Your account has been created';

        $mail_body = 'Welcome to OpenLabyrinth, ' . $userData['uname'] . '!' . PHP_EOL . PHP_EOL;
        $mail_body .= 'Here is information about your account:' . PHP_EOL;
        $mail_body .= '---------------------------------------' . PHP_EOL;
        $mail_body .= 'Username: ' . $userData['uid'] . PHP_EOL;
        $mail_body .= 'Password: ' . $userData['upw'] . PHP_EOL;
        $mail_body .= 'Full name: ' . $userData['uname'] . PHP_EOL;
        $mail_body .= 'Language: ' . $langName->name . PHP_EOL;
        $mail_body .= 'User type: ' . $typeName->name . PHP_EOL;
        $mail_body .= '---------------------------------------' . PHP_EOL;
        $mail_body .= 'URL to the home page: ' . $URL;

        $mail->Body = $mail_body;

        if (!empty($userData['uemail'])) {
            $mail->AddAddress($userData['uemail']);
            $mail->Send();
        }
    }

    public function action_saveNewUser()
    {
        if (!empty($_POST)) {
            Session::instance()->set('newUser', $_POST);
            $checkUserName = DB_ORM::model('user')->getUserByName(htmlspecialchars($_POST['uid']));
            $checkUserEmail = empty($_POST['uemail'])
                ? false
                : DB_ORM::model('user')->getUserByEmail(htmlspecialchars($_POST['uemail']));

            if ((!empty($_POST['uid'])) & (!$checkUserName) & (!$checkUserEmail)) {
                $userData = $_POST;

                DB_ORM::model('user')->createUser($userData['uid'], $userData['upw'], $userData['uname'],
                    $userData['uemail'], $userData['usertype'], $userData['langID'], $userData['uiMode']);

                if (isset($userData['sendEmail'])) {
                    $this->sendNewUserMail($userData);
                }

                Session::instance()->delete('newUser');

                $this->templateData['newUser'] = $_POST;
                $this->templateData['newUser']['langID'] = DB_ORM::model('language', array($_POST['langID']))->name;
                $this->templateData['newUser']['usertype'] = DB_ORM::model('user_type',
                    array($_POST['usertype']))->name;
                $this->templateData['center'] = View::factory('usermanager/userSummary')->set('templateData',
                    $this->templateData);
                $this->template->set('templateData', $this->templateData);
            } else {
                $error = array();
                if (empty($_POST['uid'])) {
                    $error[] = __('Empty username is not allowed.');
                } elseif ($checkUserName) {
                    $error[] = __('Such username already exists.');
                }
                if ($checkUserEmail) {
                    $error[] = __('Such email address already exists.');
                }
                Session::instance()->set('errorMsg', implode('<br />', $error));
                Request::initial()->redirect(URL::base() . 'usermanager/addUser');
            }
        }
    }

    public function action_viewUser()
    {
        $this->manageUser('view');
    }

    public function action_editUser()
    {
        $this->manageUser('edit');
    }

    public function manageUser($action)
    {
        $userId = $this->request->param('id', 0);
        $user = DB_ORM::model('User', array($userId));
        $loggedUser = Auth::instance()->get_user();
        $loggedUserType = $loggedUser->type->name;

        if (!($loggedUserType == 'superuser' OR $loggedUserType == 'Director') AND $loggedUser->id != $userId) {
            Request::initial()->redirect(URL::base());
        }

        $this->templateData['user'] = $user;
        $this->templateData['languages'] = Model_Leap_Language::all();
        $this->templateData['types'] = DB_ORM::select('user_type')->query()->as_array();
        $this->templateData['errorMsg'] = Session::instance()->get('errorMsg');
        Session::instance()->delete('errorMsg');

        switch ($action) {
            case 'edit':
                Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Edit User') . ' ' . $user->nickname)->set_url(URL::base() . 'usermanager/editUser/' . $userId));
                $this->templateData['userType'] = DB_ORM::model('user_type', array($user->type_id))->name;
                $this->templateData['center'] = View::factory('usermanager/editUser')->set('templateData',
                    $this->templateData);
                break;
            case 'view':
                Breadcrumbs::add(Breadcrumb::factory()->set_title(__('View User') . ' ' . $user->nickname)->set_url(URL::base() . 'usermanager/ViewUser/' . $userId));
                $this->templateData['center'] = View::factory('usermanager/viewUser')->set('templateData',
                    $this->templateData);
                break;
            default:
                Request::initial()->redirect(URL::base());
        }

        $this->template->set('templateData', $this->templateData);
    }

    public function action_saveOldUser()
    {
        if (!empty($_POST)) {
            $userId = $this->request->param('id', 0);
            $user = DB_ORM::model('user')->getUserById(htmlspecialchars($userId));
            $userEmail = $user->email;
            $newEmail = Arr::get($_POST, 'uemail', '');

            if (Auth::instance()->get_user()->type->name != 'superuser') {
                $type = Auth::instance()->get_user()->type->id;
            } else {
                $type = null;
            }

            if ($userEmail != $newEmail) {
                if (!empty($newEmail)) {
                    $checkUserEmail = DB_ORM::model('user')->getUserByEmail(htmlspecialchars($newEmail));
                } else {
                    $checkUserEmail = false;
                }

                if (!$checkUserEmail) {
                    DB_ORM::model('user')->updateUser($userId, Arr::get($_POST, 'upw', ''),
                        Arr::get($_POST, 'uname', ''), Arr::get($_POST, 'uemail', ''),
                        Arr::get($_POST, 'usertype', $type), Arr::get($_POST, 'langID', null));
                } else {
                    Session::instance()->set('errorMsg', __('Such email address already exists.'));
                    Request::initial()->redirect(URL::base() . 'usermanager/editUser/' . $userId);
                }
            } else {
                DB_ORM::model('user')->updateUser($userId, Arr::get($_POST, 'upw', ''), Arr::get($_POST, 'uname', ''),
                    Arr::get($_POST, 'uemail', ''), Arr::get($_POST, 'usertype', $type),
                    Arr::get($_POST, 'langID', null));
            }
        }

        if ($userId == Auth::instance()->get_user()->id) {
            Session::instance()->set('user_was_updated', true);
        }

        Model_Leap_Metadata_Record::updateMetadata("user", $userId, $_POST);
        Request::initial()->redirect(URL::base() . 'usermanager');
    }

    public function action_deleteUser()
    {
        DB_ORM::model('user', array($this->request->param('id', 0)))->delete();
        Request::initial()->redirect(URL::base() . 'usermanager');
    }

    public function action_addGroup()
    {
        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Add Group'))->set_url(URL::base() . 'usermanager/addGroup'));

        $this->templateData['center'] = View::factory('usermanager/addGroup');
        $this->template->set('templateData', $this->templateData);
    }

    public function action_saveNewGroup()
    {
        if (isset($_POST) && !empty($_POST)) {
            DB_ORM::model('group')->createGroup(Arr::get($_POST, 'groupname', 'empty_name'));
        }
        Request::initial()->redirect(URL::base() . 'usermanager');
    }

    public function action_editGroup()
    {
        $groupId = $this->request->param('id', 0);

        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Edit Group'))->set_url(URL::base() . 'usermanager/editGroup/' . $groupId));

        $this->templateData['group'] = DB_ORM::model('group', array($groupId));
        $this->templateData['users'] = DB_ORM::model('group')->getAllUsersOutGroup($groupId);
        $this->templateData['members'] = DB_ORM::model('group')->getAllUsersInGroup($groupId);
        $this->templateData['center'] = View::factory('usermanager/editGroup')->set('templateData',
            $this->templateData);
        $this->template->set('templateData', $this->templateData);
    }

    public function action_deleteGroup()
    {
        DB_ORM::model('group', array($this->request->param('id', 0)))->delete();
        Request::initial()->redirect(URL::base() . 'usermanager');
    }

    public function action_addMemberToGroup()
    {
        if (isset($_POST) && !empty($_POST)) {
            DB_ORM::model('user_group')->add($this->request->param('id', 0), Arr::get($_POST, 'userid', null));
        }
        Request::initial()->redirect(URL::base() . 'usermanager/editGroup/' . $this->request->param('id', 0));
    }

    public function action_updateGroup()
    {
        if (isset($_POST) && !empty($_POST)) {
            DB_ORM::model('group')->updateGroup($this->request->param('id', 0),
                Arr::get($_POST, 'groupname', 'empty_name'));
        }

        Request::initial()->redirect(URL::base() . 'usermanager/editGroup/' . $this->request->param('id', 0));
    }

    public function action_removeMember()
    {
        $userId = $this->request->param('id', 0);
        $groupId = $this->request->param('id2', 0);

        DB_ORM::model('user_group')->remove((int)$groupId, (int)$userId);
        Request::initial()->redirect(URL::base() . 'usermanager/editGroup/' . $groupId);
    }

}