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

class Controller_ClusterManager extends Controller_Base {

    public function before() {
        $this->templateData['labyrinthSearch'] = 1;

        parent::before();

        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('My Labyrinths'))->set_url(URL::base() . 'authoredLabyrinth'));
    }

    public function action_index() {
        $mapId = $this->request->param('id', NULL);
        if ($mapId != NULL) {
            $this->templateData['map'] = DB_ORM::model('map', array((int) $mapId));
            $this->templateData['dams'] = DB_ORM::model('map_dam')->getAllDamByMap((int) $mapId);

            Breadcrumbs::add(Breadcrumb::factory()->set_title($this->templateData['map']->name)->set_url(URL::base() . 'labyrinthManager/global/' . $mapId));
            Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Clusters'))->set_url(URL::base() . 'clusterManager/index/' . $mapId));

            $view = View::factory('labyrinth/cluster/view');
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

    public function action_addDam() {
        $mapId = $this->request->param('id', NULL);
        if ($mapId != NULL) {
            $this->templateData['map'] = DB_ORM::model('map', array((int) $mapId));

            Breadcrumbs::add(Breadcrumb::factory()->set_title($this->templateData['map']->name)->set_url(URL::base() . 'labyrinthManager/global/' . $mapId));
            Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Clusters'))->set_url(URL::base() . 'clusterManager/index/' . $mapId));

            Breadcrumbs::add(Breadcrumb::factory()->set_title(__('New'))->set_url(URL::base() . 'clusterManager/addDam/' . $mapId));

            $addView = View::factory('labyrinth/cluster/add');
            $addView->set('templateData', $this->templateData);

            $leftView = View::factory('labyrinth/labyrinthEditorMenu');
            $leftView->set('templateData', $this->templateData);

            $this->templateData['left'] = $leftView;
            $this->templateData['center'] = $addView;
            unset($this->templateData['right']);
            $this->template->set('templateData', $this->templateData);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

    public function action_saveNewDam() {
        $mapId = $this->request->param('id', NULL);
        if ($_POST and $mapId != NULL) {
            Db_ORM::model('map_dam')->createDam($mapId, $_POST);
            Request::initial()->redirect(URL::base() . 'clusterManager/index/' . $mapId);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

    public function action_deleteDam() {
        $mapId = $this->request->param('id', NULL);
        $damId = $this->request->param('id2', NULL);

        if ($damId != NULL and $mapId != NULL) {
            Db_ORM::model('map_dam', array((int) $damId))->delete();
            Request::initial()->redirect(URL::base() . 'clusterManager/index/' . $mapId);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

    public function action_editCluster() {
        $mapId = $this->request->param('id', NULL);
        $damId = $this->request->param('id2', NULL);

        if ($mapId != NULL and $damId != NULL) {
            $this->templateData['map'] = DB_ORM::model('map', array((int) $mapId));
            $this->templateData['dam'] = DB_ORM::model('map_dam', array((int) $damId));
            $this->templateData['vpds'] = DB_ORM::model('map_dam')->getElementsNotAdded((int) $damId);
            $this->templateData['files'] = DB_ORM::model('map_dam')->getMediaFilesNotAdded((int) $damId);
            $this->templateData['dams'] = DB_ORM::model('map_dam')->getDamNotAdded((int) $damId);
            $this->templateData['preview'] = Controller_RenderLabyrinth::parseText('[[DAM:' . $damId . ']]');

            Breadcrumbs::add(Breadcrumb::factory()->set_title($this->templateData['map']->name)->set_url(URL::base() . 'labyrinthManager/global/' . $mapId));
            Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Clusters'))->set_url(URL::base() . 'clusterManager/index/' . $mapId));

            Breadcrumbs::add(Breadcrumb::factory()->set_title($this->templateData['dam']->name)->set_url(URL::base() . 'clusterManager/index/' . $mapId. '/'. $this->templateData['dam']->id));
            $editView = View::factory('labyrinth/cluster/edit');
            $editView->set('templateData', $this->templateData);

            $leftView = View::factory('labyrinth/labyrinthEditorMenu');
            $leftView->set('templateData', $this->templateData);

            $this->templateData['left'] = $leftView;
            $this->templateData['center'] = $editView;
            unset($this->templateData['right']);
            $this->template->set('templateData', $this->templateData);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

    public function action_updateDamName() {
        $mapId = $this->request->param('id', NULL);
        $damId = $this->request->param('id2', NULL);

        if ($_POST and $mapId != NULL and $damId != NULL) {
            DB_ORM::model('map_dam')->updateDamName($damId, $_POST);
            Request::initial()->redirect(URL::base() . 'clusterManager/editCluster/' . $mapId . '/' . $damId);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

    public function action_addElementToDam() {
        $mapId = $this->request->param('id', NULL);
        $damId = $this->request->param('id2', NULL);

        if ($_POST and $mapId != NULL and $damId != NULL) {
            DB_ORM::model('map_dam')->addElement($damId, $_POST, 'vpd');
            Request::initial()->redirect(URL::base() . 'clusterManager/editCluster/' . $mapId . '/' . $damId);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

    public function action_removeElementFormDam() {
        $mapId = $this->request->param('id', NULL);
        $damId = $this->request->param('id2', NULL);
        $elementId = $this->request->param('id3', NULL);

        if ($elementId != NULL and $mapId != NULL and $damId != NULL) {
            DB_ORM::model('map_dam_element', array((int) $elementId))->delete();
            Request::initial()->redirect(URL::base() . 'clusterManager/editCluster/' . $mapId . '/' . $damId);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

    public function action_updateDamElement() {
        $mapId = $this->request->param('id', NULL);
        $damId = $this->request->param('id2', NULL);
        $elementId = $this->request->param('id3', NULL);

        if ($_POST and $elementId != NULL and $mapId != NULL and $damId != NULL) {
            DB_ORM::model('map_dam_element')->updateElement($elementId, $_POST);
            Request::initial()->redirect(URL::base() . 'clusterManager/editCluster/' . $mapId . '/' . $damId);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

    public function action_addFileToDam() {
        $mapId = $this->request->param('id', NULL);
        $damId = $this->request->param('id2', NULL);

        if ($_POST and $mapId != NULL and $damId != NULL) {
            DB_ORM::model('map_dam')->addFile($damId, $_POST, 'mr');
            Request::initial()->redirect(URL::base() . 'clusterManager/editCluster/' . $mapId . '/' . $damId);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

    public function action_addDamToDam() {
        $mapId = $this->request->param('id', NULL);
        $damId = $this->request->param('id2', NULL);

        if ($_POST and $mapId != NULL and $damId != NULL) {
            DB_ORM::model('map_dam')->addDam($damId, $_POST, 'dam');
            Request::initial()->redirect(URL::base() . 'clusterManager/editCluster/' . $mapId . '/' . $damId);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

}

?>