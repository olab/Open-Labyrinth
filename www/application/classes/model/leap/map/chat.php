<?php defined('SYSPATH') or die('No direct script access.');

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
}

?>