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

abstract class Searcher_Element_BasicMap extends Searcher_Element {
    protected $mapId;
    protected $fields;

    public function __construct($mapId, $fields) {
        $this->mapId = $mapId;
        $this->fields = $fields;
    }

    public function search($searchText) {
        $data = array();
        $search = '%' . strtolower($searchText) . '%';

        if($this->fields == null || count($this->fields) <= 0 || $searchText == null || empty($searchText)) return $data;

        $builder = DB_SQL::select('default')
                           ->column('id')
                           ->from($this->getTable());

        $builder->where('map_id', '=', $this->mapId, 'AND');
        $builder->where_block('(');
        foreach($this->fields as $field) {
            $builder->where('LOWER(' . $field['field'] . ')', 'like', $search, 'OR', true);
        }
        $builder->where_block(')');

        $builder->distinct(true);

        $records = $builder->query();

        if($records->is_loaded()) {
            foreach($records as $record) {
                $modelObject = DB_ORM::model($this->getModelName(), array((int)$record['id']));
                $isAdd = false;

                foreach($this->fields as $field) {
                    if(strpos(strtolower(strip_tags($modelObject->$field['field'])), strtolower($searchText)) !== false) {
                        $isAdd = true;
                        break;
                    }
                }

                if($isAdd) {
                    $content = array();
                    foreach($this->fields as $field) {
                        $content[] = array('label' => $field['label'], 'value' => $modelObject->$field['field']);
                    }

                    $data[] = new Searcher_Result($this->getType(), $this->getURL($modelObject), $this->getURLTitle($modelObject), $content, $searchText);
                }
            }
        }

        return $data;
    }

    protected abstract function getTable();
    protected abstract function getModelName();
    protected abstract function getURL($object);
    protected abstract function getURLTitle($object);
    protected abstract function getType();
};