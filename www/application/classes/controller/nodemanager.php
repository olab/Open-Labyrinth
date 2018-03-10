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

    public function before() {
        $this->templateData['labyrinthSearch'] = 1;

        parent::before();

        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('My Labyrinths'))->set_url(URL::base() . 'authoredLabyrinth'));
    }

    public function action_index()
    {
        $mapId = (int) $this->request->param('id', 0);

        DB_ORM::model('User')->can('edit', array('mapId' => $mapId));
        if ($mapId)
        {
            $this->templateData['map'] = DB_ORM::model('map', array($mapId));
            $this->templateData['nodes'] = DB_ORM::model('map_node')->getNodesByMap($mapId);

            $ses = Session::instance();
            if($ses->get('warningMessage'))
            {
                $this->templateData['warningMessage'] = $ses->get('warningMessage');
                $this->templateData['listOfUsedReferences'] = $ses->get('listOfUsedReferences');
                $ses->delete('listOfUsedReferences');
                $ses->delete('warningMessage');
            }

            Breadcrumbs::add(Breadcrumb::factory()->set_title($this->templateData['map']->name)->set_url(URL::base() . 'labyrinthManager/global/' . $mapId));
            Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Nodes'))->set_url(URL::base() . 'nodeManager/index/' . $mapId));

            $nodeView = View::factory('labyrinth/node/view');
            $nodeView->set('templateData', $this->templateData);

            $leftView = View::factory('labyrinth/labyrinthEditorMenu');
            $leftView->set('templateData', $this->templateData);

            $this->templateData['left'] = $leftView;
            $this->templateData['center'] = $nodeView;
            unset($this->templateData['right']);
            $this->template->set('templateData', $this->templateData);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

    public function action_addNodeNote() {
        $node = DB_ORM::model('map_node', array((int)$this->request->param('id', null)));

        $redirectURL = URL::base();

        if($node != null && $node->map->assign_forum_id != null) {
            $topicId = null;

            if($node->notes != null && count($node->notes) == 1) {
                $topicId = $node->notes[0]->id;
            } else {
                $topicId = DB_ORM::model('dtopic')->createTopic($node->title . ' - ' . $node->id, 0, 1, $node->map->assign_forum_id, $node->id);
                $users   = DB_ORM::model('dforum_users')->getAllUsersInForum($node->map->assign_forum_id);
                $groups  = DB_ORM::model('dforum_groups')->getAllGroupsInForum($node->map->assign_forum_id);

                DB_ORM::model('dtopic_users')->updateUsers($topicId, $users);
                DB_ORM::model('dtopic_groups')->updateGroups($topicId, $groups);
            }

            if($topicId != null) { $redirectURL .= 'dtopicManager/viewTopic/' . $topicId; }
        }

        Request::initial()->redirect($redirectURL);
    }

    public function action_addNode()
    {
        $mapId      = (int) $this->request->param('id', 0);
        $editMode   = (int) $this->request->param('id2', 0);

        if ( ! $mapId) Request::initial()->redirect(URL::base());

        $this->templateData['map']          = DB_ORM::model('map', array($mapId));
        $this->templateData['editMode']     = $editMode ? $editMode : 'w';
        $this->templateData['linkStyles']   = DB_ORM::select('map_node_link_style')->query()->as_array();
        $this->templateData['priorities']   = DB_ORM::model('map_node_priority')->getAllPriorities();
        $this->templateData['counters']     = DB_ORM::model('map_counter')->getCountersByMap((int) $mapId);
        $this->templateData['popups']       = DB_ORM::model('map_popup')->getAllMapPopups($mapId);
        $this->templateData['mainLinkStyle']= DB_ORM::model('map_node')->getMainLinkStyles($mapId);
        $this->templateData['left']         = View::factory('labyrinth/labyrinthEditorMenu')->set('templateData', $this->templateData);
        $this->templateData['center']       = View::factory('labyrinth/node/addNode')->set('templateData', $this->templateData);


        $this->template->set('templateData', $this->templateData);
    }

    public function action_editNode ()
    {
        $nodeId     = (int) $this->request->param('id', 0);
        $tinyMCEv3  = $this->request->param('id2', 0);

        if ( ! $nodeId) Request::initial()->redirect(URL::base());

        $node   = DB_ORM::model('map_node', array($nodeId));
        $mapId  = $node->map_id;

        DB_ORM::model('User')->can('edit', array('mapId' => $mapId));

        $this->templateData['tinyMCEv3']    = $tinyMCEv3;
        $this->templateData['node']         = $node;
        $this->templateData['map']          = DB_ORM::model('map', array($mapId));
        $this->templateData['linkStyles']   = DB_ORM::select('map_node_link_style')->query()->as_array();
        $this->templateData['priorities']   = DB_ORM::model('map_node_priority')->getAllPriorities();
        $this->templateData['counters']     = DB_ORM::model('map_counter')->getCountersByMap($mapId);
        $this->templateData['popups']       = DB_ORM::model('map_popup')->getAllMapPopups($mapId);
        $this->templateData['left']         = View::factory('labyrinth/labyrinthEditorMenu')->set('templateData', $this->templateData);
        $this->templateData['center']       = View::factory('labyrinth/node/editNode')->set('templateData', $this->templateData);

        Breadcrumbs::add(Breadcrumb::factory()->set_title($this->templateData['map']->name)->set_url(URL::base().'labyrinthManager/global/'.$mapId));
        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Nodes'))->set_url(URL::base().'nodeManager/index/'.$mapId));
        Breadcrumbs::add(Breadcrumb::factory()->set_title($this->templateData['node']->title)->set_url(URL::base().'nodeManager/editNode/'.$node->id));

        $this->template->set('templateData', $this->templateData);
    }

    public function action_createNode()
    {
        $mapId = (int) $this->request->param('id', 0);
        $post = $this->request->post();

        if ( ! ($post AND $mapId)) Request::initial()->redirect(URL::base());

        $post['map_id'] = $mapId;
        $node = DB_ORM::model('map_node')->createNode($post);
        
        $text = Arr::get($post, 'mnodetext', '');
        $info = Arr::get($post, 'mnodeinfo', '');
        $crossreferences = new CrossReferences();
        $nodetext = $crossreferences->checkReference($mapId, $node->id, $text, $info);
        if(isset($nodetext['text'])){
            DB_ORM::model('map_node')->updateNodeText($node->id, $nodetext['text']);
        }
        if(isset($nodetext['info'])){
            DB_ORM::model('map_node')->updateNodeInfo($node->id, $nodetext['info']);
        }
        
        if ( ! $node) Request::initial()->redirect(URL::base().'nodeManager/index/'.$mapId);

        DB_ORM::model('map_node_counter')->updateNodeCounterByNode($node->id, $node->map_id, $post);
        Request::initial()->redirect(URL::base().'nodeManager/index/'.$node->map_id);
    }

    public function action_updateNode() {
        $nodeId = (int) $this->request->param('id', 0);
        $post = $this->request->post();
        $mapId = $post['map_id'];
        if ( ! ($post AND $nodeId)) Request::initial()->redirect(URL::base());

        $text = Arr::get($post, 'mnodetext', '');
        $info = Arr::get($post, 'mnodeinfo', '');
        $crossreferences = new CrossReferences();
        $nodetext = $crossreferences->checkReference($mapId, $nodeId, $text, $info);
        if(isset($nodetext['text'])){
            $post['mnodetext'] = $nodetext['text'];
        }
        if(isset($nodetext['info'])){
            $post['mnodeinfo'] = $nodetext['info'];
        }
        $references = DB_ORM::model('map_node_reference')->getNotParent($mapId, $nodeId, 'INFO');
        $privete = Arr::get($post, 'is_private');
        if($references != NULL && $privete){
            $ses = Session::instance();
            $ses->set('listOfUsedReferences', CrossReferences::getListReferenceForView($references));
            $ses->set('warningMessage', 'The node wasn\'t set to private. Field with supporting information of the selected node is used in the following labyrinths:');
            $post['is_private'] = FALSE;
        } 
        
        $node = DB_ORM::model('map_node')->updateNode($nodeId, $post);
        Model_Leap_Metadata_Record::updateMetadata("map_node",$nodeId,$post);

        if ( ! $node) Request::initial()->redirect(URL::base());

        DB_ORM::model('map_node_counter')->updateNodeCounterByNode($node->id, $node->map_id, $post);
        Request::initial()->redirect(URL::base().'nodeManager/index/'.$node->map_id);
    }

    public function action_deleteNode()
    {
        $mapId  = (int) $this->request->param('id', 0);
        $nodeId = (int) $this->request->param('id2', 0);

        DB_ORM::model('User')->can('edit', array('mapId' => $mapId));
        if ( ! $nodeId) Request::initial()->redirect(URL::base());

        $references = DB_ORM::model('map_node_reference')->getByElementType($nodeId, 'INFO');
        if($references != NULL){
            $ses = Session::instance();
            $ses->set('listOfUsedReferences', CrossReferences::getListReferenceForView($references));
            $ses->set('warningMessage', 'The node wasn\'t deleted. Field with supporting information of the selected node is used in the following labyrinths:');
        } else {
            DB_ORM::model('map_node_reference')->deleteByNodeId($mapId, $nodeId);
            DB_ORM::model('map_node')->deleteNode($nodeId);
        }

        Request::initial()->redirect(URL::base() . 'nodeManager/index/' . $mapId);
    }

    public function action_setRootNode() {
        $mapId = (int) $this->request->param('id', 0);
        $nodeId = (int) $this->request->param('id2', 0);

        if ($mapId && $nodeId) {
            DB_ORM::model('map_node')->setRootNode($mapId, $nodeId);
            Request::initial()->redirect(URL::base() . 'nodeManager/editNode/' . $nodeId);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

    public function action_editConditional() {
        $nodeId = (int) $this->request->param('id', 0);
        $countOfCondidtionFiled = (int) $this->request->param('id2', 0);
        if ($nodeId) {
            if ($countOfCondidtionFiled) {
                $this->templateData['countOfCondidtionFiled'] = $countOfCondidtionFiled;
            } else {
                $this->templateData['countOfCondidtionFiled'] = 0;
            }

            $this->templateData['node'] = DB_ORM::model('map_node', array($nodeId));
            $this->templateData['nodes'] = DB_ORM::model('map_node')->getNodesByMap($this->templateData['node']->map_id);
            $this->templateData['map'] = DB_ORM::model('map', array((int) $this->templateData['node']->map_id));

            $editConditionalView = View::factory('labyrinth/node/editConditional');
            $editConditionalView->set('templateData', $this->templateData);

            $leftView = View::factory('labyrinth/labyrinthEditorMenu');
            $leftView->set('templateData', $this->templateData);

            $this->templateData['left'] = $leftView;
            $this->templateData['center'] = $editConditionalView;
            unset($this->templateData['right']);
            $this->template->set('templateData', $this->templateData);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

    public function action_addConditionalCount() {
        $nodeId = (int) $this->request->param('id', 0);
        $countOfCondidtionFiled = (int) $this->request->param('id2', 0);
        if ($nodeId) {
            if ($countOfCondidtionFiled) {
                $countOfCondidtionFiled++;
                Request::initial()->redirect(URL::base() . 'nodeManager/editConditional/' . $nodeId . '/' . $countOfCondidtionFiled);
            } else {
                Request::initial()->redirect(URL::base() . 'nodeManager/editConditional/' . $nodeId . '/1');
            }
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

    public function action_deleteConditionalCount() {
        $nodeId = (int) $this->request->param('id', 0);
        $countOfCondidtionFiled = (int) $this->request->param('id2', 0);
        if ($nodeId) {
            if ($countOfCondidtionFiled) {
                $countOfCondidtionFiled--;
                Request::initial()->redirect(URL::base() . 'nodeManager/editConditional/' . $nodeId . '/' . $countOfCondidtionFiled);
            } else {
                Request::initial()->redirect(URL::base() . 'nodeManager/editConditional/' . $nodeId . '/1');
            }
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

    public function action_saveConditional() {
        $nodeId = (int) $this->request->param('id', 0);
        $countOfCondidtionFiled = (int) $this->request->param('id2', 0);
        if (isset($_POST) && !empty($_POST) && $nodeId) {
            if ($countOfCondidtionFiled) {
                DB_ORM::model('map_node')->addCondtional($nodeId, $_POST, $countOfCondidtionFiled);
                Request::initial()->redirect(URL::base() . 'nodeManager/editNode/' . $nodeId);
            } else {
                Request::initial()->redirect(URL::base() . 'nodeManager/editConditional/' . $nodeId . '/1');
            }
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

    public function action_grid()
    {
        $mapId      = (int) $this->request->param('id', 0);
        $orderBy    = $this->request->param('id2', null);
        $logicSort  = $this->request->param('id3', 0);

        if ( ! $mapId) Request::initial()->redirect(URL::base());

        DB_ORM::model('User')->can('edit', array('mapId' => $mapId));
        $this->templateData['orderBy']      = $orderBy;
        $this->templateData['logicSort']    = $logicSort;
        $this->templateData['map']          = DB_ORM::model('map', array($mapId));
        $this->templateData['nodes']        = DB_ORM::model('map_node')->getNodesByMap($mapId, $orderBy, $logicSort);

        Breadcrumbs::add(Breadcrumb::factory()->set_title($this->templateData['map']->name)->set_url(URL::base() . 'labyrinthManager/global/' . $mapId));
        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Node Grid'))->set_url(URL::base() . 'nodeManager/grid/' . $mapId));

        $nodeGridView = View::factory('labyrinth/node/grid');
        $nodeGridView->set('templateData', $this->templateData);

        $leftView = View::factory('labyrinth/labyrinthEditorMenu');
        $leftView->set('templateData', $this->templateData);

        $this->templateData['left'] = $leftView;
        $this->templateData['center'] = $nodeGridView;
        unset($this->templateData['right']);
        $this->template->set('templateData', $this->templateData);
    }

    public function action_saveGrid() {
        $mapId = (int) $this->request->param('id', 0);
        if (isset($_POST) && !empty($_POST) && $mapId) {
            DB_ORM::model('map_node')->updateAllNode($_POST, $mapId);
            Request::initial()->redirect(URL::base() . 'nodeManager/grid/' . $mapId . '/' . Arr::get($_POST, 'orderBy', 0) . '/' . Arr::get($_POST, 'logicSort', 0));
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

    public function action_sections() {
        $mapId = (int) $this->request->param('id', 0);
        if ($mapId)
        {

            DB_ORM::model('User')->can('edit', array('mapId' => $mapId));
            $this->templateData['map'] = DB_ORM::model('map', array($mapId));
            $this->templateData['node_sections'] = DB_ORM::model('map_node_section')->getAllSectionsByMap($mapId);
            $this->templateData['sections'] = DB_ORM::model('map_section')->getAllSections();

            Breadcrumbs::add(Breadcrumb::factory()->set_title($this->templateData['map']->name)->set_url(URL::base() . 'labyrinthManager/global/' . $mapId));
            Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Sections'))->set_url(URL::base() . 'nodeManager/sections/' . $mapId));

            $nodeSectionsView = View::factory('labyrinth/node/section');
            $nodeSectionsView->set('templateData', $this->templateData);

            $leftView = View::factory('labyrinth/labyrinthEditorMenu');
            $leftView->set('templateData', $this->templateData);

            $this->templateData['left'] = $leftView;
            $this->templateData['center'] = $nodeSectionsView;
            unset($this->templateData['right']);
            $this->template->set('templateData', $this->templateData);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

    public function action_updateSection() {
        $mapId = (int) $this->request->param('id', 0);
        if (isset($_POST) && !empty($_POST) && $mapId) {
            DB_ORM::model('map')->updateSection($mapId, $_POST);
            Request::initial()->redirect(URL::base() . 'nodeManager/sections/' . $mapId);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

    public function action_addNodeSection() {
        $mapId = (int) $this->request->param('id', 0);
        if (isset($_POST) && !empty($_POST) && $mapId) {
            DB_ORM::model('map_node_section')->createSection($mapId, $_POST);
            Request::initial()->redirect(URL::base() . 'nodeManager/sections/' . $mapId);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

    public function action_editSection()
    {
        $mapId      = (int) $this->request->param('id', 0);
        $sectionId  = (int) $this->request->param('id2', 0);

        if ( ! ($sectionId AND $mapId)) Request::initial()->redirect(URL::base());

        $this->templateData['map']          = DB_ORM::model('map', array($mapId));
        $this->templateData['section']      = DB_ORM::model('map_node_section', array($sectionId));
        $this->templateData['nodes']        = DB_ORM::model('map_node')->getAllNodesNotInSection($mapId);
        $this->templateData['node_type']    = DB_ORM::model('map_node_section_node')->nodeType;
        $this->templateData['left']         = View::factory('labyrinth/labyrinthEditorMenu')->set('templateData', $this->templateData);
        $this->templateData['center']       = View::factory('labyrinth/node/editSection')->set('templateData', $this->templateData);
        $this->template->set('templateData', $this->templateData);

        Breadcrumbs::add(Breadcrumb::factory()->set_title($this->templateData['map']->name)->set_url(URL::base() . 'labyrinthManager/global/' . $mapId));
        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Sections'))->set_url(URL::base() . 'nodeManager/sections/' . $mapId));
        Breadcrumbs::add(Breadcrumb::factory()->set_title($this->templateData['section']->name)->set_url(URL::base() . 'nodeManager/editSection/' . $mapId. '/'. $sectionId));
    }

    public function action_updatePreventRevisit ()
    {
        $id_map      = (int) $this->request->param('id', 0);
        $id_section  = (int) $this->request->param('id2', 0);

        if ( ! ($id_section AND $id_map)) Request::initial()->redirect(URL::base());

        $status = $this->request->post('submit');
        $section = DB_ORM::model('map_node_section', array($id_section))->nodes;

        foreach ($section as $node)
        {
            $node = DB_ORM::model('Map_Node', array($node->node_id));
            if ($status !== null) $node->undo = $status;
            $node->save();
        }
        Request::initial()->redirect(URL::base().'nodeManager/editSection/'.$id_map.'/'.$id_section);
    }

    public function action_updateNodeSection() {
        $mapId = (int) $this->request->param('id', 0);
        $sectionId = (int) $this->request->param('id2', 0);
        if (isset($_POST) && !empty($_POST) && $sectionId && $mapId) {
            DB_ORM::model('map_node_section')->updateSectionRow($sectionId, $_POST);
            Request::initial()->redirect(URL::base().'nodeManager/editSection/'.$mapId.'/'.$sectionId);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

    public function action_deleteNodeSection() {
        $mapId = (int) $this->request->param('id', 0);
        $sectionId = (int) $this->request->param('id2', 0);
        if ($sectionId && $mapId) {
            DB_ORM::model('map_node_section')->deleteSection($sectionId);
            Request::initial()->redirect(URL::base() . 'nodeManager/sections/' . $mapId);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

    public function action_addNodeInSection() {
        $mapId = (int) $this->request->param('id', 0);
        $sectionId = (int) $this->request->param('id2', 0);
        if (isset($_POST) && !empty($_POST) && $sectionId && $mapId) {
            $nodeId = (int) Arr::get($_POST, 'mnodeID', 0);
            if ($nodeId) {
                DB_ORM::model('map_node_section_node')->addNode($nodeId, $sectionId);
            }
            Request::initial()->redirect(URL::base() . 'nodeManager/editSection/' . $mapId . '/' . $sectionId);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

    public function action_updateSectionNodes()
    {
        $mapId      = (int) $this->request->param('id', 0);
        $sectionId  = (int) $this->request->param('id2', 0);

        if ( ! ($sectionId AND $mapId)) Request::initial()->redirect(URL::base());

        DB_ORM::model('map_node_section_node')->updateSectionNodes($sectionId, $this->request->post());
        Request::initial()->redirect(URL::base().'nodeManager/editSection/'.$mapId.'/'.$sectionId);
    }

    public function action_deleteNodeBySection() {
        $mapId = (int) $this->request->param('id', 0);
        $sectionId = (int) $this->request->param('id2', 0);
        $nodeId = (int) $this->request->param('id3', 0);
        if ($sectionId && $mapId && $nodeId) {
            DB_ORM::model('map_node_section_node')->deleteNodeBySection($sectionId, $nodeId);
            Request::initial()->redirect(URL::base() . 'nodeManager/editSection/' . $mapId . '/' . $sectionId);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

}