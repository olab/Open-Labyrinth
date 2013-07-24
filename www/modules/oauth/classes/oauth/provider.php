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
 * Abstract Class OAuth_Provider. Provide access to some OAuth system
 */
abstract class OAuth_Provider {
    /**
     * Name of provider
     *
     * @var string
     */
    protected $name = null;

    /**
     * Return name of provider
     *
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Return authorize URL for provider
     *
     * @param string $redirectURL
     * @return string
     */
    abstract public function getAuthorizeURL($redirectURL = null);

    /**
     * Return OAuth access token
     *
     * @param string $request
     * @return OAuth_Token
     */
    abstract public function getAccessToken($request = null, $redirectURL = null);

    /**
     * Get information
     *
     * @param OAuth_Token $token
     * @param $name
     * @param null $params
     * @return mixed
     */
    abstract public function get(OAuth_Token $token, $name, $params = null);
}