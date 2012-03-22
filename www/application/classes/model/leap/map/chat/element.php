<?php defined('SYSPATH') or die('No direct script access.');

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
    
}

?>