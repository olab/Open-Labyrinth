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

class Model_Leap_Map_VisualDisplay_Panel extends DB_ORM_Model {

    public function __construct() {
        parent::__construct();
        $this->fields = array(
            'id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 10,
                'nullable' => FALSE,
                'unsigned' => TRUE,
            )),
            
            'visual_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 10,
                'nullable' => FALSE,
                'unsigned' => TRUE,
            )),
            
            'x' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => TRUE,
                'unsigned' => FALSE,
                'default' => 0
            )),
            
            'y' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => TRUE,
                'unsigned' => FALSE,
                'default' => 0
            )),
            
            'width' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => TRUE,
                'unsigned' => FALSE
            )),
            
            'height' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => TRUE,
                'unsigned' => FALSE
            )),
            
            'background_color' => new DB_ORM_Field_String($this, array(
                'max_length' => 40,
                'nullable' => TRUE,
                'savable' => TRUE,
            )),
            
            'border_size' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 10,
                'nullable' => FALSE,
                'unsigned' => TRUE,
                'default' => 0
            )),
            
            'border_color' => new DB_ORM_Field_String($this, array(
                'max_length' => 40,
                'nullable' => TRUE,
                'savable' => TRUE,
            )),
            
            'border_radius' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 10,
                'nullable' => FALSE,
                'unsigned' => TRUE,
                'default' => 0
            )),
            
            'angle' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => TRUE,
                'unsigned' => FALSE
            )),
            
            'z_index' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 10,
                'nullable' => TRUE,
                'unsigned' => TRUE,
            ))
        );
        
        $this->relations = array(
            'visual' => new DB_ORM_Relation_BelongsTo($this, array(
                'child_key' => array('visual_id'),
                'parent_key' => array('id'),
                'parent_model' => 'Map_VisualDisplay',
            ))
        );
    }
    
    public static function data_source() {
        return 'default';
    }

    public static function table() {
        return 'map_visual_display_panels';
    }

    public static function primary_key() {
        return array('id');
    }
};
?>