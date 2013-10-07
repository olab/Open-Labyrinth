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
class Model_Leap_Webinar_User extends DB_ORM_Model {

    public function __construct() {
        parent::__construct();

        $this->fields = array(
            'id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
                'unsigned' => TRUE
            )),

            'webinar_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
                'unsigned' => TRUE
            )),

            'user_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
                'unsigned' => TRUE
            )),

            'include_4R' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
                'unsigned' => TRUE
            ))
        );

        $this->relations = array(
            'user' => new DB_ORM_Relation_BelongsTo($this, array(
                'child_key' => array('user_id'),
                'parent_key' => array('id'),
                'parent_model' => 'user',
            ))
        );
    }

    public static function data_source() {
        return 'default';
    }

    public static function table() {
        return 'webinar_users';
    }

    public static function primary_key() {
        return array('id');
    }

    /**
     * Remove all users from webinar
     *
     * @param $webinarId
     */
    public function removeUsers($webinarId) {
        DB_SQL::delete('default')
                ->from($this->table())
                ->where('webinar_id', '=', $webinarId)
                ->execute();
    }

    /**
     * Add user to webinar
     *
     * @param integer $webinarId - webinar id
     * @param integer $userId - user id
     */
    public function addUser($webinarId, $userId) {
        return DB_ORM::insert('webinar_user')
                       ->column('webinar_id', $webinarId)
                       ->column('user_id', $userId)
                       ->execute();
    }

    public function updateInclude4R($id,$isInclude) {
        $this->id = $id;
        $this->load();
        $this->include_4R = $isInclude;
        $this->save();
    }

    public function getNotIncludedUsers($webId) {

        $builder = DB_SQL::select('default',array(DB::expr('user_id')))
            ->from($this->table())
            ->where('webinar_id', '=', $webId,'AND')
            ->where('include_4R', '=', 0);

        $result = $builder->query();

        $users = array();
        if ($result->is_loaded()) {
            foreach ($result as $record => $val) {
                $users[] = $val['user_id'];
            }
        }
        return $users;
    }
}