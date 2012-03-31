<?php defined('SYSPATH') or die('No direct script access.');

class Controller_ReportManager extends Controller_Base {
    
    public function action_index() {
        $mapId = $this->request->param('id', NULL);
        if($mapId != NULL and $this->checkUser()) {
            $this->templateData['map'] = DB_ORM::model('map', array((int)$mapId));
            $this->templateData['sessions'] = DB_ORM::model('user_session')->getAllSessionByMap((int)$mapId);
            
            $allReportView = View::factory('labyrinth/report/allView');
            $allReportView->set('templateData', $this->templateData);
            
            $this->templateData['center'] = $allReportView;
            unset($this->templateData['left']);
            unset($this->templateData['right']);
            $this->template->set('templateData', $this->templateData);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }
    
    public function action_showReport() {
        $reportId = $this->request->param('id', NULL);
        if($reportId != NULL) {
            $this->templateData['session'] = DB_ORM::model('user_session', array((int)$reportId));
            $this->templateData['counters'] = DB_ORM::model('user_sessionTrace')->getCountersValues($this->templateData['session']->id);
            $this->templateData['questions'] = DB_ORM::model('map_question')->getQuestionsByMap($this->templateData['session']->map_id);
            $this->templateData['nodes'] = DB_ORM::model('map_node')->getNodesByMap($this->templateData['session']->map_id);
            if($this->templateData['questions'] != NULL) {
                foreach($this->templateData['questions'] as $question) {
                    $response = DB_ORM::model('user_response')->getResponce($this->templateData['session']->id, $question->id);
                    if($response != NULL) {
                        $this->templateData['responses'][$question->id] = $response;
                    }
                }
                
            }
            
            $this->templateData['feedbacks'] = Model::factory('labyrinth')->getMainFeedback($this->templateData['session'], $this->templateData['counters'], $this->templateData['session']->map_id);
            
            $reportView = View::factory('labyrinth/report/report');
            $reportView->set('templateData', $this->templateData);
            
            $this->templateData['center'] = $reportView;
            unset($this->templateData['left']);
            unset($this->templateData['right']);
            $this->template->set('templateData', $this->templateData);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }
    
    public function action_summaryReport() {
        $mapId = $this->request->param('id', NULL);
        if($mapId != NULL) {
            $this->templateData['map'] = DB_ORM::model('map', array((int)$mapId));  
            $this->templateData['sessions'] = DB_ORM::model('user_session')->getAllSessionByMap((int)$mapId);
            
            if(count($this->templateData['sessions']) > 0) {
                $minClicks = count($this->templateData['sessions'][0]->traces);
                foreach($this->templateData['sessions'] as $session) {
                    if($minClicks > count($session->traces)) {
                        $minClicks = count($session->traces);
                    }
                }
            }
            
            if(count($this->templateData['sessions']) > 0) {
                foreach($this->templateData['sessions'] as $session) {
                    $this->templateData['counters'][] = DB_ORM::model('user_sessionTrace')->getCountersValues($session->id);
                }
            }
            
            $this->templateData['allCounters'] = DB_ORM::model('map_counter')->getCountersByMap((int)$mapId);
            $this->templateData['minClicks'] = $minClicks;
            
            $summaryView = View::factory('labyrinth/report/summary');
            $summaryView->set('templateData', $this->templateData);
            
            $this->templateData['center'] = $summaryView;
            unset($this->templateData['left']);
            unset($this->templateData['right']);
            $this->template->set('templateData', $this->templateData);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }
    
    private function checkUser() {
        if(Auth::instance()->get_user()->type->name == 'author' or Auth::instance()->get_user()->type->name == 'superuser') {
            return TRUE;
        }
        
        return FALSE;
    }
}
    
?>
