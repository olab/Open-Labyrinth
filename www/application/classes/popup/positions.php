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

class Popup_Positions {
    const TOP_LEFT     = 1;
    const TOP_RIGHT    = 2;
    const BOTTOM_LEFT  = 3;
    const BOTTOM_RIGHT = 4;

    /**
     * Convert position to string
     *
     * @param {integer} $position - position ID
     * @return null|string - position string equivalent
     */
    public static function toString($position) {
        $result = null;

        switch($position) {
            case Popup_Positions::TOP_LEFT:
                $result = 'top left';
                break;
            case Popup_Positions::TOP_RIGHT:
                $result = 'top right';
                break;
            case Popup_Positions::BOTTOM_LEFT:
                $result = 'bottom left';
                break;
            case Popup_Positions::BOTTOM_RIGHT:
                $result = 'botoom right';
                break;
        }

        return $result;
    }
}