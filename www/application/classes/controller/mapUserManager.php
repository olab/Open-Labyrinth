<?php defined('SYSPATH') or die('No direct script access.');

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
            Request::initial()->redirect(URL::base());
        }
    }
    
    public function action_addUser() {
        $mapId = $this->request->param('id', NULL);
        if($_POST and $mapId != NULL) {
            DB_ORM::model('map_user')->addUser($mapId, Arr::get($_POST, 'mapuserID', NULL));
            Request::initial()->redirect(URL::base().'mapUserManager/index/'.$mapId);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }
    
    public function action_deleteUser() {
        $mapId = $this->request->param('id', NULL);
        $userId = $this->request->param('id2', NULL);
        if($mapId != NULL) {
            DB_ORM::model('map_user')->deleteByUserId($mapId, $userId);
            Request::initial()->redirect(URL::base().'mapUserManager/index/'.$mapId);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }
}
    
?>
