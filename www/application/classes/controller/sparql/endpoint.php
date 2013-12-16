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


    class Controller_Sparql_Endpoint extends Controller_Base
    {

        public function __construct(Request $request, Response $response)
        {
            Helper_RDF_Store::initialize();
            // Assign the request to the controller
            $this->request = $request;
            // Assign a response to the controller
            $this->response = $response;


        }

        public function action_index()
        {
            //$maps = DB_ORM::model('map')->getAllEnabledAndKeyMap();
            //$this->templateData['maps'] = $maps;
            $url_base = URL::base(NULL, TRUE);
            $no_request = empty($_GET) && empty($_POST);
            if ($this->request->query("show_inline") == 1 || $no_request)
                echo "<a href='$url_base'>&lt;&lt; Back to OpenLabyrinth</a>";
            $store = Helper_RDF_Store::getDriver();

            $ep = $store::getEndpoint();

            echo $ep->getResult();


            /* request handling */
            //$ep->go();
        }

        public function before()
        {

            $store = Helper_RDF_Store::getDriver();

            $ep = $store::getEndpoint();

            $ep->handleRequest();


            switch ($this->request->query('output')) {
                case "rdfxml":
                    $this->response->headers('Content-Type', 'application/rdf+xml');
                    break;
                case "json":
                    $this->response->headers('Content - Type', 'application / json');
                    break;
                case "turtle":
                    $this->response->headers('Content - Type', 'text / turtle');
                    break;
                case "xml":
                    $this->response->headers('Content - Type', 'application / xml"');
                    break;
            default:
                    break;

            }
            //$ep->sendHeaders();

    }

    public function after(){}
}
