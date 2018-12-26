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

        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('My Labyrinths'))->set_url(URL::base() . 'authoredLabyrinth'));

        parent::before();
    }

    public function action_index()
    {
        $mapId = $this->request->param('id', NULL);
        if ( ! $mapId) Controller::redirect(URL::base());

        DB_ORM::model('User')->can('edit', array('mapId' => $mapId));
        $this->templateData['map'] = DB_ORM::model('map', array((int) $mapId));
        $this->templateData['dams'] = DB_ORM::model('map_dam')->getAllDamByMap((int) $mapId);

        $ses = Session::instance();
        if($ses->get('warningMessage')){
            $this->templateData['warningMessage'] = $ses->get('warningMessage');
            $this->templateData['listOfUsedReferences'] = $ses->get('listOfUsedReferences');
            $ses->delete('listOfUsedReferences');
            $ses->delete('warningMessage');
        }

        $this->templateData['left'] = View::factory('labyrinth/labyrinthEditorMenu')->set('templateData', $this->templateData);
        $this->templateData['center'] = View::factory('labyrinth/cluster/view')->set('templateData', $this->templateData);
        $this->template->set('templateData', $this->templateData);

        Breadcrumbs::add(Breadcrumb::factory()->set_title($this->templateData['map']->name)->set_url(URL::base().'labyrinthManager/global/'.$mapId));
        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Clusters'))->set_url(URL::base().'clusterManager/index/'.$mapId));
    }

    public function action_addDam() {
        $mapId = $this->request->param('id', NULL);

        if ( ! $mapId) Controller::redirect(URL::base());

        DB_ORM::model('User')->can('edit', array('mapId' => $mapId));
        $this->templateData['map'] = DB_ORM::model('map', array((int) $mapId));
        $this->templateData['left'] = View::factory('labyrinth/labyrinthEditorMenu')->set('templateData', $this->templateData);
        $this->templateData['center'] = View::factory('labyrinth/cluster/add')->set('templateData', $this->templateData);
        $this->template->set('templateData', $this->templateData);

        Breadcrumbs::add(Breadcrumb::factory()->set_title($this->templateData['map']->name)->set_url(URL::base().'labyrinthManager/global/'.$mapId));
        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Clusters'))->set_url(URL::base().'clusterManager/index/'.$mapId));
        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('New'))->set_url(URL::base().'clusterManager/addDam/'.$mapId));
    }

    public function action_saveNewDam() {
        $mapId = $this->request->param('id', NULL);
        if ($_POST and $mapId != NULL) {
            Db_ORM::model('map_dam')->createDam($mapId, $_POST);
            Controller::redirect(URL::base() . 'clusterManager/index/' . $mapId);
        } else {
            Controller::redirect(URL::base());
        }
    }

    public function action_deleteDam()
    {
        $mapId = $this->request->param('id', NULL);
        $damId = $this->request->param('id2', NULL);

        if ($damId AND $mapId)
        {
            DB_ORM::model('User')->can('edit', array('mapId' => $mapId));

            $references = DB_ORM::model('map_node_reference')->getByElementType($damId, 'DAM');
            if($references != NULL)
            {
                $ses = Session::instance();
                $ses->set('listOfUsedReferences', CrossReferences::getListReferenceForView($references));
                $ses->set('warningMessage', 'The claster wasn\'t deleted. The selected claster is used in the following labyrinths:');
            }
            else DB_ORM::model('map_dam', array((int) $damId))->delete();
            Controller::redirect(URL::base().'clusterManager/index/'.$mapId);
        }
        else Controller::redirect(URL::base());
    }

    public function action_editCluster()
    {
        $mapId = $this->request->param('id', NULL);
        $damId = $this->request->param('id2', NULL);

        if ($mapId AND $damId)
        {
            DB_ORM::model('User')->can('edit', array('mapId' => $mapId));
            $this->templateData['map']      = DB_ORM::model('map', array((int) $mapId));
            $this->templateData['dam']      = DB_ORM::model('map_dam', array((int) $damId));
            $this->templateData['vpds']     = DB_ORM::model('map_dam')->getElementsNotAdded((int) $damId);
            $this->templateData['files']    = DB_ORM::model('map_dam')->getMediaFilesNotAdded((int) $damId);
            $this->templateData['dams']     = DB_ORM::model('map_dam')->getDamNotAdded((int) $damId);
            $this->templateData['preview']  = Controller_RenderLabyrinth::parseText('[[DAM:' . $damId . ']]');

            $usedElements = DB_ORM::model('map_node_reference')->getByElementType($damId, 'DAM');
            $this->templateData['used'] = count($usedElements);
            $ses = Session::instance();
            if($ses->get('dam_ses') == 'setPrivate')
            {
                $this->templateData['warningMessage'] = 'The claster is did not set to private. Please, check other labyrinths on reference on this claster.';
                $ses->delete('avatar_ses');
            }

            $this->templateData['left'] = View::factory('labyrinth/labyrinthEditorMenu')->set('templateData', $this->templateData);
            $this->templateData['center'] = View::factory('labyrinth/cluster/edit')->set('templateData', $this->templateData);
            $this->template->set('templateData', $this->templateData);

            Breadcrumbs::add(Breadcrumb::factory()->set_title($this->templateData['map']->name)->set_url(URL::base() . 'labyrinthManager/global/' . $mapId));
            Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Clusters'))->set_url(URL::base() . 'clusterManager/index/' . $mapId));
            Breadcrumbs::add(Breadcrumb::factory()->set_title($this->templateData['dam']->name)->set_url(URL::base() . 'clusterManager/index/' . $mapId. '/'. $this->templateData['dam']->id));
        }
        else Controller::redirect(URL::base());
    }

    public function action_updateDamName() {
        $mapId = $this->request->param('id', NULL);
        $damId = $this->request->param('id2', NULL);

        if ($_POST and $mapId != NULL and $damId != NULL) {
            $references = DB_ORM::model('map_node_reference')->getNotParent($mapId, $damId, 'DAM');
            $privete = Arr::get($_POST, 'is_private');
            if($references != NULL && $privete){
                $ses = Session::instance();
                $ses->set('listOfUsedReferences', CrossReferences::getListReferenceForView($references));
                $ses->set('warningMessage', 'The claster wasn\'t set to private. The selected claster is used in the following labyrinths:');
                $_POST['is_private'] = FALSE;
            }
            DB_ORM::model('map_dam')->updateDamName($damId, $_POST);
            Controller::redirect(URL::base() . 'clusterManager/editCluster/' . $mapId . '/' . $damId);
        } else {
            Controller::redirect(URL::base());
        }
    }

    public function action_addElementToDam() {
        $mapId = $this->request->param('id', NULL);
        $damId = $this->request->param('id2', NULL);

        if ($_POST and $mapId != NULL and $damId != NULL) {
            DB_ORM::model('map_dam')->addElement($damId, $_POST, 'vpd');
            Controller::redirect(URL::base() . 'clusterManager/editCluster/' . $mapId . '/' . $damId);
        } else {
            Controller::redirect(URL::base());
        }
    }

    public function action_removeElementFormDam() {
        $mapId = $this->request->param('id', NULL);
        $damId = $this->request->param('id2', NULL);
        $elementId = $this->request->param('id3', NULL);

        if ($elementId != NULL and $mapId != NULL and $damId != NULL) {
            DB_ORM::model('map_dam_element', array((int) $elementId))->delete();
            Controller::redirect(URL::base() . 'clusterManager/editCluster/' . $mapId . '/' . $damId);
        } else {
            Controller::redirect(URL::base());
        }
    }

    public function action_updateDamElement() {
        $mapId = $this->request->param('id', NULL);
        $damId = $this->request->param('id2', NULL);
        $elementId = $this->request->param('id3', NULL);

        if ($_POST and $elementId != NULL and $mapId != NULL and $damId != NULL) {
            DB_ORM::model('map_dam_element')->updateElement($elementId, $_POST);
            Controller::redirect(URL::base() . 'clusterManager/editCluster/' . $mapId . '/' . $damId);
        } else {
            Controller::redirect(URL::base());
        }
    }

    public function action_addFileToDam() {
        $mapId = $this->request->param('id', NULL);
        $damId = $this->request->param('id2', NULL);

        if ($_POST and $mapId != NULL and $damId != NULL) {
            DB_ORM::model('map_dam')->addFile($damId, $_POST, 'mr');
            Controller::redirect(URL::base() . 'clusterManager/editCluster/' . $mapId . '/' . $damId);
        } else {
            Controller::redirect(URL::base());
        }
    }

    public function action_addDamToDam() {
        $mapId = $this->request->param('id', NULL);
        $damId = $this->request->param('id2', NULL);

        if ($_POST and $mapId != NULL and $damId != NULL) {
            DB_ORM::model('map_dam')->addDam($damId, $_POST, 'dam');
            Controller::redirect(URL::base() . 'clusterManager/editCluster/' . $mapId . '/' . $damId);
        } else {
            Controller::redirect(URL::base());
        }
    }

}