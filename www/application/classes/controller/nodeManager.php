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

class Controller_NodeManager extends Controller_Base {
    
    public function action_index() {
        $mapId = $this->request->param('id', NULL);
        if($mapId != NULL) {
            $this->templateData['map'] = DB_ORM::model('map', array((int)$mapId));
            $this->templateData['nodes'] = DB_ORM::model('map_node')->getNodesByMap((int)$mapId);
            
            $nodeView = View::factory('labyrinth/node/view');
            $nodeView->set('templateData', $this->templateData);
        
            $leftView = View::factory('labyrinth/labyrinthEditorMenu');
            $leftView->set('templateData', $this->templateData);
            
            $this->templateData['left'] = $leftView;
            $this->templateData['center'] = $nodeView;
            unset($this->templateData['right']);
            $this->template->set('templateData', $this->templateData);
        } else {
             Request::initial()->redirect('home');
        }
    }
    
    public function action_addNode() {
        $mapId = $this->request->param('id', NULL);
        $editMode = $this->request->param('id2', NULL);
        if($mapId != NULL) {
            if($editMode != NULL) {
                $this->templateData['editMode'] = $editMode;
            } else {
                $this->templateData['editMode'] = 'w';
            }
            
            $this->templateData['map'] = DB_ORM::model('map', array((int)$mapId));
            $this->templateData['linkStyles'] = DB_ORM::model('map_node_link_style')->getAllLinkStyles();
            $this->templateData['priorities'] = DB_ORM::model('map_node_priority')->getAllPriorities();
            
            $addNodeView = View::factory('labyrinth/node/addNode');
            $addNodeView->set('templateData', $this->templateData);
            
            $leftView = View::factory('labyrinth/labyrinthEditorMenu');
            $leftView->set('templateData', $this->templateData);
        
            $this->templateData['left'] = $leftView;
            $this->templateData['center'] = $addNodeView;
            unset($this->templateData['right']);
            $this->template->set('templateData', $this->templateData);
        } else {
             Request::initial()->redirect('home');
        }
    }
    
    public function action_createNode() {
        $mapId = $this->request->param('id', NULL);
        if($_POST and $mapId != NULL) {
            $_POST['map_id'] = $mapId;
            $node = DB_ORM::model('map_node')->createNode($_POST);
            Request::initial()->redirect('nodeManager/index/'.$mapId);
        } else {
            Request::initial()->redirect("home");
        }
    }
    
    public function action_editNode() {
        $nodeId = $this->request->param('id', NULL);
        $editMode = $this->request->param('id2', NULL);
        if($nodeId != NULL) {
            if($editMode != NULL) {
                $this->templateData['editMode'] = $editMode;
            } else {
                $this->templateData['editMode'] = 'w';
            }
            
            $this->templateData['node'] = DB_ORM::model('map_node', array((int)$nodeId));
            $this->templateData['map'] = DB_ORM::model('map', array((int)$this->templateData['node']->map_id));
            $this->templateData['linkStyles'] = DB_ORM::model('map_node_link_style')->getAllLinkStyles();
            $this->templateData['priorities'] = DB_ORM::model('map_node_priority')->getAllPriorities();
            $this->templateData['counters'] = DB_ORM::model('map_counter')->getCountersByMap((int)$this->templateData['node']->map_id);
            
            $editNodeView = View::factory('labyrinth/node/editNode');
            $editNodeView->set('templateData', $this->templateData);
            
            $leftView = View::factory('labyrinth/labyrinthEditorMenu');
            $leftView->set('templateData', $this->templateData);
        
            $this->templateData['left'] = $leftView;
            $this->templateData['center'] = $editNodeView;
            unset($this->templateData['right']);
            $this->template->set('templateData', $this->templateData);
        } else {
            Request::initial()->redirect('home');
        }
    }
    
    public function action_updateNode() {
        $nodeId = $this->request->param('id', NULL);
        if($_POST and $nodeId != NULL) {
            $node = DB_ORM::model('map_node')->updateNode($nodeId, $_POST);
            if($node != NULL) {
                DB_ORM::model('map_node_counter')->updateNodeCounterByNode($node->id, $node->map_id, $_POST);
                Request::initial()->redirect('nodeManager/index/'.$node->map_id); 
            } else {
                Request::initial()->redirect('home');
            }
        } else {
            Request::initial()->redirect('home');
        }
    }
    
    public function action_setRootNode() {
        $mapId = $this->request->param('id', NULL);
        $nodeId = $this->request->param('id2', NULL);
        
        if($mapId != NULL and $nodeId != NULL) {
            DB_ORM::model('map_node')->setRootNode($mapId, $nodeId);
            Request::initial()->redirect('nodeManager/editNode/'.$nodeId);
        } else {
            Request::initial()->redirect('home');
        }
    }
    
