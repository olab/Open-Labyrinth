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


class Model_Leap_DForum_Groups extends DB_ORM_Model {

    public function __construct() {
        parent::__construct();

        $this->fields = array(
            'id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            'id_group' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            'id_forum' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
        );
    }

    public static function data_source() {
        return 'default';
    }

    public static function table() {
        return 'dforum_groups';
    }

    public static function primary_key() {
        return array('id');
    }

    public function updateGroups($forumId, $groups, $sendNotifications = -1){
        $groupsInForum = $this->getAllGroupsInForum($forumId, 'id');
        $usersInForum  = DB_ORM::model('dforum_users')->getAllUsersInForum($forumId);
        if (count($groupsInForum) <= 0) {
            $groupsInForum = array();
        }

        if (count($groups) > 0){
            $groupsIds = array();
            foreach($groups as $key => $groupId){
                if (in_array($groupId, $groupsInForum)){
                    $keyForum = array_search($groupId, $groupsInForum);
                    unset($groupsInForum[$keyForum]);
                    unset($groups[$key]);
                } else {
                    $this->id_group = $groupId;
                    $this->id_forum = $forumId;

                    $groupsIds[] = $groupId;

                    $group = DB_ORM::model('group', array((int)$groupId));
                    if(count($group->users) > 0) {
                        foreach($group->users as $groupUser) {
                            if(!in_array($groupUser->user_id, $usersInForum)) {
                                DB_ORM::model('dforum_users')->addUser($forumId, $groupUser->user_id, $sendNotifications);
                            }
                        }
                    }

                    $this->save();
                    $this->reset();
                }
            }

            if (count($groupsIds) > 0)
            {
                Controller_DForumManager::action_mail('addGroupToForum',$forumId, '' , '', $groupsIds );
            }
        }

        if (count($groupsInForum) > 0) {
            $groupsIds = array();
            foreach($groupsInForum as $groupId){
                $this->deleteGroupInForum($forumId, $groupId);
                $group = DB_ORM::model('group', array((int)$groupId));
                if(count($group->users) > 0) {
                    foreach($group->users as $groupUser) {
                        if(!in_array($groupUser->user_id, $usersInForum)) {
                            DB_ORM::model('dforum_users')->deleteUser($forumId, $groupUser->user_id);
                        }
                    }
                }

                $groupsIds[] = $groupId;
            }
            Controller_DForumManager::action_mail('deleteGroupFromForum', $forumId,'','',$groupsIds);
        }
    }

    public function deleteGroupInForum($forumId, $groupId){
        $builder = DB_ORM::delete($this->table())->where('id_group', '=', $groupId, 'AND')->where('id_forum', '=', $forumId);
        $builder->execute();
    }

    public function deleteAllGroups($forumId) {
        DB_SQL::delete('default')
                ->from($this->table())
                ->where('id_forum', '=', $forumId)
                ->execute();
    }

    public function getAllGroupsInForum($forumId, $return = 'all'){
        $builder = DB_SQL::select('default')->from($this->table())->where('id_forum', '=', $forumId);
        $result = $builder->query();

        if($result->is_loaded()) {
            if ($return == 'id'){
                $array = array();
                foreach($result as $record){
                    $array[] = $record['id_group'];
                }
                return $array;
            } else {
                return $result->as_array();
            }
        }

        return null;
    }

    public function getAllGroupsInForumInfo($forumId){
        $result = array();
        $ids = $this->getAllGroupsInForum($forumId, 'id');

        if ($ids)
        {
            foreach($ids as $id) {
                $obj = DB_ORM::model('group', array($id));
                $result[] = $obj->as_array();
            }
        }
        return $result;
    }

}