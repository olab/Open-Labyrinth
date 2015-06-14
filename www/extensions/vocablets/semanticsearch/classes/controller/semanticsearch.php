<?php
require_once(Kohana::find_file('vendor', 'arc2/ARC2'));
require_once(Kohana::find_file('vendor', 'sparqllib'));

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

class Controller_SemanticSearch extends Controller_Base
{

    public function action_index()
    {
        $openView = View::factory('semanticsearch');
        $session = Session::instance();

        $searchResults = $session->get_once('searchResults');
        $searchTerm = $session->get_once('searchTerm');
        $search_error = $session->get_once('search_error');

        if (isset($searchResults)) {
            $this->templateData["searchResults"] = $searchResults;
        }

        if (isset($searchTerm)) {
            $this->templateData["searchTerm"] = $searchTerm;
        }

        if(isset($search_error)){
            $this->templateData["search_error"] = $search_error;
        }
        $openView->set('templateData', $this->templateData);

        $this->templateData['center'] = $openView;
        unset($this->templateData['right']);
        $this->template->set('templateData', $this->templateData);
    }

    private function bioportalSearch($term, $limit = 50)
    {
        $requestFields = array(
            "q" => $term,
            "pagesize" => $limit,
            "apikey" => "1bea74bb-234e-4bd2-b141-86c70591ea46",
            "require_exact_match" => "true"
        );

        $results = $this->execute("http://data.bioontology.org/search", $requestFields);

        return $results;
    }

    public function action_doSearch()
    {
        $session = Session::instance();

        $term = $this->request->post('term', NULL);
        $first_wave = $this->bioportalSearch($term);
        $newTerms = array();
        $first_wave_count = count($first_wave["collection"]);

        for ($i = 0; $i < min(5, $first_wave_count); $i++) {

            $res = $first_wave["collection"][$i];

            if (isset($res["synonym"])) {
                foreach ($res["synonym"] as $synonym) {
                    $newTerms[ucwords($synonym)] = ucwords($synonym);
                }
            }
            $newTerms[ucwords($res["prefLabel"])] = ucwords($res["prefLabel"]);
        }

        $results = $first_wave["collection"];

        foreach ($newTerms as $newTerm) {
            $newWave = $this->bioportalSearch($newTerm, 25);
            $newResults = $newWave["collection"];

            $results = array_merge($results, $newResults);
        }
        $uris = array();

        if(count($results)<1){
            $error = "The term was not found in Biontology";
        }
        else{
            foreach ($results as $result) {
                $uris[$result["@id"]] = "<" . $result["@id"] . ">";
            }

            $baseURL = "http://olabdev.tk/resource/map_node";// URL::base().'resource/map_node';
            $inExpression = implode(",", $uris);

            $sparql =
                "
            # Here you define what variables you want in the output from the combinations that you get inside the where clause{}
            # Graph is the node (because in inline annotation each node has its own graph), subject is the term found anf object is the term's label
            select  ?object ?subject ?graph ?text ?title ?maptitle ?map
            #?graph ?subject ?map
            where {
            #you need to also select the containing graph - see the filter expression below...
            graph ?graph
            {

            # This is your main query predicate: You are looking for a triple into the graph that satisfies this expression: a <subject> has a relation with an <object> and that relation is of the schema name kind
               ?subject <http://schema.org/name> ?object.
            FILTER(?subject IN ($inExpression))
            }.
            # This filter achieves two things: 1)it will only get things under olabdev.tk graph 2) it will only get things under a map_node graph, that is inline annotation stuff
              FILTER(STRSTARTS(STR(?graph), '$baseURL')).
            # This filter throws away empty stuff that should not be there (under investigation)
              FILTER (!isBlank(?subject)).
              ?graph <http://purl.org/dc/terms/isPartOf> ?map.
              ?map <http://purl.org/dc/terms/title> ?maptitle.
              ?graph <http://purl.org/dc/terms/title> ?title.
              ?graph <http://purl.org/dc/elements/1.1/description> ?text.


            }
            ";

            $sparqlResults = $this->doSparql($sparql);
            if(count($sparqlResults)<1){
                $error = "The term was not found among labyrinths";
            }
            $newResults = array();

            foreach($sparqlResults as $sparqlResult){
                if(!isset($newResults[$sparqlResult["map"]])){
                    $newResults[$sparqlResult["map"]]= array(
                        "title" => $sparqlResult["maptitle"],
                        "nodes"=>array()
                    );
                }
                $newResults[$sparqlResult["map"]]["nodes"][] = array(
                    "title" => $sparqlResult["title"],
                    "text" => $sparqlResult["text"],
                    "term" => $sparqlResult["object"],
                    "termURI"=>$sparqlResult["subject"],
                    "uri" => $sparqlResult["graph"]
                );

            }




            $session->set('searchResults', $newResults);
        }
        if(isset($error))
            $session->set('search_error', $error);
        $session->set('searchTerm', $term);





        Request::initial()->redirect(URL::base() . 'semanticsearch/index/');
    }


