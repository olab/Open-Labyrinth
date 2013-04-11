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
 * Model for map_chat_elements table in database 
 */
class Model_Leap_Map_Chat_Element extends DB_ORM_Model {

    public function __construct() {
        parent::__construct();

        $this->fields = array(
            'id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
                'unsigned' => TRUE,
            )),
            
            'chat_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            
            'question' => new DB_ORM_Field_Text($this, array(
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            
            'response' => new DB_ORM_Field_Text($this, array(
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            
            'function' => new DB_ORM_Field_String($this, array(
                'max_length' => 10,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
        );
        
        $this->relations = array(
            'chat' => new DB_ORM_Relation_BelongsTo($this, array(
                'child_key' => array('chat_id'),
                'parent_key' => array('id'),
                'parent_model' => 'map_chat',
            )),
        );
    }

    public static function data_source() {
        return 'default';
    }

    public static function table() {
        return 'map_chat_elements';
    }

    public static function primary_key() {
        return array('id');
    }
    
    public function getAllElementsByChatId($chatId) {
        $builder = DB_SQL::select('default')->from($this->table())->where('chat_id', '=', $chatId);
        $result = $builder->query();
        
        if($result->is_loaded()) {
            $elements = array();
            foreach($result as $record) {
                $elements[] = DB_ORM::model('map_chat_element', array((int)$record['id']));
            }
            
            return $elements;
        }
        
        return NULL;
    }
    
    public function deleteElementsByChatId($chatId) {
        $builder = DB_ORM::delete('map_chat_element')->where('chat_id', '=', (int)$chatId);
        $builder->execute();
    }
    
    public function addElement($chatId, $values) {
        $this->chat_id = $chatId;
        $this->question = Arr::get($values, 'question', '');
        $this->response = Arr::get($values, 'response', '');
        $this->function = Arr::get($values, 'counter', '');
        
        $this->save();
    }
        
    public function deleteElemtnsByNumber($chatId, $number) {
        $builder = DB_SQL::select('default')->from($this->table())->where('chat_id', '=', $chatId);
        $result = $builder->query();
        
        if($result->is_loaded()) {
            if($number < count($result) and isset($result[$number])) {
                DB_ORM::model('map_chat_element', array((int)$result[$number]['id']))->delete();
            }
        }
    }
    
    public function updateElementsByChatId($chatId, $chatQuestionCount, $values) {
        $elements = $this->getAllElementsByChatId($chatId);

        for($i = 0; $i < $chatQuestionCount; $i++) {
            if($elements != NULL and $i < count($elements) and isset($elements[$i])) {
                $elements[$i]->question = Arr::get($values, 'question'.($i+1), '');
                $elements[$i]->response = Arr::get($values, 'response'.($i+1), '');
                $elements[$i]->function = Arr::get($values, 'counter'.($i+1), '');
                
                $elements[$i]->save();
            } else {
                $this->addElement($chatId, array(
                    'question' => Arr::get($values, 'question'.($i+1), ''),
                    'response' => Arr::get($values, 'response'.($i+1), ''),
                    'counter' => Arr::get($values, 'counter'.($i+1), ''),
                    ));
            }
        }
    }
    
    public function duplicateElements($fromChatId, $toChatId) {
        $elements = $this->deleteElementsByChatId($fromChatId);
        
        if($elements == null || $toChatId == null || $toChatId <= 0) return;
        
        foreach($elements as $element) {
            $builder = DB_ORM::insert('map_chat_element')
                    ->column('chat_id', $toChatId)
                    ->column('question', $element->question)
                    ->column('response', $element->response)
                    ->column('function', $element->function);
            
            $builder->execute();
        }
    }

    public function exportMVP($chatId) {
        $builder = DB_SQL::select('default')->from($this->table())->where('chat_id', '=', $chatId);
        $result = $builder->query();

        if($result->is_loaded()) {
            $elements = array();
            foreach($result as $record) {
                $elements[] = $record;
            }

            return $elements;
        }

        return NULL;
    }
}

?>