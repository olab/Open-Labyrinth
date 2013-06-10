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
 * Class OAuth_Provider_Twitter - Twitter OAuth provider
 */
class OAuth_Provider_Twitter extends OAuth_Provider {
    /**
     * Name of provider
     *
     * @var string
     */
    protected $name = 'twitter';

    private $DOMAIN_MAP = array(
        'request'   => 'https://api.twitter.com/oauth/request_token',
        'authorize' => 'https://api.twitter.com/oauth/authenticate',
        'access'    => 'https://api.twitter.com/oauth/access_token',
        'api'       => 'https://api.twitter.com/1'
    );

    private $id     = null;
    private $secret = null;

    /**
     * Default constructor
     */
    public function __construct() {
        $config = Kohana::$config->load('oauth');

        if(!property_exists($config, 'twitter')) {
            throw new Kohana_Exception('Can\'t load configuration for Twitter provider');
        }


        if(!isset($config->twitter['id'])) {
            throw new Kohana_Exception('Please set in configuration file Twitter consumer key');
        }

        if(!isset($config->twitter['secret'])) {
            throw new Kohana_Exception('Please set in configuration file Twitter secret');
        }

        $this->id     = $config->twitter['id'];
        $this->secret = $config->twitter['secret'];
    }

    /**
     * Return authorize URL for provider
     *
     * @param string $redirectURL
     * @return string
     */
    public function getAuthorizeURL($redirectURL = null) {
        $signature = OAuth_Signature::factory('sha1', array(
            'consumerKey'            => $this->id,
            'secret'                 => $this->secret,
            'url'                    => $this->DOMAIN_MAP['request'],
            'method'                 => 'GET'
        ));

        $params = array(
            'oauth_consumer_key'     => $this->id,
            'oauth_nonce'            => $signature->getNonce(),
            'oauth_timestamp'        => $signature->getTimeStamp(),
            'oauth_version'          => $signature->getVersion(),
            'oauth_signature_method' => $signature->getSignatureMethod()
        );

        $stringParams       = http_build_query($params);
        $stringParams      .= '&oauth_signature=' . $signature->getSignature();

        $requestToken       = new OAuth_Request('GET', $this->DOMAIN_MAP['request'], $stringParams);
        $response           = $requestToken->execute();

        $requestTokenParams = OAuth::parseStringParams($response);

        $authorizeURL = URL::base();
        if(isset($requestTokenParams['oauth_token'])) {
            $authorizeURL = $this->DOMAIN_MAP['authorize'] . '?' . http_build_query(array('oauth_token' => $requestTokenParams['oauth_token']));
        }

        return $authorizeURL;
    }

    /**
     * Return OAuth access token
     *
     * @param string $request
     * @return OAuth_Token
     */
    public function getAccessToken($request = null, $redirectURL = null) {
        $oauthToken    = Arr::get($request, 'oauth_token', null);
        $oauthVerifier = Arr::get($request, 'oauth_verifier', null);
        if($oauthToken == null || $oauthVerifier == null) return null;

        $signature = OAuth_Signature::factory('sha1', array(
            'consumerKey'            => $this->id,
            'secret'                 => $this->secret,
            'url'                    => $this->DOMAIN_MAP['request'],
            'method'                 => 'GET'
        ));

        $params = array(
            'oauth_consumer_key'     => $this->id,
            'oauth_nonce'            => $signature->getNonce(),
            'oauth_timestamp'        => $signature->getTimeStamp(),
            'oauth_version'          => $signature->getVersion(),
            'oauth_signature_method' => $signature->getSignatureMethod()
        );

        $stringParams       = http_build_query($params);
        $stringParams      .= '&oauth_signature=' . $signature->getSignature();
        $stringParams      .= '&oauth_token='     . $oauthToken;
        $stringParams      .= '&oauth_verifier='  . $oauthVerifier;

        $accessTokenRequest = new OAuth_Request('POST', $this->DOMAIN_MAP['access'], $stringParams);
        $response           = $accessTokenRequest->execute();

        $accessTokenParams  = OAuth::parseStringParams($response);

        $accessToken        = null;
        if(isset($accessTokenParams['oauth_token'])) {
            $accessToken = new OAuth_Token($accessTokenParams['oauth_token'], $accessTokenParams['oauth_token_secret']);
        }

        return $accessToken;
    }

    public function get(OAuth_Token $token, $name, $params = null) {
        if($token == null || $name == null) return null;

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

        $signature = OAuth_Signature::factory('sha1', array(
            'consumerKey'            => $this->id,
            'secret'                 => $this->secret,
            'url'                    => $this->DOMAIN_MAP['api'] . '/account/verify_credentials.json',
            'method'                 => 'GET',
            'token'                  => $token
        ));

        $params = array(
            'oauth_consumer_key'     => $this->id,
            'oauth_nonce'            => $signature->getNonce(),
            'oauth_timestamp'        => $signature->getTimeStamp(),
            'oauth_version'          => $signature->getVersion(),
            'oauth_signature_method' => $signature->getSignatureMethod()
        );

        $stringParams  = http_build_query($params);
        $stringParams .= '&oauth_signature=' . $signature->getSignature();
        $stringParams .= '&oauth_token='     . $token->getToken();

        $request       = new OAuth_Request('GET', $this->DOMAIN_MAP['api'] . '/account/verify_credentials.json', $stringParams);

        return $request->execute();
    }
}