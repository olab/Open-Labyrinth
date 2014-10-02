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
 * Model for lti_nonces in database
 */
class Model_Leap_Lti_Nonce extends DB_ORM_Model {
    public function __construct() {
        parent::__construct();

        $this->fields = array(
            'consumer_key' => new DB_ORM_Field_String($this, array(
                    'max_length' => 255,
                    'nullable' => FALSE,
                    'savable' => TRUE,
                )),
            'value' => new DB_ORM_Field_String($this, array(
                    'max_length' => 32,
                    'nullable' => FALSE,
                    'savable' => TRUE,
                )),
            'expires' => new DB_ORM_Field_DateTime($this, array(
                    'savable' => TRUE,
                    'nullable' => FALSE,
                )),

        );
        $this->relations = array(
            'nonce' => new DB_ORM_Relation_BelongsTo($this, array(
                    'child_key' => array('consumer_key'),
                    'parent_key' => array('consumer_key'),
                    'parent_model' => 'lti_consumer',
                )),
        );
    }
    public static function data_source() {
        return 'default';
    }

    public static function table() {
        return 'lti_nonces';
    }

    public static function primary_key() {
        return array('value');
    }
    public function saveNonce($values) {
        return DB_ORM::insert('lti_Nonce')
            ->column('consumer_key',        Arr::get($values, 'consumer_key',''))
            ->column('value',               Arr::get($values, 'value',''))
            ->column('expires',             Arr::get($values, 'expires', ''))
            ->execute();
    }

    public function getAllRecords() {
        $builder = DB_SQL::select('default')->from($this->table());
        $result = $builder->query();
        if ($result->is_loaded()){
            $elements = array();
            foreach ($result as $record) $elements[] = DB_ORM::model('lti_nonce', array($record['value']));
            return $elements;
        }
        return NULL;
    }

    public function getByKeyId($key, $value){
        $builder = DB_SQL::select('default')->from($this->table())->where('consumer_key', '=', $key, 'AND')->where('value', '=', $value);
        $result = $builder->query();
        if($result->is_loaded()) {
            return $result;
        }
        return NULL;
    }
}