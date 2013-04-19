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
 * Model for map_vpd_elements table in database 
 */
class Model_Leap_Map_Vpd_Element extends DB_ORM_Model {

    public function __construct() {
        parent::__construct();

        $this->fields = array(
            'id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
                'unsigned' => TRUE,
            )),
            
            'vpd_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 10,
                'nullable' => FALSE,
                'unsigned' => TRUE,
            )),
            
            'key' => new DB_ORM_Field_String($this, array(
                'max_length' => 100,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            
            'value' => new DB_ORM_Field_String($this, array(
                'max_length' => 500,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
        );
        
        $this->relations = array(
            'vpd' => new DB_ORM_Relation_BelongsTo($this, array(
                'child_key' => array('vpd_id'),
                'parent_key' => array('id'),
                'parent_model' => 'map_vpd',
            )),
        );
    }

    public static function data_source() {
        return 'default';
    }

    public static function table() {
        return 'map_vpd_elements';
    }

    public static function primary_key() {
        return array('id');
    }
    
    public function getValuesByVpdId($vpdId) {
        $builder = DB_SQL::select('default')->from($this->table())->where('vpd_id', '=', $vpdId);
        $result = $builder->query();
        
        if($result->is_loaded()) {
            $elements = array();
            foreach($result as $record) {
                $elements[] = DB_ORM::model('map_vpd_element', array((int)$record['id']));
            }
            
            return $elements;
        }
        
        return NULL;
    }
    
    public function saveElementValues($vpdId, $values) {
        $elements = $this->getValuesByVpdId($vpdId);
        
        if($elements != NULL) {
            if(count($elements) > 0) {
                foreach($elements as $element) {
                    $element->value = Arr::get($values, $element->key, $element->value);
                    $element->save();
                }
            }
        } else {
            if(count($values) > 0) {
                foreach($values as $key => $value) {
                    $newElement = DB_ORM::model('map_vpd_element');
                    $newElement->vpd_id = $vpdId;
                    $newElement->key = $key;
                    $newElement->value = $value;
                    
                    $newElement->save();
                }
            }
        }
    }
    
    public function duplicateElement($fromVpdId, $toVpdId) {
        $elements = $this->getValuesByVpdId($fromVpdId);
        
        if($elements == null || $toVpdId == null || $toVpdId <= 0) return;
        
        foreach($elements as $element) {
            $builder = DB_ORM::insert('map_vpd_element')
                    ->column('vpd_id', $toVpdId)
                    ->column('key', $element->key)
                    ->column('value', $element->value);
            
            $builder->execute();
        }
    }

    public function exportMVP($vpdId) {
        $builder = DB_SQL::select('default')->from($this->table())->where('vpd_id', '=', $vpdId);
        $result = $builder->query();

        if($result->is_loaded()) {
            $elements = array();
            foreach($result as $record) {
                $elements[] = $record;
            }

            return $elements;
        }

        return NULL;
    }
}

?>