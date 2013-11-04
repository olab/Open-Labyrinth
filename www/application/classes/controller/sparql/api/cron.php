<?php
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

class Controller_Sparql_API_cron extends RESTful_Controller
{


    public function __construct(Request $request, Response $response)
    {

        parent::__construct($request, $response);
        $this->_response_types = array('application/json');
    }

    public function action_index()
    {
    }

    public function action_editor()
    {

    }

    private function discover_triples($data)
    {

        $data = '<html>' . $data . '</html>';
        $parser = ARC2::getSemHTMLParser();
        $base = URL::base();
        $parser->parse($base, $data);
        $parser->extractRDF('rdfa');

        $triples = $parser->getTriples();
        return $triples;
    }

    private function metadata($classOffset, $offset, $limit)
    {

        $store = Helper_RDF_Store::getStore();
        $graph_uri = Model_Leap_Vocabulary::getGraphUri();
        $metadata = Model_Leap_Metadata::getMetadataByModelName();
        $total = count($metadata);
        if ($classOffset >= $total) return array("status" => "finished","total"=>$total);

        $field_triples = Model_Leap_Metadata_Record::getAllTriples($metadata[$classOffset]->name, $offset, $limit);
        //$triples = array_merge($triples, $field_triples);

        $arc_triples = array();
        $extra_triples = array();
        foreach ($field_triples as $triple) {
            $arc_triple = $triple->toString();

            if ($arc_triple['o_type'] == 'literal') {
                $extra_graph = $arc_triple['s'];

                $extra_triples = $this->discover_triples(html_entity_decode($arc_triple['o']));
                $store->insert($extra_triples, $extra_graph);
            }
            $arc_triples [] = $arc_triple;
        }
        $res_count = count($arc_triples);
        $extras_count = count($extra_triples);
        if ($res_count > 0) {
            $store->insert($arc_triples, $graph_uri);
            if ($res_count >= $limit)
                return array("status" => "pending","total"=>$total, "extras" => $extras_count, "count" => $res_count, "class" => $metadata[$classOffset]->name);
            else {
                return array("status" => "class", "total"=>$total, "extras" => $extras_count, "class" => $metadata[$classOffset]->name, "count" => $res_count,);
            }
        } else {
            return array("status" => "class", "total"=>$total, "class" => $metadata[$classOffset]->name, "count" => $res_count,);
        }


    }

    private function properties($classOffset, $offset, $limit)
    {
        $store = Helper_RDF_Store::getStore();
        $graph_uri = Model_Leap_Vocabulary::getGraphUri();
        $propertyMappings = Model_Leap_Vocabulary_LegacyPropertyMapping::getAllMappings();
        $total = count($propertyMappings);
        if ($classOffset >= $total) return array("status" => "finished","total"=>$total);
        $property_triples = $propertyMappings[$classOffset]->getTriples($offset, $limit);
        $extra_triples = array();
        $arc_triples = array();
        foreach ($property_triples as $triple) {
            $arc_triple = $triple->toString();

            if ($arc_triple['o_type'] == 'literal') {
                $extra_graph = $arc_triple['s'];

                $extra_triples = $this->discover_triples(html_entity_decode($arc_triple['o']));
                $store->insert($extra_triples, $extra_graph);

            }
            $arc_triples[] = $arc_triple;

        }
        $store->insert($arc_triples, $graph_uri);


        $res_count = count($arc_triples);
        $extras_count = count($extra_triples);
        if ($res_count > 0) {
            $store->insert($arc_triples, $graph_uri);
            if ($res_count >= $limit)
                return array("status" => "pending","total"=>$total, "extras" => $extras_count, "count" => $res_count, "class" => $propertyMappings[$classOffset]->property . "-" . $propertyMappings[$classOffset]->term->name);
            else {
                return array("status" => "class","total"=>$total, "extras" => $extras_count, "class" => $propertyMappings[$classOffset]->property . "-" . $propertyMappings[$classOffset]->term->name, "count" => $res_count,);
            }
        } else {
            return array("status" => "class","total"=>$total, "class" => $propertyMappings[$classOffset]->property . "-" . $propertyMappings[$classOffset]->term->name, "count" => $res_count,);
        }


    }

