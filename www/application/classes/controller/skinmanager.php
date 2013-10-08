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
        $this->templateData['labyrinthSearch'] = 1;

        parent::before();

        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('My Labyrinths'))->set_url(URL::base() . 'authoredLabyrinth'));
    }

    public function action_index() {
        $mapId = $this->request->param('id', NULL);
        if ($mapId != NULL) {
            $map = DB_ORM::model('map', array((int) $mapId));
            $this->templateData['map'] = $map;
            $this->templateData['skin'] = DB_ORM::model('map_skin')->getSkinById($map->skin_id);
            $this->templateData['action'] = 'index';
            $navigation = View::factory('labyrinth/skin/navigation');
            $navigation->set('templateData', $this->templateData);
            $this->templateData['navigation'] = $navigation;

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
        $this->templateData['map'] = DB_ORM::model('map', array((int)$mapId));
        $this->templateData['action_url'] = URL::base() . 'skinManager/skinEditorUpload/' . $mapId;
        $skinId = $this->request->param('id2', NULL);
        Breadcrumbs::add(Breadcrumb::factory()->set_title($this->templateData['map']->name)->set_url(URL::base() . 'labyrinthManager/global/' . $mapId));
        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Skin'))->set_url(URL::base() . 'skinManager/index/' . $mapId));
        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Create a new skin'))->set_url(URL::base() . 'skinManager/createSkin/' . $mapId));
        if ($skinId != NULL) {
            $this->templateData['skinData'] = DB_ORM::model('map_skin', array($skinId));
            $this->template = View::factory('labyrinth/skin/skinEditor');
            $this->template->set('templateData', $this->templateData);
        } else {
            $this->templateData['skinData'] = NULL;
            $this->templateData['action'] = 'createSkin';
            $navigation = View::factory('labyrinth/skin/navigation');
            $navigation->set('templateData', $this->templateData);
            $this->templateData['navigation'] = $navigation;

            $createSkin = View::factory('labyrinth/skin/create');
            $createSkin->set('templateData', $this->templateData);

            $leftView = View::factory('labyrinth/labyrinthEditorMenu');
            $leftView->set('templateData', $this->templateData);

            $this->templateData['left'] = $leftView;
            $this->templateData['center'] = $createSkin;
            unset($this->templateData['right']);
            $this->template->set('templateData', $this->templateData);
        }

    }

    public function action_saveSkin() {
        $mapId = $this->request->param('id', NULL);
        if (isset($_POST['save'])) {
            $skin_name = $_POST['skin_name'];
            if ($skin_name == '') {
                $skin_name = rand(0, 100000);
            }
            $checkName = DB_ORM::model('map_skin')->getMapBySkin($skin_name);
            if ($checkName != NULL) {
                $skin_name .= rand(0, 100000);
            }

            $folder = DOCROOT . 'css/skin/' . $mapId . '_' . $skin_name . '/';
            $skinPath = $mapId . '_' . $skin_name;
            @mkdir($folder, 0777);

            $file = @fopen($folder . 'default.css', 'w+');
            $css = '/* Layout Stylesheet */';
            @fwrite($file, $css);
            @fclose($file);

            $skin = DB_ORM::model('map_skin')->addSkin($skin_name, $skinPath);
            DB_ORM::model('map')->updateMapSkin($mapId, $skin->id);

            Request::initial()->redirect(URL::base() . 'skinManager/createSkin/' . $mapId . '/' . $skin->id);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

    public function action_skinEditorUpload()
    {
        $mapId = $this->request->param('id', NULL);
        if ($mapId) {
            if (isset($_POST['save'])) {
                $skinId = $_POST['skinId'];
                $skinData = DB_ORM::model('map_skin', array($skinId));
                $centre = $_POST['centre'];
                $outside = $_POST['outside'];
                $folder = DOCROOT . 'css/skin/' . $skinData->path . '/';

                $outside_image = $_POST['outside_image'];
                $centre_image = $_POST['centre_image'];

                if (($outside_image != null) & ($outside_image != 'null')) {
                    @copy(DOCROOT . "scripts/fileupload/php/files/" . $outside_image, $folder . $outside_image);
                }

                if (($centre_image != null) & ($centre_image != 'null')) {
                    @copy(DOCROOT . "scripts/fileupload/php/files/" . $centre_image, $folder . $centre_image);
                }

                $file = @fopen($folder . 'default.css', 'w+');
                $css = 'p {'.PHP_EOL.'font-size: 80%;'.PHP_EOL.'}'.PHP_EOL.'h1, h2, h3, h4, h5 {'.PHP_EOL.'font-weight: bold;'.PHP_EOL.'color: #000000'.PHP_EOL.'}'.PHP_EOL.'h1 {'.PHP_EOL.'font-size: 262.5%;'.PHP_EOL.'}'.PHP_EOL.'h2 {'.PHP_EOL.'font-size: 187.5%;'.PHP_EOL.'}'.PHP_EOL.'h3 {'.PHP_EOL.'font-size: 150%;'.PHP_EOL.'}'.PHP_EOL.'h4 {'.PHP_EOL.'font-size: 125%;'.PHP_EOL.'}'.PHP_EOL.'h5 {'.PHP_EOL.'font-size: 60%;'.PHP_EOL.'font-weight: normal;'.PHP_EOL.'}'.PHP_EOL.'li {'.PHP_EOL.'font-size: 60%;'.PHP_EOL.'}'.PHP_EOL.'a:link{font-family: Arial, Helvetica, sans-serif;  font-style: normal; font-weight: normal; color: #111111;  text-decoration:none}'.PHP_EOL.'a:visited{font-family: Arial, Helvetica, sans-serif;  font-style: normal; font-weight: normal; color: #111111;  text-decoration:none}'.PHP_EOL.'a:hover{font-family: Arial, Helvetica, sans-serif; font-style: normal; font-weight: normal; color: #111111;}'.PHP_EOL.'a:active{font-family: Arial, Helvetica, sans-serif;  font-style: normal; font-weight: normal; color: #111111;}'.PHP_EOL.PHP_EOL;
                $css .= 'body {';
                if ((!empty($outside_image)) & ($outside_image != 'null')) {
                    $css .= PHP_EOL . 'background-image: url("' . $outside_image . '");' . PHP_EOL . 'background-size: ' . $outside['b-size'] . ';' . PHP_EOL . 'background-repeat: ' . $outside['b-repeat'] . ';' . PHP_EOL . 'background-position: ' . $outside['b-position'] . ';';
                }
                $css .= PHP_EOL . 'background-color: ' . $outside['b-color'] . ';' . PHP_EOL . '}' . PHP_EOL . PHP_EOL;
                $css .= '#centre_table {';
                if ((!empty($centre_image)) & ($centre_image != 'null')) {
                    $css .= PHP_EOL . 'background-image: url("' . $centre_image . '");' . PHP_EOL . 'background-size: ' . $centre['b-size'] . ';' . PHP_EOL . 'background-repeat: ' . $centre['b-repeat'] . ';' . PHP_EOL . 'background-position: ' . $centre['b-position'] . ';';
                }
                $css .= PHP_EOL . '}' . PHP_EOL . PHP_EOL . '.centre_td {' . PHP_EOL . 'background-color: ' . $centre['b-color'] . ';' . PHP_EOL . '}';
                @fwrite($file, $css);
                @fclose($file);
                die();
            }
        }
    }

    public function action_editSkins() {
        $mapId = $this->request->param('id', NULL);
        $this->templateData['map'] = DB_ORM::model('map', array((int) $mapId));
        $this->templateData['action'] = 'editSkins';
        $navigation = View::factory('labyrinth/skin/navigation');
        $navigation->set('templateData', $this->templateData);
        $this->templateData['navigation'] = $navigation;

        $skinId = $this->request->param('id2', NULL);
        if ($skinId != NULL) {
            $skinData = DB_ORM::model('map_skin')->getSkinById($skinId);
            $cssFile = DOCROOT . 'css/skin/' . $skinData->path . '/default.css';
            $this->templateData['css_content'] = file_get_contents($cssFile);
            $this->templateData['skinData'] = $skinData;

            Breadcrumbs::add(Breadcrumb::factory()->set_title($this->templateData['map']->name)->set_url(URL::base() . 'labyrinthManager/global/' . $mapId));
            Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Skin'))->set_url(URL::base() . 'skinManager/index/' . $mapId));
            Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Edit my skins'))->set_url(URL::base() . 'skinManager/editSkins/' . $mapId));
            Breadcrumbs::add(Breadcrumb::factory()->set_title(__($skinData->name))->set_url(URL::base() . 'skinManager/editSkins/' . $mapId.'/'.$skinData->id));

            $this->templateData['skinError'] = Session::instance()->get('skinError');
            Session::instance()->delete('skinError');
            $previewList = View::factory('labyrinth/skin/edit');
            $previewList->set('templateData', $this->templateData);
        } else {
            Breadcrumbs::add(Breadcrumb::factory()->set_title($this->templateData['map']->name)->set_url(URL::base() . 'labyrinthManager/global/' . $mapId));
            Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Skin'))->set_url(URL::base() . 'skinManager/index/' . $mapId));
            Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Edit my skins'))->set_url(URL::base() . 'skinManager/editSkins/' . $mapId));
            $this->templateData['skinList'] = DB_ORM::model('map_skin')->getSkinsByUserId(Auth::instance()->get_user()->id);
            $previewList = View::factory('labyrinth/skin/editList');
            $previewList->set('templateData', $this->templateData);
        }

        $leftView = View::factory('labyrinth/labyrinthEditorMenu');
        $leftView->set('templateData', $this->templateData);

        $this->templateData['left'] = $leftView;
        $this->templateData['center'] = $previewList;
        unset($this->templateData['right']);
        $this->template->set('templateData', $this->templateData);
    }

    public function action_skinsSaveChanges()
    {
        $mapId = $this->request->param('id', NULL);
        if ($_POST) {
            $id = $_POST['skinId'];
            $name = $_POST['name'];
            if ($name == '') {
                $name = rand(0, 100000);
            }
            DB_ORM::model('map_skin')->updateSkinName($id, $name, $mapId);
            $skinData = DB_ORM::model('map_skin')->getSkinById($id);
            $content = $_POST['css'];
            $cssFile = DOCROOT . 'css/skin/' . $skinData->path . '/default.css';
            file_put_contents($cssFile, $content);
            Request::initial()->redirect(URL::base() . 'skinManager/editSkins/' . $mapId . '/' . $id);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

    public function action_listSkins()
    {
        $mapId = $this->request->param('id', NULL);
        $this->templateData['map'] = DB_ORM::model('map', array((int)$mapId));
        $this->templateData['skinList'] = DB_ORM::model('map_skin')->getAllSkins();
        $this->templateData['skinId'] = $this->request->param('id2', NULL);
        $this->templateData['action'] = 'listSkins';
        $navigation = View::factory('labyrinth/skin/navigation');
        $navigation->set('templateData', $this->templateData);
        $this->templateData['navigation'] = $navigation;

        Breadcrumbs::add(Breadcrumb::factory()->set_title($this->templateData['map']->name)->set_url(URL::base() . 'labyrinthManager/global/' . $mapId));
        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Skin'))->set_url(URL::base() . 'skinManager/index/' . $mapId));
        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Select from a list of existing skins'))->set_url(URL::base() . 'skinManager/listSkins/' . $mapId.'/'.$this->templateData['skinId']));

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
        $this->templateData['action'] = 'uploadSkin';
        $navigation = View::factory('labyrinth/skin/navigation');
        $navigation->set('templateData', $this->templateData);
        $this->templateData['navigation'] = $navigation;

        Breadcrumbs::add(Breadcrumb::factory()->set_title($this->templateData['map']->name)->set_url(URL::base() . 'labyrinthManager/global/' . $mapId));
        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Skin'))->set_url(URL::base() . 'skinManager/index/' . $mapId));
        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Upload a new skin'))->set_url(URL::base() . 'skinManager/uploadSkin/' . $mapId));

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
            $checkName = DB_ORM::model('map_skin')->getMapBySkin($filename);
            if ($checkName != NULL) {
                $filename .= rand(0, 100000);
            }
            if ($ext == 'zip') {
                $zip = new ZipArchive();
                $result = $zip->open($_FILES['zipSkin']['tmp_name']);
                if ($result === true) {
                    $zip->extractTo(DOCROOT . '/css/skin/' . $filename);
                    $zip->close();
                }

                $skin = DB_ORM::model('map_skin')->addSkin($filename, $filename);
                DB_ORM::model('map')->updateMapSkin($mapId, $skin->id);
            }
        }
        Request::initial()->redirect(URL::base() . 'skinManager/index/' . $mapId);
    }

    public function action_deleteSkin(){
        $mapId = $this->request->param('id', 0);
        $skinId = $this->request->param('id2', 0);
        if ($mapId & $skinId){
            DB_ORM::model('map_skin')->deleteSkin($skinId);
            Request::initial()->redirect(URL::base() . 'skinManager/editSkins/' . $mapId);
        }
        Request::initial()->redirect(URL::base());
    }

}

