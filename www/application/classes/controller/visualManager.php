<?php defined('SYSPATH') or die('No direct script access.');

class Controller_VisualManager extends Controller_Base {
    
    public function action_index() {
        $mapId = $this->request->param('id', NULL);
        if($mapId != NULL) {
            $this->templateData['map'] = DB_ORM::model('map', array((int)$mapId));
            
            Model::factory('visualEditor')->generateXML((int)$mapId);

            $visualView = View::factory('labyrinth/visual');
            $visualView->set('templateData', $this->templateData);

            $leftView = View::factory('labyrinth/labyrinthEditorMenu');
            $leftView->set('templateData', $this->templateData);

            $this->templateData['left'] = $leftView;
            $this->templateData['center'] = $visualView;
            unset($this->templateData['right']);
            $this->template->set('templateData', $this->templateData);
        }
    }
    
    public function action_update() {
        $mapId = $this->request->param('id', NULL);
        $emap = Arr::get($_POST, 'emap', NULL);
        $elink = Arr::get($_POST, 'elink', NULL);
        $enode = Arr::get($_POST, 'enode', NULL);

        $this->templateData['map'] = DB_ORM::model('map', array((int)$mapId));
        
        Model::factory('visualEditor')->update($mapId, $emap, $enode, $elink);
        Model::factory('visualEditor')->generateXML((int)$mapId);
            
        $visualView = View::factory('labyrinth/visual');
        $visualView->set('templateData', $this->templateData);
        
        $leftView = View::factory('labyrinth/labyrinthEditorMenu');
        $leftView->set('templateData', $this->templateData);
            
        $this->templateData['left'] = $leftView;
        $this->templateData['center'] = $visualView;
        unset($this->templateData['right']);
        $this->template->set('templateData', $this->templateData);
    }
}
    
?>
