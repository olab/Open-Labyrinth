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
 * @property int $session_id
 * @property int $status
 * @property string $statement
 * @property float $timestamp
 * @property int $created_at
 * @property int $updated_at
 */
class Model_Leap_Statement extends DB_ORM_Model
{
    const STATUS_NEW = 0;
    const STATUS_SUCCESS = 1;
    const STATUS_FAIL = 2;

    public static $statuses = array(
        self::STATUS_NEW => 'New',
        self::STATUS_SUCCESS => 'Successfully sent to LRS',
        self::STATUS_FAIL => 'Failed',
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
            'session_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 10,
                'nullable' => true,
                'unsigned' => true,
                'savable' => true,
            )),
            'status' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 3,
                'nullable' => false,
                'unsigned' => true,
                'savable' => true,
            )),
            'statement' => new DB_ORM_Field_Text($this, array(
                'nullable' => false,
                'savable' => true,
            )),
            'timestamp' => new DB_ORM_Field_Decimal($this, array(
                'nullable' => false,
                'savable' => true,
            )),
            'created_at' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => false,
                'unsigned' => true,
                'savable' => true,
            )),
            'updated_at' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => false,
                'unsigned' => true,
                'savable' => true,
            )),
        );

        $this->relations = array(
            'lrs_list' => new DB_ORM_Relation_HasMany($this, array(
                'child_key' => array('id'),
                'child_model' => 'LRS',
                'parent_key' => array('id'),
                'through_keys' => array(
                    array('statement_id'), // [0] matches with parent
                    array('lrs_id'), // [1] matches with child
                ),
                'through_model' => 'LRS_Statement',
            )),
        );
    }

    public static function data_source()
    {
        return 'default';
    }

    public static function table()
    {
        return 'statements';
    }

    public static function primary_key()
    {
        return array('id');
    }


    //-----------------------------------------------------
    // Additional helper methods
    //-----------------------------------------------------

    public function getStatusName()
    {
        return isset(static::$statuses[$this->status]) ? static::$statuses[$this->status] : 'unknown';
    }

}