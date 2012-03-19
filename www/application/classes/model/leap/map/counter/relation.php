<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for map_counter_rule_relations table in database 
 */
class Model_Leap_Map_Counter_Relation extends DB_ORM_Model {

    public function __construct() {
        parent::__construct();

        $this->fields = array(
            'id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
                'unsigned' => TRUE,
            )),
            
            'title' => new DB_ORM_Field_String($this, array(
                'max_length' => 70,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            
            'value' => new DB_ORM_Field_String($this, array(
                'max_length' => 50,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
        );
    }

    public static function data_source() {
        return 'default';
    }

    public static function table() {
        return 'map_counter_rule_relations';
    }

    public static function primary_key() {
        return array('id');
    }
    
    
    public function getAllRealtions() {
        $builder = DB_SQL::select('default')->from($this->table());
        $result = $builder->query();
        
        if($result->is_loaded()) {
            $relations = array();
            foreach($result as $record) {
                $relations[] = DB_ORM::model('map_counter_relation', array((int)$record['id']));
            }
            
            return $relations;
        }
        
        return NULL;
    }
}

?>