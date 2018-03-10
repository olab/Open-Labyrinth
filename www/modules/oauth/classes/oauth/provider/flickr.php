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
 * Class OAuth_Provider_Flickr - Flickr OAuth provider
 */
class OAuth_Provider_Flickr extends OAuth_Provider {
    /**
     * Name of provider
     *
     * @var string
     */
    protected $name = 'flickr';

    const FLICKR_REQUEST_TOKEN_SECRET = 'flickr_request_token_secret';
    const FLICKR_USER_INFO            = 'flickr_user_info';

    private $DOMAIN_MAP = array(
        'request'   => 'http://www.flickr.com/services/oauth/request_token',
        'authorize' => 'http://www.flickr.com/services/oauth/authorize',
        'access'    => 'http://www.flickr.com/services/oauth/access_token',
        'api'       => 'http://www.flickr.com/services/rest'
    );

    /**
     * Flickr appId
     *
     * @var string
     */
    private $id     = null;

    /**
     * Flickr secret key
     *
     * @var string
     */
    private $secret = null;

    /**
     * Default constructor
     */
    public function __construct($appId, $secret) {
        $this->id     = $appId;
        $this->secret = $secret;
    }

    /**
     * Return authorize URL for provider
     *
     * @param string $redirectURL
     * @return string
     */
    public function getAuthorizeURL($redirectURL = null) {
        $signature = OAuth_Signature::factory('sha1', 'POST', $this->DOMAIN_MAP['request'], array(
            'consumerKey'    => $this->id,
            'consumerSecret' => $this->secret
        ));

        $signatureData = $signature->getSignature();

        $params = array(
            'oauth_consumer_key'     => $this->id,
            'oauth_nonce'            => $signatureData['nonce'],
            'oauth_timestamp'        => $signatureData['timeStamp'],
            'oauth_version'          => '1.0',
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_signature'        => $signatureData['signature']
        );

        $requestToken       = new OAuth_Request('POST', $this->DOMAIN_MAP['request'], $params);
        $response           = $requestToken->execute();

        $requestTokenParams = OAuth::parseStringParams($response);

        $authorizeURL = URL::base();
        if(isset($requestTokenParams['oauth_token'])) {
            $authorizeURL = $this->DOMAIN_MAP['authorize'] . '?' . http_build_query(array('oauth_token' => $requestTokenParams['oauth_token'], 'perms' => 'read'));

            Session::instance()->set(OAuth_Provider_Flickr::FLICKR_REQUEST_TOKEN_SECRET, $requestTokenParams['oauth_token_secret']);
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

        $signature = OAuth_Signature::factory('sha1', 'POST', $this->DOMAIN_MAP['access'], array(
            'consumerKey'    => $this->id,
            'consumerSecret' => $this->secret,
            'verifier'       => $oauthVerifier,
            'token'          => $oauthToken,
            'tokenSecret'    => Session::instance()->get(OAuth_Provider_Flickr::FLICKR_REQUEST_TOKEN_SECRET, null)
        ));

        $signatureData = $signature->getSignature();

        $params = array(
            'oauth_consumer_key'     => $this->id,
            'oauth_nonce'            => $signatureData['nonce'],
            'oauth_timestamp'        => $signatureData['timeStamp'],
            'oauth_version'          => '1.0',
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_signature'        => $signatureData['signature'],
            'oauth_token'            => $oauthToken,
            'oauth_verifier'         => $oauthVerifier
        );

        $accessTokenRequest = new OAuth_Request('POST', $this->DOMAIN_MAP['access'], $params);
        $response           = $accessTokenRequest->execute();

        $accessTokenParams  = OAuth::parseStringParams($response);

        $accessToken        = null;
        if(isset($accessTokenParams['oauth_token'])) {
            $accessToken = new OAuth_Token($accessTokenParams['oauth_token'], $accessTokenParams['oauth_token_secret']);
            Session::instance()->set(OAuth_Provider_Flickr::FLICKR_USER_INFO, json_encode($accessTokenParams));
        }

        return $accessToken;
    }

    /**
     * Get information from oauth system
     *
     * @param OAuth_Token $token
     * @param string $name
     * @param array $params
     * @return mixed|null
     */
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

    /**
     * Return basic user info
     *
     * @param OAuth_Token $token
     * @return mixed
     */
    private function getUserInfo(OAuth_Token $token) {
        return Session::instance()->get(OAuth_Provider_Flickr::FLICKR_USER_INFO);
    }
}