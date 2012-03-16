<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for map_nodes table in database 
 */
class Model_Leap_Map_Node extends DB_ORM_Model {

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
            
            'title' => new DB_ORM_Field_String($this, array(
                'max_length' => 200,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            
            'text' => new DB_ORM_Field_Text($this, array(
                'nullable' => TRUE,
                'savable' => TRUE,
            )),
            
            'content' => new DB_ORM_Field_Text($this, array(
                'nullable' => TRUE,
                'savable' => TRUE,
            )),
            
            'type_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            
            'probability' => new DB_ORM_Field_Boolean($this, array(
                'default' => FALSE,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            
            'conditional' => new DB_ORM_Field_String($this, array(
                'max_length' => 500,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            
            'conditional_message' => new DB_ORM_Field_String($this, array(
                'max_length' => 1000,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            
            'info' => new DB_ORM_Field_String($this, array(
                'max_length' => 1000,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            
            'link_style_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            
            'priority_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            
            'kfp' => new DB_ORM_Field_Boolean($this, array(
                'default' => FALSE,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            
            'undo' => new DB_ORM_Field_Boolean($this, array(
                'default' => FALSE,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            
            'x' => new DB_ORM_Field_Double($this, array(
                'nullable' => TRUE,
                'savable' => TRUE,
            )),
            
            'y' => new DB_ORM_Field_Double($this, array(
                'nullable' => TRUE,
                'savable' => TRUE,
            )),
            
            'rgb' => new DB_ORM_Field_String($this, array(
                'max_length' => 8,
                'nullable' => TRUE,
                'savable' => TRUE,
            )),
        );
        
        $this->relations = array(
            'map' => new DB_ORM_Relation_BelongsTo($this, array(
                'child_key' => array('map_id'),
                'parent_key' => array('id'),
                'parent_model' => 'map',
            )),
            
            'type' => new DB_ORM_Relation_BelongsTo($this, array(
                'child_key' => array('type_id'),
                'parent_key' => array('id'),
                'parent_model' => 'map_node_type',
            )),
            
            'link_style' => new DB_ORM_Relation_BelongsTo($this, array(
                'child_key' => array('link_style_id'),
                'parent_key' => array('id'),
                'parent_model' => 'map_node_link_style',
            )),
            
            'priority' => new DB_ORM_Relation_BelongsTo($this, array(
                'child_key' => array('priority_id'),
                'parent_key' => array('id'),
                'parent_model' => 'map_node_priority',
            )),
        );
    }

    public static function data_source() {
        return 'default';
    }

    public static function table() {
        return 'map_nodes';
    }

    public static function primary_key() {
        return array('id');
    }
    
}

?>