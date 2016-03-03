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

    /**
     * @param $result
     * @param $object
     * @param $verb
     * @param Model_Leap_User_Session|int $session
     * @param null|float $timestamp
     * @return Model_Leap_Statement|static
     */
    public static function create($result, $object, $verb, $session, $timestamp = null)
    {
        /** @var self|static $model */
        $model = new static;
        if(is_numeric($session)){
            /** @var Model_Leap_User_Session $session */
            $session = DB_ORM::model('User_Session', array($session));
        }
        $model->session_id = $session->id;

        //timestamp
        if($timestamp === null){
            $timestamp = microtime(true);
        }

        $model->timestamp = $timestamp;

        $statement = array();
        $statement['timestamp'] = DateTime::createFromFormat('U', round((float)$model->timestamp))
            ->format(DateTime::ISO8601);
        //end timestamp

        //actor
        $user = $session->user;
        $statement['actor'] = array(
            'objectType' => 'Agent',
            'name' => trim($user->nickname),
            'mbox' => 'mailto:' . trim($user->email),
            'account' => array(
                'homePage' => URL::base(TRUE),
                'name' => $user->id,
            ),
        );
        //end actor

        //verb
        $statement['verb'] = array(
            'id' => $verb,
        );
        //end verb

        //object
        $statement['object']['objectType'] = 'Activity';
        $statement['object'] = $object;
        //end object

        //result
        $statement['result'] = $result;
        //end result

        //context
        $map_url = URL::base(TRUE) . 'renderLabyrinth/index/' . $session->map_id;
        $statement['context'] = array(
            'registration' => $session->id,
            'contextActivities' => array(
                'parent' => array(
                    'id' => $map_url,
                ),
            )
        );

        $webinar_id = $session->webinar_id;
        if(!empty($webinar_id)){
            $webinar_url = URL::base(TRUE) . 'webinarManager/render/' . $webinar_id;
            $statement['context']['contextActivities']['grouping']['id'] = $webinar_url;
        }
        //end context

        $model->statement = json_encode($statement);

        $model->save();

        return $model;
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