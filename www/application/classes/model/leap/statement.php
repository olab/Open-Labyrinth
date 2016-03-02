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
 * @property int|null $session_id
 * @property string $statement
 * @property float $timestamp
 * @property int $created_at
 * @property \TinCan\LRSResponse $response
 * @property Model_Leap_LRS[]|DB_ResultSet $lrs_list
 */
class Model_Leap_Statement extends DB_ORM_Model
{

    public $response;

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

    public static function create($session_id = null, $timestamp = null)
    {
        $model = new static;

        $model->session_id = $session_id;

        if($timestamp === null){
            $timestamp = microtime(true);
        }

        $model->timestamp = $timestamp;

        $statement = array();
        $statement['timestamp'] = DateTime::createFromFormat('U', round((float)$model->timestamp))
            ->format(DateTime::ISO8601);

        //TODO: implement statement

        $model->statement = json_encode($statement);

        return $model->save();
    }

    public static function xApiInit()
    {
        require_once MODPATH . 'TinCanPHP/autoload.php';
    }

    public function send(Model_Leap_LRS $lrs_obj)
    {
        static::xApiInit();

        $lrs = new TinCan\RemoteLRS($lrs_obj->url, $lrs_obj->api_version, $lrs_obj->username, $lrs_obj->password);

        /** @var \TinCan\LRSResponse $response */
        $response = $lrs->saveStatement(json_decode($this->statement));

        if ($response->success) {
            return true;
        } else {
            $this->response = $response;
            return false;
        }
    }

    public function save($reload = FALSE)
    {
        $id = $this->id;

        if($id <= 0) {
            $this->created_at = time();
        }

        parent::save($reload);
    }

}