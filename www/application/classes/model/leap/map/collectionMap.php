<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for map_collectionMaps table in database 
 */
class Model_Leap_Map_CollectionMap extends DB_ORM_Model {

    public function __construct() {
        parent::__construct();

        $this->fields = array(
            'id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
                'unsigned' => TRUE,
            )),
            
            'collection_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 10,
                'nullable' => FALSE,
                'unsigned' => TRUE,
            )),
            
            'map_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 10,
                'nullable' => FALSE,
                'unsigned' => TRUE,
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
        return 'map_collectionMaps';
    }

    public static function primary_key() {
        return array('id');
    }
    
    public function deleteByIDs($collectionId, $mapId) {
        DB_ORM::delete('map_collectionMap')
                ->where('map_id', '=', $mapId)
                ->where('collection_id', '=', $collectionId)
                ->execute();
    }
    
    public function getAllNotAddedMaps($collectionId) {
        $builder = DB_SQL::select('default')->from($this->table())
                ->where('collection_id', '=', $collectionId);
        $result = $builder->query();

        if($result->is_loaded()) {
            $mapIDs = array();
            foreach($result as $record) {
                $mapIDs[] = (int)$record['map_id'];
            }

            if(count($mapIDs) > 0) {
                return DB_ORM::model('map')->getMaps($mapIDs);
            } else {
                return DB_ORM::model('map')->getAllMap();
            }
            
            return NULL;
        }
        return DB_ORM::model('map')->getAllMap();
    }
}

?>
