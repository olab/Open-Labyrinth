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
 * @property int $id
 * @property int $type_id
 * @property int $map_id
 * @property int $priority_id
 * @property int|bool $end
 * @property int|bool $undo
 * @property string $title
 * @property string $text
 * @property float $x
 * @property float $y
 * @property Model_Leap_Map_Node_Link[]|DB_ResultSet $links
 */
class Model_Leap_Map_Node extends Model_Leap_Base
{

    public function __construct()
    {
        parent::__construct();

        $this->fields = array(
            'id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => false,
                'unsigned' => true,
            )),
            'map_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => false,
            )),
            'title' => new DB_ORM_Field_String($this, array(
                'max_length' => 200,
                'nullable' => false,
                'savable' => true,
            )),
            'text' => new DB_ORM_Field_String($this, array(
                'max_length' => 4000,
                'nullable' => false,
                'savable' => true,
            )),
            'type_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => false,
            )),
            'probability' => new DB_ORM_Field_Boolean($this, array(
                'default' => false,
                'nullable' => false,
                'savable' => true,
            )),
            'conditional' => new DB_ORM_Field_String($this, array(
                'max_length' => 500,
                'nullable' => false,
                'savable' => true,
            )),
            'conditional_message' => new DB_ORM_Field_String($this, array(
                'max_length' => 1000,
                'nullable' => false,
                'savable' => true,
            )),
            'info' => new DB_ORM_Field_Text($this, array(
                'nullable' => false,
                'savable' => true,
            )),
            'link_style_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => false,
            )),
            'link_type_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => false,
            )),
            'priority_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => false,
            )),
            'kfp' => new DB_ORM_Field_Boolean($this, array(
                'default' => false,
                'nullable' => false,
                'savable' => true,
            )),
            'undo' => new DB_ORM_Field_Boolean($this, array(
                'default' => false,
                'nullable' => false,
                'savable' => true,
            )),
            'end' => new DB_ORM_Field_Boolean($this, array(
                'default' => false,
                'nullable' => false,
                'savable' => true,
            )),
            'x' => new DB_ORM_Field_Double($this, array(
                'nullable' => true,
                'savable' => true,
            )),
            'y' => new DB_ORM_Field_Double($this, array(
                'nullable' => true,
                'savable' => true,
            )),
            'rgb' => new DB_ORM_Field_String($this, array(
                'max_length' => 8,
                'nullable' => true,
                'savable' => true,
            )),
            'show_info' => new DB_ORM_Field_Boolean($this, array(
                'default' => false,
                'nullable' => false,
                'savable' => true,
            )),
            'is_private' => new DB_ORM_Field_Boolean($this, array(
                'savable' => true,
                'nullable' => false,
                'default' => false
            )),
            'annotation' => new DB_ORM_Field_Text($this, array(
                'nullable' => true,
                'savable' => true,
            ))
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
            'notes' => new DB_ORM_Relation_HasMany($this, array(
                'child_key' => array('node_id'),
                'child_model' => 'dtopic',
                'parent_key' => array('id'),
                'options' => array(
                    array('where', array('status', '=', '1'))
                )
            ))
        );
        self::initialize_metadata($this);
    }

    private static function initialize_metadata($object)
    {
        $metadata = Model_Leap_Metadata::getMetadataRelations("map_node", $object);
        $object->relations = array_merge($object->relations, $metadata);
    }

    public static function data_source()
    {
        return 'default';
    }

    public static function table()
    {
        return 'map_nodes';
    }

    public static function primary_key()
    {
        return array('id');
    }

    public static function getAdminBaseUrl()
    {
        return URL::base(true) . 'nodeManager/editNode/';
    }

    public function toxAPIExtensionObject()
    {
        $result = $this->as_array();
        $result['id'] = static::getAdminBaseUrl() . $this->id;
        $result['internal_id'] = $this->id;

        return $result;
    }

    public function toxAPIObject()
    {
        $node = $this;
        $url = static::getAdminBaseUrl() . $node->id;
        $object = array(
            'id' => $url,
            'definition' => array(
                'name' => array(
                    'en-US' => 'node "' . $node->title . '" (#' . $node->id . ')'
                ),
                'description' => array(
                    'en-US' => 'Node content: ' . Model_Leap_Statement::sanitizeString($node->text)
                ),
                'type' => 'http://activitystrea.ms/schema/1.0/node',
                'moreInfo' => $url,
            ),
        );

        $object['definition']['extensions'][Model_Leap_Statement::getExtensionNodeKey()] = $node->toxAPIExtensionObject();

        return $object;
    }

    /**
     * @param int $session_id
     * @return bool
     */
    public function isVisitedDuringSession($session_id)
    {
        $counter = DB_SQL::select()
            ->from(Model_Leap_User_SessionTrace::table())
            ->where('session_id', '=', $session_id)
            ->where('node_id', '=', $this->id)
            ->column(DB_SQL::expr("COUNT(*)"), 'counter')
            ->query();

        return ($counter[0]['counter'] > 0);
    }

    /**
     * @return bool
     */
    public function isMustVisit()
    {
        return ($this->priority_id == 3);
    }

    /**
     * @return bool
     */
    public function isMustAvoid()
    {
        return ($this->priority_id == 2);
    }

    public function getNodeTitle()
    {
        $node_title = explode('|', $this->title);

        return trim($node_title[0]);
    }

    public function getNodesByMap($mapId, $orderBy = null, $logicSort = null, $lengthSort = false)
    {
        $builder = DB_SQL::select('default')->from($this->table(), 't')->column('t.id', 'id')->where('map_id', '=',
            $mapId);
        switch ($orderBy) {
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
                $builder = $builder->join('left', 'map_node_section_nodes', 'r')->on('r.node_id', '=',
                    't.id')->order_by('r.id', 'ASC');
                break;
            case 10:
                $builder = $builder->join('left', 'map_node_section_nodes', 'r')->on('r.node_id', '=',
                    't.id')->order_by('r.id', 'DESC');
                break;
            default:
                $builder = $builder->order_by('id', 'ASC');
        }
        $result = $builder->query();

        $nodes = array();
        if ($result->is_loaded()) {
            $rootNodes = array();
            $endNodes = array();

            foreach ($result as $record) {
                $tmp = DB_ORM::model('map_node', array((int)$record['id']));
                if ($logicSort != null AND $logicSort == 1) {
                    if ($tmp->type_id == 1) {
                        $rootNodes[] = $tmp;
                    } elseif ($tmp->end) {
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

            if ($lengthSort) {
                usort($nodes, function ($a, $b) {
                    return strlen($b->title) - strlen($a->title);
                });
            }
        }

        return $nodes;
    }

    public function getAllNode($mapId)
    {
        return DB_ORM::select('Map_Node')->where('map_id', '=', $mapId)->query()->as_array();
    }

    public function createNode($values)
    {
        $mapId = Arr::get($values, 'map_id', null);
        if ($mapId != null) {
            return DB_ORM::model('map_node', array(
                DB_ORM::insert('map_node')
                    ->column('map_id', $mapId)
                    ->column('title', Arr::get($values, 'mnodetitle', ''))
                    ->column('text', Arr::get($values, 'mnodetext', ''))
                    ->column('type_id', Arr::get($values, 'type_id', false) ? Arr::get($values, 'type_id', false) : 2)
                    ->column('is_private', Arr::get($values, 'is_private', false) ? 1 : 0)
                    ->column('probability', Arr::get($values, 'mnodeprobability', false))
                    ->column('info', Arr::get($values, 'mnodeinfo', ''))
                    ->column('link_style_id', Arr::get($values, 'linkstyle', 1))
                    ->column('link_type_id', 2)
                    ->column('priority_id', Arr::get($values, 'priority', 1))
                    ->column('undo', Arr::get($values, 'mnodeUndo', false))
                    ->column('end', (int)Arr::get($values, 'ender', false))
                    ->column('show_info', Arr::get($values, 'show_info', false) ? 1 : 0)
                    ->column('annotation', Arr::get($values, 'annotation', null))
                    ->execute()
            ));
        }

        return null;
    }

    public function createFullNode($mapId, $values)
    {
        // if mapId doesn't exist end function
        if (!$mapId) {
            return null;
        }

        $record = new $this;
        $record->map_id = $mapId;
        $record->title = Arr::get($values, 'title', '');
        $record->text = Arr::get($values, 'text', '');
        $record->type_id = Arr::get($values, 'type_id', false);
        $record->probability = Arr::get($values, 'probability', false);
        $record->conditional = Arr::get($values, 'conditional', '');
        $record->conditional_message = Arr::get($values, 'conditional_message', '');
        $record->info = Arr::get($values, 'info', '');
        $record->is_private = Arr::get($values, 'is_private', false) ? 1 : 0;;
        $record->link_style_id = Arr::get($values, 'link_style_id', 1);
        $record->link_type_id = Arr::get($values, 'link_type_id', 1);
        $record->priority_id = Arr::get($values, 'priority_id', 1);
        $record->kfp = Arr::get($values, 'kfp', 0);
        $record->undo = Arr::get($values, 'undo', false);
        $record->end = Arr::get($values, 'end', false);
        $record->x = Arr::get($values, 'x', false);
        $record->y = Arr::get($values, 'y', false);
        $record->rgb = Arr::get($values, 'rgb', false);
        $record->show_info = Arr::get($values, 'show_info', false) ? 1 : 0;
        $record->annotation = Arr::get($values, 'annotation', '');
        $record->save();

        return $record->getLastAddedNode($mapId);
    }

    public function createNodeFromJSON($mapId, $values)
    {
        if ($mapId == null) {
            return null;
        }

        $linkStyle = Arr::get($values, 'linkStyle', 1);
        if ($linkStyle == 0) {
            $linkStyle = 1;
        }

        return DB_ORM::insert('map_node')
            ->column('map_id', $mapId)
            ->column('title', urldecode(str_replace('+', '&#43;', base64_decode(Arr::get($values, 'title', '')))))
            ->column('text', urldecode(str_replace('+', '&#43;', base64_decode(Arr::get($values, 'content', '')))))
            ->column('info', urldecode(str_replace('+', '&#43;', base64_decode(Arr::get($values, 'support', '')))))
            ->column('is_private', (int)Arr::get($values, 'is_private', 0) ? 1 : 0)
            ->column('probability', (Arr::get($values, 'isExit', 'false') == 'true'))
            ->column('type_id', (Arr::get($values, 'isRoot', 'false') == 'true') ? 1 : 2)
            ->column('link_style_id', $linkStyle)
            ->column('priority_id', Arr::get($values, 'nodePriority', 1))
            ->column('undo', (Arr::get($values, 'undo', 'false') == 'true'))
            ->column('end', (Arr::get($values, 'isEnd', 'false') == 'true'))
            ->column('x', Arr::get($values, 'x', 0))
            ->column('y', Arr::get($values, 'y', 0))
            ->column('rgb', Arr::get($values, 'color', '#FFFFFF'))
            ->column('annotation',
                urldecode(str_replace('+', '&#43;', base64_decode(Arr::get($values, 'annotation', null)))))
            ->column('show_info', (int)Arr::get($values, 'showInfo', 0))
            ->execute();
    }

    public function updateNodeFromJSON($mapId, $nodeId, $values)
    {
        $this->id = $nodeId;
        $this->load();

        $linkStyle = Arr::get($values, 'linkStyle', 1);
        if ($linkStyle == 0) {
            $linkStyle = 1;
        }

        if ($this) {
            $this->title = urldecode(str_replace('+', '&#43;', base64_decode(Arr::get($values, 'title', ''))));
            $text = urldecode(str_replace('+', '&#43;', base64_decode(Arr::get($values, 'content', ''))));
            $info = urldecode(str_replace('+', '&#43;', base64_decode(Arr::get($values, 'support', ''))));
            $crossReferences = new CrossReferences();
            $nodeText = $crossReferences->checkReference($mapId, $nodeId, $text, $info);
            $this->text = isset($nodeText['text']) ? $nodeText['text'] : $text;
            $this->info = isset($nodeText['info']) ? $nodeText['info'] : $this->info = $info;
            $reference = DB_ORM::model('map_node_reference')->getNotParent($mapId, $nodeId, 'INFO');
            $private = (int)Arr::get($values, 'isPrivate', 0);
            $this->is_private = ($reference != null && $private) ? false : $private;
            $this->probability = Arr::get($values, 'isExit', 'false') == 'true';
            $this->type_id = (Arr::get($values, 'isRoot', 'false') == 'true') ? 1 : 2;
            $this->link_style_id = $linkStyle;
            $this->priority_id = Arr::get($values, 'nodePriority', 1);
            $this->undo = Arr::get($values, 'undo', 'false') == 'true';
            $this->end = Arr::get($values, 'isEnd', 'false') == 'true';
            $this->x = Arr::get($values, 'x', 0);
            $this->y = Arr::get($values, 'y', 0);
            $this->rgb = Arr::get($values, 'color', '#FFFFFF');
            $this->show_info = (int)Arr::get($values, 'showInfo', 0);
            $this->annotation = urldecode(str_replace('+', '&#43;',
                base64_decode(Arr::get($values, 'annotation', null))));

            $this->save();
        }
    }

    public function updateNodeStyle($nodeId, $values)
    {
        $this->id = $nodeId;
        $this->load();

        if ($this->is_loaded()) {
            $this->x = Arr::get($values, 'x', 0);
            $this->y = Arr::get($values, 'y', 0);
            $this->rgb = Arr::get($values, 'rgb', '#FFFFFF');

            $this->save();
        }
    }

    public function updateNodeText($nodeId, $text)
    {
        $this->id = $nodeId;
        $this->load();

        if ($this->is_loaded()) {
            $this->text = $text;

            $this->save();
        }
    }

    public function updateNodeInfo($nodeId, $text)
    {
        $this->id = $nodeId;
        $this->load();

        if ($this->is_loaded()) {
            $this->info = $text;
            $this->save();
        }
    }

    public function getLastAddedNode($mapId)
    {
        $result = DB_SQL::select('default')
            ->from($this->table())
            ->where('map_id', '=', $mapId)
            ->order_by('id', 'DESC')
            ->limit(1)
            ->query();

        return $result->is_loaded() ? DB_ORM::model('map_node', array($result[0]['id'])) : null;
    }

    public function createDefaultRootNode($mapId)
    {
        if ($mapId != null) {
            $this->map_id = $mapId;
            $this->title = 'Root Node';
            $this->text = '';
            $this->info = '';
            $this->is_private = false;
            $this->probability = false;
            $this->link_style_id = 5;
            $this->link_type_id = 2;
            $this->priority_id = 1;
            $this->type_id = 1;
            $this->undo = false;
            $this->end = false;
            $this->show_info = false;

            $this->save();
        }
    }

    public function updateNode($nodeId, $values)
    {
        $this->id = $nodeId;
        $this->load();
        if ($this) {
            $this->title = Arr::get($values, 'mnodetitle', $this->title);
            $this->text = Arr::get($values, 'mnodetext', $this->text);
            $this->info = Arr::get($values, 'mnodeinfo', $this->info);
            $this->is_private = Arr::get($values, 'is_private', false) ? 1 : 0;
            $this->probability = Arr::get($values, 'mnodeprobability', $this->probability);
            $this->link_style_id = Arr::get($values, 'linkstyle', $this->link_style_id);
            $this->link_type_id = Arr::get($values, 'linktype', $this->link_type_id);
            $this->priority_id = Arr::get($values, 'priority', $this->priority_id);
            $this->undo = Arr::get($values, 'mnodeUndo', $this->undo);
            $this->end = Arr::get($values, 'ender', $this->end);
            $this->show_info = Arr::get($values, 'show_info', false) ? 1 : 0;
            $this->annotation = Arr::get($values, 'annotation', $this->annotation);

            $this->save();

            return $this;
        }

        return null;
    }

    public function getRootNode()
    {
        $typeId = DB_ORM::model('map_node_type')->getTypeByName('root')->id;
        if ($typeId != null) {
            $builder = DB_SQL::select('default')->from($this->table())->where('type_id', '=', $typeId);
            $result = $builder->query();

            if ($result->is_loaded()) {
                return DB_ORM::model('map_node', array((int)$result[0]['id']));
            }
        }

        return null;
    }

    public function setRootNode($mapId, $nodeId)
    {
        $rootNode = $this->getRootNodeByMap($mapId);
        if ($rootNode != null) {
            $rootNode->type_id = DB_ORM::model('map_node_type')->getTypeByName('child')->id;
            $rootNode->save();
        }

        $this->id = $nodeId;
        $this->load();
        $this->type_id = DB_ORM::model('map_node_type')->getTypeByName('root')->id;
        $this->save();
    }

    public function addCondtional($nodeId, $values, $countOfConditional)
    {
        $this->id = $nodeId;
        $this->load();

        if ($this) {
            $this->conditional_message = Arr::get($values, 'abs', '');
            $operator = Arr::get($values, 'operator', 'and');
            $conditional = '(';
            for ($i = 0; $i < $countOfConditional - 1; $i++) {
                $conditional .= '[' . Arr::get($values, 'el_' . $i, 0) . ']' . $operator;
            }

            if ($countOfConditional > 0) {
                $conditional .= '[' . Arr::get($values, 'el_' . ($countOfConditional - 1), 0) . ']';
            }
            $conditional .= ')';

            $this->conditional = $conditional;

            $this->save();
        }
    }

    public function updateAllNode($values, $mapId = null)
    {
        $nodes = $this->getAllNode($mapId);
        foreach ($nodes as $node) {
            $node->title = Arr::get($values, 'title_' . $node->id, $node->title);
            $node->text = Arr::get($values, 'text_' . $node->id, $node->text);
            $node->info = Arr::get($values, 'info_' . $node->id, $node->info);

            $node->save();
        }
    }

    public function getAllNodesNotInSection($mapId = null)
    {
        $tableName = DB_ORM::model('map_node_section_node');
        $builder = DB_SQL::select('default')
            ->from($tableName::table())
            ->column('node_id');

        $allNodeInSectionresult = $builder->query();

        $ids = array();

        foreach ($allNodeInSectionresult as $record) {
            $ids[] = (int)$record['node_id'];
        }

        $builder = null;
        if (count($ids) > 0) {
            $builder = DB_SQL::select('default')->from($this->table())->where('id', 'NOT IN', $ids);
        } else {
            $builder = DB_SQL::select('default')->from($this->table());
        }
        if (isset($mapId)) {
            $builder->where("map_id", "=", $mapId);
        }
        $result = $builder->query();

        $nodes = array();
        foreach ($result as $record) {
            $nodes[] = DB_ORM::model('map_node', array((int)$record['id']));
        }

        return $nodes;
    }

    public function setLinkStyle($mapId, $linkStyleId)
    {
        $mainLinkStyle = $this->getMainLinkStyles($mapId);
        foreach (DB_ORM::select('map_node')->where('map_id', '=', $mapId)->where('link_style_id', '=',
            $mainLinkStyle)->query()->as_array() as $nodeObj) {
            $nodeObj->link_style_id = $linkStyleId;
            $nodeObj->save();
        }
    }

    public function getNodesWithoutLink($nodeId)
    {
        $this->id = $nodeId;
        $this->load();

        if (count($this->links) > 0) {
            $ids = array();
            foreach ($this->links as $link) {
                $ids[] = $link->node_2->id;
            }

            $builder = DB_SQL::select('default')
                ->from($this->table())
                ->where('map_id', '=', $this->map_id, 'AND')
                ->where('id', 'NOT IN', $ids);
            $result = $builder->query();

            if ($result->is_loaded()) {
                $nodes = array();
                foreach ($result as $record) {
                    $nodes[] = DB_ORM::model('map_node', array((int)$record['id']));
                }

                return $nodes;
            }

            return null;
        }

        return $this->getNodesByMap($this->map_id);
    }

    public function getCounter($counterId)
    {
        if (count($this->counters) > 0) {
            foreach ($this->counters as $counter) {
                if ($counter->counter_id == $counterId) {
                    return $counter;
                }
            }

            return null;
        }

        return null;
    }

    public function getNodeById($nodeId)
    {
        $builder = DB_SQL::select('default')->from($this->table())->where('id', '=', $nodeId);
        $result = $builder->query();

        if ($result->is_loaded()) {
            return DB_ORM::model('map_node', array((int)$result[0]['id']));
        }

        return null;
    }

    public function getRootNodeByMap($mapId)
    {
        return DB_ORM::select('map_node')->where('type_id', '=', 1)->where('map_id', '=', $mapId)->query()->fetch(0);
    }

    public function deleteNode($nodeId)
    {
        $this->id = $nodeId;
        $this->load();

        if ($this->is_loaded()) {
            if (count($this->links) > 0) {
                foreach ($this->links as $link) {
                    $link->delete();
                }
            }
        }

        $this->delete();
    }

    public function createVUENode($mapId, $title, $text, $x, $y, $rgb)
    {
        $this->map_id = $mapId;
        $this->title = $title;
        $this->text = $text;
        $this->type_id = 2;
        $this->x = $x;
        $this->y = $y;
        $this->rgb = $rgb;
        $this->info = '';
        $this->probability = false;
        $this->link_style_id = 1;
        $this->priority_id = 1;
        $this->undo = false;
        $this->end = false;

        $this->save();
    }

    public function duplicateNodes($fromMapId, $toMapId)
    {
        $mapNodes = array();

        foreach (DB_ORM::select('Map_Node')->where('map_id', '=', $fromMapId)->query()->as_array() as $node) {
            $newNode = DB_ORM::insert('Map_Node')
                ->column('map_id', $toMapId)
                ->column('title', $node->title)
                ->column('text', $node->text)
                ->column('type_id', $node->type_id)
                ->column('probability', $node->probability)
                ->column('conditional', $node->conditional)
                ->column('conditional_message', $node->conditional_message)
                ->column('info', $node->info)
                ->column('is_private', $node->is_private)
                ->column('link_style_id', $node->link_style_id)
                ->column('priority_id', $node->priority_id)
                ->column('kfp', $node->kfp)
                ->column('undo', $node->undo)
                ->column('end', $node->end)
                ->column('x', $node->x)
                ->column('y', $node->y)
                ->column('rgb', $node->rgb)
                ->column('show_info', $node->show_info)
                ->column('annotation', $node->annotation)
                ->execute();

            $mapNodes[$node->id] = $newNode;
        }

        return $mapNodes;
    }

    public function replaceDuplcateNodeContenxt(
        $nodeMap,
        $elemMap,
        $vpdMap,
        $avatarMap,
        $chatMap,
        $questionMap,
        $damMap,
        $mapId,
        $newMapId
    ) {
        foreach ($nodeMap as $v) {
            $this->id = $v;
            $this->load();
            $this->text = $this->parseText($this->text, $elemMap, $vpdMap, $avatarMap, $chatMap, $questionMap, $damMap,
                $nodeMap);
            $this->info = $this->parseText($this->info, $elemMap, $vpdMap, $avatarMap, $chatMap, $questionMap, $damMap,
                $nodeMap);
            $this->text = $this->relativeLink($this->text, $mapId, $newMapId, $nodeMap);
            $this->info = $this->relativeLink($this->info, $mapId, $newMapId, $nodeMap);
            $this->save();
        }
    }

    private function relativeLink($text, $oldMapId, $newMapId, $nodeMap)
    {
        foreach ($nodeMap as $oldNodeId => $newNodeId) {
            $searchIndex = 'renderLabyrinth/index/' . $oldMapId . '/' . $oldNodeId;
            $replaceIndex = 'renderLabyrinth/index/' . $newMapId . '/' . $newNodeId;
            $searchGo = 'renderLabyrinth/go/' . $oldMapId . '/' . $oldNodeId;
            $replaceGo = 'renderLabyrinth/go/' . $newMapId . '/' . $newNodeId;
            $searchFile = '/files/' . $oldMapId;
            $replaceFile = '/files/' . $newMapId;
            $text = str_replace($searchIndex, $replaceIndex, $text);
            $text = str_replace($searchGo, $replaceGo, $text);
            $text = str_replace($searchFile, $replaceFile, $text);
        }

        return $text;
    }

    private function parseText($text, $elemMap, $vpdMap, $avatarMap, $chatMap, $questionMap, $damMap, $nodeMap)
    {
        $result = $text;

        $codes = array('MR', 'FL', 'CHAT', 'DAM', 'AV', 'VPD', 'QU', 'INFO', 'CD', 'CR', 'NODE', 'BUTTON');

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
                                        if (isset($elemMap[(int)$id])) {
                                            $replaceString = '[[' . $code . ':' . $elemMap[(int)$id] . ']]';
                                        }
                                        break;
                                    case 'AV':
                                        if (isset($avatarMap[(int)$id])) {
                                            $replaceString = '[[' . $code . ':' . $avatarMap[(int)$id] . ']]';
                                        }
                                        break;
                                    case 'CHAT':
                                        if (isset($chatMap[(int)$id])) {
                                            $replaceString = '[[' . $code . ':' . $chatMap[(int)$id] . ']]';
                                        }
                                        break;
                                    case 'QU':
                                        if (isset($questionMap[(int)$id])) {
                                            $replaceString = '[[' . $code . ':' . $questionMap[(int)$id] . ']]';
                                        }
                                        break;
                                    case 'VPD':
                                        if (isset($vpdMap[(int)$id])) {
                                            $replaceString = '[[' . $code . ':' . $vpdMap[(int)$id] . ']]';
                                        }
                                        break;
                                    case 'DAM':
                                        if (isset($damMap[(int)$id])) {
                                            $replaceString = '[[' . $code . ':' . $damMap[(int)$id] . ']]';
                                        }
                                        break;
                                    case 'INFO':
                                        break;
                                    case 'CD':
                                        break;
                                    case 'CR':
                                        break;
                                    case 'NODE':
                                        $replaceString = '[[' . $code . ':' . $nodeMap[(int)$id] . ']]';
                                        break;
                                    case 'BUTTON':
                                        $replaceString = '[[' . $code . ':' . $nodeMap[(int)$id] . ']]';
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

    public function getEndNodesForMap($mapId)
    {
        return DB_ORM::select('Map_Node')->where('map_id', '=', $mapId)->where('end', '=', 1)->query()->as_array();
    }

    public function getNodeName($nodeId)
    {
        $this->id = $nodeId;
        $this->load();

        $name = null;

        if ($this->is_loaded()) {
            $name = $this->title;
        }

        return $name;
    }

    public function getMainLinkStyles($mapId)
    {
        // 5 link style create
        $linkStyle[1] = 0;
        $linkStyle[2] = 0;
        $linkStyle[3] = 0;
        $linkStyle[4] = 0;
        $linkStyle[5] = 0;

        foreach (DB_ORM::select('Map_Node')->where('map_id', '=', $mapId)->query()->as_array() as $nodeObj) {
            if (isset($linkStyle[$nodeObj->link_style_id])) {
                $linkStyle[$nodeObj->link_style_id] += 1;
            }
        }
        // sort array, first key element contain main link style
        arsort($linkStyle);

        return key($linkStyle);
    }
}