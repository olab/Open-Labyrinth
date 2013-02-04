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
        $this->uri = $uri;
        $this->narrower = array();
        $this->broader = array();
        $graph = new Graphite();
        $graph->load( $source );
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
        $graph = new Graphite();
        $graph->load( $source );
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

        return $html;
    }

    public static function getAllTerms($source){
        $graph = new Graphite();
        $graph->load( $source );
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
