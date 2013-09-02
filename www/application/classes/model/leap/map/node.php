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
 * Model for map_nodes table in database 
 */
class Model_Leap_Map_Node extends DB_ORM_Model {

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
            
            'title' => new DB_ORM_Field_String($this, array(
                'max_length' => 200,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            
            'text' => new DB_ORM_Field_String($this, array(
                'max_length' => 4000,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            
            'content' => new DB_ORM_Field_String($this, array(
                'max_length' => 4000,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            
            'type_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            
            'probability' => new DB_ORM_Field_Boolean($this, array(
                'default' => FALSE,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            
            'conditional' => new DB_ORM_Field_String($this, array(
                'max_length' => 500,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            
            'conditional_message' => new DB_ORM_Field_String($this, array(
                'max_length' => 1000,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            
            'info' => new DB_ORM_Field_String($this, array(
                'max_length' => 1000,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            
            'link_style_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            
            'link_type_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            
            'priority_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            
            'kfp' => new DB_ORM_Field_Boolean($this, array(
                'default' => FALSE,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            
            'undo' => new DB_ORM_Field_Boolean($this, array(
                'default' => FALSE,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            
            'end' => new DB_ORM_Field_Boolean($this, array(
                'default' => FALSE,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            
            'x' => new DB_ORM_Field_Double($this, array(
                'nullable' => TRUE,
                'savable' => TRUE,
            )),
            
            'y' => new DB_ORM_Field_Double($this, array(
                'nullable' => TRUE,
                'savable' => TRUE,
            )),
            
            'rgb' => new DB_ORM_Field_String($this, array(
                'max_length' => 8,
                'nullable' => TRUE,
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
                'child_key' => array('type_id'),
                'parent_key' => array('id'),
                'parent_model' => 'map_node_type',
            )),
            
            'link_style' => new DB_ORM_Relation_BelongsTo($this, array(
                'child_key' => array('link_style_id'),
                'parent_key' => array('id'),
                'parent_model' => 'map_node_link_style',
            )),
            
            'link_type' => new DB_ORM_Relation_BelongsTo($this, array(
                'child_key' => array('link_type_id'),
                'parent_key' => array('id'),
                'parent_model' => 'map_node_link_type',
            )),
            
            'priority' => new DB_ORM_Relation_BelongsTo($this, array(
                'child_key' => array('priority_id'),
                'parent_key' => array('id'),
                'parent_model' => 'map_node_priority',
            )),
            
            'sections' => new DB_ORM_Relation_HasMany($this, array(
                'child_key' => array('node_id'),
                'child_model' => 'map_node_section_node',
                'parent_key' => array('id'),
            )),
            
            'links' => new DB_ORM_Relation_HasMany($this, array(
                'child_key' => array('node_id_1'),
                'child_model' => 'map_node_link',
                'parent_key' => array('id'),
            )),
            
            'counters' => new DB_ORM_Relation_HasMany($this, array(
                'child_key' => array('node_id'),
                'child_model' => 'map_node_counter',
                'parent_key' => array('id'),
            )),
        );
        self::initialize_metadata($this);
    }

    private static function initialize_metadata($object)
    {
        $metadata = Model_Leap_Metadata::getMetadataRelations("map_node", $object);
        $object->relations = array_merge($object->relations, $metadata);
    }

    public static function data_source() {
        return 'default';
    }

    public static function table() {
        return 'map_nodes';
    }

    public static function primary_key() {
        return array('id');
    }
    
    public function getNodesByMap($mapId, $orderBy = null, $logicSort = null, $lengthSort = false) {
        $builder = DB_SQL::select('default')->from($this->table(), 't')->column('t.id', 'id')->where('map_id', '=', $mapId);
        switch($orderBy) {
            case 1:
                $builder = $builder->order_by('id', 'ASC');
                break;
            case 2:
                $builder = $builder->order_by('id', 'DESC');
                break;
            case 3:
                $builder = $builder->order_by('title', 'ASC');
                break;
            case 4:
                $builder = $builder->order_by('title', 'DESC');
                break;
            case 5:
                $builder = $builder->order_by('x', 'ASC');
                break;
            case 6:
                $builder = $builder->order_by('x', 'DESC');
                break;
            case 7:
                $builder = $builder->order_by('y', 'ASC');
                break;
            case 8:
                $builder = $builder->order_by('y', 'DESC');
                break;
            case 9:
                $builder = $builder->join('left', 'map_node_section_nodes', 'r')->on('r.node_id', '=', 't.id')->order_by('r.id', 'ASC');
                break;
            case 10:
                $builder = $builder->join('left', 'map_node_section_nodes', 'r')->on('r.node_id', '=', 't.id')->order_by('r.id', 'DESC');
                break;
            default:
                $builder = $builder->order_by('id', 'ASC');
        }
        
        $result = $builder->query();
        
        if($result->is_loaded()) {
            $nodes = array();
            $rootNodes = array();
            $endNodes = array();
            foreach($result as $record) {
                $tmp = DB_ORM::model('map_node', array((int)$record['id']));
                if($logicSort != null && $logicSort == 1) {
                    if($tmp->type_id == 1) {
                        $rootNodes[] = $tmp;
                    } else if($tmp->end) {
                        $endNodes[] = $tmp;
                    } else {
                        $nodes[] = $tmp;
                    }
                } else {
                    $nodes[] = $tmp;
                }
            }
            
            $nodes = array_merge($rootNodes, $nodes);
            $nodes = array_merge($nodes, $endNodes);

            if ($lengthSort){
                usort($nodes, function($a, $b) {
                    return strlen($b->title) - strlen($a->title);
                });
            }

            return $nodes;
        }
        
        return NULL;
    }
    
    public function getAllNode($mapId = null) {
        $builder = DB_SQL::select('default')->from($this->table());
        if($mapId != null)
            $builder = $builder->where ('map_id', '=', $mapId);
        
        $result = $builder->query();
        
        if($result->is_loaded()) {
            $nodes = array();
            foreach($result as $record) {
                $nodes [] = DB_ORM::model('map_node', array((int)$record['id']));
            }
            
            return $nodes;
        }
        
        return NULL;
    }
    
    public function createNode($values) {
        $mapId = Arr::get($values, 'map_id', NULL);
        if($mapId != NULL) {
            return DB_ORM::model('map_node', array(DB_ORM::insert('map_node')
                    ->column('map_id', $mapId)
                    ->column('title', Arr::get($values, 'mnodetitle', ''))
                    ->column('text', Arr::get($values, 'mnodetext', ''))
                    ->column('info', Arr::get($values, 'mnodeinfo', ''))
                    ->column('probability', Arr::get($values, 'mnodeprobability', FALSE))
                    ->column('link_style_id', Arr::get($values, 'linkstyle', 1))
                    ->column('link_type_id', 2)
                    ->column('priority_id', Arr::get($values, 'priority', 1))
                    ->column('undo', Arr::get($values, 'mnodeUndo', FALSE))
                    ->column('end', Arr::get($values, 'ender', FALSE))
                    ->column('type_id', Arr::get($values, 'type_id', FALSE) ? Arr::get($values, 'type_id', FALSE) : 2)
                    ->execute()));
        }
        
        return NULL;
    }

    public function createFullNode($mapId, $values){
        if($mapId != NULL) {
            $this->map_id = $mapId;
            $this->title = Arr::get($values, 'title', '');
            $this->text = Arr::get($values, 'text', '');
            $this->info = Arr::get($values, 'info', '');
            $this->probability = Arr::get($values, 'probability', FALSE);
            $this->link_style_id = Arr::get($values, 'link_style_id', 1);
            $this->link_type_id = Arr::get($values, 'link_type_id', 1);
            $this->priority_id = Arr::get($values, 'priority_id', 1);
            $this->undo = Arr::get($values, 'undo', FALSE);
            $this->end = Arr::get($values, 'end', FALSE);
            $this->type_id = Arr::get($values, 'type_id', FALSE);
            $this->x = Arr::get($values, 'x', FALSE);
            $this->y = Arr::get($values, 'y', FALSE);
            $this->rgb = Arr::get($values, 'rgb', FALSE);

            $this->save();

            return $this->getLastAddedNode($mapId);
        }

        return NULL;
    }

    public function createNodeFromJSON($mapId, $values) {
        if($mapId == null) return null;

        $builder = DB_ORM::insert('map_node')
                ->column('map_id', $mapId)
                ->column('title', urldecode(str_replace('+', '&#43;', base64_decode(Arr::get($values, 'title', '')))))
                ->column('text', urldecode(str_replace('+', '&#43;', base64_decode(Arr::get($values, 'content', '')))))
                ->column('info', urldecode(str_replace('+', '&#43;', base64_decode(Arr::get($values, 'support', '')))))
                ->column('probability', (Arr::get($values, 'isExit', 'false') == 'true'))
                ->column('type_id', (Arr::get($values, 'isRoot', 'false') == 'true') ? 1 : 2)
                ->column('link_style_id', Arr::get($values, 'linkStyle', 1))
                ->column('priority_id', Arr::get($values, 'nodePriority', 1))
                ->column('undo', (Arr::get($values, 'undo', 'false') == 'true'))
                ->column('end', (Arr::get($values, 'isEnd', 'false') == 'true'))
                ->column('x', Arr::get($values, 'x', 0))
                ->column('y', Arr::get($values, 'y', 0))
                ->column('rgb', Arr::get($values, 'color', '#FFFFFF'));

        return $builder->execute();
    }

    public function updateNodeFromJSON($nodeId, $values) {
        $this->id = $nodeId;
        $this->load();
        if($this) {
            $this->title = urldecode(str_replace('+', '&#43;', base64_decode(Arr::get($values, 'title', ''))));
            $this->text = urldecode(str_replace('+', '&#43;', base64_decode(Arr::get($values, 'content', ''))));
            $this->info = urldecode(str_replace('+', '&#43;', base64_decode(Arr::get($values, 'support', ''))));
            $this->probability = Arr::get($values, 'isExit', 'false') == 'true';
            $this->type_id = (Arr::get($values, 'isRoot', 'false') == 'true') ? 1 : 2;
            $this->link_style_id = Arr::get($values, 'linkStyle', 1);
            $this->priority_id = Arr::get($values, 'nodePriority', 1);
            $this->undo = Arr::get($values, 'undo', 'false') == 'true';
            $this->end = Arr::get($values, 'isEnd', 'false') == 'true';
            $this->x = Arr::get($values, 'x', 0);
            $this->y = Arr::get($values, 'y', 0);
            $this->rgb = Arr::get($values, 'color', '#FFFFFF');

            $this->save();
        }
    }

    public function getLastAddedNode($mapId){
        $builder = DB_SQL::select('default')->from($this->table())->where('map_id', '=', $mapId)->order_by('id', 'DESC')->limit(1);
        $result = $builder->query();

        if ($result->is_loaded()) {
            return DB_ORM::model('map_node', array($result[0]['id']));
        }

        return NULL;
    }

    public function createDefaultRootNode($mapId) {
        if($mapId != NULL) {
            $this->map_id = $mapId;
            $this->title = 'Root Node';
            $this->text = '';
            $this->info = '';
            $this->probability = FALSE;
            $this->link_style_id = 1;
            $this->link_type_id = 2;
            $this->priority_id = 1;
            $this->type_id = 1;
            $this->undo = FALSE;
            $this->end = FALSE;
            
            $this->save();
        }
    }
    
    public function updateNode($nodeId, $values) {
        $this->id = $nodeId;
        $this->load();
        if($this) {
            $this->title = Arr::get($values, 'mnodetitle', $this->title);
            $this->text = Arr::get($values, 'mnodetext', $this->text);
            $this->info = Arr::get($values, 'mnodeinfo', $this->info);
            $this->probability = Arr::get($values, 'mnodeprobability', $this->probability);
            $this->link_style_id = Arr::get($values, 'linkstyle', $this->link_style_id);
            $this->link_type_id = Arr::get($values, 'linktype', $this->link_type_id);
            $this->priority_id = Arr::get($values, 'priority', $this->priority_id);
            $this->undo = Arr::get($values, 'mnodeUndo', $this->undo);
            $this->end = Arr::get($values, 'ender', $this->end);
            
            $this->save();
            
            return $this;
        }
        
        return NULL;
    }
    
    public function getRootNode() {
        $typeId = DB_ORM::model('map_node_type')->getTypeByName('root')->id;
        if($typeId != NULL) {
            $builder = DB_SQL::select('default')->from($this->table())->where('type_id', '=', $typeId);
            $result = $builder->query();

            if($result->is_loaded()) {
                return DB_ORM::model('map_node', array((int)$result[0]['id']));
            }
        }
        
        return NULL;
    }
    
    public function setRootNode($mapId, $nodeId) {
        $rootNode = $this->getRootNodeByMap($mapId);
        if($rootNode != NULL) {
            $rootNode->type_id = DB_ORM::model('map_node_type')->getTypeByName('child')->id;
            $rootNode->save();
        }
        
        $this->id = $nodeId;
        $this->load();
        $this->type_id = DB_ORM::model('map_node_type')->getTypeByName('root')->id;
        $this->save();
    }
    
    public function addCondtional($nodeId, $values, $countOfConditional) {
        $this->id = $nodeId;
        $this->load();
        
        if($this) {
            $this->conditional_message = Arr::get($values, 'abs', '');
            $operator = Arr::get($values, 'operator', 'and');
            $conditional = '(';
            for($i = 0; $i < $countOfConditional - 1; $i++) {
                $conditional .= '['.Arr::get($values, 'el_'.$i, 0).']'.$operator;
            }
            
            if($countOfConditional > 0) {
                $conditional .=  '['.Arr::get($values, 'el_'.($countOfConditional - 1), 0).']';
            }
            $conditional .= ')';
            
            $this->conditional = $conditional;
            
            $this->save();
        }
    }
    
    public function updateAllNode($values, $mapId = null) {
        $nodes = $this->getAllNode($mapId);
        foreach($nodes as $node) {
            $node->title = Arr::get($values, 'title_'.$node->id, $node->title);
            $node->text = Arr::get($values, 'text_'.$node->id, $node->text);
            $node->info = Arr::get($values, 'info_'.$node->id, $node->info);
            
            $node->save();
        }
    }
    
    public function getAllNodesNotInSection($mapId = null) {
        $tableName = DB_ORM::model('map_node_section_node');
        $builder = DB_SQL::select('default')
                ->from($tableName::table())
                ->column('node_id');

        $allNodeInSectionresult = $builder->query();
        
        $ids = array();
        if($allNodeInSectionresult->is_loaded()) {
            foreach($allNodeInSectionresult as $record) {
                $ids[] = (int)$record['node_id'];
            }
        }
        $builder = NULL;
        if(count($ids) > 0) {
            $builder = DB_SQL::select('default')->from($this->table())->where('id', 'NOT IN', $ids);
        } else {
            $builder = DB_SQL::select('default')->from($this->table());
        }
        if(isset($mapId))$builder->where("map_id","=",$mapId);
        $result = $builder->query();
        
        if($result->is_loaded()) {
            $nodes = array();
            foreach($result as $record) {
                $nodes[] = DB_ORM::model('map_node', array((int)$record['id']));
            }
            
            return $nodes;
        }
        
        return NULL;
    }

    public function setLinkStyle($linkStyleId) {
        $linkStyles = DB_ORM::model('map_node_link_style')->getAllLinkStyles();
        if($linkStyles != null && count($linkStyles) > 0) {
            foreach($linkStyles as $style) {
                if($style->id == $linkStyleId) {
                    DB_ORM::update('map_node')->set('link_style_id', $linkStyleId)->execute();
                    break;
                }
            }
        }
    }

    public function getNodesWithoutLink($nodeId) {
        $this->id = $nodeId;
        $this->load();
        
        if(count($this->links) > 0) {
            $ids = array();
            foreach($this->links as $link) {
                $ids[] = $link->node_2->id;
            }
            
            $builder = DB_SQL::select('default')
                    ->from($this->table())
					->where('map_id', '=', $this->map_id, 'AND')
                    ->where('id', 'NOT IN', $ids);
            $result = $builder->query();
            
            if($result->is_loaded()) {
                $nodes = array();
                foreach($result as $record) {
                    $nodes[] = DB_ORM::model('map_node', array((int)$record['id']));
                }
                
                return $nodes;
            }
            
            return NULL;
        } 
            
        return $this->getNodesByMap($this->map_id);
    }
    
    public function getCounter($counterId) {
        if(count($this->counters) > 0) {
            foreach($this->counters as $counter) {
                if($counter->counter_id == $counterId) {
                    return $counter;
                }
            }
            
            return NULL;
        }
        
        return NULL;
    }
    
    public function getNodeById($nodeId) {
        $builder = DB_SQL::select('default')->from($this->table())->where('id', '=', $nodeId);
        $result = $builder->query();
        
        if($result->is_loaded()) {
            return DB_ORM::model('map_node', array((int)$result[0]['id']));
        }
        
        return NULL;
    }
    
    public function getRootNodeByMap($mapId) {
        $typeId = DB_ORM::model('map_node_type')->getTypeByName('root')->id;
        if($typeId != NULL) {
            $builder = DB_SQL::select('default')
                    ->from($this->table())
                    ->where('type_id', '=', $typeId, 'AND')
                    ->where('map_id', '=', $mapId);
            $result = $builder->query();

            if($result->is_loaded()) {
                return DB_ORM::model('map_node', array((int)$result[0]['id']));
            }
        }
        
        return NULL;
    }
    
    public function deleteNode($nodeId) {
        $this->id = $nodeId;
        $this->load();
        
        if($this->is_loaded()) {
            if(count($this->links) > 0) {
                foreach($this->links as $link) {
                    $link->delete();
                }
            }
        }
        
        $this->delete();
    }
    
    public function createVUENode($mapId, $title, $text, $x, $y, $rgb) {
        $this->map_id = $mapId;
        $this->title = $title;
        $this->text = $text;
        $this->type_id = 2;
        $this->x = $x;
        $this->y = $y;
        $this->rgb = $rgb;
        $this->info = '';
        $this->probability = FALSE;
        $this->link_style_id = 1;
        $this->priority_id = 1;
        $this->undo = FALSE;
        $this->end = FALSE;
        
        $this->save();
    }
    
    public function duplicateNodes($fromMapId, $toMapId) {
        $nodes = $this->getNodesByMap($fromMapId);
        
        if($nodes == null) return array();
        
        $nodeMap = array();
        foreach($nodes as $node) {
            $values = array('title' => $node->title,
                            'text' => $node->title,
                            'info' => $node->info,
                            'probability' => $node->probability,
                            'link_style_id' => $node->link_style_id,
                            'link_type_id' => $node->link_type_id,
                            'priority_id' => $node->priority_id,
                            'undo' => $node->undo,
                            'end' => $node->end,
                            'type_id' => $node->type_id,
                            'x' => $node->x,
                            'y' => $node->y,
                            'rgb' => $node->rgb);
            
            $newNode = $this->createFullNode($toMapId, $values);
            
            $nodeMap[$node->id] = $newNode->id;
        }
        
        return $nodeMap;
    }
    
    public function replaceDuplcateNodeContenxt($nodeMap, $elemMap, $vpdMap, $avatarMap, $chatMap, $questionMap, $damMap) {
        foreach($nodeMap as $k => $v) {
            $this->id = $v;
            $this->load();
            
            $this->text = $this->parseText($this->text, $elemMap, $vpdMap, $avatarMap, $chatMap, $questionMap, $damMap);
            $this->info = $this->parseText($this->info, $elemMap, $vpdMap, $avatarMap, $chatMap, $questionMap, $damMap);
            
            $this->save();
        }
    }
    
    private function parseText($text, $elemMap, $vpdMap, $avatarMap, $chatMap, $questionMap, $damMap) {
        $result = $text;

        $codes = array('MR', 'FL', 'CHAT', 'DAM', 'AV', 'VPD', 'QU', 'INFO');

        foreach ($codes as $code) {
            $regExp = '/[\[' . $code . ':\d\]]+/';
            if (preg_match_all($regExp, $text, $matches)) {
                foreach ($matches as $match) {
                    foreach ($match as $value) {
                        if (stristr($value, '[[' . $code . ':')) {
                            $m = explode(':', $value);
                            $id = substr($m[1], 0, strlen($m[1]) - 2);
                            if (is_numeric($id)) {
                                $replaceString = '';
                                switch ($code) {
                                    case 'MR':
                                        if(isset($elemMap[(int)$id]))
                                            $replaceString = '[[' . $code . ':' . $elemMap[(int)$id] . ']]';
                                        break;
                                    case 'AV':
                                        if(isset($avatarMap[(int)$id]))
                                            $replaceString = '[[' . $code . ':' . $avatarMap[(int)$id] . ']]';
                                        break;
                                    case 'CHAT':
                                        if(isset($chatMap[(int)$id]))
                                            $replaceString = '[[' . $code . ':' . $chatMap[(int)$id] . ']]';
                                        break;
                                    case 'QU':
                                        if(isset($questionMap[(int)$id]))
                                            $replaceString = '[[' . $code . ':' . $questionMap[(int)$id] . ']]';
                                        break;
                                    case 'VPD':
                                        if(isset($vpdMap[(int)$id]))
                                            $replaceString = '[[' . $code . ':' . $vpdMap[(int)$id] . ']]';
                                        break;
                                    case 'DAM':
                                        if(isset($damMap[(int)$id]))
                                            $replaceString = '[[' . $code . ':' . $damMap[(int)$id] . ']]';
                                        break;
                                    case 'INFO':
                                        break;
                                }

                                $result = str_replace('[[' . $code . ':' . $id . ']]', $replaceString, $result);
                            }
                        }
                    }
                }
            }
        }

        return $result;
    }

    public function exportMVP($mapId) {
        $builder = DB_SQL::select('default')->from($this->table())->where('map_id', '=', $mapId);
        $result = $builder->query();

        if($result->is_loaded()) {
            $nodes = array();
            foreach($result as $record) {
                $nodes[] = $record;
            }

            return $nodes;
        }

        return NULL;
    }

    public function getEndNodesForMap($mapId) {
        $records = DB_SQL::select('default')
                           ->from($this->table())
                           ->where('map_id', '=', $mapId, 'AND')
                           ->where('end', '=', 1)
                           ->column('id')
                           ->query();

        $result = null;
        if($records->is_loaded()) {
            $result = array();
            foreach($records as $record) {
                $result[] = DB_ORM::model('map_node', array((int)$record['id']));
            }
        }

        return $result;
    }

    public function getNodeName($nodeId) {
        $this->id = $nodeId;
        $this->load();

        $name = null;

        if ($this->is_loaded()){
            $name = $this->title;
        }

        return $name;
    }
}

?>