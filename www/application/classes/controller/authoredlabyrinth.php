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

class Controller_AuthoredLabyrinth extends Controller_Base {

    public function before() {
        parent::before();

        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('My Labyrinths'))->set_url(URL::base() . 'authoredLabyrinth'));
    }

    public function action_index()
    {
        $user = Auth::instance()->get_user();
        $this->templateData['maps']     = ($user->type->name == 'superuser') ? DB_ORM::model('map')->getAllEnabledMap() : DB_ORM::model('map')->getAllEnabledAndAuthoredMap($user->id);
        $this->templateData['center']   = View::factory('labyrinth/authored')->set('templateData', $this->templateData);
        $this->template->set('templateData', $this->templateData);
    }

    public function action_info() {
        $mapId = (int) $this->request->param('id', 0);
        if ($mapId) {
            Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Labyrinth Details'))->set_url(URL::base() . 'authoredLabyrinth/info/' . $mapId));

            $this->templateData['map'] = DB_ORM::model('map', array($mapId));

            $infoView = View::factory('labyrinth/labyrinthInfo');
            $infoView->set('templateData', $this->templateData);

            $this->templateData['center'] = $infoView;
            unset($this->templateData['right']);
            $this->template->set('templateData', $this->templateData);
        } else {
            Request::initial()->redirect(URL::base() . 'openLabyrinth');
        }
    }

    public function action_duplicate()
    {
        $mapId = (int) $this->request->param('id', 0);
        if ($mapId) DB_ORM::model('map')->duplicateMap($mapId);
        Request::initial()->redirect(URL::base().'authoredLabyrinth');
    }

}