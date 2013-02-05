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
 * Model for map_chats table in database 
 */
class Model_Leap_Map_Chat extends DB_ORM_Model {

    public function __construct() {
        parent::__construct();

        $this->fields = array(
            'id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
                'unsigned' => TRUE,
            )),
            
            'map_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            
            'counter_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            
            'stem' => new DB_ORM_Field_Text($this, array(
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
        );
        
        $this->relations = array(
            'counter' => new DB_ORM_Relation_BelongsTo($this, array(
                'child_key' => array('counter_id'),
                'parent_key' => array('id'),
                'parent_model' => 'map_counter',
            )),
            
            'map' => new DB_ORM_Relation_BelongsTo($this, array(
                'child_key' => array('map_id'),
                'parent_key' => array('id'),
                'parent_model' => 'map',
            )),
            
            'elements' => new DB_ORM_Relation_HasMany($this, array(
                'child_key' => array('chat_id'),
                'child_model' => 'map_chat_element',
                'parent_key' => array('id'),
            )),
        );
    }

    public static function data_source() {
        return 'default';
    }

    public static function table() {
        return 'map_chats';
    }

    public static function primary_key() {
        return array('id');
    }
    
    public function getChatsByMap($mapId) {
        $builder = DB_SQL::select('default')
                ->from($this->table())
                ->where('map_id', '=', $mapId);
        
        $result = $builder->query();
        
        if($result->is_loaded()) {
            $chats = array();
            foreach($result as $record) {
                $chats[] = DB_ORM::model('map_chat', array((int)$record['id']));
            }
            
            return $chats;
        }
        
        return NULL;
    }
    
    public function addChat($mapId, $countOfQuestions, $values) {
        $builder = DB_ORM::insert('map_chat')
                ->column('map_id', $mapId)
                ->column('stem', Arr::get($values, 'cStem', ''))
                ->column('counter_id', Arr::get($values, 'scount', 0));
        $newChatId = $builder->execute();
        
        for($i = 1; $i <= $countOfQuestions; $i++) {
            $element = DB_ORM::model('map_chat_element');
            $element->chat_id = $newChatId;
            $element->question = Arr::get($values, 'question'.$i, '');
            $element->response = Arr::get($values, 'response'.$i, '');
            $element->function = Arr::get($values, 'counter'.$i, '');
            
            $element->save();
        }
    }
    
    public function updateChat($chatId, $chatQuestionCount, $values) {
        $this->id = $chatId;
        $this->load();
        
        $this->stem = Arr::get($values, 'cStem', $this->stem);
        $this->counter_id = Arr::get($values, 'scount', $this->counter_id);
        $this->save();
        
        DB_ORM::model('map_chat_element')->updateElementsByChatId($chatId, $chatQuestionCount, $values);
    }
    
    public function duplicateChats($fromMapId, $toMapId, $counterMap) {
        $chats = $this->getChatsByMap($fromMapId);
        
        if($chats == null || $toMapId == null || $toMapId <= 0) return array();
        
        $chatMap = array();
        foreach($chats as $chat) {
            $builder = DB_ORM::insert('map_chat')
                    ->column('map_id', $toMapId)
                    ->column('stem', $chat->stem);
            if(isset($counterMap[$chat->counter_id]))
                $builder = $builder->column ('counter_id', $counterMap[$chat->counter_id]);

            $chatMap[$chat->id] = $builder->execute();
            
            DB_ORM::model('map_chat_element')->duplicateElements($chat->id, $chatMap[$chat->id]);
        }
        
        return $chatMap;
    }
}

?>