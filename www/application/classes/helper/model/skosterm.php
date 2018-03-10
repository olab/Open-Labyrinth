<?php
/**
 * Created by JetBrains PhpStorm.
 * User: larjohns
 * Date: 26/10/2012
 * Time: 4:17 μμ
 * To change this template use File | Settings | File Templates.
 */
require_once(Kohana::find_file('vendor', 'arc2/ARC2'));
require_once(Kohana::find_file('vendor', 'Graphite'));
class Helper_Model_SkosTerm extends Kohana_Object
{
    public function __construct($uri, $source){
        if (!parse_url($source, PHP_URL_SCHEME) != '') $source = URL::base().$source;
        $this->uri = $uri;
        $this->narrower = array();
        $this->broader = array();
        $graph = self::initGraph($source);
        $this->label = $graph->resource($uri)->label();
        $this->broader =  $graph->resource($uri)->all("skos:broader");
        $this->narrower =  $graph->resource($uri)->all("skos:narrower");

    }

    private $label;

    private $narrower;

    private $broader;

    private $uri;

    public function label(){
        return $this->label;
    }

    public function uri(){
        return $this->uri;
    }


    public static  function buildTree($terms, $tree=array(), $myparent = null){

        foreach($terms as $term){
            $uri = $term->toString();
            if(!isset($tree[$uri])){
                $tree[$uri]['term'] = $term;
                $tree[$uri]['children'] = array();
                $tree[$uri]['parents'] = array();
                $tree[$uri]['label'] = $term->label();

                $children = $term->all("skos:narrower");
                foreach($children as $child){
                    $childUri = $child->toString();
                    if(!in_array($childUri,$tree[$uri]['children']))
                        $tree[$uri]['children'][] = $childUri;
                }

                $tree = self::buildTree($children, $tree, $uri);

                $parents = $term->all("skos:broader");

                $tree = self::buildTree($parents, $tree, null);
                foreach($parents as $parent){
                    $parentUri = $parent->toString();
                    if(!in_array($uri,$tree[$parentUri]['children']))
                        $tree[$parentUri]['children'][] = $uri;
                    if(!in_array($parentUri,$tree[$uri]['parents']))
                        $tree[$uri]["parents"][]=$parentUri;
                }

            }
            if($myparent!==null) {
                if(!in_array($myparent,$tree[$uri]['parents']))
                    $tree[$uri]["parents"][]=$myparent;
            }
        }
        return $tree;
    }


    public static function printRecursive(&$html, $tree, $root, $processed = array(), $level =0 ){

        $children = $tree[$root]["children"];
        $processed[] = $root;
        $html .= '<ul>';

        foreach($children as $child){
            if(in_array($child,$processed))continue;
            $identifier = 'RDF_' . md5($child);
            $html .=  "<li id='$identifier'>";
            $html .=  "<input type='hidden' value='$child' class='$identifier'>";
            $html .=  '<a>'.$tree[$child]['label'].'</a>';
            $processed = self::printRecursive($html,$tree,$child,$processed, $level+1 );
            $html .=  '</li>';
       }
        $html .=  '</ul>';
        return $processed;
    }


    public static function getAllTermsTree($source, $name, $cardinality){
        if (!parse_url($source, PHP_URL_SCHEME) != '') $source  = Model_Leap_Vocabulary::getGraphUri().$source;

        $graph = self::initGraph($source);
        $terms = $graph->allOfType( "skos:Concept" );

        $tree = self::buildTree($terms);

        $html = "";

        foreach($tree as  $uri=>$node){
            if(isset($node["term"])){
                $tree[$uri]["term"]=array();
            }
        }

        if($cardinality == Model_Leap_Metadata::Cardinality_Many){
            $cardinalityClass = "multi";
        }
        else {
            $cardinalityClass = "single";
        }
        $html .= '<div id="'.$name.'_results"></div>';

        $html .= '<div id="'.$name.'_modal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="'.$name.'Label" aria-hidden="true">';
        $html .= '  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="'.$name.'Label">Select '.$name.'</h3>
  </div>';
        $html .= '  <div class="modal-body">';
        $html .= "<div id='$name' class='tree $cardinalityClass'>";
        $processed = array();
        foreach($tree as $uri=>$node){
            $root = false;
            if(count($node["parents"])==0)
            {
                $root = true;
                $html .= $uri.'<br/>';
            }

            if($root){
                $processed = self::printRecursive($html,$tree,$uri, $processed );
            }
        }
        $html .= '</div>';
        $html .= '  <div class="modal-footer">
    <button class="btn btn-primary" data-dismiss="modal" aria-hidden="true">Close</button>

  </div>';

        $html .= '</div>';
        $html .= '</div>';

        return $html;
    }

    private static  function initGraph($source, $id = "")
    {
        if (!parse_url($source, PHP_URL_SCHEME) != '') $source = URL::base().$source;
        if(!is_dir(sys_get_temp_dir() . '/olab/'))mkdir(sys_get_temp_dir() . '/olab/');
        $temp_file = sys_get_temp_dir() . '/olab/' . md5($source).$id;
        $graph = new Graphite();
        if (!file_exists($temp_file)) {
            $graph->load($source);
            $graph->freeze($temp_file);
        } else{

            $graph = Graphite::thaw($temp_file);
            if(count($graph->allSubjects())<1)
            {
                $graph->load($source);
                $graph->freeze($temp_file);
            }

        }

        return $graph;
    }

    public static function getAllTerms($source){
        if (!parse_url($source, PHP_URL_SCHEME) != '') $source = URL::base().$source;
        $graph = self::initGraph($source);
        $terms = $graph->allOfType( "skos:Concept" );

        $pairs = array();

        foreach($terms as $term){
            $uri = $term->toString();
            $label =  $term->label();
            $pairs[$uri]= $label;
        }

        return $pairs;

    }
}
