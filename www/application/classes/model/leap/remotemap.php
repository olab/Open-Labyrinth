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
 * Model for map_remoteServices table in database 
 */
class Model_Leap_RemoteMap extends DB_ORM_Model {

    public function __construct() {
        parent::__construct();

        $this->fields = array(
            'id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
                'unsigned' => TRUE,
            )),
            
            'service_id' => new DB_ORM_Field_Integer($this, array(
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
        return 'remoteMaps';
    }

    public static function primary_key() {
        return array('id');
    }
    
    public function getMapsByService($id) {
        $builder = DB_SQL::select('default')->from($this->table())->where('service_id', '=', $id);
        $result = $builder->query();
        
        if($result->is_loaded()) {
            $maps = array();
            foreach($result as $record) {
                $maps[] = DB_ORM::model('remoteMap', array((int)$record['id']));
            }
            
            return $maps;
        }
        
        return NULL;
    }
    
    public function addMap($mapId, $serviceId) {
        if($mapId != NULL and $serviceId != NULL) {
            $this->map_id = $mapId;
            $this->service_id = $serviceId;

            $this->save();
        }
    }
    
    public function checkMap($serviceId, $mapId) {
        $builder = DB_SQL::select('default')->from($this->table())->where('service_id', '=', $serviceId, 'AND')->where('map_id', '=', $mapId);
        $result = $builder->query();
        
        if($result->is_loaded()) {
            return TRUE;
        }
        
        return FALSE;
    }
}

?>