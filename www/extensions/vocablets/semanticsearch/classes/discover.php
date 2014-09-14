<?php
/**
 * Created by PhpStorm.
 * User: larjohns
 * Date: 26/5/2014
 * Time: 3:43 μμ
 */
require_once(Kohana::find_file('vendor', 'arc2/ARC2'));
require_once(Kohana::find_file('vendor', 'sparqllib'));

class Discover {

    public static  function getLabyrinths(){




        $sparql_config = Kohana::$config->load('sparql');


        if (isset($sparql_config["graph"]))
            $graph = " FROM <" . $sparql_config["graph"] . ">";
        else $graph = "";




        $sparql =
            "
           select ?s  ?t ?l ?title
{
?s <http://purl.org/meducator/ns/subject> ?t.
?s <http://purl.org/meducator/ns/educationalLevel> ?l.
?s <http://purl.org/meducator/ns/title> ?title.
}" ;
        $db = sparql_connect($sparql_config["endpoint"]);

        $result = $db->query($sparql);
        if (!$result) {
            print $db->errno() . ": " . $db->error() . "\n";
            exit;
        }

        $labyrinths_res = $result->fetch_all();


        $labyrinths = array();

        foreach($labyrinths_res as $lr){
            if(isset($lr["t"])){
                if(!isset($labyrinths[$lr["t"]]))
                {
                    $labyrinths[$lr["t"]] = array();
                }
                if(!isset($labyrinths[$lr["t"]][$lr["l"]])){
                    $labyrinths[$lr["t"]][$lr["l"]]= array();
                }

                $labyrinths[$lr["t"]][$lr["l"]][] = array("title"=>$lr["title"],"link"=>$lr["s"]);

            }
        }

         return $labyrinths;
    }
} 