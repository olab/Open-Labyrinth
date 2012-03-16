<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for map_skins table in database 
 */
class Model_Leap_Map_Skin extends DB_ORM_Model {

    public function __construct() {
        parent::__construct();

        $this->fields = array(
            'id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
                'unsigned' => TRUE,
            )),
            
            'name' => new DB_ORM_Field_String($this, array(
                'max_length' => 100,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            
            'path' => new DB_ORM_Field_String($this, array(
                'max_length' => 200,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
        );
    }

    public static function data_source() {
        return 'default';
    }

    public static function table() {
        return 'map_skins';
    }

    public static function primary_key() {
        return array('id');
    }
    
    public function getAllSkinsId() {
        $builder = DB_SQL::select('default')->from($this->table())->column('id');
        $result = $builder->query();
        
        
        $ids = array();
        if ($result->is_loaded()) {
            foreach ($result as $record) {
                $ids[] = (int)$record['id'];
            }
        }

        return $ids;
    }
    
    public function getAllSkins() {
        $result = array();
        $ids = $this->getAllSkinsId();
        
        foreach($ids as $id) {
            $result[] = DB_ORM::model('map_skin', array($id));
        }
        
        return $result;
    }
}

?>