    public function action_editConditional() {
        $nodeId = $this->request->param('id', NULL);
        $countOfCondidtionFiled = $this->request->param('id2', NULL);
        if($nodeId != NULL) {
            if($countOfCondidtionFiled != NULL) {
                $this->templateData['countOfCondidtionFiled'] = $countOfCondidtionFiled;
            } else {
                $this->templateData['countOfCondidtionFiled'] = 0;
            }
            
            $this->templateData['node'] = DB_ORM::model('map_node', array((int)$nodeId));
            $this->templateData['nodes'] = DB_ORM::model('map_node')->getNodesByMap($this->templateData['node']->map_id);
            $this->templateData['map'] = DB_ORM::model('map', array((int)$this->templateData['node']->map_id));
            
            $editConditionalView = View::factory('labyrinth/node/editConditional');
            $editConditionalView->set('templateData', $this->templateData);
            
            $leftView = View::factory('labyrinth/labyrinthEditorMenu');
            $leftView->set('templateData', $this->templateData);
            
            $this->templateData['left'] = $leftView;
            $this->templateData['center'] = $editConditionalView;
            unset($this->templateData['right']);
            $this->template->set('templateData', $this->templateData);
        } else {
            Request::initial()->redirect('home');
        }
    }
    
    public function action_addConditionalCount() {
        $nodeId = $this->request->param('id', NULL);
        $countOfCondidtionFiled = $this->request->param('id2', NULL);
        if($nodeId != NULL) {
            if($countOfCondidtionFiled != NULL) {
                $countOfCondidtionFiled++;
                Request::initial()->redirect('nodeManager/editConditional/'.$nodeId.'/'.$countOfCondidtionFiled);
            } else {
                Request::initial()->redirect('nodeManager/editConditional/'.$nodeId.'/1');
            }
        } else {
            Request::initial()->redirect('home');
        }
    }
    
    public function action_deleteConditionalCount() {
        $nodeId = $this->request->param('id', NULL);
        $countOfCondidtionFiled = $this->request->param('id2', NULL);
        if($nodeId != NULL) {
            if($countOfCondidtionFiled != NULL) {
                $countOfCondidtionFiled--;
                Request::initial()->redirect('nodeManager/editConditional/'.$nodeId.'/'.$countOfCondidtionFiled);
            } else {
                Request::initial()->redirect('nodeManager/editConditional/'.$nodeId.'/1');
            }
        } else {
            Request::initial()->redirect('home');
        }
    }
    
    public function action_saveConditional() {
        $nodeId = $this->request->param('id', NULL);
        $countOfCondidtionFiled = $this->request->param('id2', NULL);
        if($nodeId != NULL and $_POST) {
            if($countOfCondidtionFiled != NULL) {
                DB_ORM::model('map_node')->addCondtional($nodeId, $_POST, $countOfCondidtionFiled);
                Request::initial()->redirect('nodeManager/editNode/'.$nodeId);
            } else {
                Request::initial()->redirect('nodeManager/editConditional/'.$nodeId.'/1');
            }
        } else {
            Request::initial()->redirect('home');
        }
    }
    
    public function action_grid() {
        $mapId = $this->request->param('id', NULL);
        if($mapId != NULL) {
            $this->templateData['map'] = DB_ORM::model('map', array((int)$mapId));
            $this->templateData['nodes'] = DB_ORM::model('map_node')->getNodesByMap((int)$mapId);
            
            $nodeGridView = View::factory('labyrinth/node/grid');
            $nodeGridView->set('templateData', $this->templateData);
        
            $leftView = View::factory('labyrinth/labyrinthEditorMenu');
            $leftView->set('templateData', $this->templateData);
            
            $this->templateData['left'] = $leftView;
            $this->templateData['center'] = $nodeGridView;
            unset($this->templateData['right']);
            $this->template->set('templateData', $this->templateData);
        } else {
            Request::initial()->redirect('home');
        }
    }
    
    public function action_saveGrid() {
        $mapId = $this->request->param('id', NULL);
        if($_POST and $mapId != NULL) {
            DB_ORM::model('map_node')->updateAllNode($_POST);
            Request::initial()->redirect('nodeManager/grid/'.$mapId);
        } else {
            Request::initial()->redirect('home');
        }
    }
    
