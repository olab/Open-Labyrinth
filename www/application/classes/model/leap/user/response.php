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
            'created_at' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
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
    
    public function createResponse($sessionId, $questionId, $response, $nodeId = null, $created_at = null)
    {
        $sessionObj = DB_ORM::model('user_session', (int)$sessionId);
        $sessionObjId = $sessionObj->id;

        if (empty($sessionId) || empty($sessionObjId) || !empty($sessionObj->end_time)) return false;
        if(empty($created_at)) $created_at = time();

        return DB_ORM::insert('User_Response')
            ->column('question_id', $questionId)
            ->column('session_id', $sessionId)
            ->column('response', $response)
            ->column('node_id', $nodeId)
            ->column('created_at', $created_at)
            ->execute();
    }

    public function createTurkTalkResponse($sessionId, $questionId, $response, $chat_session_id, $isLearner = false, $nodeId = null, $created_at = null)
    {
        $json_response = array();
        $role = ($isLearner) ? 'learner' : 'turker';
        if($isLearner) {
            $responses = $this->getTurkTalkResponse($questionId, $sessionId, $chat_session_id);

            if (empty($responses)) {
                //TODO: save initial question
                //$initial_question = '';
                //$this->createResponse($sessionId, $questionId, $initial_question, $nodeId, $created_at);
            }
        }

        $json_response[$chat_session_id] = array('role'=>$role, 'text'=>$response);
        $json_response = json_encode($json_response);

        $this->createResponse($sessionId, $questionId, $json_response, $nodeId, $created_at);
    }

    public function getTurkTalkResponse($question_id, $session_id, $chat_session_id = null)
    {
        $result = array();
        $obj_responses = $this->getResponses($question_id, array($session_id));
        if(!empty($obj_responses)){
            $responses = array();
            foreach($obj_responses as $obj_response){
                $response = json_decode($obj_response->response, true);
                $key = key($response);
                $responses[$key][] = array_pop($response);
            }

            if(empty($chat_session_id)) {
                $last_chat_session_id = array_keys($responses);
                $last_chat_session_id = max($last_chat_session_id);
                $chat_session_id = $last_chat_session_id;
            }
            if(isset($responses[$chat_session_id])) {
                $result = $responses[$chat_session_id];
            }
        }
        return $result;
    }

    public function getTurkTalkLastChatId($question_id, $session_id)
    {
        $result = null;
        $obj_responses = $this->getResponses($question_id, array($session_id));
        if(!empty($obj_responses)){
            $responses = array();
            foreach($obj_responses as $obj_response){
                $response = json_decode($obj_response->response, true);
                $responses[] = key($response);
            }

            $result = max($responses);
        }
        return $result;
    }

    public function updateById($id, $response)
    {
        DB_ORM::update('User_Response')->set('response', $response)->where('id', '=', $id)->execute();
    }
    
    public function updateResponse($sessionId, $questionId, $response, $nodeId)
    {
        $result = DB_ORM::select('User_Response')->where('session_id', '=', $sessionId)->where('question_id', '=', $questionId)->query()->fetch(0);

        if( ! $result) $this->createResponse($sessionId, $questionId, $response, $nodeId);

        $result->response = $response;
        $result->node_id = $nodeId;
        $result->save();
    }
    
    public function getResponse ($sessionId, $questionId, $nodesId = array(), $orderBy = 'ASC')
    {
        if (count($nodesId) > 0) {
            $result = array();
            foreach ($nodesId as $nodeId) {
                $response = DB_ORM::select('user_response')
                    ->where('session_id', '=', $sessionId)
                    ->where('question_id', '=', $questionId)
                    ->where('node_id', '=', $nodeId)
                    ->order_by('id', $orderBy)
                    ->query()
                    ->fetch(0);

                if ($response) $result[] = $response;
            }
            return $result;
        } else {
            return DB_ORM::select('user_response')
                ->where('session_id', '=', $sessionId)
                ->where('question_id', '=', $questionId)
                ->order_by('id', $orderBy)
                ->query()
                ->as_array();
        }
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

    public function getResponsesBySessionAndNode($session_id, $node_id){
        return DB_ORM::select('user_response')
            ->where('session_id', '=', $session_id)
            ->where('node_id', '=', $node_id)
            ->query()
            ->as_array();
    }

    public function getResponses($questionId, $sessions, $orderBy = 'ASC')
    {
        if (!count($sessions)) $sessions = array('');

        $result = DB_SQL::select('default')
            ->from($this->table())
            ->where('question_id', '=', $questionId)
            ->where('session_id', 'IN', $sessions)
            ->order_by('id', $orderBy)
            ->query();

        if($result->is_loaded()) {
            $responses = array();
            foreach($result as $record){
                $responses[] = DB_ORM::model('user_response', array((int)$record['id']));
            }
            return $responses;
        }

        return NULL;
    }

    public function getResponsesBySessionID($sessionId)
    {
        $result    = DB_SQL::select('default')->from($this->table())->where('session_id', '=', $sessionId)->query();
        $responses = array();

        if ($result->is_loaded()) {
            foreach($result as $record){
                $responses[] = $record;
            }
        }

        return $responses;
    }

    public function sjtConvertResponse ($response) {
        $result = '';
        foreach(json_decode($response) as $responseId){
            $result .= DB_ORM::model('Map_Question_Response', array($responseId))->response.',';
        }
        return trim($result, ',');
    }
}