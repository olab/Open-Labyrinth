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
 * Class OAuth_Request - OAuth request
 */
class OAuth_Request {
    /**
     * Request method (POST, GET, etc.)
     *
     * @var string
     */
    protected $method      = 'GET';

    /**
     * Request url
     *
     * @var string
     */
    protected $url         = null;

    /**
     * List of parameters
     *
     * @var array
     */
    protected $params      = null;

    /**
     * CURL options
     *
     * @var array
     */
    protected $curlOptions = array(
        CURLOPT_USERAGENT      => 'OAuth-module-v-1-0',
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 60,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_SSL_VERIFYPEER => false
    );

    /**
     * Return request method
     *
     * @return string
     */
    public function getMethod() {
        return $this->method;
    }

    /**
     * Set request method
     *
     * @param string $method
     */
    public function setMethod($method) {
        $this->method = strtoupper($method);
    }

    /**
     * Return url address
     *
     * @return string
     */
    public function getURL() {
        return $this->url;
    }

    /**
     * Set url address
     *
     * @param string $url
     */
    public function setURL($url) {
        $this->url = $url;
    }

    /**
     * Return parameters
     *
     * @return array
     */
    public function getParams() {
        return $this->params;
    }

    /**
     * Set new parameters
     *
     * @param array|string $params
     */
    public function setParam($params) {
        $this->params = $params;
    }

    /**
     * Return all curl options
     *
     * @return array
     */
    public function getCurlOptions() {
        return $this->curlOptions;
    }

    /**
     * Set new curl options
     *
     * @param array $options
     */
    public function setCurlOptions(array $options) {
        $this->curlOptions = $options;
    }

    /**
     * Set curl option
     *
     * @param CURL_OPTION_CONSTANT $option
     * @param mixed $value
     */
    public function setCurlOption($option, $value) {
        $this->curlOptions[$option] = $value;
    }

    /**
     * Default constructor
     *
     * @param string $method - method of the request
     * @param string $url - url address for request
     * @param array|string $params - request parameters
     */
    public function __construct($method, $url, $params = null) {
        if($method) {
            $this->setMethod($method);
        }

        $this->setURL($url);
        $this->setParam($params);
    }

    /**
     * Execute current request
     *
     * @return mixed
     */
    public function execute() {
        $paramsQuery = $this->params;
        if($this->params != null && is_array($this->params)) {
            $paramsQuery = http_build_query($this->params);
        }

        $url = $this->url;
        if($this->method == 'POST') {
            $this->setCurlOption(CURLOPT_POST, true);
            $this->setCurlOption(CURLOPT_POSTFIELDS, $paramsQuery);
        } else if($paramsQuery != '') {
            $url .= '?' . $paramsQuery;
        }

        $handle = curl_init($url);
        curl_setopt_array($handle, $this->curlOptions);

        $response = curl_exec($handle);
        curl_close($handle);

        return $response;
    }
}