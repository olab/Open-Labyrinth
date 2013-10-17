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
 * Model for maps table in database
 */
class Model_Leap_Map extends DB_ORM_Model
{

    public function __construct()
    {
        parent::__construct();

        $this->fields = array(
            'id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
                'unsigned' => TRUE,
            )),
            'name' => new DB_ORM_Field_String($this, array(
                'max_length' => 200,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            'author_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            'abstract' => new DB_ORM_Field_String($this, array(
                'max_length' => 2000,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            'startScore' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            'threshold' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            'keywords' => new DB_ORM_Field_String($this, array(
                'max_length' => 500,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            'type_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            'units' => new DB_ORM_Field_String($this, array(
                'max_length' => 10,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            'security_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            'guid' => new DB_ORM_Field_String($this, array(
                'max_length' => 50,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            'timing' => new DB_ORM_Field_Boolean($this, array(
                'default' => FALSE,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            'delta_time' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            'reminder_msg' => new DB_ORM_Field_String($this, array(
                'max_length' => 255,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            'reminder_time' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            'show_bar' => new DB_ORM_Field_Boolean($this, array(
                'default' => FALSE,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            'show_score' => new DB_ORM_Field_Boolean($this, array(
                'default' => FALSE,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            'skin_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            'enabled' => new DB_ORM_Field_Boolean($this, array(
                'default' => TRUE,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            'section_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            'language_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            'feedback' => new DB_ORM_Field_String($this, array(
                'max_length' => 2000,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            'dev_notes' => new DB_ORM_Field_String($this, array(
                'max_length' => 1000,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            'link_logic' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            'node_cont' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            'clinical_acc' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            'media_cont' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            'media_copy' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            'inst_guide' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            'metadata_file_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            'source' => new DB_ORM_Field_String($this, array(
                'max_length' => 50,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            'source_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
        );

        $this->relations = array(
            'author' => new DB_ORM_Relation_BelongsTo($this, array(
                'child_key' => array('author_id'),
                'parent_key' => array('id'),
                'parent_model' => 'user',
            )),
            'type' => new DB_ORM_Relation_BelongsTo($this, array(
                'child_key' => array('type_id'),
                'parent_key' => array('id'),
                'parent_model' => 'map_type',
            )),
            'security' => new DB_ORM_Relation_BelongsTo($this, array(
                'child_key' => array('security_id'),
                'parent_key' => array('id'),
                'parent_model' => 'map_security',
            )),
            'skin' => new DB_ORM_Relation_BelongsTo($this, array(
                'child_key' => array('skin_id'),
                'parent_key' => array('id'),
                'parent_model' => 'map_skin',
            )),
            'section' => new DB_ORM_Relation_BelongsTo($this, array(
                'child_key' => array('section_id'),
                'parent_key' => array('id'),
                'parent_model' => 'map_section',
            )),
            'language' => new DB_ORM_Relation_BelongsTo($this, array(
                'child_key' => array('language_id'),
                'parent_key' => array('id'),
                'parent_model' => 'language',
            )),
            'contributors' => new DB_ORM_Relation_HasMany($this, array(
                'child_key' => array('map_id'),
                'child_model' => 'map_contributor',
                'parent_key' => array('id'),
            )),
            'authors' => new DB_ORM_Relation_HasMany($this, array(
                'child_key' => array('map_id'),
                'child_model' => 'map_user',
                'parent_key' => array('id'),
            )),
            'nodes' => new DB_ORM_Relation_HasMany($this, array(
                'child_key' => array('map_id'),
                'child_model' => 'map_node',
                'parent_key' => array('id'),
            )),
        );
        self::initialize_metadata($this);
    }

    private static function initialize_metadata($object)
    {
        $metadata = Model_Leap_Metadata::getMetadataRelations("map", $object);
        $object->relations = array_merge($object->relations, $metadata);
    }

    public static function data_source()
    {
        return 'default';
    }

    public static function primary_key()
    {
        return array('id');
    }

    public function getAllMap()
    {
        $builder = DB_SQL::select('default')->from($this->table())->order_by('name');
        $result = $builder->query();

        if ($result->is_loaded()) {
            $maps = array();
            foreach ($result as $record) {
                $maps[] = DB_ORM::model('map', array((int)$record['id']));
            }

            return $maps;
        }

        return NULL;
    }

    public function getAllEnabledAndOpenMap()
    {
        $builder = DB_SQL::select('default')
            ->from($this->table())
            ->where('enabled', '=', 1, 'AND')
            ->where('security_id', '=', 1);
        $result = $builder->query();

        if ($result->is_loaded()) {
            $maps = array();
            foreach ($result as $record) {
                $maps[] = DB_ORM::model('map', array((int)$record['id']));
            }

            return $maps;
        }

        return NULL;
    }

    public static function table()
    {
        return 'maps';
    }

    public function getMapByName($name)
    {
        $builder = DB_SQL::select('default')->from($this->table())->where('name', '=', $name);
        $result = $builder->query();

        if ($result->is_loaded()) {
            return DB_ORM::model('map', array($result[0]['id']));
        }

        return NULL;
    }

    public function getAllEnabledMap($limit = 0)
    {
        $builder = DB_SQL::select('default')
            ->from($this->table())
            ->where('enabled', '=', 1)
            ->order_by('id', 'DESC');
        if ($limit) {
            $builder->limit($limit);
        }
        $result = $builder->query();

        if ($result->is_loaded()) {
            $maps = array();
            foreach ($result as $record) {
                $maps[] = DB_ORM::model('map', array((int)$record['id']));
            }

            return $maps;
        }

        return NULL;
    }

    public function getAllEnabledOpenVisibleMap()
    {
        $builder = DB_SQL::select('default')
            ->from($this->table())
            ->where('enabled', '=', 1, 'AND')
            ->where('security_id', '=', 1);
        $result = $builder->query();

        if ($result->is_loaded()) {
            $maps = array();
            foreach ($result as $record) {
                $maps[] = DB_ORM::model('map', array((int)$record['id']));
            }

            return $maps;
        }

        return NULL;
    }

    /**
     * @param $user_id
     * @return array|null
     */
    public function getAllMapsForRegisteredUser($user_id)
    {
        $builder = DB_SQL::select('default')
            ->distinct()
            ->all('m.*')
            ->from('maps', 'm')
            ->join('LEFT', 'map_users', 'mu')
            ->on('mu.map_id', '=', 'm.id')
            ->where('m.enabled', '=', 1)
            ->where_block('(')
            ->where('m.security_id', '=', 1)
            ->where('m.security_id', '=', 2, 'OR')
            ->where('m.security_id', '=', 4, 'OR')
            ->where_block('(', 'OR')
            ->where('m.security_id', '=', 3)
            ->where('mu.user_id', '=', $user_id, 'AND')
            ->where_block(')')
            ->where_block(')');

        $result = $builder->query();

        if ($result->is_loaded()) {
            $maps = array();
            foreach ($result as $record) {
                $maps[] = DB_ORM::model('map', array((int)$record['id']));
            }

            return $maps;
        }

        return NULL;
    }

    public function getAllMapsForLearner($learnerId) {
        $builder = DB_SQL::select('default')
            ->distinct()
            ->all('m.*')
            ->from('maps', 'm')
            ->join('LEFT', 'map_users', 'mu')
            ->on('mu.map_id', '=', 'm.id')
            ->where('security_id', '=', 1)
            ->where('mu.user_id', '=', $learnerId, 'OR')
            ->order_by('m.id', 'DESC');

        $result = $builder->query();

        if ($result->is_loaded()) {
            $maps = array();
            foreach ($result as $record) {
                $maps[] = DB_ORM::model('map', array((int) $record['id']));
            }

            return $maps;
        }

        return NULL;
    }

    public function getAllEnabledAndAuthoredMap($authorId, $limit = 0)
    {
        $limit = (int)$limit;
        $builder = DB_SQL::select('default')
            ->all('m.*')
            ->from('maps', 'm')
            ->join('LEFT', 'map_users', 'mu')
            ->on('mu.map_id', '=', 'm.id')
            ->where('enabled', '=', 1)
            ->where('author_id', '=', $authorId, 'AND')
            ->where('mu.user_id', '=', $authorId, 'OR')
            ->group_by('m.id')
            ->order_by('m.id', 'DESC');
        if ($limit) {
            $builder->limit($limit);
        }

        $result = $builder->query();

        if ($result->is_loaded()) {
            $maps = array();
            foreach ($result as $record) {
                $maps[] = DB_ORM::model('map', array((int)$record['id']));
            }

            return $maps;
        }

        return NULL;
    }

    public function getAllEnabledAndCloseMap()
    {
        $builder = DB_SQL::select('default')
            ->from($this->table())
            ->where('enabled', '=', 1, 'AND')
            ->where('security_id', '=', 2);
        $result = $builder->query();

        if ($result->is_loaded()) {
            $maps = array();
            foreach ($result as $record) {
                $maps[] = DB_ORM::model('map', array((int)$record['id']));
            }

            return $maps;
        }

        return NULL;
    }

    public function getAllEnabledAndKeyMap()
    {
        $builder = DB_SQL::select('default')
            ->from($this->table())
            ->where('enabled', '=', 1, 'AND')
            ->where('security_id', '=', 4);
        $result = $builder->query();

        if ($result->is_loaded()) {
            $maps = array();
            foreach ($result as $record) {
                $maps[] = DB_ORM::model('map', array((int)$record['id']));
            }

            return $maps;
        }

        return NULL;
    }

    public function createMap($values, $isCreateRoot = true)
    {
        $this->name = $this->getMapName(Arr::get($values, 'title', 'empty_title'));
        $this->author_id = Arr::get($values, 'author', 1);
        $this->abstract = Arr::get($values, 'description', 'empty_description');
        $this->keywords = Arr::get($values, 'keywords', 'empty_keywords');
        $this->type_id = Arr::get($values, 'type', 1);
        $this->skin_id = Arr::get($values, 'skin', 1);
        $this->timing = Arr::get($values, 'timing', FALSE);
        $this->delta_time = Arr::get($values, 'delta_time', 0);
        $this->security_id = Arr::get($values, 'security', 2);
        $this->section_id = Arr::get($values, 'section', 1);
        $this->language_id = Arr::get($values, 'language_id', 1);

        $this->save();

        $map = $this->getMapByName($this->name);
        if ($isCreateRoot) {
            DB_ORM::model('map_node')->createDefaultRootNode($map->id);
        }

        return $map;
    }

    public function createVUEMap($title, $authorId)
    {
        $builder = DB_ORM::insert('map')
            ->column('name', $this->getMapName($title))
            ->column('enabled', 1)
            ->column('abstract', 'VUE upload')
            ->column('author_id', $authorId)
            ->column('type_id', 3)
            ->column('security_id', 3)
            ->column('skin_id', 1)
            ->column('section_id', 2)
            ->column('keywords', '')
            ->column('timing', FALSE)
            ->column('delta_time', 0)
            ->column('startScore', 0)
            ->column('threshold', 0)
            ->column('units', '')
            ->column('guid', '')
            ->column('show_bar', FALSE)
            ->column('show_score', FALSE)
            ->column('feedback', '')
            ->column('dev_notes', '')
            ->column('source', '')
            ->column('source_id', 0)
            ->column('language_id', 1);

        return $builder->execute();
    }

    public function disableMap($id)
    {
        $this->id = $id;
        $this->load();

        $this->enabled = FALSE;
        $this->save();
    }

    public function updateMap($id, $values)
    {
        $this->id = $id;
        $this->load();

        $this->name = $this->getMapName(Arr::get($values, 'title', 'empty_title'), $id);
        $this->abstract = Arr::get($values, 'description', 'empty_description');
        $this->keywords = Arr::get($values, 'keywords', 'empty_keywords');
        $this->type_id = Arr::get($values, 'type', 1);
        $this->skin_id = Arr::get($values, 'skin', 1);
        $this->timing = Arr::get($values, 'timing', FALSE);
        $this->delta_time = Arr::get($values, 'delta_time', 0);
        $this->reminder_msg = Arr::get($values, 'reminder_msg', 'empty_reminder_msg');
        $this->reminder_time = Arr::get($values, 'reminder_time', 0);
        $this->security_id = Arr::get($values, 'security', 2);
        $this->section_id = Arr::get($values, 'section', 1);
        $this->link_logic = Arr::get($values, 'link_logic_date', 0);
        $this->node_cont = Arr::get($values, 'node_cont_date', 0);
        $this->clinical_acc = Arr::get($values, 'clinical_acc_date', 0);
        $this->media_cont = Arr::get($values, 'media_cont_date', 0);
        $this->media_copy = Arr::get($values, 'media_copy_date', 0);
        $this->inst_guide = Arr::get($values, 'inst_guide_date', 0);
        $this->metadata_file_id = Arr::get($values, 'metadata_file_id', 0);

        $this->save();
    }

    public function updateMapSkin($id, $value)
    {
        $this->id = $id;
        $this->load();

        $this->skin_id = $value;

        $this->save();
    }

    public function updateSection($mapId, $value)
    {
        $this->id = $mapId;
        $this->load();

        if ($this) {
            $this->section_id = Arr::get($value, 'sectionview', $this->section_id);
            $this->save();
        }
    }

    public function updateFeedback($mapId, $feedback)
    {
        if ($feedback != NULL) {
            $this->id = $mapId;
            $this->load();

            $this->feedback = $feedback;
            $this->save();
        }
    }

    public function updateType($mapId, $typeId) {
        $this->id = $mapId;
        $this->load();

        if ($this && $typeId > 0) {
            $this->type_id = $typeId;
            $this->save();
        }
    }

    public function getMaps($mapIDs)
    {
        $builder = DB_SQL::select('default')->from($this->table())->where('id', 'NOT IN', $mapIDs)->order_by('name');
        $result = $builder->query();

        if ($result->is_loaded()) {
            $maps = array();
            foreach ($result as $record) {
                $maps[] = DB_ORM::model('map', array((int)$record['id']));
            }

            return $maps;
        }

        return NULL;
    }

    public function getMapsIn($mapIDs)
    {
        $builder = DB_SQL::select('default')->from($this->table())->where('id', 'IN', $mapIDs);
        $result = $builder->query();

        if ($result->is_loaded()) {
            $maps = array();
            foreach ($result as $record) {
                $maps[] = DB_ORM::model('map', array((int)$record['id']));
            }

            return $maps;
        }

        return NULL;
    }

    public function updateMapSecurity($mapId, $securityId)
    {
        $this->id = $mapId;
        $this->load();

        if ($this->is_loaded()) {
            $this->security_id = $securityId;
            $this->save();
        }
    }

    public function getSearchMap($key, $onlyTitle = TRUE)
    {
        $builder = DB_SQL::select('default')
            ->from($this->table())
            ->where('enabled', '=', 1)
            ->where('name', 'LIKE', '%'.$key.'%');

        if (!$onlyTitle){
            $builder->where('abstract', 'LIKE', '%'.$key.'%', 'OR');
        }


        $result = $builder->query();

        if ($result->is_loaded()) {
            $maps = array();
            foreach ($result as $record) {
                $maps[] = DB_ORM::model('map', array((int)$record['id']));
            }

            return $maps;
        }

        return NULL;
    }

    public function duplicateMap($mapId)
    {
        $this->id = $mapId;
        $this->load();

        if (strlen($this->name) <= 0) return;

        $builder = DB_ORM::insert('map')
            ->column('name', $this->getMapName($this->name))
            ->column('author_id', $this->author_id)
            ->column('abstract', $this->abstract)
            ->column('startScore', $this->startScore)
            ->column('threshold', $this->threshold)
            ->column('keywords', $this->keywords)
            ->column('type_id', $this->type_id)
            ->column('units', $this->units)
            ->column('security_id', $this->security_id)
            ->column('guid', $this->guid)
            ->column('timing', $this->timing)
            ->column('delta_time', $this->delta_time)
            ->column('show_bar', $this->show_bar)
            ->column('show_score', $this->show_score)
            ->column('skin_id', $this->skin_id)
            ->column('enabled', $this->enabled)
            ->column('section_id', $this->section_id)
            ->column('language_id', $this->language_id)
            ->column('feedback', $this->feedback)
            ->column('dev_notes', $this->dev_notes)
            ->column('source', $this->source)
            ->column('source_id', $this->source_id);

        $newMapId = $builder->execute();
        $nodeMap = DB_ORM::model('map_node')->duplicateNodes($mapId, $newMapId);
        DB_ORM::model('map_node_section')->duplicateSections($mapId, $newMapId, $nodeMap);
        $elementMap = DB_ORM::model('map_element')->duplicateElements($mapId, $newMapId);
        DB_ORM::model('map_node_link')->duplicateLinks($mapId, $newMapId, $nodeMap, $elementMap);
        $counterMap = DB_ORM::model('map_counter')->duplicateCounters($mapId, $newMapId, $nodeMap, $elementMap);
        $chatMap = DB_ORM::model('map_chat')->duplicateChats($mapId, $newMapId, $counterMap);
        $questionMap = DB_ORM::model('map_question')->duplicateQuestions($mapId, $newMapId, $counterMap);
        $avartMap = DB_ORM::model('map_avatar')->duplicateAvatars($mapId, $newMapId);
        $vpdMap = DB_ORM::model('map_vpd')->duplicateElements($mapId, $newMapId);
        $damsMap = DB_ORM::model('map_dam')->duplicateDam($mapId, $newMapId, $vpdMap, $elementMap);
        DB_ORM::model('map_feedback_rule')->duplicateRules($mapId, $newMapId);
        DB_ORM::model('map_user')->duplicateUsers($mapId, $newMapId);
        DB_ORM::model('map_key')->duplicateKeys($mapId, $newMapId);
        DB_ORM::model('map_node')->replaceDuplcateNodeContenxt($nodeMap, $elementMap, $vpdMap, $avartMap, $chatMap, $questionMap, $damsMap);
    }

    public function getMapName($mapName, $mapId = 0)
    {
        $result = $mapName;
        $builder = DB_SQL::select('default')
            ->from($this->table())
            ->where('name', '=', $mapName)
                ->column('name')
                ->column('id');
        $query = $builder->query();

        if ($query->is_loaded() && $query->count() > 0) {
            if($query->count() == 1 && $mapId > 0 && $query[0]['id'] == $mapId) {
                return $result;
            }

            $addNumber = 1;
            $tmpName = $mapName . '_%';
            $builder = DB_SQL::select('default')
                ->from($this->table())
                ->where('name', 'like', $tmpName)
                ->column('name');
            $query = $builder->query();
            if ($query->is_loaded() && $query->count() > 0) {
                foreach ($query as $record) {
                    $expl = explode($mapName . '_', $record['name']);
                    if (count($expl) == 2) {
                        if (is_int((int)$expl[1])) {
                            $n = (int)$expl[1];
                            if ($addNumber <= $n)
                                $addNumber = $n + 1;
                        }
                    }
                }
            }

            $result .= '_' . $addNumber;
        }

        return $result;
    }

    public function countLinks(){
        $result = count(DB_ORM::model("Model_Leap_Map_Node_Link")->getLinksByMap($this->id));
        return $result;
    }

    public function createLinearMap($mapId, $values) {
        if($mapId <= 0) return;

        $count = Arr::get($values, 'nodesCount', 0);

        $node = DB_ORM::model('map_node')->createNode(array('map_id' => $mapId,
                                                            'mnodetitle' => Arr::get($values, 'rootTitle', ''),
                                                            'mnodetext' => Arr::get($values, 'rootContent', ''),
                                                            'type_id' => 1));
        $oldNode = $node;
        for($i = 1; $i <= $count; $i++) {
            $node = DB_ORM::model('map_node')->createNode(array('map_id' => $mapId,
                                                                'mnodetitle' => Arr::get($values, 'nodeTitle' . $i, ''),
                                                                'mnodetext' => Arr::get($values, 'nodeContent' . $i, '')));
            if($oldNode != null && $node != null) {
                DB_ORM::model('map_node_link')->addFullLink($mapId, array('node_id_1' => $oldNode->id, 'node_id_2' => $node->id));
                $oldNode = $node;
            }
        }

        $node = DB_ORM::model('map_node')->createNode(array('map_id' => $mapId,
                                                            'mnodetitle' => Arr::get($values, 'endTitle', ''),
                                                            'mnodetext' => Arr::get($values, 'endContent', '')));

        if($oldNode != null && $node != null) {
            DB_ORM::model('map_node_link')->addFullLink($mapId, array('node_id_1' => $oldNode->id, 'node_id_2' => $node->id));
        }
    }

    public function createBranchedMap($mapId, $values) {
        if($mapId <= 0) return;

        $count = Arr::get($values, 'nodesCount', 0);

        $node = DB_ORM::model('map_node')->createNode(array('map_id' => $mapId,
                                                            'mnodetitle' => Arr::get($values, 'rootTitle', ''),
                                                            'mnodetext' => Arr::get($values, 'rootContent', ''),
                                                            'type_id' => 1));
        $oldNode = $node;
        $nodes = array();
        for($i = 1; $i <= $count; $i++) {
            $node = DB_ORM::model('map_node')->createNode(array('map_id' => $mapId,
                                                                'mnodetitle' => Arr::get($values, 'nodeTitle' . $i, ''),
                                                                'mnodetext' => Arr::get($values, 'nodeContent' . $i, '')));
            $nodes[] = $node;
            if($oldNode != null && $node != null) {
                DB_ORM::model('map_node_link')->addFullLink($mapId, array('node_id_1' => $oldNode->id, 'node_id_2' => $node->id));
            }
        }

        $node = DB_ORM::model('map_node')->createNode(array('map_id' => $mapId,
                                                            'mnodetitle' => Arr::get($values, 'endTitle', ''),
                                                            'mnodetext' => Arr::get($values, 'endContent', '')));
        if(count($nodes) > 0) {
            foreach($nodes as $n) {
                DB_ORM::model('map_node_link')->addFullLink($mapId, array('node_id_1' => $n->id, 'node_id_2' => $node->id));
            }
        } else if($oldNode != null && $node != null) {
            DB_ORM::model('map_node_link')->addFullLink($mapId, array('node_id_1' => $oldNode->id, 'node_id_2' => $node->id));
        }
    }

    public function exportMVP($mapId){
        $builder = DB_SQL::select('default')
            ->from($this->table())
            ->where('id', '=', $mapId);

        $result = $builder->query();

        if ($result->is_loaded()) {
            return $result[0];
        }

        return NULL;
    }

    public function getAllowedMap($userId) {

        $builder = DB_SQL::select('default', array(DB_SQL::expr('m.id')))
            ->from('maps', 'm')
            ->join('LEFT', 'map_users', 'mu')
            ->on('mu.map_id', '=', 'm.id')
            ->where('enabled', '=', 1)
            ->where('author_id', '=', $userId, 'AND')
            ->where('mu.user_id', '=', $userId, 'OR')
            ->order_by('m.id', 'DESC');

        $result = $builder->query();

        $res = array();

        if ($result->is_loaded()) {
            foreach ($result as $record => $val) {
                $res[] =  $val['id'];
            }
        }
        return $res;

    }
}


