<?php
defined('SYSPATH') or die('No direct script access.');
/**
 * Class to represent a tool consumer context share key
 *
 * @deprecated Use Lti_Resource_Link_Share_Key instead
 * @see Lti_Resource_Link_Share_Key
 *
 * @author  Stephen P Vickers <stephen@spvsoftwareproducts.com>
 * @version 2.3.02
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3
 */
class Lti_ContextShareKey extends Lti_ResourceLinkShareKey {

    /**
     * ID for context being shared.
     *
     * @deprecated Use Lti_Resource_Link_Share_Key->primary_resource_link_id instead
     * @see Lti_Resource_Link_Share_Key::$primary_resource_link_id
     */
    public $primary_context_id = NULL;

    /**
     * Class constructor.
     *
     * @param Lti_ResourceLink $resource_link  Resource_Link object
     * @param string      $id      Value of share key (optional, default is null)
     */
    public function __construct($resource_link, $id = NULL) {

        parent::__construct($resource_link, $id);
        $this->primary_context_id = &$this->primary_resource_link_id;

    }

}