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
 * Model for map_counter_rule_relations table in database 
 */
class Model_Leap_Map_Node_Counter extends DB_ORM_Model {

    public function __construct() {
        parent::__construct();

        $this->fields = array(
            'id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
                'unsigned' => TRUE,
            )),
            
            'node_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            
            'counter_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            
            'function' => new DB_ORM_Field_String($this, array(
                'max_length' => 20,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),

            'display' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
        );
        
        $this->relations = array(
            'node' => new DB_ORM_Relation_BelongsTo($this, array(
                'child_key' => array('node_id'),
                'parent_key' => array('id'),
                'parent_model' => 'map_node',
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
        return 'map_node_counters';
    }

    public static function primary_key() {
        return array('id');
    }

    public function getAllNodeCounters() {
        $builder = DB_SQL::select('default')->from($this->table());
        $result = $builder->query();
        
        if($result->is_loaded()) {
            $counters = array();
            foreach($result as $record) {
                $counters[] = DB_ORM::model('map_node_counter', array((int)$record['id']));
            }
            
            return $counters;
        }
        
        return NULL;
    }

    public function getNodeCountersByMap($map_id) {
        $builder = DB_SQL::select('default', array('map_node_counters.id', 'map_node_counters.node_id', 'map_node_counters.counter_id', 'map_node_counters.function', 'map_node_counters.display'))->from('map_counters')->join('RIGHT', 'map_node_counters')->on('map_node_counters.counter_id', '=', 'map_counters.id')->where('map_id', '=', $map_id);
        $result = $builder->query();

        if($result->is_loaded()) {
            $counters = array();
            foreach($result as $record) {
                $counters[] = DB_ORM::model('map_node_counter', array((int)$record['id']));
            }

            return $counters;
        }

        return NULL;
    }

    public function getNodeCounter($nodeId, $counterId) {
        $builder = DB_SQL::select('default')
                ->from($this->table())
                ->where('node_id', '=', $nodeId, 'AND')
                ->where('counter_id', '=', $counterId);
        $result = $builder->query();
        
        if($result->is_loaded()) {   
            return DB_ORM::model('map_node_counter', array((int)$result[0]['id']));
        }
        
        return NULL;
    }
    
    public function addNodeCounter($nodeId, $counterId, $function, $display = 1) {
        $builder = DB_SQL::select('default')
                ->from($this->table())
                ->where('node_id', '=', $nodeId, 'AND')
                ->where('counter_id', '=', $counterId);
        
        $result = $builder->query();
        
        if(!$result->is_loaded()){
            $this->node_id = $nodeId; 
            $this->counter_id = $counterId;
            $this->function = str_replace(',', '.', $function);
            $this->display = $display;

            $this->save();
            $this->reset();
        } else {
            $this->updateNodeCounter($nodeId, $counterId, $function, $display);
        }
    }

    public function deleteNodeCounter($nodeId, $counterId) {
        $builder = DB_SQL::select('default')
                ->from($this->table())
                ->where('node_id', '=', $nodeId, 'AND')
                ->where('counter_id', '=', $counterId);
        $result = $builder->query();
        
        if($result->is_loaded()) {
            DB_ORM::model('map_node_counter', array((int)$result[0]['id']))->delete();
        }
    }
    
    public function deleteAllNodeCounterByCounter($counterId) {
        $builder = DB_SQL::select('default')
                ->from($this->table())
                ->where('counter_id', '=', $counterId);
        $result = $builder->query();
        
        if($result->is_loaded()) {
            foreach($result as $record) {
                DB_ORM::model('map_node_counter', array((int)$record['id']))->delete();
            }
        }
    }
    
    public function updateNodeCounter($nodeId, $counterId, $function, $display = 1) {
        $builder = DB_SQL::select('default')
                ->from($this->table())
                ->where('node_id', '=', $nodeId, 'AND')
                ->where('counter_id', '=', $counterId);
        
        $result = $builder->query();
        
        if($result->is_loaded()){
            $this->id = $result[0]['id'];
            $this->load();
            
            if($this) {
                $this->function = str_replace(',', '.', $function);
                $this->display = $display;

                $this->save();
            }
        }
    }

    public function updateVisibleForCounters($mapId, $counterId, $visible){
        $existingCounterNode = array();
        $nodes = DB_ORM::model('map_node')->getNodesByMap($mapId);
        $builder = DB_SQL::select('default')
            ->from($this->table())
            ->where('counter_id', '=', $counterId);

        $result = $builder->query();

        if ($result->is_loaded()){
            if (count($result) > 0){
                foreach($result as $r){
                    $this->id = $r['id'];
                    $this->load();

                    if($this) {
                        $existingCounterNode[$this->node_id] = 1;
                        $this->display = $visible;
                        $this->save();
                    }
                }
            }
        }

        if (count($nodes) > 0){
            foreach($nodes as $node){
                if (Arr::get($existingCounterNode, $node->id, 0) != 1){
                    $newMapCounter = DB_ORM::model('map_node_counter');
                    $newMapCounter->counter_id = $counterId;
                    $newMapCounter->node_id = $node->id;
                    $newMapCounter->display = $visible;
                    $newMapCounter->save();
                }
            }
        }
    }

    public function updateNodeCounterByNode($nodeId, $map_id, $values) {
        $counters = DB_ORM::model('map_counter')->getCountersByMap($map_id);
        if(count($counters) > 0) {
            foreach($counters as $counter) {
                $function = str_replace(',', '.', Arr::get($values, 'cfunc_'.$counter->id));
                $display = Arr::get($values, 'cfunc_ch_'.$counter->id);
                $nodeCounter = DB_ORM::model('map_node_counter')->getNodeCounter($nodeId, $counter->id);
                if($nodeCounter != NULL) {
                    $this->updateNodeCounter($nodeId, $counter->id, $function, $display);
                } else {
                    $this->addNodeCounter($nodeId, $counter->id, $function, $display);
                }
            }
        }
    }
    
    public function updateNodeCounters($values, $counterId = NULL, $mapId = NULL) {
        $changeToCustom = array();
        $counters = DB_ORM::model('map_node_counter')->getNodeCountersByMap($mapId);
        if(count($counters) > 0) {
            foreach($counters as $counter) {
                $inputName = '';
                if($counterId != NULL) {
                    if($counterId == $counter->counter_id) {
                        $inputName = 'nc_'.$counter->node_id.'_'.$counter->counter_id;
                        $chName = 'ch_'.$counter->node_id.'_'.$counter->counter_id;
                        $counter->function = str_replace(',', '.', Arr::get($values, $inputName, $counter->function));
                        if (Arr::get($values, $chName, NULL) == NULL){
                            $counter->display = 0;
                        }else{
                            $counter->display = 1;
                        }
                        $changeToCustom[$counter->counter_id][$counter->display] = true;
                        $counter->save();
                    }
                } else {
                    $inputName = 'nc_'.$counter->node_id.'_'.$counter->counter_id;
                    $chName = 'ch_'.$counter->node_id.'_'.$counter->counter_id;
                    $counter->function = str_replace(',', '.', Arr::get($values, $inputName, $counter->function));
                    if (Arr::get($values, $chName, NULL) == NULL){
                        $counter->display = 0;
                    }else{
                        $counter->display = 1;
                    }
                    $changeToCustom[$counter->counter_id][$counter->display] = true;
                    $counter->save();
                }
                if ($inputName){
                    unset($values[$inputName]);
                }
            }

            foreach($values as $key => $value){
                if ((strpos($key, 'nc_') !== false)){
                    $array = explode('_', $key);
                    $display = Arr::get($values, 'ch_'.$array[1].'_'.$array[2], 0);
                    $changeToCustom[$array[2]][$display] = true;
                    $this->addNodeCounter($array[1], $array[2], $value, $display);
                }
            }

            if (count($changeToCustom) > 0){
                foreach($changeToCustom as $counterId => $array){
                    if (count($array) == 2){
                        $array['cVisible'] = 2;
                        DB_ORM::model('map_counter')->updateCounter($counterId, $array, false);
                    } else {
                        if (isset($array[0])){
                            $array['cVisible'] = 0;
                            DB_ORM::model('map_counter')->updateCounter($counterId, $array, false);
                        } else {
                            $array['cVisible'] = 1;
                            DB_ORM::model('map_counter')->updateCounter($counterId, $array, false);
                        }
                    }

                }
            }
        } else {
            if($mapId != NULL) {
                $nodes = DB_ORM::model('map_node')->getNodesByMap($mapId);
                $counters = DB_ORM::model('map_counter')->getCountersByMap($mapId);
                if(count($counters) > 0) {
                    foreach($counters as $counter) {
                        if(count($nodes) > 0) {
                            foreach($nodes as $node) {
                                $newMapCounter = DB_ORM::model('map_node_counter');
                                $newMapCounter->counter_id = $counter->id;
                                $newMapCounter->node_id = $node->id;
                                $newMapCounter->display = 1;
                                $newMapCounter->save();
                            }
                        }
                    }
                }
                
                $this->updateNodeCounters($values, $counterId, $mapId);
            }
        }
    }
}

?>