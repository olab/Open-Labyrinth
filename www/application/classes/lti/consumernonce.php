<?php
defined('SYSPATH') or die('No direct script access.');
/**
 * Class to represent a tool consumer nonce
 *
 * @author  Stephen P Vickers <stephen@spvsoftwareproducts.com>
 * @version 2.3.02
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3
 */
class Lti_ConsumerNonce {

    /**
     * Maximum age nonce values will be retained for (in minutes).
     */
    const MAX_NONCE_AGE = 30;  // in minutes

    /**
     * Date/time when the nonce value expires.
     */
    public  $expires = NULL;

    /**
     * Lti_Tool_Consumer object to which this nonce applies.
     */
    private $consumer = NULL;
    /**
     * Nonce value.
     */
    private $value = NULL;

    /**
     * Class constructor.
     *
     * @param Lti_ToolConsumer $consumer Consumer object
     * @param string            $value    Nonce value (optional, default is null)
     */
    public function __construct($consumer, $value = NULL) {

        $this->consumer = $consumer;
        $this->value = $value;
        $this->expires = time() + (self::MAX_NONCE_AGE * 60);

    }

    /**
     * Load a nonce value from the database.
     *
     * @return boolean True if the nonce value was successfully loaded
     */
    public function load() {

        return $this->consumer->getDataConnector()->Consumer_Nonce_load($this);

    }

    /**
     * Save a nonce value in the database.
     *
     * @return boolean True if the nonce value was successfully saved
     */
    public function save() {

        return $this->consumer->getDataConnector()->Consumer_Nonce_save($this);

    }

    /**
     * Get tool consumer.
     *
     * @return Lti_ToolConsumer Consumer for this nonce
     */
    public function getConsumer() {

        return $this->consumer;

    }

    /**
     * Get tool consumer key.
     *
     * @return string Consumer key value
     */
    public function getKey() {

        return $this->consumer->getKey();

    }

    /**
     * Get outcome value.
     *
     * @return string Outcome value
     */
    public function getValue() {

        return $this->value;

    }

}
