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

            'orderBy' => new DB_ORM_Field_Text($this, array(
                'enum' => array('x', 'y', 'random'),
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
                    array('order_by', array('node_id', 'ASC'))
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
        return DB_ORM::select('map_node_section')->where('map_id', '=', $mapId)->query()->as_array();
    }
    
    public function createSection($mapId, $values, $orderBy = 'random') {
        $this->map_id = $mapId;
        $this->orderBy = $orderBy;
        $this->name = Arr::get($values, 'sectionname', '');
        $this->save();
        return $this->getLastAddedSection($mapId);
    }

    public function getLastAddedSection($mapId){
        $builder = DB_SQL::select('default')->from($this->table())->where('map_id', '=', $mapId)->order_by('id', 'DESC')->limit(1);
        $result = $builder->query();

        if ($result->is_loaded()) {
            return DB_ORM::model('map_node_section', array($result[0]['id']));
        }

        return NULL;
    }
    
    public function updateSectionRow($id, $values, $orderBy = 'random') {
        $this->id = $id;
        $this->load();
        
        $this->name = Arr::get($values, 'sectiontitle', $this->name);
        $this->orderBy = $orderBy;
        $this->save();
    }
    
    public function deleteSection($sectionId) {
        $this->id = $sectionId;
        $this->delete();
        
        $tableName = DB_ORM::model('map_node_section_node');
        $builder = DB_SQL::delete('default')->from($tableName::table())->where('section_id', '=', $sectionId);
        $builder->execute();
    }
    
    public function getSectionsByMapId ($mapId)
    {
        return DB_ORM::select('Map_Node_Section')->where('map_id', '=', $mapId)->query()->as_array();
    }
    
    public function duplicateSections ($fromMapId, $toMapId, $nodeMap)
    {
        if ( ! $toMapId) return;

        foreach($this->getSectionsByMapId($fromMapId) as $section)
        {
            $builder = DB_ORM::insert('map_node_section')
                    ->column('map_id', $toMapId)
                    ->column('name', $section->name);
            
            $newId = $builder->execute();
                    
            DB_ORM::model('map_node_section_node')->duplicateSectionNodes($section->id, $newId, $nodeMap);
        }
    }
}