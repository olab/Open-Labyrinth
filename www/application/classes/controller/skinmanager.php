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

class Controller_SkinManager extends Controller_Base {

    public function before() {
        parent::before();

        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('My Labyrinths'))->set_url(URL::base() . 'authoredLabyrinth'));
    }

    public function action_index() {
        $mapId = $this->request->param('id', NULL);
        if ($mapId != NULL) {
            $map = DB_ORM::model('map', array((int) $mapId));
            $this->templateData['map'] = $map;
            $this->templateData['skin'] = DB_ORM::model('map_skin', array($map->skin_id));

            Breadcrumbs::add(Breadcrumb::factory()->set_title($this->templateData['map']->name)->set_url(URL::base() . 'labyrinthManager/global/' . $mapId));
            Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Skin'))->set_url(URL::base() . 'skinManager/index/' . $mapId));

            $skinView = View::factory('labyrinth/skin/view');
            $skinView->set('templateData', $this->templateData);

            $leftView = View::factory('labyrinth/labyrinthEditorMenu');
            $leftView->set('templateData', $this->templateData);

            $this->templateData['left'] = $leftView;
            $this->templateData['center'] = $skinView;
            unset($this->templateData['right']);
            $this->template->set('templateData', $this->templateData);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

    public function action_createSkin() {
        $mapId = $this->request->param('id', NULL);
        $this->templateData['action_url'] = URL::base() . 'skinManager/saveSkin/' . $mapId;
        $this->template = View::factory('labyrinth/skin/create');
        $this->template->set('templateData', $this->templateData);
    }

    public function action_saveSkin() {
        $mapId = $this->request->param('id', NULL);
        if (isset($_POST['save'])) {
            $skin_name = $_POST['skin_name'];
            $centre = $_POST['centre'];
            $outside = $_POST['outside'];
            $folder = DOCROOT . 'css/skin/' . $mapId . '_' . $skin_name . '/';
            $skinName = $skin_name;
            $skinPath = $mapId . '_' . $skin_name;
            @mkdir($folder, 0777);

            $outside_image = $_POST['outside_image'];
            $centre_image = $_POST['centre_image'];

            if ($outside_image != null) {
                @rename(DOCROOT . "scripts/fileupload/php/files/" . $outside_image, $folder . $outside_image);
            }

            if ($centre_image != null) {
                @rename(DOCROOT . "scripts/fileupload/php/files/" . $centre_image, $folder . $centre_image);
            }

            $file = @fopen($folder . 'default.css', 'w+');

            $css = 'body {background-image: url("' . $outside_image . '"); background-color: ' . $outside['b-color'] . '; background-size: ' . $outside['b-size'] . '; background-repeat: ' . $outside['b-repeat'] . '; background-position: ' . $outside['b-position'] . ';} #centre_table {background-image: url("' . $centre_image . '"); background-size: ' . $centre['b-size'] . '; background-repeat: ' . $centre['b-repeat'] . '; background-position: ' . $centre['b-position'] . ';} .centre_td {background-color: ' . $centre['b-color'] . ';}';
            @fwrite($file, $css);

            $skin = DB_ORM::model('map_skin')->addSkin($skinName, $skinPath);
            DB_ORM::model('map')->updateMapSkin($mapId, $skin->id);
            Request::initial()->redirect(URL::base() . 'skinManager/index/' . $mapId);
        }
    }

    public function action_listSkins() {
        $mapId = $this->request->param('id', NULL);
        $this->templateData['map'] = DB_ORM::model('map', array((int) $mapId));
        $this->templateData['skinList'] = DB_ORM::model('map_skin')->getAllSkins();
        $this->templateData['skinId'] = $this->request->param('id2', NULL);
        $previewList = View::factory('labyrinth/skin/list');
        $previewList->set('templateData', $this->templateData);

        $leftView = View::factory('labyrinth/labyrinthEditorMenu');
        $leftView->set('templateData', $this->templateData);

        $this->templateData['left'] = $leftView;
        $this->templateData['center'] = $previewList;
        unset($this->templateData['right']);
        $this->template->set('templateData', $this->templateData);
    }

    public function action_saveSelectedSkin() {
        $mapId = $this->request->param('id', NULL);
        if ($_POST['skinId'] != 0 and $mapId != NULL) {
            DB_ORM::model('map')->updateMapSkin($mapId, $_POST['skinId']);
        }
        Request::initial()->redirect(URL::base() . 'skinManager/index/' . $mapId);
    }

    public function action_uploadSkin() {
        $mapId = $this->request->param('id', NULL);
        $this->templateData['map'] = DB_ORM::model('map', array((int) $mapId));
        $previewUpload = View::factory('labyrinth/skin/upload');
        $previewUpload->set('templateData', $this->templateData);

        $leftView = View::factory('labyrinth/labyrinthEditorMenu');
        $leftView->set('templateData', $this->templateData);

        $this->templateData['left'] = $leftView;
        $this->templateData['center'] = $previewUpload;
        unset($this->templateData['right']);
        $this->template->set('templateData', $this->templateData);
    }

    public function action_uploadNewSkin() {
        $mapId = $this->request->param('id', NULL);
        if (is_uploaded_file($_FILES['zipSkin']['tmp_name'])) {
            $ext = substr(($_FILES['zipSkin']['name']), -3);
            $filename = substr(($_FILES['zipSkin']['name']), 0, strlen($_FILES['zipSkin']['name']) - 4);
            if ($ext == 'zip') {
                $zip = new ZipArchive();
                $result = $zip->open($_FILES['zipSkin']['tmp_name']);
                if ($result === true) {
                    $zip->extractTo(DOCROOT . '/css/skin/');
                    $zip->close();
                }

                $skin = DB_ORM::model('map_skin')->addSkin($filename, $filename);
                DB_ORM::model('map')->updateMapSkin($mapId, $skin->id);
            }
        }
        Request::initial()->redirect(URL::base() . 'skinManager/index/' . $mapId);
    }

}

?>
