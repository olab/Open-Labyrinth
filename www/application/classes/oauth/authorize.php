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
 * Class OAuth_Authorize - OAuth authorize
 */
abstract class OAuth_Authorize {
    protected $name = null;

    /**
     * Create authorize system by name
     *
     * @param $name
     * @return null|OAuth_Authorize_Facebook|OAuth_Authorize_Github|OAuth_Authorize_Twitter
     */
    public static function factory($name) {
        $oauthAuthorize = null;
        switch($name) {
            case 'github':
                $oauthAuthorize = new OAuth_Authorize_Github();
                break;
            case 'facebook':
                $oauthAuthorize = new OAuth_Authorize_Facebook();
                break;
            case 'twitter':
                $oauthAuthorize = new OAuth_Authorize_Twitter();
                break;
        }

        return $oauthAuthorize;
    }

    /**
     * Return authorize system name
     *
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Login
     *
     * @param $providerId
     * @param $info
     * @return mixed
     */
    abstract public function login($providerId, $info);
}