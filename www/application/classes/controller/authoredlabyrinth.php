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

    public function action_index() {
        if (Auth::instance()->get_user()->type->name == 'superuser') {
            $maps = DB_ORM::model('map')->getAllEnabledMap();
        } else {
            $maps = DB_ORM::model('map')->getAllEnabledAndAuthoredMap(Auth::instance()->get_user()->id);
        }
        $this->templateData['maps'] = $maps;

        $openView = View::factory('labyrinth/authored');
        $openView->set('templateData', $this->templateData);

        $this->templateData['center'] = $openView;
        unset($this->templateData['right']);
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

}