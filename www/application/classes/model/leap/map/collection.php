<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for map_collections table in database 
 */
class Model_Leap_Map_Collection extends DB_ORM_Model {

    public function __construct() {
        parent::__construct();

        $this->fields = array(
            'id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
                'unsigned' => TRUE,
            )),
            
            'name' => new DB_ORM_Field_String($this, array(
                'max_length' => 200,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
        );
        
        $this->relations = array(
            'maps' => new DB_ORM_Relation_HasMany($this, array(
                'child_key' => array('collection_id'),
                'child_model' => 'map_collectionMap',
                'parent_key' => array('id'),
            )),
        );
    }

    public static function data_source() {
        return 'default';
    }

    public static function table() {
        return 'map_collections';
    }

    public static function primary_key() {
        return array('id');
    }
    
    public function getAllCollections() {
        $builder = DB_SQL::select('default')->from($this->table());
        $result = $builder->query();
        
        if($result->is_loaded()) {
            $collections = array();
            foreach($result as $record) {
                $collections[] = DB_ORM::model('map_collection', array((int)$record['id']));
            }
            
            return $collections;
        }
        
        return NULL;
    }
}

?>
