<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for map_elements table in database 
 */
class Model_Leap_Map_Element extends DB_ORM_Model {

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
            
            'mime' => new DB_ORM_Field_String($this, array(
                'max_length' => 20,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            
            'name' => new DB_ORM_Field_String($this, array(
                'max_length' => 200,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            
            'path' => new DB_ORM_Field_String($this, array(
                'max_length' => 300,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            
            'args' => new DB_ORM_Field_String($this, array(
                'max_length' => 100,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
        );
        
        $this->relations = array(
            'map' => new DB_ORM_Relation_BelongsTo($this, array(
                'child_key' => array('map_id'),
                'parent_key' => array('id'),
                'parent_model' => 'map',
            )),
        );
    }

    public static function data_source() {
        return 'default';
    }

    public static function table() {
        return 'map_elements';
    }

    public static function primary_key() {
        return array('id');
    }
    
    public function getImagesByMap($mapId) {
        $builder = DB_SQL::select('default')
                ->from($this->table())
                ->where('map_id', '=', $mapId)
                ->where('mime', 'IN', array('gif', 'jpg', 'jpeg'));
        
        $result = $builder->query();
        
        if($result->is_loaded()) {
            $elements = array();
            foreach($result as $record) {
                $elements[] = DB_ORM::model('map_element', array((int)$record['id']));
            }
            
            return $elements;
        }
        
        return NULL;
    }
}

?>