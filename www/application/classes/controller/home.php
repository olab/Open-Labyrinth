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

class Controller_Home extends Controller_Base {

    public function action_index() {
    }

    public function action_login() {
        if ($_POST) {
            $status = Auth::instance()->login($_POST['username'], $_POST['password']);
            $redirectURL = URL::base();
            if (!$status) {
                Session::instance()->set('redirectURL', (Arr::get($_POST, 'redirectURL', '')));
                Notice::add('You have entered the wrong username/password combination. Please try again.');
            } else {
                $redirectURL = URL::base() . Arr::get($_POST, 'redirectURL', '');
            }
            
            Request::initial()->redirect($redirectURL);
        }
    }

    public function action_loginOAuth() {
        $providerId = $this->request->param('id', 0);
        if($providerId > 0) {
            $provider = OAuth::factory(DB_ORM::model('oauthprovider', array((int)$providerId)));
            if($provider != null) {
                Session::instance()->set('OAuthProviderId', $providerId);
                Request::initial()->redirect($provider->getAuthorizeURL(URL::base(true, false, true) . 'home/loginOAuthCallback'));
            }
        }

        Request::initial()->redirect(URL::base());
    }

    public function action_loginOAuthCallback() {
        $providerId = Session::instance()->get('OAuthProviderId', 0);
        if($providerId > 0) {
            $provider = OAuth::factory(DB_ORM::model('oauthprovider', array((int)$providerId)));
            if($provider != null) {
                $token     = $provider->getAccessToken($_REQUEST, URL::base(true, false, true) . 'home/loginOAuthCallback');
                if($token != null) {
                    $userInfo  = $provider->get($token, 'user-info');
                    $authorize = OAuth_Authorize::factory($provider->getName());
                    if($authorize != null) {
                        $authorize->login($providerId, $userInfo);
                    }
                }
            }
        }

        Request::initial()->redirect(URL::base());
    }

    public function action_logout() {
        if (Auth::instance()->logged_in()) {
            Auth::instance()->logout();
        }
		
        Request::initial()->redirect(URL::base());
    }

