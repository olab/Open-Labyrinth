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
 * Model for groups table in database
 */
class Model_Leap_Group extends DB_ORM_Model {

    public function __construct() {
        parent::__construct();

        $this->fields = array(
            'id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
                'unsigned' => TRUE,
            )),

            'name' => new DB_ORM_Field_String($this, array(
                'max_length' => 100,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
        );

        $this->relations = array(
            'users' => new DB_ORM_Relation_HasMany($this, array(
                'child_key' => array('group_id'),
                'child_model' => 'user_group',
                'parent_key' => array('id'),
            )),
        );
    }

    public static function data_source() {
        return 'default';
    }

    public static function table() {
        return 'groups';
    }

    public static function primary_key() {
        return array('id');
    }

    public function getAllGroupsId($order = 'ASC') {
        $builder = DB_SQL::select('default')->from($this->table())->column('id')->order_by('name', $order);
        $result = $builder->query();

        $ids = array();
        if ($result->is_loaded()) {
            foreach ($result as $record) {
                $ids[] = (int)$record['id'];
            }
        }

        return $ids;
    }

    public function getAllGroups($order = 'ASC', $notInGroups = array()) {
        $result = array();
        $ids = $this->getAllGroupsId($order);

        foreach($ids as $id) {
            if (count($notInGroups) AND in_array($id, $notInGroups)){
                continue;
            }
            $result[] = DB_ORM::model('group', array($id));
        }

        return $result;
    }

    public function createGroup($name) {
        $this->name = $name;
        $this->save();
    }

    public function getAllUsersInGroup($groupId) {
        $this->id = $groupId;
        $this->load();

        $result = array();
        foreach($this->users as $user) {
            $result[] = DB_ORM::model('user', array($user->user_id));
        }

        return $result;
    }

    public function getAllUsersOutGroup($groupId) {
        $this->id = $groupId;
        $this->load();

        $userIds = array();
        foreach($this->users as $user) {
            $userIds[] = (int)$user->user_id;
        }

        return DB_ORM::model('user')->getAllUserWithNotId($userIds);
    }

    public function updateGroup($id, $name) {
        $this->id = $id;
        $this->load();

        $this->name = $name;
        $this->save();
    }
}

