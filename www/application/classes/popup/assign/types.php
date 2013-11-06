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

class Popup_Assign_Types {
    const LABYRINTH = 1;
    const NODE      = 2;
    const SECTION   = 3;

    /**
     * Convert assign type to string
     *
     * @param {integer} $assignType - assign type ID
     * @return null|string - assign type string equivalent
     */
    public static function toString($assignType) {
        $result = null;

        switch($assignType) {
            case Popup_Assign_Types::LABYRINTH:
                $result = 'labyrinth';
                break;
            case Popup_Assign_Types::NODE:
                $result = 'node';
                break;
            case Popup_Assign_Types::SECTION:
                $result = 'section';
                break;
        }

        return $result;
    }
}