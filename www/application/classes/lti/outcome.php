<?php
defined('SYSPATH') or die('No direct script access.');
/**
 * Class to represent an outcome
 *
 * @author  Stephen P Vickers <stephen@spvsoftwareproducts.com>
 * @version 2.3.02
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3
 */
class Lti_Outcome {

    /**
     * Language value.
     */
    public $language = NULL;
    /**
     * Outcome status value.
     */
    public $status = NULL;
    /**
     * Outcome date value.
     */
    public $date = NULL;
    /**
     * Outcome type value.
     */
    public $type = NULL;
    /**
     * Outcome data source value.
     */
    public $data_source = NULL;

    /**
     * Result sourcedid.
     */
    private $sourcedid = NULL;
    /**
     * Outcome value.
     */
    private $value = NULL;

    /**
     * Class constructor.
     *
     * @param string $sourcedid Result sourcedid value for the user/resource link
     * @param string $value     Outcome value (optional, default is none)
     */
    public function __construct($sourcedid, $value = NULL) {

        $this->sourcedid = $sourcedid;
        $this->value = $value;
        $this->language = 'en-US';
        $this->date = gmdate('Y-m-d\TH:i:s\Z', time());
        $this->type = 'decimal';

    }

    /**
     * Get the result sourcedid value.
     *
     * @return string Result sourcedid value
     */
    public function getSourcedid() {

        return $this->sourcedid;

    }

    /**
     * Get the outcome value.
     *
     * @return string Outcome value
     */
    public function getValue() {

        return $this->value;

    }

    /**
     * Set the outcome value.
     *
     * @param string Outcome value
     */
    public function setValue($value) {

        $this->value = $value;

    }

}