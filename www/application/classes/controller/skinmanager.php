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

            //$folder = DOCROOT . 'css/skin/' . $mapId . '_' . $skin_name . '/';
            $skinPath = $mapId . '_' . $skin_name;
            //@mkdir($folder, 0777);

            //$file = @fopen($folder . 'default.css', 'w+');
            //$css = '/* Layout Stylesheet */';
            //@fwrite($file, $css);
            //@fclose($file);

            $skin = DB_ORM::model('map_skin')->addSkin($skin_name, $skinPath);
            DB_ORM::model('map')->updateMapSkin($mapId, $skin->id);
            DB_ORM::model('map_skin')->updateSkinData($skin->id,
                                                      '{"tree": [{"data":"Root","attr":{"id":"objectid-63a18691-2f1e-40a8-f2b5-48d4b58d8b93","class":"","rel":"root"},"state":"open","metadata":{},"children":[{"data":"Block component","attr":{"id":"objectid-36a280ec-b8f7-416d-c36a-db3a326522dc","class":""},"state":"open","metadata":{},"children":[{"data":"Node title component","attr":{"id":"objectid-da3e11a8-8bb1-4c5b-77dc-0c42f0bdd432"},"metadata":{}},{"data":"Image component","attr":{"id":"objectid-40948c57-1bae-47af-8c95-f26260aa2967"},"metadata":{}},{"data":"Node content component","attr":{"id":"objectid-1870d9e7-6b6e-4b68-a95a-571567f5b236"},"metadata":{}},{"data":"Counters container component","attr":{"id":"objectid-08b152a3-7cb9-40ee-af72-1f733c35eb41","class":""},"metadata":{}}]}]}], "components": [{"id":"objectid-da3e11a8-8bb1-4c5b-77dc-0c42f0bdd432","parentId":"objectid-36a280ec-b8f7-416d-c36a-db3a326522dc","type":"nodetitle","BorderSize":null,"BorderColor":null,"BorderType":null,"BorderRadius":null,"Float":null,"Clear":null,"Align":"center","FontFamily":"Arial","FontSize":"16px","FontWeight":"bold","FontColor":"#000","MarginTop":"auto","MarginRight":"auto","MarginBottom":"auto","MarginLeft":"auto","Position":"inherit","Left":"auto","Top":"auto","Right":"auto","Bottom":"auto"}, {"id":"objectid-08b152a3-7cb9-40ee-af72-1f733c35eb41","parentId":"objectid-36a280ec-b8f7-416d-c36a-db3a326522dc","type":"counterscontainer","BorderSize":null,"BorderColor":null,"BorderType":null,"BorderRadius":null,"Float":null,"Clear":null,"FontFamily":"Arial","FontSize":"10px","FontWeight":"normal","FontColor":"#e62d2d","MarginTop":"auto","MarginRight":"auto","MarginBottom":"auto","MarginLeft":"auto","Align":"right","Position":"inherit","Left":"auto","Top":"auto","Right":"auto","Bottom":"auto"}, {"id":"objectid-40948c57-1bae-47af-8c95-f26260aa2967","parentId":"objectid-36a280ec-b8f7-416d-c36a-db3a326522dc","type":"image","Width":"100","MinWidth":null,"MaxWidth":null,"Height":"auto","MinHeight":null,"MaxHeight":null,"BorderSize":null,"BorderColor":"transperent","BorderType":null,"BorderRadius":null,"Float":"none","MarginTop":"auto","MarginRight":"auto","MarginBottom":"auto","MarginLeft":"auto","Position":"inherit","Left":"auto","Top":"auto","Right":"auto","Bottom":"auto","Src":"../../../scripts/skineditor/css/no.gif"}, {"id":"objectid-70b7c35e-b3c3-4ee8-561f-9ca8209ae2cf","parentId":"objectid-f3eba5cb-6605-46a5-d0fe-ee104832297a","type":"nodetitle","BorderSize":null,"BorderColor":null,"BorderType":null,"BorderRadius":null,"Float":null,"Clear":null,"Align":null,"FontFamily":"Arial","FontSize":12,"FontWeight":"bold","FontColor":"#000","MarginTop":"auto","MarginRight":"auto","MarginBottom":"auto","MarginLeft":"auto","Position":"inherit","Left":"auto","Top":"auto","Right":"auto","Bottom":"auto"}, {"id":"objectid-1870d9e7-6b6e-4b68-a95a-571567f5b236","parentId":"objectid-36a280ec-b8f7-416d-c36a-db3a326522dc","type":"nodecontent","BorderSize":null,"BorderColor":null,"BorderType":null,"BorderRadius":null,"Float":null,"Clear":null,"Align":null,"FontFamily":"Arial","FontSize":12,"FontWeight":"normal","FontColor":"#000","MarginTop":"auto","MarginRight":"auto","MarginBottom":"auto","MarginLeft":"auto","Position":"inherit","Left":"auto","Top":"auto","Right":"auto","Bottom":"auto"}, {"id":"objectid-36a280ec-b8f7-416d-c36a-db3a326522dc","parentId":"objectid-63a18691-2f1e-40a8-f2b5-48d4b58d8b93","type":"block","Width":"100%","MinWidth":null,"MaxWidth":null,"Height":"100%","MinHeight":null,"MaxHeight":null,"BorderSize":"0px","BorderColor":"#504f4f","BorderType":"dashed","BorderRadius":null,"Float":"none","Clear":null,"BackgroundColor":"transparent","BackgroundURL":null,"BackgroundRepeat":"repeat","BackgroundPosition":"0% 0%","MarginTop":"auto","MarginRight":"auto","MarginBottom":"auto","MarginLeft":"auto","PaddingTop":"auto","PaddingRight":"auto","PaddingBottom":"auto","PaddingLeft":"auto","Align":"justify","Position":"inherit","Left":"auto","Top":"auto","Right":"auto","Bottom":"auto"}, {"id":"objectid-63a18691-2f1e-40a8-f2b5-48d4b58d8b93","parentId":null,"type":"root"}], "html": "PGRpdiBpZD0ib2JqZWN0aWQtMzZhMjgwZWMtYjhmNy00MTZkLWMzNmEtZGIzYTMyNjUyMmRjIiBjbGFzcz0idWktcmVzaXphYmxlIiBzdHlsZT0id2lkdGg6IDEwMCU7IGhlaWdodDogMTAwJTsgYm9yZGVyOiAwcHggZGFzaGVkIHJnYig4MCwgNzksIDc5KTsgZmxvYXQ6IG5vbmU7IHRvcDogMHB4OyBsZWZ0OiAwcHg7Ij48ZGl2IGlkPSJvYmplY3RpZC1kYTNlMTFhOC04YmIxLTRjNWItNzdkYy0wYzQyZjBiZGQ0MzIiIHN0eWxlPSJ3aWR0aDogYXV0bzsgaGVpZ2h0OiBhdXRvOyBjb2xvcjogcmdiKDAsIDAsIDApOyB0ZXh0LWFsaWduOiBjZW50ZXI7IGZvbnQtc2l6ZTogMTZweDsgZm9udC13ZWlnaHQ6IGJvbGQ7IiBjbGFzcz0iIj57Tk9ERV9USVRMRX08L2Rpdj48aW1nIGlkPSJvYmplY3RpZC00MDk0OGM1Ny0xYmFlLTQ3YWYtOGM5NS1mMjYyNjBhYTI5NjciIHNyYz0iLi4vLi4vLi4vc2NyaXB0cy9za2luZWRpdG9yL2Nzcy9uby5naWYiIHN0eWxlPSJ3aWR0aDogMTAwcHg7IGhlaWdodDogYXV0bzsgZmxvYXQ6IG5vbmU7IiBjbGFzcz0iIj48ZGl2IGlkPSJvYmplY3RpZC0xODcwZDllNy02YjZlLTRiNjgtYTk1YS01NzE1NjdmNWIyMzYiIHN0eWxlPSJ3aWR0aDogYXV0bzsgaGVpZ2h0OiBhdXRvOyBjb2xvcjogcmdiKDAsIDAsIDApOyIgY2xhc3M9IiI+e05PREVfQ09OVEVOVH08L2Rpdj48ZGl2IGlkPSJvYmplY3RpZC0wOGIxNTJhMy03Y2I5LTQwZWUtYWY3Mi0xZjczM2MzNWViNDEiIHN0eWxlPSJ3aWR0aDogYXV0bzsgaGVpZ2h0OiBhdXRvOyBjb2xvcjogcmdiKDIzMCwgNDUsIDQ1KTsgdGV4dC1hbGlnbjogcmlnaHQ7IGZvbnQtc2l6ZTogMTBweDsiIGNsYXNzPSJjb21wb25lbnQtc2VsZWN0ZWQiPntDT1VOVEVSU308L2Rpdj48ZGl2IGNsYXNzPSJ1aS1yZXNpemFibGUtaGFuZGxlIHVpLXJlc2l6YWJsZS1lIiBzdHlsZT0iei1pbmRleDogMTAwMDsiPjwvZGl2PjxkaXYgY2xhc3M9InVpLXJlc2l6YWJsZS1oYW5kbGUgdWktcmVzaXphYmxlLXMiIHN0eWxlPSJ6LWluZGV4OiAxMDAwOyI+PC9kaXY+PGRpdiBjbGFzcz0idWktcmVzaXphYmxlLWhhbmRsZSB1aS1yZXNpemFibGUtc2UgdWktaWNvbiB1aS1pY29uLWdyaXBzbWFsbC1kaWFnb25hbC1zZSIgc3R5bGU9InotaW5kZXg6IDEwMDA7Ij48L2Rpdj48L2Rpdj4="}',
                                                      '<div id="objectid-ff19d0fc-6b42-439d-9db4-d245414df64c" class="ui-resizable" style="width: 90%; height: 100%; border: 0px dashed rgb(80, 79, 79); float: none; top: 0px; left: 0px; padding-top: 0px; margin: 20px auto 0px;"><div id="objectid-23db1186-d7c6-4a98-6951-f4bf4e71f05e" class="ui-resizable" style="width: 78%; height: 100%; border: 0px dashed rgb(80, 79, 79); float: left; background-color: rgb(255, 255, 255); padding: 10px;"><div id="objectid-2a5d0909-f739-476b-262c-2b2de1b1e71b" style="width: auto; height: auto; color: rgb(0, 0, 0); font-size: 26px; font-weight: bold; text-align: left;" class="">{NODE_TITLE}</div><div id="objectid-ec8279db-df41-4b93-8652-809545d9b3f1" style="width: auto; height: auto; color: rgb(0, 0, 0); text-align: justify; margin-top: 16px;" class="">{NODE_CONTENT}</div><div id="objectid-a2b26a51-fa9d-44f5-3c57-ccbbb505a750" class="ui-resizable" style="width: 50%; height: 100%; border: 0px dashed rgb(80, 79, 79); float: left;"><div id="objectid-6eda7e63-ba82-4f16-1776-641dc087c014" style="color: rgb(0, 0, 0); text-align: left;" class="">{LINKS}</div><div class="ui-resizable-handle ui-resizable-e" style="z-index: 1000;"></div><div class="ui-resizable-handle ui-resizable-s" style="z-index: 1000;"></div><div class="ui-resizable-handle ui-resizable-se ui-icon ui-icon-gripsmall-diagonal-se" style="z-index: 1000;"></div></div><div id="objectid-b95ea42e-7597-4750-d664-ed7600210aa4" class="ui-resizable" style="width: 49%; height: 100%; border: 0px dashed rgb(80, 79, 79); float: left; text-align: right;"><div id="objectid-e3f1ed24-0ed1-47d1-6729-ed59a1452a2f" style="width: auto; height: auto; color: rgb(0, 0, 0);" class="">{COUNTERS}</div><div class="ui-resizable-handle ui-resizable-e" style="z-index: 1000;"></div><div class="ui-resizable-handle ui-resizable-s" style="z-index: 1000;"></div><div class="ui-resizable-handle ui-resizable-se ui-icon ui-icon-gripsmall-diagonal-se" style="z-index: 1000;"></div></div><div class="ui-resizable-handle ui-resizable-e" style="z-index: 1000;"></div><div class="ui-resizable-handle ui-resizable-s" style="z-index: 1000;"></div><div class="ui-resizable-handle ui-resizable-se ui-icon ui-icon-gripsmall-diagonal-se" style="z-index: 1000;"></div></div><div id="objectid-0ccb7522-6fc3-4e37-548b-4db9f9136c1d" class="ui-resizable component-selected" style="width: 19%; height: 200px; border: 0px dashed rgb(80, 79, 79); float: left; margin-left: 5px; background-color: rgb(255, 255, 255); top: 0px; left: 0px;"><div id="objectid-d1765db4-9178-4eec-d7ef-3212275c3472" class="" style="color: rgb(0, 0, 0); font-size: 11px; text-align: left; margin: 10px 10px 20px;">{MAP_INFO}</div><div id="objectid-4f4bc601-14bb-4722-902d-bbe109ee1197" class="ui-resizable" style="width: 148px; height: 54px; border: 0px dashed rgb(80, 79, 79); float: none; text-align: left; top: 0px; left: 0px; margin-left: 10px;"><div class="ui-resizable-handle ui-resizable-e" style="z-index: 1000;"></div><div class="ui-resizable-handle ui-resizable-s" style="z-index: 1000;"></div><div class="ui-resizable-handle ui-resizable-se ui-icon ui-icon-gripsmall-diagonal-se" style="z-index: 1000;"></div><div id="objectid-298a819b-a452-4cf1-bdf3-890e2c0eadd9" class="">{BOOKMARK}</div></div><div id="objectid-2ceff777-ffc7-4ffc-e3ed-2238e8a24b15" class="ui-resizable" style="width: 143px; height: 24px; border: 0px dashed rgb(80, 79, 79); float: none; text-align: left; padding: 10px; top: 0px; left: 0px; clear: both;"><img id="objectid-82e476f6-49b9-4e9b-9dba-7a0d1377acfb" src="data:image/jpeg;base64,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" style="width: 130px; height: auto; float: none;" class=""><div class="ui-resizable-handle ui-resizable-e" style="z-index: 1000;"></div><div class="ui-resizable-handle ui-resizable-s" style="z-index: 1000;"></div><div class="ui-resizable-handle ui-resizable-se ui-icon ui-icon-gripsmall-diagonal-se" style="z-index: 1000;"></div></div><div class="ui-resizable-handle ui-resizable-e" style="z-index: 1000;"></div><div class="ui-resizable-handle ui-resizable-s" style="z-index: 1000;"></div><div class="ui-resizable-handle ui-resizable-se ui-icon ui-icon-gripsmall-diagonal-se" style="z-index: 1000;"></div></div><div id="objectid-60e108f9-254d-4df1-03c3-d81c7c98a767" class="ui-resizable" style="width: 79%; height: 100%; border: 0px dashed rgb(80, 79, 79); float: none; clear: both;"><div id="objectid-f3d9a18b-5122-442c-0778-1d428ff6bf9e" style="color: rgb(0, 0, 0); text-align: left;" class="">{REVIEW}</div><div class="ui-resizable-handle ui-resizable-e" style="z-index: 1000;"></div><div class="ui-resizable-handle ui-resizable-s" style="z-index: 1000;"></div><div class="ui-resizable-handle ui-resizable-se ui-icon ui-icon-gripsmall-diagonal-se" style="z-index: 1000;"></div></div><div class="ui-resizable-handle ui-resizable-e" style="z-index: 1000;"></div><div class="ui-resizable-handle ui-resizable-s" style="z-index: 1000;"></div><div class="ui-resizable-handle ui-resizable-se ui-icon ui-icon-gripsmall-diagonal-se" style="z-index: 1000;"></div></div>');

            Request::initial()->redirect(URL::base() . 'skinManager/editSkins/' . $mapId . '/' . $skin->id);
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
            $this->templateData['skinData'] = $skinData;

            $this->templateData['skinError'] = Session::instance()->get('skinError');
            Session::instance()->delete('skinError');
            $this->template = View::factory('labyrinth/skin/skinEditor');
            $this->template->set('templateData', $this->templateData);
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

    public function action_updateSkinData() {
        $this->auto_render = false;

        $skinId = Arr::get($_POST, 'skinId', null);
        if($skinId == null) { echo '{status: "error", errorMessage: "Wrong Skin ID"}'; return; }

        $data = Arr::get($_POST, 'data', null);
        $html = Arr::get($_POST, 'html', null);
        $body = Arr::get($_POST, 'body', null);
        DB_ORM::model('map_skin')->updateSkinData($skinId, $data, $html, $body);

        echo '{status: "ok"}';
    }
}