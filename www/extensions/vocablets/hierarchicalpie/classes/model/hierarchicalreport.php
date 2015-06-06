<?php
/**
 * Created by PhpStorm.
 * User: larjohns
 * Date: 13/5/2014
 * Time: 5:15 μμ
 */

require_once(Kohana::find_file('vendor', 'arc2/ARC2'));
require_once(Kohana::find_file('vendor', 'sparqllib'));

class Model_HierarchicalReport
{

    private static $nodesList = array();

    protected static  $broader = "http://www.w3.org/2004/02/skos/core#broader";
    protected  static $label = "http://www.w3.org/2004/02/skos/core#prefLabel";
    protected static $predicate = "http://purl.org/meducator/ns/DisciplineSpeciality";

    public static function buildObject()
    {
        $self = get_called_class();
        $sparql_config = Kohana::$config->load('sparql');
        $graph_uri = Model_Leap_Vocabulary::getGraphUri();
$predicateUri = $self::$predicate;

$labelUri = $self::$label;
$broaderUri = $self::$broader;
        $db = sparql_connect($sparql_config["endpoint"]);

        if (!$db) {
            print $db->errno() . ": " . $db->error() . "\n";
            exit;
        }

        if(isset($sparql_config["driver"])&&$sparql_config["driver"]=="Helper_RDF_Store_Arc"){
            $sparql =
                "select distinct  ?term count( ?labyrinth) as ?count ?termlabel   ?termparent ?termparentlabel
?termgrandpa1  ?termgrandpa1label ?termgrandpa2   ?termgrandpa2label
where

{
graph<$graph_uri>{
?labyrinth <$predicateUri> ?term.
?term <$labelUri> ?termlabel.
OPTIONAL {
?term <$broaderUri> ?termparent.
?termparent <$labelUri> ?termparentlabel.

OPTIONAL {
?termparent <$broaderUri> ?termgrandpa1.
?termgrandpa1 <$labelUri> ?termgrandpa1label.

OPTIONAL {
?termgrandpa1 <$broaderUri> ?termgrandpa2.
?termgrandpa2 <$labelUri> ?termgrandpa2label.
}.

}.


}.
}
}



group by ?term ?termparent ?termgrandpa1 ?termgrandpa2



                ";

        }

       else{
           $sparql =
               "select  min(?term) as ?term count(distinct ?labyrinth) as ?count min(?termlabel) as ?termlabel  ?termparent min(?termparentlabel) as ?termparentlabel
   ?termgrandpa1 max(?termgrandpa1label) as ?termgrandpa1label ?termgrandpa2 max(?termgrandpa2label) as ?termgrandpa2label
   where

   {
graph <$graph_uri>{
   ?labyrinth <$predicateUri> ?term.
   ?term <$labelUri> ?termlabel.
   OPTIONAL {
   ?term <$broaderUri> ?termparent.
   ?termparent <$labelUri> ?termparentlabel.

   OPTIONAL {
   ?termparent <$broaderUri> ?termgrandpa1.
   ?termgrandpa1 <$labelUri> ?termgrandpa1label.

   OPTIONAL {
   ?termgrandpa1 <$broaderUri> ?termgrandpa2.
   ?termgrandpa2 <$labelUri> ?termgrandpa2label.
   }.

   }.


   }.
   }
   }



   group by ?term ?termparent ?termgrandpa1 ?termgrandpa2



   ";
       }
       // var_dump($sparql);
        $result = $db->query($sparql);
        if (!$result) {
            print $db->errno() . ": " . $db->error() . "\n";
            exit;
        }


        self::$nodesList = $result->fetch_all();


        $roots = self::getRoots();


        $total = 0;

        $central = array(
            0=>"All Terms",
            1=>array($total, $total),
            2=>array(),
        );

        foreach ($roots as &$root) {

            self::traverse($root);
            $total += $root[1][0];

            $central[2][] = $root;

        }

        $central[1]= array($total, $total);

        return $central;


    }

    private static function getChildren($node)
    {
        $id = $node[3];
        $children = array();
        foreach (self::$nodesList as $sparqlNode) {

            if ( isset($sparqlNode["termparent"]) && $sparqlNode["termparent"] == $id) {
                //child

                $child = array(
                    0 => $sparqlNode["termlabel"],
                    1 => array(0 => intval($sparqlNode["count"]), 1 => intval($sparqlNode["count"]),),
                    2 => array(),
                    3 => $sparqlNode["term"],
                );
                $children[$child[3]] = $child;
            }

        }


        foreach (self::$nodesList as $sparqlNode) {

            if ( isset($sparqlNode["termgrandpa1"]) && $sparqlNode["termgrandpa1"] == $id) {
                //child
                $child = array(
                    0 => $sparqlNode["termparentlabel"],
                    1 => array( 0=>0, 1 => 0,),
                    2 => array(),
                    3 => $sparqlNode["termparent"],
                );
                if(!isset( $children[$child[3]]))
                    $children[$child[3]] = $child;
            }

        }


        foreach (self::$nodesList as $sparqlNode) {

            if ( isset($sparqlNode["termgrandpa2"]) && $sparqlNode["termgrandpa2"] == $id) {
                //child
                $child = array(
                    0 => $sparqlNode["termgrandpa1label"],
                    1 => array( 0=>0, 1 => 0,),
                    2 => array(),
                    3 => $sparqlNode["termgrandpa1"],
                );
                if(!isset( $children[$child[3]]))
                    $children[$child[3]] = $child;
            }

        }

        return $children;

    }

    private static function getRoots()
    {
        $roots = array();



        foreach (self::$nodesList as $sparqlNode) {


            if (empty($sparqlNode["termparent"])) {
                //root
                $root = array(
                    0 => $sparqlNode["termlabel"],
                    1 => array(0 => intval($sparqlNode["count"]), 1 => intval($sparqlNode["count"]),),
                    2 => array(),
                    3 => $sparqlNode["term"],
                );

            }
            elseif(empty($sparqlNode["termgrandpa1"])){
                //root
                $root = array(
                    0 => $sparqlNode["termparentlabel"],
                    1 => array(0 => 0, 1 => 0,),
                    2 => array(),
                    3 => $sparqlNode["termparent"],
                );

            }
            elseif(empty($sparqlNode["termgrandpa2"])){
                //root
                $root = array(
                    0 => $sparqlNode["termgrandpa1label"],
                    1 => array(0 => 0, 1 => 0,),
                    2 => array(),
                    3 => $sparqlNode["termgrandpa1"],
                );

            }
            else{
                //root
                $root = array(
                    0 => $sparqlNode["termgrandpa2label"],
                    1 => array(0 => 0, 1 => 0,),
                    2 => array(),
                    3 => $sparqlNode["termgrandpa2"],
                );

            }
            if($root[0]!="Health Occupations")
                $roots[$root[3]] = $root;
        }

        return $roots;

    }


    private static function traverse(&$node)
    {

        $children = self::getChildren($node);
        $node[2] = &$children;


        if (!empty($children)) {
            $extras = 0;

            foreach ($children as &$child) {
                $value = self::traverse($child);

                $extras += $value;

            }

            $node[1][0] += $extras;

        }


        return $node[1][0];
    }

} 