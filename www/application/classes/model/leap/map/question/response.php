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
            
            'is_correct' => new DB_ORM_Field_Boolean($this, array(
                'default' => TRUE,
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
        
        if($responses != NULL) {
            for($i = 0; $i < count($responses); $i++) {
                $responses[$i]->response = Arr::get($values, 'qresp'.($i + 1).'t', '');
                $responses[$i]->feedback = Arr::get($values, 'qfeed'.($i + 1), '');
                $responses[$i]->is_correct = Arr::get($values, 'qresp'.($i + 1).'y', 0);
                $responses[$i]->score = Arr::get($values, 'qresp'.($i + 1).'s', 0);
            
                $responses[$i]->save();
            }
        }
    }
    
    public function deleteByQuestion($questionId) {
        $builder = DB_ORM::delete('map_question_response')->where('question_id', '=', (int)$questionId);
        $builder->execute();
    }
 
}

?>