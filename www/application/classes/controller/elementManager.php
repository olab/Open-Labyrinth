<?php defined('SYSPATH') or die('No direct script access.');

class Controller_ElementManager extends Controller_Base {
    
    public function action_index() {
        $mapId = $this->request->param('id', NULL);
        
        if($mapId != NULL) {
            $map = DB_ORM::model('map', array((int)$mapId));
            
            $this->templateData['map'] = $map;
            $this->templateData['vpds'] = DB_ORM::model('map_vpd')->getAllVpdByMap($map->id);

            $view = View::factory('labyrinth/element/view');
            $view->set('templateData', $this->templateData);

            $leftView = View::factory('labyrinth/labyrinthEditorMenu');
            $leftView->set('templateData', $this->templateData);

            $this->templateData['left'] = $leftView;
            $this->templateData['center'] = $view;
            unset($this->templateData['right']);
            $this->template->set('templateData', $this->templateData);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }
    
    public function action_addNewElement() {
        $mapId = $this->request->param('id', NULL);
        $type = $this->request->param('id2', NULL);
        
        if($mapId != NULL) {
            $map = DB_ORM::model('map', array((int)$mapId));
            $this->templateData['map'] = $map;
            
            if($type != NULL) {
                $this->templateData['add_type'] = $type;
                $this->templateData['files'] = DB_ORM::model('map_element')->getAllMediaFiles((int)$mapId);
            }
            
            $this->templateData['types'] = DB_ORM::model('map_vpd_type')->getAllTypes();

            $view = View::factory('labyrinth/element/add');
            $view->set('templateData', $this->templateData);

            $leftView = View::factory('labyrinth/labyrinthEditorMenu');
            $leftView->set('templateData', $this->templateData);

            $this->templateData['left'] = $leftView;
            $this->templateData['center'] = $view;
            unset($this->templateData['right']);
            $this->template->set('templateData', $this->templateData);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }
    
    public function action_saveElement() {
        $mapId = $this->request->param('id', NULL);
        $type = $this->request->param('id2', NULL);
        
        if($_POST and $mapId != NULL and $type != NULL) {
            DB_ORM::model('map_vpd')->createNewElement($mapId, $type, $_POST);
            Request::initial()->redirect(URL::base().'elementManager/index/'.$mapId);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }
    
    public function action_updateElement() {
        $mapId = $this->request->param('id', NULL);
        $vpdId = $this->request->param('id2', NULL);
        
        if($_POST and $mapId != NULL and $vpdId != NULL) {
            DB_ORM::model('map_vpd_element')->saveElementValues($vpdId, $_POST);
            Request::initial()->redirect(URL::base().'elementManager/index/'.$mapId);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }
    
    public function action_deleteVpd() {
        $mapId = $this->request->param('id', NULL);
        $vpdId = $this->request->param('id2', NULL);
        
        if($mapId != NULL and $vpdId != NULL) {
            DB_ORM::model('map_vpd', array((int)$vpdId))->delete();
            Request::initial()->redirect(URL::base().'elementManager/index/'.$mapId);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }
    
    public function action_editVpd() {
        $mapId = $this->request->param('id', NULL);
        $vpdId = $this->request->param('id2', NULL);
        
        if($mapId != NULL and $vpdId != NULL) {
            $map = DB_ORM::model('map', array((int)$mapId));
            $this->templateData['map'] = $map;
            
            $this->templateData['vpd'] = DB_ORM::model('map_vpd', array((int)$vpdId));
            $this->templateData['files'] = DB_ORM::model('map_element')->getAllMediaFiles((int)$mapId);
            
            $this->templateData['types'] = DB_ORM::model('map_vpd_type')->getAllTypes();

            $view = View::factory('labyrinth/element/edit');
            $view->set('templateData', $this->templateData);

            $leftView = View::factory('labyrinth/labyrinthEditorMenu');
            $leftView->set('templateData', $this->templateData);

            $this->templateData['left'] = $leftView;
            $this->templateData['center'] = $view;
            unset($this->templateData['right']);
            $this->template->set('templateData', $this->templateData);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }
}
    
?>
