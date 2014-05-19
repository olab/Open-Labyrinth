<?php
/**
 * Created by PhpStorm.
 * User: larjohns
 * Date: 17/5/2014
 * Time: 3:12 πμ
 */
require_once(Kohana::find_file('vendor', 'arc2/ARC2'));
require_once(Kohana::find_file('vendor', 'easyrdf/lib/EasyRdf'));
require_once(Kohana::find_file('vendor', 'entrez'));

class Model_Mesh_Render
{
    public static function render($params)
    {

        $triples = self::discover_triples($params->text);
        $parser = new EasyRdf_Parser_Rdfa();
        $graph = new EasyRdf_Graph();
        $triples = $parser->parse($graph, $params->text, "rdfa", URL::base());
        //var_dump($graph);
        $resources = $graph->resources();
        $sparql_config = Kohana::$config->load('mesh');

        $sparql = new EasyRdf_Sparql_Client($sparql_config["mesh_endpoint"]);
        if (isset($mesh_config["mesh_graph"]))
            $graph = " FROM <" . $mesh_config["mesh_graph"] . ">";
        else $graph = "";

        $uris = array();
        foreach ($resources as $uri => $resource) {
            //var_dump($resource);
            if ($resource->get("rdf:type") == "http://schema.org/Disease") {

                $uris[] = "<$uri>";
            }
        }
        if(empty($uris))return array();

        $inarray = implode(", ", $uris);


        $sparql_result = $sparql->query(
            "
            select ?s ?label
$graph

{?s rdfs:label ?label.

 FILTER (?s IN($inarray)).
}"
        );

        $terms = array();
        foreach ($sparql_result as $row) {
           $terms[] =  $row->label;
        }

        $termsString = implode(" AND ", $terms );





        $entrez = new Entrez("pubmed");
        $entrez->search($termsString);
        $entrez->fetch(0, 5, 'xml', 'medline');
        $results = $entrez->getResults();

        $readings = array();
        foreach ($results as $result) {
            $readings[] = array(
                "title" => $result["title"],
                "url" => "http://www.ncbi.nlm.nih.gov/pubmed/" . $result["pmid"],
            );
        }

        //return "";
        return $readings;

    }

    private static function discover_triples($data)
    {

        $data = '<html>' . $data . '</html>';
        $parser = ARC2::getSemHTMLParser();
        $base = URL::base();
        $parser->parse($base, $data);
        $parser->extractRDF('rdfa');

        $triples = $parser->getTriples();
        return $triples;
    }
}