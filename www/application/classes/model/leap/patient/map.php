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
class Model_Leap_Patient_Map extends DB_ORM_Model {

    public function __construct() {
        parent::__construct();

        $this->fields = array(
            'id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            'id_map' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 10,
                'nullable' => FALSE,
            )),
            'id_patient' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            'queue' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
        );
    }

    public static function data_source() {
        return 'default';
    }

    public static function table() {
        return 'patient_map';
    }

    public static function primary_key() {
        return array('id');
    }

    public function get_maps_id ($id_patient)
    {
        $id_maps = array();
        $patient_maps = DB_ORM::select('Patient_Map')->where('id_patient', '=', $id_patient)->query()->as_array();
        foreach ($patient_maps as $patient_map)
        {
            $id_maps[$patient_map->id] = $patient_map->id_map;
        }
        return $id_maps;
    }

    public function get_patients_id ($id_map)
    {
        $id_patients = array();
        $patient_maps = DB_ORM::select('Patient_Map')->where('id_map', '=', $id_map)->query()->as_array();
        foreach ($patient_maps as $patient_map)
        {
            $id_patients[] = $patient_map->id_patient;
        }
        return $id_patients;
    }
}





