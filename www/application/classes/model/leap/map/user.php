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
    
    public function getAllUsers ($mapId, $order = "DESC")
    {
        $builder = DB_SQL::select('default')
            ->from($this->table())
            ->where('map_users.map_id', '=', $mapId)
            ->join('LEFT', 'users')->on('map_users.user_id', '=', 'users.id')
            ->order_by('nickname', $order);
        $result = $builder->query();
        
        if ($result->is_loaded())
        {
            $users = array();
            foreach($result as $record) $users[] = DB_ORM::model('user', array((int)$record['user_id']));
            return $users;
        }
        return array();
    }

    public function getAllAuthors($mapId, $order = 'DESC'){
        $builder = DB_SQL::select('default')->
            from($this->table())
            ->join('LEFT', 'users')->on('map_users.user_id', '=', 'users.id')
            ->where('map_users.map_id', '=', $mapId, 'AND')
            ->where_block('(')->where('users.type_id', '=', '2')
            ->where('users.type_id', '=', '4', 'OR')
            ->where_block(')')
            ->order_by('users.nickname', $order)
            ->column('map_users.user_id');
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

    public function getAllLearners($mapId, $order = "DESC"){
        $builder = DB_SQL::select('default')
            ->from($this->table())
            ->join('LEFT', 'users')
            ->on('map_users.user_id', '=', 'users.id')
            ->where('map_users.map_id', '=', $mapId, 'AND')
            ->where('users.type_id', '=', '1')
            ->order_by('users.nickname', $order)
            ->column('map_users.user_id');
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

    public function getAllMapUserIDs($mapId) {
        $builder = DB_SQL::select('default')->from($this->table())->where('map_id', '=', $mapId)->column('user_id');
        $query   = $builder->query();

        $ids = null;
        if($query->is_loaded()) {
            $ids = array();
            foreach($query as $user) {
                $ids[] = $user;
            }
        }

        return $ids;
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
    
    public function addUser ($mapId, $userId)
    {
        if ($mapId != NULL and $userId != NULL)
        {
            DB_ORM::insert('map_user')
                ->column('map_id', $mapId)
                ->column('user_id', $userId)
                ->execute();
        }
    }

    public function addAllLearners($mapId) {
        if($mapId == null && $mapId <= 0) return;

        $userIds = $this->getAllMapUserIDs((int) $mapId);
        $learners = DB_ORM::model('user')->getUsersByTypeName('learner', $userIds);

        if($learners == null || count($learners) <= 0) return;

        foreach($learners as $learner) {
            $this->addUser($mapId, $learner->id);
        }
    }

    public function removeAllLearners($mapId) {
        if($mapId == null && $mapId <= 0) return;

        $learners = $this->getAllUsers((int) $mapId);

        if($learners == null || count($learners) <= 0) return;

        foreach($learners as $learner) {
            if($learner->type->name == 'learner') {
                $this->deleteByUserId($mapId, $learner->id);
            }
        }
    }

    public function addAllAuthors($mapId) {
        if($mapId == null && $mapId <= 0) return;

        $userIds = $this->getAllMapUserIDs((int) $mapId);

        $admins = DB_ORM::model('user')->getUsersByTypeName('superuser', $userIds);
        $authors = DB_ORM::model('user')->getUsersByTypeName('author', $userIds);

        if($admins != null && count($admins) > 0) {
            foreach($admins as $admin) {
                if($admin->id != Auth::instance()->get_user()->id) {
                    $this->addUser($mapId, $admin->id);
                }
            }
        }

        if($authors != null && count($authors) > 0) {
            foreach($authors as $author) {
                if($author->id != Auth::instance()->get_user()->id) {
                    $this->addUser($mapId, $author->id);
                }
            }
        }
    }

    public function removeAllAuthors($mapId) {
        if($mapId == null && $mapId <= 0) return;

        $learners = $this->getAllUsers((int) $mapId);

        if($learners == null || count($learners) <= 0) return;

        foreach($learners as $learner) {
            if(($learner->type->name == 'superuser' || $learner->type->name == 'author') && ($learner->id != Auth::instance()->get_user()->id)) {
                $this->deleteByUserId($mapId, $learner->id);
            }
        }
    }

    public function addAllReviewers($mapId)
    {
        if ($mapId == null) return;

        $reviewers = DB_ORM::model('User')->getAllReviewers();

        foreach($reviewers as $reviewer)
        {
            $this->addUser($mapId, $reviewer->id);
        }
    }

    public function removeAllReviewers($mapId)
    {
        if($mapId == null) return;

        $reviewers = DB_ORM::model('User')->getAllReviewers();

        foreach($reviewers as $reviewer)
        {
                $this->deleteByUserId($mapId, $reviewer->id);
        }
    }
    
    public function duplicateUsers($fromMapId, $toMapId)
    {
        foreach($this->getAllUsers($fromMapId) as $user) $this->addUser($toMapId, $user->id);
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