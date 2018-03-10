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

    public function after() {
        unset($this->templateData['right']);
        $this->template->set('templateData', $this->templateData);

        parent::after();
    }

    public function action_index()
    {
        $mapId = $this->request->param('id', null);

        if ($mapId == null) Request::initial()->redirect(URL::base());

        DB_ORM::model('User')->can('edit', array('mapId' => $mapId));

        $this->templateData['map']    = DB_ORM::model('map', array((int) $mapId));
        $this->templateData['popups'] = DB_ORM::model('map_popup')->getAllMapPopups($mapId);
        $this->templateData['center'] = View::factory('labyrinth/popup/list')->set('templateData', $this->templateData);
        $this->templateData['left']   = View::factory('labyrinth/labyrinthEditorMenu')->set('templateData', $this->templateData);

        Breadcrumbs::add(Breadcrumb::factory()->set_title($this->templateData['map']->name)->set_url(URL::base() . 'labyrinthManager/global/' . $mapId));
        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Messages'))->set_url(URL::base() . 'popupManager/index/' . $mapId));
    }

    public function action_newPopup()
    {
        $mapId = $this->request->param('id', null);

        if ($mapId == null) Request::initial()->redirect(URL::base());

        DB_ORM::model('User')->can('edit', array('mapId' => $mapId));

        $this->preparePopupData($mapId);

        $this->templateData['counters'] = DB_ORM::model('map_counter')->getCountersByMap($mapId);
        $this->templateData['center']   = View::factory('labyrinth/popup/popup')->set('templateData', $this->templateData);
        $this->templateData['left']     = View::factory('labyrinth/labyrinthEditorMenu')->set('templateData', $this->templateData);

        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Add Message'))->set_url(URL::base() . 'popupManager/addMessage/' . $mapId));
    }

    public function action_editPopup()
    {
        $popupId = $this->request->param('id', null);

        if($popupId == null) Request::initial()->redirect(URL::base());

        $popup = DB_ORM::model('map_popup', array((int)$popupId));
        $mapId = $popup->map_id;

        DB_ORM::model('User')->can('edit', array('mapId' => $mapId));

        // get redirect_type_id
        foreach (DB_ORM::model('map_popup', array((int)$popupId))->assign as $obj) $redirect_type_id = $obj->redirect_type_id;

        $this->preparePopupData($mapId);
        $this->templateData['popup']    = $popup;
        $this->templateData['counters'] = DB_ORM::model('map_counter')->getCountersByMap($mapId);
        $this->templateData['center']   = View::factory('labyrinth/popup/popup')->set('templateData', $this->templateData)->bind('redirect_type_id', $redirect_type_id);
        $this->templateData['left']     = View::factory('labyrinth/labyrinthEditorMenu')->set('templateData', $this->templateData);

        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Edit Message'))->set_url(URL::base() . 'popupManager/editPopup/' . $mapId));
    }

    public function action_savePopup() {
        $mapId    = $this->request->param('id', null);
        $redirect = URL::base().'popupManager/index/'.$mapId;

        if($mapId != null) {
            $popupId = DB_ORM::model('map_popup')->savePopup($mapId, $_POST);
            if($popupId != null) {
                $redirect = URL::base().'popupManager/editPopup/'.$popupId;
            }
        }

        Request::initial()->redirect($redirect);
    }

    public function action_deletePopup()
    {
        $mapId       = $this->request->param('id', null);
        $popupId     = $this->request->param('id2', null);
        $redirectURL = URL::base();

        if ($mapId != NULL & $popupId != NULL)
        {
            DB_ORM::model('User')->can('edit', array('mapId' => $mapId));

            DB_ORM::model('map_popup_assign', array((int) $popupId))->delete();
            DB_ORM::model('map_popup_style', array((int) $popupId))->delete();
            DB_ORM::model('map_popup_counter')->deleteCounters((int) $popupId, 'popup_id');
            DB_ORM::model('map_popup', array((int) $popupId))->delete();

            $redirectURL .= 'popupManager/index/' . $mapId;
        }

        Request::initial()->redirect($redirectURL);
    }

    private function preparePopupData($mapId) {
        $this->templateData['map']                = DB_ORM::model('map', array($mapId));
        $this->templateData['popupPositionTypes'] = DB_ORM::model('map_popup_position_type')->getAll();
        $this->templateData['popupPositions']     = DB_ORM::model('map_popup_position')->getAll();
        $this->templateData['popupAssignTypes']   = DB_ORM::model('map_popup_assign_type')->getAll();
        $this->templateData['nodes']              = DB_ORM::model('map_node')->getNodesByMap($mapId);
        $this->templateData['sections']           = DB_ORM::model('map_node_section')->getAllSectionsByMap($mapId);

        Breadcrumbs::add(Breadcrumb::factory()->set_title($this->templateData['map']->name)->set_url(URL::base() . 'labyrinthManager/global/' . $mapId));
        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Messages'))->set_url(URL::base() . 'popupManager/index/' . $mapId));
    }
}