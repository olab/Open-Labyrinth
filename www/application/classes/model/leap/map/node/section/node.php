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

    public function createNode($nodeId, $sectionId, $order) {
        $this->node_id = $nodeId;
        $this->section_id = $sectionId;
        $this->order = $order;

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

    public function deleteNodesBySection($sectionId) {
        DB_SQL::delete('default')->from($this->table())->where('section_id', '=', $sectionId)->execute();
    }

    public function getSectionNodes($sectionId) {
        $builder = DB_SQL::select('default')->from($this->table())->where('section_id', '=', $sectionId)->order_by('id');
        $result = $builder->query();
        
        if($result->is_loaded()) {
            $sections = array();
            foreach($result as $record) {
                $sections[] = DB_ORM::model('map_node_section_node', array((int)$record['id']));
            }
            
            return $sections;
        }
        
        return NULL;
    }
    
    public function duplicateSectionNodes($fromSectionId, $toSectionId, $nodeMap) {
        $nodes = $this->getSectionNodes($fromSectionId);
        
        if($nodes == null || $toSectionId == null || $toSectionId <= 0) return;
        
        foreach($nodes as $node) {
            $builder = DB_ORM::insert('map_node_section_node')
                    ->column('section_id', $toSectionId)
                    ->column('order', $node->order);
            
            if(isset($nodeMap[$node->node_id]))
                $builder = $builder->column ('node_id', $nodeMap[$node->node_id]);
            
            $builder->execute();
        }
    }

    public function exportMVP($sectionId) {
        $builder = DB_SQL::select('default')->from($this->table())->where('section_id', '=', $sectionId)->order_by('id');
        $result = $builder->query();

        if($result->is_loaded()) {
            $sections = array();
            foreach($result as $record) {
                $sections[] = $record;
            }

            return $sections;
        }

        return NULL;
    }
}

?>