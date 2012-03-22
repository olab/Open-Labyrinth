<?php defined('SYSPATH') or die('No direct script access.');

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
             Request::initial()->redirect(URL::base());
        }
    }
    
    public function action_addAvatar() {
        $mapId = $this->request->param('id', NULL);
        if($mapId != NULL) {
            $avatarId = DB_ORM::model('map_avatar')->addAvatar($mapId);
            Request::initial()->redirect(URL::base().'avatarManager/editAvatar/'.$mapId.'/'.$avatarId);
        } else {
             Request::initial()->redirect(URL::base());
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
             Request::initial()->redirect(URL::base());
        }
    }
    
    public function action_deleteAvatar() {
        $mapId = $this->request->param('id', NULL);
        $avatarId = $this->request->param('id2', NULL);
        if($mapId != NULL and $avatarId != NULL) {
            DB_ORM::model('map_avatar', array((int)$avatarId))->delete();
            Request::initial()->redirect(URL::base().'avatarManager/index/'.$mapId);
        } else {
             Request::initial()->redirect(URL::base());
        }
    }
    
    public function action_updateAvatar() {
        $mapId = $this->request->param('id', NULL);
        $avatarId = $this->request->param('id2', NULL);
        if($_POST and $mapId != NULL and $avatarId != NULL) {
            DB_ORM::model('map_avatar')->updateAvatar($avatarId, $_POST);
            Request::initial()->redirect(URL::base().'avatarManager/editAvatar/'.$mapId.'/'.$avatarId);
        } else {
             Request::initial()->redirect(URL::base());
        }
    }
    
    public function action_duplicateAvatar() {
        $mapId = $this->request->param('id', NULL);
        $avatarId = $this->request->param('id2', NULL);
        if($mapId != NULL and $avatarId != NULL) {
            DB_ORM::model('map_avatar')->duplicateAvatar($avatarId);
            Request::initial()->redirect(URL::base().'avatarManager/index/'.$mapId);
        } else {
             Request::initial()->redirect(URL::base());
        }
    }
    
}
    
?>
