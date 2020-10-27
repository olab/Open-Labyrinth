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
 * Model for map_nodes table in database
 */
class Model_Leap_Patient extends DB_ORM_Model {

    public $allType = array(
        'Longitudinal same set',
        'Longitudinal different set',
        'Parallel same set',
        'Parallel different set'
    );

    public function __construct() {
        parent::__construct();

        $this->fields = array(
            'id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            'name' => new DB_ORM_Field_String($this, array(
                'max_length' => 45,
                'nullable' => FALSE,
            )),
            'type' => new DB_ORM_Field_Text($this, array(
                'max_length' => 45,
                'enum' => $this->allType,
                'nullable' => FALSE,
            )),
        );
    }

    public static function data_source() {
        return 'default';
    }

    public static function table() {
        return 'patient';
    }

    public static function primary_key() {
        return array('id');
    }

    public function update ($name, $type, $id)
    {
        if ($id) DB_ORM::update('Patient')->set('name', $name)->set('type', $type)->where('id', '=', $id)->execute();
        else $id = DB_ORM::insert('Patient')->column('name', $name)->column('type', $type)->execute();
        return $id;
    }
}