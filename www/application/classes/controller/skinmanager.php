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

    public function action_index()
    {
        $mapId = $this->request->param('id', NULL);
        if ( ! $mapId) Request::initial()->redirect(URL::base());

        DB_ORM::model('User')->can('edit', array('mapId' => $mapId));

        $map = DB_ORM::model('map', array((int) $mapId));

        $this->templateData['map']          = $map;
        $this->templateData['skin']         = DB_ORM::model('map_skin')->getSkinById($map->skin_id);
        $this->templateData['action']       = 'index';
        $this->templateData['navigation']   = View::factory('labyrinth/skin/navigation')->set('templateData', $this->templateData);
        $this->templateData['left']         = View::factory('labyrinth/labyrinthEditorMenu')->set('templateData', $this->templateData);
        $this->templateData['center']       = View::factory('labyrinth/skin/view')->set('templateData', $this->templateData);

        $this->template->set('templateData', $this->templateData);

        Breadcrumbs::add(Breadcrumb::factory()->set_title($this->templateData['map']->name)->set_url(URL::base().'labyrinthManager/global/'.$mapId));
        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Skin'))->set_url(URL::base().'skinManager/index/'.$mapId));
    }

    public function action_createSkin()
    {
        $mapId = $this->request->param('id', NULL);
        $skinId = $this->request->param('id2', NULL);

        DB_ORM::model('User')->can('edit', array('mapId' => $mapId));

        $this->templateData['map'] = DB_ORM::model('map', array((int)$mapId));
        $this->templateData['action_url'] = URL::base() . 'skinManager/skinEditorUpload/' . $mapId;

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

    private function saveSkin($mapId, $skin_name)
    {
        DB_ORM::model('User')->can('edit', array('mapId' => $mapId));

        if ($skin_name == '') $skin_name = rand(0, 100000);

        $checkName = DB_ORM::model('map_skin')->getMapByName($skin_name);
        if ($checkName != false) $skin_name .= rand(0, 100000);

        $skinPath = $mapId.'_'.$skin_name;

        $skin = DB_ORM::model('map_skin')->addSkin($skin_name, $skinPath);
        DB_ORM::model('map')->updateMapSkin($mapId, $skin->id);
        DB_ORM::model('map_skin')->updateSkinData(
            $skin->id,
            '{"tree": [{"data":"Root","attr":{"id":"objectid-6985d501-126d-4ad6-82cb-b621aa47e8b0","class":"","rel":"root"},"state":"open","metadata":{},"children":[{"data":"Block component","attr":{"id":"objectid-4fc732f1-cf7f-485b-5abb-b815561eb12b","class":""},"state":"open","metadata":{},"children":[{"data":"Block component","attr":{"id":"objectid-8519be58-7a02-4767-84ea-fbb6f7a76c43"},"state":"open","metadata":{},"children":[{"data":"Node title component","attr":{"id":"objectid-476cd9a9-2f0b-46a6-085e-25c4b3c86edd"},"metadata":{}},{"data":"Node content component","attr":{"id":"objectid-7ecfd3d2-c03c-4e3f-15fc-428507675410"},"metadata":{}},{"data":"Block component","attr":{"id":"objectid-8697c5bb-d528-41dc-c130-72457d090cd2"},"state":"open","metadata":{},"children":[{"data":"Links component","attr":{"id":"objectid-dd7b1ed9-e772-4516-3fb2-85506ef336ba","class":""},"metadata":{}}]},{"data":"Block component","attr":{"id":"objectid-1f96f6fc-f7d3-4979-f2a3-835cb44a0f9a","class":""},"state":"open","metadata":{},"children":[{"data":"Counters container component","attr":{"id":"objectid-b0ac1f6f-d17c-4487-ac36-3fe98964037e","class":""},"metadata":{}}]}]},{"data":"Block component","attr":{"id":"objectid-62d5be84-6793-47be-15f2-027f6cf5383b"},"state":"open","metadata":{},"children":['.
            '{"data":"Section","attr":{"id":"objectid-ec2c6ce6-803e-40ea-55c5-344974209bbb"},"metadata":{}},'.
            '{"data":"Map Info component","attr":{"id":"objectid-ec2c6ce6-803e-40ea-55c5-344974209cdc"},"metadata":{}},'.
            '{"data":"Block component","attr":{"id":"objectid-8fe33a23-d8b7-48df-49e7-46aa32447e8e"},"state":"open","metadata":{},"children":[{"data":"Bookmark component","attr":{"id":"objectid-cb11c45b-57d1-45dc-2c54-1478e04ad997","class":""},"metadata":{}}]},{"data":"Block component","attr":{"id":"objectid-fff3cede-a558-46ec-59b6-0f7c96b4ae37"},"state":"open","metadata":{},"children":[{"data":"Reset component","attr":{"id":"objectid-617b4f95-381f-4bd6-799c-e1db2423e0f3","class":""},"metadata":{}}]},{"data":"Image component","attr":{"id":"objectid-adb73f6d-2c5e-4f97-85e7-6f662f75eccb","class":""},"metadata":{}}]},{"data":"Block component","attr":{"id":"objectid-6b1221fb-1e0e-49c5-33e5-34ae7046ed14","class":""},"state":"open","metadata":{},"children":[{"data":"Review component","attr":{"id":"objectid-f588346d-3f3f-4254-b32f-d79838b5b62b","class":""},"metadata":{}}]}]}]}], "components": [{"id":"objectid-adb73f6d-2c5e-4f97-85e7-6f662f75eccb","parentId":"objectid-62d5be84-6793-47be-15f2-027f6cf5383b","type":"image","Width":"50%","MinWidth":null,"MaxWidth":null,"Height":"auto","MinHeight":null,"MaxHeight":null,"BorderSize":null,"BorderColor":"transperent","BorderType":null,"BorderRadius":null,"Float":"none","MarginTop":"auto","MarginRight":"auto","MarginBottom":"10px","MarginLeft":"10px","Position":"inherit","Left":"auto","Top":"auto","Right":"auto","Bottom":"auto","Src":"../../../scripts/skineditor/css/no.gif"}, {"id":"objectid-617b4f95-381f-4bd6-799c-e1db2423e0f3","parentId":"objectid-fff3cede-a558-46ec-59b6-0f7c96b4ae37","type":"reset"}, {"id":"objectid-fff3cede-a558-46ec-59b6-0f7c96b4ae37","parentId":"objectid-62d5be84-6793-47be-15f2-027f6cf5383b","type":"block","Width":"100%","MinWidth":null,"MaxWidth":null,"Height":"auto","MinHeight":null,"MaxHeight":null,"BorderSize":"0px","BorderColor":"#504f4f","BorderType":"dashed","BorderRadius":null,"Float":"none","Clear":null,"BackgroundColor":"transparent","BackgroundURL":null,"BackgroundRepeat":"repeat","BackgroundPosition":"0% 0%","MarginTop":"auto","MarginRight":"auto","MarginBottom":"20px","MarginLeft":"auto","PaddingTop":"auto","PaddingRight":"auto","PaddingBottom":"auto","PaddingLeft":"10px","Align":"justify","Position":"inherit","Left":"auto","Top":"auto","Right":"auto","Bottom":"auto","IsPopupInside":false}, {"id":"objectid-cb11c45b-57d1-45dc-2c54-1478e04ad997","parentId":"objectid-8fe33a23-d8b7-48df-49e7-46aa32447e8e","type":"bookmark"}, {"id":"objectid-8fe33a23-d8b7-48df-49e7-46aa32447e8e","parentId":"objectid-62d5be84-6793-47be-15f2-027f6cf5383b","type":"block","Width":"100%","MinWidth":null,"MaxWidth":null,"Height":"auto","MinHeight":null,"MaxHeight":null,"BorderSize":"0px","BorderColor":"#504f4f","BorderType":"dashed","BorderRadius":null,"Float":"none","Clear":null,"BackgroundColor":"transparent","BackgroundURL":null,"BackgroundRepeat":"repeat","BackgroundPosition":"0% 0%","MarginTop":"auto","MarginRight":"auto","MarginBottom":"auto","MarginLeft":"auto","PaddingTop":"auto","PaddingRight":"auto","PaddingBottom":"auto","PaddingLeft":"10px","Align":"justify","Position":"inherit","Left":"auto","Top":"auto","Right":"auto","Bottom":"auto","IsPopupInside":false}, {"id":"objectid-1f96f6fc-f7d3-4979-f2a3-835cb44a0f9a","parentId":"objectid-8519be58-7a02-4767-84ea-fbb6f7a76c43","type":"block","Width":"40%","MinWidth":null,"MaxWidth":null,"Height":"auto","MinHeight":null,"MaxHeight":null,"BorderSize":"0px","BorderColor":"#504f4f","BorderType":"dashed","BorderRadius":null,"Float":"right","Clear":null,"BackgroundColor":"transparent","BackgroundURL":null,"BackgroundRepeat":"repeat","BackgroundPosition":"0% 0%","MarginTop":"20px","MarginRight":"10px","MarginBottom":"10px","MarginLeft":"auto","PaddingTop":"auto","PaddingRight":"auto","PaddingBottom":"auto","PaddingLeft":"auto","Align":"right","Position":"inherit","Left":"auto","Top":"auto","Right":"auto","Bottom":"auto","IsPopupInside":false}, {"id":"objectid-b0ac1f6f-d17c-4487-ac36-3fe98964037e","parentId":"objectid-1f96f6fc-f7d3-4979-f2a3-835cb44a0f9a","type":"counterscontainer","BorderSize":null,"BorderColor":null,"BorderType":null,"BorderRadius":null,"Float":null,"Clear":null,"FontFamily":"Arial","FontSize":12,"FontWeight":"normal","FontColor":"#000","MarginTop":"auto","MarginRight":"auto","MarginBottom":"auto","MarginLeft":"auto","Align":"justify","Position":"inherit","Left":"auto","Top":"auto","Right":"auto","Bottom":"auto"}, {"id":"objectid-dd7b1ed9-e772-4516-3fb2-85506ef336ba","parentId":"objectid-8697c5bb-d528-41dc-c130-72457d090cd2","type":"links","BorderSize":null,"BorderColor":null,"BorderType":null,"BorderRadius":null,"Float":"none","Clear":null,"FontFamily":"Arial","FontSize":12,"FontWeight":"normal","FontColor":"#000","BackgroundColor":"transparent","BackgroundURL":null,"BackgroundRepeat":"repeat","BackgroundPosition":"0% 0%","MarginTop":"auto","MarginRight":"auto","MarginBottom":"auto","MarginLeft":"auto","PaddingTop":"auto","PaddingRight":"auto","PaddingBottom":"auto","PaddingLeft":"auto","Align":"left","Position":"inherit","Left":"auto","Top":"auto","Right":"auto","Bottom":"auto"}, {"id":"objectid-8697c5bb-d528-41dc-c130-72457d090cd2","parentId":"objectid-8519be58-7a02-4767-84ea-fbb6f7a76c43","type":"block","Width":"49%","MinWidth":null,"MaxWidth":null,"Height":"auto","MinHeight":null,"MaxHeight":null,"BorderSize":"0px","BorderColor":"#504f4f","BorderType":"dashed","BorderRadius":null,"Float":"left","Clear":null,"BackgroundColor":"transparent","BackgroundURL":null,"BackgroundRepeat":"repeat","BackgroundPosition":"0% 0%","MarginTop":"20px","MarginRight":"auto","MarginBottom":"auto","MarginLeft":"10px","PaddingTop":"auto","PaddingRight":"auto","PaddingBottom":"auto","PaddingLeft":"auto","Align":"justify","Position":"inherit","Left":"auto","Top":"auto","Right":"auto","Bottom":"auto","IsPopupInside":false}, {"id":"objectid-7ecfd3d2-c03c-4e3f-15fc-428507675410","parentId":"objectid-8519be58-7a02-4767-84ea-fbb6f7a76c43","type":"nodecontent","BorderSize":null,"BorderColor":null,"BorderType":null,"BorderRadius":null,"Float":null,"Clear":null,"Align":"justify","FontFamily":"Arial","FontSize":12,"FontWeight":"normal","FontColor":"#000","MarginTop":"auto","MarginRight":"10px","MarginBottom":"auto","MarginLeft":"10px","Position":"inherit","Left":"auto","Top":"auto","Right":"auto","Bottom":"auto"}, {"id":"objectid-f588346d-3f3f-4254-b32f-d79838b5b62b","parentId":"objectid-6b1221fb-1e0e-49c5-33e5-34ae7046ed14","type":"review","BorderSize":null,"BorderColor":null,"BorderType":null,"BorderRadius":null,"Float":"none","Clear":null,"FontFamily":"Arial","FontSize":"14px","FontWeight":"normal","FontColor":"#000","BackgroundColor":"transparent","BackgroundURL":null,"BackgroundRepeat":"repeat","BackgroundPosition":"0% 0%","MarginTop":"10px","MarginRight":"auto","MarginBottom":"auto","MarginLeft":"10px","PaddingTop":"auto","PaddingRight":"auto","PaddingBottom":"auto","PaddingLeft":"auto","Align":"left","Position":"inherit","Left":"auto","Top":"auto","Right":"auto","Bottom":"auto"}, {"id":"objectid-476cd9a9-2f0b-46a6-085e-25c4b3c86edd","parentId":"objectid-8519be58-7a02-4767-84ea-fbb6f7a76c43","type":"nodetitle","BorderSize":null,"BorderColor":null,"BorderType":null,"BorderRadius":null,"Float":null,"Clear":null,"Align":"left","FontFamily":"Arial","FontSize":"24px","FontWeight":"bold","FontColor":"#000","MarginTop":"10px","MarginRight":"10px","MarginBottom":"10px","MarginLeft":"10px","Position":"inherit","Left":"auto","Top":"auto","Right":"auto","Bottom":"auto"}, '.
            '{"id":"objectid-ec2c6ce6-803e-40ea-55c5-344974209bbb","parentId":"objectid-62d5be84-6793-47be-15f2-027f6cf5383b","type":"section","BorderSize":null,"BorderColor":null,"BorderType":null,"BorderRadius":null,"Float":"none","Clear":null,"FontFamily":"Arial","FontSize":12,"FontWeight":"normal","FontColor":"#000","BackgroundColor":"transparent","BackgroundURL":null,"BackgroundRepeat":"repeat","BackgroundPosition":"0% 0%","MarginTop":"10px","MarginRight":"10px","MarginBottom":"20px","MarginLeft":"10px","PaddingTop":"auto","PaddingRight":"auto","PaddingBottom":"auto","PaddingLeft":"auto","Align":"left","Position":"inherit","Left":"auto","Top":"auto","Right":"auto","Bottom":"auto"}, '.
            '{"id":"objectid-ec2c6ce6-803e-40ea-55c5-344974209cdc","parentId":"objectid-62d5be84-6793-47be-15f2-027f6cf5383b","type":"mapinfo","BorderSize":null,"BorderColor":null,"BorderType":null,"BorderRadius":null,"Float":"none","Clear":null,"FontFamily":"Arial","FontSize":12,"FontWeight":"normal","FontColor":"#000","BackgroundColor":"transparent","BackgroundURL":null,"BackgroundRepeat":"repeat","BackgroundPosition":"0% 0%","MarginTop":"10px","MarginRight":"10px","MarginBottom":"20px","MarginLeft":"10px","PaddingTop":"auto","PaddingRight":"auto","PaddingBottom":"auto","PaddingLeft":"auto","Align":"left","Position":"inherit","Left":"auto","Top":"auto","Right":"auto","Bottom":"auto"}, '.
            '{"id":"objectid-6b1221fb-1e0e-49c5-33e5-34ae7046ed14","parentId":"objectid-4fc732f1-cf7f-485b-5abb-b815561eb12b","type":"block","Width":"100%","MinWidth":null,"MaxWidth":null,"Height":"35px","MinHeight":null,"MaxHeight":null,"BorderSize":"0px","BorderColor":"#504f4f","BorderType":"dashed","BorderRadius":null,"Float":"left","Clear":null,"BackgroundColor":"transparent","BackgroundURL":null,"BackgroundRepeat":"repeat","BackgroundPosition":"0% 0%","MarginTop":"auto","MarginRight":"auto","MarginBottom":"auto","MarginLeft":"auto","PaddingTop":"auto","PaddingRight":"auto","PaddingBottom":"auto","PaddingLeft":"auto","Align":"justify","Position":"inherit","Left":"auto","Top":"auto","Right":"auto","Bottom":"auto","IsPopupInside":false}, {"id":"objectid-62d5be84-6793-47be-15f2-027f6cf5383b","parentId":"objectid-4fc732f1-cf7f-485b-5abb-b815561eb12b","type":"block","Width":"20%","MinWidth":null,"MaxWidth":null,"Height":"auto","MinHeight":null,"MaxHeight":null,"BorderSize":"0px","BorderColor":"#504f4f","BorderType":"dashed","BorderRadius":null,"Float":"left","Clear":null,"BackgroundColor":"#ffffff","BackgroundURL":null,"BackgroundRepeat":"repeat","BackgroundPosition":"0% 0%","MarginTop":"auto","MarginRight":"auto","MarginBottom":"auto","MarginLeft":"5px","PaddingTop":"auto","PaddingRight":"auto","PaddingBottom":"auto","PaddingLeft":"auto","Align":"justify","Position":"inherit","Left":"auto","Top":"auto","Right":"auto","Bottom":"auto","IsPopupInside":false}, {"id":"objectid-8519be58-7a02-4767-84ea-fbb6f7a76c43","parentId":"objectid-4fc732f1-cf7f-485b-5abb-b815561eb12b","type":"block","Width":"79%","MinWidth":null,"MaxWidth":null,"Height":"auto","MinHeight":null,"MaxHeight":null,"BorderSize":1,"BorderColor":"#ffffff","BorderType":"dashed","BorderRadius":null,"Float":"left","Clear":null,"BackgroundColor":"#ffffff","BackgroundURL":null,"BackgroundRepeat":"repeat","BackgroundPosition":"0% 0%","MarginTop":"auto","MarginRight":"auto","MarginBottom":"auto","MarginLeft":"auto","PaddingTop":"auto","PaddingRight":"auto","PaddingBottom":"auto","PaddingLeft":"auto","Align":"justify","Position":"inherit","Left":"auto","Top":"auto","Right":"auto","Bottom":"auto","IsPopupInside":true}, {"id":"objectid-4fc732f1-cf7f-485b-5abb-b815561eb12b","parentId":"objectid-6985d501-126d-4ad6-82cb-b621aa47e8b0","type":"block","Width":"90%","MinWidth":null,"MaxWidth":null,"Height":"auto","MinHeight":null,"MaxHeight":null,"BorderSize":"0px","BorderColor":"#504f4f","BorderType":"dashed","BorderRadius":null,"Float":"none","Clear":"both","BackgroundColor":"transparent","BackgroundURL":null,"BackgroundRepeat":"repeat","BackgroundPosition":"0% 0%","MarginTop":"0px","MarginRight":"auto","MarginBottom":"0px","MarginLeft":"auto","PaddingTop":"auto","PaddingRight":"auto","PaddingBottom":"auto","PaddingLeft":"auto","Align":"justify","Position":"inherit","Left":"auto","Top":"auto","Right":"auto","Bottom":"auto","IsPopupInside":false}, {"id":"objectid-444fc3a8-7ef3-4b15-679c-b3440c712249","parentId":"objectid-23db1186-d7c6-4a98-6951-f4bf4e71f05e","type":"image","Width":"100","MinWidth":null,"MaxWidth":null,"Height":"auto","MinHeight":null,"MaxHeight":null,"BorderSize":null,"BorderColor":"transperent","BorderType":null,"BorderRadius":null,"Float":"none","MarginTop":"auto","MarginRight":"auto","MarginBottom":"auto","MarginLeft":"auto","Position":"inherit","Left":"auto","Top":"auto","Right":"auto","Bottom":"auto","Src":"../../../scripts/skineditor/css/no.gif"}, {"id":"objectid-4f4bc601-14bb-4722-902d-bbe109ee1197","parentId":"objectid-0ccb7522-6fc3-4e37-548b-4db9f9136c1d","type":"block","Width":174,"MinWidth":null,"MaxWidth":null,"Height":60,"MinHeight":null,"MaxHeight":null,"BorderSize":"0px","BorderColor":"#504f4f","BorderType":"dashed","BorderRadius":null,"Float":"none","Clear":null,"BackgroundColor":"transparent","BackgroundURL":null,"BackgroundRepeat":"repeat","BackgroundPosition":"0% 0%","MarginTop":"auto","MarginRight":"auto","MarginBottom":"auto","MarginLeft":"10px","PaddingTop":"auto","PaddingRight":"auto","PaddingBottom":"auto","PaddingLeft":"auto","Align":"left","Position":"inherit","Left":"auto","Top":"auto","Right":"auto","Bottom":"auto","IsPopupInside":false}, {"id":"objectid-298a819b-a452-4cf1-bdf3-890e2c0eadd9","parentId":"objectid-4f4bc601-14bb-4722-902d-bbe109ee1197","type":"bookmark"}, {"id":"objectid-d1765db4-9178-4eec-d7ef-3212275c3472","parentId":"objectid-0ccb7522-6fc3-4e37-548b-4db9f9136c1d","type":"links","BorderSize":null,"BorderColor":null,"BorderType":null,"BorderRadius":null,"Float":"none","Clear":null,"FontFamily":"Arial","FontSize":"11px","FontWeight":"normal","FontColor":"#000000","BackgroundColor":"transparent","BackgroundURL":null,"BackgroundRepeat":"repeat","BackgroundPosition":"0% 0%","MarginTop":"10px","MarginRight":"10px","MarginBottom":"20px","MarginLeft":"10px","PaddingTop":"auto","PaddingRight":"auto","PaddingBottom":"auto","PaddingLeft":"auto","Align":null,"Position":"inherit","Left":"auto","Top":"auto","Right":"auto","Bottom":"0px"}, {"id":"objectid-2ceff777-ffc7-4ffc-e3ed-2238e8a24b15","parentId":"objectid-0ccb7522-6fc3-4e37-548b-4db9f9136c1d","type":"block","Width":"70%","MinWidth":null,"MaxWidth":null,"Height":73,"MinHeight":null,"MaxHeight":null,"BorderSize":"0px","BorderColor":"#504f4f","BorderType":"dashed","BorderRadius":null,"Float":"none","Clear":"both","BackgroundColor":"transparent","BackgroundURL":null,"BackgroundRepeat":"repeat","BackgroundPosition":"0% 0%","MarginTop":"auto","MarginRight":"auto","MarginBottom":"auto","MarginLeft":"auto","PaddingTop":"10px","PaddingRight":"10px","PaddingBottom":"10px","PaddingLeft":"10px","Align":"left","Position":"inherit","Left":"auto","Top":"auto","Right":"auto","Bottom":"auto","IsPopupInside":false}, {"id":"objectid-82e476f6-49b9-4e9b-9dba-7a0d1377acfb","parentId":"objectid-2ceff777-ffc7-4ffc-e3ed-2238e8a24b15","type":"image","Width":"130px","MinWidth":null,"MaxWidth":null,"Height":"auto","MinHeight":null,"MaxHeight":null,"BorderSize":null,"BorderColor":"transperent","BorderType":null,"BorderRadius":null,"Float":"none","MarginTop":"auto","MarginRight":"auto","MarginBottom":"auto","MarginLeft":"auto","Position":"inherit","Left":"auto","Top":"auto","Right":"auto","Bottom":"auto","Src":"../../../scripts/skineditor/css/no.gif"}, {"id":"objectid-e3f1ed24-0ed1-47d1-6729-ed59a1452a2f","parentId":"objectid-b95ea42e-7597-4750-d664-ed7600210aa4","type":"counterscontainer","BorderSize":null,"BorderColor":null,"BorderType":null,"BorderRadius":null,"Float":null,"Clear":null,"FontFamily":"Arial","FontSize":12,"FontWeight":"normal","FontColor":"#000","MarginTop":"auto","MarginRight":"auto","MarginBottom":"auto","MarginLeft":"auto","Align":"justify","Position":"inherit","Left":"auto","Top":"auto","Right":"auto","Bottom":"auto"}, {"id":"objectid-6eda7e63-ba82-4f16-1776-641dc087c014","parentId":"objectid-a2b26a51-fa9d-44f5-3c57-ccbbb505a750","type":"links","BorderSize":null,"BorderColor":null,"BorderType":null,"BorderRadius":null,"Float":"none","Clear":null,"FontFamily":"Arial","FontSize":12,"FontWeight":"normal","FontColor":"#000","BackgroundColor":"transparent","BackgroundURL":null,"BackgroundRepeat":"repeat","BackgroundPosition":"0% 0%","MarginTop":"auto","MarginRight":"auto","MarginBottom":"auto","MarginLeft":"auto","PaddingTop":"auto","PaddingRight":"auto","PaddingBottom":"auto","PaddingLeft":"auto","Align":"left","Position":"inherit","Left":"auto","Top":"auto","Right":"auto","Bottom":"auto"}, {"id":"objectid-b95ea42e-7597-4750-d664-ed7600210aa4","parentId":"objectid-23db1186-d7c6-4a98-6951-f4bf4e71f05e","type":"block","Width":"49%","MinWidth":null,"MaxWidth":null,"Height":"100%","MinHeight":null,"MaxHeight":null,"BorderSize":"0px","BorderColor":"#504f4f","BorderType":"dashed","BorderRadius":null,"Float":"left","Clear":null,"BackgroundColor":"transparent","BackgroundURL":null,"BackgroundRepeat":"repeat","BackgroundPosition":"0% 0%","MarginTop":"auto","MarginRight":"auto","MarginBottom":"auto","MarginLeft":"auto","PaddingTop":"auto","PaddingRight":"auto","PaddingBottom":"auto","PaddingLeft":"auto","Align":"right","Position":"inherit","Left":"auto","Top":"auto","Right":"auto","Bottom":"auto","IsPopupInside":false}, {"id":"objectid-a2b26a51-fa9d-44f5-3c57-ccbbb505a750","parentId":"objectid-23db1186-d7c6-4a98-6951-f4bf4e71f05e","type":"block","Width":"50%","MinWidth":null,"MaxWidth":null,"Height":"100%","MinHeight":null,"MaxHeight":null,"BorderSize":"0px","BorderColor":"#504f4f","BorderType":"dashed","BorderRadius":null,"Float":"left","Clear":null,"BackgroundColor":"transparent","BackgroundURL":null,"BackgroundRepeat":"repeat","BackgroundPosition":"0% 0%","MarginTop":"auto","MarginRight":"auto","MarginBottom":"auto","MarginLeft":"auto","PaddingTop":"auto","PaddingRight":"auto","PaddingBottom":"auto","PaddingLeft":"auto","Align":"justify","Position":"inherit","Left":"auto","Top":"auto","Right":"auto","Bottom":"auto","IsPopupInside":false}, {"id":"objectid-ec8279db-df41-4b93-8652-809545d9b3f1","parentId":"objectid-23db1186-d7c6-4a98-6951-f4bf4e71f05e","type":"nodecontent","BorderSize":null,"BorderColor":null,"BorderType":null,"BorderRadius":null,"Float":null,"Clear":null,"Align":"justify","FontFamily":"Arial","FontSize":12,"FontWeight":"normal","FontColor":"#000","MarginTop":"16px","MarginRight":"auto","MarginBottom":"auto","MarginLeft":"auto","Position":"inherit","Left":"auto","Top":"auto","Right":"auto","Bottom":"auto"}, {"id":"objectid-2a5d0909-f739-476b-262c-2b2de1b1e71b","parentId":"objectid-23db1186-d7c6-4a98-6951-f4bf4e71f05e","type":"nodetitle","BorderSize":null,"BorderColor":null,"BorderType":null,"BorderRadius":null,"Float":null,"Clear":null,"Align":"left","FontFamily":"Arial","FontSize":"26px","FontWeight":"bold","FontColor":"#000","MarginTop":"auto","MarginRight":"auto","MarginBottom":"auto","MarginLeft":"auto","Position":"inherit","Left":"auto","Top":"auto","Right":"auto","Bottom":"auto"}, {"id":"objectid-f3d9a18b-5122-442c-0778-1d428ff6bf9e","parentId":"objectid-60e108f9-254d-4df1-03c3-d81c7c98a767","type":"links","BorderSize":null,"BorderColor":null,"BorderType":null,"BorderRadius":null,"Float":"none","Clear":null,"FontFamily":"Arial","FontSize":12,"FontWeight":"normal","FontColor":"#000","BackgroundColor":"transparent","BackgroundURL":null,"BackgroundRepeat":"repeat","BackgroundPosition":"0% 0%","MarginTop":"auto","MarginRight":"auto","MarginBottom":"auto","MarginLeft":"auto","PaddingTop":"auto","PaddingRight":"auto","PaddingBottom":"auto","PaddingLeft":"auto","Align":"left","Position":"inherit","Left":"auto","Top":"auto","Right":"auto","Bottom":"auto"}, {"id":"objectid-60e108f9-254d-4df1-03c3-d81c7c98a767","parentId":"objectid-ff19d0fc-6b42-439d-9db4-d245414df64c","type":"block","Width":"79%","MinWidth":null,"MaxWidth":null,"Height":"100%","MinHeight":null,"MaxHeight":null,"BorderSize":"0px","BorderColor":"#504f4f","BorderType":"dashed","BorderRadius":null,"Float":"none","Clear":"both","BackgroundColor":"transparent","BackgroundURL":null,"BackgroundRepeat":"repeat","BackgroundPosition":"0% 0%","MarginTop":"auto","MarginRight":"auto","MarginBottom":"auto","MarginLeft":"auto","PaddingTop":"auto","PaddingRight":"auto","PaddingBottom":"auto","PaddingLeft":"auto","Align":"justify","Position":"inherit","Left":"auto","Top":"auto","Right":"auto","Bottom":"auto","IsPopupInside":false}, {"id":"objectid-0ccb7522-6fc3-4e37-548b-4db9f9136c1d","parentId":"objectid-ff19d0fc-6b42-439d-9db4-d245414df64c","type":"block","Width":"19%","MinWidth":null,"MaxWidth":null,"Height":280,"MinHeight":null,"MaxHeight":null,"BorderSize":"0px","BorderColor":"#504f4f","BorderType":"dashed","BorderRadius":null,"Float":"left","Clear":null,"BackgroundColor":"#ffffff","BackgroundURL":null,"BackgroundRepeat":"repeat","BackgroundPosition":"0% 0%","MarginTop":"auto","MarginRight":"auto","MarginBottom":"auto","MarginLeft":"5px","PaddingTop":"auto","PaddingRight":"auto","PaddingBottom":"auto","PaddingLeft":"auto","Align":"justify","Position":"relative","Left":"auto","Top":"auto","Right":"auto","Bottom":"auto","IsPopupInside":false}, {"id":"objectid-23db1186-d7c6-4a98-6951-f4bf4e71f05e","parentId":"objectid-ff19d0fc-6b42-439d-9db4-d245414df64c","type":"block","Width":"78%","MinWidth":null,"MaxWidth":null,"Height":"100%","MinHeight":null,"MaxHeight":null,"BorderSize":"0px","BorderColor":"#504f4f","BorderType":"dashed","BorderRadius":null,"Float":"left","Clear":null,"BackgroundColor":"#ffffff","BackgroundURL":"url(/files/skin_58/1387886540)","BackgroundRepeat":"repeat","BackgroundPosition":"0% 0%","MarginTop":"auto","MarginRight":"auto","MarginBottom":"auto","MarginLeft":"auto","PaddingTop":"10px","PaddingRight":"10px","PaddingBottom":"10px","PaddingLeft":"10px","Align":"justify","Position":"inherit","Left":"auto","Top":"auto","Right":"auto","Bottom":"auto","IsPopupInside":false}, {"id":"objectid-6985d501-126d-4ad6-82cb-b621aa47e8b0","parentId":null,"type":"root","BorderSize":null,"BorderColor":null,"BorderType":null,"BorderRadius":null,"Float":null,"Clear":null,"FontFamily":"Arial","FontSize":"12px","FontWeight":"bolder","FontColor":"#000","BackgroundColor":"#eeeeee","BackgroundURL":null,"BackgroundRepeat":null,"BackgroundPosition":"0 0px","MarginTop":"auto","MarginRight":"auto","MarginBottom":"auto","MarginLeft":"auto","PaddingTop":"auto","PaddingRight":"auto","PaddingBottom":"auto","PaddingLeft":"auto","Align":null,"Position":null,"Left":null,"Top":null,"Right":null,"Bottom":null}], "html": "PGRpdiBpZD0ib2JqZWN0aWQtNGZjNzMyZjEtY2Y3Zi00ODViLTVhYmItYjgxNTU2MWViMTJiIiBjbGFzcz0idWktcmVzaXphYmxlIiBzdHlsZT0id2lkdGg6IDkwJTsgaGVpZ2h0OiBhdXRvOyBib3JkZXI6IDBweCBkYXNoZWQgcmdiKDgwLCA3OSwgNzkpOyBmbG9hdDogbm9uZTsgbWFyZ2luOiAwcHggYXV0bzsgdGV4dC1hbGlnbjoganVzdGlmeTsgY2xlYXI6IGJvdGg7Ij48ZGl2IGlkPSJvYmplY3RpZC04NTE5YmU1OC03YTAyLTQ3NjctODRlYS1mYmI2ZjdhNzZjNDMiIGNsYXNzPSJ1aS1yZXNpemFibGUgcG9wdXAtaW5zaWRlLWNvbnRhaW5lciIgc3R5bGU9IndpZHRoOiA3OSU7IGhlaWdodDogYXV0bzsgYm9yZGVyOiAxcHggZGFzaGVkIHJnYigyNTUsIDI1NSwgMjU1KTsgZmxvYXQ6IGxlZnQ7IHRvcDogMHB4OyBsZWZ0OiAwcHg7IGJhY2tncm91bmQtY29sb3I6IHJnYigyNTUsIDI1NSwgMjU1KTsiPjxkaXYgaWQ9Im9iamVjdGlkLTQ3NmNkOWE5LTJmMGItNDZhNi0wODVlLTI1YzRiM2M4NmVkZCIgc3R5bGU9IndpZHRoOiBhdXRvOyBoZWlnaHQ6IGF1dG87IGNvbG9yOiByZ2IoMCwgMCwgMCk7IG1hcmdpbjogMTBweDsgZm9udC1zaXplOiAyNHB4OyBmb250LXdlaWdodDogYm9sZDsgdGV4dC1hbGlnbjogbGVmdDsiIGNsYXNzPSIiPntOT0RFX1RJVExFfTwvZGl2PjxkaXYgaWQ9Im9iamVjdGlkLTdlY2ZkM2QyLWMwM2MtNGUzZi0xNWZjLTQyODUwNzY3NTQxMCIgc3R5bGU9IndpZHRoOiBhdXRvOyBoZWlnaHQ6IGF1dG87IGNvbG9yOiByZ2IoMCwgMCwgMCk7IHRleHQtYWxpZ246IGp1c3RpZnk7IG1hcmdpbi1sZWZ0OiAxMHB4OyBtYXJnaW4tcmlnaHQ6IDEwcHg7IiBjbGFzcz0iIj57Tk9ERV9DT05URU5UfTwvZGl2PjxkaXYgaWQ9Im9iamVjdGlkLTg2OTdjNWJiLWQ1MjgtNDFkYy1jMTMwLTcyNDU3ZDA5MGNkMiIgY2xhc3M9InVpLXJlc2l6YWJsZSBjb21wb25lbnQtc2VsZWN0ZWQiIHN0eWxlPSJ3aWR0aDogNDklOyBoZWlnaHQ6IGF1dG87IGJvcmRlcjogMHB4IGRhc2hlZCByZ2IoODAsIDc5LCA3OSk7IGZsb2F0OiBsZWZ0OyBtYXJnaW4tbGVmdDogMTBweDsgbWFyZ2luLXRvcDogMjBweDsiPjxkaXYgaWQ9Im9iamVjdGlkLWRkN2IxZWQ5LWU3NzItNDUxNi0zZmIyLTg1NTA2ZWYzMzZiYSIgc3R5bGU9ImNvbG9yOiByZ2IoMCwgMCwgMCk7IHRleHQtYWxpZ246IGxlZnQ7IiBjbGFzcz0iIj57TElOS1N9PC9kaXY+PGRpdiBjbGFzcz0idWktcmVzaXphYmxlLWhhbmRsZSB1aS1yZXNpemFibGUtZSIgc3R5bGU9InotaW5kZXg6IDEwMDA7Ij48L2Rpdj48ZGl2IGNsYXNzPSJ1aS1yZXNpemFibGUtaGFuZGxlIHVpLXJlc2l6YWJsZS1zIiBzdHlsZT0iei1pbmRleDogMTAwMDsiPjwvZGl2PjxkaXYgY2xhc3M9InVpLXJlc2l6YWJsZS1oYW5kbGUgdWktcmVzaXphYmxlLXNlIHVpLWljb24gdWktaWNvbi1ncmlwc21hbGwtZGlhZ29uYWwtc2UiIHN0eWxlPSJ6LWluZGV4OiAxMDAwOyI+PC9kaXY+PC9kaXY+PGRpdiBpZD0ib2JqZWN0aWQtMWY5NmY2ZmMtZjdkMy00OTc5LWYyYTMtODM1Y2I0NGEwZjlhIiBjbGFzcz0idWktcmVzaXphYmxlIiBzdHlsZT0id2lkdGg6IDQwJTsgaGVpZ2h0OiBhdXRvOyBib3JkZXI6IDBweCBkYXNoZWQgcmdiKDgwLCA3OSwgNzkpOyBmbG9hdDogcmlnaHQ7IHRleHQtYWxpZ246IHJpZ2h0OyBtYXJnaW4tcmlnaHQ6IDEwcHg7IG1hcmdpbi10b3A6IDIwcHg7IG1hcmdpbi1ib3R0b206IDEwcHg7Ij48ZGl2IGlkPSJvYmplY3RpZC1iMGFjMWY2Zi1kMTdjLTQ0ODctYWMzNi0zZmU5ODk2NDAzN2UiIHN0eWxlPSJ3aWR0aDogYXV0bzsgaGVpZ2h0OiBhdXRvOyBjb2xvcjogcmdiKDAsIDAsIDApOyIgY2xhc3M9IiI+e0NPVU5URVJTfTwvZGl2PjxkaXYgY2xhc3M9InVpLXJlc2l6YWJsZS1oYW5kbGUgdWktcmVzaXphYmxlLWUiIHN0eWxlPSJ6LWluZGV4OiAxMDAwOyI+PC9kaXY+PGRpdiBjbGFzcz0idWktcmVzaXphYmxlLWhhbmRsZSB1aS1yZXNpemFibGUtcyIgc3R5bGU9InotaW5kZXg6IDEwMDA7Ij48L2Rpdj48ZGl2IGNsYXNzPSJ1aS1yZXNpemFibGUtaGFuZGxlIHVpLXJlc2l6YWJsZS1zZSB1aS1pY29uIHVpLWljb24tZ3JpcHNtYWxsLWRpYWdvbmFsLXNlIiBzdHlsZT0iei1pbmRleDogMTAwMDsiPjwvZGl2PjwvZGl2PjxkaXYgY2xhc3M9InVpLXJlc2l6YWJsZS1oYW5kbGUgdWktcmVzaXphYmxlLWUiIHN0eWxlPSJ6LWluZGV4OiAxMDAwOyI+PC9kaXY+PGRpdiBjbGFzcz0idWktcmVzaXphYmxlLWhhbmRsZSB1aS1yZXNpemFibGUtcyIgc3R5bGU9InotaW5kZXg6IDEwMDA7Ij48L2Rpdj48ZGl2IGNsYXNzPSJ1aS1yZXNpemFibGUtaGFuZGxlIHVpLXJlc2l6YWJsZS1zZSB1aS1pY29uIHVpLWljb24tZ3JpcHNtYWxsLWRpYWdvbmFsLXNlIiBzdHlsZT0iei1pbmRleDogMTAwMDsiPjwvZGl2PjwvZGl2PjxkaXYgaWQ9Im9iamVjdGlkLTYyZDViZTg0LTY3OTMtNDdiZS0xNWYyLTAyN2Y2Y2Y1MzgzYiIgY2xhc3M9InVpLXJlc2l6YWJsZSIgc3R5bGU9IndpZHRoOiAyMCU7IGhlaWdodDogYXV0bzsgYm9yZGVyOiAwcHggZGFzaGVkIHJnYig4MCwgNzksIDc5KTsgZmxvYXQ6IGxlZnQ7IG1hcmdpbi1sZWZ0OiA1cHg7IHRvcDogMHB4OyBsZWZ0OiAwcHg7IGJhY2tncm91bmQtY29sb3I6IHJnYigyNTUsIDI1NSwgMjU1KTsiPjxkaXYgaWQ9Im9iamVjdGlkLWVjMmM2Y2U2LTgwM2UtNDBlYS01NWM1LTM0NDk3NDIwOWNkYyIgc3R5bGU9ImNvbG9yOiByZ2IoMCwgMCwgMCk7IG1hcmdpbjogMTBweCAxMHB4IDIwcHg7IHRleHQtYWxpZ246IGxlZnQ7IiBjbGFzcz0iIj57TUFQX0lORk99PC9kaXY+PGRpdiBpZD0ib2JqZWN0aWQtOGZlMzNhMjMtZDhiNy00OGRmLTQ5ZTctNDZhYTMyNDQ3ZThlIiBjbGFzcz0idWktcmVzaXphYmxlIiBzdHlsZT0id2lkdGg6IDEwMCU7IGhlaWdodDogYXV0bzsgYm9yZGVyOiAwcHggZGFzaGVkIHJnYig4MCwgNzksIDc5KTsgZmxvYXQ6IG5vbmU7IG1hcmdpbi1sZWZ0OiBhdXRvOyBtYXJnaW4tcmlnaHQ6IGF1dG87IHBhZGRpbmctbGVmdDogMTBweDsiPjxkaXYgaWQ9Im9iamVjdGlkLWNiMTFjNDViLTU3ZDEtNDVkYy0yYzU0LTE0NzhlMDRhZDk5NyI+e0JPT0tNQVJLfTwvZGl2PjxkaXYgY2xhc3M9InVpLXJlc2l6YWJsZS1oYW5kbGUgdWktcmVzaXphYmxlLWUiIHN0eWxlPSJ6LWluZGV4OiAxMDAwOyI+PC9kaXY+PGRpdiBjbGFzcz0idWktcmVzaXphYmxlLWhhbmRsZSB1aS1yZXNpemFibGUtcyIgc3R5bGU9InotaW5kZXg6IDEwMDA7Ij48L2Rpdj48ZGl2IGNsYXNzPSJ1aS1yZXNpemFibGUtaGFuZGxlIHVpLXJlc2l6YWJsZS1zZSB1aS1pY29uIHVpLWljb24tZ3JpcHNtYWxsLWRpYWdvbmFsLXNlIiBzdHlsZT0iei1pbmRleDogMTAwMDsiPjwvZGl2PjwvZGl2PjxkaXYgaWQ9Im9iamVjdGlkLWZmZjNjZWRlLWE1NTgtNDZlYy01OWI2LTBmN2M5NmI0YWUzNyIgY2xhc3M9InVpLXJlc2l6YWJsZSIgc3R5bGU9IndpZHRoOiAxMDAlOyBoZWlnaHQ6IGF1dG87IGJvcmRlcjogMHB4IGRhc2hlZCByZ2IoODAsIDc5LCA3OSk7IGZsb2F0OiBub25lOyBwYWRkaW5nLWxlZnQ6IDEwcHg7IG1hcmdpbi1ib3R0b206IDIwcHg7Ij48ZGl2IGlkPSJvYmplY3RpZC02MTdiNGY5NS0zODFmLTRiZDYtNzk5Yy1lMWRiMjQyM2UwZjMiPntSRVNFVH08L2Rpdj48ZGl2IGNsYXNzPSJ1aS1yZXNpemFibGUtaGFuZGxlIHVpLXJlc2l6YWJsZS1lIiBzdHlsZT0iei1pbmRleDogMTAwMDsiPjwvZGl2PjxkaXYgY2xhc3M9InVpLXJlc2l6YWJsZS1oYW5kbGUgdWktcmVzaXphYmxlLXMiIHN0eWxlPSJ6LWluZGV4OiAxMDAwOyI+PC9kaXY+PGRpdiBjbGFzcz0idWktcmVzaXphYmxlLWhhbmRsZSB1aS1yZXNpemFibGUtc2UgdWktaWNvbiB1aS1pY29uLWdyaXBzbWFsbC1kaWFnb25hbC1zZSIgc3R5bGU9InotaW5kZXg6IDEwMDA7Ij48L2Rpdj48L2Rpdj48aW1nIGlkPSJvYmplY3RpZC1hZGI3M2Y2ZC0yYzVlLTRmOTctODVlNy02ZjY2MmY3NWVjY2IiIHNyYz0iL2ltYWdlcy9vcGVubGFieXJpbnRoLXBvd2VybG9nby13ZWUuanBnIiBzdHlsZT0id2lkdGg6IDUwJTsgaGVpZ2h0OiBhdXRvOyBmbG9hdDogbm9uZTsgbWFyZ2luLWxlZnQ6IDEwcHg7IG1hcmdpbi1ib3R0b206IDEwcHg7IiBjbGFzcz0iIj48ZGl2IGNsYXNzPSJ1aS1yZXNpemFibGUtaGFuZGxlIHVpLXJlc2l6YWJsZS1lIiBzdHlsZT0iei1pbmRleDogMTAwMDsiPjwvZGl2PjxkaXYgY2xhc3M9InVpLXJlc2l6YWJsZS1oYW5kbGUgdWktcmVzaXphYmxlLXMiIHN0eWxlPSJ6LWluZGV4OiAxMDAwOyI+PC9kaXY+PGRpdiBjbGFzcz0idWktcmVzaXphYmxlLWhhbmRsZSB1aS1yZXNpemFibGUtc2UgdWktaWNvbiB1aS1pY29uLWdyaXBzbWFsbC1kaWFnb25hbC1zZSIgc3R5bGU9InotaW5kZXg6IDEwMDA7Ij48L2Rpdj48L2Rpdj48ZGl2IGlkPSJvYmplY3RpZC02YjEyMjFmYi0xZTBlLTQ5YzUtMzNlNS0zNGFlNzA0NmVkMTQiIGNsYXNzPSJ1aS1yZXNpemFibGUiIHN0eWxlPSJ3aWR0aDogMTAwJTsgaGVpZ2h0OiAzNXB4OyBib3JkZXI6IDBweCBkYXNoZWQgcmdiKDgwLCA3OSwgNzkpOyBmbG9hdDogbGVmdDsiPjxkaXYgaWQ9Im9iamVjdGlkLWY1ODgzNDZkLTNmM2YtNDI1NC1iMzJmLWQ3OTgzOGI1YjYyYiIgc3R5bGU9ImNvbG9yOiByZ2IoMCwgMCwgMCk7IHRleHQtYWxpZ246IGxlZnQ7IG1hcmdpbi1sZWZ0OiAxMHB4OyBtYXJnaW4tdG9wOiAxMHB4OyBmb250LXNpemU6IDE0cHg7IiBjbGFzcz0iIj57UkVWSUVXfTwvZGl2PjxkaXYgY2xhc3M9InVpLXJlc2l6YWJsZS1oYW5kbGUgdWktcmVzaXphYmxlLWUiIHN0eWxlPSJ6LWluZGV4OiAxMDAwOyI+PC9kaXY+PGRpdiBjbGFzcz0idWktcmVzaXphYmxlLWhhbmRsZSB1aS1yZXNpemFibGUtcyIgc3R5bGU9InotaW5kZXg6IDEwMDA7Ij48L2Rpdj48ZGl2IGNsYXNzPSJ1aS1yZXNpemFibGUtaGFuZGxlIHVpLXJlc2l6YWJsZS1zZSB1aS1pY29uIHVpLWljb24tZ3JpcHNtYWxsLWRpYWdvbmFsLXNlIiBzdHlsZT0iei1pbmRleDogMTAwMDsiPjwvZGl2PjwvZGl2PjxkaXYgY2xhc3M9InVpLXJlc2l6YWJsZS1oYW5kbGUgdWktcmVzaXphYmxlLWUiIHN0eWxlPSJ6LWluZGV4OiAxMDAwOyI+PC9kaXY+PGRpdiBjbGFzcz0idWktcmVzaXphYmxlLWhhbmRsZSB1aS1yZXNpemFibGUtcyIgc3R5bGU9InotaW5kZXg6IDEwMDA7Ij48L2Rpdj48ZGl2IGNsYXNzPSJ1aS1yZXNpemFibGUtaGFuZGxlIHVpLXJlc2l6YWJsZS1zZSB1aS1pY29uIHVpLWljb24tZ3JpcHNtYWxsLWRpYWdvbmFsLXNlIiBzdHlsZT0iei1pbmRleDogMTAwMDsiPjwvZGl2PjwvZGl2Pg=="}',

            '<div id="objectid-4fc732f1-cf7f-485b-5abb-b815561eb12b" class="ui-resizable" style="width: 90%; height: auto; border: 0px dashed rgb(80, 79, 79); float: none; margin: 0px auto; text-align: justify; clear: both;">'.
            '<div id="objectid-8519be58-7a02-4767-84ea-fbb6f7a76c43" class="ui-resizable popup-inside-container" style="width: 79%; height: auto; border: 1px dashed rgb(255, 255, 255); float: left; top: 0px; left: 0px; background-color: rgb(255, 255, 255);">'.
            '<div id="objectid-476cd9a9-2f0b-46a6-085e-25c4b3c86edd" style="width: auto; height: auto; color: rgb(0, 0, 0); margin: 10px; font-size: 24px; font-weight: bold; text-align: left;" class="">{NODE_TITLE}</div>'.
            '<div id="objectid-7ecfd3d2-c03c-4e3f-15fc-428507675410" style="width: auto; height: auto; color: rgb(0, 0, 0); text-align: justify; margin-left: 10px; margin-right: 10px;" class="">{NODE_CONTENT}</div>'.
            '<div id="objectid-8697c5bb-d528-41dc-c130-72457d090cd2" class="ui-resizable component-selected" style="width: 49%; height: auto; border: 0px dashed rgb(80, 79, 79); float: left; margin-left: 10px; margin-top: 20px;">'.
            '<div id="objectid-dd7b1ed9-e772-4516-3fb2-85506ef336ba" style="color: rgb(0, 0, 0); text-align: left;" class="">{LINKS}</div>'.
            '<div class="ui-resizable-handle ui-resizable-e" style="z-index: 1000;"></div>'.
            '<div class="ui-resizable-handle ui-resizable-s" style="z-index: 1000;"></div>'.
            '<div class="ui-resizable-handle ui-resizable-se ui-icon ui-icon-gripsmall-diagonal-se" style="z-index: 1000;"></div>'.
            '</div>'.
            '<div id="objectid-1f96f6fc-f7d3-4979-f2a3-835cb44a0f9a" class="ui-resizable" style="width: 40%; height: auto; border: 0px dashed rgb(80, 79, 79); float: right; text-align: right; margin-right: 10px; margin-top: 20px; margin-bottom: 10px;">'.
            '<div id="objectid-b0ac1f6f-d17c-4487-ac36-3fe98964037e" style="width: auto; height: auto; color: rgb(0, 0, 0);" class="">{COUNTERS}</div>'.
            '<div class="ui-resizable-handle ui-resizable-e" style="z-index: 1000;"></div>'.
            '<div class="ui-resizable-handle ui-resizable-s" style="z-index: 1000;"></div>'.
            '<div class="ui-resizable-handle ui-resizable-se ui-icon ui-icon-gripsmall-diagonal-se" style="z-index: 1000;"></div>'.
            '</div>'.
            '<div class="ui-resizable-handle ui-resizable-e" style="z-index: 1000;"></div>'.
            '<div class="ui-resizable-handle ui-resizable-s" style="z-index: 1000;"></div>'.
            '<div class="ui-resizable-handle ui-resizable-se ui-icon ui-icon-gripsmall-diagonal-se" style="z-index: 1000;"></div>'.
            '</div>'.
            '<div id="objectid-62d5be84-6793-47be-15f2-027f6cf5383b" class="ui-resizable" style="width: 20%; height: auto; border: 0px dashed rgb(80, 79, 79); float: left; margin-left: 5px; top: 0px; left: 0px; background-color: rgb(255, 255, 255);">'.
            '<div id="objectid-ec2c6ce6-803e-40ea-55c5-344974209bbb" style="color: rgb(0, 0, 0); margin: 10px; text-align: left;" c"">{SECTION_INFO}</div>'.
            '<div id="objectid-ec2c6ce6-803e-40ea-55c5-344974209cdc" style="color: rgb(0, 0, 0); margin: 10px 10px 20px; text-align: left;" class="">{MAP_INFO}</div>'.
            '<div id="objectid-8fe33a23-d8b7-48df-49e7-46aa32447e8e" class="ui-resizable" style="width: 100%; height: auto; border: 0px dashed rgb(80, 79, 79); float: none; margin-left: auto; margin-right: auto; padding-left: 10px;">'.
            '<div id="objectid-cb11c45b-57d1-45dc-2c54-1478e04ad997">{BOOKMARK}</div>'.
            '<div class="ui-resizable-handle ui-resizable-e" style="z-index: 1000;"></div>'.
            '<div class="ui-resizable-handle ui-resizable-s" style="z-index: 1000;"></div>'.
            '<div class="ui-resizable-handle ui-resizable-se ui-icon ui-icon-gripsmall-diagonal-se" style="z-index: 1000;"></div>'.
            '</div>'.
            '<div id="objectid-fff3cede-a558-46ec-59b6-0f7c96b4ae37" class="ui-resizable" style="width: 100%; height: auto; border: 0px dashed rgb(80, 79, 79); float: none; padding-left: 10px; margin-bottom: 20px;">'.
            '<div id="objectid-617b4f95-381f-4bd6-799c-e1db2423e0f3">{RESET}</div>'.
            '<div class="ui-resizable-handle ui-resizable-e" style="z-index: 1000;"></div>'.
            '<div class="ui-resizable-handle ui-resizable-s" style="z-index: 1000;"></div>'.
            '<div class="ui-resizable-handle ui-resizable-se ui-icon ui-icon-gripsmall-diagonal-se" style="z-index: 1000;"></div>'.
            '</div>'.
            '<img id="objectid-adb73f6d-2c5e-4f97-85e7-6f662f75eccb" src="/images/openlabyrinth-powerlogo-wee.jpg" style="width: 50%; height: auto; float: none; margin-left: 10px; margin-bottom: 10px;" class="">'.
            '<div class="ui-resizable-handle ui-resizable-e" style="z-index: 1000;"></div>'.
            '<div class="ui-resizable-handle ui-resizable-s" style="z-index: 1000;"></div>'.
            '<div class="ui-resizable-handle ui-resizable-se ui-icon ui-icon-gripsmall-diagonal-se" style="z-index: 1000;"></div>'.
            '</div>'.
            '<div id="objectid-6b1221fb-1e0e-49c5-33e5-34ae7046ed14" class="ui-resizable" style="width: 100%; height: 35px; border: 0px dashed rgb(80, 79, 79); float: left;">'.
            '<div id="objectid-f588346d-3f3f-4254-b32f-d79838b5b62b" style="color: rgb(0, 0, 0); text-align: left; margin-left: 10px; margin-top: 10px; font-size: 14px;" class="">{REVIEW}</div>'.
            '<div class="ui-resizable-handle ui-resizable-e" style="z-index: 1000;"></div>'.
            '<div class="ui-resizable-handle ui-resizable-s" style="z-index: 1000;"></div>'.
            '<div class="ui-resizable-handle ui-resizable-se ui-icon ui-icon-gripsmall-diagonal-se" style="z-index: 1000;"></div>'.
            '</div>'.
            '<div class="ui-resizable-handle ui-resizable-e" style="z-index: 1000;"></div>'.
            '<div class="ui-resizable-handle ui-resizable-s" style="z-index: 1000;"></div>'.
            '<div class="ui-resizable-handle ui-resizable-se ui-icon ui-icon-gripsmall-diagonal-se" style="z-index: 1000;"></div>'.
            '</div>',
            'background-color: rgb(238, 238, 238); background-size: 100%; font-family: Arial; font-size: 12px; color: rgb(0, 0, 0); background-position: 0px 0px;'
        );

        return $skin->id;
    }

    public function action_saveSkin()
    {
        $mapId = $this->request->param('id', NULL);

        if ( ! isset($_POST['save'])) Request::initial()->redirect(URL::base());

        $skinId = $this->saveSkin($mapId, $this->request->post('skin_name'));

        Request::initial()->redirect(URL::base().'skinManager/editSkins/'.$mapId.'/'.$skinId);
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

    public function action_editSkins()
    {
        $mapId  = $this->request->param('id', NULL);
        $skinId = $this->request->param('id2', NULL);

        DB_ORM::model('User')->can('edit', array('mapId' => $mapId));

        $this->templateData['map']        = DB_ORM::model('map', array((int) $mapId));
        $this->templateData['action']     = 'editSkins';
        $this->templateData['navigation'] = View::factory('labyrinth/skin/navigation')->set('templateData', $this->templateData);

        if ($skinId) {
            $skinData = DB_ORM::model('map_skin')->getSkinById($skinId);
            $this->templateData['skinData'] = $skinData;
            $skinSourcePath = DOCROOT.'/application/views/labyrinth/skin/'.$skinId.'/skin.source';

            if (file_exists($skinSourcePath)) {
                $this->templateData['skinHTML'] = base64_encode(file_get_contents($skinSourcePath));
            }

            $this->templateData['action_url'] = URL::base().'skinManager/skinEditorUpload/'.$mapId;
            $this->templateData['skinError'] = Session::instance()->get('skinError');
            Session::instance()->delete('skinError');
            $this->template = View::factory($skinData->data != null ? 'labyrinth/skin/skinEditor' : 'labyrinth/skin/skinEditorOld');
            $this->template->set('templateData', $this->templateData);
        } else {
            Breadcrumbs::add(Breadcrumb::factory()->set_title($this->templateData['map']->name)->set_url(URL::base().'labyrinthManager/global/'.$mapId));
            Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Skin'))->set_url(URL::base().'skinManager/index/'.$mapId));
            Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Edit my skins'))->set_url(URL::base().'skinManager/editSkins/'.$mapId));

            $this->templateData['skinList'] = DB_ORM::model('map_skin')->getSkinsByUserId(Auth::instance()->get_user()->id);
            $this->templateData['center'] = View::factory('labyrinth/skin/editList')->set('templateData', $this->templateData);
        }

        $this->templateData['left'] = View::factory('labyrinth/labyrinthEditorMenu')->set('templateData', $this->templateData);
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

        DB_ORM::model('User')->can('edit', array('mapId' => $mapId));

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

    public function action_uploadSkin()
    {
        $mapId = $this->request->param('id', NULL);

        $this->templateData['map']        = DB_ORM::model('map', array((int) $mapId));
        $this->templateData['action']     = 'uploadSkin';
        $this->templateData['navigation'] = View::factory('labyrinth/skin/navigation')->set('templateData', $this->templateData);
        $this->templateData['left']       = View::factory('labyrinth/labyrinthEditorMenu')->set('templateData', $this->templateData);
        $this->templateData['center']     = View::factory('labyrinth/skin/upload')->set('templateData', $this->templateData);

        $this->template->set('templateData', $this->templateData);
        Breadcrumbs::add(Breadcrumb::factory()->set_title($this->templateData['map']->name)->set_url(URL::base().'labyrinthManager/global/'.$mapId));
    }

    public function action_uploadNewSkin()
    {
        $mapId = $this->request->param('id', NULL);
        $zip   = Arr::get($_FILES, 'zipSkin', false);
        $tmp   = Arr::get($zip, 'tmp_name');

        if (is_uploaded_file($tmp))
        {
            $zipName   = Arr::get($zip, 'name');
            $ext       = substr($zipName, -3);
            $filename  = substr(($zipName), 0, strlen($zipName) - 4);
            $checkName = DB_ORM::model('map_skin')->getMapByName($filename);

            if ($checkName != false) $filename .= rand(0, 100000);
            if ($ext == 'zip')
            {
                $zip = new ZipArchive();
                if ($zip->open($tmp) === true)
                {
                    $skin     = DB_ORM::model('Map_Skin')->addSkin($filename, $mapId.'_'.$filename);
                    $skinId   = $skin->id;
                    $skinPath = DOCROOT.'/application/views/labyrinth/skin/'.$skinId.'/';

                    $zip->extractTo($skinPath);
                    $zip->close();

                    // get unique data, and delete unnecessary file
                    $data = file_get_contents($skinPath.'skindata.source');
                    unlink($skinPath.'skindata.source');

                    DB_ORM::update('Map_Skin')->set('data', $data)->where('id', '=', $skinId)->execute();
                    DB_ORM::model('Map')->updateMapSkin($mapId, $skinId);
                }
            }
        }
        Request::initial()->redirect(URL::base().'skinManager/editSkins/'.$mapId);
    }

    public function action_exportSkins()
    {
        $skinId      = $this->request->param('id');
        $skinObj     = DB_ORM::model('Map_Skin', array($skinId));
        $skinName    = $skinObj->name;
        $skinData    = $skinObj->data;
        $path        = DOCROOT.'/application/views/labyrinth/skin/'.$skinId.'/';
        $filesByPath = scandir($path);
        $tmpPath     = DOCROOT.'/tmp/';

        // get name of all files by path
        $files = array();
        foreach($filesByPath as $fileName)
        {
            $files[] = $path.$fileName;
        }
        $files = array_slice($files, 2);

        // create unique file skindata.source
        $uniqueFile = $tmpPath.'skindata.source';
        file_put_contents($uniqueFile, $skinData);

        // create archive
        $zip = new ZipArchive;
        $pathToArchive = $tmpPath.$skinId.".zip";
        $zip->open($pathToArchive, ZipArchive::CREATE);
        foreach ($files as $file)
        {
            $zip->addFile($file, basename($file));
        }
        $zip->addFile($uniqueFile, 'skindata.source');
        $zip->close();
        unlink($uniqueFile);

        // Give file to user
        header("Content-type: application/zip");
        header("Content-Disposition: attachment; filename=$skinName.zip");
        header("Content-length: ".filesize($path));

        // send archive
        readfile($pathToArchive);
        exit;
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

    public function action_ajaxCloneSkinData() {
        $mapId  = Arr::get($_POST, 'mapId', null);
        $data   = Arr::get($_POST, 'data', null);
        $html   = Arr::get($_POST, 'html', null);
        $name   = Arr::get($_POST, 'name', null);
        $skinId = $this->saveSkin($mapId, $name);

        DB_ORM::model('map_skin')->updateSkinData($skinId, $data, $html);

        exit(json_encode($skinId));
    }

    public function action_updateSkinData() {
        $this->auto_render = false;

        $skinId = Arr::get($_POST, 'skinId', null);
        $data   = Arr::get($_POST, 'data', null);
        $html   = Arr::get($_POST, 'html', null);

        if($skinId == null) { echo '{status: "error", errorMessage: "Wrong Skin ID"}'; return; }

        DB_ORM::model('map_skin')->updateSkinData($skinId, $data, $html);

        echo '{status: "ok"}';
    }

    public function action_uploadSkinImage()
    {
        $this->auto_render = false;

        $skinId   = Arr::get($_POST, 'skinId', null);
        $fileName = Arr::get($_POST, 'fileName', null);

        if ($skinId == null) {
            echo '{"status": "error", "errorMessage": "Wrong Skin ID"}';
            return;
        }

        $data    = Arr::get($_POST, 'data', null);
        $skinDir = $_SERVER['DOCUMENT_ROOT'].'/files/skin_'.$skinId.'/';
        if( ! is_dir($skinDir)) {
            mkdir($skinDir);
        }
        $file = $skinDir.$fileName;
        $this->base64_to_jpeg($data, $file);

        echo '{"status": "ok", "path": "'.URL::base().'files/skin_'.$skinId.'/'.$fileName.'"}';
    }

    function base64_to_jpeg($base64_string, $output_file) {

        $ifp = fopen($output_file, "wb");

        $data = explode(',', $base64_string);

        fwrite($ifp, base64_decode($data[1]));
        fclose($ifp);

        return $output_file;
    }
}