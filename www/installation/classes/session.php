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

session_start();
class Session
{
    public static function set($key, $value){
        $_SESSION[$key] = $value;
    }

    public static function get($key, $default = NULL) {
        if(isset($_SESSION[$key]) && !empty($_SESSION[$key]))
            return $_SESSION[$key];
        else
            return $default;
    }

    public static function get_once($key, $default = NULL){
        $value = Session::get($key, $default);
        unset($_SESSION[$key]);
        return $value;
    }

}

