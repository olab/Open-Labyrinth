<?php
defined('SYSPATH') or die('No direct script access.');
/**
 * OAuth libaray file
 */
require_once('oauth.php');

/**
 * Class to represent an OAuth datastore
 *
 * @author  Stephen P Vickers <stephen@spvsoftwareproducts.com>
 * @version 2.3.02
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3
 */
class Lti_OAuthDataStore extends OAuthDataStore {

    /**
     * Lti_ToolProvider object.
     */
    private $tool_provider = NULL;

    /**
     * Class constructor.
     *
     * @param Lti_ToolProvider $tool_provider ToolProvider object
     */
    public function __construct($tool_provider) {

        $this->tool_provider = $tool_provider;

    }

    /**
     * Create an OAuthConsumer object for the tool consumer.
     *
     * @param string $consumer_key Consumer key value
     *
     * @return OAuthConsumer OAuthConsumer object
     */
    function lookup_consumer($consumer_key) {

        return new OAuthConsumer($this->tool_provider->consumer->getKey(),
            $this->tool_provider->consumer->secret);

    }

    /**
     * Create an OAuthToken object for the tool consumer.
     *
     * @param string $consumer   OAuthConsumer object
     * @param string $token_type Token type
     * @param string $token      Token value
     *
     * @return OAuthToken OAuthToken object
     */
    function lookup_token($consumer, $token_type, $token) {

        return new OAuthToken($consumer, "");

    }

    /**
     * Lookup nonce value for the tool consumer.
     *
     * @param OAuthConsumer $consumer  OAuthConsumer object
     * @param string        $token     Token value
     * @param string        $value     Nonce value
     * @param string        $timestamp Date/time of request
     *
     * @return boolean True if the nonce value already exists
     */
    function lookup_nonce($consumer, $token, $value, $timestamp) {

        $nonce = new Lti_ConsumerNonce($this->tool_provider->consumer, $value);
        $ok = !$nonce->load();
        if ($ok) {
            $ok = $nonce->save();
        }
        if (!$ok) {
            $this->tool_provider->reason = 'Invalid nonce.';
        }

        return !$ok;

    }

    /**
     * Get new request token.
     *
     * @param OAuthConsumer $consumer  OAuthConsumer object
     * @param string        $callback  Callback URL
     *
     * @return string Null value
     */
    function new_request_token($consumer, $callback = NULL) {

        return NULL;

    }

    /**
     * Get new access token.
     *
     * @param string        $token     Token value
     * @param OAuthConsumer $consumer  OAuthConsumer object
     * @param string        $verifier  Verification code
     *
     * @return string Null value
     */
    function new_access_token($token, $consumer, $verifier = NULL) {

        return NULL;

    }

}