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
class Model_Leap_Map_Popup extends DB_ORM_Model {

    public function __construct() {
        parent::__construct();

        $this->fields = array(
            'id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),

            'map_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE
            )),

            'title' => new DB_ORM_Field_String($this, array(
                'max_length' => 300,
                'nullable' => FALSE,
                'savable' => TRUE
            )),

            'text' => new DB_ORM_Field_Text($this, array(
                'nullable' => FALSE,
                'savable' => TRUE
            )),

            'position_type' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE
            )),

            'position_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE
            )),

            'time_before' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
                'savable' => TRUE,
                'default' => 0
            )),

            'time_length' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
                'savable' => TRUE,
                'default' => 0
            )),

            'is_enabled' => new DB_ORM_Field_Boolean($this, array(
                'nullable' => FALSE,
                'default' => TRUE
            )),

            'title_hide' => new DB_ORM_Field_Boolean($this, array(
                'nullable' => FALSE
            )),

            'annotation' => new DB_ORM_Field_Text($this, array(
                'nullable' => FALSE,
                'savable' => TRUE
            ))
        );

        $this->relations = array(
            'assign' => new DB_ORM_Relation_HasOne($this, array(
                'child_key' => array('map_popup_id'),
                'child_model' => 'map_popup_assign',
                'parent_key' => array('id')
            )),

            'style' => new DB_ORM_Relation_HasOne($this, array(
                'child_key' => array('map_popup_id'),
                'child_model' => 'map_popup_style',
                'parent_key' => array('id')
            ))
        );
    }

    public static function data_source() {
        return 'default';
    }

    public static function table() {
        return 'map_popups';
    }

    public static function primary_key() {
        return array('id');
    }

    /**
     * Get all map popups
     *
     * @param {integer} $mapId - map ID
     * @return array - array of map popups
     */
    public function getAllMapPopups($mapId) {
        $records = DB_SQL::select('default')
                           ->column('id')
                           ->from($this->table())
                           ->where('map_id', '=', $mapId)
                           ->query();
        $result = array();

        if($records->is_loaded()) {
            foreach($records as $record) {
                $result[] = DB_ORM::model('map_popup', array((int)$record['id']));
            }
        }

        return $result;
    }

    /**
     * Save popup
     *
     * @param {integer} $mapId - map ID
     * @param {array} $values - popup values
     * @return null|{integer} - null or popup ID
     */
    public function savePopup($mapId, $values) {
        if($mapId == null || $values == null || count($values) <= 0) return null;
        $popupId = Arr::get($values, 'popupId', null);

        return ($popupId != null && $popupId > 0) ? $this->updatePopup($popupId, $mapId, $values)
                                                  : $this->createNewPopup($mapId, $values);
    }

    /**
     * Get all enabled map popups
     *
     * @param {integer} $mapId - map ID
     * @return array - array of enabled map popups
     */
    public function getEnabledMapPopups($mapId) {
        $records = DB_SQL::select('default')
                           ->column('id')
                           ->from($this->table())
                           ->where('map_id', '=', $mapId, 'AND')
                           ->where('is_enabled', '=', 1)
                           ->query();
        $result  = array();

        if($records->is_loaded()) {
            foreach($records as $record) {
                $result[] = DB_ORM::model('map_popup', array((int) $record['id']));
            }
        }

        return $result;
    }

    private function createNewPopup($mapId, $values) {
        $newPopupId = DB_ORM::insert('map_popup')
                              ->column('map_id',        $mapId)
                              ->column('title',         Arr::get($values, 'title',        ''))
                              ->column('text',          Arr::get($values, 'text',         ''))
                              ->column('position_type', Arr::get($values, 'positionType', Popup_Position_Types::OUTSIDE_NODE))
                              ->column('position_id',   Arr::get($values, 'position',     Popup_Positions::TOP_LEFT))
                              ->column('time_before',   Arr::get($values, 'timeBefore',   0))
                              ->column('time_length',   Arr::get($values, 'timeLength',   0))
                              ->column('is_enabled',    Arr::get($values, 'enabled',      false))
                              ->column('title_hide',    Arr::get($values, 'title_hide',   0))
                              ->column('annotation',    Arr::get($values, 'annotation',   ''))
                              ->execute();

        if($newPopupId != null && $newPopupId > 0) {
            DB_ORM::model('map_popup_assign')->assignPopup($newPopupId, $mapId, $values);
            DB_ORM::model('map_popup_style')->saveStyle($newPopupId, $values);
        }

        return $newPopupId;
    }

    private function updatePopup($popupId, $mapId, $values) {
        if($popupId == null || $mapId == null || $values == null || count($values) <= 0) return $popupId;

        $popup = DB_SQL::select('default')->from($this->table())->where('id', '=', $popupId)->limit(1)->query();
        if($popup->is_loaded()) {
            DB_SQL::update('default')
                    ->table($this->table())
                    ->set('title',         Arr::get($values, 'title',        ''))
                    ->set('text',          Arr::get($values, 'text',         ''))
                    ->set('position_type', Arr::get($values, 'positionType', Popup_Position_Types::OUTSIDE_NODE))
                    ->set('position_id',   Arr::get($values, 'position',     Popup_Positions::TOP_LEFT))
                    ->set('time_before',   Arr::get($values, 'timeBefore',   0))
                    ->set('time_length',   Arr::get($values, 'timeLength',   0))
                    ->set('is_enabled',    Arr::get($values, 'enabled',      false))
                    ->set('title_hide',    Arr::get($values, 'title_hide',   0))
                    ->set('annotation',    Arr::get($values, 'annotation',   ''))
                    ->where('id', '=', $popupId)
                    ->execute();

            DB_ORM::model('map_popup_assign')->assignPopup($popupId, $mapId, $values);
            DB_ORM::model('map_popup_style')->saveStyle($popupId, $values);
        }

        return $popupId;
    }
}

?>