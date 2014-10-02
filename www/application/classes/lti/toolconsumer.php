<?php
defined('SYSPATH') or die('No direct script access.');
/**
 * Class to represent a tool consumer
 *
 * @author  Stephen P Vickers <stephen@spvsoftwareproducts.com>
 * @version 2.3.02
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3
 */
class Lti_ToolConsumer {

    /**
     * Local name of tool consumer.
     */
    public $name = NULL;
    /**
     * Shared secret.
     */
    public $secret = NULL;
    /**
     * LTI version (as reported by last tool consumer connection).
     */
    public $lti_version = NULL;
    /**
     * Name of tool consumer (as reported by last tool consumer connection).
     */
    public $consumer_name = NULL;
    /**
     * Tool consumer version (as reported by last tool consumer connection).
     */
    public $consumer_version = NULL;
    /**
     * Tool consumer GUID (as reported by first tool consumer connection).
     */
    public $consumer_guid = NULL;
    /**
     * Optional CSS path (as reported by last tool consumer connection).
     */
    public $css_path = NULL;
    /**
     * True if the tool consumer instance is protected by matching the consumer_guid value in incoming requests.
     */
    public $protected = FALSE;
    /**
     * True if the tool consumer instance is enabled to accept incoming connection requests.
     */
    public $enabled = FALSE;
    /**
     * Date/time from which the the tool consumer instance is enabled to accept incoming connection requests.
     */
    public $enable_from = NULL;
    /**
     * Date/time until which the tool consumer instance is enabled to accept incoming connection requests.
     */
    public $enable_until = NULL;

    public $without_end_date = false;
    /**
     * Date of last connection from this tool consumer.
     */
    public $last_access = NULL;
    /**
     * Default scope to use when generating an Id value for a user.
     */
    public $id_scope = Lti_ToolProvider::ID_SCOPE_ID_ONLY;
    /**
     * Default email address (or email domain) to use when no email address is provided for a user.
     */
    public $defaultEmail = '';
    /**
     * Date/time when the object was created.
     */
    public $created = NULL;
    /**
     * Date/time when the object was last updated.
     */
    public $updated = NULL;
    public $role = 1;
    /**
     * Consumer key value.
     */
    private $key = NULL;
    /**
     * Data connector object or string.
     */
    // private $data_connector = NULL;

    /**
     * Class constructor.
     *
     * @param string  $key             Consumer key
     * @param mixed   $data_connector  String containing table name prefix, or database connection object, or array containing one or both values (optional, default is MySQL with an empty table name prefix)
     * @param boolean $autoEnable      true if the tool consumers is to be enabled automatically (optional, default is false)
     */
    public function __construct($key = NULL, $data_connector = '', $autoEnable = FALSE) {

        $this->data_connector = $data_connector == '' ? new Lti_DataConnector() : $data_connector;
        if (!empty($key)) {
            $this->load($key, $autoEnable);
        } else {
            $this->secret = $data_connector->getRandomString(32);
        }

    }

    /**
     * Initialise the tool consumer.
     */
    public function initialise() {

        $this->key = NULL;
        $this->name = NULL;
        $this->secret = NULL;
        $this->lti_version = NULL;
        $this->consumer_name = NULL;
        $this->consumer_version = NULL;
        $this->consumer_guid = NULL;
        $this->css_path = NULL;
        $this->protected = FALSE;
        $this->enabled = FALSE;
        $this->enable_from = NULL;
        $this->enable_until = NULL;
        $this->without_end_date = false;
        $this->last_access = NULL;
        $this->id_scope = Lti_ToolProvider::ID_SCOPE_ID_ONLY;
        $this->defaultEmail = '';
        $this->created = NULL;
        $this->updated = NULL;
        $this->role =1;

    }

    /**
     * Save the tool consumer to the database.
     *
     * @return boolean True if the object was successfully saved
     */
    public function save() {

        return $this->data_connector->Tool_Consumer_save($this);

    }

    /**
     * Delete the tool consumer from the database.
     *
     * @return boolean True if the object was successfully deleted
     */
    public function delete() {

        return $this->data_connector->Tool_Consumer_delete($this);

    }

    /**
     * Get the tool consumer key.
     *
     * @return string Consumer key value
     */
    public function getKey() {

        return $this->key;

    }

    /**
     * Get the data connector.
     *
     * @return mixed Data connector object or string
     */
    public function getDataConnector() {

        return $this->data_connector;

    }

    /**
     * Is the consumer key available to accept launch requests?
     *
     * @return boolean True if the consumer key is enabled and within any date constraints
     */
    public function getIsAvailable() {

        $ok = $this->enabled;

        $now = time();
        if ($ok && !is_null($this->enable_from)) {
            $ok = $this->enable_from <= $now;
        }
        if ($ok && !is_null($this->enable_until)) {
            $ok = $this->enable_until > $now;
        }

        return $ok;

    }

###
###  PRIVATE METHOD
###

    /**
     * Load the tool consumer from the database.
     *
     * @param string  $key        The consumer key value
     * @param boolean $autoEnable True if the consumer should be enabled (optional, default if false)
     *
     * @return boolean True if the consumer was successfully loaded
     */
    private function load($key, $autoEnable = FALSE) {

        $this->initialise();
        $this->key = $key;
        $ok = $this->data_connector->Tool_Consumer_load($this);
        if (!$ok) {
            $this->enabled = $autoEnable;
        }

        return $ok;

    }

}