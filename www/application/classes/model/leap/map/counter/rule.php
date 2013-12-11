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
 * Model for map_counter_rules table in database 
 */
class Model_Leap_Map_Counter_Rule extends DB_ORM_Model {

    public function __construct() {
        parent::__construct();

        $this->fields = array(
            'id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
                'unsigned' => TRUE,
            )),
            
            'counter_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            
            'relation_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            
            'value' => new DB_ORM_Field_Double($this, array(
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            
            'function' => new DB_ORM_Field_String($this, array(
                'max_length' => 50,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            
            'redirect_node_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            
            'message' => new DB_ORM_Field_String($this, array(
                'max_length' => 500,
                'nullable' => TRUE,
                'savable' => TRUE,
            )),
            
            'counter' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            
            'counter_value' => new DB_ORM_Field_String($this, array(
                'max_length' => 50,
                'nullable' => TRUE,
                'savable' => TRUE,
            )),
        );
        
        $this->relations = array(
            'counter' => new DB_ORM_Relation_BelongsTo($this, array(
                'child_key' => array('counter_id'),
                'parent_key' => array('id'),
                'parent_model' => 'map_counter',
            )),
            
            'relation' => new DB_ORM_Relation_HasOne($this, array(
                'child_key' => array('id'),
                'parent_key' => array('relation_id'),
                'child_model' => 'map_counter_relation',
            )),
            
            'redirect_node' => new DB_ORM_Relation_BelongsTo($this, array(
                'child_key' => array('redirect_node_id'),
                'parent_key' => array('id'),
                'parent_model' => 'map_node',
            )),
        );
    }

    public static function data_source() {
        return 'default';
    }

    public static function table() {
        return 'map_counter_rules';
    }

    public static function primary_key() {
        return array('id');
    }
    
    
    public function getRulesByCounterId($counterId) {
        $builder = DB_SQL::select('default')->from($this->table())->where('counter_id', '=', $counterId);
        $result = $builder->query();
        
        if($result->is_loaded()) {
            $rules = array();
            foreach($result as $record) {
                $rules[] = DB_ORM::model('map_counter_rule', array((int)$record['id']));
            }
            
            return $rules;
        }
        
        return array();
    }
    
    public function addRule($counterId, $values) {
        $this->counter_id = $counterId;
        $this->relation_id = Arr::get($values, 'relation', 1);
        $this->value = str_replace(',','.', Arr::get($values, 'rulevalue', 0));
        $this->function = 'redir';                                              // In current version
        $this->redirect_node_id = Arr::get($values, 'node', 0);
        $ctrval = Arr::get($values, 'ctrval', '=0');
        if (!empty($ctrval)){
            $this->counter_value = $ctrval;
        }else{
            $this->counter_value = '=0';
        }
        $this->save();
    }
    
    public function duplicateRules($fromCounterId, $toCounterId, $nodeMap) {
        $rules = $this->getRulesByCounterId($fromCounterId);
        
        if($toCounterId == null || $toCounterId <= 0) return;
        
        foreach($rules as $rule) {
            $builder = DB_ORM::insert('map_counter_rule')
                    ->column('counter_id', $toCounterId)
                    ->column('relation_id', $rule->relation_id)
                    ->column('value', $rule->value)
                    ->column('function', $rule->function)
                    ->column('message', $rule->message)
                    ->column('counter', $rule->counter)
                    ->column('counter_value', $rule->counter_value);
            
            if(isset($nodeMap[$rule->redirect_node_id]))
                $builder = $builder->column ('redirect_node_id', $nodeMap[$rule->redirect_node_id]);
            
            $builder->execute();
        }
    }

    public function exportMVP($counterId) {
        $builder = DB_SQL::select('default')->from($this->table())->where('counter_id', '=', $counterId);
        $result = $builder->query();

        if($result->is_loaded()) {
            $rules = array();
            foreach($result as $record) {
                $rules[] = $record;
            }

            return $rules;
        }

        return NULL;
    }
}

?>