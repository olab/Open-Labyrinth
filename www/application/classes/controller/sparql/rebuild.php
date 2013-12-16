<?php
/**
 * Created by JetBrains PhpStorm.
 * User: larjohns
 * Date: 29/8/2012
 * Time: 2:51 μμ
 * To change this template use File | Settings | File Templates.
 */


/* ARC2 static class inclusion */


require_once(Kohana::find_file('vendor', 'arc2/ARC2'));
class Controller_Sparql_Rebuild extends Controller_Base
{

    public function action_go()
    {
        $external = $this->request->query("external");

        /* instantiation */
        $store = Helper_RDF_Store::getStore();

        $store->reset();

        $metadata = Model_Leap_Metadata::getMetadataByModelName();
        $graph_uri = Model_Leap_Vocabulary::getGraphUri();

        //$triples = array();
        foreach ($metadata as $field) {

            $field_triples = Model_Leap_Metadata_Record::getAllTriples($field->name);
            //$triples = array_merge($triples, $field_triples);

            foreach ($field_triples as $triple) {

                $arc_triple = $triple->toString();

                if($arc_triple['o_type']=='literal'){
                    $extra_graph = $arc_triple['s'];

                    $extra_triples  = $this->discover_triples(html_entity_decode($arc_triple['o']));

                    foreach ($extra_triples as $extra_triple) {
                        $store->insert(array($extra_triple),$extra_graph);
                    }
                }

                $store->insert(array($arc_triple),$graph_uri);
            }
        }

        if($external=="on"){

            $vocabs = Model_Leap_Vocabulary::getAllVocabulary();

            foreach ($vocabs as $vocabulary) {
                $parser = ARC2::getRDFParser();
                $uri_abs = $vocabulary->alternative_source_uri;
                if (!parse_url($vocabulary->alternative_source_uri, PHP_URL_SCHEME) != '')
                    $uri_abs  = Model_Leap_Vocabulary::getGraphUri().$vocabulary->alternative_source_uri;

                $parser->parse($uri_abs);
                $triples = $parser->getTriples();
                $store->insert($triples, $graph_uri);

                $parser = ARC2::getRDFParser();
                $parser->parse($vocabulary->namespace);
                $triples = $parser->getTriples();
                $store->insert($triples, $graph_uri);

            }
            /*        foreach($triples as $triple){
                        echo $triple->toString() . "<br/>";
                    }*/

        }
        $classMappings = Model_Leap_Vocabulary_ClassMapping::getAllClassMappings();

        foreach ($classMappings as $cmapping) {

            $class_triples = $cmapping->getTriples();

            foreach ($class_triples as $triple) {
                $arc_triple = $triple->toString();

                $store->insert(array($arc_triple),$graph_uri);

            }
        }

        $propertyMappings = Model_Leap_Vocabulary_LegacyPropertyMapping::getAllMappings();
        foreach ($propertyMappings as $pmapping) {

            $property_triples = $pmapping->getTriples();

            foreach ($property_triples as $triple) {

                $arc_triple = $triple->toString();
                if($arc_triple['o_type']=='literal'){
                    $extra_graph = $arc_triple['s'];

                    $extra_triples  = $this->discover_triples(html_entity_decode($arc_triple['o']));

                    foreach ($extra_triples as $extra_triple) {
                        $store->insert(array($extra_triple),$extra_graph);
                    }

                }

                $store->insert(array($arc_triple),$graph_uri);
            }

        }

        $openView = View::factory('sparql/rebuildOK');
        // $openView->set('templateData', $this->templateData);

        $this->templateData['center'] = $openView;
        // unset($this->templateData['right']);
        $this->template->set('templateData', $this->templateData);
    }

    private function discover_triples($data){

        $data = '<html>'.$data.'</html>';
        $parser = ARC2::getSemHTMLParser();
        $base = URL::base();
        $parser->parse($base, $data);
        $parser->extractRDF('rdfa');

        $triples = $parser->getTriples();
        return $triples;
    }

    public function action_index(){
        $View = View::factory('sparql/rebuild');
        // $openView->set('templateData', $this->templateData);

        $this->templateData['center'] = $View;
        // unset($this->templateData['right']);
        $this->template->set('templateData', $this->templateData);

    }

    public function before() {
        parent::before();

        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('SPARQL Endpoint Indexing'))->set_url(URL::base() . 'sparql/rebuild'));
        if(Auth::instance()->get_user()==NULL || Auth::instance()->get_user()->type->name != 'superuser') {
            Request::initial()->redirect(URL::base());
        }
        $leftView = View::factory('vocabulary/semanticsMenu');
        $leftView->set('templateData', $this->templateData);

        $this->templateData['title']= "Rebuild SPARQL Endpoint Index";
        $this->templateData['left'] = $leftView;
        unset($this->templateData['right']);
        $this->template->set('templateData', $this->templateData);
    }

}




