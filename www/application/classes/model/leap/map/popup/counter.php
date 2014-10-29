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
class Model_Leap_Map_Popup_Counter extends DB_ORM_Model {

    public function __construct() {
        parent::__construct();

        $this->fields = array(
            'id' => new DB_ORM_Field_Integer($this, array(
                    'max_length' => 11,
                    'nullable' => FALSE,
                    'unsigned' => TRUE,
                )),

            'popup_id' => new DB_ORM_Field_Integer($this, array(
                    'max_length' => 11,
                    'nullable' => FALSE,
                )),

            'counter_id' => new DB_ORM_Field_Integer($this, array(
                    'max_length' => 10,
                    'nullable' => FALSE,
                )),

            'function' => new DB_ORM_Field_Text($this, array(
                    'nullable' => FALSE,
                    'savable' => TRUE,
                )),
        );

    }

    public static function data_source() {
        return 'default';
    }

    public static function table() {
        return 'map_popups_counters';
    }

    public static function primary_key() {
        return array('id');
    }

    public function deleteCounters($id, $field) {
        $result = DB_SQL::select('default')
            ->from($this->table())
            ->where($field, '=', $id)
            ->query();

        foreach ($result as $row){
            $this->id = $row['id'];
            $this->load();
            $this->delete();
        }
    }

    public function addPopupCounter ( $popupId, $counterId, $function) {
        $result = DB_SQL::select('default')
            ->from($this->table())
            ->where('popup_id', '=', $popupId, 'AND')
            ->where('counter_id', '=', $counterId)
            ->query();

        if ( ! $result->is_loaded()) {
            $this->popup_id = $popupId;
            $this->counter_id = $counterId;
            $this->function = str_replace(',', '.', $function);
            $this->save();
            $this->reset();
        } else {
            $this->updatePopupCounter($popupId, $counterId, $function);
        }
    }

    public function updatePopupCounter($popupId, $counterId, $function) {
        $result = DB_SQL::select('default')
            ->from($this->table())
            ->where('popup_id', '=', $popupId, 'AND')
            ->where('counter_id', '=', $counterId)
            ->query();

        if($result->is_loaded()){
            $this->id = $result[0]['id'];
            $this->load();
            if($this) {
                $this->function = str_replace(',', '.', $function);
                $this->save();
            }
        }
    }

    public function createPopupCounters($popupId, $counterId, $function) {
        $record = DB_ORM::model('map_popup_counter');
        $record->popup_id = $popupId;
        $record->counter_id = $counterId;
        $record->function = $function;
        $record->save();
    }

    public function getPopupCountersByMap ($map_id) {
        $result = DB_SQL::select('default', array(
            'map_popups_counters.id',
            'map_popups_counters.popup_id',
            'map_popups_counters.counter_id',
            'map_popups_counters.function',
        ))
            ->from('map_counters')
            ->join('RIGHT', 'map_popups_counters')
            ->on('map_popups_counters.counter_id', '=', 'map_counters.id')
            ->where('map_id', '=', $map_id)
            ->query();

        if ($result->is_loaded()) {
            $counters = array();
            foreach($result as $record) {
                $counters[] = DB_ORM::model('map_popup_counter', array((int)$record['id']));
            }
            return $counters;
        }
        return NULL;
    }

    public function updatePopupCounters($values, $popupId)
    {
        $counters = DB_SQL::select('default')->from($this->table())->where('popup_id', '=', $popupId)->query();

        // update records
        foreach ($counters as $counter) {
            $counter_id = Arr::get($counter, 'counter_id');
            $this->updatePopupCounter($popupId, $counter_id, Arr::get($values, $counter_id));
            unset ($values[$counter_id]);
        }

        // new records
        foreach ($values as $counterId=>$function) $this->createPopupCounters($popupId, $counterId, $function);
    }

    public function saveCounters ($id_popup, $info){
        foreach ($info as $id_counter=>$v){
            $result = DB_SQL::select('default')->from($this->table())->where('popup_id', '=', $id_popup, 'AND')->where('counter_id', '=', $id_counter)->query();
            $result[0]['id'] ? $this->updatePopupCounter($id_popup, $id_counter, Arr::get($v, 'function'))
                             : $this->createPopupCounters($id_popup, $id_counter, Arr::get($v, 'function'));
        }
    }

    public function getCountersScore ($popup_id) {
        $records = DB_SQL::select('default')->from($this->table())->where('popup_id', '=', $popup_id)->query();

        $result = array();
        if($records->is_loaded()) {
            foreach($records as $r) {
                $result[] = DB_ORM::model('map_popup_counter', array((int)$r['id']));
            }
        }
        return $result;
    }

    public function exportMVP($popupId)
    {
        return DB_SQL::select('default')->from($this->table())->where('popup_id', '=', $popupId)->query()->as_array();
    }
}