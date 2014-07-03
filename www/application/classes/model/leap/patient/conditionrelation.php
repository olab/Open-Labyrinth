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
class Model_Leap_Patient_ConditionRelation extends DB_ORM_Model {

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
            'id_condition' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
        );
    }

    public static function data_source() {
        return 'default';
    }

    public static function table() {
        return 'patient_condition_relation';
    }

    public static function primary_key() {
        return array('id');
    }

    public function get_conditions ($id_patient)
    {
        $conditions = array();
        $conditions_relation = DB_ORM::select('Patient_ConditionRelation')->where('id_patient', '=', $id_patient)->query()->as_array();
        foreach ($conditions_relation as $condition_relation)
        {
            $conditions[] = DB_ORM::model('Patient_Condition', array($condition_relation->id_condition));
        }
        return $conditions;
    }

    public function deletePatientConditions ($id_patient)
    {
        $conditions = $this->get_conditions($id_patient);
        foreach ($conditions as $condition)
        {
            $condition->delete();
        }
    }

    public function check_and_create ($id_patient, $id_condition)
    {
        $result = DB_ORM::select('Patient_ConditionRelation')->where('id_patient', '=', $id_patient)->where('id_condition', '=', $id_condition)->query()->as_array();
        if( ! $result)
        {
            $this->id_patient = $id_patient;
            $this->id_condition = $id_condition;
            $this->save();
        }
    }
}
