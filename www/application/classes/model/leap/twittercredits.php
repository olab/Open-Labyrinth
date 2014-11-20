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
 * Model for map_nodes table in database
 */
class Model_Leap_TwitterCredits extends DB_ORM_Model {

    public function __construct() {
        parent::__construct();

        $this->fields = array(
            'id' => new DB_ORM_Field_Integer($this, array(
                'nullable' => FALSE,
            )),
            'API_key' => new DB_ORM_Field_Text($this, array(
                'nullable' => FALSE,
            )),
            'API_secret' => new DB_ORM_Field_Text($this, array(
                'nullable' => FALSE,
            )),
            'Access_token' => new DB_ORM_Field_Text($this, array(
                'nullable' => FALSE,
            )),
            'Access_token_secret' => new DB_ORM_Field_Text($this, array(
                'nullable' => FALSE,
            )),
        );
    }

    public static function data_source() {
        return 'default';
    }

    public static function table() {
        return 'twitter_credits';
    }

    public static function primary_key() {
        return array('id');
    }

    public function add ($apiKey, $apiSecret, $accessToken, $accessTokenSecret)
    {
        $record = new $this;
        $record->API_key             = $apiKey;
        $record->API_secret          = $apiSecret;
        $record->Access_token        = $accessToken;
        $record->Access_token_secret = $accessTokenSecret;
        $record->save();
    }

    public function update ($id, $apiKey, $apiSecret, $accessToken, $accessTokenSecret)
    {
        $this->id = $id;
        $this->load();
        $this->API_key             = $apiKey;
        $this->API_secret          = $apiSecret;
        $this->Access_token        = $accessToken;
        $this->Access_token_secret = $accessTokenSecret;
        $this->save();
    }

    public function deleteRecord ($id)
    {
        $record = new $this;
        $record->id = $id;
        $record->delete();
    }
}