<?php
/**
 * Open Labyrinth [ http://www.openlabyrinth.ca ]
 *
 * Open Labyrinth is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Open Labyrinth is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Open Labyrinth.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @copyright Copyright 2012 Open Labyrinth. All Rights Reserved.
 *
 */
defined('SYSPATH') or die('No direct script access.');

/**
 * Model for map_references table in database 
 */

class Model_Leap_Map_Node_Reference extends DB_ORM_Model {
    
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
                'unsigned' => TRUE,
            )),
            
            'node_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            
            'element_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            
            'type' => new DB_ORM_Field_String($this, array(
                'max_length' => 10,
                'nullable' => TRUE,
                'savable' => TRUE,
            ))
        );
        
        $this->relations = array(
            'nodes' => new DB_ORM_Relation_BelongsTo($this, array(
                'child_key' => array('node_id'),
                'parent_key' => array('id'),
                'parent_model' => 'map_node_reference',
            ))
        );
    }

    public static function data_source() {
        return 'default';
    }

    public static function table() {
        return 'map_node_references';
    }

    public static function primary_key() {
        return array('id');
    }
    
    public function addReference($nodeId, $values) {
        return DB_ORM::insert('map_node_reference')
                       ->column('map_id', Arr::get($values, 'map_id', 0))
                       ->column('node_id', $nodeId)
                       ->column('element_id', Arr::get($values, 'element_id', 0))
                       ->column('type', Arr::get($values, 'type', ''))
                       ->execute();
    }
    
    public function deleteByNodeId($mapId, $nodeId) {       
        DB_SQL::delete ('default')->from($this->table())->where('map_id', '=', $mapId, 'AND')->where('node_id', '=', $nodeId)->execute();
    }
    
    public function deleteById($Id) {       
        DB_SQL::delete ('default')->from($this->table())->where('id', '=', $Id)->execute();
    }
    
    public function getByElementType($elementId, $type) {
        
        $builder = DB_SQL::select('default')->from($this->table())
                ->where('element_id', '=', $elementId, 'AND')
                ->where('type', '=', $type);
                $result = $builder->query();
        if ($result->is_loaded())
        {
            $elements = array();
            foreach ($result as $record) $elements[] = DB_ORM::model('map_node_reference', array((int)$record['id']));
            return $elements;
        }
        return NULL; 
    }

    public function getNotParent($mapId, $elementId, $type) {
        $builder = DB_SQL::select('default')->from($this->table())
            ->where('map_id', '!=', $mapId, 'AND')
            ->where('element_id', '=', $elementId, 'AND')
            ->where('type', '=', $type);
        $result = $builder->query();
        if ($result->is_loaded())
        {
            $elements = array();
            foreach ($result as $record) $elements[] = DB_ORM::model('map_node_reference', array((int)$record['id']));
            return $elements;
        }
        return NULL;
    }
    
    public function getAllRecords() { 
        $builder = DB_SQL::select('default')->from($this->table());
        $result = $builder->query();
        if ($result->is_loaded()){
            $elements = array();
            foreach ($result as $record) $elements[] = DB_ORM::model('map_node_reference', array((int)$record['id']));
            return $elements;
        }
        return NULL; 
    }
    
    public function getByMapeNodeId($mapId, $nodeId) { 
        $builder = DB_SQL::select('default')->from($this->table())->where('map_id', '=', $mapId, 'AND')->where('node_id', '=', $nodeId);
        $result = $builder->query();
        if ($result->is_loaded()){
            $elements = array();
            foreach ($result as $record) $elements[] = DB_ORM::model('map_node_reference', array((int)$record['id']));
            return $elements;
        }
        return NULL; 
    }
}

?>