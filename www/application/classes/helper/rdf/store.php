<?php
/**
 * Created by JetBrains PhpStorm.
 * User: larjohns
 * Date: 8/12/2012
 * Time: 6:36 μμ
 * To change this template use File | Settings | File Templates.
 */

class Helper_RDF_Store {


    protected  static $endpoint;
    protected static $store;

    public static  function initialize(){


    }

    public static function getEndpoint(){

    }


    public static function getDriver(){
        $sparql_config = Kohana::$config->load('sparql');
        if(isset($sparql_config["driver"]))
            $driver = $sparql_config["driver"];
        if(!isset($driver))$driver = 'Helper_RDF_Store_Arc';
        return $driver;
    }


    public static function getStore(){

        $driver = self::getDriver();

        if(!isset($driver::$store))
            $driver::initialize();
        return $driver::$store;
    }
    private  static function  discover_triples($data)
    {

        $data = '<html>' . $data . '</html>';
        $parser = ARC2::getSemHTMLParser();
        $base = URL::base();
        $parser->parse($base, $data);
        $parser->extractRDF('rdfa');

        $triples = $parser->getTriples();
        return $triples;
    }
    public static function indexModel($model){
        $class = get_class($model);
        $smallName = Model_Leap_Metadata::getSmallModelName($class);
        $store = Helper_RDF_Store::getStore();
        $graph_uri = Model_Leap_Vocabulary::getGraphUri();


        $metadata = Model_Leap_Metadata::getMetadataByModelName($smallName);


        //$triples = array();
        foreach ($metadata as $field) {

            $field_triples = Model_Leap_Metadata_Record::getAllTriples($field->name,0,0,$model->id);
            //$triples = array_merge($triples, $field_triples);

            foreach ($field_triples as $triple) {


                $arc_triple = $triple->toString();

                if($arc_triple['o_type']=='literal'){
                    $extra_graph = $arc_triple['s'];

                    $extra_triples  = self::discover_triples(html_entity_decode($arc_triple['o']));

                    foreach ($extra_triples as $extra_triple) {
                  //      $store->insert(array($extra_triple),$extra_graph);
                    }
                }

                $store->insert(array($arc_triple),$graph_uri);
            }
        }

         $classMappings = Model_Leap_Vocabulary_ClassMapping::get_mapping_by_class($smallName);

        foreach ($classMappings as $cmapping) {

            $class_triples = $cmapping->getTriples(0,0,$model->id);

            foreach ($class_triples as $triple) {
                $arc_triple = $triple->toString();

                $store->insert(array($arc_triple),$graph_uri);

            }
        }

        $propertyMappings = Model_Leap_Vocabulary_LegacyPropertyMapping::get_mappings_by_class($smallName);
        var_dump($propertyMappings);
        foreach ($propertyMappings as $pmapping) {

            $property_triples = $pmapping->getTriples(0,0,$model->id);

            foreach ($property_triples as $triple) {

                $arc_triple = $triple->toString();
                if($arc_triple['o_type']=='literal'){
                    $extra_graph = $arc_triple['s'];

                    $extra_triples  = self::discover_triples(html_entity_decode($arc_triple['o']));

                    foreach ($extra_triples as $extra_triple) {
                        $store->insert(array($extra_triple),$extra_graph);
                    }

                }

                $store->insert(array($arc_triple),$graph_uri);
            }

        }

    }



}
