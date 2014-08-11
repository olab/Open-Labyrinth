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
 * Model for map_dams table in database 
 */
class Model_Leap_Map_Dam extends DB_ORM_Model {

    public function __construct() {
        parent::__construct();

        $this->fields = array(
            'id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
                'unsigned' => TRUE,
            )),
            
            'map_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 10,
                'nullable' => FALSE,
                'unsigned' => TRUE,
            )),
            
            'name' => new DB_ORM_Field_String($this, array(
                'max_length' => 500,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            'is_private' => new DB_ORM_Field_Boolean($this, array(
                'savable' => TRUE,
                'nullable' => FALSE,
                'default' => FALSE
            ))
        );
        
        $this->relations = array(
            'map' => new DB_ORM_Relation_BelongsTo($this, array(
                'child_key' => array('map_id'),
                'parent_key' => array('id'),
                'parent_model' => 'map',
            )),
            
            'elements' => new DB_ORM_Relation_HasMany($this, array(
                'child_key' => array('dam_id'),
                'child_model' => 'map_dam_element',
                'parent_key' => array('id'),
                'options' => array(array('order_by', array('order', 'ASC')), ),
            )),
        );
    }

    public static function data_source() {
        return 'default';
    }

    public static function table() {
        return 'map_dams';
    }

    public static function primary_key() {
        return array('id');
    }
    
    public function getAllDamByMap($mapId) {
        $builder = DB_SQL::select('default')->from($this->table())->where('map_id', '=', $mapId);
        $result = $builder->query();
        
        if ($result->is_loaded())
        {
            $dams = array();
            foreach($result as $record) $dams[] = DB_ORM::model('map_dam', array((int)$record['id']));
            return $dams;
        }
        return array();
    }
    
    public function createDam($mapId, $values) {
        $this->map_id = $mapId;
        $this->name = Arr::get($values, 'damname', '');
        
        $this->save();
    }
    
    public function updateDamName($damId, $values) {
        $this->id = $damId;
        $this->load();
        
        if($this->is_loaded()) {
            $this->name = Arr::get($values, 'damname', $this->name);
            $this->is_private = Arr::get($values, 'is_private', false);
            $this->save();
        }
    }
    
    public function getElementsNotAdded($damId) {
        $this->id = $damId;
        $this->load();
        
        if($this->is_loaded()) {
            $vpdIDs = array();
            if(count($this->elements) > 0) {
                foreach($this->elements as $element) {
                    $vpdIDs[] = $element->element_id;
                }
            }
            
            if(count($vpdIDs) > 0) {
                return DB_ORM::model('map_vpd')->getVpdNotInArrayIDs($vpdIDs, $this->map_id);
            } else {
                return DB_ORM::model('map_vpd')->getAllVpdByMap($this->map_id);
            }
        }
        
        return NULL;
    }
    
    public function getMediaFilesNotAdded($damId) {
        $this->id = $damId;
        $this->load();
        
        if($this->is_loaded()) {
            $filesIDs = array();
            if(count($this->elements) > 0) {
                foreach($this->elements as $element) {
                    if($element->element_type != 'vpd' and $element->element_type != 'dam')
                        $filesIDs[] = $element->element_id;
                }
            }
            
            if(count($filesIDs) > 0) {
                return DB_ORM::model('map_element')->getAllMediaFilesNotInIds($filesIDs, $this->map_id);
            } else {
                return DB_ORM::model('map_element')->getAllMediaFiles($this->map_id);
            }
        }
        
        return NULL;
    }
    
    public function getDamNotAdded($damId) {
        $this->id = $damId;
        $this->load();
        
        if($this->is_loaded()) {
            $damIDs = array();
            if(count($this->elements) > 0) {
                foreach($this->elements as $element) {
                    if($element->element_type == 'dam')
                        $damIDs[] = $element->element_id;
                }
            }
            $damIDs[] = $this->id;
            
            if(count($damIDs) > 0) {
                
                return $this->getDamNotInIds($damIDs, $this->map_id);
            } else {
                return $this->getAllDamByMap($this->map_id);
            }
        }
        
        return NULL;
    }
    
    public function getDamNotInIds($ids, $mapId) {
        $builder = DB_SQL::select('default')
                ->from($this->table())
                ->where('id', 'NOT IN', $ids);
        
        $result = $builder->query();
        
        if($result->is_loaded()) {
            $dams = array();
            foreach($result as $record) {
                if($record['map_id'] == $mapId || ($record['map_id'] != $mapId && !$record['is_private'])){
                    $dams[] = DB_ORM::model('map_dam', array((int)$record['id']));
                }
            }
            
            return $dams;
        }
        
        return NULL;
    }
    
    public function addElement($damId, $values, $type) {
        $this->id = $damId;
        $this->load();
        
        if($this->is_loaded()) {
            $vpdId = Arr::get($values, 'vpdid', NULL);
            if($vpdId != NULL) {
                DB_ORM::model('map_dam_element')->createNewElement($damId, $vpdId, $type);
            }
        }
    }
    
    public function addFile($damId, $values, $type) {
        $this->id = $damId;
        $this->load();
        
        if($this->is_loaded()) {
            $mrId = Arr::get($values, 'mrid', NULL);
            if($mrId != NULL) {
                DB_ORM::model('map_dam_element')->createNewElement($damId, $mrId, $type);
            }
        }
    }
    
    public function addDam($damId, $values, $type) {
        $this->id = $damId;
        $this->load();
        
        if($this->is_loaded()) {
            $addDamId = Arr::get($values, 'adamid', NULL);
            if($addDamId != NULL) {
                DB_ORM::model('map_dam_element')->createNewElement($damId, $addDamId, $type);
            }
        }
    }

    public function duplicateDam($fromMapId, $toMapId, $vpdsMap, $elemMap)
    {
        if( ! $toMapId) return array();

        $damsMap = array();

        foreach ($this->getAllDamByMap($fromMapId) as $dam)
        {
            $damsMap[$dam->id] = DB_ORM::insert('map_dam')
                ->column('map_id', $toMapId)
                ->column('name', $dam->name)
                ->column('is_private', $dam->is_private)
                ->execute();
        }

        foreach($damsMap as $k => $v) DB_ORM::model('map_dam_element')->duplicateElements($k, $v, $vpdsMap, $elemMap, $damsMap);
        return $damsMap;
    }
}