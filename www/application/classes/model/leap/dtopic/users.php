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


class Model_Leap_DTopic_Users extends DB_ORM_Model {

    public function __construct() {
        parent::__construct();

        $this->fields = array(
            'id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            'id_user' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            'id_topic' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            'is_notificate' => new DB_ORM_Field_Boolean($this, array(
                'default' => TRUE,
                'nullable' => FALSE,
                'savable' => TRUE
            ))
        );
    }

    public static function data_source() {
        return 'default';
    }

    public static function table() {
        return 'dtopic_users';
    }

    public static function primary_key() {
        return array('id');
    }

    public function addUser($topicId, $userId) {
        $this->id_user = $userId;
        $this->id_topic = $topicId;

        $this->save();
        $this->reset();
    }

    public function deleteUser($topicId, $userId) {
        DB_SQL::delete('default')->from($this->table())->where('id_topic', '=', $topicId, 'AND')->where('id_user', '=', $userId)->execute();
    }

    public function updateUsers($topicId, $users) {
        $usersInTopic = $this->getAllUsersInTopic($topicId, 'id');

        if (count($usersInTopic) <= 0) {
            $usersInTopic = array();
        }

        if (count($users) > 0){
            foreach($users as $key => $userId){
                if (in_array($userId, $usersInTopic)){
                    $keyForum = array_search($userId, $usersInTopic);
                    unset($usersInTopic[$keyForum]);
                    unset($users[$key]);
                } else {
                    $this->id_user = $userId;
                    $this->id_topic = $topicId;

                    $this->save();
                    $this->reset();

                    $userInfo = DB_ORM::model('user')->getUserById($userId);

                    if ($userInfo->email != '') {
                        Controller_DForumManager::action_mail('addUserToForum',$topicId, '' , '', $userInfo->email , '','fromTopic');
                    }
                }
            }
        }

        if (count($usersInTopic) > 0) {
            $usersEmail = array();
            foreach($usersInTopic as $userId){
                $this->deleteUserInForum($topicId, $userId);

                $userInfo = DB_ORM::model('user')->getUserById($userId);

                if ($userInfo->email != '')
                {
                    $usersEmail[] = $userInfo->email;
                }
            }
            Controller_DForumManager::action_mail('deleteUserFromForum', $topicId,'','',$usersEmail,'','fromTopic');
        }
    }

    public function deleteUserInForum($topicId, $userId){
        $builder = DB_ORM::delete($this->table())->where('id_user', '=', $userId, 'AND')->where('id_topic', '=', $topicId);
        $builder->execute();
    }

    public function deleteAllUsers($topicId) {
        DB_SQL::delete('default')
                ->from($this->table())
                ->where('id_topic', '=', $topicId)
                ->execute();
    }

    public function getAllUsersInTopic($topicId, $return = 'all', $isNotificated = null){
        $builder = DB_SQL::select('default')->from($this->table())->where('id_topic', '=', $topicId, 'AND');
        if($isNotificated != null) {
            $builder->where('is_notificate', '=', $isNotificated);
        }
        $result = $builder->query();

        if($result->is_loaded()) {
            if ($return == 'id'){
                $array = array();
                foreach($result as $record){
                    $array[] = $record['id_user'];
                }
                return $array;
            } else {
                return $result->as_array();
            }
        }

        return null;
    }

    public function getAllUsersInTopicInfo($topicId, $isNotificated = null){
        $result = array();
        $ids = $this->getAllUsersInTopic($topicId, 'id', $isNotificated);

        if(count($ids) <= 0) return $result;
        foreach($ids as $id) {
            $user= DB_SQL::select('default',array(DB::expr('u.*') , DB::expr('op.icon as icon'), DB::expr('ut.name as type_name') ))->from('users', 'u')
                ->join('LEFT','oauth_providers','op')
                ->on('u.oauth_provider_id','=','op.id')
                ->join('LEFT','user_types','ut')
                ->on('u.type_id','=','ut.id')
                ->where('u.id', '=', $id)
                ->order_by('nickname', 'ASC');

            $res = $user->query();

            foreach ($res as $record) {
                $result[] = $record;
            }

        }

        return $result;
    }

    public function getTopicUser($topicIds, $userId) {
        $builder = DB_SQL::select('default')
                           ->from($this->table())
                           ->column('id')
                           ->column('id_topic')
                           ->where('id_user', '=', $userId, 'AND');
        if($topicIds != null && count($topicIds) > 0) {
            $builder = $builder->where('id_topic', 'IN', $topicIds);
        }

        $records = $builder->query();

        $result = array();
        if($records->is_loaded()) {
            foreach($records as $record) {
                $result[$record['id_topic']] = DB_ORM::model('dtopic_users', array($record['id']));
            }
        }

        return $result;
    }

    public function updateNotifications($topicId, $userId, $notification) {
        $records = DB_SQL::select('default')
                           ->from($this->table())
                           ->column('id')
                           ->where('id_topic', '=', $topicId, 'AND')
                           ->where('id_user', '=', $userId)
                           ->query();

        if(!$records->is_loaded()) {
            DB_ORM::insert('dtopic_users')
                    ->column('is_notificate', $notification)
                    ->column('id_topic', $topicId)
                    ->column('id_user', $userId)
                    ->execute();
        } else {
            DB_ORM::update('dtopic_users')
                    ->set('is_notificate', $notification)
                    ->where('id_topic', '=', $topicId, 'AND')
                    ->where('id_user', '=', $userId)
                    ->execute();
        }
    }
}