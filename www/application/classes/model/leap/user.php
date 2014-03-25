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
 * Model for users table in database
 */
class Model_Leap_User extends DB_ORM_Model {

    public function __construct() {
        parent::__construct();

        $this->fields = array(
            'id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
                'unsigned' => TRUE,
            )),
            'username' => new DB_ORM_Field_String($this, array(
                'max_length' => 40,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            'password' => new DB_ORM_Field_String($this, array(
                'max_length' => 800,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            'email' => new DB_ORM_Field_String($this, array(
                'max_length' => 250,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            'nickname' => new DB_ORM_Field_String($this, array(
                'max_length' => 120,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            'language_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            'type_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            'resetHashKey' => new DB_ORM_Field_String($this, array(
                'max_length' => 255,
                'nullable' => TRUE,
            )),
            'resetHashKeyTime' => new DB_ORM_Field_DateTime($this, array(
                'nullable' => TRUE,
            )),
            'resetAttempt' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => TRUE,
            )),
            'resetTimestamp' => new DB_ORM_Field_DateTime($this, array(
                'nullable' => TRUE,
            )),
            'visualEditorAutosaveTime' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
                'default' => 60000
            )),
            'oauth_provider_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => TRUE,
            )),
            'oauth_id' => new DB_ORM_Field_String($this, array(
                'max_length' => 300,
                'nullable' => TRUE,
                'savable' => TRUE,
            )),
            'history' => new DB_ORM_Field_String($this, array(
                'max_length' => 255,
                'nullable' => TRUE,
                'savable' => TRUE,
            )),
            'history_readonly' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 1,
                'nullable' => TRUE,
            )),
            'history_timestamp' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => TRUE,
            )),
        );

        $this->relations = array(
            'language' => new DB_ORM_Relation_BelongsTo($this, array(
                'child_key' => array('language_id'),
                'parent_key' => array('id'),
                'parent_model' => 'language',
            )),
            
            'type' => new DB_ORM_Relation_BelongsTo($this, array(
                'child_key' => array('type_id'),
                'parent_key' => array('id'),
                'parent_model' => 'user_type',
            )),
            
            'groups' => new DB_ORM_Relation_HasMany($this, array(
                'child_key' => array('user_id'),
                'child_model' => 'user_group',
                'parent_key' => array('id'),
            )),
        );
        self::initialize_metadata($this);
    }


