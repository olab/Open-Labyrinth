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
            Request::initial()->redirect(URL::base() . 'collectionManager');
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

    public function action_viewAll(){
        $collectionId = $this->request->param('id', 0);
        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('View Collection'))->set_url(URL::base() . 'collectionManager/viewAll/' . $collectionId));

        if ($collectionId) {
            $this->templateData['collection'] = DB_ORM::model('map_collection', array((int) $collectionId));

            $editView = View::factory('labyrinth/collection/viewAll');
            $editView->set('templateData', $this->templateData);

            $this->templateData['center'] = $editView;
            unset($this->templateData['right']);
            $this->template->set('templateData', $this->templateData);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

    public function action_editCollection() {
        $collectionId = $this->request->param('id', 0);
        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Edit Collection'))->set_url(URL::base() . 'collectionManager/editCollection/' . $collectionId));

        if ($collectionId) {
            $this->templateData['collection'] = DB_ORM::model('map_collection', array((int) $collectionId));
            $this->templateData['maps'] = DB_ORM::model('map_collectionMap')->getAllNotAddedMaps((int) $collectionId);

            $editView = View::factory('labyrinth/collection/edit');
            $editView->set('templateData', $this->templateData);

            $this->templateData['center'] = $editView;
            unset($this->templateData['right']);
            $this->template->set('templateData', $this->templateData);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

    public function action_updateName() {
        $collectionId = $this->request->param('id', NULL);
        if ($_POST and $collectionId != NULL) {
            $collection = DB_ORM::model('map_collection', array((int) $collectionId));
            if ($collection) {
                $collection->name = Arr::get($_POST, 'colname', $collection->name);
                $collection->save();
            }

            Request::initial()->redirect(URL::base() . 'collectionManager/editCollection/' . $collectionId);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

    public function action_deleteMap() {
        $collectionId = $this->request->param('id', NULL);
        $mapId = $this->request->param('id2', NULL);
        if ($collectionId != NULL) {
            DB_ORM::model('map_collectionMap')->deleteByIDs($collectionId, $mapId);
            Request::initial()->redirect(URL::base() . 'collectionManager/editCollection/'.$collectionId);
        } else {
            Request::initial()->redirect(URL::base());
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

            Request::initial()->redirect(URL::base() . 'collectionManager/editCollection/' . $collectionId);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

}

