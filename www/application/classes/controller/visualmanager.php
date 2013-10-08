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

class Controller_VisualManager extends Controller_Base {

    public function before() {
        $this->templateData['labyrinthSearch'] = 1;

        parent::before();

        $this->mapId = (int) $this->request->param('id', 0);
        if ($this->mapId) {
            $this->templateData['map'] = DB_ORM::model('map', array($this->mapId));
            Breadcrumbs::add(Breadcrumb::factory()->set_title(__('My Labyrinths'))->set_url(URL::base() . 'authoredLabyrinth'));
            Breadcrumbs::add(Breadcrumb::factory()->set_title($this->templateData['map']->name)->set_url(URL::base() . 'labyrinthManager/global/' . $this->mapId));
        }
    }

    public function action_index() {
        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Visual Editor'))->set_url(URL::base() . 'visualManager/index/' . $this->mapId));

        $saveJson = '';

        $this->templateData['node'] = DB_ORM::model('map_node')->getRootNodeByMap($this->mapId);
        $this->templateData['counters'] = DB_ORM::model('map_counter')->getCountersByMap((int)$this->mapId);
        $this->templateData['linkStyles'] = DB_ORM::model('map_node_link_style')->getAllLinkStyles();
        $this->templateData['priorities'] = DB_ORM::model('map_node_priority')->getAllPriorities();

        $json = Model::factory('visualEditor')->generateJSON($this->mapId);

        $this->templateData['mapJSON'] = $json;

        if($saveJson != null)
            $this->templateData['saveMapJSON'] = '\'' . (strlen($saveJson->json) > 0 ? $saveJson->json : 'empty') . '\'';

        if(Auth::instance()->logged_in()) {
            $user = Auth::instance()->get_user();
            if($user != null) {
                $this->templateData['user'] = DB_ORM::model('user', array($user->id));
            }
        }

        $visualView = View::factory('labyrinth/visual');
        $visualView->set('templateData', $this->templateData);

        $leftView = View::factory('labyrinth/labyrinthEditorMenu');
        $leftView->set('templateData', $this->templateData);

        $this->templateData['left'] = $leftView;
        $this->templateData['center'] = $visualView;
        unset($this->templateData['right']);
        $this->template->set('templateData', $this->templateData);
    }

    public function action_indexOriginal() {
        $mapId = $this->request->param('id', NULL);

        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Visual Editor'))->set_url(URL::base() . 'visualManager/index/' . $mapId));

        if ($mapId != NULL) {
            $this->templateData['map'] = DB_ORM::model('map', array((int) $mapId));

            Model::factory('visualEditor')->generateXML((int) $mapId);

            $visualView = View::factory('labyrinth/visual');
            $visualView->set('templateData', $this->templateData);

            $leftView = View::factory('labyrinth/labyrinthEditorMenu');
            $leftView->set('templateData', $this->templateData);

            $this->templateData['left'] = $leftView;
            $this->templateData['center'] = $visualView;
            unset($this->templateData['right']);
            $this->template->set('templateData', $this->templateData);
        }
    }

    public function action_update() {
        $mapId = $this->request->param('id', NULL);
        $emap = Arr::get($_POST, 'emap', NULL);
        $elink = Arr::get($_POST, 'elink', NULL);
        $enode = Arr::get($_POST, 'enode', NULL);

        $this->templateData['map'] = DB_ORM::model('map', array((int) $mapId));

        Model::factory('visualEditor')->update($mapId, $emap, $enode, $elink);
        Model::factory('visualEditor')->generateXML((int) $mapId);

        $visualView = View::factory('labyrinth/visual');
        $visualView->set('templateData', $this->templateData);

        $leftView = View::factory('labyrinth/labyrinthEditorMenu');
        $leftView->set('templateData', $this->templateData);

        $this->templateData['left'] = $leftView;
        $this->templateData['center'] = $visualView;
        unset($this->templateData['right']);
        $this->template->set('templateData', $this->templateData);
    }

    public function action_updateJSON() {
        $mapId = Arr::get($_POST, 'id', null);
        $json = Arr::get($_POST, 'data', null);

        $this->auto_render = false;
        Model::factory('visualEditor')->updateFromJSON($mapId, $json);

        echo Model::factory('visualEditor')->generateJSON($mapId);
    }


    public function action_bufferCopy() {
        $json = Arr::get($_POST, 'data', null);

        if(Auth::instance()->logged_in()) {
            Session::instance()->set('buffer', $json);
        }
    }

    public function action_bufferPaste() {
        $this->auto_render = false;
        $result = '';
        if(Auth::instance()->logged_in()) {
            $buffer = Session::instance()->get('buffer', NULL);

            $result = $buffer != null ? $buffer : '';
        }

        echo $result;
    }

    public function action_updateSettings() {
        $this->auto_render = false;

        $time = Arr::get($_POST, 'time', 50000);
        if(Auth::instance()->logged_in()) {
            $user = Auth::instance()->get_user();
            if($user != null) {
                DB_ORM::model('user')->updateSettings($user->id, array('time' => $time));
                Auth::instance()->login($user->username, $user->password);
            }
        }
    }
}