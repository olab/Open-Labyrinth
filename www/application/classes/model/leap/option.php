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
 * @property string $name
 * @property string $value
 * @property string $autoload
 */
class Model_Leap_Option extends DB_ORM_Model
{

    public function __construct()
    {
        parent::__construct();

        $this->fields = array(
            'id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 10,
                'nullable' => false,
                'unsigned' => true,
            )),
            'name' => new DB_ORM_Field_String($this, array(
                'max_length' => 64,
                'nullable' => false,
            )),
            'value' => new DB_ORM_Field_Text($this, array(
                'max_length' => 4294967295,
                'nullable' => false,
            )),
            'autoload' => new DB_ORM_Field_String($this, array(
                'max_length' => 20,
                'nullable' => false,
            )),
        );
    }

    public static function data_source()
    {
        return 'default';
    }

    public static function table()
    {
        return 'options';
    }

    public static function primary_key()
    {
        return array('id');
    }

    /**
     * @param string $option
     * @param mixed $default
     * @return mixed
     */
    public static function get_option($option, $default = false)
    {
        $option = trim($option);
        if (empty($option)) {
            return false;
        }

        $row = DB_ORM::select('Option')
            ->where('name', '=', $option)
            ->limit(1)
            ->query()
            ->fetch(0);

        if (is_object($row)) {
            $value = $row->value;
        } else { // option does not exist
            return $default;
        }

        if (is_serialized($value)) { // don't attempt to unserialize data that wasn't serialized going in
            return @unserialize($value);
        } else {
            return $value;
        }
    }

}