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
 * @property bool $is_enabled
 * @property string $name
 * @property string $url
 * @property string $username
 * @property string $password
 * @property int $api_version
 */
class Model_Leap_LRS extends DB_ORM_Model
{
    const API_VERSION_1_0_0 = 1;
    const API_VERSION_1_0_1 = 2;

    public static $api_versions = array(
        self::API_VERSION_1_0_0 => '1.0.0',
        self::API_VERSION_1_0_1 => '1.0.1',
    );

    public function __construct()
    {
        parent::__construct();

        $this->fields = array(
            'id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 10,
                'nullable' => false,
                'unsigned' => true,
            )),
            'is_enabled' => new DB_ORM_Field_Boolean($this, array(
                'default' => false,
                'nullable' => false,
                'savable' => true,
            )),
            'name' => new DB_ORM_Field_String($this, array(
                'max_length' => 255,
                'nullable' => false,
                'savable' => true,
            )),
            'url' => new DB_ORM_Field_String($this, array(
                'max_length' => 255,
                'nullable' => false,
                'savable' => true,
            )),
            'username' => new DB_ORM_Field_String($this, array(
                'max_length' => 255,
                'nullable' => false,
                'savable' => true,
            )),
            'password' => new DB_ORM_Field_String($this, array(
                'max_length' => 255,
                'nullable' => false,
                'savable' => true,
            )),
            'api_version' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 3,
                'nullable' => false,
                'unsigned' => true,
                'savable' => true,
            )),
        );
    }

    public static function data_source()
    {
        return 'default';
    }

    public static function table()
    {
        return 'lrs';
    }

    public static function primary_key()
    {
        return array('id');
    }


    //-----------------------------------------------------
    // Additional helper methods
    //-----------------------------------------------------

    public function getAPIVersionName()
    {
        return isset(static::$api_versions[$this->api_version]) ? static::$api_versions[$this->api_version] : 'unknown';
    }

}