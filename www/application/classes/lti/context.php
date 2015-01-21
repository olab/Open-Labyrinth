<?php
defined('SYSPATH') or die('No direct script access.');
/**
 * Class to represent a tool consumer context
 *
 * @deprecated Use Lti_Resource_Link instead
 * @see Lti_Resource_Link
 *
 * @author  Stephen P Vickers <stephen@spvsoftwareproducts.com>
 * @version 2.3.02
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3
 */
class Lti_Context extends Lti_ResourceLink {

    /**
     * ID value for context being shared (if any).
     *
     * @deprecated Use primary_resource_link_id instead
     * @see Lti_Resource_Link::$primary_resource_link_id
     */
    public $primary_context_id = NULL;

    /**
     * Class constructor.
     *
     * @param string $consumer Consumer key value
     * @param string $id       Resource link ID value
     */
    public function __construct($consumer, $id) {

        parent::__construct($consumer, $id);
        $this->primary_context_id = &$this->primary_resource_link_id;

    }

}