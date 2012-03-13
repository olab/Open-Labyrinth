<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Home extends Controller_Base {

    public function action_index() {

        // Create admin test user
          /*$users = ORM::factory('user');
          $users->username = 'testUser222';
          $users->password = 'a123456789';
          $users->email = 'user2222@user.com';
          $users->nickname = 'userok5';
          $users->language_id = 1;
          $users->type_id = 2;
          $users->create();*/
    }

    public function action_login() {
        if ($_POST) {
            $status = Auth::instance()->login($_POST['username'], $_POST['password']);
            
            if ($status) {
                Request::initial()->redirect(URL::base());
            } else {
                $this->templateData['error'] = 'Invalid username or password';
                $this->template->set('templateData', $this->templateData);
            }
        }
    }

    public function action_logout() {
        if (Auth::instance()->logged_in()) {
            Auth::instance()->logout();
        }

        Request::initial()->redirect(URL::base());
    }

}

?>