    protected function execute($url, $values)
    {
        $process = curl_init($url);
        $fields_string = http_build_query($values);

        curl_setopt($process, CURLOPT_HEADER, 0);
        curl_setopt($process, CURLOPT_TIMEOUT, 30);
        curl_setopt($process, CURLOPT_POST, 1);
        curl_setopt($process, CURLOPT_POSTFIELDS, $fields_string);

        curl_setopt($process, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($process, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/x-www-form-urlencoded',
            'Connection: Keep-Alive'
        ));
        $return = curl_exec($process);

        curl_close($process);
        return json_decode($return, true);
    }

    public function action_wheel()
    {

        $allMapsQuery = "select ?map ?name{graph<http://olabdev.tk/>{?map a <http://xmlns.com/foaf/0.1/Document>.?map <http://purl.org/dc/terms/title> ?name}}";
        $allMaps = $this->doSparql($allMapsQuery);


        $map = ($this->request->query("map", null));
        $query = "
            select  count(?object) as ?count ?graph ?graph2 ?title1 ?title2
            where {
            graph ?graph
            {
               ?subject <http://schema.org/name> ?object.
            }.
            graph ?graph2
            {
               ?subject2 <http://schema.org/name> ?object.
            }.
              FILTER(STRSTARTS(STR(?graph), 'http://olabdev.tk/resource/map_node')).
              FILTER (!isBlank(?subject))
              FILTER (?graph!=?graph2)
            ?graph2 <http://purl.org/dc/terms/isPartOf> <$map>.
            ?graph <http://purl.org/dc/terms/isPartOf> <$map>.
            ?graph <http://purl.org/dc/terms/title> ?title1.
            ?graph2 <http://purl.org/dc/terms/title> ?title2.
            }
            Group by ?graph ?graph2  ?title1 ?title2
            having count(?object)>0
        ";

        $sparqlResults = $this->doSparql($query);
        if (count($sparqlResults) < 1) {
            $matrix = array();
            $labels = array();
        } else {
            $index = array();
            $labels = array();
            foreach ($sparqlResults as $result2) {
                if (intval($result2["count"] > 0)) {
                    if (!array_search($result2["graph"], $index)) {
                        $index[] = $result2["graph"];
                        $labels[$result2["graph"]] = $result2["title1"] . " ";

                    }
                    if (!array_search($result2["graph2"], $index)) {
                        $index[] = $result2["graph2"];
                        $labels[$result2["graph2"]] = $result2["title2"] . " ";

                    }
                }

            }
            $index = array_unique($index);
            $index = array_values($index);


            $matrix = array_fill(0, count($index), array_fill(0, count($index), 0));;
            $inverse_index = array_flip($index);

            $labels = array_values(array_intersect_key($labels, $inverse_index));

            foreach ($sparqlResults as $result) {

                $x = $inverse_index[$result{"graph"}];
                $y = $inverse_index[$result{"graph2"}];
                $matrix[$x][$y] = intval($result["count"]);
                $matrix[$y][$x] = intval($result["count"]);
            }
        }


        $this->templateData["map"] = $map;
        $this->templateData["labels"] = $labels;
        $this->templateData["data"] = $matrix;
        $this->templateData["allMaps"] = $allMaps;
        $openView = View::factory('wheel');
        $openView->set('templateData', $this->templateData);

        $this->templateData['center'] = $openView;
        unset($this->templateData['right']);

        $this->template->set('templateData', $this->templateData);
    }


    private function doSparql($query)
    {
        $sparql_config = Kohana::$config->load('sparql');

        $db = sparql_connect($sparql_config["endpoint"]);

        if (!$db) {
            print $db->errno() . ": " . $db->error() . "\n";
            exit;
        }

        $result = $db->query($query);
        if (!$result) {
            print $db->errno() . ": " . $db->error() . "\n";
            exit;
        }

        return $result->fetch_all();
    }

}

