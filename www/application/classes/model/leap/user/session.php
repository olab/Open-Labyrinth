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
 * Model for user_sessions table in database 
 */
class Model_Leap_User_Session extends DB_ORM_Model {

    public function __construct() {
        parent::__construct();

        $this->fields = array(
            'id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 10,
                'nullable' => FALSE,
            )),
            
            'user_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 10,
                'nullable' => FALSE,
                'unsigned' => TRUE,
            )),
            
            'map_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 10,
                'nullable' => FALSE,
                'unsigned' => TRUE,
            )),
            
            'start_time' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            
            'user_ip' => new DB_ORM_Field_String($this, array(
                'max_length' => 50,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
        );
        
        $this->relations = array(
            'user' => new DB_ORM_Relation_BelongsTo($this, array(
                'child_key' => array('user_id'),
                'parent_key' => array('id'),
                'parent_model' => 'user',
            )),
            
            'traces' => new DB_ORM_Relation_HasMany($this, array(
                'child_key' => array('session_id'),
                'child_model' => 'user_sessionTrace',
                'parent_key' => array('id'),
                'options' => array(array('order_by', array('date_stamp', 'ASC')), ),
            )),
            
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
        return 'user_sessions';
    }

    public static function primary_key() {
        return array('id');
    }
    
    public function createSession($userId, $mapId, $startTime, $userIp) {
        $builder = DB_ORM::insert('user_session')
                ->column('user_id', $userId)
                ->column('map_id', $mapId)
                ->column('start_time', $startTime)
                ->column('user_ip', $userIp);
        
        return $builder->execute();
    }
    
    public function getAllSessionByMap($mapId) {
        $builder = DB_SQL::select('default')->from($this->table())->where('map_id', '=', $mapId)->order_by('start_time', 'DESC');
        $result = $builder->query();
        
        if($result->is_loaded()) {
            $sessions = array();
            foreach($result as $record) {
                $sessions[] = DB_ORM::model('user_session', array((int)$record['id']));
            }
            
            return $sessions;
        }
        
        return NULL;
    }
    
    public function getAllSessionByUser($userId) {
        $builder = DB_SQL::select('default')->from($this->table())->where('user_id', '=', $userId)->order_by('start_time', 'DESC');
        $result = $builder->query();
        
        if($result->is_loaded()) {
            $sessions = array();
            foreach($result as $record) {
                $sessions[] = DB_ORM::model('user_session', array((int)$record['id']));
            }
            
            return $sessions;
        }
        
        return NULL;
    }
    
    public function getSessionByUserMapIDs($userId, $mapId) {
        $builder = DB_SQL::select('default')
                ->from($this->table())
                ->where('user_id', '=', $userId, 'AND')
                ->where('map_id', '=', $mapId)
                ->order_by('start_time', 'DESC');
        $result = $builder->query();
        
        if($result->is_loaded()) {
            $sessions = array();
            foreach($result as $record) {
                $sessions[] = DB_ORM::model('user_session', array((int)$record['id']));
            }
            
            return $sessions;
        }
        
        return NULL;
    }
}

?>