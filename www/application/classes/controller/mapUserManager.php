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

class Controller_MapUserManager extends Controller_Base {
    
    public function action_index() {
        $mapId = $this->request->param('id', NULL);
        if($mapId != NULL) {
            $this->templateData['map'] = DB_ORM::model('map', array((int)$mapId));
            
            $userIds = DB_ORM::model('map_user')->getAllUsersIds((int)$mapId);
            $this->templateData['existUsers'] = DB_ORM::model('map_user')->getAllUsers((int)$mapId);
            
            $this->templateData['admins'] = DB_ORM::model('user')->getUsersByTypeName('superuser', $userIds);
            $this->templateData['authors'] = DB_ORM::model('user')->getUsersByTypeName('author', $userIds);
            $this->templateData['learners'] = DB_ORM::model('user')->getUsersByTypeName('learner', $userIds);
            
            $mapUserView = View::factory('labyrinth/user/view');
            $mapUserView->set('templateData', $this->templateData);
        
            $leftView = View::factory('labyrinth/labyrinthEditorMenu');
            $leftView->set('templateData', $this->templateData);
            
            $this->templateData['left'] = $leftView;
            $this->templateData['center'] = $mapUserView;
            unset($this->templateData['right']);
            $this->template->set('templateData', $this->templateData);
        } else {
            Request::initial()->redirect("home");
        }
    }
    
    public function action_addUser() {
        $mapId = $this->request->param('id', NULL);
        if($_POST and $mapId != NULL) {
            DB_ORM::model('map_user')->addUser($mapId, Arr::get($_POST, 'mapuserID', NULL));
            Request::initial()->redirect(URL::base().'mapUserManager/index/'.$mapId);
        } else {
            Request::initial()->redirect("home");
        }
    }
    
    public function action_deleteUser() {
        $mapId = $this->request->param('id', NULL);
        $userId = $this->request->param('id2', NULL);
        if($mapId != NULL) {
            DB_ORM::model('map_user')->deleteByUserId($mapId, $userId);
            Request::initial()->redirect(URL::base().'mapUserManager/index/'.$mapId);
        } else {
            Request::initial()->redirect("home");
        }
    }
}
    
?>
