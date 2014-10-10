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
 * Model for map_feedback_rules table in database 
 */
class Model_Leap_Map_Feedback_Rule extends DB_ORM_Model {

    public function __construct() {
        parent::__construct();

        $this->fields = array(
            'id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
                'unsigned' => TRUE,
            )),
            
            'map_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            
            'rule_type_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            
            'operator_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            
            'counter_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            
            'value' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            
            'message' => new DB_ORM_Field_Text($this, array(
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
        );
        
        $this->relations = array(
            'map' => new DB_ORM_Relation_BelongsTo($this, array(
                'child_key' => array('map_id'),
                'parent_key' => array('id'),
                'parent_model' => 'map',
            )),
            
            'type' => new DB_ORM_Relation_BelongsTo($this, array(
                'child_key' => array('rule_type_id'),
                'parent_key' => array('id'),
                'parent_model' => 'map_feedback_type',
            )),
            
            'operator' => new DB_ORM_Relation_BelongsTo($this, array(
                'child_key' => array('operator_id'),
                'parent_key' => array('id'),
                'parent_model' => 'map_feedback_operator',
            )),
            
            'counter' => new DB_ORM_Relation_BelongsTo($this, array(
                'child_key' => array('counter_id'),
                'parent_key' => array('id'),
                'parent_model' => 'map_counter',
            )),
        );
    }

    public static function data_source() {
        return 'default';
    }

    public static function table() {
        return 'map_feedback_rules';
    }

    public static function primary_key() {
        return array('id');
    }
    
    public function getRulesByMap ($mapId)
    {
        $builder = DB_SQL::select('default')->from($this->table())->where('map_id', '=', $mapId);
        $result = $builder->query();
        
        if($result->is_loaded())
        {
            $rules = array();
            foreach($result as $record)$rules[] = DB_ORM::model('map_feedback_rule', array((int)$record['id']));
            return $rules;
        }
        return array();
    }
    
    public function getAllRules() {
        $builder = DB_SQL::select('default')->from($this->table());
        $result = $builder->query();
        
        if($result->is_loaded()) {
            $rules = array();
            foreach($result as $record) {
                $rules[] = DB_ORM::model('map_feedback_rule', array((int)$record['id']));
            }
            
            return $rules;
        }
        
        return NULL;
    }
    
    public function getRulesByTypeName($typeName) {
        $rules = $this->getAllRules();
        if($rules != NULL and count($rules) > 0) {
            $result = array();
            foreach($rules as $rule) {
                if($rule->type->name == $typeName) {
                    $result[] = $rule;
                }
            }
            
            return $result;
        }
        
        return NULL;
    }
    
    public function getRulesByTypeNameAndMapId($typeName, $mapId) {
        $rules = $this->getRulesByTypeName($typeName);
        if($rules != null && count($rules) > 0) {
            $result = array();
            foreach($rules as $rule) {
                if($rule->map_id == $mapId)
                    $result[] = $rule;
            }
            
            return count($result) > 0 ? $result : NULL;
        }
        
        return NULL;
    }
    
    public function addRule($mapId, $typeName, $values) {
        switch($typeName) {
            case 'time':
                $this->addTimeRule($mapId, $values);
                break;
            case 'visit':
                $this->addVisitRule($mapId, $values);
                break;
            case 'must':
                $type = Arr::get($values, 'crtype', NULL);
                if($type != NULL) {
                    switch($type){
                        case 'mustvisit':
                            $this->addMustVisitRule($mapId, $values);
                            break;
                        case 'mustavoid':
                            $this->addMustAvoidRule($mapId, $values);
                            break;
                    }
                }
                break;
            case 'counter':
                $this->addCounterRule($mapId, $values);
                break;
        }
    }
    
    public function duplicateRules($fromMapId, $toMapId)
    {
        if( ! $toMapId) return;
        
        foreach($this->getRulesByMap($fromMapId) as $rule)
        {
            DB_ORM::insert('map_feedback_rule')
                ->column('map_id', $toMapId)
                ->column('rule_type_id', $rule->rule_type_id)
                ->column('operator_id', $rule->operator_id)
                ->column('counter_id', $rule->counter_id)
                ->column('value', $rule->value)
                ->column('message', $rule->message)
                ->execute();
        }
    }
    
    private function addTimeRule($mapId, $values) {
        $this->map_id = $mapId;
        $this->rule_type_id = 1;
        $this->operator_id = Arr::get($values, 'cop', 0);
        $this->value = Arr::get($values, 'cval', 0);
        $this->message = Arr::get($values, 'cMess', '');
        
        $this->save();
    }
    
    private function addVisitRule($mapId, $values) {
        $this->map_id = $mapId;
        $this->rule_type_id = 3;
        $this->value = Arr::get($values, 'cval', '');
        $this->message = Arr::get($values, 'cMess', '');
        
        $this->save();
    }
    
    private function addMustVisitRule($mapId, $values) {
        $this->map_id = $mapId;
        $this->rule_type_id = 4;
        $this->operator_id = Arr::get($values, 'cop', 0);
        $this->value = Arr::get($values, 'cval', '');
        $this->message = Arr::get($values, 'cMess', '');
        
        $this->save();
    }
    
    private function addMustAvoidRule($mapId, $values) {
        $this->map_id = $mapId;
        $this->rule_type_id = 5;
        $this->operator_id = Arr::get($values, 'cop', 0);
        $this->value = Arr::get($values, 'cval', '');
        $this->message = Arr::get($values, 'cMess', '');
        
        $this->save();
    }
    
    private function addCounterRule($mapId, $values) {
        $counterId = Arr::get($values, 'cid', NULL);
        if($counterId != NULL) {
            $this->map_id = $mapId;
            $this->rule_type_id = 2;
            $this->counter_id = $counterId;
            $this->operator_id = Arr::get($values, 'cop', 0);
            $this->value = Arr::get($values, 'cval', '');
            $this->message = Arr::get($values, 'cMess', '');

            $this->save();
        }
    }
}