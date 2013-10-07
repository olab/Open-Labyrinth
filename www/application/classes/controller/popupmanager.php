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

class Controller_PopupManager extends Controller_Base {

    public function before() {
        parent::before();

        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('My Labyrinths'))->set_url(URL::base() . 'authoredLabyrinth'));
    }

    public function action_index() {

        $mapId = $this->request->param('id', NULL);
        if ($mapId != NULL) {
            $this->templateData['map'] = DB_ORM::model('map', array((int) $mapId));
            $this->templateData['messages'] = DB_ORM::model('map_popup')->getAllMessageByMap($mapId);

            Breadcrumbs::add(Breadcrumb::factory()->set_title($this->templateData['map']->name)->set_url(URL::base() . 'labyrinthManager/global/' . $mapId));
            Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Messages'))->set_url(URL::base() . 'popupManager/index/' . $mapId));

            $popupView = View::factory('labyrinth/popup/all');
            $popupView->set('templateData', $this->templateData);

            $leftView = View::factory('labyrinth/labyrinthEditorMenu');
            $leftView->set('templateData', $this->templateData);

            $this->templateData['center'] = $popupView;
            $this->templateData['left'] = $leftView;
            unset($this->templateData['right']);
            $this->template->set('templateData', $this->templateData);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

    public function action_addMessage(){
        $mapId = $this->request->param('id', NULL);
        if ($mapId != NULL) {
            $this->templateData['map'] = DB_ORM::model('map', array((int) $mapId));
            $this->templateData['positions'] = DB_ORM::model('map_popup_position')->getAllPositions();
            $this->templateData['styles'] = DB_ORM::model('map_popup_style')->getAllStyles();
            $this->templateData['nodes'] = DB_ORM::model('map_node')->getAllNode($mapId);

            Breadcrumbs::add(Breadcrumb::factory()->set_title($this->templateData['map']->name)->set_url(URL::base() . 'labyrinthManager/global/' . $mapId));
            Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Messages'))->set_url(URL::base() . 'popupManager/index/' . $mapId));
            Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Add Message'))->set_url(URL::base() . 'popupManager/addMessage/' . $mapId));

            $view = View::factory('labyrinth/popup/addEditMessage');
            $view->set('templateData', $this->templateData);

            $leftView = View::factory('labyrinth/labyrinthEditorMenu');
            $leftView->set('templateData', $this->templateData);

            $this->templateData['center'] = $view;
            $this->templateData['left'] = $leftView;
            unset($this->templateData['right']);
            $this->template->set('templateData', $this->templateData);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

    public function action_createMessage(){
        $mapId = $this->request->param('id', NULL);
        if ($_POST & $mapId != NULL) {

            if ($_POST['enabled'] == 0) {
                $_POST['time_before'] = 0;
                $_POST['time_length'] = 0;
            }

            DB_ORM::model('map_popup')->addMessage($mapId, $_POST);
            Request::initial()->redirect(URL::base() . 'popupManager/index/' . $mapId);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

    public function action_deleteMessage(){
        $mapId = $this->request->param('id', NULL);
        $messageId = $this->request->param('id2', NULL);
        if ($mapId != NULL & $messageId != NULL) {
            DB_ORM::model('map_popup', array((int) $messageId))->delete();
            Request::initial()->redirect(URL::base() . 'popupManager/index/' . $mapId);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

    public function action_editMessage(){
        $mapId = $this->request->param('id', NULL);
        $messageId = $this->request->param('id2', NULL);
        if ($mapId != NULL & $messageId != NULL) {
            $this->templateData['map'] = DB_ORM::model('map', array((int) $mapId));
            $this->templateData['message'] = DB_ORM::model('map_popup', array((int) $messageId));
            $this->templateData['positions'] = DB_ORM::model('map_popup_position')->getAllPositions();
            $this->templateData['styles'] = DB_ORM::model('map_popup_style')->getAllStyles();
            $this->templateData['nodes'] = DB_ORM::model('map_node')->getAllNode($mapId);

            Breadcrumbs::add(Breadcrumb::factory()->set_title($this->templateData['map']->name)->set_url(URL::base() . 'labyrinthManager/global/' . $mapId));
            Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Messages'))->set_url(URL::base() . 'popupManager/index/' . $mapId));
            Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Edit Message'))->set_url(URL::base() . 'popupManager/editMessage/' . $mapId));

            $view = View::factory('labyrinth/popup/addEditMessage');
            $view->set('templateData', $this->templateData);

            $leftView = View::factory('labyrinth/labyrinthEditorMenu');
            $leftView->set('templateData', $this->templateData);

            $this->templateData['center'] = $view;
            $this->templateData['left'] = $leftView;
            unset($this->templateData['right']);
            $this->template->set('templateData', $this->templateData);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

    public function action_updateMessage(){
        $mapId = $this->request->param('id', NULL);
        $messageId = $this->request->param('id2', NULL);
        if ($_POST & $mapId != NULL & $messageId != NULL) {

            if ($_POST['enabled'] == 0) {
                $_POST['time_before'] = 0;
                $_POST['time_length'] = 0;
            }

            DB_ORM::model('map_popup')->editMessage($messageId, $_POST);
            Request::initial()->redirect(URL::base() . 'popupManager/index/' . $mapId);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

}
