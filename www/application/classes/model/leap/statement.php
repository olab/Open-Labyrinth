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
 * @property int $updated_at
 * @property \TinCan\LRSResponse $response
 * @property Model_Leap_LRS[]|DB_ResultSet $lrs_list
 */
class Model_Leap_Statement extends Model_Leap_Base
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
            'timestamp' => new DB_ORM_Field_String($this, array(
                'max_length' => 255,
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

    /**
     * @param $result
     * @param $object
     * @param $verb
     * @param Model_Leap_User_Session|int $session
     * @param null|float $timestamp
     * @return Model_Leap_Statement|static
     */
    public static function create($session, $verb, $object, $result, $context = null, $timestamp = null)
    {
        /** @var self|static $model */
        $model = new static;
        if (is_numeric($session)) {
            /** @var Model_Leap_User_Session $session */
            $session = DB_ORM::model('User_Session', array($session));
        }
        $model->session_id = $session->id;

        //timestamp
        if ($timestamp === null) {
            $timestamp = microtime(true);
        }

        $model->timestamp = (float)$timestamp;

        $statement = array();
        // Use format('c') instead of format(\DateTime::ISO8601) due to bug in format(\DateTime::ISO8601) that generates an invalid timestamp.
        $statement['timestamp'] = DateTime::createFromFormat('U', round($model->timestamp))
            ->format('c');
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
        $statement['context']['contextActivities']['category']['id'] = URL::base(TRUE) . 'sessions/' . $session->id;

        if ($context === null) {
            $map_url = URL::base(TRUE) . 'renderLabyrinth/index/' . $session->map_id;
            $statement['context']['contextActivities']['parent']['id'] = $map_url;

            $webinar_id = $session->webinar_id;
            if (!empty($webinar_id)) {
                $webinar_url = URL::base(TRUE) . 'webinarManager/render/' . $webinar_id;
                $statement['context']['contextActivities']['grouping']['id'] = $webinar_url;
            }
        } else {
            $statement['context'] = array_merge($statement['context'], $context);
        }
        //end context

        $model->statement = json_encode($statement);

        $model->insert();
        $model->bindLRS();

        return $model;
    }

    public function bindLRS()
    {
        $lrs_list = DB_ORM::select('LRS')->order_by('name')->query();
        foreach ($lrs_list as $lrs) {
            $lrs_statement = new Model_Leap_LRSStatement();
            $lrs_statement->lrs_id = $lrs->id;
            $lrs_statement->statement_id = $this->id;

            $lrs_statement->save();
        }
    }

    public static function xApiInit()
    {
        require_once MODPATH . 'TinCanPHP/autoload.php';
    }

    public function send(Model_Leap_LRS $lrs_obj)
    {
        static::xApiInit();

        $lrs = new TinCan\RemoteLRS($lrs_obj->url, $lrs_obj->getAPIVersionName(), $lrs_obj->username, $lrs_obj->password);

        /** @var \TinCan\LRSResponse $response */
        $data = json_decode($this->statement, true);
        unset($data['timestamp']);
        $response = $lrs->saveStatement($data, true);
        if ($response->success) {
            return true;
        } else {
            var_dump($response);die;
            $this->response = $response;
            return false;
        }
    }

    public function save($reload = FALSE)
    {
        $id = $this->id;

        if ($id <= 0) {
            $this->created_at = time();
        }

        $this->updated_at = time();

        parent::save($reload);
    }

    public function insert($reload = FALSE)
    {
        $id = $this->id;

        if ($id <= 0) {
            $this->created_at = time();
        }

        $this->updated_at = time();

        parent::insert($reload);
    }

}