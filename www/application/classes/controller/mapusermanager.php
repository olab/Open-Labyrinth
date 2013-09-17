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

class Controller_MapUserManager extends Controller_Base {

    public function before() {
        $this->templateData['labyrinthSearch'] = 1;

        parent::before();

        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('My Labyrinths'))->set_url(URL::base() . 'authoredLabyrinth'));
    }

    public function action_index() {
        $mapId = $this->request->param('id', NULL);
        $authorOrder = $this->request->param('id2', 0);
        $learnerOrder = $this->request->param('id3', 0);

        if ($mapId != NULL) {
            $this->templateData['map'] = DB_ORM::model('map', array((int) $mapId));

            $userIds = DB_ORM::model('map_user')->getAllUsersIds((int) $mapId);

            $authorOrder = $authorOrder == 0 ? 'ASC' : 'DESC';
            $learnerOrder = $learnerOrder == 0 ? 'ASC' : 'DESC';

            $this->templateData['authorOrder']   = $authorOrder  == 'ASC' ? 0 : 1;
            $this->templateData['learnerOrder']  = $learnerOrder == 'ASC' ? 0 : 1;

            $this->templateData['existAuthors']  = DB_ORM::model('map_user')->getAllAuthors((int) $mapId, $authorOrder);
            $this->templateData['existLearners'] = DB_ORM::model('map_user')->getAllLearners((int) $mapId, $learnerOrder);

            $this->templateData['admins']        = DB_ORM::model('user')->getUsersByTypeName('superuser', $userIds, $authorOrder);
            $this->templateData['authors']       = DB_ORM::model('user')->getUsersByTypeName('author', $userIds, $authorOrder);
            $this->templateData['learners']      = DB_ORM::model('user')->getUsersByTypeName('learner', $userIds, $learnerOrder);
            $this->templateData['allAdmins']     = array_merge((array)$this->templateData['admins'], (array)$this->templateData['authors']);

            Breadcrumbs::add(Breadcrumb::factory()->set_title($this->templateData['map']->name)->set_url(URL::base() . 'labyrinthManager/global/' . $mapId));
            Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Users'))->set_url(URL::base() . 'mapUserManager/index/' . $mapId));

            $mapUserView = View::factory('labyrinth/user/view');
            $mapUserView->set('templateData', $this->templateData);

            $leftView = View::factory('labyrinth/labyrinthEditorMenu');
            $leftView->set('templateData', $this->templateData);

            $this->templateData['left'] = $leftView;
            $this->templateData['center'] = $mapUserView;
            unset($this->templateData['right']);
            $this->template->set('templateData', $this->templateData);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

    public function action_addUser() {
        $mapId = $this->request->param('id', NULL);
        $authorOrder = $this->request->param('id2', 0);
        $learnerOrder = $this->request->param('id3', 0);
        if ($_POST and $mapId != NULL) {
            $existAuthors  = DB_ORM::model('map_user')->getAllAuthors((int) $mapId);
            $existLearners = DB_ORM::model('map_user')->getAllLearners((int) $mapId);

            $existUserMap = array();
            if($existAuthors != null && count($existAuthors) > 0) {
                foreach($existAuthors as $author) {
                    $existUserMap[$author->id] = $author;
                }
            }

            if($existLearners != null && count($existLearners) > 0) {
                foreach($existLearners as $learner) {
                    $existUserMap[$learner->id] = $learner;
                }
            }

            $admins        = DB_ORM::model('user')->getUsersByTypeName('superuser');
            $authors       = DB_ORM::model('user')->getUsersByTypeName('author');
            $learners      = DB_ORM::model('user')->getUsersByTypeName('learner');
            $allUsers      = array_merge($admins, $authors, $learners);

            if(count($allUsers) > 0) {
                foreach($allUsers as $user) {
                    $isExist = Arr::get($_POST, 'user' . $user->id, null);
                    if($isExist != null) {
                        if(!isset($existUserMap[$user->id])) {
                            DB_ORM::model('map_user')->addUser($mapId, $user->id);
                        }
                    } else {
                        DB_ORM::model('map_user')->deleteByUserId($mapId, $user->id);
                    }
                }
            }

            Request::initial()->redirect(URL::base() . 'mapUserManager/index/' . $mapId . '/' . $authorOrder . '/' . $learnerOrder);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

    public function action_deleteUser() {
        $mapId = $this->request->param('id', NULL);
        $userId = $this->request->param('id2', NULL);
        if ($mapId != NULL) {
            DB_ORM::model('map_user')->deleteByUserId($mapId, $userId);
            Request::initial()->redirect(URL::base() . 'mapUserManager/index/' . $mapId);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

    public function action_addAllLearners() {
        $mapId = $this->request->param('id', NULL);
        $authorOrder = $this->request->param('id2', 0);
        $learnerOrder = $this->request->param('id3', 0);
        if ($mapId != NULL) {
            DB_ORM::model('map_user')->addAllLearners($mapId);
            Request::initial()->redirect(URL::base() . 'mapUserManager/index/' . $mapId . '/' . $authorOrder . '/' . $learnerOrder);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

    public function action_removeAllLearners() {
        $mapId = $this->request->param('id', NULL);
        $authorOrder = $this->request->param('id2', 0);
        $learnerOrder = $this->request->param('id3', 0);
        if ($mapId != NULL) {
            DB_ORM::model('map_user')->removeAllLearners($mapId);
            Request::initial()->redirect(URL::base() . 'mapUserManager/index/' . $mapId . '/' . $authorOrder . '/' . $learnerOrder);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

    public function action_addAllAuthors() {
        $mapId = $this->request->param('id', NULL);
        if ($mapId != NULL) {
            DB_ORM::model('map_user')->addAllAuthors($mapId);
            Request::initial()->redirect(URL::base() . 'mapUserManager/index/' . $mapId);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

    public function action_removeAllAuthors() {
        $mapId = $this->request->param('id', NULL);
        if ($mapId != NULL) {
            DB_ORM::model('map_user')->removeAllAuthors($mapId);
            Request::initial()->redirect(URL::base() . 'mapUserManager/index/' . $mapId);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

}

