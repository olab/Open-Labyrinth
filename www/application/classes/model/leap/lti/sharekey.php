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
 * Model for lti_contexts in database
 */
class Model_Leap_Lti_ShareKey extends DB_ORM_Model {
    public function __construct() {
        parent::__construct();

        $this->fields = array(
            'share_key_id' => new DB_ORM_Field_String($this, array(
                    'max_length' => 32,
                    'nullable' => FALSE,
                    'savable' => TRUE,
                )),
            'primary_consumer_key' => new DB_ORM_Field_String($this, array(
                    'max_length' => 255,
                    'nullable' => FALSE,
                    'savable' => TRUE,
                )),
            'primary_context_id' => new DB_ORM_Field_String($this, array(
                    'max_length' => 255,
                    'nullable' => FALSE,
                    'savable' => TRUE,
                )),
            'auto_approve' => new DB_ORM_Field_Integer($this, array(
                    'max_length' => 1,
                    'nullable' => FALSE,
                    'savable' => TRUE,
                    'unsigned' => TRUE
                )),
            'expires' => new DB_ORM_Field_DateTime($this, array(
                    'savable' => TRUE,
                    'nullable' => FALSE,
                )),

        );
    }
    public static function data_source() {
        return 'default';
    }

    public static function table() {
        return 'lti_sharekeys';
    }

    public static function primary_key() {
        return array('share_key_id');
    }

    public function addShareKey($values){
        $query = DB_ORM::insert('Lti_ShareKey')
            ->column('share_key_id',            Arr::get($values, 'share_key_id', ''))
            ->column('primary_consumer_key',    Arr::get($values, 'primary_consumer_key', ''))
            ->column('primary_context_id',      Arr::get($values, 'primary_context_id', ''))
            ->column('auto_approve',            Arr::get($values, 'auto_approve', NULL))
            ->column('expires',                 Arr::get($values, 'expires', ''));

        return $query->execute();
    }

    public function getAllRecords() {
        $builder = DB_SQL::select('default')->from($this->table());
        $result = $builder->query();
        if ($result->is_loaded()){
            $elements = array();
            foreach ($result as $record) $elements[] = DB_ORM::model('lti_sharekey', array($record['share_key_id']));
            return $elements;
        }
        return NULL;
    }

}