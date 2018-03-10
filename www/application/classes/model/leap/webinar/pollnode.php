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
class Model_Leap_Webinar_PollNode extends DB_ORM_Model {

    public function __construct() {
        parent::__construct();

        $this->fields = array(
            'id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
                'unsigned' => TRUE,
            )),

            'node_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 10,
                'nullable' => FALSE,
                'unsigned' => TRUE,
            )),

            'webinar_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
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
        return 'webinar_node_poll';
    }

    public static function primary_key() {
        return array('id');
    }

    public function update ($node_id, $webinar_id, $time, $id = null)
    {
        $record = $id ? DB_ORM::model('Webinar_PollNode', array($id)) : new $this;
        $record->node_id = $node_id;
        $record->webinar_id = $webinar_id;
        $record->time = $time;
        $record->save();
    }

    public function getWebinarNodes ($webinar_id)
    {
        return DB_ORM::select('Webinar_PollNode')->where('webinar_id', '=', $webinar_id)->query()->as_array();
    }

    public function deleteNode ($nodeId)
    {
        DB_ORM::delete('Webinar_PollNode')->where('node_id', '=', $nodeId)->execute();;
    }

    public function getTime ($nodeId, $webinarId)
    {
        $record = DB_ORM::select('Webinar_PollNode')->where('node_id', '=', $nodeId)->where('webinar_id', '=', $webinarId)->query()->fetch(0);
        return $record ? $record->time : 0;
    }
}