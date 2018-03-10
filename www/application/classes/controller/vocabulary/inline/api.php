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

class Controller_Vocabulary_Inline_Api extends RESTful_Controller
{


    public function __construct(Request $request, Response $response)
    {

        parent::__construct($request, $response);
        $this->_response_types = array('application/json');
    }







    public function action_get()
    {

        $query = $this->request->query('query');

        switch ($query) {
            case "name":
            {
                $type = $this->request->query('type');
                $term = $this->request->query('term');
                echo json_encode(Model_Leap_Vocabulary_EntityType::getSuggestion($term,$type));


            }
                break;
            case "types":
            {
                $dummyConfig = Helper_Model_AnnotatedEntity::generateDummyEntities();
                $vocabletsConfig = Model_Leap_Vocabulary_EntityType::getConfig();
                $config = array_merge_recursive($dummyConfig,$vocabletsConfig);
                echo json_encode($config);
            }
                break;

        }


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
