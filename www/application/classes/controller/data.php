<?php
/**
 * Created by JetBrains PhpStorm.
 * User: larjohns
 * Date: 12/11/2012
 * Time: 10:04 μμ
 * To change this template use File | Settings | File Templates.
 */

require_once(Kohana::find_file('vendor', 'arc2/ARC2'));
class Controller_Data extends Controller_Base
{


    public function __construct(Request $request, Response $response)
    {
        // Assign the request to the controller
        $this->request = $request;

        // Assign a response to the controller
        $this->response = $response;
    }

    public function before(){
        $this->response->headers('Content-Type', 'application/rdf+xml');

    }

    public function after(){}

    public function action_index() {
        $id = $this->request->param('id', NULL);
        $type = $this->request->param('type', NULL);

        $store = Helper_RDF_Store::getStore();


        $uri = Model_Leap_Vocabulary::getObjectUri($type,$id);


        $query = "DESCRIBE <$uri>";
        $resp = $store->query($query,"rdfxml");
        $ser = ARC2::getRDFXMLSerializer();
        $rdfxml = $ser->getSerializedIndex($resp['result']);


        $this->response->body($rdfxml);



    }



}
