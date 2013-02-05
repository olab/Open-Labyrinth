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
 * Model for map_keys table in database 
 */
class Model_Leap_Map_Key extends DB_ORM_Model {

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
            
            'key' => new DB_ORM_Field_String($this, array(
                'max_length' => 20,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
        );
    }

    public static function data_source() {
        return 'default';
    }

    public static function table() {
        return 'map_keys';
    }

    public static function primary_key() {
        return array('id');
    }
    
    public function getKeysByMap($mapId) {
        $builder = DB_SQL::select('default')->from($this->table())->where('map_id', '=', $mapId);
        $result = $builder->query();
        
        if($result->is_loaded()) {
            $keys = array();
            foreach($result as $record) {
                $keys[] = DB_ORM::model('map_key', array((int)$record['id']));
            }
            
            return $keys;
        }
        
        return NULL;
    }
    
    public function updateKeys($mapId, $values) {
        $keys = $this->getKeysByMap($mapId);
        if($keys != NULL) {
            if(count($keys) > 0) {
                foreach($keys as $key) {
                    $data = Arr::get($values, 'key_'.$key->id, NULL);
                    if($data != NULL) {
                        $key->key = $data;
                        $key->save();
                    }
                }
            }
        }
    }
    
    public function createKeys($mapId, $values, $count) {
        for($i = 0; $i < $count; $i++) {
            $data = Arr::get($values, 'akey_'.($i+1), NULL);
            var_dump($data);
            if($data != NULL) {
                $newKey = DB_ORM::model('map_key');
                $newKey->map_id = $mapId;
                $newKey->key = $data;
                $newKey->save();
            }
        }
    }

    public function checkKey($mapId, $key){
        $builder = DB_SQL::select('default')->from($this->table())->where('map_id', '=', $mapId, 'AND')->where('key', '=', $key);
        $result = $builder->query();
        if ($result->is_loaded()) {
            return true;
        }
        return false;
    }
    
    public function duplicateKeys($fromMapId, $toMapId) {
        $keys = $this->getKeysByMap($fromMapId);
        
        if($keys == null || $toMapId == null || $toMapId <= 0) return;
        
        foreach($keys as $key) {
            $builder = DB_ORM::insert('map_key')
                    ->column('map_id', $toMapId)
                    ->column('key', $key->key);
            
            $builder->execute();
        }
    }
}

?>