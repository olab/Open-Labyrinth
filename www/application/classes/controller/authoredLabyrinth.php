<?php defined('SYSPATH') or die('No direct script access.');

class Controller_AuthoredLabyrinth extends Controller_Base {
    
    public function action_index() {
        $maps = DB_ORM::model('map')->getAllEnabledAndAuthoredMap(Auth::instance()->get_user()->id);
        $this->templateData['maps'] = $maps;
        
        $openView = View::factory('labyrinth/authored');
        $openView->set('templateData', $this->templateData);
        
        $this->templateData['center'] = $openView;
        unset($this->templateData['right']);
        $this->template->set('templateData', $this->templateData);
    }
    
    public function action_info() {
        $mapId = $this->request->param('id', NULL);
        if($mapId) {
            $this->templateData['map'] = DB_ORM::model('map', array((int)$mapId));
            
            $infoView = View::factory('labyrinth/labyrinthInfo');
            $infoView->set('templateData', $this->templateData);
            
            $this->templateData['center'] = $infoView;
            unset($this->templateData['right']);
            $this->template->set('templateData', $this->templateData);
        } else {
            Request::initial()->redirect(URL::base().'openLabyrinth');
        }
    }
}
    
?>
