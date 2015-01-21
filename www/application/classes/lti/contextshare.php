<?php
defined('SYSPATH') or die('No direct script access.');
/**
 * Class to represent a tool consumer context share
 *
 * @deprecated Use Lti_Resource_Link_Share instead
 * @see Lti_Resource_Link_Share
 *
 * @author  Stephen P Vickers <stephen@spvsoftwareproducts.com>
 * @version 2.3.02
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3
 */
class Lti_ContextShare extends Lti_ResourceLinkShare {

    /**
     * Context ID value.
     *
     * @deprecated Use Lti_Resource_Link_Share->resource_link_id instead
     * @see Lti_Resource_Link_Share::$resource_link_id
     */
    public $context_id = NULL;

    /**
     * Class constructor.
     */
    public function __construct() {

        parent::__construct();
        $this->context_id = &$this->resource_link_id;

    }

}