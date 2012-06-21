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

class Controller_AvatarManager extends Controller_Base {
    
    public function action_index() {
        $mapId = $this->request->param('id', NULL);
        if($mapId != NULL) {
            $this->templateData['map'] = DB_ORM::model('map', array((int)$mapId));
            $this->templateData['avatars'] = DB_ORM::model('map_avatar')->getAvatarsByMap((int)$mapId);
            
            $avatarView = View::factory('labyrinth/avatar/view');
            $avatarView->set('templateData', $this->templateData);
        
            $leftView = View::factory('labyrinth/labyrinthEditorMenu');
            $leftView->set('templateData', $this->templateData);
            
            $this->templateData['left'] = $leftView;
            $this->templateData['center'] = $avatarView;
            unset($this->templateData['right']);
            $this->template->set('templateData', $this->templateData);
        } else {
             Request::initial()->redirect("home");
        }
    }
    
    public function action_addAvatar() {
        $mapId = $this->request->param('id', NULL);
        if($mapId != NULL) {
            $avatarId = DB_ORM::model('map_avatar')->addAvatar($mapId);
            Request::initial()->redirect('avatarManager/editAvatar/'.$mapId.'/'.$avatarId);
        } else {
             Request::initial()->redirect("home");
        }
    }
    
    public function action_editAvatar() {
        $mapId = $this->request->param('id', NULL);
        $avatarId = $this->request->param('id2', NULL);
        if($mapId != NULL and $avatarId != NULL) {
            $this->templateData['map'] = DB_ORM::model('map', array((int)$mapId));
            $this->templateData['avatar'] = DB_ORM::model('map_avatar', array((int)$avatarId));
            
            $edtAvatarView = View::factory('labyrinth/avatar/edit');
            $edtAvatarView->set('templateData', $this->templateData);
        
            $leftView = View::factory('labyrinth/labyrinthEditorMenu');
            $leftView->set('templateData', $this->templateData);
            
            $this->templateData['left'] = $leftView;
            $this->templateData['center'] = $edtAvatarView;
            unset($this->templateData['right']);
            $this->template->set('templateData', $this->templateData);
        } else {
             Request::initial()->redirect("home");
        }
    }
    
    public function action_deleteAvatar() {
        $mapId = $this->request->param('id', NULL);
        $avatarId = $this->request->param('id2', NULL);
        if($mapId != NULL and $avatarId != NULL) {
            DB_ORM::model('map_avatar', array((int)$avatarId))->delete();
            Request::initial()->redirect('avatarManager/index/'.$mapId);
        } else {
             Request::initial()->redirect("home");
        }
    }
    
    public function action_updateAvatar() {
        $mapId = $this->request->param('id', NULL);
        $avatarId = $this->request->param('id2', NULL);
        if($_POST and $mapId != NULL and $avatarId != NULL) {
            DB_ORM::model('map_avatar')->updateAvatar($avatarId, $_POST);
            Request::initial()->redirect('avatarManager/editAvatar/'.$mapId.'/'.$avatarId);
        } else {
             Request::initial()->redirect("home");
        }
    }
    
    public function action_duplicateAvatar() {
        $mapId = $this->request->param('id', NULL);
        $avatarId = $this->request->param('id2', NULL);
        if($mapId != NULL and $avatarId != NULL) {
            DB_ORM::model('map_avatar')->duplicateAvatar($avatarId);
            Request::initial()->redirect('avatarManager/index/'.$mapId);
        } else {
             Request::initial()->redirect("home");
        }
    }
    
}
    
?>
