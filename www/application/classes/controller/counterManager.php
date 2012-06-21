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

class Controller_CounterManager extends Controller_Base {
    
    public function action_index() {
        $mapId = $this->request->param('id', NULL);
        if($mapId != NULL) {
            $this->templateData['map'] = DB_ORM::model('map', array((int)$mapId));
            $this->templateData['counters'] = DB_ORM::model('map_counter')->getCountersByMap($mapId);           

            $countersView = View::factory('labyrinth/counter/view');
            $countersView->set('templateData', $this->templateData);

            $leftView = View::factory('labyrinth/labyrinthEditorMenu');
            $leftView->set('templateData', $this->templateData);

            $this->templateData['center'] = $countersView;
            $this->templateData['left'] = $leftView;
            unset($this->templateData['right']);
            $this->template->set('templateData', $this->templateData);
        } else {
            Request::initial()->redirect("home");
        }
    }
    
    public function action_addCounter() {
        $mapId = $this->request->param('id', NULL);
        if($mapId != NULL) {
            $this->templateData['map'] = DB_ORM::model('map', array((int)$mapId));
            $this->templateData['images'] = DB_ORM::model('map_element')->getImagesByMap($mapId);

            $addCounterView = View::factory('labyrinth/counter/add');
            $addCounterView->set('templateData', $this->templateData);

            $leftView = View::factory('labyrinth/labyrinthEditorMenu');
            $leftView->set('templateData', $this->templateData);

            $this->templateData['center'] = $addCounterView;
            $this->templateData['left'] = $leftView;
            unset($this->templateData['right']);
            $this->template->set('templateData', $this->templateData);
        } else {
            Request::initial()->redirect("home");
        }
    }
    
        
    public function action_saveNewCounter() {
        $mapId = $this->request->param('id', NULL);
        if($_POST and $mapId != NULL) {
            DB_ORM::model('map_counter')->addCounter($mapId, $_POST);
            Request::initial()->redirect(URL::base().'counterManager/index/'.$mapId);
        } else {
            Request::initial()->redirect("home");
        }
    }
    
    public function action_editCounter() {
        $mapId = $this->request->param('id', NULL);
        $counterId = $this->request->param('id2', NULL);
        if($mapId != NULL and $counterId != NULL) {
            $this->templateData['map'] = DB_ORM::model('map', array((int)$mapId));
            $this->templateData['counter'] = DB_ORM::model('map_counter', array((int)$counterId));
            $this->templateData['images'] = DB_ORM::model('map_element')->getImagesByMap($mapId);
            $this->templateData['rules'] = DB_ORM::model('map_counter_rule')->getRulesByCounterId($counterId);
            $this->templateData['relations'] = DB_ORM::model('map_counter_relation')->getAllRealtions();
            $this->templateData['nodes'] = DB_ORM::model('map_node')->getNodesByMap($mapId);
            
            $editCounterView = View::factory('labyrinth/counter/edit');
            $editCounterView->set('templateData', $this->templateData);

            $leftView = View::factory('labyrinth/labyrinthEditorMenu');
            $leftView->set('templateData', $this->templateData);

            $this->templateData['center'] = $editCounterView;
            $this->templateData['left'] = $leftView;
            unset($this->templateData['right']);
            $this->template->set('templateData', $this->templateData);
        } else {
            Request::initial()->redirect("home");
        }
    }
    
    public function action_updateCounter() {
        $mapId = $this->request->param('id', NULL);
        $counterId = $this->request->param('id2', NULL);
        if($_POST and $mapId != NULL and $counterId != NULL) {
            DB_ORM::model('map_counter')->updateCounter($counterId, $_POST);
            Request::initial()->redirect(URL::base().'counterManager/editCounter/'.$mapId.'/'.$counterId);
        } else {
            Request::initial()->redirect("home");
        }
    }
    
