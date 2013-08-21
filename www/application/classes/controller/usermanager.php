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

class Controller_UserManager extends Controller_Base {

    public function before() {
        parent::before();

        if (Auth::instance()->get_user()->type->name != 'superuser' && Auth::instance()->get_user()->type->name != 'author' && Auth::instance()->get_user()->type->name != 'learner') {
            Request::initial()->redirect(URL::base());
        }

        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Manage Users'))->set_url(URL::base() . 'usermanager'));

        unset($this->templateData['right']);
        $this->template->set('templateData', $this->templateData);
    }

    public function action_index() {
        $this->templateData['users'] = DB_ORM::model('user')->getAllUsersAndAuth();
        $this->templateData['userCount'] = count($this->templateData['users']);
        $this->templateData['currentUserId'] = Auth::instance()->get_user()->id;
        $this->templateData['groups'] = DB_ORM::model('group')->getAllGroups();

        $view = View::factory('usermanager/view');
        $view->set('templateData', $this->templateData);

        $this->templateData['center'] = $view;
        $this->template->set('templateData', $this->templateData);
    }

    public function action_addUser() {
        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Add User'))->set_url(URL::base() . 'usermanager/addUser'));

        $this->templateData['types'] = DB_ORM::model('user_type')->getAllTypes();
        $this->templateData['post'] = Session::instance()->get('newUser');
        $array = array('uid', 'upw', 'uname', 'uemail', 'usertype', 'langID');
        foreach ($array as $value) {
            if (!isset($this->templateData['post'][$value])) {
                $this->templateData['post'][$value] = NULL;
            }
        }
        $this->templateData['errorMsg'] = Session::instance()->get('errorMsg');
        Session::instance()->delete('errorMsg');
        $addUserView = View::factory('usermanager/addUser');
        $addUserView->set('templateData', $this->templateData);

        $this->templateData['center'] = $addUserView;
        $this->template->set('templateData', $this->templateData);
    }

