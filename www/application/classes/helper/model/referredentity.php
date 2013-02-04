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
class Helper_Model_ReferredEntity extends Kohana_Object
{
    public function __construct($uri, $source, $labelProperty=null){
        $this->uri = $uri;
        $this->narrower = array();
        $this->broader = array();
        $graph = new Graphite();
        $graph->load( $source );

        if($labelProperty===null)
            $this->label = $graph->resource($uri)->label();
        else $this->label   = $graph->resource($uri)->get($labelProperty);




    }

    private $label;


    private $uri;

    public function label(){
        return $this->label;
    }

    public function uri(){
        return $this->uri;
    }


    public static  function buildTree($terms, $labelProperty=null, $tree=array()){

        foreach($terms as $term){
            $uri = $term->toString();
            if(!isset($tree[$uri])){
                $tree[$uri]['term'] = $term;
                if($labelProperty===null)
                    $tree[$uri]['label'] = $term->label();
                else $tree[$uri]['label']  = $term->get($labelProperty);
                $tree[$uri]['uri'] = $uri;
            }
        }
        return $tree;
    }


    public static function printTree(&$html, $tree, $root){

        //var_dump($tree);die;

        $html .= '<ul>';

            $uri  = $root['uri'];
            $identifier = 'RDF_' . md5($uri);
            $html .=  "<li id='$identifier'>";
            $html .=  "<input type='hidden' value='$uri' class='$identifier'>";
            $html .=  '<a>'.$tree[$uri]['label'].'</a>';

            $html .=  '</li>';

        $html .=  '</ul>';

    }


    public static function getAllTermsTree($source, $name, $cardinality, $label=null, $type = NULL){
        $graph = new Graphite();
        $graph->load( $source );
        if($type==NULL)
            $terms = $graph->allSubjects();
        else
            $terms = $graph->allOfType( $type );

        $tree = self::buildTree($terms, $label);

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
        foreach($tree as $uri){

            self::printTree($html,$tree,$uri, $processed );

        }
        $html .= '</div>';

        return $html;
    }

    public static function getAllTerms($source, $name, $values, $labelProperty, $type = NULL){

        $graph = new Graphite();
        $graph->load( $source );
        if($type==NULL)
            $terms = $graph->allSubjects();
        else
            $terms = $graph->allOfType( $type );

        $pairs = array();

        foreach($terms as $term){
            $uri = $term->toString();

            if($labelProperty===null)
                $label = $term->label();
            else $label  = $term->get($labelProperty);


            $pairs[$uri]= $label;
        }


        $html = "<select name='$name' >";




        if(!empty($values)){
            $value = $values[0]->uri;
        }
        else $value = "";




        foreach($pairs as $uri=>$lbl){
            $selected = $uri ==$value?"selected='selected'":"";

            $html .="<option value='$uri'". $selected .">$lbl</option>";
        }

        $html .= "</select>";

        return $html;

    }
}
