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

class Controller_AvatarManager extends Controller_Base {

    public function before() {
        $this->templateData['labyrinthSearch'] = 1;

        parent::before();

        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('My Labyrinths'))->set_url(URL::base() . 'authoredLabyrinth'));
    }

    public function action_index() {
        $mapId = $this->request->param('id', NULL);
        if ($mapId != NULL) {
            $this->templateData['map'] = DB_ORM::model('map', array((int) $mapId));
            $this->templateData['avatars'] = DB_ORM::model('map_avatar')->getAvatarsByMap((int) $mapId);

            Breadcrumbs::add(Breadcrumb::factory()->set_title($this->templateData['map']->name)->set_url(URL::base() . 'labyrinthManager/global/' . $mapId));
            Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Avatars'))->set_url(URL::base() . 'avatarManager/index/' . $mapId));

            $avatarView = View::factory('labyrinth/avatar/view');
            $avatarView->set('templateData', $this->templateData);

            $leftView = View::factory('labyrinth/labyrinthEditorMenu');
            $leftView->set('templateData', $this->templateData);

            $this->templateData['left'] = $leftView;
            $this->templateData['center'] = $avatarView;
            unset($this->templateData['right']);
            $this->template->set('templateData', $this->templateData);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

    public function action_addAvatar() {
        $mapId = $this->request->param('id', NULL);
        if ($mapId != NULL) {
            $avatarId = DB_ORM::model('map_avatar')->addAvatar($mapId);
            Request::initial()->redirect(URL::base() . 'avatarManager/editAvatar/' . $mapId . '/' . $avatarId);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

    public function action_editAvatar() {
        $mapId = $this->request->param('id', NULL);
        $avatarId = $this->request->param('id2', NULL);
        if ($mapId != NULL and $avatarId != NULL) {
            $this->templateData['map'] = DB_ORM::model('map', array((int) $mapId));
            $this->templateData['avatar'] = DB_ORM::model('map_avatar', array((int) $avatarId));

            Breadcrumbs::add(Breadcrumb::factory()->set_title($this->templateData['map']->name)->set_url(URL::base() . 'labyrinthManager/global/' . $mapId));
            Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Avatars'))->set_url(URL::base() . 'avatarManager/index/' . $mapId));
            Breadcrumbs::add(Breadcrumb::factory()->set_title($avatarId)->set_url(URL::base() . 'avatarManager/editAvatar/' . $mapId.'/'.$avatarId));
            $edtAvatarView = View::factory('labyrinth/avatar/edit');
            $edtAvatarView->set('templateData', $this->templateData);

            $leftView = View::factory('labyrinth/labyrinthEditorMenu');
            $leftView->set('templateData', $this->templateData);

            $this->templateData['left'] = $leftView;
            $this->templateData['center'] = $edtAvatarView;
            unset($this->templateData['right']);
            $this->template->set('templateData', $this->templateData);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

    public function action_deleteAvatar() {
        $mapId = $this->request->param('id', NULL);
        $avatarId = $this->request->param('id2', NULL);
        if ($mapId != NULL and $avatarId != NULL) {
            $upload_dir = DOCROOT . '/avatars/';
            $avatarImage = DB_ORM::model('map_avatar')->getAvatarImage($avatarId);
            if (!empty($avatarImage)) {
                @unlink($upload_dir . $avatarImage);
            }
            DB_ORM::model('map_avatar', array((int) $avatarId))->delete();
            Request::initial()->redirect(URL::base() . 'avatarManager/index/' . $mapId);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

    public function action_updateAvatar() {
        $mapId = $this->request->param('id', NULL);
        $avatarId = $this->request->param('id2', NULL);
        if ($_POST and $mapId != NULL and $avatarId != NULL) {
            $upload_dir = DOCROOT . '/avatars/';
            $avatarImage = DB_ORM::model('map_avatar')->getAvatarImage($avatarId);
            if (!empty($avatarImage)) {
                @unlink($upload_dir . $avatarImage);
            }
            $img = $_POST['image_data'];
            $img = str_replace('data:image/png;base64,', '', $img);
            $img = str_replace(' ', '+', $img);
            $data = base64_decode($img);
            $file = uniqid() . '.png';
            file_put_contents($upload_dir . $file, $data);
            $_POST['image_data'] = $file;
            DB_ORM::model('map_avatar')->updateAvatar($avatarId, $_POST);
            if ($_POST['save_exit_value'] == 0) {
                Request::initial()->redirect(URL::base() . 'avatarManager/editAvatar/' . $mapId . '/' . $avatarId);
            } else {
                Request::initial()->redirect(URL::base() . 'avatarManager/index/' . $mapId);
            }
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

    public function action_duplicateAvatar() {
        $mapId = $this->request->param('id', NULL);
        $avatarId = $this->request->param('id2', NULL);
        if ($mapId != NULL and $avatarId != NULL) {
            $avatarImage = DB_ORM::model('map_avatar')->getAvatarImage($avatarId);
            if (!empty($avatarImage)) {
                $upload_dir = DOCROOT . '/avatars/';
                $file = uniqid() . '.png';
                copy($upload_dir . $avatarImage, $upload_dir . $file);
            } else {
                $file = NULL;
            }
            DB_ORM::model('map_avatar')->duplicateAvatar($avatarId, $file);
            Request::initial()->redirect(URL::base() . 'avatarManager/index/' . $mapId);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

}

