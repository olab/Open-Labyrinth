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

class Controller_Base extends Controller_Template {

    public $template = 'home';
    protected $templateData = array();

    public function before() {
        parent::before();

        if (Auth::instance()->logged_in()) {
            I18n::lang(Auth::instance()->get_user()->language->key);
            $this->templateData['username'] = Auth::instance()->get_user()->nickname;

            Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Home'))->set_url(URL::base()));

            if (Auth::instance()->get_user()->type->name == 'superuser' or Auth::instance()->get_user()->type->name == 'author') {
                $centerView = View::factory('adminMenu');

                /*
                 * Fetch the latest authored labyrinths.
                 */
                if (Auth::instance()->get_user()->type->name == 'superuser') {
                    $maps = DB_ORM::model('map')->getAllEnabledMap(7);
                } else {
                    $maps = DB_ORM::model('map')->getAllEnabledAndAuthoredMap(Auth::instance()->get_user()->id, 7);
                }

                $this->templateData['latestAuthoredLabyrinths'] = $maps;

                /*
                 * Fetch the latest played labyrinths.
                 */
                $mapIDs = array();
                $sessions = DB_ORM::model('user_session')->getAllSessionByUser(Auth::instance()->get_user()->id, 7);
                if (count($sessions) > 0) {
                    foreach ($sessions as $s) {
                        $mapIDs[] = $s->map_id;
                    }
                }

                if (count($mapIDs) > 0) {
                    $this->templateData['latestPlayedLabyrinths'] = DB_ORM::model('map')->getMapsIn($mapIDs);
                }

                $this->templateData['center'] = $centerView;
                $centerView->set('templateData', $this->templateData);
            } else {
                $centerView = View::factory('userMenu');
                $centerView->set('openLabyrinths', DB_ORM::model('map')->getAllMapsForRegisteredUser(Auth::instance()->get_user()->id));
                $centerView->set('presentations', DB_ORM::model('map_presentation')->getPresentationsByUserId(Auth::instance()->get_user()->id));

                $centerView->set('templateData', $this->templateData);
                $this->templateData['center'] = $centerView;
            }
        } else {
            $this->templateData['left'] = View::factory('login');

            $centerView = View::factory('userMenu');

            $centerView->set('openLabyrinths', DB_ORM::model('map')->getAllEnabledOpenVisibleMap());

            $centerView->set('templateData', $this->templateData);
            $this->templateData['center'] = $centerView;
        }

        $this->templateData['title'] = 'OpenLabyrinth';
        $this->template->set('templateData', $this->templateData);
    }

}

