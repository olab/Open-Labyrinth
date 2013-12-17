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
class Model_Leap_Map_Popup_Assign extends DB_ORM_Model {

    public function __construct() {
        parent::__construct();

        $this->fields = array(
            'id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            'map_popup_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            'assign_type_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            'assign_to_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            'redirect_type_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            'redirect_to_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => TRUE,
            )),
        );
    }

    public static function data_source() {
        return 'default';
    }

    public static function table() {
        return 'map_popups_assign';
    }

    public static function primary_key() {
        return array('id');
    }

    /**
     * Assign popup
     *
     * @param {integer} $popupId - popup ID
     * @param {integer} $mapId - map ID
     * @param {array} $values - assign values
     * @param (string) $how_many - one or many
     */
    public function assignPopup($popupId, $mapId, $values) {
        if($popupId == null || $mapId == null || $mapId <= 0 || $values == null || count($values) <= 0) return;

        $assignId     = $mapId;
        $assignType   = Arr::get($values, 'assignType', Popup_Assign_Types::LABYRINTH);
        $redirectType = Arr::get($values, 'redirectType', Popup_Redirect_Types::NONE);
        $redirectId   = null;

        switch ($assignType) {
            case Popup_Assign_Types::NODE:
                $assignId = Arr::get($values, 'node', 0);
                break;
            case Popup_Assign_Types::SECTION:
                $assignId = Arr::get($values, 'section', 0);
                break;
        }

        switch ($redirectType) {
            case Popup_Redirect_Types::NODE:
                $redirectId = Arr::get($values, 'redirectNodeId', null);
                break;
        }

        $assign_from_db = array();
        // get all popups assign to node
        foreach (DB_SQL::select('default')->from($this->table())->where('map_popup_id', '=', $popupId)->query() as $id) {
            $assign_from_db[$id['id']] = $id['assign_to_id'];
        }

        // delete assign that not included in $assignId
        foreach (array_diff($assign_from_db, $assignId) as $id=>$v) {
            DB_ORM::model('map_popup_assign', $id)->delete();
        }

        if ($assignType == Popup_Assign_Types::NODE) {
            foreach ($assignId as $id_node){
                $id = array_search($id_node, $assign_from_db);
                if($id) {
                    DB_SQL::update('default')
                        ->table($this->table())
                        ->set('map_popup_id',     $popupId)
                        ->set('assign_type_id',   $assignType)
                        ->set('assign_to_id',     $id_node)
                        ->set('redirect_type_id', $redirectType)
                        ->set('redirect_to_id',   $redirectId)
                        ->where('id', '=', $id)
                        ->execute();
                } else {
                    DB_SQL::insert('default')
                        ->into($this->table())
                        ->column('map_popup_id',     $popupId)
                        ->column('assign_type_id',   $assignType)
                        ->column('assign_to_id',     $id_node)
                        ->column('redirect_type_id', $redirectType)
                        ->column('redirect_to_id',   $redirectId)
                        ->execute();
                }
            }
        } else {
            DB_SQL::insert('default')
                ->into($this->table())
                ->column('map_popup_id',     $popupId)
                ->column('assign_type_id',   $assignType)
                ->column('assign_to_id',     $assignId)
                ->column('redirect_type_id', $redirectType)
                ->column('redirect_to_id',   $redirectId)
                ->execute();
        }
    }
}