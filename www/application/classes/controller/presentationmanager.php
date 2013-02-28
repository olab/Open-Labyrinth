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

class Controller_PresentationManager extends Controller_Base {

    public function before() {
        parent::before();

        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Manage Presentations'))->set_url(URL::base() . 'presentationmanager'));

        $this->template->set('templateData', $this->templateData);
    }

    public function action_index() {
        $this->templateData['presentations'] = DB_ORM::model('map_presentation')->getAllPresentations(Auth::instance()->get_user()->id);

        $presentationView = View::factory('presentation');
        $presentationView->set('templateData', $this->templateData);

        $this->templateData['center'] = $presentationView;
        unset($this->templateData['left']);
        unset($this->templateData['right']);
        $this->template->set('templateData', $this->templateData);
    }

    public function action_addPresentation() {
        if ($_POST and Auth::instance()->logged_in()) {
            DB_ORM::model('map_presentation')->addPresentation(Auth::instance()->get_user()->id, $_POST);
            Request::initial()->redirect(URL::base() . 'presentationManager');
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

    public function action_editPresentation() {
        $presentationId = $this->request->param('id', NULL);
        if ($presentationId != NULL) {
            $this->templateData['presentation'] = DB_ORM::model('map_presentation', array((int) $presentationId));

            if (count($this->templateData['presentation']->maps) > 0) {
                $mapIDs = array();
                foreach ($this->templateData['presentation']->maps as $mp) {
                    $mapIDs[] = $mp->map->id;
                }

                $this->templateData['maps'] = DB_ORM::model('map')->getMaps($mapIDs);
            } else {
                $this->templateData['maps'] = DB_ORM::model('map')->getAllMap();
            }
            Breadcrumbs::add(Breadcrumb::factory()->set_title($this->templateData['presentation']->title)->set_url(URL::base() . 'presentationmanager/editPresentation'.$presentationId));
            $userIDs = array();
            $userIDs[] = $this->templateData['presentation']->author->id;
            foreach ($this->templateData['presentation']->users as $user) {
                $userIDs[] = $user->user->id;
            }

            $this->templateData['notUsers'] = DB_ORM::model('user')->getAllUserWithNotId($userIDs);
            $this->templateData['groups'] = DB_ORM::model('group')->getAllGroups();

            $editPresentationView = View::factory('presentation/edit');
            $editPresentationView->set('templateData', $this->templateData);

            $this->templateData['center'] = $editPresentationView;
            unset($this->templateData['left']);
            unset($this->templateData['right']);
            $this->template->set('templateData', $this->templateData);
        } else {
            Request::initial()->redirect(URL::base() . 'presentationManager');
        }
    }

    public function action_addMap() {
        $presentationId = $this->request->param('id', NULL);
        if ($_POST and $presentationId != NULL) {
            DB_ORM::model('map_presentation_map')->add($presentationId, Arr::get($_POST, 'labid', NULL));
            Request::initial()->redirect(URL::base() . 'presentationManager/editPresentation/' . $presentationId);
        } else {
            Request::initial()->redirect(URL::base() . 'presentationManager');
        }
    }

    public function action_deleteMap() {
        $presentationId = $this->request->param('id', NULL);
        $mapId = $this->request->param('id2', NULL);
        if ($presentationId != NULL and $mapId != NULL) {
            DB_ORM::model('map_presentation_map', array((int) $mapId))->delete();
            Request::initial()->redirect(URL::base() . 'presentationManager/editPresentation/' . $presentationId);
        } else {
            Request::initial()->redirect(URL::base() . 'presentationManager');
        }
    }

    public function action_deletePresentation() {
        $presentationId = $this->request->param('id', NULL);
        if ($presentationId != NULL) {
            DB_ORM::model('map_presentation', array((int) $presentationId))->delete();
            Request::initial()->redirect(URL::base() . 'presentationManager');
        } else {
            Request::initial()->redirect(URL::base() . 'presentationManager');
        }
    }

    public function action_updatePresentation() {
        $presentationId = $this->request->param('id', NULL);
        if ($_POST and $presentationId != NULL) {
            DB_ORM::model('map_presentation')->updatePresentation($presentationId, $_POST);
            Request::initial()->redirect(URL::base() . 'presentationManager/editPresentation/' . $presentationId);
        } else {
            Request::initial()->redirect(URL::base() . 'presentationManager');
        }
    }

    public function action_resetSecurity() {
        $presentationId = $this->request->param('id', NULL);
        $securityId = $this->request->param('id2', NULL);
        if ($securityId != NULL and $presentationId != NULL) {
            $presentation = DB_ORM::model('map_presentation', array((int) $presentationId));

            if (count($presentation->maps) > 0) {
                foreach ($presentation->maps as $mp) {
                    DB_ORM::model('map')->updateMapSecurity($mp->map->id, $securityId);
                }
            }
            Request::initial()->redirect(URL::base() . 'presentationManager/editPresentation/' . $presentationId);
        } else {
            Request::initial()->redirect(URL::base() . 'presentationManager');
        }
    }

    public function action_addUser() {
        $presentationId = $this->request->param('id', NULL);
        if ($_POST and $presentationId != NULL) {
            $userId = Arr::get($_POST, 'presUserID', NULL);
            if ($userId != NULL) {
                $type = substr($userId, 0, 2);
                switch ($type) {
                    case 'u:':
                        DB_ORM::model('map_presentation_user')->add($presentationId, (int) substr($userId, 2, strlen($userId)));
                        break;
                    case 'g:':
                        $typeName = Arr::get($_POST, 'presUserType', NULL);
                        DB_ORM::model('map_presentation_user')->addUsersFromGroup($presentationId, (int) substr($userId, 2, strlen($userId)), $typeName);
                        break;
                }
            }
            Request::initial()->redirect(URL::base() . 'presentationManager/editPresentation/' . $presentationId);
        } else {
            Request::initial()->redirect(URL::base() . 'presentationManager');
        }
    }

    public function action_deleteUser() {
        $presentationId = $this->request->param('id', NULL);
        $userId = $this->request->param('id2', NULL);
        if ($userId != NULL and $presentationId != NULL) {
            DB_ORM::model('map_presentation_user', array((int) $userId))->delete();
            Request::initial()->redirect(URL::base() . 'presentationManager/editPresentation/' . $presentationId);
        } else {
            Request::initial()->redirect(URL::base() . 'presentationManager');
        }
    }

    public function action_render() {
        $presentationId = $this->request->param('id', NULL);
        if ($this->checkUser($presentationId)) {
            $this->templateData['presentation'] = DB_ORM::model('map_presentation', array((int) $presentationId));

            $renderView = View::factory('presentation/render');
            $renderView->set('templateData', $this->templateData);

            $this->template = $renderView;
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

    private function checkUser($presentationId) {
        $presentation = DB_ORM::model('map_presentation', array((int) $presentationId));
        if ($presentation) {

            if (Auth::instance()->logged_in()) {

                $userIDs = array();
                $userIDs[] = $presentation->author->id;
                foreach ($presentation->users as $user) {
                    $userIDs[] = $user->user_id;
                }

                if (array_search(Auth::instance()->get_user()->id, $userIDs) === FALSE) {
                    return FALSE;
                } else {
                    if ($presentation->access == 1) {
                        if (Auth::instance()->get_user()->type->name == 'learner') {
                            return FALSE;
                        }
                    }
                    return TRUE;
                }
            }

            return FALSE;
        }

        return FALSE;
    }

}

