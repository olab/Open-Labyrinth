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

class Controller_RemoteServiceManager extends Controller_Base {

    public function before() {
        parent::before();

        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Manage Remote Services'))->set_url(URL::base() . 'remoteservicemanager'));

        $this->template->set('templateData', $this->templateData);
    }

    public function action_index() {
        $this->templateData['services'] = DB_ORM::model('remoteService')->getAllServices();

        $remoteView = View::factory('remote');
        $remoteView->set('templateData', $this->templateData);

        $this->templateData['center'] = $remoteView;
        unset($this->templateData['left']);
        unset($this->templateData['right']);
        $this->template->set('templateData', $this->templateData);
    }

    public function action_addService() {
        $this->templateData['users'] = DB_ORM::model('user')->getUsersByTypeName('remote service');

        $addRemoteView = View::factory('remote/add');
        $addRemoteView->set('templateData', $this->templateData);

        $this->templateData['center'] = $addRemoteView;
        unset($this->templateData['left']);
        unset($this->templateData['right']);
        $this->template->set('templateData', $this->templateData);
    }

    public function action_saveNewService() {
        if ($_POST) {
            DB_ORM::model('remoteService')->addNewService($_POST);
        }

        Request::initial()->redirect(URL::base() . 'remoteServiceManager');
    }

    public function action_editService() {
        $serviceId = $this->request->param('id', NULL);
        if ($serviceId != NULL) {
            $this->templateData['service'] = DB_ORM::model('remoteService', array((int) $serviceId));
            $this->templateData['users'] = DB_ORM::model('user')->getUsersByTypeName('remote service');

            $editRemoteView = View::factory('remote/edit');
            $editRemoteView->set('templateData', $this->templateData);

            $this->templateData['center'] = $editRemoteView;
            unset($this->templateData['left']);
            unset($this->templateData['right']);
            $this->template->set('templateData', $this->templateData);
        } else {
            Request::initial()->redirect(URL::base() . 'remoteServiceManager');
        }
    }

    public function action_editServiceMap() {
        $serviceId = $this->request->param('id', NULL);
        if ($serviceId != NULL) {
            $this->templateData['service'] = DB_ORM::model('remoteService', array((int) $serviceId));
            if (count($this->templateData['service']->maps) > 0) {
                $mapIDs = array();
                foreach ($this->templateData['service']->maps as $map) {
                    $mapIDs[] = $map->map_id;
                }

                $this->templateData['maps'] = DB_ORM::model('map')->getMaps($mapIDs);
            } else {
                $this->templateData['maps'] = DB_ORM::model('map')->getAllMap();
            }

            $editRemoteView = View::factory('remote/editMap');
            $editRemoteView->set('templateData', $this->templateData);

            $this->templateData['center'] = $editRemoteView;
            unset($this->templateData['left']);
            unset($this->templateData['right']);
            $this->template->set('templateData', $this->templateData);
        } else {
            Request::initial()->redirect(URL::base() . 'remoteServiceManager');
        }
    }

    public function action_addMap() {
        $serviceId = $this->request->param('id', NULL);
        if ($_POST and $serviceId != NULL) {
            $mapId = Arr::get($_POST, 'mapid', NULL);
            if ($mapId != NULL) {
                DB_ORM::model('remoteMap')->addMap($mapId, $serviceId);
            }

            Request::initial()->redirect(URL::base() . 'remoteServiceManager/editServiceMap/' . $serviceId);
        } else {
            Request::initial()->redirect(URL::base() . 'remoteServiceManager');
        }
    }

    public function action_deleteMap() {
        $serviceId = $this->request->param('id', NULL);
        $mapId = $this->request->param('id2', NULL);
        if ($mapId != NULL and $serviceId != NULL) {
            DB_ORM::model('remoteMap', array((int) $mapId))->delete();
            Request::initial()->redirect(URL::base() . 'remoteServiceManager/editServiceMap/' . $serviceId);
        } else {
            Request::initial()->redirect(URL::base() . 'remoteServiceManager');
        }
    }

    public function action_updateService() {
        $serviceId = $this->request->param('id', NULL);
        if ($_POST and $serviceId != NULL) {
            DB_ORM::model('remoteService')->updateService($serviceId, $_POST);
            Request::initial()->redirect(URL::base() . 'remoteServiceManager/editService/' . $serviceId);
        } else {
            Request::initial()->redirect(URL::base() . 'remoteServiceManager');
        }
    }

}

?>