    public function action_saveNewUser() {
        if (isset($_POST) && !empty($_POST)) {
            Session::instance()->set('newUser', $_POST);
            $checkUserName = DB_ORM::model('user')->getUserByName(htmlspecialchars($_POST['uid']));
            if (!empty($_POST['uemail'])) {
                $checkUserEmail = DB_ORM::model('user')->getUserByEmail(htmlspecialchars($_POST['uemail']));
            } else {
                $checkUserEmail = false;
            }

            if ((!empty($_POST['uid'])) & (!$checkUserName) & (!$checkUserEmail)) {
                $userData = $_POST;
                DB_ORM::model('user')->createUser($userData['uid'], $userData['upw'], $userData['uname'], $userData['uemail'], $userData['usertype'], $userData['langID']);
                Session::instance()->delete('newUser');

                $this->templateData['newUser'] = $_POST;
                $this->templateData['newUser']['langID'] = DB_ORM::model('language', array($_POST['langID']))->name;
                $this->templateData['newUser']['usertype'] = DB_ORM::model('user_type', array($_POST['usertype']))->name;

                $summaryView = View::factory('usermanager/userSummary');
                $summaryView->set('templateData', $this->templateData);

                $this->templateData['center'] = $summaryView;
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

    public function action_viewUser() {
        $this->templateData['user'] = DB_ORM::model('user', array($this->request->param('id', 0)));

        if (Auth::instance()->get_user()->type->name != 'superuser' && Auth::instance()->get_user()->id != $this->request->param('id', 0)) {
            Request::initial()->redirect(URL::base());
        }

        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('View User').' '. $this->templateData['user']->nickname)->set_url(URL::base() . 'usermanager/ViewUser/' . $this->request->param('id', 0)));

        $this->templateData['types'] = DB_ORM::model('user_type')->getAllTypes();
        $this->templateData['errorMsg'] = Session::instance()->get('errorMsg');
        Session::instance()->delete('errorMsg');

        $viewUserView = View::factory('usermanager/viewUser');
        $viewUserView->set('templateData', $this->templateData);

        $this->templateData['center'] = $viewUserView;
        $this->template->set('templateData', $this->templateData);
    }

    public function action_editUser() {


        $this->templateData['user'] = DB_ORM::model('user', array($this->request->param('id', 0)));

        if (Auth::instance()->get_user()->type->name != 'superuser' && Auth::instance()->get_user()->id != $this->request->param('id', 0)) {
            Request::initial()->redirect(URL::base());
        }

        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Edit User').' '. $this->templateData['user']->nickname)->set_url(URL::base() . 'usermanager/editUser/' . $this->request->param('id', 0)));

        $this->templateData['types'] = DB_ORM::model('user_type')->getAllTypes();
        $this->templateData['errorMsg'] = Session::instance()->get('errorMsg');
        Session::instance()->delete('errorMsg');

        $editUserView = View::factory('usermanager/editUser');
        $editUserView->set('templateData', $this->templateData);

        $this->templateData['center'] = $editUserView;
        $this->template->set('templateData', $this->templateData);
    }

    public function action_saveOldUser() {
        if (isset($_POST) && !empty($_POST)) {
            $userId = $this->request->param('id', 0);
            $user = DB_ORM::model('user')->getUserById(htmlspecialchars($userId));
            $userEmail = $user->email;
            $newEmail = Arr::get($_POST, 'uemail', '');

            if (Auth::instance()->get_user()->type->name != 'superuser') {
                $type = Auth::instance()->get_user()->type->id;
            }
            else {
                $type = NULL;
            }

            if ($userEmail != $newEmail) {
                if (!empty($newEmail)) {
                    $checkUserEmail = DB_ORM::model('user')->getUserByEmail(htmlspecialchars($newEmail));
                } else {
                    $checkUserEmail = false;
                }

                if (!$checkUserEmail) {
                    DB_ORM::model('user')->updateUser($userId, Arr::get($_POST, 'upw', ''), Arr::get($_POST, 'uname', ''), Arr::get($_POST, 'uemail', ''), Arr::get($_POST, 'usertype', $type), Arr::get($_POST, 'langID', NULL));
                } else {
                    Session::instance()->set('errorMsg', __('Such email address already exists.'));
                    Request::initial()->redirect(URL::base() . 'usermanager/editUser/' . $userId);
                }
            } else {
                DB_ORM::model('user')->updateUser($userId, Arr::get($_POST, 'upw', ''), Arr::get($_POST, 'uname', ''), Arr::get($_POST, 'uemail', ''), Arr::get($_POST, 'usertype', $type), Arr::get($_POST, 'langID', NULL));
            }
        }

        Model_Leap_Metadata_Record::updateMetadata("user",$userId,$_POST);
        Request::initial()->redirect(URL::base() . 'usermanager');
    }

    public function action_deleteUser() {
        DB_ORM::model('user', array($this->request->param('id', 0)))->delete();
        Request::initial()->redirect(URL::base() . 'usermanager');
    }

    public function action_addGroup() {
        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Add Group'))->set_url(URL::base() . 'usermanager/addGroup'));

        $this->templateData['center'] = View::factory('usermanager/addGroup');
        $this->template->set('templateData', $this->templateData);
    }

    public function action_saveNewGroup() {
        if (isset($_POST) && !empty($_POST)) {
            DB_ORM::model('group')->createGroup(Arr::get($_POST, 'groupname', 'empty_name'));
        }
        Request::initial()->redirect(URL::base() . 'usermanager');
    }

    public function action_editGroup() {
        $groupId = $this->request->param('id', 0);

        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Edit Group'))->set_url(URL::base() . 'usermanager/editGroup/' . $groupId));

        $this->templateData['group'] = DB_ORM::model('group', array($groupId));

        $this->templateData['users'] = DB_ORM::model('group')->getAllUsersOutGroup($groupId);
        $this->templateData['members'] = DB_ORM::model('group')->getAllUsersInGroup($groupId);

        $view = View::factory('usermanager/editGroup');
        $view->set('templateData', $this->templateData);

        $this->templateData['center'] = $view;
        $this->template->set('templateData', $this->templateData);
    }

    public function action_deleteGroup() {
        DB_ORM::model('group', array($this->request->param('id', 0)))->delete();
        Request::initial()->redirect(URL::base() . 'usermanager');
    }

    public function action_addMemberToGroup() {
        if (isset($_POST) && !empty($_POST)) {
            DB_ORM::model('user_group')->add($this->request->param('id', 0), Arr::get($_POST, 'userid', NULL));
        }
        Request::initial()->redirect(URL::base() . 'usermanager/editGroup/' . $this->request->param('id', 0));
    }

    public function action_updateGroup() {
        if (isset($_POST) && !empty($_POST)) {
            DB_ORM::model('group')->updateGroup($this->request->param('id', 0), Arr::get($_POST, 'groupname', 'empty_name'));
        }

        Request::initial()->redirect(URL::base() . 'usermanager/editGroup/' . $this->request->param('id', 0));
    }

    public function action_removeMember() {
        $userId = $this->request->param('id', 0);
        $groupId = $this->request->param('id2', 0);

        DB_ORM::model('user_group')->remove((int) $groupId, (int) $userId);
        Request::initial()->redirect(URL::base() . 'usermanager/editGroup/' . $groupId);
    }

}