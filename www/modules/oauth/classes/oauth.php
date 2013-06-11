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

class OAuth {
    /**
     * Return needed provider by name
     *
     * @param $provider
     * @return null|OAuth_Provider_Facebook|OAuth_Provider_Github|OAuth_Provider_Twitter
     */
    public static function factory($provider) {
        if($provider == null || $provider->appId == null || $provider->secret == null) return null;

        $oauthProvider = null;
        switch($provider->name) {
            case 'github':
                $oauthProvider = new OAuth_Provider_Github();
                break;
            case 'facebook':
                $oauthProvider = new OAuth_Provider_Facebook($provider->appId, $provider->secret);
                break;
            case 'twitter':
                $oauthProvider = new OAuth_Provider_Twitter($provider->appId, $provider->secret);
                break;
            case 'linkedin':
                $oauthProvider = new OAuth_Provider_Linkedin($provider->appId, $provider->secret);
                break;
            case 'google':
                $oauthProvider = new OAuth_Provider_Google($provider->appId, $provider->secret);
                break;
            case 'tumblr':
                $oauthProvider = new OAuth_Provider_Tumblr($provider->appId, $provider->secret);
                break;
        }

        return $oauthProvider;
    }

    /**
     * Decode url
     *
     * @param $input
     * @return array|string
     */
    public static function urldecode($input) {
        if(is_array($input)) {
            return array_map(array('OAuth', 'urldecode'), $input);
        }

        return rawurldecode($input);
    }

    /**
     * Encode url
     *
     * @param $input
     * @return string
     */
    public static function urlencode($input) {
        if (is_array($input)) {
            return array_map(array('OAuth', 'urlencode'), $input);
        }

        $input = rawurlencode($input);
        if (version_compare(PHP_VERSION, '<', '5.3')) {
            $input = str_replace('%7E', '~', $input);
        }

        return $input;
    }

    /**
     * Parse values from string
     *
     * @param string $stringParams
     * @return array (key => value)
     */
    public static function parseStringParams($stringParams) {
        $result = array();

        if($stringParams == null || empty($stringParams)) return $result;

        $params = explode('&', $stringParams);
        if($params != null && count($params) > 0) {
            foreach($params as $param) {
                list($name, $value) = explode('=', $param);

                $name  = OAuth::urldecode($name);
                $value = OAuth::urldecode($value);

                if (isset($result[$name])) {
                    if ( ! is_array($result[$name])) {
                        $result[$name] = array($result[$name]);
                    }

                    $result[$name][] = $value;
                } else {
                    $result[$name] = $value;
                }
            }
        }

        return $result;
    }

    /**
     * Normalize parameters
     *
     * @param array $params
     * @return string
     */
    public static function normalizeParams(array $params = NULL) {
        if (!$params) return '';

        $keys   = OAuth::urlencode(array_keys($params));
        $values = OAuth::urlencode(array_values($params));
        $params = array_combine($keys, $values);
        uksort($params, 'strcmp');

        $query = array();
        foreach ($params as $name => $value) {
            if (is_array($value)) {
                $value = natsort($value);
                foreach ($value as $duplicate) {
                    $query[] = $name.'='.$duplicate;
                }
            } else {
                $query[] = $name.'='.$value;
            }
        }

        return implode('&', $query);
    }

}