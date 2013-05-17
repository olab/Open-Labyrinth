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

class Controller_UpdateDatabase extends Controller_Base {

    public function action_index() {
        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Updating Database'))->set_url(URL::base() . 'updateDatabase'));

        $result = Updates::update();

        switch ($result){
            case 0:
                $center = '<div class="alert alert-info"><span class="lead">New updates to the database was not found.</span></div><div><a class="btn" href="'.URL::base().'">Go to the home page</a></div>';
                break;

            case 1:
                $center = '<div class="alert alert-success"><span class="lead">Database has been successfully updated.</span></div><div><a class="btn" href="'.URL::base().'">Go to the home page</a></div>';
                break;

            case 2:
                $center = '<div class="alert alert-error"><span class="lead">Update directory "'.URL::base().'updates" was not found.</span></div><div><a class="btn" href="'.URL::base().'">Go to the home page</a></div>';
                break;

            case 3:
                $center = '<div class="alert alert-error"><span class="lead">Update directory "'.URL::base().'updates" is not writable. Please check permissions to folder in your server.</span></div><div><a class="btn" href="'.URL::base().'">Go to the home page</a></div>';
                break;
        }

        $this->templateData['center'] = $center;
        unset($this->templateData['left']);
        unset($this->templateData['right']);
        $this->template->set('templateData', $this->templateData);
    }
}