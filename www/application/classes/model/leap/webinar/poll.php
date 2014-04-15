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
 * Model for users table in database
 */
class Model_Leap_Webinar_Poll extends DB_ORM_Model {

    public function __construct() {
        parent::__construct();

        $this->fields = array(
            'id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
                'unsigned' => TRUE,
            )),

            'on_node' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 10,
                'nullable' => FALSE,
                'unsigned' => TRUE,
            )),

            'to_node' => new DB_ORM_Field_Integer($this, array(
                    'max_length' => 10,
                    'nullable' => FALSE,
                    'unsigned' => TRUE,
                )),

            'time' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            ))
        );
    }

    public static function data_source() {
        return 'default';
    }

    public static function table() {
        return 'webinar_poll';
    }

    public static function primary_key() {
        return array('id');
    }

    public function savePoll ($onNode, $toNode)
    {
        $record = new $this;
        $record->on_node = $onNode;
        $record->to_node = $toNode;
        $record->time    = time();
        $record->save();
    }

    public function getNodeIdByPoll($onNode, $range)
    {
        $from = time() - $range;
        $result[] = 0;
        foreach(DB_ORM::select('Webinar_Poll')->where('on_node', '=', $onNode)->query()->as_array() as $obj)
        {
            if ($from <= $obj->time) $result[$obj->to_node] = isset($result[$obj->to_node]) ? $result[$obj->to_node]+1 : 1;
        }
        asort($result);
        end($result);
        return json_encode(key($result));
    }
}