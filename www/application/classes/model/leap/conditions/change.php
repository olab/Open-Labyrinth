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
class Model_Leap_Conditions_Change extends DB_ORM_Model {

    public function __construct() {
        parent::__construct();

        $this->fields = array(
            'id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            'condition_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            'scenario_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            'node_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 10,
                'nullable' => FALSE,
            )),
            'value' => new DB_ORM_Field_Text($this, array(
                'nullable' => FALSE,
            )),
            'appears' => new DB_ORM_Field_Boolean($this, array(
                'nullable' => FALSE,
            )),
        );
    }

    public static function data_source() {
        return 'default';
    }

    public static function table() {
        return 'conditions_change';
    }

    public static function primary_key() {
        return array('id');
    }

    public function add ($conditionId, $scenarioId, $nodeId, $value, $appears)
    {
        $record = new $this;
        $record->condition_id   = $conditionId;
        $record->scenario_id    = $scenarioId;
        $record->node_id        = $nodeId;
        $record->value          = $value;
        $record->appears        = $appears;
        $record->save();
    }

    public function update ($id, $value, $appears)
    {
        $this->id = $id;
        $this->load();
        $this->value   = $value;
        $this->appears = $appears;
        $this->save();
    }

    public function deleteRecord ($id)
    {
        $record = new $this;
        $record->id = $id;
        $record->delete();
    }
}