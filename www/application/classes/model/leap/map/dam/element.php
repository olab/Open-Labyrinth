<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for map_dam_elements table in database 
 */
class Model_Leap_Map_Dam_Element extends DB_ORM_Model {

    public function __construct() {
        parent::__construct();

        $this->fields = array(
            'id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
                'unsigned' => TRUE,
            )),
            
            'dam_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 10,
                'nullable' => FALSE,
                'unsigned' => TRUE,
            )),
            
            'element_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 10,
                'nullable' => FALSE,
                'unsigned' => TRUE,
            )),
            
            'order' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
                'unsigned' => TRUE,
            )),
            
            'element_type' => new DB_ORM_Field_String($this, array(
                'max_length' => 20,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            
            'display' => new DB_ORM_Field_String($this, array(
                'max_length' => 20,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
        );
        
        $this->relations = array(
            'dam' => new DB_ORM_Relation_BelongsTo($this, array(
                'child_key' => array('dam_id'),
                'parent_key' => array('id'),
                'parent_model' => 'map_dam',
            )),
            
            'vpd' => new DB_ORM_Relation_BelongsTo($this, array(
                'child_key' => array('element_id'),
                'parent_key' => array('id'),
                'parent_model' => 'map_vpd',
            )),
            
            'element' => new DB_ORM_Relation_BelongsTo($this, array(
                'child_key' => array('element_id'),
                'parent_key' => array('id'),
                'parent_model' => 'map_element',
            )),
            
            'edam' => new DB_ORM_Relation_BelongsTo($this, array(
                'child_key' => array('element_id'),
                'parent_key' => array('id'),
                'parent_model' => 'map_dam',
            )),
        );
    }

    public static function data_source() {
        return 'default';
    }

    public static function table() {
        return 'map_dam_elements';
    }

    public static function primary_key() {
        return array('id');
    }
    
    public function createNewElement($damId, $elementId, $type) {
        $this->dam_id = $damId;
        $this->element_id = $elementId;
        $this->element_type = $type;
        
        $this->save();
    }
    
    public function updateElement($id, $values) {
        $this->id = $id;
        $this->load();
        
        if($this->is_loaded()) {
            $this->order = Arr::get($values, 'order', $this->order);
            $this->display = Arr::get($values, 'trigger', $this->display);
            
            $this->save();
        }
    }
}

?>