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
class Model_Leap_Webinar extends DB_ORM_Model {

    public function __construct() {
        parent::__construct();

        $this->fields = array(
            'id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
                'unsigned' => TRUE
            )),

            'author_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
                'unsigned' => TRUE
            )),

            'title' => new DB_ORM_Field_String($this, array(
                'max_length' => 250,
                'nullable' => FALSE,
                'savable' => TRUE
            )),

            'current_step' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => TRUE,
                'unsigned' => TRUE
            )),

            'forum_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
                'savable' => TRUE
            )),

            'publish' => new DB_ORM_Field_String($this, array(
                'max_length' => 100,
                'nullable' => TRUE,
                'savable' => TRUE
            ))
        );

        $this->relations = array(
            'maps' => new DB_ORM_Relation_HasMany($this, array(
                'child_key' => array('webinar_id'),
                'child_model' => 'webinar_map',
                'parent_key' => array('id')
            )),

            'users' => new DB_ORM_Relation_HasMany($this, array(
                'child_key' => array('webinar_id'),
                'child_model' => 'webinar_user',
                'parent_key' => array('id')
            )),

            'groups' => new DB_ORM_Relation_HasMany($this, array(
                'child_key' => array('webinar_id'),
                'child_model' => 'webinar_group',
                'parent_key' => array('id')
            )),

            'forum' => new DB_ORM_Relation_BelongsTo($this, array(
                'child_key' => array('forum_id'),
                'parent_key' => array('id'),
                'parent_model' => 'dforum'
            ))
        );
    }

    public static function data_source() {
        return 'default';
    }

    public static function table() {
        return 'webinars';
    }

    public static function primary_key() {
        return array('id');
    }

    /**
     * Return all webinars
     *
     * @return array|null
     */
    public function getAllWebinars() {
        $records = DB_SQL::select('default')
                           ->from($this->table())
                           ->column('id')
                           ->query();

        $result  = null;
        if($records->is_loaded()) {
            $result = array();

            foreach($records as $record) {
                $result[] = DB_ORM::model('webinar', array((int)$record['id']));
            }
        }

        return $result;
    }

    /**
     * Save webinar
     *
     * @param array $values - values
     */
    public function saveWebinar($values) {
        $webinarId = Arr::get($values, 'webinarId', null);

        $forumId = null;
        if($webinarId == null || $webinarId < 0) {
            $webinar = DB_ORM::insert('webinar')
                               ->column('title', Arr::get($_POST, 'title', ''))
                               ->column('author_id', Auth::instance()->get_user()->id)
                               ->column('current_step', 1);
            $webinar->statement();
            $webinarId = $webinar->execute();
        } else {
            DB_ORM::update('webinar')
                    ->set('title', Arr::get($values, 'title', ''))
                    ->where('id', '=', $webinarId)
                    ->execute();
        }

        DB_ORM::model('webinar_map')->removeMaps($webinarId);

        for($i = 1; $i <= 3; $i++) {
            $j = 1;
            while($map = Arr::get($values, 's' . $i . '-labyrinth-' . $j, false)) {
                DB_ORM::model('webinar_map')->addMap($webinarId, $map, $i);
                $j++;
            }
        }

        $webinar   = DB_ORM::model('webinar', array((int)$webinarId));
        $forumId   = null;
        if($webinar->forum_id != null && $webinar->forum_id > 0) {
            $forumId = $webinar->forum_id;
            DB_ORM::model('dforum')->updateForum($webinar->title, 1, $forumId);
        } else {
            $forumId = DB_ORM::model('dforum')->createForum($webinar->title, 1);
        }

        $firstMessage = Arr::get($values, 'firstmessage', null);

        if($firstMessage != null && strlen($firstMessage) > 0 && ($webinar->forum_id == null || $webinar->forum_id <= 0)) {
            DB_ORM::model('dforum_messages')->createMessage($forumId, $firstMessage);
        }

        DB_ORM::update('webinar')
                ->set('forum_id', $forumId)
                ->where('id', '=', $webinarId)
                ->execute();

        $usersMap  = array();
        $users     = Arr::get($values, 'users', null);
        DB_ORM::model('webinar_user')->removeUsers($webinarId);
        if($users != null && count($users) > 0) {
            foreach($users as $userId) {
                DB_ORM::model('webinar_user')->addUser($webinarId, $userId);
                $usersMap[$userId] = $userId;
            }
        }

        $groups = Arr::get($values, 'groups', null);
        DB_ORM::model('webinar_group')->removeAllGroups($webinarId);
        if($groups != null && count($groups) > 0) {
            foreach($groups as $groupId) {
                DB_ORM::model('webinar_group')->addGroup($webinarId, $groupId);
                $usersGroup = DB_ORM::model('group')->getAllUsersInGroup($groupId);
                if($usersGroup != null && count($usersGroup) > 0) {
                    foreach($usersGroup as $userGroup) {
                        if(!isset($usersMap[$userGroup->id])) {
                            DB_ORM::model('webinar_user')->addUser($webinarId, $userGroup->id);
                        }
                    }
                }
            }
        }

        $users[] = Auth::instance()->get_user()->id;
        DB_ORM::model('dforum_users')->updateUsers($forumId, $users);
        DB_ORM::model('dforum_groups')->updateGroups($forumId, $groups);
    }

    /**
     * Delete webinar with maps
     *
     * @param integer $webinarId - webinar ID
     */
    public function deleteWebinar($webinarId) {
        $webinar = DB_ORM::model('webinar', array((int)$webinarId));

        DB_ORM::model('webinar_map')->removeMaps($webinarId);
        DB_ORM::model('webinar_user')->removeUsers($webinarId);
        DB_ORM::model('webinar_group')->removeAllGroups($webinarId);

        if($webinar != null && $webinar->forum_id > 0) {
            DB_ORM::model('dforum')->deleteForum($webinar->forum_id);
        }

        DB_SQL::delete('default')
                ->from($this->table())
                ->where('id', '=', $webinarId)
                ->execute();
    }

    /**
     * Change webinar step
     *
     * @param integer $webinarId - webinar ID
     * @param integer $step - number of step
     */
    public function changeWebinarStep($webinarId, $step) {
        DB_ORM::update('webinar')
                ->set('current_step', $step)
                ->where('id', '=', $webinarId)
                ->execute();
    }

    /**
     * Return all webinars for user
     *
     * @param integer $userId - user ID
     * @return array - array of users
     */
    public function getWebinarsForUser($userId) {
        $records = DB_SQL::select('default')
                           ->from($this->table())
                           ->join('left', 'webinar_users')
                           ->on('webinar_users.webinar_id', '=', 'webinars.id')
                           ->where('webinar_users.user_id', '=', $userId)
                           ->column('webinars.id')
                           ->query();

        $result = null;
        if($records->is_loaded()) {
            foreach($records as $record) {
                $result[] = DB_ORM::model('webinar', array((int)$record['id']));
            }
        }

        return $result;
    }
}