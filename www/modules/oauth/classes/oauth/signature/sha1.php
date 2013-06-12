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
    protected $name     = 'sha1';

    /**
     * HTTP Method (GET, POST, etc.)
     *
     * @var string
     */
    private $httpMethod = null;

    /**
     * URL address
     *
     * @var string
     */
    private $url        = null;

    /**
     * Main parameters for signature
     *
     * Array(
     *  'consumerKey',
     *  'consumerSecret',
     *  'nonce',
     *  'method',
     *  'timeStamp',
     *  'version',
     *  'verifier',
     *  'tokenSecret',
     *  'token'
     * )
     *
     * @var array
     */
    private $params     = null;

    /**
     * Default constructor
     *
     * @param string $method
     * @param string $url
     * @param array $params
     */
    public function __construct($method, $url, $params) {
        $this->httpMethod = $method;
        $this->url        = $url;
        $this->params     = $params;
    }

    /**
     * Return signature
     *
     * @return array with signature values Array('signature', 'timeStamp', 'nonce')
     */
    public function getSignature() {
        $params           = $this->getMainParams();
        $normalizedParams = OAuth::normalizeParams($params);

        $baseString       = $this->httpMethod . '&' . OAuth::urlencode($this->url) . '&' . OAuth::urlencode($normalizedParams);

        $key              = OAuth::urlencode(Arr::get($this->params, 'consumerSecret', '')) . '&';
        $key2             = Arr::get($this->params, 'tokenSecret', null);
        if($key2 != null && !empty($key2)) {
            $key .= OAuth::urlencode($key2);
        }

        return array('signature' => base64_encode(hash_hmac('sha1', $baseString, $key, true)),
                     'timeStamp' => $params['oauth_timestamp'],
                     'nonce'     => $params['oauth_nonce']);
    }

    /**
     * Return basic parameters for generating signature
     *
     * @return array
     */
    private function getMainParams() {
        $params = array (
            'oauth_consumer_key'     => Arr::get($this->params, 'consumerKey', ''         ),
            'oauth_nonce'            => Arr::get($this->params, 'nonce'      , md5(time())),
            'oauth_signature_method' => Arr::get($this->params, 'method'     , 'HMAC-SHA1'),
            'oauth_timestamp'        => Arr::get($this->params, 'timeStamp'  , time()     ),
            'oauth_version'          => Arr::get($this->params, 'version'    , '1.0'      )
        );

        $token = Arr::get($this->params, 'token', null);
        if($token != null) {
            $params['oauth_token'] = $token;
        }

        $verifier = Arr::get($this->params, 'verifier', null);
        if($verifier != null) {
            $params['oauth_verifier'] = $verifier;
        }

        return $params;
    }
}