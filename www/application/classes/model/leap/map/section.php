<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for map_sections table in database 
 */
class Model_Leap_Map_Section extends DB_ORM_Model {

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
            'description' => new DB_ORM_Field_String($this, array(
                'max_length' => 700,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
        );
    }

    public static function data_source() {
        return 'default';
    }

    public static function table() {
        return 'map_sections';
    }

    public static function primary_key() {
        return array('id');
    }
    
    public function getAllSectionsId() {
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
    
    public function getAllSections() {
        $result = array();
        $ids = $this->getAllSectionsId();
        foreach($ids as $id) {
            $result[] = DB_ORM::model('map_section', array($id));
        }
        
        return $result;
    }
}

?>
