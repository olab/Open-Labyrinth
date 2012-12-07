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
 * Model for map_vpds table in database 
 */
class Model_Leap_Map_Vpd extends DB_ORM_Model {

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
            
            'vpd_type_id' => new DB_ORM_Field_Integer($this, array(
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
            
            'type' => new DB_ORM_Relation_BelongsTo($this, array(
                'child_key' => array('vpd_type_id'),
                'parent_key' => array('id'),
                'parent_model' => 'map_vpd_type',
            )),
            
            'elements' => new DB_ORM_Relation_HasMany($this, array(
                'child_key' => array('vpd_id'),
                'child_model' => 'map_vpd_element',
                'parent_key' => array('id'),
            )),
        );
    }

    public static function data_source() {
        return 'default';
    }

    public static function table() {
        return 'map_vpds';
    }

    public static function primary_key() {
        return array('id');
    }
    
    public function getAllVpdByMap($mapId) {
        $builder = DB_SQL::select('default')->from($this->table())->where('map_id', '=', $mapId)->order_by('vpd_type_id', 'ASC');
        $result = $builder->query();
        
        if($result->is_loaded()) {
            $vpds = array();
            foreach($result as $record) {
                $vpds[] = DB_ORM::model('map_vpd', array((int)$record['id']));
            }
            
            return $vpds;
        }
        
        return NULL;
    }
    
    public function getVpdNotInArrayIDs($ids) {
        $builder = DB_SQL::select('default')->from($this->table())->where('id', 'NOT IN', $ids);
        $result = $builder->query();
        
        if($result->is_loaded()) {
            $vpds = array();
            foreach($result as $record) {
                $vpds[] = DB_ORM::model('map_vpd', array((int)$record['id']));
            }
            
            return $vpds;
        }
        
        return NULL;
    }
    
    public function createNewElement($mapId, $typeName, $values) {
        $typeId = DB_ORM::model('map_vpd_type')->getTypeIdByName($typeName);
        
        if($typeId != NULL) {
            $builder = DB_ORM::insert('map_vpd')
                    ->column('map_id', $mapId)
                    ->column('vpd_type_id', (int)$typeId);
            $id = $builder->execute();
            
            DB_ORM::model('map_vpd_element')->saveElementValues($id, $values);
        }
    }

    public function createNewElementTypeId($mapId, $typeId, $values) {
        $builder = DB_ORM::insert('map_vpd')
            ->column('map_id', $mapId)
            ->column('vpd_type_id', (int)$typeId);
        $id = $builder->execute();

        DB_ORM::model('map_vpd_element')->saveElementValues($id, $values);
        return $id;
    }
}

?>