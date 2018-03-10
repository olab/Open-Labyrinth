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

class Notice {
    private static $session;
    private static $initialized = false;

    // current notices
    private static $notices = array();

    function __construct() {
    }

    static function init() {
        self::$session = new Session();
        self::$notices['current'] = json_decode(self::$session->get_once('flash'));
        if(!is_array(self::$notices['current'])) self::$notices['current'] = array();
        self::$initialized = true;
    }

    static function add($notice, $key=null) {
        if(!self::$initialized) self::init();
        if(!is_null($key)) {
            self::$notices['new'][$key] = $notice;
        } else {
            self::$notices['new'][] = $notice;
        }
        self::$session->set('flash', json_encode(self::$notices['new']));
        return true;
    }

    static function get($item = null) {
        if(!self::$initialized) self::init();
        if($item == null) {
            return self::$notices['current'];
        }
        if(!array_key_exists($item, self::$notices['current']))
                return null;
        return self::$notices['current'][$item];
    }
}
?>