<?php
defined('SYSPATH') or die('No direct script access.');
/**
 * Class to represent a tool consumer resource link share key
 *
 * @author  Stephen P Vickers <stephen@spvsoftwareproducts.com>
 * @version 2.3.02
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3
 */
class Lti_ResourceLinkShareKey {

    /**
     * Maximum permitted life for a share key value.
     */
    const MAX_SHARE_KEY_LIFE = 168;  // in hours (1 week)
    /**
     * Default life for a share key value.
     */
    const DEFAULT_SHARE_KEY_LIFE = 24;  // in hours
    /**
     * Minimum length for a share key value.
     */
    const MIN_SHARE_KEY_LENGTH = 5;
    /**
     * Maximum length for a share key value.
     */
    const MAX_SHARE_KEY_LENGTH = 32;

    /**
     * Consumer key for resource link being shared.
     */
    public $primary_consumer_key = NULL;
    /**
     * ID for resource link being shared.
     */
    public $primary_resource_link_id = NULL;
    /**
     * Length of share key.
     */
    public $length = NULL;
    /**
     * Life of share key.
     */
    public $life = NULL;  // in hours
    /**
     * True if the sharing arrangement should be automatically approved when first used.
     */
    public $auto_approve = FALSE;
    /**
     * Date/time when the share key expires.
     */
    public $expires = NULL;

    /**
     * Share key value.
     */
    private $id = NULL;
    /**
     * Data connector.
     */
    private $data_connector = NULL;

    /**
     * Class constructor.
     *
     * @param Lti_ResourceLink $resource_link  Resource_Link object
     * @param string      $id      Value of share key (optional, default is null)
     */
    public function __construct($resource_link, $id = NULL) {

        $this->initialise();
        $this->data_connector = $resource_link->getConsumer()->getDataConnector();
        $this->id = $id;
        $this->primary_context_id = &$this->primary_resource_link_id;
        if (!empty($id)) {
            $this->load();
        } else {
            $this->primary_consumer_key = $resource_link->getKey();
            $this->primary_resource_link_id = $resource_link->getId();
        }

    }

    /**
     * Initialise the resource link share key.
     */
    public function initialise() {

        $this->primary_consumer_key = NULL;
        $this->primary_resource_link_id = NULL;
        $this->length = NULL;
        $this->life = NULL;
        $this->auto_approve = FALSE;
        $this->expires = NULL;

    }

    /**
     * Save the resource link share key to the database.
     *
     * @return boolean True if the share key was successfully saved
     */
    public function save() {

        if (empty($this->life)) {
            $this->life = self::DEFAULT_SHARE_KEY_LIFE;
        } else {
            $this->life = max(min($this->life, self::MAX_SHARE_KEY_LIFE), 0);
        }
        $this->expires = time() + ($this->life * 60 * 60);
        if (empty($this->id)) {
            if (empty($this->length) || !is_numeric($this->length)) {
                $this->length = self::MAX_SHARE_KEY_LENGTH;
            } else {
                $this->length = max(min($this->length, self::MAX_SHARE_KEY_LENGTH), self::MIN_SHARE_KEY_LENGTH);
            }
            $this->id = Lti_DataConnector::getRandomString($this->length);
        }

        return Lti_DataConnector::Resource_Link_Share_Key_save($this);

    }

    /**
     * Delete the resource link share key from the database.
     *
     * @return boolean True if the share key was successfully deleted
     */
    public function delete() {

        return Lti_DataConnector::Resource_Link_Share_Key_delete($this);

    }

    /**
     * Get share key value.
     *
     * @return string Share key value
     */
    public function getId() {

        return $this->id;

    }

###
###  PRIVATE METHOD
###

    /**
     * Load the resource link share key from the database.
     */
    private function load() {

        $this->initialise();
        $this->data_connector->Resource_Link_Share_Key_load($this);
        if (!is_null($this->id)) {
            $this->length = strlen($this->id);
        }
        if (!is_null($this->expires)) {
            $this->life = ($this->expires - time()) / 60 / 60;
        }

    }

}