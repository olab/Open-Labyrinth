<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for map_node_sections table in database 
 */
class Model_Leap_Map_Node_Section extends DB_ORM_Model {

    public function __construct() {
        parent::__construct();

        $this->fields = array(
            'id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
                'unsigned' => TRUE,
            )),
            
            'name' => new DB_ORM_Field_String($this, array(
                'max_length' => 50,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            
            'map_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
        );
        
        $this->relations = array(
            'map' => new DB_ORM_Relation_BelongsTo($this, array(
                'child_key' => array('map_id'),
                'parent_key' => array('id'),
                'parent_model' => 'map',
            )),
            
            'nodes' => new DB_ORM_Relation_HasMany($this, array(
                'child_key' => array('section_id'),
                'child_model' => 'map_node_section_node',
                'parent_key' => array('id'),
                'options' => array(
                    array('order_by', array('order', 'ASC')),
                ),
            )),
        );
    }

    public static function data_source() {
        return 'default';
    }

    public static function table() {
        return 'map_node_sections';
    }

    public static function primary_key() {
        return array('id');
    }
    
    public function getAllSectionsByMap($mapId) {
        $builder = DB_SQL::select('default')->from($this->table())->where('map_id', '=', (int)$mapId);
        $result = $builder->query();
        
        if($result->is_loaded()) {
            $sections = array();
            foreach($result as $record) {
                $sections[] = DB_ORM::model('map_node_section', array((int)$record['id']));
            }
            
            return $sections;
        }
        
        return NULL;
    }
    
    public function createSection($mapId, $values) {
        $this->map_id = $mapId;
        $this->name = Arr::get($values, 'sectionname', '');
        $this->save();
    }
    
    public function updateSectionName($id, $values) {
        $this->id = $id;
        $this->load();
        
        $this->name = Arr::get($values, 'sectiontitle', $this->name);
        $this->save();
    }
    
    public function deleteSection($sectionId) {
        $this->id = $sectionId;
        $this->delete();
        
        $tableName = DB_ORM::model('map_node_section_node');
        $builder = DB_SQL::delete('default')->from($tableName::table())->where('section_id', '=', $sectionId);
        $builder->execute();
    }
    
    public function getSectionsByMapId($mapId) {
        $builder = DB_SQL::select('default')->from($this->table())->where('map_id', '=', $mapId)->order_by('id');
        $result = $builder->query();
        
        if($result->is_loaded()) {
            $sections = array();
            foreach($result as $record) {
                $sections[] = DB_ORM::model('map_node_section', array((int)$record['id']));
            }
            
            return $sections;
        }
        
        return NULL;
    }
}

?>