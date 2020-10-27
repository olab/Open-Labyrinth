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
 * @property int $id
 * @property int $language_id
 * @property int $type_id
 * @property int $visualEditorAutosaveTime
 * @property int $resetAttempt
 * @property int $oauth_provider_id
 * @property int $oauth_id
 * @property int $history_readonly
 * @property int $history_timestamp
 * @property int|bool $is_lti
 * @property string $username
 * @property string $password
 * @property string $email
 * @property string $nickname
 * @property string $resetTimestamp
 * @property string $resetHashKey
 * @property string $history
 * @property string $modeUI
 * @property string $settings
 * @property Model_Leap_Language $language
 * @property Model_Leap_User_Type $type
 * @property Model_Leap_User_Group[]|DB_ResultSet $groups
 */
class Model_Leap_User extends DB_ORM_Model implements Model_ACL_User
{
    const TYPE_LEARNER = 1;
    const TYPE_AUTHOR = 2;
    const TYPE_REVIEWER = 3;
    const TYPE_SUPERUSER = 4;
    const TYPE_REMOTE_SERVICE = 5;
    const TYPE_DIRECTOR = 6;

    public function __construct()
    {
        parent::__construct();

        $this->fields = array(
            'id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => false,
                'unsigned' => true,
            )),
            'username' => new DB_ORM_Field_String($this, array(
                'max_length' => 40,
                'nullable' => false,
                'savable' => true,
            )),
            'password' => new DB_ORM_Field_String($this, array(
                'max_length' => 800,
                'nullable' => false,
                'savable' => true,
            )),
            'email' => new DB_ORM_Field_String($this, array(
                'max_length' => 250,
                'nullable' => false,
                'savable' => true,
            )),
            'nickname' => new DB_ORM_Field_String($this, array(
                'max_length' => 120,
                'nullable' => false,
                'savable' => true,
            )),
            'language_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => false,
            )),
            'type_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => false,
            )),
            'resetHashKey' => new DB_ORM_Field_String($this, array(
                'max_length' => 255,
                'nullable' => true,
            )),
            'resetHashKeyTime' => new DB_ORM_Field_DateTime($this, array(
                'nullable' => true,
            )),
            'resetAttempt' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => true,
            )),
            'resetTimestamp' => new DB_ORM_Field_DateTime($this, array(
                'nullable' => true,
            )),
            'visualEditorAutosaveTime' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => false,
                'default' => 60000
            )),
            'oauth_provider_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => true,
            )),
            'oauth_id' => new DB_ORM_Field_String($this, array(
                'max_length' => 300,
                'nullable' => true,
                'savable' => true,
            )),
            'history' => new DB_ORM_Field_String($this, array(
                'max_length' => 255,
                'nullable' => true,
                'savable' => true,
            )),
            'history_readonly' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 1,
                'nullable' => true,
            )),
            'history_timestamp' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => true,
            )),
            'modeUI' => new DB_ORM_Field_Text($this, array(
                'max_length' => 45,
                'enum' => array('advanced', 'easy'),
                'nullable' => false,
            )),
            'is_lti' => new DB_ORM_Field_Boolean($this, array(
                'nullable' => true,
                'default' => false,
            )),
            'settings' => new DB_ORM_Field_Text($this, array(
                'max_length' => 65535,
                'nullable' => true,
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

    public static function data_source()
    {
        return 'default';
    }

    public static function table()
    {
        return 'users';
    }

    public static function primary_key()
    {
        return array('id');
    }

    public function getUserById($id)
    {
        $this->id = $id;
        $this->load();

        return $this;
    }

    public function getUserByName($username)
    {
        $builder = DB_SQL::select('default')->from($this->table())->where('username', '=', $username);
        $result = $builder->query();

        if ($result->is_loaded()) {
            $this->id = $result[0]['id'];
            $this->load();

            return $this;
        } else {
            return false;
        }
    }

    public function getUserByOAuth($oauthProvider, $oauthId)
    {
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

    public function createOAuthUser($oauthProviderId, $oauthId, $nickname)
    {
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

    public function getUserByEmail($email)
    {
        $builder = DB_SQL::select('default')->from($this->table())->where('email', '=', $email);
        $result = $builder->query();

        if ($result->is_loaded()) {
            $this->id = $result[0]['id'];
            $this->load();

            return $this;
        } else {
            return false;
        }
    }

    public function getUserByHaskKey($hashKey)
    {
        $builder = DB_SQL::select('default')->from($this->table())->where('resetHashKey', '=', $hashKey);
        $result = $builder->query();

        if ($result->is_loaded()) {
            $this->id = $result[0]['id'];
            $this->load();

            return $this;
        } else {
            return false;
        }
    }

    public function getAllUsersId($order = 'DESC', $isLti = false)
    {
        $ids = array();
        $result = DB_SQL::select('default')->from($this->table())->column('id')->order_by('nickname',
            $order)->where('is_lti', '=', $isLti)->query();

        foreach ($result as $record) {
            $ids[] = $record['id'];
        }

        return $ids;
    }

    public function getAllUsers($order = 'DESC')
    {
        return DB_ORM::select('User')->order_by('nickname', $order)->query()->as_array();
    }

    public function getAllUsersAndAuth($order = 'DESC', $notInUsers = array())
    {
        $result = array();

        foreach ($this->getAllUsersId($order) as $id) {
            if (count($notInUsers) AND in_array($id, $notInUsers)) {
                continue;
            }

            $res = DB_SQL::select('default',
                array(DB::expr('u.*'), DB::expr('op.icon as icon'), DB::expr('ut.name as type_name')))
                ->from($this->table(), 'u')
                ->join('LEFT', 'oauth_providers', 'op')
                ->on('u.oauth_provider_id', '=', 'op.id')
                ->join('LEFT', 'user_types', 'ut')
                ->on('u.type_id', '=', 'ut.id')
                ->where('u.id', '=', $id)
                ->order_by('nickname', $order)
                ->query();

            foreach ($res as $record) {
                $result[] = $record;
            }
        }

        return $result;
    }

    public function getUserByIdAndAuth($id)
    {
        $user = DB_SQL::select('default',
            array(DB::expr('u.*'), DB::expr('op.icon as icon'), DB::expr('ut.name as type_name')))->from($this->table(),
            'u')
            ->join('LEFT', 'oauth_providers', 'op')
            ->on('u.oauth_provider_id', '=', 'op.id')
            ->join('LEFT', 'user_types', 'ut')
            ->on('u.type_id', '=', 'ut.id')
            ->where('u.id', '=', $id)
            ->limit(1);

        $result = $user->query()->as_array();

        return $result[0];
    }

    public function createUser(
        $username,
        $password,
        $nickname,
        $email,
        $typeId,
        $languageId,
        $uiMode = 'easy',
        $isLti = false
    ) {
        return DB_ORM::insert('User')
            ->column('username', $username)
            ->column('password', Auth::instance()->hash($password))
            ->column('email', $email)
            ->column('nickname', $nickname)
            ->column('language_id', $languageId)
            ->column('type_id', $typeId)
            ->column('modeUI', $uiMode)
            ->column('is_lti', $isLti)
            ->execute();
    }

    public function updateUser($id, $password, $nickname, $email, $typeId, $languageId, $isLti = false)
    {
        $this->id = $id;
        $this->load();

        if ($password != '') {
            $this->password = Auth::instance()->hash($password);
        }

        $this->email = $email;
        $this->nickname = $nickname;
        $this->language_id = $languageId;
        $this->type_id = $typeId;
        $this->is_lti = $isLti;

        $this->save();
    }

    public function updateHashKeyResetPassword($id, $hashKey)
    {
        $this->id = $id;
        $this->load();

        $this->resetHashKey = $hashKey;
        $this->resetHashKeyTime = date("Y-m-d H:i:s");
        $this->save();
    }

    public function saveResetPassword($hashKey, $password)
    {
        $builder = DB_SQL::select('default')->from($this->table())->where('resetHashKey', '=', $hashKey);
        $result = $builder->query();

        if ($result->is_loaded()) {
            $this->id = $result[0]['id'];
            $this->load();

            $this->password = $password;
            $this->resetHashKey = null;
            $this->resetHashKeyTime = null;
            $this->resetAttempt = $this->resetAttempt + 1;
            $this->resetTimestamp = date("Y-m-d H:i:s");
            $this->save();
        }
    }

    public function getAllUserWithNotId($ids)
    {
        if (count($ids) <= 0) {
            return $this->getAllUsers();
        }
        $builder = DB_SQL::select('default')
            ->from($this->table())
            ->where('id', 'NOT IN', $ids)
            ->column('id');
        $qResult = $builder->query();


        if ($qResult->is_loaded()) {
            $result = array();
            foreach ($qResult as $record) {
                $result[] = DB_ORM::model('user', array((int)$record['id']));
            }

            return $result;
        }

        return null;
    }

    public function getAllReviewers($order = 'ASC')
    {
        return DB_ORM::select('User')
            ->where('type_id', '=', 3)
            ->order_by('nickname', $order)
            ->query()
            ->as_array();
    }

    public function getUsersByTypeName($typeName, $ids = null, $order = 'DESC')
    {
        $result = array();

        if ($ids != null) {
            $users = DB_ORM::select('Model_Leap_User')->where('id', 'NOT IN', $ids)->order_by('nickname',
                $order)->query()->as_array();
        } else {
            $users = $this->getAllUsers($order);
        }

        foreach ($users as $user) {
            if ($user->type->name == $typeName) {
                $result[] = $user;
            }
        }

        return $result;
    }

    public function updateSettings($userId, $settings)
    {
        DB_ORM::update('user')->set('visualEditorAutosaveTime', Arr::get($settings, 'time', 50000))->where('id', '=',
            $userId)->execute();
    }

    public function updateUserHistory($id, $url, $readonly, $timestamp)
    {
        $this->id = $id;
        $this->load();

        $this->history = $url;
        $this->history_readonly = $readonly;
        $this->history_timestamp = $timestamp;

        $this->save();
    }

    public function getUsersHistory($user_id)
    {
        $this->id = $user_id;
        $this->load();

        $builder = DB_SQL::select('default')
            ->from($this->table())
            ->where('history', 'IS NOT', null);

        $return = array();
        $result = $builder->query();
        if ($result->is_loaded()) {
            foreach ($result as $record) {
                $user_id = $record['id'];
                $return[$user_id]['id'] = $record['id'];
                $return[$user_id]['href'] = $record['history'];
                $return[$user_id]['username'] = $record['nickname'];
                $return[$user_id]['readonly'] = $record['history_readonly'];

                $time = time();
                $maxHistoryLiveTime = 1800;

                if ($record['history_timestamp'] != null && ($time - $record['history_timestamp']) > $maxHistoryLiveTime) {
                    $user_id = $record['id'];
                    $uri = null;
                    $readonly = null;
                    $timestamp = null;
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

    public static function getLastSessionTrace($user_id, $webinar_id = null, $map_id = null)
    {
        $builder = DB_ORM::select('User_SessionTrace');

        if (!empty($webinar_id)) {
            $builder->join('INNER', 'user_sessions')->on('user_sessions.id', '=', 'user_sessiontraces.session_id');
        }

        $builder->where('user_sessiontraces.user_id', '=', $user_id);

        if (!empty($map_id)) {
            $builder->where('user_sessiontraces.map_id', '=', $map_id);
        }

        if (!empty($webinar_id)) {
            $builder->where('user_sessions.webinar_id', '=', $webinar_id);
        }

        return $builder
            ->order_by('user_sessiontraces.id', 'DESC')
            ->limit(1)
            ->query()
            ->fetch(0);
    }

    public function can($policy_name, $args = array())
    {
        $status = false;
        try {
            $this->id = Auth::instance()->get_user()->id;
            $this->load();

            $reflection = new ReflectionClass('Policy_' . $policy_name);
            $class = $reflection->newInstanceArgs();
            $status = $class->execute($this, $args);
            if (true === $status) {
                return true;
            }
        } catch (ReflectionException $ex) // try and find a message based policy
        {
            // Try each of this user's roles to match a policy
            foreach ($this->roles->find_all() as $role) {
                $status = Kohana::message('policy', $policy_name . '.' . $role->id);
                if ($status) {
                    return true;
                }
            }
        }
        // We don't know what kind of specific error this was
        if (false === $status) {
            $status = Policy::GENERAL_FAILURE;
        }
        Policy::$last_code = $status;

        return true === $status;
    }

    public function assert($policy_name, $args = array())
    {
        $status = $this->can($policy_name, $args);
        if (true !== $status) {
            throw new Policy_Exception(
                'Could not authorize policy :policy',
                array(':policy' => $policy_name),
                Policy::$last_code
            );
        }
    }

    public function changeUI($mode)
    {
        $this->modeUI = $mode;
        $this->save();
    }

    /**
     * @param array $settings
     * @throws Kohana_Marshalling_Exception
     */
    public function saveSettings($settings)
    {
        $settings = json_encode($settings);
        $this->settings = $settings;
        $this->save();
    }

    public function getSettings()
    {
        $settings = json_decode($this->settings, true);

        return !empty($settings) ? $settings : array();
    }

    /**
     * @return bool
     */
    public function isSuperuser()
    {
        return ($this->type_id == self::TYPE_SUPERUSER);
    }
}

