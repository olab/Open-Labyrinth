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
 * Class OAuth_Signature - OAuth signature
 */
abstract class OAuth_Signature {
    protected $name = null;

    /**
     * Return signature builder
     *
     * @param string $name
     * @param array $params
     * @return OAuth_Signature
     */
    public static function factory($name, $method, $url, array $params = null) {
        $signature = null;
        switch($name) {
            case 'sha1':
                $signature = new OAuth_Signature_SHA1($method, $url, $params);
                break;
        }

        return $signature;
    }

    /**
     * Return signature
     *
     * @return string
     */
    abstract public function getSignature();
}