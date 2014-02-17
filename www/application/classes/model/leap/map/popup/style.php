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
 * Model for user_types table in database 
 */
class Model_Leap_Map_Popup_Style extends DB_ORM_Model {

    public function __construct() {
        parent::__construct();

        $this->fields = array(
            'map_popup_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE
            )),

            'is_default_background_color' => new DB_ORM_Field_Boolean($this, array(
                'default' => TRUE,
                'nullable' => FALSE,
                'savable' => TRUE
            )),

            'is_background_transparent' => new DB_ORM_Field_Boolean($this, array(
                'default' => FALSE,
                'nullable' => FALSE,
                'savable' => TRUE
            )),
            
            'background_color' => new DB_ORM_Field_String($this, array(
                'max_length' => 10,
                'nullable' => FALSE,
                'savable' => TRUE
            )),

            'font_color' => new DB_ORM_Field_String($this, array(
                'max_length' => 10,
                'nullable' => FALSE,
                'savable' => TRUE
            )),

            'border_color' => new DB_ORM_Field_String($this, array(
                'max_length' => 10,
                'nullable' => FALSE,
                'savable' => TRUE
            )),

            'is_border_transparent' => new DB_ORM_Field_Boolean($this, array(
                'default' => FALSE,
                'nullable' => FALSE,
                'savable' => TRUE
            )),

            'background_transparent' => new DB_ORM_Field_String($this, array(
                'max_length' => 4,
                'nullable' => FALSE,
                'savable' => TRUE
            )),

            'border_transparent' => new DB_ORM_Field_String($this, array(
                'max_length' => 4,
                'nullable' => FALSE,
                'savable' => TRUE
            ))
        );
    }
    
    public static function data_source() {
        return 'default';
    }

    public static function table() {
        return 'map_popups_styles';
    }

    public static function primary_key() {
        return array('map_popup_id');
    }

    public function saveStyle($popupId, $values) {
        if($popupId == null || $popupId <= 0 || $values == null || count($values) <= 0) return;

        $style                    = DB_SQL::select('default')->from($this->table())->where('map_popup_id', '=', $popupId)->limit(1)->query();;
        $isDefaultBackgroundColor = Arr::get($values, 'isDefaultBackgroundColor', 0);
        $isBackgroundTransparent  = Arr::get($values, 'isBackgroundTransparent', 'off') != 'off';
        $isBorderTransparent      = Arr::get($values, 'isBorderTransparent', 'off') != 'off';
        $backgroundColor          = ($isDefaultBackgroundColor == 1) ? Arr::get($values, 'defaultBackgroundColor', '#ffff00')
                                                                     : Arr::get($values, 'customBackgroundColor',  '#ffff00');

        if($style->is_loaded()) {
            DB_SQL::update('default')
                    ->table($this->table())
                    ->set('is_default_background_color', $isDefaultBackgroundColor == 1)
                    ->set('background_color',            $backgroundColor)
                    ->set('is_background_transparent',   $isBackgroundTransparent)
                    ->set('font_color',                  Arr::get($values, 'fontColor',   '#000000'))
                    ->set('border_color',                Arr::get($values, 'borderColor', '#ffffff'))
                    ->set('is_border_transparent',       $isBorderTransparent)
                    ->set('background_transparent',      Arr::get($values, 'background_transparent',   '0%'))
                    ->set('border_transparent',          Arr::get($values, 'border_transparent',   '0%'))
                    ->where('map_popup_id', '=', $popupId)
                    ->execute();
        } else {
            DB_SQL::insert('default')
                    ->into($this->table())
                    ->column('map_popup_id',                $popupId)
                    ->column('is_default_background_color', $isDefaultBackgroundColor == 1)
                    ->column('background_color',            $backgroundColor)
                    ->column('is_background_transparent',   $isBackgroundTransparent)
                    ->column('font_color',                  Arr::get($values, 'fontColor',   '#000000'))
                    ->column('border_color',                Arr::get($values, 'borderColor', '#ffffff'))
                    ->column('is_border_transparent',       $isBorderTransparent)
                    ->column('background_transparent',      Arr::get($values, 'background_transparent',   '0%'))
                    ->column('border_transparent',          Arr::get($values, 'border_transparent',   '0%'))
                    ->execute();
        }
    }
}

