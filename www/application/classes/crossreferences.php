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

class CrossReferences {

    public function checkReference($mapId, $nodeId, $text, $info)
    {
        $textRecords   = array();
        $textRecords[] = $this->getReferenceField($mapId, $nodeId, $text);
        $textRecords[] = $this->getReferenceField($mapId, $nodeId, $info);
        $replace       = array();
        for($i = 0; $i<=1; $i++)
        {
            if($textRecords[$i])
            {
                foreach($textRecords[$i] as $record)
                {
                    $key = false;
                    $parent = false;
                    switch ($record['type'])
                    {
                        case 'MR':
                            $element = DB_ORM::model('map_element', array((int) $record['element_id']));
                            if ($element->map_id && !$element->is_private) $key = true;
                            if ($element->map_id == $mapId) $parent = true;
                            break;
                        case 'VPD':
                            $vpdElements = DB_ORM::model('map_vpd_element')->getValuesByVpdId($record['element_id']);
                            if ($vpdElements)
                            {
                                foreach($vpdElements as $vpdElement)
                                {
                                    if($vpdElement->key == 'Private') $vpdPrivate = $vpdElement->value;
                                }
                            }
                            if (empty($vpdPrivate)) $vpdPrivate = 'Off';
                            if ($vpdPrivate == 'Off') $key = true;
                            $vpdMapId = DB_ORM::model('map_vpd')->getMapId($record['element_id']);
                            if ($vpdMapId == $mapId) $parent = true;
                            break;
                        case 'QU':
                            $question = DB_ORM::model('map_question', array((int) $record['element_id']));
                            if ($question->map_id && !$question->is_private) $key = true;
                            if ($question->map_id == $mapId) $parent = true;
                            break;
                        case 'INFO':
                            $nod = DB_ORM::model('map_node', array((int) $record['element_id']));
                            if ($nod->map_id && !$nod->is_private) $key = true;
                            if ($nod->map_id == $mapId) $parent = true;
                            break;
                        case 'AV':
                            $avatar = DB_ORM::model('map_avatar', array((int) $record['element_id']));
                            if ($avatar->map_id && !$avatar->is_private) $key = true;
                            if ($avatar->map_id == $mapId) $parent = true;
                            break;
                        case 'CHAT':
                            $chat = DB_ORM::model('map_chat', array((int) $record['element_id']));
                            if ($chat->map_id && !$chat->is_private) $key = true;
                            if ($chat->map_id == $mapId) $parent = true;
                            break;
                        case 'DAM':
                            $dam = DB_ORM::model('map_dam', array((int) $record['element_id']));
                            if ($dam->map_id && !$dam->is_private) $key = true;
                            if ($dam->map_id == $mapId) $parent = true;
                            break;
                    }

                    if ($key || $parent) $this->addReferenceToBD($mapId, $nodeId, $record);
                    else
                    {
                        $search = '[['.$record['type'].':'.$record['element_id'].']]';
                        if($i == 0)
                        {
                            $text = str_replace($search, '', $text);
                            $replace['text'] = $text;
                        }
                        if($i == 1)
                        {
                            $info = str_replace($search, '', $info);
                            $replace['info'] = $info;
                        }
                    }
                }
            }
        }
        $this->deleteReferenceFromBD($mapId, $nodeId, $textRecords);
        return $replace;
    }

    private function deleteReferenceFromBD($mapId, $nodeId, $records){
        $dbRecords = DB_ORM::model('map_node_reference')->getByMapeNodeId($mapId, $nodeId);
        if(is_array($records[0]) && is_array($records[1])){
            $records = array_merge($records[0],$records[1]);
        } else{
            $records = $records[0];
        }
        if(is_array($records) && is_array($dbRecords)){
            foreach($dbRecords as $dbRecord){
                $flag = false;
                foreach($records as $record){
                    if($dbRecord->element_id == $record['element_id'] && $dbRecord->type == $record['type']){
                        $flag = true;
                    }
                }
                if(!$flag){
                    DB_ORM::model('map_node_reference')->deleteById($dbRecord->id);
                }
            }
        } else {
            DB_ORM::model('map_node_reference')->deleteByNodeId($mapId, $nodeId);
        }
    }

    private function addReferenceToBD($mapId, $nodeId, $record){
        $dbRecords = DB_ORM::model('map_node_reference')->getByMapeNodeId($mapId, $nodeId);
        $flag = false;
        if($dbRecords){
            foreach($dbRecords as $dbRecord){
                if($dbRecord->element_id == $record['element_id'] && $dbRecord->type == $record['type']){
                    $flag = true;
                }
            }
        }
        if(!$flag){
            $reference = DB_ORM::model('map_node_reference')->addReference($nodeId, $record);
        }
    }

    private function getReferenceField($mapId, $nodeId, $text){
        $codes = array('MR', 'VPD', 'QU', 'INFO', 'AV', 'CHAT', 'DAM');
        $result = array();
        foreach ($codes as $code) {
            $regExp = '/[\[' . $code . ':\d\]]+/';
            if (preg_match_all($regExp, $text, $matches)) {
                foreach ($matches as $match) {
                    foreach ($match as $value) {
                        if (stristr($value, '[[' . $code . ':')) {
                            $m = explode(':', $value);
                            $id = substr($m[1], 0, strlen($m[1]) - 2);
                            if (is_numeric($id)) {
                                $result[] = array('map_id'=>$mapId,'node_id'=>$nodeId,'element_id'=>$id, 'type'=>$code);
                            }
                        }
                    }
                }
            }
        }
        return $result;
    }

    public static function getListReferenceForView($references){
        $listOfUsedReferences = array();
        foreach($references as $reference){
            $nod = DB_ORM::model('map_node', array((int) $reference->node_id));
            $map = DB_ORM::model('map', array((int) $reference->map_id));
            $listOfUsedReferences[] = array(array('map_id'     => $reference->map_id,
                'map_name'   => $map->name),
                array('node_id'    => $reference->node_id,
                    'node_title' => $nod->title));
        }
        return $listOfUsedReferences;
    }

}