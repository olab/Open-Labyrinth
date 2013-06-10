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
 * Class OAuth_Provider_Facebook - Facebook OAuth provider
 */
class OAuth_Provider_Facebook extends OAuth_Provider {
    /**
     * Name of provider
     *
     * @var string
     */
    protected $name = 'facebook';

    private $DOMAIN_MAP = array(
        'authorize' => 'https://www.facebook.com/dialog/oauth',
        'access'    => 'https://graph.facebook.com/oauth/access_token',
        'api'       => 'https://graph.facebook.com'
    );

    private $id     = null;
    private $secret = null;

    /**
     * Default constructor
     */
    public function __construct() {
        $config = Kohana::$config->load('oauth');

        if(!property_exists($config, 'facebook')) {
            throw new Kohana_Exception('Can\'t load configuration for Facebook provider');
        }


        if(!isset($config->facebook['id'])) {
            throw new Kohana_Exception('Please set in configuration file Facebook client id');
        }

        if(!isset($config->facebook['secret'])) {
            throw new Kohana_Exception('Please set in configuration file Facebook secret');
        }

        $this->id     = $config->facebook['id'];
        $this->secret = $config->facebook['secret'];
    }

    /**
     * Return authorize URL for provider
     *
     * @param string $redirectURL
     * @return string
     */
    public function getAuthorizeURL($redirectURL = null) {
        return $this->DOMAIN_MAP['authorize'] . '?' . http_build_query(array(
            'client_id'    => $this->id,
            'redirect_uri' => $redirectURL
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
            'redirect_uri'  => $redirectURL,
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

    /**
     * Return user info
     *
     * @param OAuth_Token $token
     * @return mixed|null
     */
    private function getUserInfo(OAuth_Token $token) {
        if($token == null) return null;

        $params = array(
            'access_token' => $token->getToken()
        );

        $request = new OAuth_Request('GET', $this->DOMAIN_MAP['api'] . '/me', $params);

        return $request->execute();
    }

    /**
     * Parse access token from string
     *
     * @param $response
     * @return null|OAuth_Token
     */
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