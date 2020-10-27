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
 * Model for oauth providers table in database
 */
class Model_Leap_OAuthProvider extends DB_ORM_Model {

    public function __construct() {
        parent::__construct();

        $this->fields = array(
            'id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
                'unsigned' => FALSE,
            )),
            'name' => new DB_ORM_Field_String($this, array(
                'max_length' => 250,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            'version' => new DB_ORM_Field_String($this, array(
                'max_length' => 200,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            'icon' => new DB_ORM_Field_String($this, array(
                'max_length' => 255,
                'nullable' => TRUE,
                'savable' => TRUE,
            )),
            'appId' => new DB_ORM_Field_String($this, array(
                'max_length' => 300,
                'nullable' => TRUE,
                'savable' => TRUE,
            )),
            'secret' => new DB_ORM_Field_String($this, array(
                'max_length' => 300,
                'nullable' => TRUE,
                'savable' => TRUE,
            )),
        );
    }

    public static function data_source() {
        return 'default';
    }

    public static function table() {
        return 'oauth_providers';
    }

    public static function primary_key() {
        return array('id');
    }

    /**
     * Return all providers for OAuth module
     *
     * @return array|null
     */
    public function getAll() {
        $builder   = DB_SQL::select('default')->column('id')->from($this->table());
        $query     = $builder->query();

        $providers = null;
        if($query->is_loaded()) {
            $providers = array();
            foreach($query as $record) {
                $providers[] = DB_ORM::model('OAuthProvider', array((int)$record['id']));
            }
        }

        return $providers;
    }

    /**
     * Return provider by name
     *
     * @param string $name
     * @return mixed|null
     */
    public function getByName($name) {
        $builder   = DB_SQL::select('default')->column('id')->from($this->table())->where('name', '=', $name)->limit(1);
        $query     = $builder->query();

        $provider = null;
        if($query->is_loaded()) {
            $provider = DB_ORM::model('OAuthProvider', array((int)$query[0]['id']));
        }

        return $provider;
    }

    public function updateData($id, $appId, $secret) {
        DB_ORM::update('OAuthProvider')->set('appId', $appId)->set('secret', $secret)->where('id', '=', $id)->execute();
    }
}