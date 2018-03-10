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
class Model_Leap_Patient_Scenario extends DB_ORM_Model {

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
            'id_scenario' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            ))
        );
    }

    public static function data_source() {
        return 'default';
    }

    public static function table() {
        return 'patient_scenario';
    }

    public static function primary_key() {
        return array('id');
    }

    public function getPatientScenario($idPatient)
    {
        $result = array();
        $query  = $this->getScenarioByPatient($idPatient);
        foreach ($query as $obj)
        {
            $result[$obj->id] = $obj->id_scenario;
        }
        return $result;
    }

    public function getScenarioByPatient($idPatient)
    {
        return DB_ORM::select('Patient_Scenario')->where('id_patient', '=', $idPatient)->query()->as_array();
    }

    public function create($idPatient, $idScenario)
    {
        $record = new $this;
        $record->id_patient = $idPatient;
        $record->id_scenario = $idScenario;
        $record->save();
    }

    public function update($id, $idPatient, $idScenario)
    {
        $record = $this->thisLoad($id);
        $record->id_patient = $idPatient;
        $record->id_scenario = $idScenario;
        $record->save();
    }

    public function deleteRecord($id)
    {
        $record = $this->thisLoad($id);
        $record->delete();
    }

    public function thisLoad($id){
        $record = new $this;
        $record->id = $id;
        $record->load();
        return $record;
    }

    public function getPatientsByMap ($id_map)
    {
        $allPatients = array();
        $patient_maps = DB_ORM::select('Webinar_Map')->where('reference_id', '=', $id_map)->where('which', '=', 'labyrinth')->query()->as_array();

        foreach ($patient_maps as $scenarioMapObj)
        {
            $scenarioId = $scenarioMapObj->webinar_id;
            $scenarioPatients = $this->getPatientsByScenario($scenarioId);
            $allPatients = array_merge($allPatients, $scenarioPatients);
        }
        return $allPatients;
    }

    public function getPatientsByScenario ($scenarioId)
    {
        $patients = array();
        $patientScenarioObj  = DB_ORM::select('Patient_Scenario')->where('id_scenario', '=', $scenarioId)->query()->as_array();

        foreach ($patientScenarioObj as $obj)
        {
            $patients[] = DB_ORM::model('Patient', array($obj->id_patient));
        }
        return $patients;
    }
}