<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for map_vpd_types table in database 
 */
class Model_Leap_Map_Vpd_Type extends DB_ORM_Model {

    public function __construct() {
        parent::__construct();

        $this->fields = array(
            'id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 10,
                'nullable' => FALSE,
                'unsigned' => TRUE,
            )),
            
            'name' => new DB_ORM_Field_String($this, array(
                'max_length' => 100,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            
            'label' => new DB_ORM_Field_String($this, array(
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
        return 'map_vpd_types';
    }

    public static function primary_key() {
        return array('id');
    }
    
    public function getAllTypes() {
        $builder = DB_SQL::select('default')->from($this->table());
        $result = $builder->query();
        
        if($result->is_loaded()) {
            $types = array();
            foreach($result as $record) {
                $types[] = DB_ORM::model('map_vpd_type', array((int)$record['id']));
            }
            
            return $types;
        }
        
        return NULL;
    }
    
    public function getTypeIdByName($name) {
        $builder = DB_SQL::select('default')->from($this->table())->where('name', '=', $name);
        $result = $builder->query();
        
        if($result->is_loaded()) {
            return $result[0]['id'];
        }
        
        return NULL;
    }
}

?>