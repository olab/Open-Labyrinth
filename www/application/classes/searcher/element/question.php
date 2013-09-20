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

class Searcher_Element_Question extends Searcher_Element {
    private $mapId;
    private $fields;

    public function __construct($mapId, $fields) {
        $this->mapId = $mapId;
        $this->fields = $fields;
    }

    public function search($searchText) {
        $data = array();
        $search = '%' . strtolower($searchText) . '%';

        if($this->fields == null || count($this->fields) <= 0 || $searchText == null || empty($searchText)) return $data;

        $builder = DB_SQL::select('default')
                           ->column('M.id', 'id', true)
                           ->from(Model_Leap_Map_Question::table(), 'M');
        $checkForResponse = $this->checkForResponses();
        if($checkForResponse) {
            $builder->join('LEFT OUTER', Model_Leap_Map_Question_Response::table(), 'R');
            $builder->on('M.id', '=', 'R.question_id');
        }

        $builder->where('map_id', '=', $this->mapId, 'AND');
        $builder->where_block('(');
        foreach($this->fields as $field) {
            $fieldName = $field['field'];
            if(isset($field['type']) && $field['type'] == 'response') {
                $fieldName = 'R.' . $fieldName;
            } else {
                $fieldName = 'M.' . $fieldName;
            }

            $builder->where('LOWER(' . $fieldName . ')', 'like', $search, 'OR', true);
        }
        $builder->where_block(')');

        $builder->distinct(true);

        $records = $builder->query();

        if($records->is_loaded()) {
            foreach($records as $record) {
                $modelObject = DB_ORM::model('map_question', array((int)$record['id']));
                $isAdd = false;

                foreach($this->fields as $field) {
                    $fieldName = $field['field'];
                    if(isset($field['type']) && $field['type'] == 'response') {
                        if($modelObject->responses != null && count($modelObject->responses) > 0) {
                            foreach($modelObject->responses as $response) {
                                if(strpos(strtolower(strip_tags($response->$fieldName)), strtolower($searchText)) !== false) {
                                    $isAdd = true;
                                    break;
                                }
                            }
                        }
                    } else if(strpos(strtolower(strip_tags($modelObject->$fieldName)), strtolower($searchText)) !== false) {
                        $isAdd = true;
                        break;
                    }
                }

                if($isAdd) {
                    $content = array();
                    foreach($this->fields as $field) {
                        $fieldName = $field['field'];
                        if(isset($field['type']) && $field['type'] == 'response') {
                            if($modelObject->responses != null && count($modelObject->responses) > 0) {
                                foreach($modelObject->responses as $response) {
                                    if(strpos(strtolower(strip_tags($response->$fieldName)), strtolower($searchText)) !== false) {
                                        $content[] = array('label' => $field['label'], 'value' => $response->$fieldName);
                                    }
                                }
                            }
                        } else {
                            $content[] = array('label' => $field['label'], 'value' => $modelObject->$fieldName);
                        }
                    }

                    $data[] = new Searcher_Result('question', URL::base() . 'questionManager/question/' . $this->mapId . '/' . $modelObject->entry_type_id . '/' . $modelObject->id, $modelObject->stem, $content, $searchText);
                }
            }
        }


        return $data;
    }

    private function checkForResponses() {
        if($this->fields == null || count($this->fields) <= 0) return false;

        foreach($this->fields as $field) {
            if(isset($field['type']) && $field['type'] == 'response') {
                return true;
            }
        }

        return false;
    }
};