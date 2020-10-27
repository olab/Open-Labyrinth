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

class Controller_CollectionManager extends Controller_Base {

    public function before() {
        parent::before();

        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('My Collections'))->set_url(URL::base() . 'collectionManager'));
    }

    public function action_index() {
        $collections = DB_ORM::model('map_collection')->getAllCollections();
        $this->templateData['collections'] = $collections;

        $openView = View::factory('labyrinth/collection/view');
        $openView->set('templateData', $this->templateData);

        $this->templateData['center'] = $openView;
        unset($this->templateData['right']);
        $this->template->set('templateData', $this->templateData);
    }

    public function action_addCollection() {
        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Create Collection'))->set_url(URL::base() . 'collectionManager/addCollection'));

        $addView = View::factory('labyrinth/collection/add');
        $addView->set('templateData', $this->templateData);

        $this->templateData['center'] = $addView;
        unset($this->templateData['right']);
        $this->template->set('templateData', $this->templateData);
    }

    public function action_saveNewCollection() {
        if ($_POST) {
            $newCollection = DB_ORM::model('map_collection');
            $newCollection->name = Arr::get($_POST, 'colname', '');
            $newCollection->save();
            Controller::redirect(URL::base() . 'collectionManager');
        } else {
            Controller::redirect(URL::base());
        }
    }

    public function action_viewAll()
    {
        $collectionId = $this->request->param('id', 0);

        if ($collectionId) {
            $collectionMap = DB_ORM::model('map_collection', array((int) $collectionId));
            $this->templateData['canEdit'] = array();
            foreach ($collectionMap->maps as $map) {
                if ($this->checkTypeCompatibility($map->map->id)) $this->templateData['canEdit'][] = $map->map->id;
            }
            $this->templateData['collection'] = $collectionMap;
            $this->templateData['center'] = View::factory('labyrinth/collection/viewAll')->set('templateData', $this->templateData);
            $this->template->set('templateData', $this->templateData);
        } else {
            Controller::redirect(URL::base());
        }
        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('View Collection'))->set_url(URL::base() . 'collectionManager/viewAll/' . $collectionId));
    }

    public function action_editCollection()
    {
        $collectionId = $this->request->param('id', 0);

        if ($collectionId) {
            $collectionMap = DB_ORM::model('map_collection', array((int) $collectionId));
            $this->templateData['canEdit'] = array();
            foreach ($collectionMap->maps as $map) {
                if (DB_ORM::model('User')->can('edit', array('mapId' => $map->map->id))) $this->templateData['canEdit'][] = $map->map->id;
            }
            $this->templateData['collection']   = $collectionMap;
            $this->templateData['maps']         = DB_ORM::model('map_collectionMap')->getAllNotAddedMaps((int) $collectionId);
            $this->templateData['center']       = View::factory('labyrinth/collection/edit')->set('templateData', $this->templateData);

            $this->template->set('templateData', $this->templateData);
        } else {
            Controller::redirect(URL::base());
        }
        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Edit Collection'))->set_url(URL::base() . 'collectionManager/editCollection/' . $collectionId));
    }

    public function action_updateName() {
        $collectionId = $this->request->param('id', NULL);
        if ($_POST and $collectionId != NULL) {
            $collection = DB_ORM::model('map_collection', array((int) $collectionId));
            if ($collection) {
                $collection->name = Arr::get($_POST, 'colname', $collection->name);
                $collection->save();
            }

            Controller::redirect(URL::base() . 'collectionManager/editCollection/' . $collectionId);
        } else {
            Controller::redirect(URL::base());
        }
    }

    public function action_deleteMap() {
        $collectionId = $this->request->param('id', NULL);
        $mapId = $this->request->param('id2', NULL);
        if ($collectionId != NULL) {
            DB_ORM::model('map_collectionMap')->deleteByIDs($collectionId, $mapId);
            Controller::redirect(URL::base() . 'collectionManager/editCollection/'.$collectionId);
        } else {
            Controller::redirect(URL::base());
        }
    }

    public function action_addMap() {
        $collectionId = $this->request->param('id', NULL);
        if ($_POST and $collectionId != NULL) {
            $mapId = Arr::get($_POST, 'mapid', NULL);
            if ($mapId != NULL) {
                $new = DB_ORM::model('map_collectionMap');
                $new->collection_id = $collectionId;
                $new->map_id = $mapId;
                $new->save();
            }

            Controller::redirect(URL::base() . 'collectionManager/editCollection/' . $collectionId);
        } else {
            Controller::redirect(URL::base());
        }
    }

    /**
     * @param $idMap
     * @return bool - compatibility user type to map type
     */
    // method is cloned from renderLabyrinth.php
    public function checkTypeCompatibility ($idMap)
    {
        $logged         = Auth::instance()->logged_in();
        $userType       = false;
        $idUser         = false;
        $map            = DB_ORM::model('Map', array($idMap));
        $labyrinthType  = $map->security_id;
        $idScenario     = false;
        $assignUser     = false;
        if ($logged)
        {
            $user       = Auth::instance()->get_user();
            $userType   = $user->type_id;
            $idUser     = $user->id;
            $idScenario = DB_ORM::model('User_Session', array(Session::instance()->get('session_id')))->webinar_id;
            $assignUser = DB_ORM::model('Map_User')->assignOrNot($idMap, $user);
        }

        // first check by author_id, second check for author right
        $owner = $map->author_id == $idUser;
        if ( ! $owner AND $userType == 2) $owner = (bool) DB_ORM::select('Map_User')->where('user_id', '=', $idUser)->where('map_id', '=', $idMap)->query()->as_array();

        switch ($userType)
        {
            case '1':
                if ($assignUser OR
                    ($labyrinthType == 1) OR
                    ($labyrinthType == 2 AND $idScenario) OR
                    ($labyrinthType == 3 AND ($owner OR $idScenario))) return true;
                return false;
            case '2':
            case '3':
            case '6':
                if ($assignUser OR
                    ($labyrinthType == 1) OR
                    ($labyrinthType == 2) OR
                    ($labyrinthType == 3 AND ($owner OR $idScenario))) return true;
                return false;
            case '4':
                return true;
            default:
                if ($labyrinthType == 1) return true;
                return false;
        }
    }

}

