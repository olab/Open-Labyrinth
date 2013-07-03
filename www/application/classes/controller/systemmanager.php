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

class Controller_SystemManager extends Controller_Base {

    public function before() {
        parent::before();

        if (Auth::instance()->get_user()->type->name != 'superuser') {
            Request::initial()->redirect(URL::base());
        }

        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('System Settings'))->set_url(URL::base() . 'systemmanager'));

        unset($this->templateData['right']);
        $this->template->set('templateData', $this->templateData);
    }

    public function action_index() {
        $this->templateData['token'] = Security::token();
        $this->templateData['email_config'] = Kohana::$config->load('email');
        $viewPasswordReset = View::factory('systemmanager/passwordReset');
        $viewPasswordReset->set('templateData', $this->templateData);
        $this->templateData['tabsName'][0] = __('Password Recovery Settings');
        $this->templateData['tabs'][0] = $viewPasswordReset;

        $this->templateData['media_copyright'] = Kohana::$config->load('media_upload_copyright');
        $viewCopyright = View::factory('systemmanager/mediaUploadCopyright');
        $viewCopyright->set('templateData', $this->templateData);
        $this->templateData['tabsName'][1] = __('Media Upload - Copyright Notice');
        $this->templateData['tabs'][1] = $viewCopyright;

        $this->templateData['support'] = Kohana::$config->load('support');
        $viewSupport = View::factory('systemmanager/support');
        $viewSupport->set('templateData', $this->templateData);
        $this->templateData['tabsName'][2] = __("Support");
        $this->templateData['tabs'][2] = $viewSupport;

        $this->templateData['oauthProviders'] = DB_ORM::model('oauthprovider')->getAll();
        $viewSupport = View::factory('systemmanager/oauth');
        $viewSupport->set('templateData', $this->templateData);
        $this->templateData['tabsName'][3] = __("OAuth");
        $this->templateData['tabs'][3] = $viewSupport;

        $view = View::factory('systemmanager/view');
        $view->set('templateData', $this->templateData);

        $this->templateData['center'] = $view;
        $this->template->set('templateData', $this->templateData);
    }

    public function action_updatePasswordResetSettings() {
        if ($_POST) {
            if (Security::check($_POST['token'])) {
                unset($_POST['token']);
                $string = 'return array (';
                foreach ($_POST as $key => $value) {
                    $value = str_replace('"', '\"', $value);
                    $string .= '"' . $key . '" => "' . $value . '", ';
                }
                $string .= ');';

                $content = '';
                $handle = fopen(DOCROOT . 'application/config/email.php', 'r');
                while (($buffer = fgets($handle)) !== false) {
                    $content .= $buffer;
                }

                $position = strpos($content, 'return array');
                $header = substr($content, 0, $position);

                file_put_contents(DOCROOT . 'application/config/email.php', $header . $string);

                Request::initial()->redirect(URL::base() . 'systemManager');
            } else {
                Request::initial()->redirect(URL::base());
            }
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

    public function action_updateMediaUploadCopyright() {
        if ($_POST) {
            if (Security::check($_POST['token'])) {
                unset($_POST['token']);
                $string = 'return array (';
                foreach ($_POST as $key => $value) {
                    $value = str_replace('"', '\"', $value);
                    $string .= '"' . $key . '" => "' . $value . '", ';
                }
                $string .= ');';

                $content = '';
                $handle = fopen(DOCROOT . 'application/config/media_upload_copyright.php', 'r');
                while (($buffer = fgets($handle)) !== false) {
                    $content .= $buffer;
                }

                $position = strpos($content, 'return array');
                $header = substr($content, 0, $position);

                file_put_contents(DOCROOT . 'application/config/media_upload_copyright.php', $header . $string);

                Request::initial()->redirect(URL::base() . 'systemManager#tabs-1');
            } else {
                Request::initial()->redirect(URL::base());
            }
        } else {
            Request::initial()->redirect(URL::base());
        }
    }
     
    public function action_updateSupportEmails() {
        if($_POST) {
            if(Security::check($_POST['token'])) {
                unset($_POST['token']);
                $fromEmail = Arr::get($_POST, 'supportEmail', '');
                $emails = Arr::get($_POST, 'emails', '');
                $content = '';
                $handle = fopen(DOCROOT . 'application/config/support.php', 'r');
                while (($buffer = fgets($handle)) !== false) {
                    $content .= $buffer;
                }

                $content = preg_replace("/'email' => '[\w\s\.,_@]*'/", "'email' => '" . $emails . "'", $content);
                $content = preg_replace("/'main_support_email' => '[\w\s\.,_@]*'/", "'main_support_email' => '" . $fromEmail . "'", $content);

                file_put_contents(DOCROOT . 'application/config/support.php', $content);

                Request::initial()->redirect(URL::base() . 'systemManager#tabs-4');
            } else {
                Request::initial()->redirect(URL::base());
            }
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

    public function action_saveOAuth() {
        if($_POST) {
            $providers = DB_ORM::model('oauthprovider')->getAll();
            if($providers != null && count($providers) > 0) {
                foreach($providers as $provider) {
                    $appId  = Arr::get($_POST, 'appId'  . $provider->id, null);
                    $secret = Arr::get($_POST, 'secret' . $provider->id, null);

                    DB_ORM::model('oauthprovider')->updateData($provider->id, $appId, $secret);
                }
            }
        }

        Request::initial()->redirect(URL::base() . 'systemManager');
    }

    public function action_updateTodayTip() {
        if ($_POST) {
            if (Security::check($_POST['token'])) {
                unset($_POST['token']);
                $string = 'return array (';
                foreach ($_POST as $key => $value) {
                    $value = str_replace('"', '\"', $value);
                    $string .= '"' . $key . '" => "' . $value . '", ';
                }
                $string .= ');';

                $content = '';
                $handle = fopen(DOCROOT . 'application/config/today_tip.php', 'r');
                while (($buffer = fgets($handle)) !== false) {
                    $content .= $buffer;
                }

                $position = strpos($content, 'return array');
                $header = substr($content, 0, $position);

                file_put_contents(DOCROOT . 'application/config/today_tip.php', $header . $string);

                Request::initial()->redirect(URL::base() . 'systemManager#tabs-4');
            } else {
                Request::initial()->redirect(URL::base());
            }
        } else {
            Request::initial()->redirect(URL::base());
        }
    }
}

?>