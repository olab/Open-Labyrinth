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

class Controller_ExportImportManager extends Controller_Base {

    public function before() {
        parent::before();

        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('My Labyrinths'))->set_url(URL::base() . 'authoredLabyrinth'));
    }

    public function action_index() {
        $mapId = $this->request->param('id', NULL);
        if ($mapId != NULL) {
            $this->templateData['map'] = DB_ORM::model('map', array((int) $mapId));

            $exportView = View::factory('labyrinth/export/export');
            $exportView->set('templateData', $this->templateData);

            $leftView = View::factory('labyrinth/labyrinthEditorMenu');
            $leftView->set('templateData', $this->templateData);

            $this->templateData['center'] = $exportView;
            $this->templateData['left'] = $leftView;
            unset($this->templateData['right']);
            $this->template->set('templateData', $this->templateData);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

    public function action_exportVUE() {
        $mapId = $this->request->param('id', NULL);

        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Export VUE'))->set_url(URL::base() . 'exportimportmanager/exportVUE/id/' . $mapId));

        if ($mapId != NULL) {
            $this->exportVUE($mapId);
            Request::initial()->redirect(URL::base() . 'export/OLVue-export-' . $mapId . '.vue');
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

    public function action_importVUE() {
        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Import VUE'))->set_url(URL::base() . 'exportimportmanager/importVUE'));

        $this->templateData['center'] = View::factory('labyrinth/import/vue');
        unset($this->templateData['left']);
        unset($this->templateData['right']);
        $this->template->set('templateData', $this->templateData);
    }

    public function action_importMVP() {
        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Import MedBiquitous VP'))->set_url(URL::base() . 'exportimportmanager/importMVP'));
        $this->templateData['center'] = View::factory('labyrinth/import/mvp');
        $this->template->set('templateData', $this->templateData);
    }

    public function action_uploadVUE() {
        if (isset($_FILES) && !empty($_FILES)) {
            if ($_FILES['filename']['size'] < 1024 * 3 * 1024) { //@todo Questionable.
                if (is_uploaded_file($_FILES['filename']['tmp_name'])) {
                    move_uploaded_file($_FILES['filename']['tmp_name'], DOCROOT . '/files/' . $_FILES['filename']['name']);
                    $fileName = 'files/' . $_FILES['filename']['name'];
                    $this->importVUE(DOCROOT . '/files/' . $_FILES['filename']['name'], Arr::get($_POST, 'mapname', ''));
                    unlink(DOCROOT . '/' . $fileName);
                }
            }
        }

        Request::initial()->redirect(URL::base());
    }

    public function action_exportMVP()
    {
        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Export Medbiquitous ANSI'))->set_url(URL::base() . 'exportimportmanager/exportMVP'));

        $this->templateData['maps'] = (Auth::instance()->get_user()->type->name == 'superuser')
            ? DB_ORM::model('map')->getAllEnabledMap()
            : DB_ORM::model('map')->getAllEnabledAndAuthoredMap(Auth::instance()->get_user()->id);

        $this->templateData['center'] = View::factory('labyrinth/export/mvp')->set('templateData', $this->templateData);
        $this->template->set('templateData', $this->templateData);
    }

    public function action_exportMVPMap()
    {
        $mapId = $this->request->param('id', 0);

        if ( ! $mapId) Request::initial()->redirect(URL::base().'exportimportmanager/exportMVP');

        $map                = DB_ORM::model('map', array((int) $mapId));
        $params['mapId']    = $mapId;
        $params['mapName']  = $map->name;
        $path               = ImportExport_Manager::getFormatSystem('MVP')->export($params);
        $pathInfo           = pathinfo($path);

        header("Content-type: application/zip");
        header("Content-Disposition: attachment; filename=".$pathInfo['basename']);
        header("Content-length: ".filesize($path));

        readfile($path);
    }

    public function exportVUE($mapId) {
        if ($mapId != NULL) {
            $header = '';
            $header .= "<?xml version='1.0' encoding='US-ASCII'?>" . chr(13) . chr(10);
            $header .= "<LW-MAP xmlns:xsi='http://www.w3.org/2001/XMLSchema-instance' xsi:noNamespaceSchemaLocation='none' ID='0' label='simplevue.vue' x='0.0' y='0.0' width='1.4E-45' height='1.4E-45' strokeWidth='0.0' autoSized='false'>" . chr(13) . chr(10);
            $header .= "<fillColor>#FFFFFF</fillColor>" . chr(13) . chr(10);
            $header .= "<strokeColor>#404040</strokeColor>" . chr(13) . chr(10);
            $header .= "<textColor>#000000</textColor>" . chr(13) . chr(10);
            $header .= "<font>SansSerif-plain-14</font>" . chr(13) . chr(10);
            $header .= "<metadata-list category-list-size='2' other-list-size='0' ontology-list-size='0' RCategoryListSize='0'>" . chr(13) . chr(10);
            $header .= "<ontology-list-string></ontology-list-string>" . chr(13) . chr(10);
            $header .= "<metadata xsi:type='vue-metadata-element'>" . chr(13) . chr(10);
            $header .= "<value></value>" . chr(13) . chr(10);
            $header .= "<key>http://vue.tufts.edu/vue.rdfs#none</key>" . chr(13) . chr(10);
            $header .= "<type>1</type>" . chr(13) . chr(10);
            $header .= "</metadata>" . chr(13) . chr(10);
            $header .= "<metadata xsi:type='vue-metadata-element'>" . chr(13) . chr(10);
            $header .= "<value>" . $mapId . "</value>" . chr(13) . chr(10);
            $header .= "<key>http://vue.tufts.edu/custom.rdfs#OLmapID</key>" . chr(13) . chr(10);
            $header .= "<type>1</type>" . chr(13) . chr(10);
            $header .= "</metadata>" . chr(13) . chr(10);
            $header .= "</metadata-list>" . chr(13) . chr(10);
            $header .= "<URIString>http://vue.tufts.edu/rdf/resource/8269e781a9fe648b019fe5e694210f17</URIString>" . chr(13) . chr(10);

            $nodes = DB_ORM::model('map_node')->getNodesByMap($mapId);
            $nodeResult = '';
            $linksResult = '';
            $footerResult = '';
            if ($nodes != NULL and count($nodes) > 0) {
                foreach ($nodes as $node) {
                    $title = $node->title;
                    $text = $node->text;

                    $title = str_replace(".", ".amp;", $title);
                    $title = str_replace("<", ".lt;", $title);
                    $title = str_replace(chr(34), ".quot;", $title);

                    $text = str_replace(".", ".amp;", $text);
                    $text = str_replace("<", ".lt;", $text);
                    $text = str_replace('"', ".quot;", $text);
                    $text = str_replace('&nbsp;', ' ', $text);

                    $mnbody = '';
                    while (strlen($text) > 70) {
                        $a = substr($text, 0, 70);
                        $text = str_replace($a, "", $text);
                        $mnbody .= $a;

                        while ((substr($text, 0, 1) != " ") and (strlen($text) > 1)) {
                            $h = substr($text, 0, 1);
                            $mnbody .= $h;
                            $text = substr($text, 1, strlen($text));
                        }
                        $mnbody .= ".#xa;" . chr(13) . chr(10);
                    }

                    $mnbody .= $text;

                    $nodeResult .= "<child ID=" . chr(34) . $node->id . chr(34) . " label=" . chr(34) . $node->title . ".#xa;\\\---///.#xa;" . $mnbody . chr(34) . " x=" . chr(34) . ($node->x * 2) . chr(34) . " y=" . chr(34) . ($node->y * 2) . chr(34) . " width=" . chr(34) . "150.0" . chr(34) . " height=" . chr(34) . "100.0" . chr(34) . " strokeWidth=" . chr(34) . "1.0" . chr(34) . " autoSized=" . chr(34) . "false" . chr(34) . " xsi:type=" . chr(34) . "node" . chr(34) . ">" . chr(13) . chr(10);
                    $nodeResult .= "<fillColor>" . str_replace('0x', '#', $node->rgb) . "</fillColor>" . chr(13) . chr(10);
                    $nodeResult .= "<strokeColor>#333333</strokeColor>" . chr(13) . chr(10);
                    $nodeResult .= "<textColor>#000000</textColor>" . chr(13) . chr(10);
                    $nodeResult .= "<font>Arial-plain-12</font>" . chr(13) . chr(10);
                    $nodeResult .= "<metadata-list category-list-size=" . chr(34) . "1" . chr(34) . " other-list-size=" . chr(34) . "0" . chr(34) . " ontology-list-size=" . chr(34) . "0" . chr(34) . " RCategoryListSize=" . chr(34) . "0" . chr(34) . ">" . chr(13) . chr(10);
                    $nodeResult .= "<ontology-list-string></ontology-list-string>" . chr(13) . chr(10);
                    $nodeResult .= "<metadata xsi:type=" . chr(34) . "vue-metadata-element" . chr(34) . ">" . chr(13) . chr(10);
                    $nodeResult .= "<value></value>" . chr(13) . chr(10);
                    $nodeResult .= "<key>http://vue.tufts.edu/vue.rdfs#none</key>" . chr(13) . chr(10);
                    $nodeResult .= "<type>1</type>" . chr(13) . chr(10);
                    $nodeResult .= "</metadata>" . chr(13) . chr(10);
                    $nodeResult .= "</metadata-list>" . chr(13) . chr(10);
                    $nodeResult .= "<URIString>http://vue.tufts.edu/rdf/resource/8269e781a9fe648b019fe5e646094b9d</URIString>" . chr(13) . chr(10);
                    $nodeResult .= "<shape arcwidth=" . chr(34) . "20.0" . chr(34) . " archeight=" . chr(34) . "20.0" . chr(34) . " xsi:type=" . chr(34) . "roundRect" . chr(34) . "/>" . chr(13) . chr(10);
                    $nodeResult .= "</child>" . chr(13) . chr(10);

                    if (count($node->links) > 0) {
                        foreach ($node->links as $link) {
                            $linksResult .= "<child ID=" . chr(34) . $link->id . chr(34) . " x=" . chr(34) . "1" . chr(34) . " y=" . chr(34) . "1" . chr(34) . " width=" . chr(34) . "1" . chr(34) . " height=" . chr(34) . "1" . chr(34) . " strokeWidth=" . chr(34) . "1.0" . chr(34) . " autoSized=" . chr(34) . "false" . chr(34) . " controlCount=" . chr(34) . "0" . chr(34) . " arrowState=" . chr(34) . "2" . chr(34) . " xsi:type=" . chr(34) . "link" . chr(34) . ">" . chr(13) . chr(10);
                            $linksResult .= "<strokeColor>#404040</strokeColor>" . chr(13) . chr(10);
                            $linksResult .= "<textColor>#404040</textColor>" . chr(13) . chr(10);
                            $linksResult .= "<font>Arial-plain-11</font>" . chr(13) . chr(10);
                            $linksResult .= "<metadata-list category-list-size=" . chr(34) . "1" . chr(34) . " other-list-size=" . chr(34) . "0" . chr(34) . " ontology-list-size=" . chr(34) . "0" . chr(34) . " RCategoryListSize=" . chr(34) . "0" . chr(34) . ">" . chr(13) . chr(10);
                            $linksResult .= "<ontology-list-string></ontology-list-string>" . chr(13) . chr(10);
                            $linksResult .= "<metadata xsi:type=" . chr(34) . "vue-metadata-element" . chr(34) . ">" . chr(13) . chr(10);
                            $linksResult .= "<value></value>" . chr(13) . chr(10);
                            $linksResult .= "<key>http://vue.tufts.edu/vue.rdfs#none</key>" . chr(13) . chr(10);
                            $linksResult .= "<type>1</type>" . chr(13) . chr(10);
                            $linksResult .= "</metadata>" . chr(13) . chr(10);
                            $linksResult .= "</metadata-list>" . chr(13) . chr(10);
                            $linksResult .= "<URIString>http://vue.tufts.edu/rdf/resource/8269e795a9fe648b019fe5e649ec98b8</URIString>" . chr(13) . chr(10);

                            $linksResult .= "<point1 x=" . chr(34) . $link->node_1->x . chr(34) . " y=" . chr(34) . $link->node_1->y . chr(34) . "/>" . chr(13) . chr(10);

                            $linksResult .= "<point2 x=" . chr(34) . $link->node_2->x . chr(34) . " y=" . chr(34) . $link->node_2->x . chr(34) . "/>" . chr(13) . chr(10);

                            $linksResult .= "<ID1 xsi:type=" . chr(34) . "node" . chr(34) . ">" . $link->node_1->id . "</ID1>" . chr(13) . chr(10);
                            $linksResult .= "<ID2 xsi:type=" . chr(34) . "node" . chr(34) . ">" . $link->node_2->id . "</ID2>" . chr(13) . chr(10);
                            $linksResult .= "</child>" . chr(13) . chr(10);
                        }
                    }
                }
            }

            $footerResult .= "<userZoom>1.0</userZoom>" . chr(13) . chr(10);
            $footerResult .= "<userOrigin x=" . chr(34) . "-12.0" . chr(34) . " y=" . chr(34) . "-12.0" . chr(34) . "/>" . chr(13) . chr(10);
            $footerResult .= "<presentationBackground>#202020</presentationBackground>" . chr(13) . chr(10);
            $footerResult .= "<PathwayList currentPathway=" . chr(34) . "0" . chr(34) . " revealerIndex=" . chr(34) . "-1" . chr(34) . ">" . chr(13) . chr(10);
            $footerResult .= "<pathway ID=" . chr(34) . "1" . chr(34) . " label=" . chr(34) . "Untitled Pathway" . chr(34) . " x=" . chr(34) . "0.0" . chr(34) . " y=" . chr(34) . "0.0" . chr(34) . " width=" . chr(34) . "1.4E-45" . chr(34) . " height=" . chr(34) . "1.4E-45" . chr(34) . " strokeWidth=" . chr(34) . "0.0" . chr(34) . " autoSized=" . chr(34) . "false" . chr(34) . " currentIndex=" . chr(34) . "-1" . chr(34) . " locked=" . chr(34) . "false" . chr(34) . " open=" . chr(34) . "true" . chr(34) . ">" . chr(13) . chr(10);
            $footerResult .= "<strokeColor>#B3993333</strokeColor>" . chr(13) . chr(10);
            $footerResult .= "<textColor>#000000</textColor>" . chr(13) . chr(10);
            $footerResult .= "<font>SansSerif-plain-14</font>" . chr(13) . chr(10);
            $footerResult .= "<metadata-list category-list-size=" . chr(34) . "0" . chr(34) . " other-list-size=" . chr(34) . "0" . chr(34) . " ontology-list-size=" . chr(34) . "0" . chr(34) . " RCategoryListSize=" . chr(34) . "0" . chr(34) . ">" . chr(13) . chr(10);
            $footerResult .= "<ontology-list-string></ontology-list-string>" . chr(13) . chr(10);
            $footerResult .= "</metadata-list>" . chr(13) . chr(10);
            $footerResult .= "<URIString>http://vue.tufts.edu/rdf/resource/8269e795a9fe648b019fe5e698d37a7d</URIString>" . chr(13) . chr(10);
            $footerResult .= "<masterSlide ID=" . chr(34) . "8" . chr(34) . " x=" . chr(34) . "0.0" . chr(34) . " y=" . chr(34) . "0.0" . chr(34) . " width=" . chr(34) . "800.0" . chr(34) . " height=" . chr(34) . "600.0" . chr(34) . " strokeWidth=" . chr(34) . "0.0" . chr(34) . " autoSized=" . chr(34) . "false" . chr(34) . ">" . chr(13) . chr(10);
            $footerResult .= "<fillColor>#000000</fillColor>" . chr(13) . chr(10);
            $footerResult .= "<strokeColor>#404040</strokeColor>" . chr(13) . chr(10);
            $footerResult .= "<textColor>#000000</textColor>" . chr(13) . chr(10);
            $footerResult .= "<font>SansSerif-plain-14</font>" . chr(13) . chr(10);
            $footerResult .= "<metadata-list category-list-size=" . chr(34) . "0" . chr(34) . " other-list-size=" . chr(34) . "0" . chr(34) . " ontology-list-size=" . chr(34) . "0" . chr(34) . " RCategoryListSize=" . chr(34) . "0" . chr(34) . ">" . chr(13) . chr(10);
            $footerResult .= "<ontology-list-string></ontology-list-string>" . chr(13) . chr(10);
            $footerResult .= "</metadata-list>" . chr(13) . chr(10);
            $footerResult .= "<URIString>http://vue.tufts.edu/rdf/resource/8269e7dba9fe648b019fe5e6dcec1bac</URIString>" . chr(13) . chr(10);
            $footerResult .= "<titleStyle ID=" . chr(34) . "9" . chr(34) . " label=" . chr(34) . "Header" . chr(34) . " x=" . chr(34) . "335.5" . chr(34) . " y=" . chr(34) . "172.5" . chr(34) . " width=" . chr(34) . "129.0" . chr(34) . " height=" . chr(34) . "55.0" . chr(34) . " strokeWidth=" . chr(34) . "0.0" . chr(34) . " autoSized=" . chr(34) . "true" . chr(34) . " isStyle=" . chr(34) . "true" . chr(34) . " xsi:type=" . chr(34) . "node" . chr(34) . ">" . chr(13) . chr(10);
            $footerResult .= "<strokeColor>#404040</strokeColor>" . chr(13) . chr(10);
            $footerResult .= "<textColor>#FFFFFF</textColor>" . chr(13) . chr(10);
            $footerResult .= "<font>Gill Sans-plain-36</font>" . chr(13) . chr(10);
            $footerResult .= "<metadata-list category-list-size=" . chr(34) . "0" . chr(34) . " other-list-size=" . chr(34) . "0" . chr(34) . " ontology-list-size=" . chr(34) . "0" . chr(34) . " RCategoryListSize=" . chr(34) . "0" . chr(34) . ">" . chr(13) . chr(10);
            $footerResult .= "<ontology-list-string></ontology-list-string>" . chr(13) . chr(10);
            $footerResult .= "</metadata-list>" . chr(13) . chr(10);
            $footerResult .= "<URIString>http://vue.tufts.edu/rdf/resource/8269e7dba9fe648b019fe5e66aa2accc</URIString>" . chr(13) . chr(10);
            $footerResult .= "<shape xsi:type=" . chr(34) . "rectangle" . chr(34) . "/>" . chr(13) . chr(10);
            $footerResult .= "</titleStyle>" . chr(13) . chr(10);
            $footerResult .= "<textStyle ID=" . chr(34) . "10" . chr(34) . " label=" . chr(34) . "Slide Text" . chr(34) . " x=" . chr(34) . "346.5" . chr(34) . " y=" . chr(34) . "281.5" . chr(34) . " width=" . chr(34) . "107.0" . chr(34) . " height=" . chr(34) . "37.0" . chr(34) . " strokeWidth=" . chr(34) . "0.0" . chr(34) . " autoSized=" . chr(34) . "true" . chr(34) . " isStyle=" . chr(34) . "true" . chr(34) . " xsi:type=" . chr(34) . "node" . chr(34) . ">" . chr(13) . chr(10);
            $footerResult .= "<strokeColor>#404040</strokeColor>" . chr(13) . chr(10);
            $footerResult .= "<textColor>#FFFFFF</textColor>" . chr(13) . chr(10);
            $footerResult .= "<font>Gill Sans-plain-22</font>" . chr(13) . chr(10);
            $footerResult .= "<metadata-list category-list-size=" . chr(34) . "0" . chr(34) . " other-list-size=" . chr(34) . "0" . chr(34) . " ontology-list-size=" . chr(34) . "0" . chr(34) . " RCategoryListSize=" . chr(34) . "0" . chr(34) . ">" . chr(13) . chr(10);
            $footerResult .= "<ontology-list-string></ontology-list-string>" . chr(13) . chr(10);
            $footerResult .= "</metadata-list>" . chr(13) . chr(10);
            $footerResult .= "<URIString>http://vue.tufts.edu/rdf/resource/8269e7dba9fe648b019fe5e64a1d2b24</URIString>" . chr(13) . chr(10);
            $footerResult .= "<shape xsi:type=" . chr(34) . "rectangle" . chr(34) . "/>" . chr(13) . chr(10);
            $footerResult .= "</textStyle>" . chr(13) . chr(10);
            $footerResult .= "<linkStyle ID=" . chr(34) . "11" . chr(34) . " label=" . chr(34) . "Links" . chr(34) . " x=" . chr(34) . "373.5" . chr(34) . " y=" . chr(34) . "384.0" . chr(34) . " width=" . chr(34) . "53.0" . chr(34) . " height=" . chr(34) . "32.0" . chr(34) . " strokeWidth=" . chr(34) . "0.0" . chr(34) . " autoSized=" . chr(34) . "true" . chr(34) . " isStyle=" . chr(34) . "true" . chr(34) . " xsi:type=" . chr(34) . "node" . chr(34) . ">" . chr(13) . chr(10);
            $footerResult .= "<strokeColor>#404040</strokeColor>" . chr(13) . chr(10);
            $footerResult .= "<textColor>#B3BFE3</textColor>" . chr(13) . chr(10);
            $footerResult .= "<font>Gill Sans-plain-18</font>" . chr(13) . chr(10);
            $footerResult .= "<metadata-list category-list-size=" . chr(34) . "0" . chr(34) . " other-list-size=" . chr(34) . "0" . chr(34) . " ontology-list-size=" . chr(34) . "0" . chr(34) . " RCategoryListSize=" . chr(34) . "0" . chr(34) . ">" . chr(13) . chr(10);
            $footerResult .= "<ontology-list-string></ontology-list-string>" . chr(13) . chr(10);
            $footerResult .= "</metadata-list>" . chr(13) . chr(10);
            $footerResult .= "<URIString>http://vue.tufts.edu/rdf/resource/8269e7dba9fe648b019fe5e6375d1c02</URIString>" . chr(13) . chr(10);
            $footerResult .= "<shape xsi:type=" . chr(34) . "rectangle" . chr(34) . "/>" . chr(13) . chr(10);
            $footerResult .= "</linkStyle>" . chr(13) . chr(10);
            $footerResult .= "</masterSlide>" . chr(13) . chr(10);
            $footerResult .= "</pathway>" . chr(13) . chr(10);
            $footerResult .= "</PathwayList>" . chr(13) . chr(10);
            $footerResult .= "<date>2009-09-03</date>" . chr(13) . chr(10);
            $footerResult .= "<mapFilterModel/>" . chr(13) . chr(10);
            $footerResult .= "<modelVersion>4</modelVersion>" . chr(13) . chr(10);
            $footerResult .= "<saveLocation>C:\Documents and Settings\Administrator\Desktop</saveLocation>" . chr(13) . chr(10);
            $footerResult .= "<saveFile>C:\Documents and Settings\Administrator\Desktop\simplevue.vue</saveFile>" . chr(13) . chr(10);
            $footerResult .= "</LW-MAP>" . chr(13) . chr(10);

            $out = $header . $nodeResult . $linksResult . $footerResult;

            $fileName = $_SERVER['DOCUMENT_ROOT'] . '/export/OLVue-export-' . $mapId . '.vue';
            $f = fopen($fileName, 'w') or die("can't create file");
            fwrite($f, $out);
            fclose($f);
        }
    }

    public function importVUE($fileName, $title) {
        $tmpFileName = DOCROOT . '/files/';
        $tmpFileName .= time();
        $tmpFileName .= '.xml';

        $tmpFile = fopen($tmpFileName, 'w');

        $file = fopen($fileName, 'r');
        $buffer = fread($file, filesize($fileName));
        fclose($file);

        $buffer = str_replace('xsi:type', 'childNodeType', $buffer);

        fwrite($tmpFile, $buffer);
        fclose($tmpFile);

        $xml = simplexml_load_file($tmpFileName);

        $mapTitle = $fileName . '-imported';
        if ($title != '') {
            $mapTitle = $title;
        }

        $newMap = DB_ORM::model('map')->createVUEMap($mapTitle, Auth::instance()->get_user()->id);

        foreach ($xml->child as $child) {
            $id1 = $child->ID1;
            $id2 = $child->ID2;
            $rgb = str_replace('#', '0x', $child->fillColor);

            $xLow = $yLow = 10;

            $x = $child['x'] / 2;
            $y = $child['y'] / 2;
            $nodeType = $child['childNodeType'];
            $arrowState = $child['arrowState'];
            $label = $child['label'];

            if ($x > $xLow) {
                $xLow = $x;
            }
            if ($y > $yLow) {
                $yLow = $y;
            }

            if (strlen($label) > 0) {
                $label = str_replace(Chr(34), "&quot;", $label);
            }
            if (strlen($label) > 0) {
                $label = str_replace(Chr(10), " ", $label);
            }
            if (strlen($label) > 0) {
                $label = str_replace(Chr(13), " ", $label);
            }
            if (strlen($label) > 0) {
                $label = str_replace("'", "&#39;", $label);
            }
            if (strlen($label) > 0) {
                $label = str_replace(chr(13) . chr(10), " ", $label);
            }
            if (strpos($label, "O2")) {
                $label = str_replace("O2", "O<sub>2</sub>", $label);
            }
            if (strpos($label, "CO2")) {
                $label = str_replace("CO2", "CO<sub>2</sub>", $label);
            }
            if (strpos($label, "H2O")) {
                $label = str_replace("H2O", "H<sub>2</sub>O", $label);
            }
            if (strpos($label, "K+")) {
                $label = str_replace("K+", "K<sup>+</sup>", $label);
            }
            if (strpos($label, "H+")) {
                $label = str_replace("H+", "H<sup>+</sup>", $label);
            }
            if (strpos($label, "Na+")) {
                $label = str_replace("Na+", "Na<sup>+</sup>", $label);
            }
            if (strpos($label, "Ca++")) {
                $label = str_replace("Ca++", "Ca<sup>++</sup>", $label);
            }

            if ($nodeType == 'node') {
                $label = (String) $label;
                if ($label != '') {
                    $arr = explode(".#xa;\\\---///.#xa;", $label);
                    $nodeTitle = $arr[0];
                    $nodeText = $arr[1];

                    DB_ORM::model('map_node')->createVUENode($newMap, $nodeTitle, $nodeText, $xLow, $yLow, $rgb);
                }
            } else if ($nodeType == 'link') {
                $directional = (int) $arrowState;
                switch ($directional) {
                    case 0:
                        DB_ORM::model('map_node_link')->addVUELink($newMap, (int) $id1, (int) $id2);
                        DB_ORM::model('map_node_link')->addVUELink($newMap, (int) $id2, (int) $id1);
                        break;
                    case 1:
                        DB_ORM::model('map_node_link')->addVUELink($newMap, (int) $id1, (int) $id2);
                        break;
                    case 2:
                        DB_ORM::model('map_node_link')->addVUELink($newMap, (int) $id2, (int) $id1);
                        break;
                }
            }
        }

        unlink($tmpFileName);
    }

    public function action_uploadMVP()
    {
        $lastMapOfCurrentUser = DB_ORM::model('map')->getLastEnabledAndAuthoredMap($this->templateData['user_id']);
        try
        {
            if (isset($_FILES) AND ! empty($_FILES)) {
                set_time_limit(0);
                if (is_uploaded_file($_FILES['filename']['tmp_name']))
                {
                    move_uploaded_file($_FILES['filename']['tmp_name'], DOCROOT.'/files/'.$_FILES['filename']['name']);
                    $fileName = 'files/'.$_FILES['filename']['name'];
                    $data = $this->importMVP(DOCROOT.$fileName);
                }
            }
            Notice::add('Labyrinth <a target="_blank" href="'.URL::base().'labyrinthManager/info/'.$data["id"].'">'.$data["title"].'</a> has been successfully imported.', 'success');
            Request::initial()->redirect(URL::base().'exportImportManager/importMVP');
        }
        catch (Exception $e)
        {
            $message = $e->getMessage();
            Notice::add("Error, labyrinth was not imported correctly.".PHP_EOL.$message, 'error');
            $lastAddedMapOfCurrentUser = DB_ORM::model('map')->getLastEnabledAndAuthoredMap($this->templateData['user_id']);
            if ($lastMapOfCurrentUser != $lastAddedMapOfCurrentUser)
            {
                DB_ORM::model('map')->deleteMap($lastAddedMapOfCurrentUser);
            }
            Request::initial()->redirect(URL::base().'exportImportManager/importMVP');
        }
    }

    public function getIdFromString($string) {
        if (!empty($string)) {
            $array = explode('[@', (string) $string);
            if (isset($array[1])) {
                $string = $array[1];
            } else {
                $string = $array[0];
            }
            preg_match_all('!\d+!', $string, $matches);
            return $matches[0][0];
        } else {
            return 0;
        }
    }

    public function importMVP($file)
    {
        $zip = new ZipArchive;
        $res = $zip->open($file);
        $folderName = 'mvp'.rand().'/';
        if ($res === true)
        {
            $folderPath = DOCROOT.'files/'.$folderName;
            $zip->extractTo($folderPath);
            $zip->close();
            $data = $this->parseMVPFile($folderName);
            $this->deleteDir($folderPath);
        }
        else
        {
            echo 'failed'; //TODO: redirect to error
            Request::initial()->redirect(URL::base());
            return false;
        }

        unlink($file);
        return $data;
    }

    public function parseMVPFile($mvpFolder)
    {
        $version     = null;
        $tmpFolder   = DOCROOT.'files/'.$mvpFolder;
        $tmpFileName = $tmpFolder.'/metadata.xml';
        $xml         = $this->parseXML($tmpFileName);

        if (isset($xml->metadata->version))
        {
            ImportExport_Manager::getFormatSystem('MVP')->import($tmpFolder);
            return true;
        }

        if (isset($xml->general->identifier->catalog) && $xml->general->identifier->catalog == 'vpSim')
        {
            ImportExport_Manager::getFormatSystem('MVPvpSim')->import(array(
                'tmpFolder'        => $tmpFolder,
                'filesFolder'      => DOCROOT.'files',
                'filesShortFolder' => 'files'
            ));
            return true;
        }

        if (isset($xml->general->version)) $version = (string)$xml->general->version;

        $findElement        = array();
        $replaceElement     = array();
        $map                = array();
        $map['title']       =(string) $xml->general->title->string;
        $map['title']       = preg_replace_callback('~&#([0-9a-fA-F]+)~i', array($this,"qm_fix_callback"), $map['title']);
        $map['title']       = $this->html_entity_decode_numeric($map['title']);
        $map['author']      = Auth::instance()->get_user()->id;
        $map['language']    = (string) $xml->general->language;
        $map['language']    = ($map['language'] == 'en') ? 1 : 2;
        $map['description'] = (string) $xml->general->description->string;
        $map['keywords']    = (string) $xml->general->keyword->string;
        $map['section']     = 2;
        $map                = DB_ORM::model('map')->createMap($map, false);
        $tmpFileName        = $tmpFolder.'/imsmanifest.xml';
        $xml                = $this->parseXML($tmpFileName);
        $elements           = array();

        if (isset($xml->resources->resource)) {
            if (!file_exists(DOCROOT.'/files/'.$map->id)) {
                mkdir(DOCROOT.'/files/'.$map->id, 0777, true);
            }
            foreach ($xml->resources->resource as $resource) {
                $attr = $resource->attributes();
                if (!strstr($attr->href, 'xml')) {
                    $id = (string) $attr->identifier;
                    $elements[$id]['href'] = (string) $attr->href;
                    $fileName = $this->endc(explode('/', (string) $attr->href));
                    if (file_exists($tmpFolder . (string) $attr->href)){
                        copy($tmpFolder . (string) $attr->href, DOCROOT . '/files/'.$map->id.'/' . $fileName);
                        $values['path'] = 'files/'.$map->id.'/' . $fileName;
                        $values['name'] = $fileName;

                        $elementDB = DB_ORM::model('map_element')->saveElement($map->id, $values);
                        $elements[$id]['database_id'] = $elementDB->id;
                        $findElement[] = '[[MR:' . $id . ']]';
                        $replaceElement[] = '[[MR:' . $elementDB->id . ']]';
                    }
                }
            }
        }

        $tmpFileName = $tmpFolder . '/activitymodel.xml';
        $xml = $this->parseXML($tmpFileName);
        $countersArray = array();
        if (isset($xml->Properties->Counters->Counter)) {
            foreach ($xml->Properties->Counters->Counter as $counter) {
                $attr = $counter->attributes();
                $id = (int) $attr->id;
                $countersArray[$id]['isVisible'] = (int) $attr->isVisible;
                $countersArray[$id]['label'] = (string) $counter->CounterLabel;
                $countersArray[$id]['initValue'] = (string) $counter->CounterInitValue;
                $values['cName'] = (string) $counter->CounterLabel;
                $values['cDesc'] = '';
                $values['cIconId'] = NULL;
                $values['cStartV'] = (string) $counter->CounterInitValue;
                if ((string) $attr->isVisible == 'true') {
                    $values['cVisible'] = 1;
                } else {
                    $values['cVisible'] = 0;
                }

                $counterDB = DB_ORM::model('map_counter')->addCounter($map->id, $values);
                $countersArray[$id]['database_id'] = $counterDB->id;

                $rules = array();
                $i = 0;
                if (isset($counter->CounterRules)) {
                    foreach ($counter->CounterRules as $rule) {
                        switch ((string) $rule->Rule->Relation) {
                            case 'eq':
                                $rules[$i]['relation'] = 1;
                                break;
                            case 'neq':
                                $rules[$i]['relation'] = 2;
                                break;
                            case 'leq':
                                $rules[$i]['relation'] = 3;
                                break;
                            case 'lt':
                                $rules[$i]['relation'] = 4;
                                break;
                            case 'geq':
                                $rules[$i]['relation'] = 5;
                                break;
                            case 'gt':
                                $rules[$i]['relation'] = 6;
                                break;
                            default:
                                $rules[$i]['relation'] = 1;
                        }
                        $rules[$i]['rulevalue'] = (string) $rule->Rule->Value;
                        $rules[$i]['node'] = (int) $this->getIdFromString($rule->Rule->RuleRedirect);
                        $i++;
                    }
                    $countersArray[$id]['rules'] = $rules;
                }
            }
        }

        $nodeSection = array();
        $nodeArray = array();
        if (isset($xml->ActivityNodes->NodeSection)) {
            foreach ($xml->ActivityNodes->NodeSection as $section) {
                $attr = $section->attributes();
                $label = (string) $attr->label;
                if ($label != 'unallocated') {
                    $id = (int) $attr->id;
                    $nodeSection[$id]['sectionname'] = html_entity_decode((string) $attr->label);

                    $activityNode = array();
                    $i = 0;
                    if (isset($section->ActivityNode)) {
                        foreach ($section->ActivityNode as $node) {
                            $activityNode[$i]['node_id'] = $this->getIdFromString($node->Content);
                            $i++;
                        }
                        $nodeSection[$id]['activityNode'] = $activityNode;
                    }
                } else {
                    if (isset($section->ActivityNode)) {
                        foreach ($section->ActivityNode as $node) {
                            $nodeAttr = $node->attributes();
                            $id = (int) preg_replace('/\D+/', '', $nodeAttr->id);
                            $nodeArray[$id]['title'] = $this->html_entity_decode_numeric(((string) $nodeAttr->label)) ;

                            $nodeArray[$id]['text'] = $this->getIdFromString($node->Content);
                            $nodeArray[$id]['rules_probability'] = (string) $node->Rules->Probability;
                            $nodeArray[$id]['rules_navigateGlobal'] = (string) $node->Rules->NavigateGlobal;

                            if (isset($node->Rules->CounterActionRule)) {
                                $i = 0;
                                $ruleArray = array();
                                foreach ($node->Rules->CounterActionRule as $rule) {
                                    $ruleArray[$i]['function'] = (string) $rule->CounterOperator . (string) $rule->CounterRuleValue;
                                    $ruleArray[$i]['counter_id'] = $countersArray[(int) $this->getIdFromString((string) $rule->CounterPath)]['database_id'];
                                    $i++;
                                }
                                $nodeArray[$id]['rules'] = $ruleArray;
                            }
                        }
                    }
                }
            }
        }

        $linksArray = array();
        $i = 0;
        if (isset($xml->Links->Link)) {
            foreach ($xml->Links->Link as $link) {
                $linkAttr = $link->attributes();
                $linksArray[$i]['text'] = html_entity_decode((string) $linkAttr->label);
                $linksArray[$i]['display'] = (int) $linkAttr->display;

                $linksArray[$i]['node_id_1'] = $this->getIdFromString($link->ActivityNodeA);
                $linksArray[$i]['node_id_2'] = $this->getIdFromString($link->ActivityNodeB);
                $i++;
            }
        }

        if (isset($xml->XtensibleInfo->OL_xtensible->OL_node)) {
            foreach ($xml->XtensibleInfo->OL_xtensible->OL_node as $node) {
                $nodeAttr = $node->attributes();
                $id = (int) $nodeAttr->ID;
                if (isset($nodeArray[$id])) {
                    if ((string) $nodeAttr->undoLinks == 'y') {
                        $nodeArray[$id]['undo'] = 1;
                    } else {
                        $nodeArray[$id]['undo'] = 0;
                    }

                    if ((string) $nodeAttr->nodeProbs == 'y') {
                        $nodeArray[$id]['probability'] = 1;
                    } else {
                        $nodeArray[$id]['probability'] = 0;
                    }

                    $nodeArray[$id]['priority_id'] = 1;
                    if ((string) $nodeAttr->nodePriority == 'neg') {
                        $nodeArray[$id]['priority_id'] = 2;
                    } elseif ((string) $nodeAttr->nodePriority == 'pos') {
                        $nodeArray[$id]['priority_id'] = 3;
                    }

                    switch ((string) $nodeAttr->linkSorting) {
                        case 'o':
                            $nodeArray[$id]['link_type_id'] = 1;
                            break;
                        case 'r':
                            $nodeArray[$id]['link_type_id'] = 2;
                            break;
                        case '1':
                            $nodeArray[$id]['link_type_id'] = 3;
                            break;
                        default:
                            $nodeArray[$id]['link_type_id'] = 2;
                    }

                    switch ((string) $nodeAttr->linkPresentation) {
                        case 'drop':
                            $nodeArray[$id]['link_style_id'] = 2;
                            break;
                        case 'conf':
                            $nodeArray[$id]['link_style_id'] = 3;
                            break;
                        case 'fill':
                            $nodeArray[$id]['link_style_id'] = 4;
                            break;
                        default:
                            $nodeArray[$id]['link_style_id'] = 1;
                    }


                    if ((string) $nodeAttr->mnodeType == 'root') {
                        $nodeArray[$id]['type_id'] = 1;
                        $nodeArray[$id]['end'] = 0;
                    } elseif ((string) $nodeAttr->mnodeType == 'child') {
                        $nodeArray[$id]['type_id'] = 2;
                        $nodeArray[$id]['end'] = 0;
                    } elseif ((string) $nodeAttr->mnodeType == 'end') {
                        $nodeArray[$id]['type_id'] = 2;
                        $nodeArray[$id]['end'] = 1;
                    }
                    $nodeArray[$id]['x'] = (string) $nodeAttr->mnodeX;
                    $nodeArray[$id]['y'] = (string) $nodeAttr->mnodeY;
                    $nodeArray[$id]['rgb'] = (string) $nodeAttr->mnodeRGB;
                    $nodeArray[$id]['info'] = html_entity_decode((string) $node->OL_infoText->div);

                }
            }
        }

        $tmpFileName = $tmpFolder . '/virtualpatientdata.xml';
        $xml = $this->parseXML($tmpFileName);

        $avatarsArray = array();
        if (isset($xml->XtensibleInfo->OL_xtensible->OL_avatars->OL_avatar)) {
            foreach ($xml->XtensibleInfo->OL_xtensible->OL_avatars->OL_avatar as $avatar) {
                $avatarAttr = $avatar->attributes();
                $id = (int) $avatarAttr->ID;
                $avatarsArray[$id]['avskin1'] = (string) $avatarAttr->AvatarSkin1;
                $avatarsArray[$id]['avskin2'] = (string) $avatarAttr->AvatarSkin2;
                $avatarsArray[$id]['avcloth'] = (string) $avatarAttr->AvatarCloth;

                $array = array('' => '', 'A' => 'nostrils', 'B' => 'petit', 'C' => 'wide');
                $avatarsArray[$id]['avnose'] = $array[(string) $avatarAttr->AvatarNose];

                $array = array('' => '', 'A' => 'none', 'B' => 'shaved', 'C' => 'longblonde', 'D' => 'short', 'E' => 'curly', 'F' => 'bob', 'G' => 'longred', 'H' => 'grandpa', 'I' => 'granny', 'K' => 'youngman', 'L' => 'long');
                $avatarsArray[$id]['avhair'] = $array[(string) $avatarAttr->AvatarHair];

                $array = array('' => '', 'A' => 'none', 'B' => 'ambulancebay', 'F' => 'bedpillow', 'G' => 'hospital', 'H' => 'waitingroom', 'J' => 'insideambulance', 'O' => 'xray', 'R' => 'ca', 'S' => 'medivachelicopter', 'V' => 'heartmonitor', 'BB' => 'stopsign', 'CC' => 'bedside', 'DD' => 'ambulance2', 'FF' => 'machine', 'D' => 'livingroom', 'K' => 'basicoffice', 'N' => 'basicroom', 'Y' => 'corridor', 'AA' => 'room', 'GG' => 'pillowb', 'JJ' => 'concourse', 'KK' => 'officecubicle', 'C' => 'residentialstreet', 'E' => 'highstreet', 'I' => 'cityskyline', 'L' => 'lakeside', 'M' => 'suburbs', 'T' => 'summer', 'U' => 'longroad', 'P' => 'downtown', 'Q' => 'winter', 'W' => 'outsidelake', 'X' => 'field', 'Z' => 'roadside', 'HH' => 'forestriver', 'II' => 'parkinglot', 'BB' => 'stopsign', 'EE' => 'yieldsign');
                $avatarsArray[$id]['avenvironment'] = $array[(string) $avatarAttr->AvatarEnvironment];

                $avatarsArray[$id]['avbkd'] = (string) $avatarAttr->AvatarBkd;

                $array = array('' => '', 'A' => 'male', 'B' => 'female');
                $avatarsArray[$id]['avsex'] = $array[(string) $avatarAttr->AvatarSex];

                $array = array('' => '', 'A' => 'smile', 'B' => 'indifferent', 'C' => 'frown');
                $avatarsArray[$id]['avmouth'] = $array[(string) $avatarAttr->AvatarMouth];

                $array = array('' => '', 'A' => 'none', 'B' => 'woolyjumper', 'C' => 'shirtandtie', 'D' => 'nurse', 'E' => 'scrubs_blue', 'F' => 'scrubs_green', 'G' => 'vest', 'H' => 'gown', 'I' => 'pyjamas_female', 'J' => 'pyjamas_male', 'K' => 'doctor_male', 'L' => 'doctor_female', 'M' => 'neck', 'N' => 'blackshirt', 'O' => 'winterjacket', 'P' => 'vneck', 'Q' => 'fleece', 'R' => 'sweater');
                $avatarsArray[$id]['avoutfit'] = $array[(string) $avatarAttr->AvatarOutfit];

                $array = array('' => '', 'A' => 'none', 'B' => 'glasses', 'T' => 'sunglasses', 'C' => 'bindi', 'D' => 'moustache', 'E' => 'freckles', 'G' => 'blusher', 'H' => 'earrings', 'I' => 'beads', 'J' => 'neckerchief', 'V' => 'redscarf', 'Y' => 'beanie', 'AA' => 'buttonscarf', 'CC' => 'baseballcap', 'DD' => 'winterhat', 'F' => 'mask', 'K' => 'stethoscope', 'L' => 'oxygenmask', 'M' => 'surgeoncap', 'N' => 'eyepatch', 'O' => 'scratches', 'P' => 'splitlip', 'Q' => 'blackeyeleft', 'R' => 'blackeyeright', 'S' => 'headbandage', 'T' => 'sunglasses', 'U' => 'neckbrace', 'W' => 'tearssmall', 'BB' => 'tearslarge', 'X' => 'sweat');
                $avatarsArray[$id]['avaccessory1'] = $array[(string) $avatarAttr->AvatarAccessory1];
                $avatarsArray[$id]['avaccessory2'] = $array[(string) $avatarAttr->AvatarAccessory2];
                $avatarsArray[$id]['avaccessory3'] = $array[(string) $avatarAttr->AvatarAccessory3];

                $array = array('' => '', 'A' => '20', 'B' => '40', 'C' => '60');
                $avatarsArray[$id]['avage'] = $array[(string) $avatarAttr->AvatarAge];

                $array = array('' => '', 'A' => 'open', 'B' => 'close');
                $avatarsArray[$id]['aveyes'] = $array[(string) $avatarAttr->AvatarEyes];

                $avatarsArray[$id]['avhaircolor'] = (string) $avatarAttr->AvatarHairColor;

                $array = array('' => '', 'A' => 'none', 'B' => 'normal', 'C' => 'think', 'D' => 'shout');
                $avatarsArray[$id]['avbubble'] = $array[(string) $avatarAttr->AvatarBubble];
                $avatarsArray[$id]['avbubbletext'] = (string) $avatar->OL_AvatarBubbleText->div;

                if (isset($avatarAttr->AvatarImage)){
                    $avatarImageName = (string) $avatarAttr->AvatarImage;
                    if($avatarImageName != 'ntr') {
                        if($avatarImageName != '') {
                            $avatarsArray[$id]['image_data'] = $avatarImageName;
                            $avatarFile = $tmpFolder.'media/'.$avatarImageName;
                            if (file_exists($avatarFile)){
                                copy($avatarFile, DOCROOT . '/avatars/' . $avatarImageName);
                            }
                        }else {
                            $avatarsArray[$id]['image_data'] = '';
                        }
                    } else {
                        $avatarsArray[$id]['image_data'] = 'ntr';
                    }
                } else {
                    $avatarsArray[$id]['image_data'] = 'ntr'; //need to reload
                }

                DB_ORM::model('map_avatar')->addAvatar($map->id);
                $avatarDB = DB_ORM::model('map_avatar')->getLastAddedAvatar($map->id);
                DB_ORM::model('map_avatar')->updateAvatar($avatarDB->id, $avatarsArray[$id]);
                $avatarsArray[$id]['database_id'] = $avatarDB->id;
                $findElement[] = '[[AV:' . $id . ']]';
                $replaceElement[] = '[[AV:' . $avatarDB->id . ']]';
            }
        }

        $questionsArray = array();
        if (isset($xml->XtensibleInfo->OL_xtensible->OL_questions->OL_question)) {
            foreach ($xml->XtensibleInfo->OL_xtensible->OL_questions->OL_question as $question) {
                $hasAnswers = true;
                $questionAttr = $question->attributes();
                $id = (int) $questionAttr->ID;
                if ((string) $questionAttr->NumTries == 1) {
                    $questionsArray[$id]['num_tries'] = 1;
                } else {
                    $questionsArray[$id]['num_tries'] = -1;
                }

                switch ((string) $questionAttr->QuestionEntryType) {
                    case 'text':
                        $questionsArray[$id]['entry_type_id'] = 1;
                        $questionsArray[$id]['num_tries'] = 0;
                        $hasAnswers = false;
                        break;
                    case 'area':
                        $questionsArray[$id]['entry_type_id'] = 2;
                        $questionsArray[$id]['num_tries'] = 0;
                        $hasAnswers = false;
                        break;
                    case 'mcq2':
                        $questionsArray[$id]['entry_type_id'] = 4;
                        break;
                    case 'mcq3':
                        $questionsArray[$id]['entry_type_id'] = 4;
                        break;
                    case 'mcq5':
                        $questionsArray[$id]['entry_type_id'] = 4;
                        break;
                    case 'mcq9':
                        $questionsArray[$id]['entry_type_id'] = 4;
                        break;
                    default:
                        $questionsArray[$id]['entry_type_id'] = 1;
                }

                $questionsArray[$id]['width'] = (string) $questionAttr->QuestionWidth;
                $questionsArray[$id]['height'] = (string) $questionAttr->QuestionHeight;
                $questionsArray[$id]['feedback'] = (string) $questionAttr->Feedback;
                $questionsArray[$id]['show_answer'] = (string) $questionAttr->ShowAnswer;
                $scoreCounter = (string) $questionAttr->ScoreCounter;
                if (!empty($scoreCounter)) {
                    if (isset($countersArray[$scoreCounter])) {
                        $questionsArray[$id]['counter_id'] = $countersArray[$scoreCounter]['database_id'];
                    }
                }

                $questionsArray[$id]['stem'] = (string) $question->OL_QuestionStem->div;

                $questionDB = DB_ORM::model('map_question')->addFullQuestion($map->id, $questionsArray[$id]);
                $questionsArray[$id]['database_id'] = $questionDB->id;
                $findElement[] = '[[QU:' . $id . ']]';
                $replaceElement[] = '[[QU:' . $questionDB->id . ']]';

                if ($hasAnswers) {
                    $questionResponces = array();
                    $j = 0;
                    for ($i = 0; $i < 10; $i++) {
                        $t = 'Resp' . $i . 't';
                        $y = 'Resp' . $i . 'y';
                        $s = 'Resp' . $i . 's';
                        $f = 'Resp' . $i . 'f';
                        if (!empty($question->$t->div)) {
                            $questionResponces[$j]['response'] = (string) $question->$t->div;
                            $questionResponces[$j]['feedback'] = (string) $question->$f->div;
                            if ((string) $question->$y->div == 'c') {
                                $questionResponces[$j]['is_correct'] = 1;
                            } else {
                                $questionResponces[$j]['is_correct'] = 0;
                            }

                            $questionResponces[$j]['score'] = (string) $question->$s->div;
                            DB_ORM::model('map_question_response')->addFullResponses($questionDB->id, $questionResponces[$j]);
                            $j++;
                        }
                    }
                    $questionsArray[$id]['responces'] = $questionResponces;
                }
            }
        }

        $elementsArray = array();
        if (isset($xml->PatientDemographics)) {
            foreach ($xml->PatientDemographics as $patien) {
                $patienAttr = $patien->attributes();
                $id = (int) $patienAttr->id;
                if (isset($patien->DemographicCharacteristic)) {
                    $elementsArray[$id]['type'] = 2;
                    $elementsArray[$id]['DemogTitle'] = (string) $patien->DemographicCharacteristic->Title;
                    $elementsArray[$id]['DemogDesc'] = (string) $patien->DemographicCharacteristic->Description;
                }
                if (isset($patien->CoreDemographics)) {
                    $arrayKeys = array_keys((array) $patien->CoreDemographics);
                    $elementsArray[$id]['type'] = 2;
                    $elementsArray[$id]['CoreDemogType'] = (string) $arrayKeys[1];
                    $elementsArray[$id]['DemogText'] = (string) $patien->CoreDemographics->$arrayKeys[1];
                }
            }
        }

        if (isset($xml->Medication)) {
            foreach ($xml->Medication as $medication) {
                $medicationAttr = $medication->attributes();
                $id = (int) $medicationAttr->id;
                $elementsArray[$id]['type'] = 4;
                $elementsArray[$id]['MedicTitle'] = (string) $medication->MedicationName;
                $elementsArray[$id]['MedicDose'] = (string) $medication->Dose;
                $elementsArray[$id]['MedicRoute'] = (string) $medication->Route;
                $elementsArray[$id]['MedicFreq'] = (string) $medication->Frequency;
                $nameAttr = $medication->MedicationName->attributes();
                $elementsArray[$id]['MedicSource'] = (string) $nameAttr->source;
                $elementsArray[$id]['MedicSourceID'] = (string) $nameAttr->sourceID;
            }
        }

        if (isset($xml->InterviewItem)) {
            foreach ($xml->InterviewItem as $interview) {
                $interviewAttr = $interview->attributes();
                $id = (int) $interviewAttr->id;
                $elementsArray[$id]['type'] = 5;
                $elementsArray[$id]['QAQuestion'] = (string) $interview->Question;
                $elementsArray[$id]['QAAnswer'] = (string) $interview->Response;
                $elementsArray[$id]['QAMedia'] = '';
                $elementsArray[$id]['trigger'] = 'on';
            }
        }

        if (isset($xml->PhysicalExam)) {
            foreach ($xml->PhysicalExam as $physical) {
                $physicalAttr = $physical->attributes();
                $id = (int) $physicalAttr->id;
                $elementsArray[$id]['type'] = 6;
                $elementsArray[$id]['ExamName'] = (string) $physical->ExamName;
                $elementsArray[$id]['ExamDesc'] = '';
                $elementsArray[$id]['BodyPart'] = (string) $physical->LocationOnBody->BodyPart;
                $elementsArray[$id]['Action'] = (string) $physical->Action;
                $elementsArray[$id]['ProxDist'] = (string) $physical->LocationOnBody->ProximalOrDistal;
                $elementsArray[$id]['RightLeft'] = (string) $physical->LocationOnBody->RightOrLeft;
                $elementsArray[$id]['FrontBack'] = (string) $physical->LocationOnBody->FrontOrBack;
                $elementsArray[$id]['InfSup'] = (string) $physical->LocationOnBody->InferiorOrSuperior;
                $elementsArray[$id]['FindName'] = (string) $physical->Finding;
                $elementsArray[$id]['FindDesc'] = (string) $physical->Description;
                $elementsArray[$id]['FindMedia'] = '';
            }
        }

        if (isset($xml->Intervention)) {
            foreach ($xml->Intervention as $intervention) {
                $interventionAttr = $intervention->attributes();
                $id = (int) $interventionAttr->id;
                $elementsArray[$id]['type'] = 9;
                $name = explode(' - ', (string) $intervention->InterventionName);
                $elementsArray[$id]['IntervTitle'] = $name[0];
                $elementsArray[$id]['IntervDesc'] = $name[1];
                $elementsArray[$id]['iMedicTitle'] = (string) $intervention->Medication->MedicationName;
                $elementsArray[$id]['iMedicDose'] = (string) $intervention->Medication->Dose;
                $elementsArray[$id]['iMedicRoute'] = (string) $intervention->Medication->Route;
                $elementsArray[$id]['iMedicFreq'] = (string) $intervention->Medication->Frequency;
                $attr = $intervention->Medication->attributes();
                $elementsArray[$id]['iMedicSource'] = (string) $attr->source;
                $elementsArray[$id]['iMedicSourceID'] = (string) $attr->sourceID;
                $elementsArray[$id]['Appropriateness'] = (string) $intervention->Appropriateness;
                $elementsArray[$id]['ResultTitle'] = (string) $intervention->Results;
                $elementsArray[$id]['iTestMedia'] = '';
            }
        }

        $nodeContentsArray = array();
        if (isset($xml->VPDText)) {
            foreach ($xml->VPDText as $vpdText) {
                $vpdTextAttr = $vpdText->attributes();
                if (strstr((string) $vpdTextAttr->id, 'NGR')) {
                    $id = 'ctt_' . $this->getIdFromString($vpdTextAttr->id);
                    if($version == '3') {
                        $nodeContentsArray[$id]['div'] = (string) base64_decode($vpdText->div);
                    }else {
                        $nodeContentsArray[$id]['div'] = $vpdText->div->asXML();
                    }
                } else {
                    $id = (int) $vpdTextAttr->id;
                    $elementsArray[$id]['type'] = 1;
                    $elementsArray[$id]['VPDTextType'] = (string) $vpdTextAttr->textType;
                    $elementsArray[$id]['VPDText'] = (string) $vpdText;
                }
            }
        }

        if (count($elementsArray) > 0) {
            foreach ($elementsArray as $id => $element) {
                $typeId = $element['type'];
                unset($element['type']);
                $elementId = DB_ORM::model('map_vpd')->createNewElementTypeId($map->id, $typeId, $element);
                $findElement[] = '[[VPD:' . $id . ']]';
                $replaceElement[] = '[[VPD:' . $elementId . ']]';
            }
        }

        if (count($nodeArray) > 0) {
            $config = array(
                'indent'         => true,
                'output-xhtml'   => true,
                'clean'          => true,
                'wrap'           => 200);
            if(extension_loaded('tidy'))$tidy = new tidy;
            foreach ($nodeArray as $key => $node) {
                $id = 'ctt_' . $node['text'];
                if (isset($nodeContentsArray[$id]['div'])) {
                    $string = html_entity_decode((string) $nodeContentsArray[$id]['div']);
                    $nodeArray[$key]['text'] = str_replace($findElement, $replaceElement, $string);
                    $nodeArray[$key]['text'] = $this->html_entity_decode_numeric($nodeArray[$key]['text']);
                    $nodeArray[$key]['info'] = str_replace($findElement, $replaceElement, $nodeArray[$key]['info'] );
                    $nodeArray[$key]['info'] = $this->html_entity_decode_numeric($nodeArray[$key]['info']);



                    if(extension_loaded('tidy')){
                    // Specify configuration
                        $nodeArray[$key]['text']= $tidy->repairString($nodeArray[$key]['text'], $config, 'utf8');
                        $nodeArray[$key]['info']= $tidy->repairString($nodeArray[$key]['info'], $config, 'utf8');
                    }

                } else {
                    $nodeArray[$key]['text'] = '';
                }
            }
        }

        if (count($nodeArray) > 0) {
            foreach ($nodeArray as $key => $node) {
                $nodeDB = DB_ORM::model('map_node')->createFullNode($map->id, $node);
                $nodeArray[$key]['database_id'] = $nodeDB->id;
                if (isset($node['rules'])) {
                    foreach ($node['rules'] as $rule) {
                        DB_ORM::model('map_node_counter')->addNodeCounter($nodeDB->id, $rule['counter_id'], $rule['function']);
                    }
                }
            }
        }

        if (count($linksArray) > 0) {
            foreach ($linksArray as $link) {
                $link['node_id_1'] = $nodeArray[$link['node_id_1']]['database_id'];
                $link['node_id_2'] = $nodeArray[$link['node_id_2']]['database_id'];
                DB_ORM::model('map_node_link')->addFullLink($map->id, $link);
            }
        }

        if (count($nodeSection) > 0) {
            $values = array();
            foreach ($nodeSection as $section) {
                $values['sectionname'] = $section['sectionname'];
                $sectionDB = DB_ORM::model('map_node_section')->createSection($map->id, $values);
                if (count($section['activityNode']) > 0) {
                    foreach ($section['activityNode'] as $activeNode) {
                        $node_id = $nodeArray[$activeNode['node_id']]['database_id'];
                        DB_ORM::model('map_node_section_node')->addNode($node_id, $sectionDB->id);
                    }
                }
            }
        }

        if (count($countersArray) > 0) {
            foreach ($countersArray as $counter) {
                if (isset($counter['rules'])) {
                    foreach ($counter['rules'] as $rules) {
                        if ($rules['node'] != ''){
                            $rules['node'] = $nodeArray[$rules['node']]['database_id'];
                        } else {
                            $rules['node'] = '';
                        }
                        DB_ORM::model('map_counter_rule')->addRule($counter['database_id'], $rules);
                    }
                }
            }
        }
        return array("title"=>$map->name, "id"=>$map->id);
    }

    public function parseXML($tmpFileName)
    {
        $content        = file_get_contents($tmpFileName);
        $searchArray    = array('<ol:', '</ol:', '&#034;');
        $replaceArray   = array('<', '</', "'");
        $xmlString      = str_replace($searchArray, $replaceArray, $content);
        $xmlString      = str_replace(array("&amp;", "&"), array("&", "&amp;"), $xmlString);

        return simplexml_load_string($xmlString);
    }

    private function endc($array) {
        return end($array);
    }

    private function deleteDir($dirPath) {
        if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
            $dirPath .= '/';
        }
        $files = glob($dirPath . '*', GLOB_MARK);
        foreach ($files as $file) {
            if (is_dir($file)) {
                self::deleteDir($file);
            } else {
                unlink($file);
            }
        }
        //rmdir($dirPath);
    }


    private function html_entity_decode_numeric($string, $quote_style = ENT_COMPAT, $charset = "utf-8")
    {
        $string = html_entity_decode($string, $quote_style, $charset);
        $string = preg_replace_callback('~&#x([0-9a-fA-F]+);~i', array($this,"chr_utf8_callback"), $string);
          $string = html_entity_decode($string, $quote_style, $charset);
        $string = preg_replace('~&#([0-9]+);~e', $this->chr_utf8("\\1"), $string);
        return $string;
    }


    /**
     * Callback helper
     */

    private function qm_fix_callback($matches)
    {

        return $matches[0].';';
    }

    /**
     * Callback helper
     */

    private function chr_utf8_callback($matches)
    {
        return $this->chr_utf8(hexdec($matches[1]));
    }

    /**
     * Multi-byte chr(): Will turn a numeric argument into a UTF-8 string.
     *
     * @param mixed $num
     * @return string
     */

    private function chr_utf8($num)
    {
        if ($num < 128) return chr($num);
        if ($num < 2048) return chr(($num >> 6) + 192) . chr(($num & 63) + 128);
        if ($num < 65536) return chr(($num >> 12) + 224) . chr((($num >> 6) & 63) + 128) . chr(($num & 63) + 128);
        if ($num < 2097152) return chr(($num >> 18) + 240) . chr((($num >> 12) & 63) + 128) . chr((($num >> 6) & 63) + 128) . chr(($num & 63) + 128);
        return '';
    }

}