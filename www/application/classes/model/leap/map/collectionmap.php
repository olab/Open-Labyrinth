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
 * Model for map_collectionMaps table in database 
 */
class Model_Leap_Map_CollectionMap extends DB_ORM_Model {

    public function __construct() {
        parent::__construct();

        $this->fields = array(
            'id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
                'unsigned' => TRUE,
            )),
            
            'collection_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 10,
                'nullable' => FALSE,
                'unsigned' => TRUE,
            )),
            
            'map_id' => new DB_ORM_Field_Integer($this, array(
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
        );
    }

    public static function data_source() {
        return 'default';
    }

    public static function table() {
        return 'map_collectionMaps';
    }

    public static function primary_key() {
        return array('id');
    }
    
    public function deleteByIDs($collectionId, $mapId) {
        DB_ORM::delete('map_collectionMap')
                ->where('map_id', '=', $mapId)
                ->where('collection_id', '=', $collectionId)
                ->execute();
    }
    
    public function getAllNotAddedMaps($collectionId) {
        $builder = DB_SQL::select('default')->from($this->table())
                ->where('collection_id', '=', $collectionId);
        $result = $builder->query();

        if($result->is_loaded()) {
            $mapIDs = array();
            foreach($result as $record) {
                $mapIDs[] = (int)$record['map_id'];
            }

            if(count($mapIDs) > 0) {
                return DB_ORM::model('map')->getMaps($mapIDs);
            } else {
                return DB_ORM::model('map')->getAllMap();
            }
            
            return NULL;
        }
        return DB_ORM::model('map')->getAllMap();
    }


    public function getAllColMapsIds() {
        $builder = DB_SQL::select('default')->from($this->table())->column('map_id');

        $result = $builder->query();

        $res = array();

        if($result->is_loaded()) {
            foreach ($result as $record) {
                $res[] = $record['map_id'];
            }
        }

        return $res;
    }
}

