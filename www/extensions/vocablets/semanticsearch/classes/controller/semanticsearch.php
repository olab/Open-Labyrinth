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

class Controller_SemanticSearch extends Controller_Base {

    public function action_index()
    {

            $data = array();

        $openView = View::factory('semanticsearch');


        $session = Session::instance();

        $searchResults = $session->get_once('searchResults');
        $searchTerm = $session->get_once('searchTerm');

        if(isset($searchResults)){
            $this->templateData["searchResults"] = $searchResults;
            $this->templateData["searchTerm"] = $searchTerm;

           //var_dump($this->templateData["searchResults"][0]);die;
        }


        $openView->set('templateData', $this->templateData);

        $this->templateData['center'] = $openView;
        unset($this->templateData['right']);
        $this->template->set('templateData', $this->templateData);


    }

    private function bioportalSearch($term, $limit = 50){


        $requestFields = array(
            "q"=>$term,
            "pagesize"=>$limit,
            "apikey"=>"1bea74bb-234e-4bd2-b141-86c70591ea46",
            "require_exact_match" =>"true"
        );

        $results = $this->execute("http://data.bioontology.org/search", $requestFields);

        return $results;
    }

    public function action_doSearch(){

        $term = $this->request->post('term', NULL);

        $first_wave = $this->bioportalSearch($term);

        $newTerms = array();

        $first_wave_count = count($first_wave["collection"]);

        for($i = 0 ; $i<min(5, $first_wave_count); $i++){

            $res =  $first_wave["collection"][$i];

            if(isset($res["synonym"])){
                foreach($res["synonym"] as $synonym){
                    $newTerms[ucwords($synonym)]= ucwords($synonym);
                }
            }

            $newTerms[ucwords($res["prefLabel"])] = ucwords($res["prefLabel"]);


        }




        $results = $first_wave["collection"];

        foreach($newTerms as $newTerm){
            $newWave = $this->bioportalSearch($newTerm,25);
            $newResults = $newWave["collection"];
           var_dump($newTerm);
            var_dump($newResults);

            $results = array_merge($results, $newResults);
        }

      //  var_dump($results);


        $uris = array();

        foreach($results as $result){
            $uris[$result["@id"]]= "<".$result["@id"].">";
        }

        $baseURL = "http://olabdev.tk/resource/map_node";// URL::base().'resource/map_node';
        $inExpression = implode(",",$uris);

     //  var_dump($results);die;
        $sparql =
            "
            # Here you define what variables you want in the output from the combinations that you get inside the where clause{}
            # Graph is the node (because in inline annotation each node has its own graph), subject is the term found anf object is the term's label
            select  ?object ?subject ?graph ?text
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

              ?graph <http://purl.org/dc/elements/1.1/description> ?text.


            }
            ";

       // var_dump($sparql);

        $session = Session::instance();

        $sparqlResults  = $this->doSparql($sparql);



        $session->set('searchResults',$sparqlResults);
        $session->set('searchTerm',$term);

        Request::initial()->redirect(URL::base() . 'semanticsearch/index/' );

    }




    protected function execute($url, $values){
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


        $openView = View::factory('wheel');
        $openView->set('templateData', $this->templateData);

        $this->templateData['center'] = $openView;
        unset($this->templateData['right']);
        $this->template->set('templateData', $this->templateData);


    }


    private function doSparql($query){
        $self = get_called_class();
        $sparql_config = Kohana::$config->load('sparql');
        $graph_uri = Model_Leap_Vocabulary::getGraphUri();

        $db = sparql_connect($sparql_config["endpoint"]);

        if (!$db) {
            print $db->errno() . ": " . $db->error() . "\n";
            exit;
        }


        // var_dump($sparql);
        $result = $db->query($query);
        if (!$result) {
            print $db->errno() . ": " . $db->error() . "\n";
            exit;
        }


        return $result->fetch_all();



    }


}

