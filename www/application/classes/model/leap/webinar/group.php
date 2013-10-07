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
class Model_Leap_Webinar_Group extends DB_ORM_Model {

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

            'group_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
                'unsigned' => TRUE
            ))
        );

        $this->relations = array(
            'group' => new DB_ORM_Relation_BelongsTo($this, array(
                'child_key' => array('group_id'),
                'parent_key' => array('id'),
                'parent_model' => 'group',
            ))
        );
    }

    public static function data_source() {
        return 'default';
    }

    public static function table() {
        return 'webinar_groups';
    }

    public static function primary_key() {
        return array('id');
    }

    /**
     * Remove all groups from webinar
     *
     * @param integer $webinarId - webinar ID
     */
    public function removeAllGroups($webinarId) {
        DB_SQL::delete('default')
                ->from($this->table())
                ->where('webinar_id', '=', $webinarId)
                ->execute();
    }

    /**
     * Ad group to webinar
     *
     * @param integer $webinarId - webinar ID
     * @param integer $groupId - group ID
     * @return integer - webinar group id
     */
    public function addGroup($webinarId, $groupId) {
        return DB_ORM::insert('webinar_group')
                       ->column('webinar_id', $webinarId)
                       ->column('group_id', $groupId)
                       ->execute();
    }
}