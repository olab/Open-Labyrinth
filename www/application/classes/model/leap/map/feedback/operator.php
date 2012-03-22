<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for map_feedback_operators table in database 
 */
class Model_Leap_Map_Feedback_Operator extends DB_ORM_Model {

    public function __construct() {
        parent::__construct();

        $this->fields = array(
            'id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
                'unsigned' => TRUE,
            )),
            
            'title' => new DB_ORM_Field_String($this, array(
                'max_length' => 100,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            
            'value' => new DB_ORM_Field_String($this, array(
                'max_length' => 20,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
        );
    }

    public static function data_source() {
        return 'default';
    }

    public static function table() {
        return 'map_feedback_operators';
    }

    public static function primary_key() {
        return array('id');
    }
    
    public function getAllOperators() {
        $builder = DB_SQL::select('default')->from($this->table());
        $result = $builder->query();
        
        if($result->is_loaded()) {
            $operators = array();
            foreach($result as $record) {
                $operators[] = DB_ORM::model('map_feedback_operator', array((int)$record['id']));
            }
            
            return $operators;
        }
        
        return NULL;
    }
    
}

?>