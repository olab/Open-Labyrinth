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
 * Class OAuth_Signature_SHA1 - OAuth signature SHA1
 */
class OAuth_Signature_SHA1 extends OAuth_Signature {
    protected $name            = 'sha1';
    protected $version         = '1.0';
    protected $nonce           = null;
    protected $timeStamp       = null;
    protected $signatureMethod = 'HMAC-SHA1';

    private $consumerKey       = null;
    private $secret            = null;
    private $url               = null;
    private $method            = null;
    private $token             = null;

    /**
     * Default constructor
     *
     * @param string $consumerKey
     * @param string $secret
     * @param string $url
     * @param string $method
     * @param string $token
     */
    public function __construct($consumerKey, $secret, $url, $method, $token = null) {
        $this->nonce       = time();
        $this->timeStamp   = time();

        $this->consumerKey = $consumerKey;
        $this->secret      = $secret;
        $this->url         = $url;
        $this->method      = $method;
        $this->token       = $token;
    }

    /**
     * Return signature
     *
     * @return string
     */
    public function getSignature() {
        $secret = $this->secret . '&';

        if($this->token != null) {
            $secret .= $this->token->getSecret();
        }

        $params = array(
            'oauth_version'          => $this->version,
            'oauth_nonce'            => $this->nonce,
            'oauth_timestamp'        => $this->timeStamp,
            'oauth_consumer_key'     => $this->consumerKey,
            'oauth_signature_method' => 'HMAC-SHA1'
        );

        if($this->token != null) {
            $params['oauth_token'] = $this->token->getToken();
        }

        $normalizedParams = OAuth::normalizeParams($params);
        $baseString       = $this->method . '&' . OAuth::urlencode($this->url) . '&' . OAuth::urlencode($normalizedParams);

        return OAuth::urlencode(base64_encode(hash_hmac('sha1', $baseString, $secret, true)));
    }
}