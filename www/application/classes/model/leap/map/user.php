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
 * Model for map_users table in database 
 */
class Model_Leap_Map_User extends DB_ORM_Model {

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
            
            'user_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
        );
        
        $this->relations = array(
            'user' => new DB_ORM_Relation_BelongsTo($this, array(
                'child_key' => array('user_id'),
                'parent_key' => array('id'),
                'parent_model' => 'user',
            )),
        );
    }

    public static function data_source() {
        return 'default';
    }

    public static function table() {
        return 'map_users';
    }

    public static function primary_key() {
        return array('id');
    }
    
    public function checkUserById($mapId, $userId) {
        $builder = DB_SQL::select('default')->from($this->table())->where('map_id', '=', $mapId, 'AND')->where('user_id', '=', $userId);
        $result = $builder->query();
        
        if($result->is_loaded()) {
            return TRUE;
        }
        
        return FALSE;
    }
    
    public function getAllUsers($mapId) {
        $builder = DB_SQL::select('default')->from($this->table())->where('map_id', '=', $mapId);
        $result = $builder->query();
        
        if($result->is_loaded()) {
            $users = array();
            foreach($result as $record) {
                $users[] = DB_ORM::model('user', array((int)$record['user_id']));
            }
            
            return $users;
        }
        
        return NULL;
    }

    public function getAllAuthors($mapId){
        $builder = DB_SQL::select('default')->
            from($this->table())->
            join('LEFT', 'users')->on('map_users.user_id', '=', 'users.id')->
            where('map_users.map_id', '=', $mapId, 'AND')->where_block('(')->where('users.type_id', '=', '2')->where('users.type_id', '=', '4', 'OR')->where_block(')');
        $result = $builder->query();

        if($result->is_loaded()) {
            $users = array();
            foreach($result as $record) {
                $users[] = DB_ORM::model('user', array((int)$record['user_id']));
            }

            return $users;
        }

        return NULL;
    }

    public function getAllLearners($mapId){
        $builder = DB_SQL::select('default')->
            from($this->table())->
            join('LEFT', 'users')->on('map_users.user_id', '=', 'users.id')->
            where('map_users.map_id', '=', $mapId, 'AND')->where('users.type_id', '=', '1');
        $result = $builder->query();

        if($result->is_loaded()) {
            $users = array();
            foreach($result as $record) {
                $users[] = DB_ORM::model('user', array((int)$record['user_id']));
            }

            return $users;
        }

        return NULL;
    }

    public function getAllUsersIds($mapId) {
        $users = $this->getAllUsers($mapId);
        if($users != NULL) {
            $ids = array();
            foreach($users as $user) {
                $ids[] = $user->id;
            }
            
            return $ids;
        }
        
        return NULL;
    }
    
    public function checkUser($users, $userId) {
        if(count($users) > 0) {
            foreach($users as $record) {
                if($record->user_id == $userId) {
                    return TRUE;
                }
            }
            
            return FALSE;
        }
        
        return FALSE;
    }
    
    public function deleteByUserId($mapId, $userId) {
        $builder = DB_ORM::delete('map_user')->where('map_id', '=', $mapId, 'AND')->where('user_id', '=', $userId);
        $builder->execute();
    }
    
    public function addUser($mapId, $userId) {
        if($mapId != NULL and $userId != NULL) {
            $this->map_id = $mapId;
            $this->user_id = $userId;
            $this->save();
        }
    }
    
    public function duplicateUsers($fromMapId, $toMapId) {
        $users = $this->getAllUsers($fromMapId);
        
        if($users == null) return;
        
        foreach($users as $user) {
            $this->addUser($toMapId, $user->id);
        }
    }

    public function exportMVP($mapId) {
        $builder = DB_SQL::select('default')->from($this->table())->where('map_id', '=', $mapId);
        $result = $builder->query();

        if($result->is_loaded()) {
            $users = array();
            foreach($result as $record) {
                $users[] = $record;
            }

            return $users;
        }

        return NULL;
    }
}

?>