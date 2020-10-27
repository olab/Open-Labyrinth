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
 * @property int $statement_id
 * @property int $lrs_id
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 * @property Model_Leap_LRS $lrs
 * @property Model_Leap_Statement $statement
 */
class Model_Leap_LRSStatement extends Model_Leap_Base
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
            'statement_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 10,
                'nullable' => false,
                'unsigned' => true,
                'savable' => true,
            )),
            'lrs_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 10,
                'nullable' => false,
                'unsigned' => true,
                'savable' => true,
            )),
            'status' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 3,
                'nullable' => false,
                'unsigned' => true,
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
            'lrs' => new DB_ORM_Relation_BelongsTo($this, array(
                'child_key' => array('lrs_id'),
                'parent_model' => 'LRS',
                'parent_key' => array('id'),
            )),
            'statement' => new DB_ORM_Relation_BelongsTo($this, array(
                'child_key' => array('statement_id'),
                'parent_model' => 'Statement',
                'parent_key' => array('id'),
            )),
        );
    }

    public static function data_source()
    {
        return 'default';
    }

    public static function table()
    {
        return 'lrs_statement';
    }

    public static function primary_key()
    {
        return array('id');
    }

    //-----------------------------------------------------
    // Additional helper methods
    //-----------------------------------------------------

    /**
     * @param Model_Leap_LRSStatement[]|DB_ResultSet $lrs_statements
     */
    public static function sendStatementsToLRS($lrs_statements)
    {
        foreach ($lrs_statements as $lrs_statement) {
            $lrs_statement->sendAndSave();
        }
    }

    /**
     * @return int
     */
    public static function count()
    {
        $result = DB_SQL::select()
            ->from(static::table())
            ->where('status', '=', Model_Leap_LRSStatement::STATUS_FAIL)
            ->column(DB_SQL::expr("COUNT(*)"), 'counter')
            ->query();

        return (int)$result[0]['counter'];
    }

    public function send()
    {
        $statement = $this->statement;
        $lrs = $this->lrs;

        return $statement->send($lrs);
    }

    public function sendAndSave()
    {
        $result = $this->send();

        $this->status = $result ? self::STATUS_SUCCESS : self::STATUS_FAIL;
        $this->save();

        return $result;
    }

    public function getStatusName()
    {
        return isset(static::$statuses[$this->status]) ? static::$statuses[$this->status] : 'unknown';
    }

    public function save($reload = false)
    {
        $id = $this->id;

        if ($id <= 0) {
            $this->created_at = time();
        }

        $this->updated_at = time();

        parent::save($reload);
    }

    public function insert($reload = false)
    {
        $id = $this->id;
        if ($id <= 0) {
            $this->created_at = time();
        }

        $this->updated_at = time();

        parent::insert($reload);
    }

}