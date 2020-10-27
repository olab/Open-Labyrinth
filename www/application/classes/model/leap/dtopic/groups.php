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


class Model_Leap_DTopic_Groups extends DB_ORM_Model {

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
            'id_topic' => new DB_ORM_Field_Integer($this, array(
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
        return 'dtopic_groups';
    }

    public static function primary_key() {
        return array('id');
    }

    public function updateGroups($topicId, $groups) {
        $groupsInTopic= $this->getAllGroupsInTopic($topicId, 'id');
        $usersInTopic  = DB_ORM::model('dtopic_users')->getAllUsersInTopic($topicId);
        if (count($groupsInTopic) <= 0) {
            $groupsInTopic = array();
        }

        if (count($groups) > 0){
            $groupsIds = array();
            foreach($groups as $key => $groupId){
                if (in_array($groupId, $groupsInTopic)){
                    $keyForum = array_search($groupId, $groupsInTopic);
                    unset($groupsInTopic[$keyForum]);
                    unset($groups[$key]);
                } else {
                    $this->id_group = $groupId;
                    $this->id_topic = $topicId;

                    $groupsIds[] = $groupId;

                    $group = DB_ORM::model('group', array((int)$groupId));
                    if(count($group->users) > 0) {
                        foreach($group->users as $groupUser) {
                            if(!in_array($groupUser->user_id, $groupsInTopic)) {
                                DB_ORM::model('dtopic_users')->addUser($topicId, $groupUser->user_id);
                            }
                        }
                    }

                    $this->save();
                    $this->reset();
                }
            }

            if (count($groupsIds) > 0)
            {
                Controller_DForumManager::action_mail('addGroupToForum',$topicId, '' , '', $groupsIds,'','fromTopic');
            }
        }

        if (count($groupsInTopic) > 0) {
            $groupsIds = array();
            foreach($groupsInTopic as $groupId){
                $this->deleteGroupInTopic($topicId, $groupId);
                $group = DB_ORM::model('group', array((int)$groupId));
                if(count($group->users) > 0) {
                    foreach($group->users as $groupUser) {
                        if(!in_array($groupUser->user_id, $usersInTopic)) {
                            DB_ORM::model('dtopic_users')->deleteUser($topicId, $groupUser->user_id);
                        }
                    }
                }

                $groupsIds[] = $groupId;
            }
            Controller_DForumManager::action_mail('deleteGroupFromForum', $topicId,'','',$groupsIds,'','fromTopic');
        }
    }

    public function deleteGroupInTopic($topicId, $groupId){
        $builder = DB_ORM::delete($this->table())->where('id_group', '=', $groupId, 'AND')->where('id_topic', '=', $topicId);
        $builder->execute();
    }

    public function deleteAllGroups($topicId) {
        DB_SQL::delete('default')
                ->from($this->table())
                ->where('id_topic', '=', $topicId)
                ->execute();
    }

    public function getAllGroupsInTopic($topicId, $return = 'all'){
        $builder = DB_SQL::select('default')->from($this->table())->where('id_topic', '=', $topicId);
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

    public function getAllGroupsInTopicInfo($topicId){
        $result = array();
        $ids = $this->getAllGroupsInTopic($topicId, 'id');

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