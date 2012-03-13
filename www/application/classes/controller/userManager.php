<?php defined('SYSPATH') or die('No direct script access.');

class Controller_UserManager extends Controller_Base {

    public function before() {
        parent::before();
        
        if(Auth::instance()->get_user()->type->name != 'superuser') {
            Request::initial()->redirect('');
        } 
    }
    
    public function action_index() {
        $users = ORM::factory('user')->find_all();
        $this->templateData['users'] = $users;
        $this->templateData['userCount'] = $users->count();
        $this->templateData['currentUserId'] = Auth::instance()->get_user()->id;
        $this->templateData['groups'] = ORM::factory('group')->find_all();
        
        $view = View::factory('usermanager/view');
        $view->set('templateData', $this->templateData);
        
        $this->templateData['center'] = $view;
        $this->template->set('templateData', $this->templateData);
    }
    
    public function action_add() {
        $this->templateData['types'] = ORM::factory('type')->find_all();
        
        $view = View::factory('usermanager/add');
        $view->set('templateData', $this->templateData);
        
        $this->templateData['center'] = $view;
        $this->template->set('templateData', $this->templateData);
    }
    
    public function action_summary() {
        if($_POST) {
            Session::instance()->set('newUser', $_POST);
            $this->templateData['newUser'] = $_POST;
             
            $this->templateData['newUser']['langID'] = ORM::factory('language', $_POST['langID'])->name;
            $this->templateData['newUser']['usertype'] = ORM::factory('type', $_POST['usertype'])->name;
            
            $view = View::factory('usermanager/summary');            
            $view->set('templateData', $this->templateData);

            $this->templateData['center'] = $view;
            $this->template->set('templateData', $this->templateData);
        }
    }
    
    public function action_saveNewUser() {
        $userData = Session::instance()->get('newUser', null);
        if($userData) {  
            ORM::factory('user')->CreateUser($userData['uid'], $userData['upw'], $userData['uname'], 
                    $userData['uemail'], $userData['usertype'], $userData['langID']);
        }
        Request::initial()->redirect(URL::base().'usermanager');
    }
    
    public function action_edit() {
        $userId = $this->request->param('id', 0);
        
        $user = ORM::factory('user', $userId);
        
        $this->templateData['user'] = $user;
        $this->templateData['types'] = ORM::factory('type')->find_all();
        
        $view = View::factory('usermanager/edit');
        $view->set('templateData', $this->templateData);
        
        $this->templateData['center'] = $view;
        $this->template->set('templateData', $this->templateData);
    }
    
    public function action_saveOldUser() {
        if($_POST) {
            $userId = $this->request->param('id', 0);
            
            ORM::factory('user')->UpdateUser($userId, Arr::get($_POST, 'upw', ''), Arr::get($_POST, 'uname', ''), 
                    Arr::get($_POST, 'uemail', ''), Arr::get($_POST, 'usertype', NULL), Arr::get($_POST, 'langID', NULL));
            
        }
        Request::initial()->redirect(URL::base().'usermanager');
    }
    
    public function action_delete() {
        ORM::factory('user', $this->request->param('id', 0))->delete();
        Request::initial()->redirect(URL::base().'usermanager');
    }
    
    public function action_addGroup() {
        $this->templateData['center'] = View::factory('usermanager/addGroup');
        $this->template->set('templateData', $this->templateData);
    }
    
    public function action_saveNewGroup() {
        if($_POST) {
            ORM::factory('group')->CreateGroup(Arr::get($_POST, 'groupname', ''));
        }
        Request::initial()->redirect(URL::base().'usermanager');
    }
    
    public function action_editGroup() {
        $groupId = $this->request->param('id', 0);
        
        $this->templateData['group'] = ORM::factory('group', $groupId);
        
        $this->templateData['members'] = ORM::factory('group', $groupId)->users->find_all();
        
        $view = View::factory('usermanager/editGroup');
        $view->set('templateData', $this->templateData);
        
        $this->templateData['center'] = $view;
        $this->template->set('templateData', $this->templateData);
    }
    
    public function action_updateGroup() {
        if($_POST) {
            ORM::factory('group')->UpdateGroup($this->request->param('id', 0), Arr::get($_POST, 'groupname', ''));
        }
        
        Request::initial()->redirect(URL::base().'usermanager/editGroup/'.$this->request->param('id', 0));
    }
    
    public function action_addMemberToGroup() {
        if($_POST) {
            ORM::factory('usersGroup')->AddUser($this->request->param('id', 0), Arr::get($_POST, 'userid', NULL));
        }
        Request::initial()->redirect(URL::base().'usermanager/editGroup/'.$this->request->param('id', 0));
    }
}