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
 * Model for map_node_links table in database 
 */
class Model_Leap_Map_Node_Link extends DB_ORM_Model {

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
            
            'node_id_1' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
                'unsigned' => TRUE,
            )),
            
            'node_id_2' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            
            'image_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            
            'text' => new DB_ORM_Field_String($this, array(
                'max_length' => 500,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            
            'order' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            
            'probability' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
        );
        
        $this->relations = array(
            'map' => new DB_ORM_Relation_BelongsTo($this, array(
                'child_key' => array('map_id'),
                'parent_key' => array('id'),
                'parent_model' => 'map',
            )),
            
            'node_1' => new DB_ORM_Relation_BelongsTo($this, array(
                'child_key' => array('node_id_1'),
                'parent_key' => array('id'),
                'parent_model' => 'map_node',
            )),
            
            'node_2' => new DB_ORM_Relation_BelongsTo($this, array(
                'child_key' => array('node_id_2'),
                'parent_key' => array('id'),
                'parent_model' => 'map_node',
            )),
            
            'image' => new DB_ORM_Relation_HasOne($this, array(
                'child_key' => array('id'),
                'parent_key' => array('image_id'),
                'child_model' => 'map_element',
            )),
        );
        self::initialize_metadata($this);
    }

    private static function initialize_metadata($object)
    {
        $metadata = Model_Leap_Metadata::getMetadataRelations("map_node_link", $object);
        $object->relations = array_merge($object->relations, $metadata);
    }

    public static function data_source() {
        return 'default';
    }

    public static function table() {
        return 'map_node_links';
    }

    public static function primary_key() {
        return array('id');
    }
    
    public function addLink($mapId, $nodeId, $values) {
        $addNodeId = Arr::get($values, 'linkmnodeid', NULL);
        if($addNodeId != NULL) {
            $this->map_id = $mapId;
            $this->node_id_1 = $nodeId;
            $this->node_id_2 = $addNodeId;

            $this->image_id = Arr::get($values, 'linkImage', NULL);
            $this->text = Arr::get($values, 'linkLabel', '');
            
            $this->save();
            
            $linkDirection = Arr::get($values, 'linkDirection', 1);
            if($linkDirection == 2) {
                $builder = DB_SQL::select('default')
                        ->from($this->table())
                        ->where('node_id_1', '=', $this->node_id_2, 'AND')
                        ->where('node_id_2', '=', $this->node_id_1);
                $result = $builder->query();
                
                if(!$result->is_loaded()) {
                    if(count($result) <= 0) {
                        $links = DB_ORM::model('map_node_link');
                        $links->map_id = $this->map_id;
                        $links->node_id_1 = $this->node_id_2;
                        $links->node_id_2 = $this->node_id_1;
                        $links->image_id = $this->image_id;

                        $links->save();
                    }
                }
            }
        }
    }

    public function addFullLink($mapId, $values){
        return DB_ORM::insert('map_node_link')
                       ->column('map_id', $mapId)
                       ->column('text', Arr::get($values, 'text', ''))
                       ->column('node_id_1', Arr::get($values, 'node_id_1', ''))
                       ->column('node_id_2', Arr::get($values, 'node_id_2', ''))
                       ->column('image_id', Arr::get($values, 'image_id', null))
                       ->column('order', Arr::get($values, 'order', 0))
                       ->column('probability', Arr::get($values, 'probability', 0))
                       ->execute();
    }

    public function addVUELink($mapId, $nodeId1, $nodeId2) {
        $this->map_id = $mapId;
        $this->node_id_1 = $nodeId1;
        $this->node_id_2 = $nodeId2;
        
        $this->save();
    }
    
    public function updateLink($linkId, $values) {
        $this->id = $linkId;
        $this->load();
        if($this) { 
            $this->image_id = Arr::get($values, 'linkImage', '');
            $this->text = Arr::get($values, 'linkLabel', '');
            
            $this->save();
            
            $linkDirection = Arr::get($values, 'linkDirection', 1);
            if($linkDirection == 2) {
                $builder = DB_SQL::select('default')
                        ->from($this->table())
                        ->where('node_id_1', '=', $this->node_id_2, 'AND')
                        ->where('node_id_2', '=', $this->node_id_1);
                $result = $builder->query();
                
                if(!$result->is_loaded()) {
                    if(count($result) <= 0) {
                        $links = DB_ORM::model('map_node_link');
                        $links->map_id = $this->map_id;
                        $links->node_id_1 = $this->node_id_2;
                        $links->node_id_2 = $this->node_id_1;
                        $links->image_id = $this->image_id;

                        $links->save();
                    } 
                }
            }
        }
    }
    
    public function updateOrders($mapId, $nodeId, $values) {
        $builder = DB_SQL::select('default')
                ->from($this->table())
                ->where('map_id', '=', $mapId, 'AND')
                ->where('node_id_1', '=', $nodeId);
        $result = $builder->query();
        
        if($result->is_loaded()) {
            foreach($result as $record) {
                $link = DB_ORM::model('map_node_link', array((int)$record['id']));
                $link->order = Arr::get($values, 'order_'.$link->id, 1);
                $link->save();
            }
        }
    }
    
    public function updateProbability($mapId, $nodeId, $values) {
        $builder = DB_SQL::select('default')
                ->from($this->table())
                ->where('map_id', '=', $mapId, 'AND')
                ->where('node_id_1', '=', $nodeId);
        $result = $builder->query();
        
        if($result->is_loaded()) {
            foreach($result as $record) {
                $link = DB_ORM::model('map_node_link', array((int)$record['id']));
                $link->probability = Arr::get($values, 'weight_'.$link->id, 1);
                $link->save();
            }
        }
    }
    
    public function getLinksByMap($mapId) {
        $builder = DB_SQL::select('default')
                ->from($this->table())
                ->where('map_id', '=', $mapId);
        $result = $builder->query();
        
        if($result->is_loaded()) {
            $links = array();
            foreach($result as $record) {
                $links[] = DB_ORM::model('map_node_link', array((int)$record['id']));
            }
            
            return $links;
        }
        
        return NULL;
    }
    
    public function getLinkByNodeIDs($nodeA, $nodeB) {
        $builder = DB_SQL::select('default')
                ->from($this->table())
                ->where('node_id_1', '=', $nodeA, 'AND')
                ->where('node_id_2', '=', $nodeB);
        
        $result = $builder->query();
        
        if($result->is_loaded()) {
            return DB_ORM::model('map_node_link', array((int)$result[0]['id']));
        }
        
        return NULL;
    }
    
    public function deleteLinks($nodeId) {
        $builder = DB_SQL::delete('default')->from($this->table())->where('node_id_1', '=', $nodeId);
        $builder->execute();
        
        $builder = DB_SQL::delete('default')->from($this->table())->where('node_id_2', '=', $nodeId);
        $builder->execute();
    }
    
    public function deleteLinkByNodeIds($nodeA, $nodeB) {
        $builder = DB_SQL::delete('default')->from($this->table())->where('node_id_1', '=', $nodeA, 'AND')->where('node_id_2', '=', $nodeB);
        $builder->execute();
        
        $builder = DB_SQL::delete('default')->from($this->table())->where('node_id_2', '=', $nodeA, 'AND')->where('node_id_1', '=', $nodeB);
        $builder->execute();
    }
    
    public function duplicateLinks($fromMapId, $toMapId, $nodeMap, $elementMap) {
        $links = $this->getLinksByMap($fromMapId);
        
        if($links == null || $toMapId == null || $toMapId <= 0) return;
        
        foreach($links as $link) {
            $builder = DB_ORM::insert('map_node_link')
                    ->column('map_id', $toMapId)
                    ->column('text', $link->text)
                    ->column('order', $link->order)
                    ->column('probability', $link->probability);
                    
            if(isset($nodeMap[$link->node_id_1]))
                $builder = $builder->column ('node_id_1', $nodeMap[$link->node_id_1]);
            
            if(isset($nodeMap[$link->node_id_2]))
                $builder = $builder->column ('node_id_2', $nodeMap[$link->node_id_2]);
   
            if(isset($elementMap[$link->image_id]))
                $builder = $builder->column ('image_id', $elementMap[$link->image_id]);
            
            $builder->execute();
        }
    }

    public function exportMVP($mapId) {
        $builder = DB_SQL::select('default')
            ->from($this->table())
            ->where('map_id', '=', $mapId);
        $result = $builder->query();

        if($result->is_loaded()) {
            $links = array();
            foreach($result as $record) {
                $links[] = $record;
            }

            return $links;
        }

        return NULL;
    }
}

?>