    private function classes($classOffset, $offset, $limit)
    {

        $store = Helper_RDF_Store::getStore();
        $graph_uri = Model_Leap_Vocabulary::getGraphUri();

        $classMappings = Model_Leap_Vocabulary_ClassMapping::getAllClassMappings();
        $total = count($classMappings);
        if ($classOffset >= $total) return array("status" => "finished","total"=>$total);

        $class_triples = $classMappings[$classOffset]->getTriples($offset, $limit);


        $arc_triples = array();
        foreach ($class_triples as $triple) {
            $arc_triple = $triple->toString();

            $arc_triples[] = $arc_triple;
        }
        $store->insert($arc_triples, $graph_uri);


        $res_count = count($arc_triples);

        if ($res_count > 0) {
            $store->insert($arc_triples, $graph_uri);
            if ($res_count >= $limit)
                return array("status" => "pending","total"=>$total, "count" => $res_count, "class" => $classMappings[$classOffset]->class . "-" . $classMappings[$classOffset]->term->name);
            else {
                return array("status" => "class","total"=>$total, "class" => $classMappings[$classOffset]->class . "-" . $classMappings[$classOffset]->term->name, "count" => $res_count,);
            }
        } else {
            return array("status" => "class", "total"=>$total,"class" => $classMappings[$classOffset]->class . "-" . $classMappings[$classOffset]->term->name, "count" => $res_count,);
        }
    }

    private function vocabs($classOffset)
    {
        $store = Helper_RDF_Store::getStore();
        $graph_uri = Model_Leap_Vocabulary::getGraphUri();


        $vocabs = Model_Leap_Vocabulary::getAllVocabulary();
        $total = count($vocabs);
        if ($classOffset >= $total) return array("status" => "finished","total"=>$total);
        $keys = array_keys($vocabs);
        $vocabulary = $vocabs[$keys[$classOffset]];

        $parser = ARC2::getRDFParser();
        $uri_abs = $vocabulary->alternative_source_uri;
        if (!parse_url($vocabulary->alternative_source_uri, PHP_URL_SCHEME) != '') $uri_abs = Model_Leap_Vocabulary::getGraphUri() . $vocabulary->alternative_source_uri;

        $parser->parse($uri_abs);
        $triples = $parser->getTriples();
        $store->insert($triples, $graph_uri);

        $parser = ARC2::getRDFParser();
        $parser->parse($vocabulary->namespace);
        $triples = $parser->getTriples();
        $store->insert($triples, $graph_uri);

        return array("status" => "class", "class" => $uri_abs,"total"=>$total, "count" => count($triples),);

    }

    public function action_go()
    {


        /* instantiation */
        $store = Helper_RDF_Store::getStore();

        $store->reset();

    }


    public function action_get()
    {


        $action = $this->request->query("action");
        $offset = $this->request->query("offset");
        $classOffset = $this->request->query("classOffset");
        $limit = $this->request->query("limit");
        $result = array();
        //  echo "lol".$this->request->query("action");
        if ($action == "metadata") {
            $result = $this->metadata($classOffset, $offset, $limit);
        } else if ($action == "properties") {
            $result = $this->properties($classOffset, $offset, $limit);
        } else if ($action == "classes") {
            $result = $this->classes($classOffset, $offset, $limit);
        } else if ($action == "vocabs") {
            $result = $this->vocabs($classOffset);
        } else {
            $store = Helper_RDF_Store::getStore();

            $store->reset();

            $vocabs = $this->request->query("vocabs");

            $types = array(
                "classes",
                "properties",
                "metadata"
            );

            if ($vocabs=="true")
                $types[] = "vocabs";

            $result = array("types" => $types);
            //start here

        }


        echo json_encode($result);
        // $editor =
        // echo json_encode(Model_Leap_Metadata_Record::getEditor($this->request->query("metadata")));

    }

    public function rest_output()
    { // some actions..
    }

    public function action_update()
    { // some actions..
    }

    public function action_create()
    { // some actions..
    }

    public function action_delete()
    { // some actions..
    }

}
