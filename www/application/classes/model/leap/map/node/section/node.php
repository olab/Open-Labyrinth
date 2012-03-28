<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for map_node_section_node table in database 
 */
class Model_Leap_Map_Node_Section_Node extends DB_ORM_Model {

    public function __construct() {
        parent::__construct();

        $this->fields = array(
            'id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
                'unsigned' => TRUE,
            )),
            
            'section_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            
            'node_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            
            'order' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
        );
        
        $this->relations = array(
            'section' => new DB_ORM_Relation_BelongsTo($this, array(
                'child_key' => array('section_id'),
                'parent_key' => array('id'),
                'parent_model' => 'map_node_section',
            )),
            
            'node' => new DB_ORM_Relation_BelongsTo($this, array(
                'child_key' => array('node_id'),
                'parent_key' => array('id'),
                'parent_model' => 'map_node',
            )),
        );
    }

    public static function data_source() {
        return 'default';
    }

    public static function table() {
        return 'map_node_section_nodes';
    }

    public static function primary_key() {
        return array('id');
    }
    
    
    public function addNode($node_id, $section_id) {
        $this->node_id = $node_id;
        $this->section_id = $section_id;
        $this->order = 0;
        
        $this->save();
    }
    
    public function updateNodesOrder($sectionId, $values) {
        $builder = DB_SQL::select('default')->from($this->table())->where('section_id', '=', $sectionId);
        $result = $builder->query();
        
        if($result->is_loaded()) {
            $sections = array();
            foreach($result as $record) {
                $sections[] = DB_ORM::model('map_node_section_node', array((int)$record['id']));
            }

            if(count($sections) > 0) {
                foreach($sections as $section) {
                    $section->order = Arr::get($values, 'node_'.$section->id, $section->order);
                    $section->save();
                }
            }
        }
    }
    
    public function deleteNodeBySection($sectionId, $nodeId) {
        $builder = DB_SQL::delete('default')
                                ->from($this->table())
                                ->where('section_id', '=', $sectionId, 'AND')
                                ->where('node_id', '=', $nodeId);
        $builder->execute();
    }
    
}

?>