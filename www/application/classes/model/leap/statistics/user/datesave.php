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
 * Model for user_sessions table in database
 */
class Model_Leap_Statistics_User_Datesave extends DB_ORM_Model {

    public function __construct() {
        parent::__construct();

        $this->fields = array(
            'id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),

            'date_save' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
                'unsigned' => TRUE,
            ))
        );
    }

    public static function data_source() {
        return 'default';
    }

    public static function table() {
        return 'statistics_user_datesave';
    }

    public static function primary_key() {
        return array('id');
    }

    public function saveDate() {
        $time = time();
        $this->date_save = $time;
        $this->save();

        return $this->returnLastRelationId();
    }

    public function returnLastRelationId() {
        $builder = DB_SQL::select('default')->from($this->table())->order_by('id', 'DESC')->limit(1);
        $result = $builder->query();

        if($result->is_loaded()) {
            foreach($result as $record) {
                return $record['id'];
            }
        }
        return NULL;
    }
}

?>