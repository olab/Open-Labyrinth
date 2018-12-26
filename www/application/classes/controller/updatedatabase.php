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

    public function action_rollback() {
        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Rolling Back Database')));

        $result = $this->request->param('id');

        $message = '<div class="alert alert-warning"><b>Attention!</b> This action could permanently delete some of your data.</div>';
        if($result !== null) {
            switch ($result) {
                case 0:
                    $message = '<div class="alert alert-info"><span class="lead">Roll back instructions was not found.</span></div>';
                    break;

                case 1:
                    $message = '<div class="alert alert-success"><span class="lead">Database has been successfully Rolling Back.</span></div>';
                    break;

                case 2:
                    $message = '<div class="alert alert-error"><span class="lead">Update directory "' . URL::base() . 'updates/roll_back" was not found.</span></div>';
                    break;
            }
        }

        $dir = DOCROOT.'updates/roll_back/';
        if(is_dir($dir)) {
            $files = scandir($dir);
            array_shift($files);
            array_shift($files);
            if(count($files) > 0){
                usort($files, array('Updates', 'sortVersionInOrder'));
                $files = array_reverse($files);
                $versions = array();
                foreach($files as $file){
                    $ext = pathinfo($file, PATHINFO_EXTENSION);
                    if ($ext == 'sql') {
                        $versions[] = pathinfo($file, PATHINFO_FILENAME);
                    }
                }

                if(count($versions) > 0){
                    array_shift($versions);
                }

                $this->templateData['versions'] = $versions;
            }
        }

        $this->templateData['message'] = $message;
        $this->templateData['center'] = View::factory('updatedatabase/rollback')->set('templateData', $this->templateData);
        unset($this->templateData['left']);
        unset($this->templateData['right']);
        $this->template->set('templateData', $this->templateData);
    }

    public function action_doRollback(){
        $post = $this->request->post();
        $toVersion = Arr::get($post, 'toVersion', null);
        $result = Updates::rollback($toVersion);
        Controller::redirect(URL::base(true).'updatedatabase/rollback/'.$result);
    }
}