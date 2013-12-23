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
                                                      '{"tree": [{"data":"Root","attr":{"id":"objectid-6985d501-126d-4ad6-82cb-b621aa47e8b0","class":"","rel":"root"},"state":"open","metadata":{},"children":[{"data":"Block component","attr":{"id":"objectid-ff19d0fc-6b42-439d-9db4-d245414df64c","class":""},"state":"open","metadata":{},"children":[{"data":"Block component","attr":{"id":"objectid-23db1186-d7c6-4a98-6951-f4bf4e71f05e"},"state":"open","metadata":{},"children":[{"data":"Node title component","attr":{"id":"objectid-2a5d0909-f739-476b-262c-2b2de1b1e71b"},"metadata":{}},{"data":"Node content component","attr":{"id":"objectid-ec8279db-df41-4b93-8652-809545d9b3f1"},"metadata":{}},{"data":"Block component","attr":{"id":"objectid-a2b26a51-fa9d-44f5-3c57-ccbbb505a750"},"state":"open","metadata":{},"children":[{"data":"Links component","attr":{"id":"objectid-6eda7e63-ba82-4f16-1776-641dc087c014","class":""},"metadata":{}}]},{"data":"Block component","attr":{"id":"objectid-b95ea42e-7597-4750-d664-ed7600210aa4","class":""},"state":"open","metadata":{},"children":[{"data":"Counters container component","attr":{"id":"objectid-e3f1ed24-0ed1-47d1-6729-ed59a1452a2f","class":""},"metadata":{}}]}]},{"data":"Block component","attr":{"id":"objectid-0ccb7522-6fc3-4e37-548b-4db9f9136c1d"},"state":"open","metadata":{},"children":[{"data":"Map Info component","attr":{"id":"objectid-d1765db4-9178-4eec-d7ef-3212275c3472"},"metadata":{}},{"data":"Block component","attr":{"id":"objectid-4f4bc601-14bb-4722-902d-bbe109ee1197"},"state":"open","metadata":{},"children":[{"data":"Bookmark component","attr":{"id":"objectid-298a819b-a452-4cf1-bdf3-890e2c0eadd9","class":""},"metadata":{}}]},{"data":"Block component","attr":{"id":"objectid-2ceff777-ffc7-4ffc-e3ed-2238e8a24b15","class":""},"state":"open","metadata":{},"children":[{"data":"Image component","attr":{"id":"objectid-82e476f6-49b9-4e9b-9dba-7a0d1377acfb","class":""},"metadata":{}}]}]},{"data":"Block component","attr":{"id":"objectid-60e108f9-254d-4df1-03c3-d81c7c98a767","class":""},"state":"open","metadata":{},"children":[{"data":"Review component","attr":{"id":"objectid-f3d9a18b-5122-442c-0778-1d428ff6bf9e","class":""},"metadata":{}}]}]}]}], "components": [{"id":"objectid-4f4bc601-14bb-4722-902d-bbe109ee1197","parentId":"objectid-0ccb7522-6fc3-4e37-548b-4db9f9136c1d","type":"block","Width":174,"MinWidth":null,"MaxWidth":null,"Height":60,"MinHeight":null,"MaxHeight":null,"BorderSize":"0px","BorderColor":"#504f4f","BorderType":"dashed","BorderRadius":null,"Float":"none","Clear":null,"BackgroundColor":"transparent","BackgroundURL":null,"BackgroundRepeat":"repeat","BackgroundPosition":"0% 0%","MarginTop":"auto","MarginRight":"auto","MarginBottom":"auto","MarginLeft":"10px","PaddingTop":"auto","PaddingRight":"auto","PaddingBottom":"auto","PaddingLeft":"auto","Align":"left","Position":"inherit","Left":"auto","Top":"auto","Right":"auto","Bottom":"auto"}, {"id":"objectid-298a819b-a452-4cf1-bdf3-890e2c0eadd9","parentId":"objectid-4f4bc601-14bb-4722-902d-bbe109ee1197","type":"bookmark"}, {"id":"objectid-d1765db4-9178-4eec-d7ef-3212275c3472","parentId":"objectid-0ccb7522-6fc3-4e37-548b-4db9f9136c1d","type":"links","BorderSize":null,"BorderColor":null,"BorderType":null,"BorderRadius":null,"Float":"none","Clear":null,"FontFamily":"Arial","FontSize":"11px","FontWeight":"normal","FontColor":"#e58585","BackgroundColor":"#df3c20","BackgroundURL":null,"BackgroundRepeat":"repeat","BackgroundPosition":"0% 0%","MarginTop":"10px","MarginRight":"10px","MarginBottom":"20px","MarginLeft":"10px","PaddingTop":"auto","PaddingRight":"auto","PaddingBottom":"auto","PaddingLeft":"auto","Position":"absolute","Left":"auto","Top":"auto","Right":"auto","Bottom":"0px"}, {"id":"objectid-2ceff777-ffc7-4ffc-e3ed-2238e8a24b15","parentId":"objectid-0ccb7522-6fc3-4e37-548b-4db9f9136c1d","type":"block","Width":"70%","MinWidth":null,"MaxWidth":null,"Height":73,"MinHeight":null,"MaxHeight":null,"BorderSize":"0px","BorderColor":"#504f4f","BorderType":"dashed","BorderRadius":null,"Float":"none","Clear":"both","BackgroundColor":"transparent","BackgroundURL":null,"BackgroundRepeat":"repeat","BackgroundPosition":"0% 0%","MarginTop":"auto","MarginRight":"auto","MarginBottom":"auto","MarginLeft":"auto","PaddingTop":"10px","PaddingRight":"10px","PaddingBottom":"10px","PaddingLeft":"10px","Align":"left","Position":"inherit","Left":"auto","Top":"auto","Right":"auto","Bottom":"auto"}, {"id":"objectid-82e476f6-49b9-4e9b-9dba-7a0d1377acfb","parentId":"objectid-2ceff777-ffc7-4ffc-e3ed-2238e8a24b15","type":"image","Width":"130px","MinWidth":null,"MaxWidth":null,"Height":"auto","MinHeight":null,"MaxHeight":null,"BorderSize":null,"BorderColor":"transperent","BorderType":null,"BorderRadius":null,"Float":"none","MarginTop":"auto","MarginRight":"auto","MarginBottom":"auto","MarginLeft":"auto","Position":"inherit","Left":"auto","Top":"auto","Right":"auto","Bottom":"auto","Src":"../../../scripts/skineditor/css/no.gif"}, {"id":"objectid-e3f1ed24-0ed1-47d1-6729-ed59a1452a2f","parentId":"objectid-b95ea42e-7597-4750-d664-ed7600210aa4","type":"counterscontainer","BorderSize":null,"BorderColor":null,"BorderType":null,"BorderRadius":null,"Float":null,"Clear":null,"FontFamily":"Arial","FontSize":12,"FontWeight":"normal","FontColor":"#000","MarginTop":"auto","MarginRight":"auto","MarginBottom":"auto","MarginLeft":"auto","Align":"justify","Position":"inherit","Left":"auto","Top":"auto","Right":"auto","Bottom":"auto"}, {"id":"objectid-6eda7e63-ba82-4f16-1776-641dc087c014","parentId":"objectid-a2b26a51-fa9d-44f5-3c57-ccbbb505a750","type":"links","BorderSize":null,"BorderColor":null,"BorderType":null,"BorderRadius":null,"Float":"none","Clear":null,"FontFamily":"Arial","FontSize":12,"FontWeight":"normal","FontColor":"#000","BackgroundColor":"transparent","BackgroundURL":null,"BackgroundRepeat":"repeat","BackgroundPosition":"0% 0%","MarginTop":"auto","MarginRight":"auto","MarginBottom":"auto","MarginLeft":"auto","PaddingTop":"auto","PaddingRight":"auto","PaddingBottom":"auto","PaddingLeft":"auto","Align":"left","Position":"inherit","Left":"auto","Top":"auto","Right":"auto","Bottom":"auto"}, {"id":"objectid-b95ea42e-7597-4750-d664-ed7600210aa4","parentId":"objectid-23db1186-d7c6-4a98-6951-f4bf4e71f05e","type":"block","Width":"49%","MinWidth":null,"MaxWidth":null,"Height":"100%","MinHeight":null,"MaxHeight":null,"BorderSize":"0px","BorderColor":"#504f4f","BorderType":"dashed","BorderRadius":null,"Float":"left","Clear":null,"BackgroundColor":"transparent","BackgroundURL":null,"BackgroundRepeat":"repeat","BackgroundPosition":"0% 0%","MarginTop":"auto","MarginRight":"auto","MarginBottom":"auto","MarginLeft":"auto","PaddingTop":"auto","PaddingRight":"auto","PaddingBottom":"auto","PaddingLeft":"auto","Align":"right","Position":"inherit","Left":"auto","Top":"auto","Right":"auto","Bottom":"auto"}, {"id":"objectid-a2b26a51-fa9d-44f5-3c57-ccbbb505a750","parentId":"objectid-23db1186-d7c6-4a98-6951-f4bf4e71f05e","type":"block","Width":"50%","MinWidth":null,"MaxWidth":null,"Height":"100%","MinHeight":null,"MaxHeight":null,"BorderSize":"0px","BorderColor":"#504f4f","BorderType":"dashed","BorderRadius":null,"Float":"left","Clear":null,"BackgroundColor":"transparent","BackgroundURL":null,"BackgroundRepeat":"repeat","BackgroundPosition":"0% 0%","MarginTop":"auto","MarginRight":"auto","MarginBottom":"auto","MarginLeft":"auto","PaddingTop":"auto","PaddingRight":"auto","PaddingBottom":"auto","PaddingLeft":"auto","Align":"justify","Position":"inherit","Left":"auto","Top":"auto","Right":"auto","Bottom":"auto"}, {"id":"objectid-ec8279db-df41-4b93-8652-809545d9b3f1","parentId":"objectid-23db1186-d7c6-4a98-6951-f4bf4e71f05e","type":"nodecontent","BorderSize":null,"BorderColor":null,"BorderType":null,"BorderRadius":null,"Float":null,"Clear":null,"Align":"justify","FontFamily":"Arial","FontSize":12,"FontWeight":"normal","FontColor":"#000","MarginTop":"16px","MarginRight":"auto","MarginBottom":"auto","MarginLeft":"auto","Position":"inherit","Left":"auto","Top":"auto","Right":"auto","Bottom":"auto"}, {"id":"objectid-2a5d0909-f739-476b-262c-2b2de1b1e71b","parentId":"objectid-23db1186-d7c6-4a98-6951-f4bf4e71f05e","type":"nodetitle","BorderSize":null,"BorderColor":null,"BorderType":null,"BorderRadius":null,"Float":null,"Clear":null,"Align":"left","FontFamily":"Arial","FontSize":"26px","FontWeight":"bold","FontColor":"#000","MarginTop":"auto","MarginRight":"auto","MarginBottom":"auto","MarginLeft":"auto","Position":"inherit","Left":"auto","Top":"auto","Right":"auto","Bottom":"auto"}, {"id":"objectid-f3d9a18b-5122-442c-0778-1d428ff6bf9e","parentId":"objectid-60e108f9-254d-4df1-03c3-d81c7c98a767","type":"links","BorderSize":null,"BorderColor":null,"BorderType":null,"BorderRadius":null,"Float":"none","Clear":null,"FontFamily":"Arial","FontSize":12,"FontWeight":"normal","FontColor":"#000","BackgroundColor":"transparent","BackgroundURL":null,"BackgroundRepeat":"repeat","BackgroundPosition":"0% 0%","MarginTop":"auto","MarginRight":"auto","MarginBottom":"auto","MarginLeft":"auto","PaddingTop":"auto","PaddingRight":"auto","PaddingBottom":"auto","PaddingLeft":"auto","Align":"left","Position":"inherit","Left":"auto","Top":"auto","Right":"auto","Bottom":"auto"}, {"id":"objectid-60e108f9-254d-4df1-03c3-d81c7c98a767","parentId":"objectid-ff19d0fc-6b42-439d-9db4-d245414df64c","type":"block","Width":"79%","MinWidth":null,"MaxWidth":null,"Height":"100%","MinHeight":null,"MaxHeight":null,"BorderSize":"0px","BorderColor":"#504f4f","BorderType":"dashed","BorderRadius":null,"Float":"none","Clear":"both","BackgroundColor":"transparent","BackgroundURL":null,"BackgroundRepeat":"repeat","BackgroundPosition":"0% 0%","MarginTop":"auto","MarginRight":"auto","MarginBottom":"auto","MarginLeft":"auto","PaddingTop":"auto","PaddingRight":"auto","PaddingBottom":"auto","PaddingLeft":"auto","Align":"justify","Position":"inherit","Left":"auto","Top":"auto","Right":"auto","Bottom":"auto"}, {"id":"objectid-0ccb7522-6fc3-4e37-548b-4db9f9136c1d","parentId":"objectid-ff19d0fc-6b42-439d-9db4-d245414df64c","type":"block","Width":"19%","MinWidth":null,"MaxWidth":null,"Height":280,"MinHeight":null,"MaxHeight":null,"BorderSize":"0px","BorderColor":"#504f4f","BorderType":"dashed","BorderRadius":null,"Float":"left","Clear":null,"BackgroundColor":"#ffffff","BackgroundURL":null,"BackgroundRepeat":"repeat","BackgroundPosition":"0% 0%","MarginTop":"auto","MarginRight":"auto","MarginBottom":"auto","MarginLeft":"5px","PaddingTop":"auto","PaddingRight":"auto","PaddingBottom":"auto","PaddingLeft":"auto","Align":"justify","Position":"relative","Left":"auto","Top":"auto","Right":"auto","Bottom":"auto"}, {"id":"objectid-23db1186-d7c6-4a98-6951-f4bf4e71f05e","parentId":"objectid-ff19d0fc-6b42-439d-9db4-d245414df64c","type":"block","Width":"78%","MinWidth":null,"MaxWidth":null,"Height":"100%","MinHeight":null,"MaxHeight":null,"BorderSize":"0px","BorderColor":"#504f4f","BorderType":"dashed","BorderRadius":null,"Float":"left","Clear":null,"BackgroundColor":"#ffffff","BackgroundURL":null,"BackgroundRepeat":"repeat","BackgroundPosition":"0% 0%","MarginTop":"auto","MarginRight":"auto","MarginBottom":"auto","MarginLeft":"auto","PaddingTop":"10px","PaddingRight":"10px","PaddingBottom":"10px","PaddingLeft":"10px","Align":"justify","Position":"inherit","Left":"auto","Top":"auto","Right":"auto","Bottom":"auto"}, {"id":"objectid-ff19d0fc-6b42-439d-9db4-d245414df64c","parentId":"objectid-6985d501-126d-4ad6-82cb-b621aa47e8b0","type":"block","Width":"90%","MinWidth":null,"MaxWidth":null,"Height":"100%","MinHeight":null,"MaxHeight":null,"BorderSize":"0px","BorderColor":"#504f4f","BorderType":"dashed","BorderRadius":null,"Float":"none","Clear":null,"BackgroundColor":"transparent","BackgroundURL":null,"BackgroundRepeat":"repeat","BackgroundPosition":"0% 0%","MarginTop":"20px","MarginRight":"auto","MarginBottom":"0px","MarginLeft":"auto","PaddingTop":"0px","PaddingRight":"auto","PaddingBottom":"auto","PaddingLeft":"auto","Align":"justify","Position":"inherit","Left":"auto","Top":"auto","Right":"auto","Bottom":"auto"}, {"id":"objectid-6985d501-126d-4ad6-82cb-b621aa47e8b0","parentId":null,"type":"root","BorderSize":null,"BorderColor":null,"BorderType":null,"BorderRadius":null,"Float":null,"Clear":null,"FontFamily":"Arial","FontSize":"12px","FontWeight":"bolder","FontColor":"#000","BackgroundColor":"#eeeeee","BackgroundURL":null,"BackgroundRepeat":null,"BackgroundPosition":"0 0px","MarginTop":"auto","MarginRight":"auto","MarginBottom":"auto","MarginLeft":"auto","PaddingTop":"auto","PaddingRight":"auto","PaddingBottom":"auto","PaddingLeft":"auto","Align":null,"Position":null,"Left":null,"Top":null,"Right":null,"Bottom":null}], "html": "PGRpdiBpZD0ib2JqZWN0aWQtZmYxOWQwZmMtNmI0Mi00MzlkLTlkYjQtZDI0NTQxNGRmNjRjIiBjbGFzcz0idWktcmVzaXphYmxlIiBzdHlsZT0id2lkdGg6IDkwJTsgaGVpZ2h0OiAxMDAlOyBib3JkZXI6IDBweCBkYXNoZWQgcmdiKDgwLCA3OSwgNzkpOyBmbG9hdDogbm9uZTsgdG9wOiAwcHg7IGxlZnQ6IDBweDsgcGFkZGluZy10b3A6IDBweDsgbWFyZ2luOiAyMHB4IGF1dG8gMHB4OyI+PGRpdiBpZD0ib2JqZWN0aWQtMjNkYjExODYtZDdjNi00YTk4LTY5NTEtZjRiZjRlNzFmMDVlIiBjbGFzcz0idWktcmVzaXphYmxlIiBzdHlsZT0id2lkdGg6IDc4JTsgaGVpZ2h0OiAxMDAlOyBib3JkZXI6IDBweCBkYXNoZWQgcmdiKDgwLCA3OSwgNzkpOyBmbG9hdDogbGVmdDsgYmFja2dyb3VuZC1jb2xvcjogcmdiKDI1NSwgMjU1LCAyNTUpOyBwYWRkaW5nOiAxMHB4OyI+PGRpdiBpZD0ib2JqZWN0aWQtMmE1ZDA5MDktZjczOS00NzZiLTI2MmMtMmIyZGUxYjFlNzFiIiBzdHlsZT0id2lkdGg6IGF1dG87IGhlaWdodDogYXV0bzsgY29sb3I6IHJnYigwLCAwLCAwKTsgZm9udC1zaXplOiAyNnB4OyBmb250LXdlaWdodDogYm9sZDsgdGV4dC1hbGlnbjogbGVmdDsiIGNsYXNzPSIiPntOT0RFX1RJVExFfTwvZGl2PjxkaXYgaWQ9Im9iamVjdGlkLWVjODI3OWRiLWRmNDEtNGI5My04NjUyLTgwOTU0NWQ5YjNmMSIgc3R5bGU9IndpZHRoOiBhdXRvOyBoZWlnaHQ6IGF1dG87IGNvbG9yOiByZ2IoMCwgMCwgMCk7IHRleHQtYWxpZ246IGp1c3RpZnk7IG1hcmdpbi10b3A6IDE2cHg7IiBjbGFzcz0iIj57Tk9ERV9DT05URU5UfTwvZGl2PjxkaXYgaWQ9Im9iamVjdGlkLWEyYjI2YTUxLWZhOWQtNDRmNS0zYzU3LWNjYmJiNTA1YTc1MCIgY2xhc3M9InVpLXJlc2l6YWJsZSIgc3R5bGU9IndpZHRoOiA1MCU7IGhlaWdodDogMTAwJTsgYm9yZGVyOiAwcHggZGFzaGVkIHJnYig4MCwgNzksIDc5KTsgZmxvYXQ6IGxlZnQ7Ij48ZGl2IGlkPSJvYmplY3RpZC02ZWRhN2U2My1iYTgyLTRmMTYtMTc3Ni02NDFkYzA4N2MwMTQiIHN0eWxlPSJjb2xvcjogcmdiKDAsIDAsIDApOyB0ZXh0LWFsaWduOiBsZWZ0OyIgY2xhc3M9IiI+e0xJTktTfTwvZGl2PjxkaXYgY2xhc3M9InVpLXJlc2l6YWJsZS1oYW5kbGUgdWktcmVzaXphYmxlLWUiIHN0eWxlPSJ6LWluZGV4OiAxMDAwOyI+PC9kaXY+PGRpdiBjbGFzcz0idWktcmVzaXphYmxlLWhhbmRsZSB1aS1yZXNpemFibGUtcyIgc3R5bGU9InotaW5kZXg6IDEwMDA7Ij48L2Rpdj48ZGl2IGNsYXNzPSJ1aS1yZXNpemFibGUtaGFuZGxlIHVpLXJlc2l6YWJsZS1zZSB1aS1pY29uIHVpLWljb24tZ3JpcHNtYWxsLWRpYWdvbmFsLXNlIiBzdHlsZT0iei1pbmRleDogMTAwMDsiPjwvZGl2PjwvZGl2PjxkaXYgaWQ9Im9iamVjdGlkLWI5NWVhNDJlLTc1OTctNDc1MC1kNjY0LWVkNzYwMDIxMGFhNCIgY2xhc3M9InVpLXJlc2l6YWJsZSIgc3R5bGU9IndpZHRoOiA0OSU7IGhlaWdodDogMTAwJTsgYm9yZGVyOiAwcHggZGFzaGVkIHJnYig4MCwgNzksIDc5KTsgZmxvYXQ6IGxlZnQ7IHRleHQtYWxpZ246IHJpZ2h0OyI+PGRpdiBpZD0ib2JqZWN0aWQtZTNmMWVkMjQtMGVkMS00N2QxLTY3MjktZWQ1OWExNDUyYTJmIiBzdHlsZT0id2lkdGg6IGF1dG87IGhlaWdodDogYXV0bzsgY29sb3I6IHJnYigwLCAwLCAwKTsiIGNsYXNzPSIiPntDT1VOVEVSU308L2Rpdj48ZGl2IGNsYXNzPSJ1aS1yZXNpemFibGUtaGFuZGxlIHVpLXJlc2l6YWJsZS1lIiBzdHlsZT0iei1pbmRleDogMTAwMDsiPjwvZGl2PjxkaXYgY2xhc3M9InVpLXJlc2l6YWJsZS1oYW5kbGUgdWktcmVzaXphYmxlLXMiIHN0eWxlPSJ6LWluZGV4OiAxMDAwOyI+PC9kaXY+PGRpdiBjbGFzcz0idWktcmVzaXphYmxlLWhhbmRsZSB1aS1yZXNpemFibGUtc2UgdWktaWNvbiB1aS1pY29uLWdyaXBzbWFsbC1kaWFnb25hbC1zZSIgc3R5bGU9InotaW5kZXg6IDEwMDA7Ij48L2Rpdj48L2Rpdj48ZGl2IGNsYXNzPSJ1aS1yZXNpemFibGUtaGFuZGxlIHVpLXJlc2l6YWJsZS1lIiBzdHlsZT0iei1pbmRleDogMTAwMDsiPjwvZGl2PjxkaXYgY2xhc3M9InVpLXJlc2l6YWJsZS1oYW5kbGUgdWktcmVzaXphYmxlLXMiIHN0eWxlPSJ6LWluZGV4OiAxMDAwOyI+PC9kaXY+PGRpdiBjbGFzcz0idWktcmVzaXphYmxlLWhhbmRsZSB1aS1yZXNpemFibGUtc2UgdWktaWNvbiB1aS1pY29uLWdyaXBzbWFsbC1kaWFnb25hbC1zZSIgc3R5bGU9InotaW5kZXg6IDEwMDA7Ij48L2Rpdj48L2Rpdj48ZGl2IGlkPSJvYmplY3RpZC0wY2NiNzUyMi02ZmMzLTRlMzctNTQ4Yi00ZGI5ZjkxMzZjMWQiIGNsYXNzPSJ1aS1yZXNpemFibGUiIHN0eWxlPSJ3aWR0aDogMTklOyBoZWlnaHQ6IDI4MHB4OyBib3JkZXI6IDBweCBkYXNoZWQgcmdiKDgwLCA3OSwgNzkpOyBmbG9hdDogbGVmdDsgbWFyZ2luLWxlZnQ6IDVweDsgYmFja2dyb3VuZC1jb2xvcjogcmdiKDI1NSwgMjU1LCAyNTUpOyB0b3A6IDBweDsgbGVmdDogMHB4OyBwb3NpdGlvbjogcmVsYXRpdmU7Ij48ZGl2IGlkPSJvYmplY3RpZC1kMTc2NWRiNC05MTc4LTRlZWMtZDdlZi0zMjEyMjc1YzM0NzIiIGNsYXNzPSJjb21wb25lbnQtc2VsZWN0ZWQiIHN0eWxlPSJjb2xvcjogcmdiKDIyOSwgMTMzLCAxMzMpOyBmb250LXNpemU6IDExcHg7IG1hcmdpbjogMTBweCAxMHB4IDIwcHg7IGJhY2tncm91bmQtY29sb3I6IHJnYigyMjMsIDYwLCAzMik7IHBvc2l0aW9uOiBhYnNvbHV0ZTsgbGVmdDogYXV0bzsgdG9wOiBhdXRvOyBib3R0b206IDBweDsiPntNQVBfSU5GT308L2Rpdj48ZGl2IGlkPSJvYmplY3RpZC00ZjRiYzYwMS0xNGJiLTQ3MjItOTAyZC1iYmUxMDllZTExOTciIGNsYXNzPSJ1aS1yZXNpemFibGUiIHN0eWxlPSJ3aWR0aDogMTc0cHg7IGhlaWdodDogNjBweDsgYm9yZGVyOiAwcHggZGFzaGVkIHJnYig4MCwgNzksIDc5KTsgZmxvYXQ6IG5vbmU7IHRleHQtYWxpZ246IGxlZnQ7IHRvcDogMHB4OyBsZWZ0OiAwcHg7IG1hcmdpbi1sZWZ0OiAxMHB4OyI+PGRpdiBpZD0ib2JqZWN0aWQtMjk4YTgxOWItYTQ1Mi00Y2YxLWJkZjMtODkwZTJjMGVhZGQ5IiBjbGFzcz0iIj57Qk9PS01BUkt9PC9kaXY+PGRpdiBjbGFzcz0idWktcmVzaXphYmxlLWhhbmRsZSB1aS1yZXNpemFibGUtZSIgc3R5bGU9InotaW5kZXg6IDEwMDA7Ij48L2Rpdj48ZGl2IGNsYXNzPSJ1aS1yZXNpemFibGUtaGFuZGxlIHVpLXJlc2l6YWJsZS1zIiBzdHlsZT0iei1pbmRleDogMTAwMDsiPjwvZGl2PjxkaXYgY2xhc3M9InVpLXJlc2l6YWJsZS1oYW5kbGUgdWktcmVzaXphYmxlLXNlIHVpLWljb24gdWktaWNvbi1ncmlwc21hbGwtZGlhZ29uYWwtc2UiIHN0eWxlPSJ6LWluZGV4OiAxMDAwOyI+PC9kaXY+PC9kaXY+PGRpdiBpZD0ib2JqZWN0aWQtMmNlZmY3NzctZmZjNy00ZmZjLWUzZWQtMjIzOGU4YTI0YjE1IiBjbGFzcz0idWktcmVzaXphYmxlIiBzdHlsZT0id2lkdGg6IDcwJTsgaGVpZ2h0OiA3M3B4OyBib3JkZXI6IDBweCBkYXNoZWQgcmdiKDgwLCA3OSwgNzkpOyBmbG9hdDogbm9uZTsgdGV4dC1hbGlnbjogbGVmdDsgcGFkZGluZzogMTBweDsgdG9wOiAwcHg7IGxlZnQ6IDBweDsgY2xlYXI6IGJvdGg7Ij48aW1nIGlkPSJvYmplY3RpZC04MmU0NzZmNi00OWI5LTRlOWItOWRiYS03YTBkMTM3N2FjZmIiIHNyYz0iZGF0YTppbWFnZS9qcGVnO2Jhc2U2NCwvOWovNEFBUVNrWkpSZ0FCQWdBQVpBQmtBQUQvN0FBUlJIVmphM2tBQVFBRUFBQUFQQUFBLys0QURrRmtiMkpsQUdUQUFBQUFBZi9iQUlRQUJnUUVCQVVFQmdVRkJna0dCUVlKQ3dnR0JnZ0xEQW9LQ3dvS0RCQU1EQXdNREF3UURBNFBFQThPREJNVEZCUVRFeHdiR3hzY0h4OGZIeDhmSHg4Zkh3RUhCd2NOREEwWUVCQVlHaFVSRlJvZkh4OGZIeDhmSHg4Zkh4OGZIeDhmSHg4Zkh4OGZIeDhmSHg4Zkh4OGZIeDhmSHg4Zkh4OGZIeDhmSHg4Zkh4OGYvOEFBRVFnQUZBQjJBd0VSQUFJUkFRTVJBZi9FQUpRQUFBSURBUUVCQUFBQUFBQUFBQUFBQUFRR0F3VUhBQWdDQVFFQkFRRUJBQUFBQUFBQUFBQUFBQUFBQWdVQkJCQUFBQVFGQXdJRkFRVUdCd0FBQUFBQUFRSURCQkVTRXdVR0FCUVZJUWN4UVNJV0YwSlJZWUV5R0pHQ0l6TkRSSEZTWXlRbE5RZ1JBQUVEQVFZRUF3a0FBQUFBQUFBQUFBQUJFUUloTVdFU0F4TUVRVkdCTW5HaEl2Q1IwZUZDVWlNVUJmL2FBQXdEQVFBQ0VRTVJBRDhBOUx2OGp4KzNPa21sd3VqUm03WGhRYnVGMGtsRHhHQVNFT1lERy9EUUZsb0R0QWRvQ0RlTlFkQTBGZFBkaVNxRGVZdFFVNHdua2pOTEhwSFFFK2dNdDdnWFBNbE0vdGxveGR5NEZRalFqbHkyUU9pQ0paWDdjcHp1Z1Y2MDl1YzRRSjZvaUhsSFdwdEk1V2xLVTJ0YS90V3pxU292SVc3djhkb0psVjNhYmhWMmsyV0taVnA2Q3JwTGtkT1VxY1FvSXFVVHBsSDFlT3ZTczltOWlNMS9CbVR4V3JpcEVSSHY0VGpucmxkZHUzT0NxejFBVE5oS2dBTEhLb1ZZeGxDd0x0aWtNbkxONmhId0hYVmxzNnBUejVmRzBWQ1UybmU4d1d0YTJYQmQyaXRaMDFSV09kb29pTHhWb3NaWXFwd01UcVZ3S1FKaVVwZ2g1L21qQ1QyaXU2SjNYMk9qTjBkeFVsYVlwbUk5cWN0WlBXanJtYm5jU3ZFRTNLallYQ2hBSXptVU1ZaHFNWW9uOGZzNjZTM0dVbTRncUttR01XNHQ5WFhpR29kZzc3dUU0emEwc0g5emNyVzVWdXJjM2FTaDBsRGtSU1dkSXRrMVZVSW94VktxZ1kwb2pHVFhOd21Sb3JLS0k3c25rcXM5YVY5NFJ6V2I1ZnJQWTdZcmNydzdUWk1VSVZGMVJnVUJFWUFBZVlpSStBQjExa1pXVktjc01VZFNqT2UxV2RFeU4va09RdTdncnMzRHNHMXRZVENLTFJvajZVMUZDQUkwenJtRVRHTWNBL3gxbzcvYTZhUmdpVmFxODErUktLYXZyTEtQT1dNV3J0eGQwZTVUdnVHUmt0a0tGM3VDYnhXNGlUY04yS1pRMm0yRS9xSVNVUDRZcCtQN05BVjJEWk4zRGVzc0x4cFhMUnhoSjFpeTF3VWR1RVc2eWh4UWVuVGJDVXprUEVXd0VFZXY1QUh6NmdCRXg3eWQwY29iMkJnM1hYWU9GYks0dVRwN2JrN2VWUjBzaThWYWxVLzVJNktKVVFLaUJ6Z242b2o1RjhBTFJETys2K1FLTEloa0NkaVZaWWh6amtHU1RONG1xOFFjTEpUSnF3VklDYXhVeWlhVXd3OEFob0F6RU11SSs3dFloa21RTzI3Unhjc0NUV2NycW5JZ21aWlI0QmhsbUVBQ0l4R1VOQUY5NE81T1JJWFY0bmg5L1dCSzAyVTkxV1R0NkxBNkFIQTVnSXE1ZHZEaUIweGtscElFRTRqK3pRQ3FOOXlSN2xtUlpHenloREdibWJFYlJlWEg4RkJUY3FsYUNzS1lBdk5LbE9hQnBRbTZsL0VDenUvZVh1RGIyVFZaVldWNW1tT3NIR0t0Z1JUQXFONFZWVGJMcHB4S0ltQVFYQmNBVW1BQTZhQXVMaGR1NER2SSs0TnJjWkFWU3g0aGFVRExNRkdiYzR2Vm5Ob1VNYWM0bENWTVZTaW9Zc0JqNGRDOU5BVTFveVBQcmcydEZuczEvd0NCWk5zQWEzOHhHck5xWUJkRU9Za0NGTVNWTWhnQUFFQ2hDQVFBQTBBTmJydmZzaHpQRzc5Y2JxWUZyamdqcDI1YUVUUktrY1E5Q2lRQkxFQVVQL0VHSFVCQ0FlbnBvQTNzL2Q4d3NhM2F5M0tYc1gxaXlxMnZnTmFUTjBVeU5RWklBcWtLU2hRcW1NTWZXSmpESHI5MEFIM3Y4TE11Q3ZsWGFTYTlCc3VveVRWNmxCMmFSSk5RQ2owRXhFMVZERit6eDh0YVg4cDlWRzVvL2g3TVRLd3lhOUd3Q3pXZkhYT0JQVlBrSXpOc2tzeHRoUmRKdXFxUlFYSTlUNmtpTVJqNXg4UTh3MXN2V25LU1pxZmhkYXJSdVRFMDREaTF5NjVPdi9Oend5RDVZK1FNRXkycDBJQWJja1dPNklnVklmcW1GSlVwWi94MTQxMjZKdlVwNlY5VjNhNTE2RGhuSHdMN2xSOTU4RjdobEpKeUZDdkwvVHFUL1QvbG42YXd5eFM3aGZISHpaYnZlV3c0RDJzZW52NmUxbTM1YVVrM1NhRVpaZktPZ0hMTC9nM2hMUDdvNExoNUM4RnVhRktsQUliYi9UaENNdnA4STZBdDBmalRjSzdmaXR4dzVhMU9qSGhZakxHWCsxOFlmVG9CZHlEOVBOQzFjLzdjbzdKUGh0M3RaZGpFMU9oUC9Tak5MTDAwQkE0L1RiTGFkeDdhcDdkWGlKOXJUMjA2bFNuSDB5VktuNzAwT3NkQWZkOC9UbEJ0elh0dU5CcHROenRwdHZJTzBrbTYwcWY1UHBsKzdRRlRudFA1czdlY3ZSOXFTcSszNlZLUEswelN6L1hTa3BVNU9rOE5BYUlQc1hlWkhIWWIyaW43cC9sMUtGRTFQZVE2eTBKcFovcDBBTGJ2akdxVGorTHE4S1duUm96Y0hNTWtJZjJzMFlmVG9BTTN3M1V4NlBDMU5zcDdaaFFtMnNnMU5yRHJUa2pHWHBvQTVoOGExTVo0L2pLbEpmMmxScFJvMHdyN0tIMDA0VHllV2dNeXlYOU0zTFhQbUp1VHFyN24vc2Y1MFJucGYwb3pSbGw5T3R6Si9ld0poN2FmYVFyQkhaWDJOd3JuaDk5eGxaU3B1K1ByMG85YSsxLzNkTDdaL1QrN3FmNldyaVRFMks3RTNSNk9JanhaZlpQeURrbkhUY3B0MlBOd2hzcXN4NkVmbzNFc3NmdWw4NDY4T1pxNk1YN1hWdWQvUXJpZi85az0iIHN0eWxlPSJ3aWR0aDogMTMwcHg7IGhlaWdodDogYXV0bzsgZmxvYXQ6IG5vbmU7IiBjbGFzcz0iIj48ZGl2IGNsYXNzPSJ1aS1yZXNpemFibGUtaGFuZGxlIHVpLXJlc2l6YWJsZS1lIiBzdHlsZT0iei1pbmRleDogMTAwMDsiPjwvZGl2PjxkaXYgY2xhc3M9InVpLXJlc2l6YWJsZS1oYW5kbGUgdWktcmVzaXphYmxlLXMiIHN0eWxlPSJ6LWluZGV4OiAxMDAwOyI+PC9kaXY+PGRpdiBjbGFzcz0idWktcmVzaXphYmxlLWhhbmRsZSB1aS1yZXNpemFibGUtc2UgdWktaWNvbiB1aS1pY29uLWdyaXBzbWFsbC1kaWFnb25hbC1zZSIgc3R5bGU9InotaW5kZXg6IDEwMDA7Ij48L2Rpdj48L2Rpdj48ZGl2IGNsYXNzPSJ1aS1yZXNpemFibGUtaGFuZGxlIHVpLXJlc2l6YWJsZS1lIiBzdHlsZT0iei1pbmRleDogMTAwMDsiPjwvZGl2PjxkaXYgY2xhc3M9InVpLXJlc2l6YWJsZS1oYW5kbGUgdWktcmVzaXphYmxlLXMiIHN0eWxlPSJ6LWluZGV4OiAxMDAwOyI+PC9kaXY+PGRpdiBjbGFzcz0idWktcmVzaXphYmxlLWhhbmRsZSB1aS1yZXNpemFibGUtc2UgdWktaWNvbiB1aS1pY29uLWdyaXBzbWFsbC1kaWFnb25hbC1zZSIgc3R5bGU9InotaW5kZXg6IDEwMDA7Ij48L2Rpdj48L2Rpdj48ZGl2IGlkPSJvYmplY3RpZC02MGUxMDhmOS0yNTRkLTRkZjEtMDNjMy1kODFjN2M5OGE3NjciIGNsYXNzPSJ1aS1yZXNpemFibGUiIHN0eWxlPSJ3aWR0aDogNzklOyBoZWlnaHQ6IDEwMCU7IGJvcmRlcjogMHB4IGRhc2hlZCByZ2IoODAsIDc5LCA3OSk7IGZsb2F0OiBub25lOyBjbGVhcjogYm90aDsiPjxkaXYgaWQ9Im9iamVjdGlkLWYzZDlhMThiLTUxMjItNDQyYy0wNzc4LTFkNDI4ZmY2YmY5ZSIgc3R5bGU9ImNvbG9yOiByZ2IoMCwgMCwgMCk7IHRleHQtYWxpZ246IGxlZnQ7IiBjbGFzcz0iIj57UkVWSUVXfTwvZGl2PjxkaXYgY2xhc3M9InVpLXJlc2l6YWJsZS1oYW5kbGUgdWktcmVzaXphYmxlLWUiIHN0eWxlPSJ6LWluZGV4OiAxMDAwOyI+PC9kaXY+PGRpdiBjbGFzcz0idWktcmVzaXphYmxlLWhhbmRsZSB1aS1yZXNpemFibGUtcyIgc3R5bGU9InotaW5kZXg6IDEwMDA7Ij48L2Rpdj48ZGl2IGNsYXNzPSJ1aS1yZXNpemFibGUtaGFuZGxlIHVpLXJlc2l6YWJsZS1zZSB1aS1pY29uIHVpLWljb24tZ3JpcHNtYWxsLWRpYWdvbmFsLXNlIiBzdHlsZT0iei1pbmRleDogMTAwMDsiPjwvZGl2PjwvZGl2PjxkaXYgY2xhc3M9InVpLXJlc2l6YWJsZS1oYW5kbGUgdWktcmVzaXphYmxlLWUiIHN0eWxlPSJ6LWluZGV4OiAxMDAwOyI+PC9kaXY+PGRpdiBjbGFzcz0idWktcmVzaXphYmxlLWhhbmRsZSB1aS1yZXNpemFibGUtcyIgc3R5bGU9InotaW5kZXg6IDEwMDA7Ij48L2Rpdj48ZGl2IGNsYXNzPSJ1aS1yZXNpemFibGUtaGFuZGxlIHVpLXJlc2l6YWJsZS1zZSB1aS1pY29uIHVpLWljb24tZ3JpcHNtYWxsLWRpYWdvbmFsLXNlIiBzdHlsZT0iei1pbmRleDogMTAwMDsiPjwvZGl2PjwvZGl2Pg=="}',
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
        //$this->templateData['center'] = $previewList;
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