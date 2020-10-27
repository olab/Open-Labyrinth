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
 * Model for lti_users in database
 */
class Model_Leap_Lti_User extends DB_ORM_Model {
    public function __construct() {
        parent::__construct();

        $this->fields = array(
            'consumer_key' => new DB_ORM_Field_String($this, array(
                    'max_length' => 255,
                    'nullable' => FALSE,
                    'savable' => TRUE,
                )),
            'context_id' => new DB_ORM_Field_String($this, array(
                    'max_length' => 255,
                    'nullable' => FALSE,
                    'savable' => TRUE,
                )),
            'user_id' => new DB_ORM_Field_String($this, array(
                    'max_length' => 255,
                    'nullable' => FALSE,
                    'savable' => TRUE,
                )),
            'lti_result_sourcedid' => new DB_ORM_Field_String($this, array(
                    'max_length' => 255,
                    'nullable' => FALSE,
                    'savable' => TRUE,
                )),

            'created' => new DB_ORM_Field_DateTime($this, array(
                    'savable' => TRUE,
                    'nullable' => FALSE,
                )),
            'updated' => new DB_ORM_Field_DateTime($this, array(
                    'savable' => TRUE,
                    'nullable' => FALSE,
                ))
        );
    }
    public static function data_source() {
        return 'default';
    }

    public static function table() {
        return 'lti_users';
    }

    public static function primary_key() {
        return array('user_id');
    }
    public function addUser($values) {
        $query = DB_ORM::insert('lti_User')
            ->column('consumer_key',        Arr::get($values, 'consumer_key',''))
            ->column('context_id',          Arr::get($values, 'context_id',''))
            ->column('user_id',             Arr::get($values, 'user_id', ''))
            ->column('lti_result_sourcedid',Arr::get($values, 'lti_result_sourcedid', ''))
            ->column('created',             Arr::get($values, 'created', ''))
            ->column('updated',             Arr::get($values, 'updated', ''));


        return $query->execute();
    }

    public function updateUser($values) {
        $consumerKey = Arr::get($values, 'consumer_key','');
        $contextId = Arr::get($values, 'context_id','');
        $userId = Arr::get($values, 'user_id', '');

        DB_ORM::update('lti_User')
            ->set('lti_result_sourcedid',Arr::get($values, 'lti_result_sourcedid', ''))
            ->set('updated',             Arr::get($values, 'updated', ''))
            ->where('consumer_key', '=', $consumerKey, 'AND')->where('context_id', '=', $contextId, 'AND')->where('user_id', '=', $userId)
            ->execute();
    }



    public function deleteConsumer($consumer) {
        DB_SQL::delete ('default')->from($this->table())->where('context_id', '=', $consumer)->execute();
    }

    public function getAllRecords() {
        $builder = DB_SQL::select('default')->from($this->table());
        $result = $builder->query();
        if ($result->is_loaded()){
            $elements = array();
            foreach ($result as $record) $elements[] = DB_ORM::model('lti_context', array($record['context_id']));
            return $elements;
        }
        return NULL;
    }

    public function getByKeyContextIdUserId($key, $contextId, $userId){
        $builder = DB_SQL::select('default')->from($this->table())->where('consumer_key', '=', $key, 'AND')->where('context_id', '=', $contextId, 'AND')->where('user_id', '=', $userId);
        $result = $builder->query();
        if($result->is_loaded()) {
            return $result;
        }
        return NULL;
    }
}