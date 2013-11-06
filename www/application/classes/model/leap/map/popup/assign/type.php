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
 * Model for map_counters table in database
 */
class Model_Leap_Map_Popup_Assign_Type extends DB_ORM_Model {

    public function __construct() {
        parent::__construct();

        $this->fields = array(
            'id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
                'savable' => FALSE
            )),

            'title' => new DB_ORM_Field_String($this, array(
                'max_length' => 300,
                'nullable' => FALSE,
                'savable' => FALSE
            ))
        );
    }

    public static function data_source() {
        return 'default';
    }

    public static function table() {
        return 'map_popup_assign_types';
    }

    public static function primary_key() {
        return array('id');
    }

    /**
     * Get all popups assign styles
     *
     * @return array - array of all popups assign styles
     */
    public function getAll() {
        $records = DB_SQL::select('default')->column('id')->from($this->table())->query();
        $result  = array();

        if($records->is_loaded()) {
            foreach($records as $record) {
                $result[] = DB_ORM::model('map_popup_assign_type', array((int)$record['id']));
            }
        }

        return $result;
    }
}