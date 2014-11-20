<?php

/**
 * Created by PhpStorm.
 * User: larjohns
 * Date: 16/5/2014
 * Time: 1:36 πμ
 */
require_once(Kohana::find_file('vendor', 'arc2/ARC2'));
require_once(Kohana::find_file('vendor', 'sparqllib'));


class Model_Leap_Vocabulary_EntityType_Disease extends Model_Leap_Vocabulary_EntityType
{
    public static function getSuggestedUris($term)
    {
        $mesh_config = Kohana::$config->load('mesh');


        if(!isset($mesh_config["mesh_endpoint"])){
            return array();
        }

        if(isset($mesh_config["mesh_graph"]))
            $graph = " FROM <".$mesh_config["mesh_graph"].">";
        else $graph = "";

        $db = sparql_connect($mesh_config["mesh_endpoint"]);

        if (!$db) {
            print $db->errno() . ": " . $db->error() . "\n";
            exit;
        }


            $sparql =
                "select ?s ?label
                $graph
                {?s rdfs:label ?label.
 FILTER regex(?label,'^$term').
}

                ";

        $result = $db->query($sparql);
        if (!$result) {
            print $db->errno() . ": " . $db->error() . "\n";
            exit;
        }

        $suggestion = $result->fetch_all();

        if(isset($suggestion[0]))
            $uri = $suggestion[0]["s"];
        else $uri = "";

        return
            array("results" => array(
                "entries" => array(
                    0 => array(
                        "uri" => $uri,
                    ),
                )
            ));
    }


    public static function getDefinition()
    {
        return
            array(
                "properties" =>
                array(
                   "name"=> array (
                        'comment' => 'The name of the item.',
                        'comment_plain' => 'The name of the item.',
                        'domains' =>
                            array (
                                0 => 'Thing',
                            ),
                        'id' => 'name',
                        'label' => 'Name',
                        'ranges' =>
                            array (
                                0 => 'Text',
                            ),
                    )
                ),
                "types" => array(
                    "Disease" =>
                        array(
                            'ancestors' =>
                                array(
                                    0 => 'Thing',
                                ),
                            'comment' => '',
                            'comment_plain' => '',
                            'id' => 'Disease',
                            'label' => 'Disease',
                            'properties' =>
                                array(

                                    0 => 'name',
                                      ),
                            'specific_properties' =>
                                array(
                                                                   ),
                            'subtypes' =>
                                array(),

                            'url' => 'http://schema.org/Disease',
                            'level' => 1,
                        )
                )

            );


    }


} 