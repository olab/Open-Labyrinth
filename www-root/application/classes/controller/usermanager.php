<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * User manager controller using for managers user only admin(role = 'admin') can use this controller 
 */
class Controller_UserManager extends Controller_MainDesign {
    /**
     * Function call before main action
     */
    public function before() {
        if (!Auth::instance()->logged_in('admin')) {
            Request::initial()->redirect('');
        }

        return parent::before();
    }

    /**
     * Action index view main user manager panel 
     */
    public function action_index() {
        $view = View::factory('usermanager/view');

        $view->set('usercount', ORM::factory('user')->count_all());
        $view->set('users', ORM::factory('user')->find_all());

        $this->template->center = $view;
    }

    /**
     * Action for work in editable mode with user 
     */
    public function action_editView() {
        $userId = $this->request->param('id', 0);
        $view = View::factory('usermanager/edit');

        $view->set('userId', $userId);
        $view->set('roles', ORM::factory('role')->where('id' , '<>' , 1)->find_all());
        $view->set('langs', ORM::factory('language')->find_all());

        $this->template->center = $view;
    }

    /**
     * Avtion for saving change for user 
     */
    public function action_editSave() {
        $user = ORM::factory('user')->where('id', '=', $this->request->param('id', 0))->find();
        if ($user) {
            if(isset($_POST['upw']) && $_POST['upw'] != '') {
                $user->password = $_POST['upw'];
            }
            
            if(isset($_POST['uname']) && $_POST['uname'] != '') {
                $user->username = $_POST['uname'];
            }
            
            if(isset($_POST['udname']) && $_POST['udname'] != '') {
                $user->displayname = $_POST['udname'];
            }
            
            if(isset($_POST['uemail']) && $_POST['uemail'] != '') {
                $user->email = $_POST['uemail'];
            }
            
            if(isset($_POST['usertype']) && $_POST['usertype'] != '') {
                foreach($user->roles->find_all() as $role) {
                    $user->remove('roles', $role);
                }
                
                $user->add('roles', ORM::factory('role', 1)); // login type
                $user->add('roles', ORM::factory('role', $_POST['usertype']));
            }
            
            if(isset($_POST['userlang']) && $_POST['userlang'] != '') {
                $user->language_id = $_POST['userlang'];
            }
            
            $user->update();
        }

        Request::initial()->redirect('index.php/usermanager');
    }

    /**
     * Action for deleting user 
     */
    public function action_delete() {
        ORM::factory('user', $this->request->param('id', 0))->delete();
        Request::initial()->redirect('index.php/usermanager');
    }

    /**
     * Action for work in adding new user 
     */
    public function action_addUserView() {
        $view = View::factory('usermanager/add');
        
        $view->set('roles', ORM::factory('role')->where('id', '<>', 1)->find_all());
        $view->set('langs', ORM::factory('language')->find_all());
        
        $this->template->center = $view;
    }

    /**
     * Action for add new user
     */
    public function action_addNewUser() {
        $users = ORM::factory('user');
        if ($users) {
            if(isset($_POST['upw']) && $_POST['upw'] != '') {
                $users->password = $_POST['upw'];
            }
            
            if(isset($_POST['uname']) && $_POST['uname'] != '') {
                $users->username = $_POST['uname'];
            }
            
            if(isset($_POST['udname']) && $_POST['udname'] != '') {
                $users->displayname = $_POST['udname'];
            }
            
            if(isset($_POST['uemail']) && $_POST['uemail'] != '') {
                $users->email = $_POST['uemail'];
            }
            
            if(isset($_POST['userlang']) && $_POST['userlang'] != '') {
                $users->language_id = $_POST['userlang'];
            }
            
            $users->create();
            $users->add('roles', ORM::factory('role', 1)); // login type
            
            if(isset($_POST['usertype']) && $_POST['usertype'] != '') {               
                $users->add('roles', ORM::factory('role', $_POST['usertype']));
            }
            
            Request::initial()->redirect('index.php/usermanager');
        }
    }
}