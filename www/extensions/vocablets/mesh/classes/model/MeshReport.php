<?php
/**
 * Created by PhpStorm.
 * User: larjohns
 * Date: 13/5/2014
 * Time: 5:15 μμ
 */

require_once(Kohana::find_file('vendor', 'arc2/ARC2'));
require_once(Kohana::find_file('vendor', 'sparqllib'));

class Model_MeshReport
{

    private static $nodesList = array();

    public static function buildObject()
    {
        $sparql_config = Kohana::$config->load('sparql');
        //var_dump($sparql_config["driver"]);
        //var_dump($sparql_config["endpoint"]);
        $db = sparql_connect($sparql_config["endpoint"]);

        if (!$db) {
            print $db->errno() . ": " . $db->error() . "\n";
            exit;
        }

        if(isset($sparql_config["driver"])&&$sparql_config["driver"]=="Helper_RDF_Store_Arc"){
            $sparql =
                "select distinct  ?mesh count( ?labyrinth) as ?count ?meshlabel   ?meshparent ?meshparentlabel
?meshgrandpa1  ?meshgrandpa1label ?meshgrandpa2   ?meshgrandpa2label
where

{

?labyrinth <http://purl.org/meducator/ns/DisciplineSpeciality> ?mesh.
?mesh <http://www.w3.org/2004/02/skos/core#prefLabel> ?meshlabel.
OPTIONAL {
?mesh <http://www.w3.org/2004/02/skos/core#broader> ?meshparent.
?meshparent <http://www.w3.org/2004/02/skos/core#prefLabel> ?meshparentlabel.

OPTIONAL {
?meshparent <http://www.w3.org/2004/02/skos/core#broader> ?meshgrandpa1.
?meshgrandpa1 <http://www.w3.org/2004/02/skos/core#prefLabel> ?meshgrandpa1label.

OPTIONAL {
?meshgrandpa1 <http://www.w3.org/2004/02/skos/core#broader> ?meshgrandpa2.
?meshgrandpa2 <http://www.w3.org/2004/02/skos/core#prefLabel> ?meshgrandpa2label.
}.

}.


}.
}



group by ?mesh ?meshparent ?meshgrandpa1 ?meshgrandpa2



                ";
        }

       else{
           $sparql =
               "select  min(?mesh) as ?mesh count(distinct ?labyrinth) as ?count min(?meshlabel) as ?meshlabel  ?meshparent min(?meshparentlabel) as ?meshparentlabel
   ?meshgrandpa1 max(?meshgrandpa1label) as ?meshgrandpa1label ?meshgrandpa2 max(?meshgrandpa2label) as ?meshgrandpa2label
   where

   {

   ?labyrinth <http://purl.org/meducator/ns/DisciplineSpeciality> ?mesh.
   ?mesh <http://www.w3.org/2004/02/skos/core#prefLabel> ?meshlabel.
   OPTIONAL {
   ?mesh <http://www.w3.org/2004/02/skos/core#broader> ?meshparent.
   ?meshparent <http://www.w3.org/2004/02/skos/core#prefLabel> ?meshparentlabel.

   OPTIONAL {
   ?meshparent <http://www.w3.org/2004/02/skos/core#broader> ?meshgrandpa1.
   ?meshgrandpa1 <http://www.w3.org/2004/02/skos/core#prefLabel> ?meshgrandpa1label.

   OPTIONAL {
   ?meshgrandpa1 <http://www.w3.org/2004/02/skos/core#broader> ?meshgrandpa2.
   ?meshgrandpa2 <http://www.w3.org/2004/02/skos/core#prefLabel> ?meshgrandpa2label.
   }.

   }.


   }.
   }



   group by ?mesh ?meshparent ?meshgrandpa1 ?meshgrandpa2



   ";
       }
        $result = $db->query($sparql);
        if (!$result) {
            print $db->errno() . ": " . $db->error() . "\n";
            exit;
        }

        //$fields = $result->field_array( $result );
        self::$nodesList = $result->fetch_all();
        //var_dump($rows);
//var_dump( self::$nodesList );

        $roots = self::getRoots();


        $total = 0;

        $central = array(
            0=>"All Health Occupations Classifications",
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
           // var_dump($sparqlNode);die;
            if ( isset($sparqlNode["meshparent"]) && $sparqlNode["meshparent"] == $id) {
                //child

                $child = array(
                    0 => $sparqlNode["meshlabel"],
                    1 => array(0 => intval($sparqlNode["count"]), 1 => intval($sparqlNode["count"]),),
                    2 => array(),
                    3 => $sparqlNode["mesh"],
                );
                $children[$child[3]] = $child;
            }

        }


        foreach (self::$nodesList as $sparqlNode) {
            // var_dump($sparqlNode);die;
            if ( isset($sparqlNode["meshgrandpa1"]) && $sparqlNode["meshgrandpa1"] == $id) {
                //child
                $child = array(
                    0 => $sparqlNode["meshparentlabel"],
                    1 => array( 0=>0, 1 => 0,),
                    2 => array(),
                    3 => $sparqlNode["meshparent"],
                );
                if(!isset( $children[$child[3]]))
                    $children[$child[3]] = $child;
            }

        }


        foreach (self::$nodesList as $sparqlNode) {
            // var_dump($sparqlNode);die;
            if ( isset($sparqlNode["meshgrandpa2"]) && $sparqlNode["meshgrandpa2"] == $id) {
                //child
                $child = array(
                    0 => $sparqlNode["meshgrandpa1label"],
                    1 => array( 0=>0, 1 => 0,),
                    2 => array(),
                    3 => $sparqlNode["meshgrandpa1"],
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


            if (empty($sparqlNode["meshparent"])) {
                //root
                $root = array(
                    0 => $sparqlNode["meshlabel"],
                    1 => array(0 => intval($sparqlNode["count"]), 1 => intval($sparqlNode["count"]),),
                    2 => array(),
                    3 => $sparqlNode["mesh"],
                );

            }
            elseif(empty($sparqlNode["meshgrandpa1"])){
                //root
                $root = array(
                    0 => $sparqlNode["meshparentlabel"],
                    1 => array(0 => 0, 1 => 0,),
                    2 => array(),
                    3 => $sparqlNode["meshparent"],
                );

            }
            elseif(empty($sparqlNode["meshgrandpa2"])){
                //root
                $root = array(
                    0 => $sparqlNode["meshgrandpa1label"],
                    1 => array(0 => 0, 1 => 0,),
                    2 => array(),
                    3 => $sparqlNode["meshgrandpa1"],
                );

            }
            else{
                //root
                $root = array(
                    0 => $sparqlNode["meshgrandpa2label"],
                    1 => array(0 => 0, 1 => 0,),
                    2 => array(),
                    3 => $sparqlNode["meshgrandpa2"],
                );

            }
            if($root[0]!="Health Occupations")
                $roots[$root[3]] = $root;
        }

        return $roots;

    }

//static  $i = 0;
    private static function traverse(&$node)
    {
       // self::$i++;
        $children = self::getChildren($node);
        $node[2] = &$children;
      //  if(self::$i<10)var_dump($children);die;

        if (!empty($children)) {
            $extras = 0;
          //  echo "\n". $node[0].": \n";
            foreach ($children as &$child) {
                $value = self::traverse($child);

                $extras += $value;
               // echo $value. ",";
            }

            $node[1][0] += $extras;

        }

        //if($node[0]=="Pediatrics") var_dump($node[1][0]);
        //echo "\n".$node[0].":: " . $node[1][0]."\n";
        return $node[1][0];
    }

} 