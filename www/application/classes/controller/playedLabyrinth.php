<?php defined('SYSPATH') or die('No direct script access.');

class Controller_PlayedLabyrinth extends Controller_Base {
    
    public function action_index() {
        $sessions = DB_ORM::model('user_session')->getAllSessionByUser(Auth::instance()->get_user()->id);
        $mapIDs = array();
        if(count($sessions) > 0) {
            foreach($sessions as $s) {
                $mapIDs[] = $s->map_id;
            }
        }
        
        if(count($mapIDs) > 0) {
            $this->templateData['maps'] = DB_ORM::model('map')->getMapsIn($mapIDs);
        }
        
        $openView = View::factory('labyrinth/played');
        $openView->set('templateData', $this->templateData);
        
        $this->templateData['center'] = $openView;
        unset($this->templateData['right']);
        $this->template->set('templateData', $this->templateData);
    }
    
    public function action_showMapInfo() {
        $mapId = $this->request->param('id', NULL);
        if($mapId != NULL) {
            $sessions = DB_ORM::model('user_session')->getAllSessionByUser(Auth::instance()->get_user()->id);
            $this->templateData['sessions'] = $sessions;
			
			if(count($this->templateData['sessions']) > 0) {
				foreach($this->templateData['sessions'] as $session) {
					$bookmark = DB_ORM::model('user_bookmark')->getBookmark($session->id);
					if($bookmark != NULL) {
						$this->templateData['bookmarks'][$session->id] = $bookmark;
					}
				}
			}
			
            $openView = View::factory('labyrinth/sessionMapInfo');
            $openView->set('templateData', $this->templateData);

            $this->templateData['center'] = $openView;
            unset($this->templateData['right']);
            $this->template->set('templateData', $this->templateData);
        } else {
            Request::initial()->redirect(URL::base().'openLabyrinth');
        }
    }
}
    
?>
