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
 * Class OAuth_Signature - OAuth signature
 */
abstract class OAuth_Signature {
    protected $name = null;

    /**
     * Version
     *
     * @var string
     */
    protected $version         = null;

    /**
     * Nonce
     *
     * @var string
     */
    protected $nonce           = null;

    /**
     * Time stamp
     *
     * @var string
     */
    protected $timeStamp       = null;

    /**
     * Signature method
     *
     * @var string
     */
    protected $signatureMethod = null;

    /**
     * Get nonce
     *
     * @return string
     */
    public function getNonce() {
        return $this->nonce;
    }

    /**
     * Get time stamp
     *
     * @return string
     */
    public function getTimeStamp() {
        return $this->timeStamp;
    }

    /**
     * Get version
     *
     * @return string
     */
    public function getVersion() {
        return $this->version;
    }

    /**
     * Get signature method
     *
     * @return string
     */
    public function getSignatureMethod() {
        return $this->signatureMethod;
    }

    /**
     * Return signature builder
     *
     * @param string $name
     * @param array $params
     * @return OAuth_Signature
     */
    public static function factory($name, array $params = null) {
        $signature = null;
        switch($name) {
            case 'sha1':
                $signature = new OAuth_Signature_SHA1(Arr::get($params, 'consumerKey', ''),
                                                      Arr::get($params, 'secret', ''),
                                                      Arr::get($params, 'url', ''),
                                                      Arr::get($params, 'method', 'GET'),
                                                      Arr::get($params, 'token', null));
                break;
        }

        return $signature;
    }

    /**
     * Return signature
     *
     * @return string
     */
    abstract public function getSignature();
}