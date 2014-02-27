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

class Controller_ElementManager extends Controller_Base {

    public function before() {
        $this->templateData['labyrinthSearch'] = 1;

        parent::before();

        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('My Labyrinths'))->set_url(URL::base() . 'authoredLabyrinth'));
    }

    public function action_index() {
        $mapId = $this->request->param('id', NULL);

        if ($mapId != NULL) {
            $map = DB_ORM::model('map', array((int) $mapId));

            $this->templateData['map'] = $map;
            $this->templateData['vpds'] = DB_ORM::model('map_vpd')->getAllVpdByMap($map->id);

            $ses = Session::instance();
            $x = $ses->get('vpdmanager_ses');
            switch ($x){
                case 'setPrivate':
                    $this->templateData['warningMessage'] = 'Element is did not set to private. Please, check other labyrinths on reference on this element.';
                    $ses->delete('vpdmanager_ses');
                    break;
                case 'delVpd':
                    $this->templateData['warningMessage'] = 'The element did not deleted. Please, check other labyrinths on reference on this element.';
                    $ses->delete('vpdmanager_ses');
                    break;
            }

            Breadcrumbs::add(Breadcrumb::factory()->set_title($this->templateData['map']->name)->set_url(URL::base() . 'labyrinthManager/global/' . $mapId));
            Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Elements'))->set_url(URL::base() . 'elementManager/index/' . $mapId));

            $view = View::factory('labyrinth/element/view');
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

    public function action_addNewElement() {
        $mapId = $this->request->param('id', NULL);
        $type = $this->request->param('id2', NULL);

        if ($mapId != NULL) {
            $map = DB_ORM::model('map', array((int) $mapId));
            $this->templateData['map'] = $map;

            if ($type != NULL) {
                $this->templateData['add_type'] = $type;
                $this->templateData['files'] = DB_ORM::model('map_element')->getAllMediaFiles((int) $mapId);
            }

            $this->templateData['types'] = DB_ORM::model('map_vpd_type')->getAllTypes();

            Breadcrumbs::add(Breadcrumb::factory()->set_title($this->templateData['map']->name)->set_url(URL::base() . 'labyrinthManager/global/' . $mapId));
            Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Elements'))->set_url(URL::base() . 'elementManager/index/' . $mapId));
            Breadcrumbs::add(Breadcrumb::factory()->set_title(__('New'))->set_url(URL::base() . 'elementManager/addNewElement/' . $mapId));
            $view = View::factory('labyrinth/element/add');
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

    public function action_saveElement() {
        $mapId = $this->request->param('id', NULL);
        $type = $this->request->param('id2', NULL);

        if ($_POST and $mapId != NULL and $type != NULL) {
            $private = Arr::get($_POST, 'Private');
            if($private){
                $_POST['Private'] = 'On';
            }else {
                $_POST['Private'] = 'Off';
            }
            DB_ORM::model('map_vpd')->createNewElement($mapId, $type, $_POST);
            Request::initial()->redirect(URL::base() . 'elementManager/index/' . $mapId);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

    public function action_updateElement() {
        $mapId = $this->request->param('id', NULL);
        $vpdId = $this->request->param('id2', NULL);

        if ($_POST and $mapId != NULL and $vpdId != NULL) {
            $private = Arr::get($_POST, 'Private');
            if($private){
                $_POST['Private'] = 'On';
            }else {
                $_POST['Private'] = 'Off';
            }
            $vpd = DB_ORM::model('map_node_reference')->getNotParent($mapId, $vpdId, 'VPD');
            if($private && $vpd != NULL){
                $ses = Session::instance();
                $ses->set('vpdmanager_ses', 'setPrivate');
                $_POST['Private'] = 'Off';
            }
            DB_ORM::model('map_vpd_element')->saveElementValues($vpdId, $_POST);
            Request::initial()->redirect(URL::base() . 'elementManager/index/' . $mapId);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

    public function action_deleteVpd() {
        $mapId = $this->request->param('id', NULL);
        $vpdId = $this->request->param('id2', NULL);

        if ($mapId != NULL and $vpdId != NULL) {
            $vpd = DB_ORM::model('map_node_reference')->getByElementType($vpdId, 'VPD');
            if($vpd != NULL){
              $ses = Session::instance();
              $ses->set('vpdmanager_ses', 'delVpd');
            } else {
                DB_ORM::model('map_vpd', array((int) $vpdId))->delete();
            }
            Request::initial()->redirect(URL::base() . 'elementManager/index/' . $mapId);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

    public function action_editVpd() {
        $mapId = $this->request->param('id', NULL);
        $vpdId = $this->request->param('id2', NULL);

        if ($mapId != NULL and $vpdId != NULL) {
            $map = DB_ORM::model('map', array((int) $mapId));
            $this->templateData['map'] = $map;

            $this->templateData['vpd'] = DB_ORM::model('map_vpd', array((int) $vpdId));
            $this->templateData['files'] = DB_ORM::model('map_element')->getAllMediaFiles((int) $mapId);

            $this->templateData['types'] = DB_ORM::model('map_vpd_type')->getAllTypes();

            $usedElements = DB_ORM::model('map_node_reference')->getByElementType($vpdId, 'VPD');
            $this->templateData['used'] = count($usedElements);

            Breadcrumbs::add(Breadcrumb::factory()->set_title($this->templateData['map']->name)->set_url(URL::base() . 'labyrinthManager/global/' . $mapId));
            Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Elements'))->set_url(URL::base() . 'elementManager/index/' . $mapId));
            Breadcrumbs::add(Breadcrumb::factory()->set_title($this->templateData['vpd']->id)->set_url(URL::base() . 'elementManager/editVpd/'  . '/'.$this->templateData['vpd']->id));
            $view = View::factory('labyrinth/element/edit');
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

}

