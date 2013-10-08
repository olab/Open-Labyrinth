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
 * Model for map_counters table in database 
 */
class Model_Leap_Map_Popup extends DB_ORM_Model {

    public function __construct() {
        parent::__construct();

        $this->fields = array(
            'id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),

            'title' => new DB_ORM_Field_String($this, array(
                'max_length' => 255,
                'nullable' => FALSE,
            )),
            
            'text' => new DB_ORM_Field_Text($this, array(
                'nullable' => FALSE,
            )),
            
            'height' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            
            'width' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),

            'position_type' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 1,
                'nullable' => FALSE,
            )),

            'position' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 1,
                'nullable' => FALSE,
            )),

            'time_before' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),

            'time_length' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),

            'color' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),

            'color_custom' => new DB_ORM_Field_String($this, array(
                'max_length' => 255,
                'nullable' => FALSE,
            )),
            
            'map_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            
            'assign_to_node' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 1,
                'default' => 0,
                'nullable' => FALSE,
            )),

            'node_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'default' => NULL,
            )),

            'enabled' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 1,
                'default' => NULL,
            )),
        );

    }

    public static function data_source() {
        return 'default';
    }

    public static function table() {
        return 'map_popup';
    }

    public static function primary_key() {
        return array('id');
    }

    public function addMessage($mapId, $arrayPost) {
        $this->map_id = $mapId;
        $this->title = $arrayPost['title'];
        $this->text = $arrayPost['text'];
        $this->position = $arrayPost['position'];
        $this->time_before = $arrayPost['time_before'];
        $this->time_length = $arrayPost['time_length'];
        $this->position_type = $arrayPost['position_type'];
        $this->color = $arrayPost['color'];
        if ($arrayPost['color_default'] == 1) {
            $this->color_custom = '';
        } else {
            $this->color_custom = $arrayPost['color_code'];
        }
        $this->assign_to_node = $arrayPost['assign_to_node'];
        $this->node_id = $arrayPost['node_id'];
        $this->enabled = $arrayPost['enabled'];

        $this->save();
    }

    public function editMessage($messageId, $arrayPost) {
        $this->id = $messageId;
        $this->load();
        if ($this->is_loaded()){
                $this->title = $arrayPost['title'];
                $this->text = $arrayPost['text'];
                $this->position = $arrayPost['position'];
                $this->time_before = $arrayPost['time_before'];
                $this->time_length = $arrayPost['time_length'];
                $this->position_type = $arrayPost['position_type'];
                $this->color = $arrayPost['color'];
                if ($arrayPost['color_default'] == 1) {
                    $this->color_custom = '';
                } else {
                    $this->color_custom = $arrayPost['color_code'];
                }
                $this->assign_to_node = $arrayPost['assign_to_node'];
                $this->node_id = $arrayPost['node_id'];
                $this->enabled = $arrayPost['enabled'];

                $this->save();
            return true;
        }
        return false;
    }

    public function getAllMessageByMap($mapId) {
        $builder = DB_SQL::select('default')->from($this->table())
            ->where('map_id', '=', $mapId);

        $result = $builder->query();
        if($result->is_loaded()) {
            $messages = array();
            foreach($result as $record) {
                $messages[] = DB_ORM::model($this->table(), array((int)$record['id']));
            }
            return $messages;
        }

        return NULL;
    }

    public function getEnabledLabyrinthMessageByMap($mapId) {
        $builder = DB_SQL::select('default')->from($this->table())
            ->where('map_id', '=', $mapId)
            ->where('enabled','=',1);

        $result = $builder->query();
        if($result->is_loaded()) {
            $messages = array();
            foreach($result as $record) {
                $messages[] = DB_ORM::model($this->table(), array((int)$record['id']));
            }
            return $messages;
        }

        return NULL;
    }
    

}

?>