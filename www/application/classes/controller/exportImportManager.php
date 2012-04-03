<?php defined('SYSPATH') or die('No direct script access.');

class Controller_ExportImportManager extends Controller_Base {

    public function action_index() {
        $this->exportVUE($this->request->param('id', NULL));
    }

    public function exportVUE($mapId) {
        if ($mapId != NULL) {
            $header = '';
            $header .= "<!-- Tufts VUE 2.2.8 concept-map (simplevue.vue) 2009-09-03 -->".chr(13).chr(10);
            $header .= "<!-- Tufts VUE: http://vue.tufts.edu/ -->".chr(13).chr(10);
            $header .= "<!-- Do Not Remove: VUE mapping @version(1.1) jar:file:/C:/Program%20Files/VUE/VUE.jar!/tufts/vue/resources/lw_mapping_1_1.xml -->".chr(13).chr(10);
            $header .= "<!-- Do Not Remove: Saved date " . date('Y.m.d H:i:s', time()) . " by superuser on platform OpenLabyrinth -->".chr(13).chr(10);
            $header .= "<!-- Do Not Remove: Saving version @(#)VUE: built July 2 2008 at 1658 by vue on Linux 2.4.21-53.EL i386 JVM 1.5.0_06-b05 -->".chr(13).chr(10);
            $header .= "<?xml version='1.0' encoding='US-ASCII'?>".chr(13).chr(10);
            $header .= "<LW-MAP xmlns:xsi='http://www.w3.org/2001/XMLSchema-instance' xsi:noNamespaceSchemaLocation='none' ID='0' label='simplevue.vue' x='0.0' y='0.0' width='1.4E-45' height='1.4E-45' strokeWidth='0.0' autoSized='false'>".chr(13).chr(10);
            $header .= "<fillColor>#FFFFFF</fillColor>".chr(13).chr(10);
            $header .= "<strokeColor>#404040</strokeColor>".chr(13).chr(10);
            $header .= "<textColor>#000000</textColor>".chr(13).chr(10);
            $header .= "<font>SansSerif-plain-14</font>".chr(13).chr(10);
            $header .= "<metadata-list category-list-size='2' other-list-size='0' ontology-list-size='0' RCategoryListSize='0'>".chr(13).chr(10);
            $header .= "<ontology-list-string></ontology-list-string>".chr(13).chr(10);
            $header .= "<metadata xsi:type='vue-metadata-element'>".chr(13).chr(10);
            $header .= "<value></value>".chr(13).chr(10);
            $header .= "<key>http://vue.tufts.edu/vue.rdfs#none</key>".chr(13).chr(10);
            $header .= "<type>1</type>".chr(13).chr(10);
            $header .= "</metadata>".chr(13).chr(10);
            $header .= "<metadata xsi:type='vue-metadata-element'>".chr(13).chr(10);
            $header .= "<value>" . $mapId . "</value>".chr(13).chr(10);
            $header .= "<key>http://vue.tufts.edu/custom.rdfs#OLmapID</key>".chr(13).chr(10);
            $header .= "<type>1</type>".chr(13).chr(10);
            $header .= "</metadata>".chr(13).chr(10);
            $header .= "</metadata-list>".chr(13).chr(10);
            $header .= "<URIString>http://vue.tufts.edu/rdf/resource/8269e781a9fe648b019fe5e694210f17</URIString>".chr(13).chr(10);

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

                    $mnbody = '';
                    while (strlen($text) > 70) {
                        $a = subtr($text, 0, 70);
                        $text = str_replace(a, "", $text);
                        $mnbody .= $a;

                        while ((subtr($text, 0, 1) != " ") and (strlen($text) > 1)) {
                            $h = subtr($text, 0, 1);
                            $mnbody .= $h;
                            $text = sustr($text, 1, strlen($text));
                        }
                        $mnbody .= ".#xa;".chr(13).chr(10);
                    }

                    $mnbody .= $text;

                    $nodeResult .= "<child ID=" . chr(34) . $node->id . chr(34) . " label=" . chr(34) . $node->title . ".#xa;\\\---///.#xa;" . $mnbody . chr(34) . " x=" . chr(34) . ($node->x * 2) . chr(34) . " y=" . chr(34) . ($node->y * 2) . chr(34) . " width=" . chr(34) . "150.0" . chr(34) . " height=" . chr(34) . "100.0" . chr(34) . " strokeWidth=" . chr(34) . "1.0" . chr(34) . " autoSized=" . chr(34) . "false" . chr(34) . " xsi:type=" . chr(34) . "node" . chr(34) . ">".chr(13).chr(10);
                    $nodeResult .= "<fillColor>" . str_replace('0x', '#', $node->rgb) . "</fillColor>".chr(13).chr(10);
                    $nodeResult .= "<strokeColor>#333333</strokeColor>".chr(13).chr(10);
                    $nodeResult .= "<textColor>#000000</textColor>".chr(13).chr(10);
                    $nodeResult .= "<font>Arial-plain-12</font>".chr(13).chr(10);
                    $nodeResult .= "<metadata-list category-list-size=" . chr(34) . "1" . chr(34) . " other-list-size=" . chr(34) . "0" . chr(34) . " ontology-list-size=" . chr(34) . "0" . chr(34) . " RCategoryListSize=" . chr(34) . "0" . chr(34) . ">".chr(13).chr(10);
                    $nodeResult .= "<ontology-list-string></ontology-list-string>".chr(13).chr(10);
                    $nodeResult .= "<metadata xsi:type=" . chr(34) . "vue-metadata-element" . chr(34) . ">".chr(13).chr(10);
                    $nodeResult .= "<value></value>".chr(13).chr(10);
                    $nodeResult .= "<key>http://vue.tufts.edu/vue.rdfs#none</key>".chr(13).chr(10);
                    $nodeResult .= "<type>1</type>".chr(13).chr(10);
                    $nodeResult .= "</metadata>".chr(13).chr(10);
                    $nodeResult .= "</metadata-list>".chr(13).chr(10);
                    $nodeResult .= "<URIString>http://vue.tufts.edu/rdf/resource/8269e781a9fe648b019fe5e646094b9d</URIString>".chr(13).chr(10);
                    $nodeResult .= "<shape arcwidth=" . chr(34) . "20.0" . chr(34) . " archeight=" . chr(34) . "20.0" . chr(34) . " xsi:type=" . chr(34) . "roundRect" . chr(34) . "/>".chr(13).chr(10);
                    $nodeResult .= "</child>".chr(13).chr(10);

                    if (count($node->links) > 0) {
                        foreach ($node->links as $link) {
                            $linksResult .= "<child ID=" . chr(34) . $link->id . chr(34) . " x=" . chr(34) . "1" . chr(34) . " y=" . chr(34) . "1" . chr(34) . " width=" . chr(34) . "1" . chr(34) . " height=" . chr(34) . "1" . chr(34) . " strokeWidth=" . chr(34) . "1.0" . chr(34) . " autoSized=" . chr(34) . "false" . chr(34) . " controlCount=" . chr(34) . "0" . chr(34) . " arrowState=" . chr(34) . "2" . chr(34) . " xsi:type=" . chr(34) . "link" . chr(34) . ">".chr(13).chr(10);
                            $linksResult .= "<strokeColor>#404040</strokeColor>".chr(13).chr(10);
                            $linksResult .= "<textColor>#404040</textColor>".chr(13).chr(10);
                            $linksResult .= "<font>Arial-plain-11</font>".chr(13).chr(10);
                            $linksResult .= "<metadata-list category-list-size=" . chr(34) . "1" . chr(34) . " other-list-size=" . chr(34) . "0" . chr(34) . " ontology-list-size=" . chr(34) . "0" . chr(34) . " RCategoryListSize=" . chr(34) . "0" . chr(34) . ">".chr(13).chr(10);
                            $linksResult .= "<ontology-list-string></ontology-list-string>".chr(13).chr(10);
                            $linksResult .= "<metadata xsi:type=" . chr(34) . "vue-metadata-element" . chr(34) . ">".chr(13).chr(10);
                            $linksResult .= "<value></value>".chr(13).chr(10);
                            $linksResult .= "<key>http://vue.tufts.edu/vue.rdfs#none</key>".chr(13).chr(10);
                            $linksResult .= "<type>1</type>".chr(13).chr(10);
                            $linksResult .= "</metadata>".chr(13).chr(10);
                            $linksResult .= "</metadata-list>".chr(13).chr(10);
                            $linksResult .= "<URIString>http://vue.tufts.edu/rdf/resource/8269e795a9fe648b019fe5e649ec98b8</URIString>".chr(13).chr(10);

                            $linksResult .= "<point1 x=" . chr(34) . $link->node_1->x . chr(34) . " y=" . chr(34) . $link->node_1->y . chr(34) . "/>".chr(13).chr(10);

                            $linksResult .= "<point2 x=" . chr(34) . $link->node_2->x . chr(34) . " y=" . chr(34) . $link->node_2->x . chr(34) . "/>".chr(13).chr(10);

                            $linksResult .= "<ID1 xsi:type=" . chr(34) . "node" . chr(34) . ">" . $link->node_1->id . "</ID1>".chr(13).chr(10);
                            $linksResult .= "<ID2 xsi:type=" . chr(34) . "node" . chr(34) . ">" . $link->node_2->id . "</ID2>".chr(13).chr(10);
                            $linksResult .= "</child>".chr(13).chr(10);
                        }
                    }
                }
            }

