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

/**
 * Model for user_responses table in database 
 */
class Model_Leap_User_Response extends DB_ORM_Model {

    public function __construct() {
        parent::__construct();

        $this->fields = array(
            'id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 10,
                'nullable' => FALSE,
            )),
            
            'question_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 10,
                'nullable' => FALSE,
                'unsigned' => TRUE,
            )),
            
            'session_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 10,
                'nullable' => FALSE,
                'unsigned' => TRUE,
            )),
            
            'response' => new DB_ORM_Field_String($this, array(
                'max_length' => 1000,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),

            'node_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 10,
                'nullable' => FALSE,
                'unsigned' => TRUE,
            )),
        );
    }

    public static function data_source() {
        return 'default';
    }

    public static function table() {
        return 'user_responses';
    }

    public static function primary_key() {
        return array('id');
    }
    
    public function createResponse($sessionId, $questionId, $response, $nodeId) {
        $this->question_id = $questionId;
        $this->session_id = $sessionId;
        $this->response = $response;
        $this->node_id = $nodeId;

        $this->save();
    }
    
    public function updateResponse($sessionId, $questionId, $response) {
        $builder = DB_SQL::select('default')
                ->from($this->table())
                ->where('session_id', '=', $sessionId, 'AND')
                ->where('question_id', '=', $questionId);
        $result = $builder->query();
        
        if($result->is_loaded()) {
            $resp = DB_ORM::model('user_response', array((int)$result[0]['id']));
            if($resp) {
                $resp->response = $response;
                $resp->save();
            }
        } else {
            $this->createResponse($sessionId, $questionId, $response);
        }
    }
    
    public function getResponce($sessionId, $questionId) {
        $builder = DB_SQL::select('default')
                ->from($this->table())
                ->where('session_id', '=', $sessionId, 'AND')
                ->where('question_id', '=', $questionId);
        $result = $builder->query();
        
        if($result->is_loaded()) {
            $responces = array();
            foreach($result as $record){
                $responces[] = DB_ORM::model('user_response', array((int)$record['id']));
            }

            return $responces;
        }

        return NULL;
    }

    public function getResponsesByQuestion($questionId) {
        $builder = DB_SQL::select('default')
            ->from($this->table())
            ->where('question_id', '=', $questionId);
        $result = $builder->query();

        if($result->is_loaded()) {
            $responses = array();
            foreach($result as $record){
                $responses[] = DB_ORM::model('user_response', array((int)$record['id']));
            }

            return $responses;
        }

        return NULL;
    }

    public function getResponses($questionId, $sessions) {

        if (!count($sessions)) {
            $sessions = array('');
        }

        $builder = DB_SQL::select('default')
            ->from($this->table())
            ->where('question_id', '=', $questionId, 'AND')
            ->where('session_id', 'IN', $sessions);
        $result = $builder->query();

        if($result->is_loaded()) {
            $responces = array();
            foreach($result as $record){
                $responces[] = DB_ORM::model('user_response', array((int)$record['id']));
            }

            return $responces;
        }

        return NULL;
    }
}

?>