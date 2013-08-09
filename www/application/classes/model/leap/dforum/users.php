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


class Model_Leap_DForum_Users extends DB_ORM_Model {

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
        return 'dforum_users';
    }

    public static function primary_key() {
        return array('id');
    }

    public function addUser($forumId, $userId) {
        $this->id_user = $userId;
        $this->id_forum = $forumId;

        $this->save();
        $this->reset();
    }

    public function deleteUser($forumId, $userId) {
        DB_SQL::delete('default')->from($this->table())->where('id_forum', '=', $forumId, 'AND')->where('id_user', '=', $userId)->execute();
    }

    public function updateUsers($forumId, $users){
        $usersInForum = $this->getAllUsersInForum($forumId, 'id');

        if (count($usersInForum) <= 0) {
            $usersInForum = array();
        }

        if (count($users) > 0){
            foreach($users as $key => $userId){
                if (in_array($userId, $usersInForum)){
                    $keyForum = array_search($userId, $usersInForum);
                    unset($usersInForum[$keyForum]);
                    unset($users[$key]);
                } else {
                    $this->id_user = $userId;
                    $this->id_forum = $forumId;

                    $this->save();
                    $this->reset();

                    $userInfo = DB_ORM::model('user')->getUserById($userId);

                    if ($userInfo->email != '') {
                        Controller_DForumManager::action_mail('addUserToForum',$forumId, '' , '', $userInfo->email );
                    }
                }
            }
        }

        if (count($usersInForum) > 0) {
            $usersEmail = array();
            foreach($usersInForum as $userId){
                $this->deleteUserInForum($forumId, $userId);

                $userInfo = DB_ORM::model('user')->getUserById($userId);

                if ($userInfo->email != '')
                {
                    $usersEmail[] = $userInfo->email;
                }
            }
            Controller_DForumManager::action_mail('deleteUserFromForum', $forumId,'','',$usersEmail);
        }
    }

    public function deleteUserInForum($forumId, $userId){
        $builder = DB_ORM::delete($this->table())->where('id_user', '=', $userId, 'AND')->where('id_forum', '=', $forumId);
        $builder->execute();
    }

    public function deleteAllUsers($forumId) {
        DB_SQL::delete('default')
                ->from($this->table())
                ->where('id_forum', '=', $forumId)
                ->execute();
    }

    public function getAllUsersInForum($forumId, $return = 'all'){
        $builder = DB_SQL::select('default')->from($this->table())->where('id_forum', '=', $forumId);
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

    public function getAllUsersInForumInfo($forumId){
        $result = array();
        $ids = $this->getAllUsersInForum($forumId, 'id');

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


}