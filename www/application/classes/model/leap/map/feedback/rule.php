<?php defined('SYSPATH') or die('No direct script access.');

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

?>