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
class Model_Leap_SJTResponse extends DB_ORM_Model
{
    public function __construct() {
        parent::__construct();

        $this->fields = array(
            'id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            'response_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 10,
                'nullable' => FALSE,
            )),
            'position' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            'points' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
        );
    }

    public static function data_source() {
        return 'default';
    }

    public static function table() {
        return 'sjt_response';
    }

    public static function primary_key() {
        return array('id');
    }

    public function convertResponse($response){
        $response = json_decode($response);
        $convertResponse = '';
        foreach ($response as $responseId) {
            $responseObj = DB_ORM::model('Map_Question_Response', array($responseId));
            if ($responseObj) $convertResponse .= $responseObj->response.', ';
        }
        return $convertResponse;
    }

    public function countPoints ($response) {
        $response = json_decode($response);
        $convertResponse = 0;
        foreach ($response as $position => $responseId) {
            $sjtObj = DB_ORM::select('SJTResponse')->where('response_id', '=', $responseId)->where('position', '=', $position)->query()->fetch(0);
            if ($sjtObj) $convertResponse += $sjtObj->points;
        }
        return $convertResponse;
    }
}