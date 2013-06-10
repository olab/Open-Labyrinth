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
 * Class OAuth_Token - OAuth token
 */
class OAuth_Token {
    /**
     * Current token
     *
     * @var string
     */
    private $token = null;

    /**
     * Token secret
     *
     * @var string
     */
    private $secret = null;

    /**
     * Default constructor
     *
     * @param null|string $token
     */
    public function __construct($token = null, $tokenSecret = null) {
        $this->token  = $token;
        $this->secret = $tokenSecret;
    }

    /**
     * Return current token
     *
     * @return string
     */
    public function getToken() {
        return $this->token;
    }

    /**
     * Get token secret
     *
     * @return null
     */
    public function getSecret() {
        return $this->secret;
    }

    /**
     * Set current token
     *
     * @param string $token
     */
    public function setToken($token) {
        $this->token = $token;
    }
}