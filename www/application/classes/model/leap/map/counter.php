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
class Model_Leap_Map_Counter extends DB_ORM_Model {

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
            
            'name' => new DB_ORM_Field_String($this, array(
                'max_length' => 200,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            
            'description' => new DB_ORM_Field_Text($this, array(
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            
            'start_value' => new DB_ORM_Field_Double($this, array(
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            
            'icon_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            
            'prefix' => new DB_ORM_Field_String($this, array(
                'max_length' => 20,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            
            'suffix' => new DB_ORM_Field_String($this, array(
                'max_length' => 20,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            
            'visible' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 1,
                'default' => 0,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            
            'out_of' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),

            'status' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 1,
                'nullable' => FALSE,
            )),
        );
        
        $this->relations = array(
            'map' => new DB_ORM_Relation_BelongsTo($this, array(
                'child_key' => array('map_id'),
                'parent_key' => array('id'),
                'parent_model' => 'map',
            )),
            
            'icon' => new DB_ORM_Relation_BelongsTo($this, array(
                'child_key' => array('icon_id'),
                'parent_key' => array('id'),
                'parent_model' => 'map_element',
            )),
        );
    }

    public static function data_source() {
        return 'default';
    }

    public static function table() {
        return 'map_counters';
    }

    public static function primary_key() {
        return array('id');
    }
    
    public function getCountersByMap ($mapId, $lengthSort = false)
    {
        $counters = DB_ORM::select('Map_Counter')->where('map_id', '=', $mapId)->query()->as_array();

        if ($lengthSort) {
            usort(
                $counters,
                function ($a, $b) {
                    return strlen($b->name) - strlen($a->name);
                }
            );
        }
        return $counters;
    }

    public function addCounter($mapId, $values) {
        $visible = Arr::get($values, 'cVisible', FALSE);

        $this->map_id       = $mapId;
        $this->name         = Arr::get($values, 'cName', '');
        $this->description  = Arr::get($values, 'cDesc', '');
        $this->icon_id      = Arr::get($values, 'cIconId', NULL);
        $this->start_value  = str_replace(',','.', Arr::get($values, 'cStartV', 0));
        $this->visible      = $visible;
        $this->status       = $this->try_become_a_main(Arr::get($values, 'status', 0), $mapId);
        $this->save();

        $lastCounter = $this->getLastAddedCounter($mapId);

        if ($visible == 2) $visible = 1;

        $nodes = DB_ORM::model('map_node')->getNodesByMap($mapId);
        if(count($nodes) > 0) {
            foreach($nodes as $node) {
                $newMapCounter = DB_ORM::model('map_node_counter');
                $newMapCounter->counter_id = $lastCounter->id;
                $newMapCounter->node_id = $node->id;
                $newMapCounter->display = $visible;
                $newMapCounter->save();
            }
        }

        return $lastCounter;
    }

    public function getLastAddedCounter($mapId){
        $builder = DB_SQL::select('default')->from($this->table())->where('map_id', '=', $mapId)->order_by('id', 'DESC')->limit(1);
        $result = $builder->query();

        if($result->is_loaded()) {
            $counter = DB_ORM::model('map_counter', array($result[0]['id']));
            return $counter;
        }
        return NULL;
    }
    
    public function updateCounter($counterId, $values, $updateVisible = true) {
        $this->id = $counterId;
        $this->load();
        
        if($this) {
            $visible = Arr::get($values, 'cVisible', $this->visible);

            $this->name         = Arr::get($values, 'cName', $this->name);
            $this->description  = Arr::get($values, 'cDesc', $this->description);
            $this->icon_id      = Arr::get($values, 'cIconId', $this->icon_id);
            $this->start_value  = str_replace(',','.', Arr::get($values, 'cStartV', $this->start_value));
            $this->visible      = $visible;
            $this->status       = $this->try_become_a_main(Arr::get($values, 'status', $this->status), $this->map_id);

            if (($visible != 2) AND ($updateVisible)){
                DB_ORM::model('map_node_counter')->updateVisibleForCounters($this->map_id, $counterId, $visible);
            }
            $this->save();
        }
    }

    public function duplicateCounters($fromMapId, $toMapId, $nodesMap, $elementsMap)
    {
        if( ! $toMapId) return array();

        $counterMap = array();

        foreach ($this->getCountersByMap($fromMapId) as $counter)
        {
            $counterMap[$counter->id] = DB_ORM::insert('map_counter')
                ->column('map_id',      $toMapId)
                ->column('name',        $counter->name)
                ->column('description', $counter->description)
                ->column('start_value', $counter->start_value)
                ->column('icon_id',     Arr::get($elementsMap, $counter->icon_id))
                ->column('prefix',      $counter->prefix)
                ->column('suffix',      $counter->suffix)
                ->column('visible',     $counter->visible)
                ->column('out_of',      $counter->out_of)
                ->execute();

            DB_ORM::model('map_counter_rule')->duplicateRules($counter->id, $counterMap[$counter->id], $nodesMap);
        }
        return $counterMap;
    }

    /**
     * @param $points string with value of all counters and main counter max_value
     * @param $map_id
     * @return info about progress in fraction and percentage
     */
    public function progress ($points, $map_id)
    {
        $progress = NULL;

        // get main counter for map
        $main_counter_id = DB_SQL::select('default')->from($this->table())->where('map_id', '=', $map_id, 'AND')->where('status', '=', 1)->query();

        // get max value and id of main counter
        preg_match('/(MCID=)(?<id>\d+),V=(?<max_value>\d+)/', $points, $main_counter);
        if ($main_counter_id AND (Arr::get(Arr::get($main_counter_id, 0, array()), 'id') == Arr::get($main_counter, 'id', 'not_exist')))
        {
            // get user ms\ain counter score
            preg_match('/CID='.$main_counter['id'].',V=(?<user_points>\d+)/', $points, $user_points);
            $progress = $user_points['user_points'];
        }
        return $progress;
    }

    /**
     * If status 1 (main), then all other counters deprived main status
     * @param $status - status of counter
     * @param $map_id - map where counter try to be main
     * @return int status of counter
     */
    private function try_become_a_main ($status, $map_id)
    {
        if ($status == 0) return $status;
        foreach (DB_SQL::select('default')->from($this->table())->where('map_id', '=', $map_id, 'AND')->where('status', '=', $status)->query() as $obj)
        {
            $counter = DB_ORM::model('map_counter', $obj['id']);
            $counter->status = 0;
            $counter->save();
        }
        return $status;
    }
}