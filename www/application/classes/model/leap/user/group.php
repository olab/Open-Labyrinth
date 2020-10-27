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
 * Model for user_groups table in database 
 */
class Model_Leap_User_Group extends DB_ORM_Model {

    public function __construct() {
        parent::__construct();

        $this->fields = array(
            'id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
                'unsigned' => TRUE,
            )),
            
            'user_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
                'unsigned' => TRUE,
            )),
            
            'group_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
                'unsigned' => TRUE,
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
        return 'user_groups';
    }

    public static function primary_key() {
        return array('id');
    }
    
    public function add($groupId, $userId) {
        $this->group_id = $groupId;
        $this->user_id = $userId;
        
        $this->save();
    }

    public function userExist($userId, $groupId){
        $result = DB_ORM::select('user_group')->where('user_id', '=', $userId)->where('group_id', '=', $groupId)->limit(1)->query()->fetch(0);
        return !empty($result);
    }
    
    public function remove($groupId, $userId) {
        $builder = DB_SQL::select('default')
                ->from($this->table())
                ->where('group_id', '=', $groupId, 'AND')
                ->where('user_id', '=', $userId, 'AND')
                ->column('id');
        $result = $builder->query();
        
        $this->id = (int)$result[0]['id'];
        $this->delete();
    }

    public function getAllUsersByGroup($groupId)
    {
        $builder = DB_SQL::select('default', array(DB::expr('user_id')))
            ->from($this->table())
            ->where('group_id', '=', $groupId, 'AND');
        $result = $builder->query();

        if($result->is_loaded()) {
            $array = array();
            foreach ($result as $record)
            {
                $array[] = $record['user_id'];
            }
            return $array;
        }
        return false;
    }

    public function getAllUsersByGroupIN($groupIds)
    {
        $builder = DB_SQL::select('default', array(DB::expr('user_id')))->from($this->table())->where('group_id', 'IN', $groupIds);
        $result = $builder->query();

        if($result->is_loaded()) {
            $array = array();

            foreach ($result as $record)
            {
                array_push($array, DB_ORM::model('user')->getUserById($record['user_id']));
            }

            $usersEmails = array();

            foreach($array as $rec)
            {
                $usersEmails[] = $rec->email;
            }

            return $usersEmails;
        }
        return false;
    }

    public function getGroupOfLearners ($mapId)
    {
        $result = array();
        foreach (DB_ORM::model('map_user')->getAllLearners($mapId) as $userObj)
        {
            foreach(DB_ORM::select('user_group')->where('user_id', '=', $userObj->id)->query()->as_array() as $userGroupObj)
            {
                $result[] = DB_ORM::model('Group', array($userGroupObj->group_id))->name;
            }
        }
        return $result;
    }
}

