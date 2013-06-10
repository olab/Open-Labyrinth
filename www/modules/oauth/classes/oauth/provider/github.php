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
 * Class OAuth_Provider_Github - Github OAuth provider
 */
class OAuth_Provider_Github extends OAuth_Provider {
    /**
     * Name of provider
     *
     * @var string
     */
    protected $name = 'github';

    private $DOMAIN_MAP = array(
        'authorize' => 'https://github.com/login/oauth/authorize',
        'access'    => 'https://github.com/login/oauth/access_token',
        'api'       => 'https://api.github.com'
    );

    private $id     = null;
    private $secret = null;

    /**
     * Default constructor
     */
    public function __construct() {
        $config = Kohana::$config->load('oauth');

        if(!property_exists($config, 'github')) {
            throw new Kohana_Exception('Can\'t load configuration for Github provider');
        }


        if(!isset($config->github['id'])) {
            throw new Kohana_Exception('Please set in configuration file Github client id');
        }

        if(!isset($config->github['secret'])) {
            throw new Kohana_Exception('Please set in configuration file Github secret');
        }

        $this->id     = $config->github['id'];
        $this->secret = $config->github['secret'];
    }

    /**
     * Return authorize URL for provider
     *
     * @param string $redirectURL
     * @return string
     */
    public function getAuthorizeURL($redirectURL = null) {
        return $this->DOMAIN_MAP['authorize'] . '?' . http_build_query(array(
                                                                           'response_type' => 'code',
                                                                           'client_id'     => $this->id,
                                                                           'redirect_uri'  => $redirectURL
                                                                       ));
    }

    /**
     * Return OAuth access token
     *
     * @param string $request
     * @return OAuth_Token
     */
    public function getAccessToken($request = null, $redirectURL = null) {
        $code = Arr::get($request, 'code', null);
        if($code == null) return null;

        $request = new OAuth_Request('POST', $this->DOMAIN_MAP['access'], array(
                                        'grant_type'    => 'authorization_code',
                                        'code'          => $code,
                                        'client_id'     => $this->id,
                                        'client_secret' => $this->secret
                                    ));

        $response = $request->execute();

        return $this->parseAccessToken($response);
    }

    /**
     * Get information
     *
     * @param OAuth_Token $token
     * @param $name
     * @param null $params
     * @return mixed
     */
    public function get(OAuth_Token $token, $name, $params = null) {
        if($token == null) return null;

        $result = null;
        switch($name) {
            case 'user-info':
                $result = $this->getUserInfo($token);
                break;
        }

        return $result;
    }

    private function getUserInfo(OAuth_Token $token) {
        if($token == null) return null;

        $params = array(
            'access_token' => $token->getToken()
        );

        $request = new OAuth_Request('GET', $this->DOMAIN_MAP['api'] . '/user', $params);

        return $request->execute();
    }

    private function parseAccessToken($response) {
        $result = null;
        if($response == null) return $result;

        $params = OAuth::parseStringParams($response);
        if(isset($params['access_token'])) {
            $result = new OAuth_Token($params['access_token']);
        }

        return $result;
    }
}