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
class Model_Leap_Patient_Assign extends DB_ORM_Model {

    public function __construct() {
        parent::__construct();

        $this->fields = array(
            'id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            'id_assign' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            'id_group' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 10,
                'nullable' => TRUE,
                'unsigned' => TRUE,
            )),
            'id_user' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 10,
                'nullable' => TRUE,
                'unsigned' => TRUE,
            )),
            'queue' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            'user_or_group' => new DB_ORM_Field_String($this, array(
                'max_length' => 45,
                'enum' => array('user','group'),
                'nullable' => FALSE,
            )),
            'status' => new DB_ORM_Field_String($this, array(
                'max_length' => 45,
                'enum' => array('played','completed','frozen'),
                'nullable' => FALSE,
            )),
        );
    }

    public static function data_source() {
        return 'default';
    }

    public static function table() {
        return 'patient_assign';
    }

    public static function primary_key() {
        return array('id');
    }

    public function get_new_index()
    {
        $result = DB_ORM::select('Patient_Assign')->group_by('id_assign')->query()->as_array();
        $result = array_pop($result);
        $result = $result ? $result->id_assign : 0;
        return $result + 1;
    }

    public function update($id_assign , array $assign)
    {

            if($id_assign)
            {

            }
            else
            {
                $id_assign  = $this->get_new_index();
                foreach($assign as $k=>$v)
                {
                    $u_or_g     = key($v);
                    DB_ORM::insert('Patient_Assign')
                        ->column('id_assign', $id_assign)
                        ->column('id_'.$u_or_g, $v[$u_or_g])
                        ->column('queue', $k)
                        ->column('user_or_group', $u_or_g)
                        ->execute();
                }
            }
        return $id_assign;
    }

}





