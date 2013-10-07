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

class Controller_LinkManager extends Controller_Base {

    public function before() {
        $this->templateData['labyrinthSearch'] = 1;

        parent::before();

        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('My Labyrinths'))->set_url(URL::base() . 'authoredLabyrinth'));
    }

    public function action_index() {
        $mapId = $this->request->param('id', NULL);
        if ($mapId != NULL) {
            $this->templateData['map'] = DB_ORM::model('map', array((int) $mapId));
            $this->templateData['nodes'] = DB_ORM::model('map_node')->getNodesByMap($mapId);

            Breadcrumbs::add(Breadcrumb::factory()->set_title($this->templateData['map']->name)->set_url(URL::base() . 'labyrinthManager/global/' . $mapId));
            Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Links'))->set_url(URL::base() . 'linkManager/index/' . $mapId));

            $linksView = View::factory('labyrinth/link/view');
            $linksView->set('templateData', $this->templateData);

            $leftView = View::factory('labyrinth/labyrinthEditorMenu');
            $leftView->set('templateData', $this->templateData);

            $this->templateData['center'] = $linksView;
            $this->templateData['left'] = $leftView;
            unset($this->templateData['right']);
            $this->template->set('templateData', $this->templateData);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

    public function action_editLinks() {
        $mapId = $this->request->param('id', NULL);
        $nodeId = $this->request->param('id2', NULL);
        if ($mapId != NULL and $nodeId != NULL) {
            $this->templateData['map'] = DB_ORM::model('map', array((int) $mapId));
            $this->templateData['node'] = DB_ORM::model('map_node', array($nodeId));
            $this->templateData['link_nodes'] = DB_ORM::model('map_node')->getNodesWithoutLink($nodeId);
            $this->templateData['linkStylies'] = DB_ORM::model('map_node_link_style')->getAllLinkStyles();
            $this->templateData['linkTypes'] = DB_ORM::model('map_node_link_type')->getAllLinkTypes();
            $this->templateData['images'] = DB_ORM::model('map_element')->getImagesByMap((int) $mapId);
            Breadcrumbs::add(Breadcrumb::factory()->set_title($this->templateData['map']->name)->set_url(URL::base() . 'labyrinthManager/global/' . $mapId));
            Breadcrumbs::add(Breadcrumb::factory()->set_title($this->templateData['node']->title)->set_url(URL::base() . 'nodeManager/index/'. $mapId. '/' . $nodeId));
            Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Links'))->set_url(URL::base() . 'linkManager/index/' . $mapId));

            $editLinkView = View::factory('labyrinth/link/edit');
            $editLinkView->set('templateData', $this->templateData);

            $leftView = View::factory('labyrinth/labyrinthEditorMenu');
            $leftView->set('templateData', $this->templateData);

            $this->templateData['center'] = $editLinkView;
            $this->templateData['left'] = $leftView;
            unset($this->templateData['right']);
            $this->template->set('templateData', $this->templateData);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

    public function action_addLink() {
        $mapId = $this->request->param('id', NULL);
        $nodeId = $this->request->param('id2', NULL);
        if ($_POST and $mapId != NULL and $nodeId != NULL) {
            DB_ORM::model('map_node_link')->addLink($mapId, $nodeId, $_POST);
            Request::initial()->redirect(URL::base() . 'linkManager/editLinks/' . $mapId . '/' . $nodeId);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

    public function action_deleteLink() {
        $mapId = $this->request->param('id', NULL);
        $nodeId = $this->request->param('id2', NULL);
        $deleteLinkId = $this->request->param('id3', NULL);
        if ($mapId != NULL and $nodeId != NULL and $deleteLinkId != NULL) {
            DB_ORM::model('map_node_link', array((int) $deleteLinkId))->delete();
            Request::initial()->redirect(URL::base() . 'linkManager/editLinks/' . $mapId . '/' . $nodeId);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

    public function action_editLink() {
        $mapId = $this->request->param('id', NULL);
        $nodeId = $this->request->param('id2', NULL);
        $editLinkId = $this->request->param('id3', NULL);
        if ($mapId != NULL and $nodeId != NULL and $editLinkId != NULL) {
            $this->templateData['map'] = DB_ORM::model('map', array((int) $mapId));
            $this->templateData['node'] = DB_ORM::model('map_node', array($nodeId));
            $this->templateData['editLink'] = DB_ORM::model('map_node_link', array((int) $editLinkId));
            $this->templateData['linkStylies'] = DB_ORM::model('map_node_link_style')->getAllLinkStyles();
            $this->templateData['images'] = DB_ORM::model('map_element')->getImagesByMap((int) $mapId);
            Breadcrumbs::add(Breadcrumb::factory()->set_title($this->templateData['map']->name)->set_url(URL::base() . 'labyrinthManager/global/' . $mapId));
            Breadcrumbs::add(Breadcrumb::factory()->set_title($this->templateData['node']->title)->set_url(URL::base() . 'nodeManager/index/'. $mapId. '/' . $nodeId));
            Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Links'))->set_url(URL::base() . 'linkManager/index/' . $mapId));
            Breadcrumbs::add(Breadcrumb::factory()->set_title(__('→'.$this->templateData['editLink']->node_2->title))->set_url(URL::base() . 'linkManager/editLink/' . $mapId. '/' . $nodeId. '/'. $editLinkId));

            $editLinkView = View::factory('labyrinth/link/edit');
            $editLinkView->set('templateData', $this->templateData);

            $leftView = View::factory('labyrinth/labyrinthEditorMenu');
            $leftView->set('templateData', $this->templateData);

            $this->templateData['center'] = $editLinkView;
            $this->templateData['left'] = $leftView;
            unset($this->templateData['right']);
            $this->template->set('templateData', $this->templateData);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

    public function action_updateLink() {
        $mapId = $this->request->param('id', NULL);
        $nodeId = $this->request->param('id2', NULL);
        $updateLinkId = $this->request->param('id3', NULL);
        if ($_POST and $mapId != NULL and $nodeId != NULL and $updateLinkId != NULL) {
            DB_ORM::model('map_node_link')->updateLink($updateLinkId, $_POST);
            Model_Leap_Metadata_Record::updateMetadata("map_node_link",$updateLinkId,$_POST);
            Request::initial()->redirect(URL::base() . 'linkManager/editLinks/' . $mapId . '/' . $nodeId);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

    public function action_updateLinkStyle() {
        $mapId = $this->request->param('id', NULL);
        $nodeId = $this->request->param('id2', NULL);
        if ($_POST and $mapId != NULL and $nodeId != NULL) {
            DB_ORM::model('map_node')->updateNode($nodeId, $_POST);
            Request::initial()->redirect(URL::base() . 'linkManager/editLinks/' . $mapId . '/' . $nodeId);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

    public function action_updateLinkType() {
        $mapId = $this->request->param('id', NULL);
        $nodeId = $this->request->param('id2', NULL);
        if ($_POST and $mapId != NULL and $nodeId != NULL) {
            DB_ORM::model('map_node')->updateNode($nodeId, $_POST);
            Request::initial()->redirect(URL::base() . 'linkManager/editLinks/' . $mapId . '/' . $nodeId);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

    public function action_updateOrder() {
        $mapId = $this->request->param('id', NULL);
        $nodeId = $this->request->param('id2', NULL);
        if ($_POST and $mapId != NULL and $nodeId != NULL) {
            $node = DB_ORM::model('map_node', array((int) $nodeId));
            if ($node->link_type->name == 'ordered') {
                DB_ORM::model('map_node_link')->updateOrders($mapId, $nodeId, $_POST);
            } else if ($node->link_type->name == 'random select one *') {
                DB_ORM::model('map_node_link')->updateProbability($mapId, $nodeId, $_POST);
            }

            Request::initial()->redirect(URL::base() . 'linkManager/editLinks/' . $mapId . '/' . $nodeId);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

}