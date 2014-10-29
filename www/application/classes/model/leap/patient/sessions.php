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
class Model_Leap_Patient_Sessions extends DB_ORM_Model {

    public function __construct() {
        parent::__construct();

        $this->fields = array(
            'id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            'id_patient' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            'path' => new DB_ORM_Field_Text($this, array(
                'nullable' => FALSE,
            )),
            'patient_condition' => new DB_ORM_Field_Text($this, array(
                'nullable' => FALSE,
            )),
            'deactivateNode' => new DB_ORM_Field_Text($this, array(
                'nullable' => FALSE,
            )),
            'whose_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            'whose' => new DB_ORM_Field_Text($this, array(
                'max_length' => 45,
                'enum' => array('user', 'group'),
                'nullable' => FALSE,
            )),
            'scenario_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
        );
    }

    public static function data_source() {
        return 'default';
    }

    public static function table() {
        return 'patient_sessions';
    }

    public static function primary_key() {
        return array('id');
    }

    public function create($patientId, $whose, $whoseId, $scenarioId)
    {
        $sessionId = DB_ORM::insert('Patient_Sessions')
            ->column('id_patient', $patientId)
            ->column('whose_id', $whoseId)
            ->column('whose', $whose)
            ->column('scenario_id', $scenarioId)
            ->column('patient_condition', '')
            ->execute();

        return DB_ORM::model('Patient_Sessions', array($sessionId));
    }

    public function getSession($patientId, $whose, $whoseId, $scenarioId)
    {
        return DB_ORM::select('Patient_Sessions')
            ->where('whose', '=', $whose)
            ->where('scenario_id', '=', $scenarioId)
            ->where('whose_id', '=', $whoseId)
            ->where('id_patient', '=', $patientId)
            ->order_by('id', 'DESC')
            ->query()
            ->fetch(0);
    }
}