    public function action_changePassword() {
        if (Auth::instance()->logged_in()) {
            $this->templateData['user'] = Auth::instance()->get_user();

            $view = View::factory('changePassword');
            $view->set('templateData', $this->templateData);

            $this->templateData['center'] = $view;
            unset($this->templateData['left']);
            unset($this->templateData['right']);
            $this->template->set('templateData', $this->templateData);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

    public function action_updatePassword() {
        if (isset($_POST) && !empty($_POST) && Auth::instance()->logged_in()) {
            Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Update Password')));

            $newPassword = Arr::get($_POST, 'newpswd', NULL);
            $confirmPassword = Arr::get($_POST, 'pswd_confirm', NULL);
            $currentPassword = Arr::get($_POST, 'upw', NULL);

            if ($currentPassword != NULL and
                    $newPassword != NULL and
                    $confirmPassword != NULL and
                    $newPassword == $confirmPassword and
                    Auth::instance()->hash($currentPassword) == Auth::instance()->get_user()->password) {
                $user = DB_ORM::model('user', array((int) Auth::instance()->get_user()->id));
                $user->password = Auth::instance()->hash($newPassword);
                $user->save();

                Request::initial()->redirect(URL::base());
            }
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

    public function action_search() {
        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Search')));

        if (isset($_POST) && !empty($_POST)) {
            $scope = Arr::get($_POST, 'scope', NULL);
            $key = Arr::get($_POST, 'searchterm', NULL);
            $title = TRUE;
            if ($scope == 'a') {
                $title = FALSE;
            }

            if ($key != NULL) {
                $maps = DB_ORM::model('map')->getSearchMap($key, $title);

                $rootNodes = array();
                if (count($maps) > 0){
                    foreach($maps as $map){
                        $rootNodes[$map->id] = DB_ORM::model('map_node')->getRootNodeByMap($map->id);
                    }
                }

                $view = View::factory('search');
                $view->set('maps', $maps);
                $view->set('term', $key);
                $view->set('rootNodes', $rootNodes);

                $this->templateData['center'] = $view;
                unset($this->templateData['right']);
                $this->template->set('templateData', $this->templateData);
            }
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

    public function action_resetPassword() {
        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Reset Password')));

        if (isset($_POST) && !empty($_POST)) {
            if (Security::check($_POST['token'])) {
                $email = htmlspecialchars($_POST['email']);
                if (!empty($email)) {
                    $user = DB_ORM::model('user')->getUserByEmail($email);
                    if ($user) {
                        $url = URL::base(true) . 'home/confirmLink/';
                        $hashKey = Auth::instance()->hash($user->username . $user->email . rand());
                        $link = $url . $hashKey;
                        //send mail start
                        $emailConfig = Kohana::$config->load('email');
                        $arraySearch = array('<%name%>', '<%username%>', '<%link%>');
                        $arrayReplace = array($user->nickname, $user->username, $link);

                        $to = $user->email;
                        $subject = $emailConfig['email_password_reset_subject'];
                        $message = str_replace($arraySearch, $arrayReplace, $emailConfig['email_password_reset_body']);
                        $from = $emailConfig['fromname'] . ' <' . $emailConfig['mailfrom'] . '>';
                        $headers = "From: " . $from;
                        mail($to, $subject, $message, $headers);

                        DB_ORM::model('user')->updateHashKeyResetPassword($user->id, $hashKey);

                        //send mail end
                        Session::instance()->set('passMessage', __('A unique link to recover your password has been sent to your registered email address. This link will only be active for 30 minutes.'));
                        Request::initial()->redirect(URL::base() . 'home/passwordMessage');
                    } else {
                        Request::initial()->redirect(URL::base());
                    }
                } else {
                    $attempt = Session::instance()->get('passAttempt') + 1;
                    Session::instance()->set('passAttempt', $attempt);
                    $isError = false;
                    if ($attempt >= 3) {
                        Session::instance()->set('passAttemptTimeStamp', time());
                        $isError = true;
                    }
                    Session::instance()->set('passError', __('The email addresses you entered do not match.'));

                    if($isError) {
                        Request::initial()->redirect(URL::base() . 'home/resetPassword');
                    } else {
                        Request::initial()->redirect(URL::base());
                    }
                }
            }
        } else {
            $attempt = Session::instance()->get('passAttempt');
            if ($attempt >= 3) {
                $timestamp = Session::instance()->get('passAttemptTimeStamp');
                $timeDiff = floor((time() - $timestamp) / 60);
                if ($timeDiff <= 20) {
                    $showDiffTime = 20 - $timeDiff;
                    if ($showDiffTime == 0) {
                        $showDiffTime = 'less 1';
                    }
                    Session::instance()->set('passMessage', 'You have exceeded the maximum number of password resets allowed. Please try again in ' . $showDiffTime . ' minutes.');
                    Request::initial()->redirect(URL::base() . 'home/passwordMessage');
                } else {
                    Session::instance()->delete('passAttempt');
                    Session::instance()->delete('passAttemptTimeStamp');
                    Session::instance()->delete('passError');
                    Request::initial()->redirect(URL::base());
                }
            } else {
                $this->templateData['passError'] = Session::instance()->get('passError');
                Session::instance()->delete('passError');
                $view = View::factory('resetPassword/view');
                $view->set('templateData', $this->templateData);

                $this->templateData['center'] = $view;
                unset($this->templateData['left']);
                unset($this->templateData['right']);
                $this->template->set('templateData', $this->templateData);
            }
        }
    }

    public function action_confirmLink() {
        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Confirm Request')));

        $hashKey = $this->request->param('id', NULL);
        if ($hashKey != NULL) {
            $user = DB_ORM::model('user')->getUserByHaskKey(htmlspecialchars($hashKey));
            if ($user) {
                $timeDiff = floor((time() - strtotime($user->resetHashKeyTime)) / 60);
                if ($timeDiff <= 30) {
                    $this->templateData['passError'] = Session::instance()->get('passError');
                    Session::instance()->delete('passError');
                    $this->templateData['hashKey'] = $hashKey;
                    $view = View::factory('resetPassword/reset');
                    $view->set('templateData', $this->templateData);

                    $this->templateData['center'] = $view;
                    unset($this->templateData['left']);
                    unset($this->templateData['right']);
                    $this->template->set('templateData', $this->templateData);
                } else {
                    Session::instance()->set('passMessage', 'Working time of the link has expired. Please repeat the password recovery procedure.');
                    Request::initial()->redirect(URL::base() . 'home/passwordMessage');
                }
            } else {
                Request::initial()->redirect(URL::base());
            }
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

    public function action_updateResetPassword() {
        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Update Password')));

        if (isset($_POST) && !empty($_POST)) {
            if (Security::check($_POST['token'])) {
                $newPassword = $_POST['newpswd'];
                $confirmPassword = $_POST['pswd_confirm'];
                $hashKey = $_POST['hashKey'];
                if (!empty($newPassword)) {
                    if ($newPassword == $confirmPassword) {
                        $user = DB_ORM::model('user')->getUserByHaskKey(htmlspecialchars($hashKey));

                        //send mail start
                        $emailConfig = Kohana::$config->load('email');
                        $arraySearch = array('<%name%>', '<%username%>');
                        $arrayReplace = array($user->nickname, $user->username);

                        $to = $user->email;
                        $subject = $emailConfig['email_password_complete_subject'];
                        $message = str_replace($arraySearch, $arrayReplace, $emailConfig['email_password_complete_body']);
                        $from = $emailConfig['fromname'] . ' <' . $emailConfig['mailfrom'] . '>';
                        $headers = "From: " . $from;

                        DB_ORM::model('user')->saveResetPassword($hashKey, Auth::instance()->hash($newPassword));
                        mail($to, $subject, $message, $headers);

                        Session::instance()->set('passMessage', 'Your new password has been saved. Please return to the login page, and login using your new password.');
                        Request::initial()->redirect(URL::base() . 'home/passwordMessage');
                    } else {
                        Session::instance()->set('passError', __('The passwords you entered do not match. Please enter your desired password in the password field and confirm your entry by entering it in the confirm password field.'));
                        Request::initial()->redirect(URL::base() . 'home/confirmLink/' . $hashKey);
                    }
                } else {
                    Session::instance()->set('passError', __('Empty password is not allowed.'));
                    Request::initial()->redirect(URL::base() . 'home/confirmLink/' . $hashKey);
                }
            } else {
                Request::initial()->redirect(URL::base());
            }
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

    public function action_passwordMessage() {
        $this->templateData['passMessage'] = Session::instance()->get('passMessage');
        if ($this->templateData['passMessage'] != NULL) {
            Session::instance()->delete('passMessage');
            $view = View::factory('resetPassword/notification');
            $view->set('templateData', $this->templateData);

            $this->templateData['center'] = $view;
            unset($this->templateData['left']);
            unset($this->templateData['right']);
            $this->template->set('templateData', $this->templateData);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

}