    public function action_sections() {
        $mapId = $this->request->param('id', NULL);
        if($mapId != NULL) {
            $this->templateData['map'] = DB_ORM::model('map', array((int)$mapId));
            $this->templateData['node_sections'] = DB_ORM::model('map_node_section')->getAllSectionsByMap($mapId);
            $this->templateData['sections'] = DB_ORM::model('map_section')->getAllSections();
            
            $nodeSectionsView = View::factory('labyrinth/node/section');
            $nodeSectionsView->set('templateData', $this->templateData);
        
            $leftView = View::factory('labyrinth/labyrinthEditorMenu');
            $leftView->set('templateData', $this->templateData);
            
            $this->templateData['left'] = $leftView;
            $this->templateData['center'] = $nodeSectionsView;
            unset($this->templateData['right']);
            $this->template->set('templateData', $this->templateData);
        } else {
            Request::initial()->redirect('home');
        }
    }
    
    public function action_updateSection() {
        $mapId = $this->request->param('id', NULL);
        if($_POST and $mapId != NULL) {
            DB_ORM::model('map')->updateSection($mapId, $_POST);
            Request::initial()->redirect('nodeManager/sections/'.$mapId);
        } else {
            Request::initial()->redirect('home');
        }
    }
    
    public function action_addNodeSection() {
        $mapId = $this->request->param('id', NULL);
        if($_POST and $mapId != NULL) {
            DB_ORM::model('map_node_section')->createSection($mapId, $_POST);
            Request::initial()->redirect('nodeManager/sections/'.$mapId);
        } else {
            Request::initial()->redirect('home');
        }
    }
    
    public function action_editSection() {
        $mapId = $this->request->param('id', NULL);
        $sectionId = $this->request->param('id2', NULL);
        if($sectionId != NULL and $mapId != NULL) {
            $this->templateData['map'] = DB_ORM::model('map', array((int)$mapId));
            $this->templateData['section'] = DB_ORM::model('map_node_section', array((int)$sectionId));
            $this->templateData['nodes'] = DB_ORM::model('map_node')->getAllNodesNotInSection();
            
            $editSectionsView = View::factory('labyrinth/node/editSection');
            $editSectionsView->set('templateData', $this->templateData);
        
            $leftView = View::factory('labyrinth/labyrinthEditorMenu');
            $leftView->set('templateData', $this->templateData);
            
            $this->templateData['left'] = $leftView;
            $this->templateData['center'] = $editSectionsView;
            unset($this->templateData['right']);
            $this->template->set('templateData', $this->templateData);
        } else {
            Request::initial()->redirect('home');
        }
    }
    
    public function action_updateNodeSection() {
        $mapId = $this->request->param('id', NULL);
        $sectionId = $this->request->param('id2', NULL);
        if($_POST and $sectionId != NULL and $mapId != NULL) {
            DB_ORM::model('map_node_section')->updateSectionName($sectionId, $_POST);
            Request::initial()->redirect('nodeManager/editSection/'.$mapId.'/'.$sectionId);
        } else {
            Request::initial()->redirect('home');
        }
    }
    
    public function action_deleteNodeSection() {
        $mapId = $this->request->param('id', NULL);
        $sectionId = $this->request->param('id2', NULL);
        if($sectionId != NULL and $mapId != NULL) {
            DB_ORM::model('map_node_section')->deleteSection((int)$sectionId);
            Request::initial()->redirect('nodeManager/sections/'.$mapId);
        } else {
            Request::initial()->redirect('home');
        }
    }
    
    public function action_addNodeInSection() {
        $mapId = $this->request->param('id', NULL);
        $sectionId = $this->request->param('id2', NULL);
        if($_POST and $sectionId != NULL and $mapId != NULL) {
            $nodeId = Arr::get($_POST, 'mnodeID', NULL);
            if($nodeId != NULL) {
                DB_ORM::model('map_node_section_node')->addNode($nodeId, $sectionId);
            }
            Request::initial()->redirect('nodeManager/editSection/'.$mapId.'/'.$sectionId);
        } else {
            Request::initial()->redirect('home');
        }
    }
    
    public function action_updateSectionNodes() {
        $mapId = $this->request->param('id', NULL);
        $sectionId = $this->request->param('id2', NULL);
        if($_POST and $sectionId != NULL and $mapId != NULL) {
            DB_ORM::model('map_node_section_node')->updateNodesOrder($sectionId, $_POST);
            Request::initial()->redirect('nodeManager/editSection/'.$mapId.'/'.$sectionId);
        } else {
            Request::initial()->redirect('home');
        }
    }
    
    public function action_deleteNodeBySection() {
        $mapId = $this->request->param('id', NULL);
        $sectionId = $this->request->param('id2', NULL);
        $nodeId = $this->request->param('id3', NULL);
        if($sectionId != NULL and $mapId != NULL and $nodeId != NULL) {
            DB_ORM::model('map_node_section_node')->deleteNodeBySection($sectionId, $nodeId);
            Request::initial()->redirect('nodeManager/editSection/'.$mapId.'/'.$sectionId);
        } else {
            Request::initial()->redirect('home');
        }
    }
}

?>