            $footerResult .= "<userZoom>1.0</userZoom>".chr(13).chr(10);
            $footerResult .= "<userOrigin x=" . chr(34) . "-12.0" . chr(34) . " y=" . chr(34) . "-12.0" . chr(34) . "/>".chr(13).chr(10);
            $footerResult .= "<presentationBackground>#202020</presentationBackground>".chr(13).chr(10);
            $footerResult .= "<PathwayList currentPathway=" . chr(34) . "0" . chr(34) . " revealerIndex=" . chr(34) . "-1" . chr(34) . ">".chr(13).chr(10);
            $footerResult .= "<pathway ID=" . chr(34) . "1" . chr(34) . " label=" . chr(34) . "Untitled Pathway" . chr(34) . " x=" . chr(34) . "0.0" . chr(34) . " y=" . chr(34) . "0.0" . chr(34) . " width=" . chr(34) . "1.4E-45" . chr(34) . " height=" . chr(34) . "1.4E-45" . chr(34) . " strokeWidth=" . chr(34) . "0.0" . chr(34) . " autoSized=" . chr(34) . "false" . chr(34) . " currentIndex=" . chr(34) . "-1" . chr(34) . " locked=" . chr(34) . "false" . chr(34) . " open=" . chr(34) . "true" . chr(34) . ">".chr(13).chr(10);
            $footerResult .= "<strokeColor>#B3993333</strokeColor>".chr(13).chr(10);
            $footerResult .= "<textColor>#000000</textColor>".chr(13).chr(10);
            $footerResult .= "<font>SansSerif-plain-14</font>".chr(13).chr(10);
            $footerResult .= "<metadata-list category-list-size=" . chr(34) . "0" . chr(34) . " other-list-size=" . chr(34) . "0" . chr(34) . " ontology-list-size=" . chr(34) . "0" . chr(34) . " RCategoryListSize=" . chr(34) . "0" . chr(34) . ">".chr(13).chr(10);
            $footerResult .= "<ontology-list-string></ontology-list-string>".chr(13).chr(10);
            $footerResult .= "</metadata-list>".chr(13).chr(10);
            $footerResult .= "<URIString>http://vue.tufts.edu/rdf/resource/8269e795a9fe648b019fe5e698d37a7d</URIString>".chr(13).chr(10);
            $footerResult .= "<masterSlide ID=" . chr(34) . "8" . chr(34) . " x=" . chr(34) . "0.0" . chr(34) . " y=" . chr(34) . "0.0" . chr(34) . " width=" . chr(34) . "800.0" . chr(34) . " height=" . chr(34) . "600.0" . chr(34) . " strokeWidth=" . chr(34) . "0.0" . chr(34) . " autoSized=" . chr(34) . "false" . chr(34) . ">".chr(13).chr(10);
            $footerResult .= "<fillColor>#000000</fillColor>".chr(13).chr(10);
            $footerResult .= "<strokeColor>#404040</strokeColor>".chr(13).chr(10);
            $footerResult .= "<textColor>#000000</textColor>".chr(13).chr(10);
            $footerResult .= "<font>SansSerif-plain-14</font>".chr(13).chr(10);
            $footerResult .= "<metadata-list category-list-size=" . chr(34) . "0" . chr(34) . " other-list-size=" . chr(34) . "0" . chr(34) . " ontology-list-size=" . chr(34) . "0" . chr(34) . " RCategoryListSize=" . chr(34) . "0" . chr(34) . ">".chr(13).chr(10);
            $footerResult .= "<ontology-list-string></ontology-list-string>".chr(13).chr(10);
            $footerResult .= "</metadata-list>".chr(13).chr(10);
            $footerResult .= "<URIString>http://vue.tufts.edu/rdf/resource/8269e7dba9fe648b019fe5e6dcec1bac</URIString>".chr(13).chr(10);
            $footerResult .= "<titleStyle ID=" . chr(34) . "9" . chr(34) . " label=" . chr(34) . "Header" . chr(34) . " x=" . chr(34) . "335.5" . chr(34) . " y=" . chr(34) . "172.5" . chr(34) . " width=" . chr(34) . "129.0" . chr(34) . " height=" . chr(34) . "55.0" . chr(34) . " strokeWidth=" . chr(34) . "0.0" . chr(34) . " autoSized=" . chr(34) . "true" . chr(34) . " isStyle=" . chr(34) . "true" . chr(34) . " xsi:type=" . chr(34) . "node" . chr(34) . ">".chr(13).chr(10);
            $footerResult .= "<strokeColor>#404040</strokeColor>".chr(13).chr(10);
            $footerResult .= "<textColor>#FFFFFF</textColor>".chr(13).chr(10);
            $footerResult .= "<font>Gill Sans-plain-36</font>".chr(13).chr(10);
            $footerResult .= "<metadata-list category-list-size=" . chr(34) . "0" . chr(34) . " other-list-size=" . chr(34) . "0" . chr(34) . " ontology-list-size=" . chr(34) . "0" . chr(34) . " RCategoryListSize=" . chr(34) . "0" . chr(34) . ">".chr(13).chr(10);
            $footerResult .= "<ontology-list-string></ontology-list-string>".chr(13).chr(10);
            $footerResult .= "</metadata-list>".chr(13).chr(10);
            $footerResult .= "<URIString>http://vue.tufts.edu/rdf/resource/8269e7dba9fe648b019fe5e66aa2accc</URIString>".chr(13).chr(10);
            $footerResult .= "<shape xsi:type=" . chr(34) . "rectangle" . chr(34) . "/>".chr(13).chr(10);
            $footerResult .= "</titleStyle>".chr(13).chr(10);
            $footerResult .= "<textStyle ID=" . chr(34) . "10" . chr(34) . " label=" . chr(34) . "Slide Text" . chr(34) . " x=" . chr(34) . "346.5" . chr(34) . " y=" . chr(34) . "281.5" . chr(34) . " width=" . chr(34) . "107.0" . chr(34) . " height=" . chr(34) . "37.0" . chr(34) . " strokeWidth=" . chr(34) . "0.0" . chr(34) . " autoSized=" . chr(34) . "true" . chr(34) . " isStyle=" . chr(34) . "true" . chr(34) . " xsi:type=" . chr(34) . "node" . chr(34) . ">".chr(13).chr(10);
            $footerResult .= "<strokeColor>#404040</strokeColor>".chr(13).chr(10);
            $footerResult .= "<textColor>#FFFFFF</textColor>".chr(13).chr(10);
            $footerResult .= "<font>Gill Sans-plain-22</font>".chr(13).chr(10);
            $footerResult .= "<metadata-list category-list-size=" . chr(34) . "0" . chr(34) . " other-list-size=" . chr(34) . "0" . chr(34) . " ontology-list-size=" . chr(34) . "0" . chr(34) . " RCategoryListSize=" . chr(34) . "0" . chr(34) . ">".chr(13).chr(10);
            $footerResult .= "<ontology-list-string></ontology-list-string>".chr(13).chr(10);
            $footerResult .= "</metadata-list>".chr(13).chr(10);
            $footerResult .= "<URIString>http://vue.tufts.edu/rdf/resource/8269e7dba9fe648b019fe5e64a1d2b24</URIString>".chr(13).chr(10);
            $footerResult .= "<shape xsi:type=" . chr(34) . "rectangle" . chr(34) . "/>".chr(13).chr(10);
            $footerResult .= "</textStyle>".chr(13).chr(10);
            $footerResult .= "<linkStyle ID=" . chr(34) . "11" . chr(34) . " label=" . chr(34) . "Links" . chr(34) . " x=" . chr(34) . "373.5" . chr(34) . " y=" . chr(34) . "384.0" . chr(34) . " width=" . chr(34) . "53.0" . chr(34) . " height=" . chr(34) . "32.0" . chr(34) . " strokeWidth=" . chr(34) . "0.0" . chr(34) . " autoSized=" . chr(34) . "true" . chr(34) . " isStyle=" . chr(34) . "true" . chr(34) . " xsi:type=" . chr(34) . "node" . chr(34) . ">".chr(13).chr(10);
            $footerResult .= "<strokeColor>#404040</strokeColor>".chr(13).chr(10);
            $footerResult .= "<textColor>#B3BFE3</textColor>".chr(13).chr(10);
            $footerResult .= "<font>Gill Sans-plain-18</font>".chr(13).chr(10);
            $footerResult .= "<metadata-list category-list-size=" . chr(34) . "0" . chr(34) . " other-list-size=" . chr(34) . "0" . chr(34) . " ontology-list-size=" . chr(34) . "0" . chr(34) . " RCategoryListSize=" . chr(34) . "0" . chr(34) . ">".chr(13).chr(10);
            $footerResult .= "<ontology-list-string></ontology-list-string>".chr(13).chr(10);
            $footerResult .= "</metadata-list>".chr(13).chr(10);
            $footerResult .= "<URIString>http://vue.tufts.edu/rdf/resource/8269e7dba9fe648b019fe5e6375d1c02</URIString>".chr(13).chr(10);
            $footerResult .= "<shape xsi:type=" . chr(34) . "rectangle" . chr(34) . "/>".chr(13).chr(10);
            $footerResult .= "</linkStyle>".chr(13).chr(10);
            $footerResult .= "</masterSlide>".chr(13).chr(10);
            $footerResult .= "</pathway>".chr(13).chr(10);
            $footerResult .= "</PathwayList>".chr(13).chr(10);
            $footerResult .= "<date>2009-09-03</date>".chr(13).chr(10);
            $footerResult .= "<mapFilterModel/>".chr(13).chr(10);
            $footerResult .= "<modelVersion>4</modelVersion>".chr(13).chr(10);
            $footerResult .= "<saveLocation>C:\Documents and Settings\Administrator\Desktop</saveLocation>".chr(13).chr(10);
            $footerResult .= "<saveFile>C:\Documents and Settings\Administrator\Desktop\simplevue.vue</saveFile>".chr(13).chr(10);
            $footerResult .= "</LW-MAP>".chr(13).chr(10);

            $out = $header . $nodeResult . $linksResult . $footerResult;

            $fileName = $_SERVER['DOCUMENT_ROOT'] . '/export/OLVue-export-' . $mapId . '.vue';
            $f = fopen($fileName, 'w') or die("can't create file");
            fwrite($f, $out);
            fclose($f);
        }
    }
}

?>
