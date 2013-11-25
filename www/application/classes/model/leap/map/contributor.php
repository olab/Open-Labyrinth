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
 * Model for map_contributors table in database 
 */
class Model_Leap_Map_Contributor extends DB_ORM_Model {

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
            'role_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            'name' => new DB_ORM_Field_String($this, array(
                'max_length' => 200,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            'organization' => new DB_ORM_Field_String($this, array(
                'max_length' => 200,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            'order' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
        );
        
        $this->relations = array(
            'role' => new DB_ORM_Relation_BelongsTo($this, array(
                'child_key' => array('role_id'),
                'parent_key' => array('id'),
                'parent_model' => 'map_contributor_role',
            )),
        );
    }

    public static function data_source() {
        return 'default';
    }

    public static function table() {
        return 'map_contributors';
    }

    public static function primary_key() {
        return array('id');
    }
    
    public function getAllContributors($mapId) {
        $builder = DB_SQL::select('default')->from($this->table())->where('map_id', '=', $mapId)->order_by('order', 'ASC');
        $result = $builder->query();
        
        if($result->is_loaded()) {
            $contributors = array();
            foreach($result as $record) {
                $contributors[] = DB_ORM::model('map_contributor', array($record['id']));
            }
            
            return $contributors;
        }
        
        return NULL;
    }
    
    public function createContributor($mapId) {
        $this->map_id = $mapId;
        $this->role_id = 13; // 'Select' role default
        $this->save();
    }

    public function createContributorFromValues($values) {
        if($values == null || !isset($values['map_id'])) { return; }

        $this->map_id  = $values['map_id'];
        $this->role_id = Arr::get($values, 'role_id', 13);
        $this->name    = Arr::get($values, 'name', '');
        $this->order   = Arr::get($values, 'order', 1);

        $this->save();
    }

    public function updateContributors($mapId, $values) {
        $contibutors = $this->getAllContributors($mapId);
        if(count($contibutors) > 0) {
            foreach($contibutors as $contributor) {
                $role = Arr::get($values, 'role_'.$contributor->id, NULL);
                $name = Arr::get($values, 'cname_'.$contributor->id, '');
                $organization = Arr::get($values, 'cnorg_'.$contributor->id, '');
                $order = Arr::get($values, 'corder_'.$contributor->id, 1);

                if($role != NULL) {
                    $contributor->role_id = $role;
                }

                if($name != NULL) {
                    $contributor->name = $name;
                }

                if($organization != NULL) {
                    $contributor->organization = $organization;
                }

                $contributor->order = $order;

                $contributor->save();
            }
        }
    }

    public function duplicateContributors($fromMapId, $toMapId) {
        $contributors = $this->getAllContributors($fromMapId);

        if($contributors == null || $toMapId == null || $toMapId <= 0) return;

        foreach($contributors as $contributor) {
            $builder = DB_ORM::insert('map_Contributor')
                    ->column('map_id', $toMapId)
                    ->column('role_id', $contributor->role_id)
                    ->column('name', $contributor->name)
                    ->column('organization', $contributor->organization);

            $builder->execute();
        }
    }

    public function exportMVP($mapId) {
        $builder = DB_SQL::select('default')->from($this->table())->where('map_id', '=', $mapId);
        $result = $builder->query();

        if($result->is_loaded()) {
            $contributors = array();
            foreach($result as $record) {
                $contributors[] = $record;
            }

            return $contributors;
        }

        return NULL;
    }
}

