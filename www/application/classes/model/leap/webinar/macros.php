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

class Model_Leap_Webinar_Macros extends DB_ORM_Model {

    public function __construct() {
        parent::__construct();

        $this->fields = array(
            'id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
                'unsigned' => TRUE,
            )),

            'text' => new DB_ORM_Field_String($this, array(
                'max_length' => 255,
                'nullable' => TRUE,
                'savable' => TRUE
            )),

            'hot_keys' => new DB_ORM_Field_String($this, array(
                'max_length' => 255,
                'nullable' => TRUE,
                'savable' => TRUE
            )),

            'webinar_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
                'unsigned' => TRUE,
            )),
        );
    }

    public static function data_source() {
        return 'default';
    }

    public static function table() {
        return 'webinar_macros';
    }

    public static function primary_key() {
        return array('id');
    }

    public function removeMacros($webinarId) {
        DB_SQL::delete('default')
            ->from($this->table())
            ->where('webinar_id', '=', $webinarId)
            ->execute();
    }

    public function addMacros($webinarId, $text, $hot_keys)
    {
        return DB_ORM::insert('webinar_macros')
            ->column('webinar_id', $webinarId)
            ->column('text', htmlspecialchars_decode($text))
            ->column('hot_keys', htmlspecialchars_decode($hot_keys))
            ->execute();
    }

}