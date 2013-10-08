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
 * Model for user_sessiontraces table in database 
 */
class Model_Leap_Statistics_User_SessionTrace extends DB_ORM_Model {

    public function __construct() {
        parent::__construct();

        $this->fields = array(
            'id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            
            'session_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 10,
                'nullable' => FALSE,
                'unsigned' => TRUE,
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
            
            'node_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 10,
                'nullable' => FALSE,
                'unsigned' => TRUE,
            )),
            
            'counters' => new DB_ORM_Field_String($this, array(
                'max_length' => 700,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            
            'date_stamp' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 20,
                'nullable' => FALSE,
            )),
            
            'confidence' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 6,
                'nullable' => FALSE,
            )),
            
            'dams' => new DB_ORM_Field_String($this, array(
                'max_length' => 700,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            
            'bookmark_made' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            
            'bookmark_used' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
        );
        
        $this->relations = array(
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
        return 'statistics_user_sessiontraces';
    }

    public static function primary_key() {
        return array('id');
    }

    public function saveWebInarSessionTraces(array $ids) {
        $data = array();

        foreach ($ids as $id) {
            $data[] = DB_ORM::model('user_sessionTrace')->getTraceBySessionID($id, 'array');
        }

        foreach ($data as $record => $sessionTraces)
        {
            foreach ($sessionTraces as $traces) {
                $builder = DB_ORM::insert('statistics_user_sessionTrace')
                    ->column('id', $traces['id'])
                    ->column('session_id', $traces['session_id'])
                    ->column('user_id', $traces['user_id'])
                    ->column('map_id', $traces['map_id'])
                    ->column('node_id', $traces['node_id'])
                    ->column('counters', $traces['counters'])
                    ->column('date_stamp', $traces['date_stamp'])
                    ->column('confidence', $traces['confidence'])
                    ->column('dams', $traces['dams'])
                    ->column('bookmark_made', $traces['bookmark_made'])
                    ->column('bookmark_used', $traces['bookmark_used']);
                $builder->execute();
            }
        }
    }

    public function isExistTrace($userId, $mapId, $sessionIDs, $nodeIDs) {
        $records = DB_SQL::select('default')
            ->from($this->table())
            ->column('id')
            ->where('user_id', '=', $userId, 'AND')
            ->where('map_id', '=', $mapId, 'AND')
            ->where('session_id', 'IN', $sessionIDs, 'AND')
            ->where('node_id', 'IN', $nodeIDs)
            ->query();

        return $records->is_loaded();
    }

    public function getUniqueTraceBySessions($sessions) {
        $records = DB_SQL::select('default')
            ->from($this->table())
            ->where('session_id', 'IN', $sessions)
            ->group_by('node_id')
            ->group_by('session_id')
            ->order_by('id')
            ->column('id')
            ->column('session_id')
            ->column('user_id')
            ->column('node_id')
            ->query();

        $result = array();
        if($records->is_loaded()) {
            foreach($records as $record) {
                $result[] = array('id'         => $record['id'],
                    'session_id' => $record['session_id'],
                    'user_id'    => $record['user_id'],
                    'node_id'    => $record['node_id']);
            }
        }

        return $result;
    }
}

?>