<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for map_node_types table in database  
 */
class Model_Leap_Map_Node_Type extends DB_ORM_Model {
    
    public function __construct() {
        parent::__construct();
        
        $this->fields = array(
            'id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
                'unsigned' => TRUE,
            )),
            
            'name' => new DB_ORM_Field_String($this, array(
                'max_length' => 70,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            
            'description' => new DB_ORM_Field_String($this, array(
                'max_length' => 500,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
        );
    }
    
    public static function data_source() {
        return 'default';
    }

    public static function table() {
        return 'map_node_types';
    }

    public static function primary_key() {
        return array('id');
    }
    
    public function getTypeByName($name) {
        $builder = DB_SQL::select('default')->from($this->table())->where('name', '=', $name);
        $result = $builder->query();
        
        if($result->is_loaded()) {
            return DB_ORM::model('map_node_type', array((int)$result[0]['id']));
        }
        
        return NULL;
    }
}

?>