private static function initialize_metadata($object)
{
    $metadata = Model_Leap_Metadata::getMetadataRelations("user", $object);
    $object->relations = array_merge($object->relations, $metadata);
}
    public static function data_source() {
        return 'default';
    }

    public static function table() {
        return 'users';
    }

    public static function primary_key() {
        return array('id');
    }

    public function getUserById($id){
        $this->id = $id;
        $this->load();

        return $this;
    }

    public function getUserByName($username) {
        $builder = DB_SQL::select('default')->from($this->table())->where('username', '=', $username);
        $result = $builder->query();

        if ($result->is_loaded()) {
            $this->id = $result[0]['id'];
            $this->load();
            
            return $this;
        }else{
            return false;
        }
    }

    public function getUserByOAuth($oauthProvider, $oauthId) {
        $builder = DB_SQL::select('default')
                            ->from($this->table())
                            ->where('oauth_provider_id', '=', $oauthProvider, 'AND')
                            ->where('oauth_id', '=', $oauthId);
        $result = $builder->query();

        $user = null;
        if ($result->is_loaded()) {
            $user = DB_ORM::model('user', array((int)$result[0]['id']));
        }

        return $user;
    }

    public function createOAuthUser($oauthProviderId, $oauthId, $nickname) {
        return DB_ORM::insert('user')
                        ->column('username', $oauthId . 'username')
                        ->column('password', Auth::instance()->hash($oauthId . 'password'))
                        ->column('email', $oauthId . '@email.generated')
                        ->column('nickname', $nickname)
                        ->column('language_id', 1)
                        ->column('type_id', 1)
                        ->column('oauth_provider_id', $oauthProviderId)
                        ->column('oauth_id', $oauthId)
                        ->execute();
    }

    public function getUserByEmail($email) {
        $builder = DB_SQL::select('default')->from($this->table())->where('email', '=', $email);
        $result = $builder->query();

        if ($result->is_loaded()) {
            $this->id = $result[0]['id'];
            $this->load();

            return $this;
        }else{
            return false;
        }
    }

    public function getUserByHaskKey($hashKey){
        $builder = DB_SQL::select('default')->from($this->table())->where('resetHashKey', '=', $hashKey);
        $result = $builder->query();

        if ($result->is_loaded()) {
            $this->id = $result[0]['id'];
            $this->load();

            return $this;
        }else{
            return false;
        }
    }

    public function getAllUsersId ($order = 'DESC')
    {
        $result = DB_SQL::select('default')->from($this->table())->column('id')->order_by('nickname', $order)->query();
        
        $ids = array();
        if ($result->is_loaded())
        {
            foreach ($result as $record)
            {
                $ids[] = (int)$record['id'];
            }
        }
        return $ids;
    }
    
    public function getAllUsers($order = 'DESC')
    {
        $result = array();
        $ids = $this->getAllUsersId($order);
        
        foreach($ids as $id) {
            $result[] = DB_ORM::model('user', array($id));
        }

        return $result;
    }

    public function getAllUsersAndAuth($order = 'DESC', $notInUsers = array())
    {
        $result = array();
        $ids = $this->getAllUsersId($order);

        foreach($ids as $id)
        {
            if (count($notInUsers) > 0) {
                if (in_array($id, $notInUsers)){
                    continue;
                }
            }

            $user= DB_SQL::select('default',array(DB::expr( 'u.*') , DB::expr('op.icon as icon'), DB::expr('ut.name as type_name') ))->from($this->table(), 'u')
                ->join('LEFT','oauth_providers','op')
                ->on('u.oauth_provider_id','=','op.id')
                ->join('LEFT','user_types','ut')
                ->on('u.type_id','=','ut.id')
                ->where('u.id', '=', $id)
                ->order_by('nickname', $order);

            $res = $user->query();

            foreach ($res as $record) $result[] = $record;
        }
        return $result;
    }

    public function getUserByIdAndAuth($id) {
        $user= DB_SQL::select('default',array(DB::expr( 'u.*') , DB::expr('op.icon as icon'), DB::expr('ut.name as type_name') ))->from($this->table(), 'u')
            ->join('LEFT','oauth_providers','op')
            ->on('u.oauth_provider_id','=','op.id')
            ->join('LEFT','user_types','ut')
            ->on('u.type_id','=','ut.id')
            ->where('u.id', '=', $id)
            ->limit(1);

        $result = $user->query()->as_array();

        return $result[0];
    }

    public function createUser($username, $password, $nickname, $email, $typeId, $languageId) {
        $this->username = $username;
        $this->password = Auth::instance()->hash($password);
        $this->email = $email;
        $this->nickname = $nickname;
        $this->language_id = $languageId;
        $this->type_id = $typeId;

        $this->save();
    }
    
    public function updateUser($id, $password, $nickname, $email, $typeId, $languageId) {
        $this->id = $id;
        $this->load();
        
        if($password != '') {
            $this->password = Auth::instance()->hash($password);
        }
        
        $this->email = $email;
        $this->nickname = $nickname;
        $this->language_id = $languageId;
        $this->type_id = $typeId;
        
        $this->save();
    }

    public function updateHashKeyResetPassword($id, $hashKey) {
        $this->id = $id;
        $this->load();

        $this->resetHashKey = $hashKey;
        $this->resetHashKeyTime = date("Y-m-d H:i:s");
        $this->save();
    }

    public function saveResetPassword($hashKey, $password){
        $builder = DB_SQL::select('default')->from($this->table())->where('resetHashKey', '=', $hashKey);
        $result = $builder->query();

        if ($result->is_loaded()) {
            $this->id = $result[0]['id'];
            $this->load();

            $this->password = $password;
            $this->resetHashKey = NULL;
            $this->resetHashKeyTime = NULL;
            $this->resetAttempt = $this->resetAttempt + 1;
            $this->resetTimestamp = date("Y-m-d H:i:s");
            $this->save();
        }
    }

    public function getAllUserWithNotId($ids) {
        if(count($ids) <= 0) {
            return $this->getAllUsers();
        }
        $builder = DB_SQL::select('default')
                ->from($this->table())
                ->where('id', 'NOT IN', $ids)
                ->column('id');
        $qResult = $builder->query();
        
        
        if($qResult->is_loaded()) {
            $result = array();
            foreach($qResult as $record) {
                $result[] = DB_ORM::model('user', array((int)$record['id']));
            }
            return $result;
        }
        
        return NULL;
    }

    public function getAllReviewers ($order = 'ASC')
    {
        return DB_ORM::select('User')
            ->where('type_id', '=', 3)
            ->order_by('nickname' ,$order)
            ->query()
            ->as_array();
    }

    public function getUsersByTypeName($typeName, $ids = NULL, $order = 'DESC') {
        $users = array();
        if($ids != NULL) {
            $builder = DB_SQL::select('default')
                    ->from($this->table())
                    ->where('id', 'NOT IN', $ids)
                    ->order_by('nickname', $order);

            $result = $builder->query();
            if($result->is_loaded()) {
                foreach($result as $record) {
                    $users[] = DB_ORM::model('user', array((int)$record['id']));
                }
            }
        } else {
            $users = $this->getAllUsers($order);
        }
        
        if($users != NULL and count($users) > 0) {
            $result = array();
            foreach($users as $user) {
                if($user->type->name == $typeName) {
                    $result[] = $user;
                }
            }
            
            return $result;
        }
        
        return NULL;
    }

    public function updateSettings($userId, $settings) {
        DB_ORM::update('user')->set('visualEditorAutosaveTime', Arr::get($settings, 'time', 50000))->where('id', '=', $userId)->execute();
    }

    public function updateUserHistory($id, $url, $readonly, $timestamp) {
        $this->id = $id;
        $this->load();

        $this->history = $url;
        $this->history_readonly = $readonly;
        $this->history_timestamp = $timestamp;

        $this->save();
    }

    public function getUsersHistory($user_id) {
        $this->id = $user_id;
        $this->load();

        $builder = DB_SQL::select('default')
            ->from($this->table())
            ->where('history', 'IS NOT', NULL);

        $return = array();
        $result = $builder->query();
        if($result->is_loaded()) {
            foreach($result as $record) {
                $user_id = $record['id'];
                $return[$user_id]['id'] = $record['id'];
                $return[$user_id]['href'] = $record['history'];
                $return[$user_id]['username'] = $record['nickname'];
                $return[$user_id]['readonly'] = $record['history_readonly'];

                $time = time();
                $maxHistoryLiveTime = 1800;

                if ($record['history_timestamp'] != NULL && ($time - $record['history_timestamp']) > $maxHistoryLiveTime) {
                    $user_id = $record['id'];
                    $uri = NULL;
                    $readonly = NULL;
                    $timestamp = NULL;
                    $this->updateUserHistory($user_id, $uri, $readonly, $timestamp);

                    if (($record['history'] == $this->history) && $this->history_readonly) {
                        $this->history_readonly = 0;
                        $this->save();
                    }
                }
            }
        }
        return $return;
    }
}

