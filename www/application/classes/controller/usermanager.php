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
        
        if(Auth::instance()->get_user()->type->name != 'superuser') {
            Request::initial()->redirect(URL::base());
        }
        
        unset($this->templateData['right']);
        $this->template->set('templateData', $this->templateData);
    }
    
    public function action_index() {   
        $this->templateData['users'] = DB_ORM::model('user')->getAllUsers(); 
        $this->templateData['userCount'] = count($this->templateData['users']);
        $this->templateData['currentUserId'] = Auth::instance()->get_user()->id;
        $this->templateData['groups'] = DB_ORM::model('group')->getAllGroups();
        
        $view = View::factory('usermanager/view');
        $view->set('templateData', $this->templateData);
        
        $this->templateData['center'] = $view;
        $this->template->set('templateData', $this->templateData);
    }
    
    public function action_addUser() {
        $this->templateData['types'] = DB_ORM::model('user_type')->getAllTypes();
        
        $addUserView = View::factory('usermanager/addUser');
        $addUserView->set('templateData', $this->templateData);
        
        $this->templateData['center'] = $addUserView;
        $this->template->set('templateData', $this->templateData);
    }
    
    public function action_newUserSummary() {
        if($_POST) {
            Session::instance()->set('newUser', $_POST);
             
            $this->templateData['newUser'] = $_POST;
            $this->templateData['newUser']['langID'] = DB_ORM::model('language', array($_POST['langID']))->name;
            $this->templateData['newUser']['usertype'] = DB_ORM::model('user_type', array($_POST['usertype']))->name;
             
            $summaryView = View::factory('usermanager/userSummary');            
            $summaryView->set('templateData', $this->templateData);

            $this->templateData['center'] = $summaryView;
            $this->template->set('templateData', $this->templateData);
        }
    }
    
    public function action_saveNewUser() {
        $userData = Session::instance()->get('newUser', null);
        if($userData) {
            DB_ORM::model('user')->createUser($userData['uid'], $userData['upw'], $userData['uname'], 
                    $userData['uemail'], $userData['usertype'], $userData['langID']);
            Session::instance()->delete('newUser');
        }
        
        Request::initial()->redirect(URL::base().'usermanager');
    }
    
    public function action_editUser() {
        $this->templateData['user'] = DB_ORM::model('user', array($this->request->param('id', 0)));
        $this->templateData['types'] = DB_ORM::model('user_type')->getAllTypes();
        
        $editUserView = View::factory('usermanager/editUser');
        $editUserView->set('templateData', $this->templateData);
        
        $this->templateData['center'] = $editUserView;
        $this->template->set('templateData', $this->templateData);
    }
    
    public function action_saveOldUser() {
        if($_POST) {
            $userId = $this->request->param('id', 0);
            
            DB_ORM::model('user')->updateUser($userId, Arr::get($_POST, 'upw', ''), Arr::get($_POST, 'uname', ''), 
                    Arr::get($_POST, 'uemail', ''), Arr::get($_POST, 'usertype', NULL), Arr::get($_POST, 'langID', NULL));
            
        }
        Request::initial()->redirect(URL::base().'usermanager');
    }
    
    public function action_deleteUser() {
        DB_ORM::model('user', array($this->request->param('id', 0)))->delete();
        Request::initial()->redirect(URL::base().'usermanager');
    }
    
    public function action_addGroup() {
        $this->templateData['center'] = View::factory('usermanager/addGroup');
        $this->template->set('templateData', $this->templateData);
    }
    
    public function action_saveNewGroup() {
        if($_POST) {
            DB_ORM::model('group')->createGroup(Arr::get($_POST, 'groupname', 'empty_name'));
        }
        Request::initial()->redirect(URL::base().'usermanager');
    }
    
    public function action_editGroup() {
        $groupId = $this->request->param('id', 0);
        
        $this->templateData['group'] = DB_ORM::model('group', array($groupId));
        
        $this->templateData['users'] = DB_ORM::model('group')->getAllUsersOutGroup($groupId);
        $this->templateData['members'] = DB_ORM::model('group')->getAllUsersInGroup($groupId);
        
        $view = View::factory('usermanager/editGroup');
        $view->set('templateData', $this->templateData);
        
        $this->templateData['center'] = $view;
        $this->template->set('templateData', $this->templateData);
    }
    
    public function action_addMemberToGroup() {
        if($_POST) {
            DB_ORM::model('user_group')->add($this->request->param('id', 0), Arr::get($_POST, 'userid', NULL));
        }
        Request::initial()->redirect(URL::base().'usermanager/editGroup/'.$this->request->param('id', 0));
    }
    
    public function action_updateGroup() {
        if($_POST) {
            DB_ORM::model('group')->updateGroup($this->request->param('id', 0), Arr::get($_POST, 'groupname', 'empty_name'));
        }
        
        Request::initial()->redirect(URL::base().'usermanager/editGroup/'.$this->request->param('id', 0));
    }
    
    public function action_removeMember() {
        $userId = $this->request->param('id', 0);
        $groupId = $this->request->param('id2', 0);
        
        DB_ORM::model('user_group')->remove((int)$groupId, (int)$userId);
        Request::initial()->redirect(URL::base().'usermanager/editGroup/'.$groupId);
    }
}

?>
