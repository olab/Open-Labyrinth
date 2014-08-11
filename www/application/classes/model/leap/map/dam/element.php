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
    
    public function duplicateElements ($fromDamId, $toDamId, $vpdMap, $elemMap, $damMap)
    {
        $b = DB_SQL::select('default')->from($this->table())->where('dam_id', '=', $fromDamId)->column('id');
        $q = $b->query();
        
        if ($q->is_loaded() AND $q->count() > 0)
        {
            $elements = array();
            foreach ($q as $r) $elements[] = DB_ORM::model('map_dam_element', array((int)$r['id']));
            
            if (count($elements) <= 0) return;
            
            foreach ($elements as $element)
            {
                $builder = DB_ORM::insert('map_dam_element')
                        ->column('dam_id', $toDamId)
                        ->column('order', $element->order)
                        ->column('element_type', $element->element_type)
                        ->column('display', $element->display);

                switch($element->element_type)
                {
                    case 'vpd':
                        $builder->column('element_id', Arr::get($vpdMap, $element->element_id));
                        break;
                    case 'mr':
                        $builder->column('element_id', Arr::get($elemMap, $element->element_id));
                        break;
                    case 'dam':
                        $builder->column('element_id', Arr::get($damMap, $element->element_id));
                        break;
                }
                $builder->execute();
            }
        }
    }

    public function exportMVP($damId)
    {
        return DB_SQL::select('default')->from($this->table())->where('dam_id', '=', $damId)->query()->as_array();
    }
}