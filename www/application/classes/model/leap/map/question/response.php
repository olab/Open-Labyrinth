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
 * Model for map_question_responces table in database 
 */
class Model_Leap_Map_Question_Response extends DB_ORM_Model {

    public function __construct() {
        parent::__construct();

        $this->fields = array(
            'id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
                'unsigned' => TRUE,
            )),
            
            'question_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            
            'response' => new DB_ORM_Field_String($this, array(
                'max_length' => 250,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            
            'feedback' => new DB_ORM_Field_Text($this, array(
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            
            'is_correct' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 4,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            
            'score' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
        );
    }

    public static function data_source() {
        return 'default';
    }

    public static function table() {
        return 'map_question_responses';
    }

    public static function primary_key() {
        return array('id');
    }

    public function addFullResponses($questionId, $values){
        $this->question_id = $questionId;
        $this->response = Arr::get($values, 'response', '');
        $this->feedback = Arr::get($values, 'feedback', '');
        $this->is_correct = Arr::get($values, 'is_correct', 0);
        $this->score = Arr::get($values, 'score', 0);

        $this->save();
        return true;
    }

    public function getResponsesByQuestion($questionId) {
        $builder = DB_SQL::select('default')->from($this->table())->where('question_id', '=', (int)$questionId);
        $result = $builder->query();
        
        if($result->is_loaded()) {
            $responses = array();
            foreach($result as $record) {
                $responses[] = DB_ORM::model('map_question_response', array((int)$record['id']));
            }
            
            return $responses;
        }
        
        return NULL;
    }
    
    public function updateResponses($questionId, $values) {
        $responses = $this->getResponsesByQuestion($questionId);
        
        if($responses != NULL && count($responses) > 0) {
            foreach($responses as $response) {
                if(isset($values['response_' . $response->id])) {
                    $response->response = Arr::get($values, 'response_' . $response->id, '');
                    $response->feedback = Arr::get($values, 'feedback_' . $response->id, '');
                    $response->is_correct = Arr::get($values, 'correctness_' . $response->id, 0);
                    $response->score = Arr::get($values, 'score_' . $response->id, 0);
                    
                    $response->save();
                } else {
                    $response->delete();
                }
            }
        }
        
        $newResponses = array();
        foreach($values as $key => $value) {
            if(!(strpos($key, 'response_') === FALSE) && !(strpos($key, '_n') === FALSE)) {
                $id = str_replace('response_', '', str_replace('_n', '', $key));
                if(strlen($id) > 0) $newResponses[$id]['response'] = $value;
            } else if(!(strpos($key, 'feedback_') === FALSE ) && !(strpos($key, '_n') === FALSE)) {
                $id = str_replace('feedback_', '', str_replace('_n', '', $key));
                if(strlen($id) > 0) $newResponses[$id]['feedback'] = $value;
            } else if(!(strpos($key, 'correctness_') === FALSE ) && !(strpos($key, '_n') === FALSE)) {
                $id = str_replace('correctness_', '', str_replace('_n', '', $key));
                if(strlen($id) > 0) $newResponses[$id]['correctness'] = $value;
            } else if(!(strpos($key, 'score_') === FALSE ) && !(strpos($key, '_n') === FALSE)) {
                $id = str_replace('score_', '', str_replace('_n', '', $key));
                if(strlen($id) > 0) $newResponses[$id]['score'] = $value;
            }
        }

        if(count($newResponses) > 0) {
            foreach($newResponses as $response) {
                DB_ORM::insert('map_question_response')
                        ->column('question_id', $questionId)
                        ->column('response', Arr::get($response, 'response', ''))
                        ->column('feedback', Arr::get($response, 'feedback', ''))
                        ->column('is_correct', Arr::get($response, 'correctness', 0))
                        ->column('score', Arr::get($response, 'score', 0))
                        ->execute();
            }
        }
    }
    
    public function deleteByQuestion($questionId) {
        $builder = DB_ORM::delete('map_question_response')->where('question_id', '=', (int)$questionId);
        $builder->execute();
    }
    
    public function duplicateResponses($fromQuestionId, $toQuestionId) {
        $resp = $this->getResponsesByQuestion($fromQuestionId);
        
        if($resp == null || $toQuestionId == null || $toQuestionId <= 0) return;
        
        foreach($resp as $r) {
            $builder = DB_ORM::insert('map_question_response')
                    ->column('question_id', $toQuestionId)
                    ->column('response', $r->response)
                    ->column('feedback', $r->feedback)
                    ->column('is_correct', $r->is_correct)
                    ->column('score', $r->score);
            
            $builder->execute();
        }
    }

    public function exportMVP($questionId) {
        $builder = DB_SQL::select('default')->from($this->table())->where('question_id', '=', (int)$questionId);
        $result = $builder->query();

        if($result->is_loaded()) {
            $responses = array();
            foreach($result as $record) {
                $responses[] = $record;
            }

            return $responses;
        }

        return NULL;
    }
}

?>