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
class Model_Leap_Map_Counter_CommonRules extends DB_ORM_Model {

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
            
            'rule' => new DB_ORM_Field_Text($this, array(
                'nullable' => FALSE,
                'savable' => TRUE,
            )),

            'isCorrect' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 1,
                'savable' => TRUE,
            ))
        );
        
        $this->relations = array(
            'map' => new DB_ORM_Relation_BelongsTo($this, array(
                'child_key' => array('map_id'),
                'parent_key' => array('id'),
                'parent_model' => 'map',
            )),
        );
    }

    public static function data_source() {
        return 'default';
    }

    public static function table() {
        return 'map_counter_common_rules';
    }

    public static function primary_key() {
        return array('id');
    }
    
    
    public function getRulesByMapId($mapId, $type = '')
    {
        $rules = array();

        if ($type == 'all')
        {
            $builder = DB_SQL::select('default')->from($this->table())->where('map_id', '=', $mapId);
        }
        else
        {
            $builder = DB_SQL::select('default')->from($this->table())
                ->where('map_id', '=', $mapId)
                ->where('isCorrect' , '=', 1);
        }

        $result = $builder->query();

        foreach ($result as $record) {
            $rules[] = DB_ORM::model('map_counter_commonrules', array((int)$record['id']));
        }
            
        return $rules;
    }
    
    public function addRule($mapId, $rule, $isCorrect) {
        $this->map_id = $mapId;
        $this->rule = $rule;
        $this->isCorrect = $isCorrect;

        $this->save();
    }

    public function editRule($ruleId, $rule, $isCorrect) {
        $this->id = $ruleId;
        $this->load();

        if ($this->is_loaded()){
            $this->rule = $rule;
            $this->isCorrect = $isCorrect;

            $this->save();
            return true;
        }
        return false;
    }

    public function exportMVP($mapId) {
        $builder = DB_SQL::select('default')->from($this->table())->where('map_id', '=', $mapId);
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

    public function duplicateRule($oldMapId, $newMapId, $counterMap, $nodeMap, $questionMap)
    {
        foreach (DB_ORM::select('Map_Counter_Commonrules')->where('map_id', '=', $oldMapId)->query()->as_array() as $rule)
        {
            $newRule = new $this;
            $newRule->map_id = $newMapId;
            $newRule->isCorrect = $rule->isCorrect;

            $result = $rule->rule;
            $codes = array('CR', 'NODE', 'QU_ANSWER');

            foreach ($codes as $code)
            {
                $regExp = '/[\['.$code.':\d\]]+/';
                if (preg_match_all($regExp, $result, $matches))
                {
                    foreach ($matches as $match)
                    {
                        foreach ($match as $value)
                        {
                            if (stristr($value, '[['.$code.':'))
                            {
                                $m = explode(':', $value);
                                $id = substr($m[1], 0, strlen($m[1]) - 2);
                                if (is_numeric($id))
                                {
                                    $replaceString = '';
                                    switch ($code) {
                                        case 'CR':
                                            if(isset($counterMap[(int)$id]))
                                                $replaceString = '[['.$code.':'.$counterMap[(int)$id].']]';
                                            break;
                                        case 'NODE':
                                            if(isset($nodeMap[(int)$id]))
                                                $replaceString = '[['.$code.':'.$nodeMap[(int)$id].']]';
                                            break;
                                        case 'QU_ANSWER':
                                            if(isset($questionMap[(int)$id]))
                                                $replaceString = '[['.$code.':'.$questionMap[(int)$id].']]';
                                            break;
                                    }
                                    $result = str_replace('[['.$code.':'.$id.']]', $replaceString, $result);
                                }
                            }
                        }
                    }
                }
            }
            $newRule->rule = $result;
            $newRule->save();
        }
    }
}