    public function action_deleteRule() {
        $mapId = $this->request->param('id', NULL);
        $counterId = $this->request->param('id2', NULL);
        $ruleId = $this->request->param('id3', NULL);
        $nodeId = $this->request->param('id4', NULL);
        if($mapId != NULL and $counterId != NULL and $ruleId != NULL and $nodeId != NULL) {
            DB_ORM::model('map_counter_rule', array((int)$ruleId))->delete();
            DB_ORM::model('map_node_counter')->deleteNodeCounter($nodeId, $counterId);
            Request::initial()->redirect(URL::base().'counterManager/editCounter/'.$mapId.'/'.$counterId);
        } else {
            Request::initial()->redirect("home");
        }
    }
    
    public function action_addRule() {
        $mapId = $this->request->param('id', NULL);
        $counterId = $this->request->param('id2', NULL);
        if($_POST and $mapId != NULL and $counterId != NULL) {
            DB_ORM::model('map_counter_rule')->addRule($counterId, $_POST);
            Request::initial()->redirect(URL::base().'counterManager/editCounter/'.$mapId.'/'.$counterId);
        } else {
            Request::initial()->redirect("home");
        }
    }
    
    public function action_deleteCounter() {
        $mapId = $this->request->param('id', NULL);
        $counterId = $this->request->param('id2', NULL);
        if($mapId != NULL and $counterId != NULL) {
            DB_ORM::model('map_node_counter')->deleteAllNodeCounterByCounter((int)$counterId);
            DB_ORM::model('map_counter', array((int)$counterId))->delete();
            Request::initial()->redirect(URL::base().'counterManager/index/'.$mapId);
        } else {
            Request::initial()->redirect("home");
        }
    }
    
    public function action_grid() {
        $mapId = $this->request->param('id', NULL);
        $counterId = $this->request->param('id2', NULL);
        if($mapId != NULL) {
            $this->templateData['map'] = DB_ORM::model('map', array((int)$mapId));
            $this->templateData['nodes'] = DB_ORM::model('map_node')->getNodesByMap((int)$mapId);
            if($counterId != NULL) {
                $this->templateData['counters'][] = DB_ORM::model('map_counter', array((int)$counterId));
                $this->templateData['oneCounter'] = true;
            } else {
                $this->templateData['counters'] = DB_ORM::model('map_counter')->getCountersByMap((int)$mapId);
            }
            
            $gridCounterView = View::factory('labyrinth/counter/grid');
            $gridCounterView->set('templateData', $this->templateData);

            $leftView = View::factory('labyrinth/labyrinthEditorMenu');
            $leftView->set('templateData', $this->templateData);

            $this->templateData['center'] = $gridCounterView;
            $this->templateData['left'] = $leftView;
            unset($this->templateData['right']);
            $this->template->set('templateData', $this->templateData);
        } else {
            Request::initial()->redirect("home");
        }
    }
    
    public function action_updateGrid() {
        $mapId = $this->request->param('id', NULL);
        $counterId = $this->request->param('id2', NULL);
        if($_POST and $mapId != NULL) {
            if($counterId != NULL) {
                DB_ORM::model('map_node_counter')->updateNodeCounters($_POST, (int)$counterId, (int)$mapId);
                Request::initial()->redirect(URL::base().'counterManager/grid/'.$mapId.'/'.$counterId);
            } else {
                DB_ORM::model('map_node_counter')->updateNodeCounters($_POST, NULL, (int)$mapId);
                Request::initial()->redirect(URL::base().'counterManager/grid/'.$mapId);
            }
        } else {
            Request::initial()->redirect("home");
        }
    }
    
    public function action_previewCounter() {
        $mapId = $this->request->param('id', NULL);
        $counterId = $this->request->param('id2', NULL);
        if($counterId != NULL) {
            $this->templateData['map'] = DB_ORM::model('map', array((int)$mapId));
            $this->templateData['counter'] = DB_ORM::model('map_counter', array((int)$counterId));
            
            $this->template = View::factory('labyrinth/counter/preview');
            $this->template->set('templateData', $this->templateData);
        } else {
            Request::initial()->redirect("home");
        }
    }
    
}

?>

