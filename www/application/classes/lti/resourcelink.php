<?php
defined('SYSPATH') or die('No direct script access.');
/**
 * OAuth libaray file
 */
require_once('oauth.php');

/**
 * Class to represent a tool consumer resource link
 *
 * @author  Stephen P Vickers <stephen@spvsoftwareproducts.com>
 * @version 2.3.02
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3
 */
class Lti_ResourceLink {

    /**
     * Read action.
     */
    const EXT_READ = 1;
    /**
     * Write (create/update) action.
     */
    const EXT_WRITE = 2;
    /**
     * Delete action.
     */
    const EXT_DELETE = 3;

    /**
     * Decimal outcome type.
     */
    const EXT_TYPE_DECIMAL = 'decimal';
    /**
     * Percentage outcome type.
     */
    const EXT_TYPE_PERCENTAGE = 'percentage';
    /**
     * Ratio outcome type.
     */
    const EXT_TYPE_RATIO = 'ratio';
    /**
     * Letter (A-F) outcome type.
     */
    const EXT_TYPE_LETTER_AF = 'letteraf';
    /**
     * Letter (A-F) with optional +/- outcome type.
     */
    const EXT_TYPE_LETTER_AF_PLUS = 'letterafplus';
    /**
     * Pass/fail outcome type.
     */
    const EXT_TYPE_PASS_FAIL = 'passfail';
    /**
     * Free text outcome type.
     */
    const EXT_TYPE_TEXT = 'freetext';

    /**
     * Context ID as supplied in the last connection request.
     */
    public $lti_context_id = NULL;
    /**
     * Resource link ID as supplied in the last connection request.
     */
    public $lti_resource_id = NULL;
    /**
     * Context title.
     */
    public $title = NULL;
    /**
     * Associative array of setting values (Lti parameters, custom parameters and local parameters).
     */
    public $settings = NULL;
    /**
     * Associative array of user group sets (NULL if the consumer does not support the groups enhancement)
     */
    public $group_sets = NULL;
    /**
     * Associative array of user groups (NULL if the consumer does not support the groups enhancement)
     */
    public $groups = NULL;
    /**
     * Request for last extension service request.
     */
    public $ext_request = NULL;
    /**
     * Response from last extension service request.
     */
    public $ext_response = NULL;
    /**
     * Consumer key value for resource link being shared (if any).
     */
    public $primary_consumer_key = NULL;
    /**
     * ID value for resource link being shared (if any).
     */
    public $primary_resource_link_id = NULL;
    /**
     * True if the sharing request has been approved by the primary resource link.
     */
    public $share_approved = NULL;
    /**
     * Date/time when the object was created.
     */
    public $created = NULL;
    /**
     * Date/time when the object was last updated.
     */
    public $updated = NULL;

    /**
     * Lti_Tool_Consumer object for this resource link.
     */
    private $consumer = NULL;
    /**
     * ID for this resource link.
     */
    private $id = NULL;
    /**
     * True if the settings value have changed since last saved.
     */
    private $settings_changed = FALSE;
    /**
     * The XML document for the last extension service request.
     */
    private $ext_doc = NULL;
    /**
     * The XML node array for the last extension service request.
     */
    private $ext_nodes = NULL;

    /**
     * Class constructor.
     *
     * @param string $consumer Consumer key value
     * @param string $id       Resource link ID value
     */
    public function __construct($consumer, $id) {

        $this->consumer = $consumer;
        $this->id = $id;
        if (!empty($id)) {
            $this->load();
        } else {
            $this->initialise();
        }

    }

    /**
     * Initialise the resource link.
     */
    public function initialise() {

        $this->lti_context_id = NULL;
        $this->lti_resource_id = NULL;
        $this->title = '';
        $this->settings = array();
        $this->group_sets = NULL;
        $this->groups = NULL;
        $this->primary_consumer_key = NULL;
        $this->primary_resource_link_id = NULL;
        $this->share_approved = NULL;
        $this->created = NULL;
        $this->updated = NULL;

    }

    /**
     * Save the resource link to the database.
     *
     * @return boolean True if the resource link was successfully saved.
     */
    public function save() {

        $ok = $this->consumer->getDataConnector()->Resource_Link_save($this);
        if ($ok) {
            $this->settings_changed = FALSE;
        }

        return $ok;

    }

    /**
     * Delete the resource link from the database.
     *
     * @return boolean True if the resource link was successfully deleted.
     */
    public function delete() {

        return $this->consumer->getDataConnector()->Resource_Link_delete($this);

    }

    /**
     * Get tool consumer.
     *
     * @return object Lti_Tool_Consumer object for this resource link.
     */
    public function getConsumer() {

        return $this->consumer;

    }

    /**
     * Get tool consumer key.
     *
     * @return string Consumer key value for this resource link.
     */
    public function getKey() {

        return $this->consumer->getKey();

    }

    /**
     * Get resource link ID.
     *
     * @return string ID for this resource link.
     */
    public function getId() {

        return $this->id;

    }

    /**
     * Get a setting value.
     *
     * @param string $name    Name of setting
     * @param string $default Value to return if the setting does not exist (optional, default is an empty string)
     *
     * @return string Setting value
     */
    public function getSetting($name, $default = '') {

        if (array_key_exists($name, $this->settings)) {
            $value = $this->settings[$name];
        } else {
            $value = $default;
        }

        return $value;

    }

    /**
     * Set a setting value.
     *
     * @param string $name  Name of setting
     * @param string $value Value to set, use an empty value to delete a setting (optional, default is null)
     */
    public function setSetting($name, $value = NULL) {

        $old_value = $this->getSetting($name);
        if ($value != $old_value) {
            if (!empty($value)) {
                $this->settings[$name] = $value;
            } else {
                unset($this->settings[$name]);
            }
            $this->settings_changed = TRUE;
        }

    }

    /**
     * Get an array of all setting values.
     *
     * @return array Associative array of setting values
     */
    public function getSettings() {

        return $this->settings;

    }

    /**
     * Save setting values.
     *
     * @return boolean True if the settings were successfully saved
     */
    public function saveSettings() {

        if ($this->settings_changed) {
            $ok = $this->save();
        } else {
            $ok = TRUE;
        }

        return $ok;

    }

    /**
     * Check if the Outcomes service is supported.
     *
     * @return boolean True if this resource link supports the Outcomes service (either the Lti 1.1 or extension service)
     */
    public function hasOutcomesService() {

        $url = $this->getSetting('ext_ims_lis_basic_outcome_url') . $this->getSetting('lis_outcome_service_url');

        return !empty($url);

    }

    /**
     * Check if the Memberships service is supported.
     *
     * @return boolean True if this resource link supports the Memberships service
     */
    public function hasMembershipsService() {

        $url = $this->getSetting('ext_ims_lis_memberships_url');

        return !empty($url);

    }

    /**
     * Check if the Setting service is supported.
     *
     * @return boolean True if this resource link supports the Setting service
     */
    public function hasSettingService() {

        $url = $this->getSetting('ext_ims_lti_tool_setting_url');

        return !empty($url);

    }

    /**
     * Perform an Outcomes service request.
     *
     * @param int $action The action type constant
     * @param Lti_Outcome $lti_outcome Outcome object
     *
     * @return boolean True if the request was successfully processed
     */
    public function doOutcomesService($action, $lti_outcome) {

        $response = FALSE;
        $this->ext_response = NULL;
#
### Use Lti 1.1 service in preference to extension service if it is available
#
        $urlLTI11 = $this->getSetting('lis_outcome_service_url');
        $urlExt = $this->getSetting('ext_ims_lis_basic_outcome_url');
        if ($urlExt || $urlLTI11) {
            switch ($action) {
                case self::EXT_READ:
                    if ($urlLTI11 && ($lti_outcome->type == self::EXT_TYPE_DECIMAL)) {
                        $do = 'readResult';
                    } else if ($urlExt) {
                        $urlLTI11 = NULL;
                        $do = 'basic-lis-readresult';
                    }
                    break;
                case self::EXT_WRITE:
                    if ($urlLTI11 && $this->checkValueType($lti_outcome, array(self::EXT_TYPE_DECIMAL))) {
                        $do = 'replaceResult';
                    } else if ($this->checkValueType($lti_outcome)) {
                        $urlLTI11 = NULL;
                        $do = 'basic-lis-updateresult';
                    }
                    break;
                case self::EXT_DELETE:
                    if ($urlLTI11 && ($lti_outcome->type == self::EXT_TYPE_DECIMAL)) {
                        $do = 'deleteResult';
                    } else if ($urlExt) {
                        $urlLTI11 = NULL;
                        $do = 'basic-lis-deleteresult';
                    }
                    break;
            }
        }
        if (isset($do)) {
            $value = $lti_outcome->getValue();
            if (is_null($value)) {
                $value = '';
            }
            if ($urlLTI11) {
                $xml = <<<EOF
      <resultRecord>
        <sourcedGUID>
          <sourcedId>{$lti_outcome->getSourcedid()}</sourcedId>
        </sourcedGUID>
        <result>
          <resultScore>
            <language>{$lti_outcome->language}</language>
            <textString>{$value}</textString>
          </resultScore>
        </result>
      </resultRecord>
EOF;
                if ($this->doLTI11Service($do, $urlLTI11, $xml)) {
                    switch ($action) {
                        case self::EXT_READ:
                            if (!isset($this->ext_nodes['imsx_POXBody']["{$do}Response"]['result']['resultScore']['textString'])) {
                                break;
                            } else {
                                $lti_outcome->setValue($this->ext_nodes['imsx_POXBody']["{$do}Response"]['result']['resultScore']['textString']);
                            }
                        case self::EXT_WRITE:
                        case self::EXT_DELETE:
                            $response = TRUE;
                            break;
                    }
                }
            } else {
                $params = array();
                $params['sourcedid'] = $lti_outcome->getSourcedid();
                $params['result_resultscore_textstring'] = $value;
                if (!empty($lti_outcome->language)) {
                    $params['result_resultscore_language'] = $lti_outcome->language;
                }
                if (!empty($lti_outcome->status)) {
                    $params['result_statusofresult'] = $lti_outcome->status;
                }
                if (!empty($lti_outcome->date)) {
                    $params['result_date'] = $lti_outcome->date;
                }
                if (!empty($lti_outcome->type)) {
                    $params['result_resultvaluesourcedid'] = $lti_outcome->type;
                }
                if (!empty($lti_outcome->data_source)) {
                    $params['result_datasource'] = $lti_outcome->data_source;
                }
                if ($this->doService($do, $urlExt, $params)) {
                    switch ($action) {
                        case self::EXT_READ:
                            if (isset($this->ext_nodes['result']['resultscore']['textstring'])) {
                                $response = $this->ext_nodes['result']['resultscore']['textstring'];
                            }
                            break;
                        case self::EXT_WRITE:
                        case self::EXT_DELETE:
                            $response = TRUE;
                            break;
                    }
                }
            }
            if (is_array($response) && (count($response) <= 0)) {
                $response = '';
            }
        }

        return $response;

    }

    /**
     * Perform a Memberships service request.
     *
     * The user table is updated with the new list of user objects.
     *
     * @param boolean $withGroups True is group information is to be requested as well
     *
     * @return mixed Array of Lti_User objects or False if the request was not successful
     */
    public function doMembershipsService($withGroups = FALSE) {
        $users = array();
        $old_users = $this->getUserResultSourcedIDs(TRUE, Lti_ToolProvider::ID_SCOPE_RESOURCE);
        $this->ext_response = NULL;
        $url = $this->getSetting('ext_ims_lis_memberships_url');
        $params = array();
        $params['id'] = $this->getSetting('ext_ims_lis_memberships_id');
        $ok = FALSE;
        if ($withGroups) {
            $ok = $this->doService('basic-lis-readmembershipsforcontextwithgroups', $url, $params);
        }
        if ($ok) {
            $this->group_sets = array();
            $this->groups = array();
        } else {
            $ok = $this->doService('basic-lis-readmembershipsforcontext', $url, $params);
        }

        if ($ok) {
            if (!isset($this->ext_nodes['memberships']['member'])) {
                $members = array();
            } else if (!isset($this->ext_nodes['memberships']['member'][0])) {
                $members = array();
                $members[0] = $this->ext_nodes['memberships']['member'];
            } else {
                $members = $this->ext_nodes['memberships']['member'];
            }

            for ($i = 0; $i < count($members); $i++) {

                $user = new Lti_User($this, $members[$i]['user_id']);
#
### Set the user name
#
                $firstname = (isset($members[$i]['person_name_given'])) ? $members[$i]['person_name_given'] : '';
                $lastname = (isset($members[$i]['person_name_family'])) ? $members[$i]['person_name_family'] : '';
                $fullname = (isset($members[$i]['person_name_full'])) ? $members[$i]['person_name_full'] : '';
                $user->setNames($firstname, $lastname, $fullname);
#
### Set the user email
#
                $email = (isset($members[$i]['person_contact_email_primary'])) ? $members[$i]['person_contact_email_primary'] : '';
                $user->setEmail($email, $this->consumer->defaultEmail);
#
### Set the user roles
#
                if (isset($members[$i]['roles'])) {
                    $user->roles = Lti_ToolProvider::parseRoles($members[$i]['roles']);
                }
#
### Set the user groups
#
                if (!isset($members[$i]['groups']['group'])) {
                    $groups = array();
                } else if (!isset($members[$i]['groups']['group'][0])) {
                    $groups = array();
                    $groups[0] = $members[$i]['groups']['group'];
                } else {
                    $groups = $members[$i]['groups']['group'];
                }
                for ($j = 0; $j < count($groups); $j++) {
                    $group = $groups[$j];
                    if (isset($group['set'])) {
                        $set_id = $group['set']['id'];
                        if (!isset($this->group_sets[$set_id])) {
                            $this->group_sets[$set_id] = array('title' => $group['set']['title'], 'groups' => array(),
                                'num_members' => 0, 'num_staff' => 0, 'num_learners' => 0);
                        }
                        $this->group_sets[$set_id]['num_members']++;
                        if ($user->isStaff()) {
                            $this->group_sets[$set_id]['num_staff']++;
                        }
                        if ($user->isLearner()) {
                            $this->group_sets[$set_id]['num_learners']++;
                        }
                        if (!in_array($group['id'], $this->group_sets[$set_id]['groups'])) {
                            $this->group_sets[$set_id]['groups'][] = $group['id'];
                        }
                        $this->groups[$group['id']] = array('title' => $group['title'], 'set' => $set_id);
                    } else {
                        $this->groups[$group['id']] = array('title' => $group['title']);
                    }
                    $user->groups[] = $group['id'];
                }
#
### If a result sourcedid is provided save the user
#
                if (isset($members[$i]['lis_result_sourcedid'])) {
                    $user->lti_result_sourcedid = $members[$i]['lis_result_sourcedid'];
                    $user->save();
                }
                $users[] = $user;
#
### Remove old user (if it exists)
#
                unset($old_users[$user->getId(Lti_ToolProvider::ID_SCOPE_RESOURCE)]);
            }
#
### Delete any old users which were not in the latest list from the tool consumer
#
            foreach ($old_users as $id => $user) {
                $user->delete();
            }
        } else {
            $users = FALSE;
        }

        return $users;

    }

    /**
     * Perform a Setting service request.
     *
     * @param int    $action The action type constant
     * @param string $value  The setting value (optional, default is null)
     *
     * @return mixed The setting value for a read action, true if a write or delete action was successful, otherwise false
     */
    public function doSettingService($action, $value = NULL) {

        $response = FALSE;
        $this->ext_response = NULL;
        switch ($action) {
            case self::EXT_READ:
                $do = 'basic-lti-loadsetting';
                break;
            case self::EXT_WRITE:
                $do = 'basic-lti-savesetting';
                break;
            case self::EXT_DELETE:
                $do = 'basic-lti-deletesetting';
                break;
        }
        if (isset($do)) {

            $url = $this->getSetting('ext_ims_lti_tool_setting_url');
            $params = array();
            $params['id'] = $this->getSetting('ext_ims_lti_tool_setting_id');
            if (is_null($value)) {
                $value = '';
            }
            $params['setting'] = $value;

            if ($this->doService($do, $url, $params)) {
                switch ($action) {
                    case self::EXT_READ:
                        if (isset($this->ext_nodes['setting']['value'])) {
                            $response = $this->ext_nodes['setting']['value'];
                            if (is_array($response)) {
                                $response = '';
                            }
                        }
                        break;
                    case self::EXT_WRITE:
                        $this->setSetting('ext_ims_lti_tool_setting', $value);
                        $this->saveSettings();
                        $response = TRUE;
                        break;
                    case self::EXT_DELETE:
                        $response = TRUE;
                        break;
                }
            }

        }

        return $response;

    }

    /**
     * Obtain an array of Lti_User objects for users with a result sourcedId.
     *
     * The array may include users from other resource links which are sharing this resource link.
     * It may also be optionally indexed by the user ID of a specified scope.
     *
     * @param boolean $local_only True if only users from this resource link are to be returned, not users from shared resource links (optional, default is false)
     * @param int     $id_scope     Scope to use for ID values (optional, default is null for consumer default)
     *
     * @return
     */
    public function getUserResultSourcedIDs($local_only = FALSE, $id_scope = NULL) {

        return $this->consumer->getDataConnector()->Resource_Link_getUserResultSourcedIDs($this, $local_only, $id_scope);

    }

    /**
     * Get an array of Lti_Resource_Link_Share objects for each resource link which is sharing this context.
     *
     * @return array Array of Lti_Resource_Link_Share objects
     */
    public function getShares() {

        return $this->consumer->getDataConnector()->Resource_Link_getShares($this);

    }

###
###  PRIVATE METHODS
###

    /**
     * Load the resource link from the database.
     *
     * @return boolean True if resource link was successfully loaded
     */
    private function load() {

        $this->initialise();
        return $this->consumer->getDataConnector()->Resource_Link_load($this);

    }

    /**
     * Convert data type of value to a supported type if possible.
     *
     * @param Lti_Outcome $lti_outcome     Outcome object
     * @param string[]    $supported_types Array of outcome types to be supported (optional, default is null to use supported types reported in the last launch for this resource link)
     *
     * @return boolean True if the type/value are valid and supported
     */
    private function checkValueType($lti_outcome, $supported_types = NULL) {

        if (empty($supported_types)) {
            $supported_types = explode(',', str_replace(' ', '', strtolower($this->getSetting('ext_ims_lis_resultvalue_sourcedids', self::EXT_TYPE_DECIMAL))));
        }
        $type = $lti_outcome->type;
        $value = $lti_outcome->getValue();
// Check whether the type is supported or there is no value
        $ok = in_array($type, $supported_types) || (strlen($value) <= 0);
        if (!$ok) {
// Convert numeric values to decimal
            if ($type == self::EXT_TYPE_PERCENTAGE) {
                if (substr($value, -1) == '%') {
                    $value = substr($value, 0, -1);
                }
                $ok = is_numeric($value) && ($value >= 0) && ($value <= 100);
                if ($ok) {
                    $lti_outcome->setValue($value / 100);
                    $lti_outcome->type = self::EXT_TYPE_DECIMAL;
                }
            } else if ($type == self::EXT_TYPE_RATIO) {
                $parts = explode('/', $value, 2);
                $ok = (count($parts) == 2) && is_numeric($parts[0]) && is_numeric($parts[1]) && ($parts[0] >= 0) && ($parts[1] > 0);
                if ($ok) {
                    $lti_outcome->setValue($parts[0] / $parts[1]);
                    $lti_outcome->type = self::EXT_TYPE_DECIMAL;
                }
// Convert letter_af to letter_af_plus or text
            } else if ($type == self::EXT_TYPE_LETTER_AF) {
                if (in_array(self::EXT_TYPE_LETTER_AF_PLUS, $supported_types)) {
                    $ok = TRUE;
                    $lti_outcome->type = self::EXT_TYPE_LETTER_AF_PLUS;
                } else if (in_array(self::EXT_TYPE_TEXT, $supported_types)) {
                    $ok = TRUE;
                    $lti_outcome->type = self::EXT_TYPE_TEXT;
                }
// Convert letter_af_plus to letter_af or text
            } else if ($type == self::EXT_TYPE_LETTER_AF_PLUS) {
                if (in_array(self::EXT_TYPE_LETTER_AF, $supported_types) && (strlen($value) == 1)) {
                    $ok = TRUE;
                    $lti_outcome->type = self::EXT_TYPE_LETTER_AF;
                } else if (in_array(self::EXT_TYPE_TEXT, $supported_types)) {
                    $ok = TRUE;
                    $lti_outcome->type = self::EXT_TYPE_TEXT;
                }
// Convert text to decimal
            } else if ($type == self::EXT_TYPE_TEXT) {
                $ok = is_numeric($value) && ($value >= 0) && ($value <=1);
                if ($ok) {
                    $lti_outcome->type = self::EXT_TYPE_DECIMAL;
                } else if (substr($value, -1) == '%') {
                    $value = substr($value, 0, -1);
                    $ok = is_numeric($value) && ($value >= 0) && ($value <=100);
                    if ($ok) {
                        if (in_array(self::EXT_TYPE_PERCENTAGE, $supported_types)) {
                            $lti_outcome->type = self::EXT_TYPE_PERCENTAGE;
                        } else {
                            $lti_outcome->setValue($value / 100);
                            $lti_outcome->type = self::EXT_TYPE_DECIMAL;
                        }
                    }
                }
            }
        }

        return $ok;

    }

    /**
     * Send a service request to the tool consumer.
     *
     * @param string $type   Message type value
     * @param string $url    URL to send request to
     * @param array  $params Associative array of parameter values to be passed
     *
     * @return boolean True if the request successfully obtained a response
     */
    private function doService($type, $url, $params) {

        $this->ext_response = NULL;
        if (!empty($url)) {
// Check for query parameters which need to be included in the signature
            $query_params = array();
            $query_string = parse_url($url, PHP_URL_QUERY);
            if (!is_null($query_string)) {
                $query_items = explode('&', $query_string);
                foreach ($query_items as $item) {
                    if (strpos($item, '=') !== FALSE) {
                        list($name, $value) = explode('=', $item);
                        $query_params[$name] = $value;
                    } else {
                        $query_params[$name] = '';
                    }
                }
            }
            $params = $params + $query_params;
// Add standard parameters
            $params['oauth_consumer_key'] = $this->consumer->getKey();
            $params['lti_version'] = Lti_ToolProvider::LTI_VERSION;
            $params['lti_message_type'] = $type;
// Add OAuth signature
            $hmac_method = new OAuthSignatureMethod_HMAC_SHA1();
            $consumer = new OAuthConsumer($this->consumer->getKey(), $this->consumer->secret, NULL);
            $req = OAuthRequest::from_consumer_and_token($consumer, NULL, 'POST', $url, $params);
            $req->sign_request($hmac_method, $consumer, NULL);
            $params = $req->get_parameters();
// Remove parameters being passed on the query string
            foreach (array_keys($query_params) as $name) {
                unset($params[$name]);
            }
// Connect to tool consumer
            $this->ext_response = $this->do_post_request($url, $params);
// Parse XML response
            if ($this->ext_response) {
                try {
                    $this->ext_doc = new DOMDocument();
                    $this->ext_doc->loadXML($this->ext_response);
                    $this->ext_nodes = $this->domnode_to_array($this->ext_doc->documentElement);
                    if (!isset($this->ext_nodes['statusinfo']['codemajor']) || ($this->ext_nodes['statusinfo']['codemajor'] != 'Success')) {
                        $this->ext_response = NULL;
                    }
                } catch (Exception $e) {
                    $this->ext_response = NULL;
                }
            } else {
                $this->ext_response = NULL;
            }
        }

        return !is_null($this->ext_response);

    }

    /**
     * Send a service request to the tool consumer.
     *
     * @param string $type Message type value
     * @param string $url  URL to send request to
     * @param string $xml  XML of message request
     *
     * @return boolean True if the request successfully obtained a response
     */
    private function doLTI11Service($type, $url, $xml) {

        $this->ext_response = NULL;
        if (!empty($url)) {
            $id = uniqid();
            $xmlRequest = <<<EOF
<?xml version = "1.0" encoding = "UTF-8"?>
<imsx_POXEnvelopeRequest xmlns = "http://www.imsglobal.org/services/ltiv1p1/xsd/imsoms_v1p0">
  <imsx_POXHeader>
    <imsx_POXRequestHeaderInfo>
      <imsx_version>V1.0</imsx_version>
      <imsx_messageIdentifier>{$id}</imsx_messageIdentifier>
    </imsx_POXRequestHeaderInfo>
  </imsx_POXHeader>
  <imsx_POXBody>
    <{$type}Request>
{$xml}
    </{$type}Request>
  </imsx_POXBody>
</imsx_POXEnvelopeRequest>
EOF;
// Calculate body hash
            $hash = base64_encode(sha1($xmlRequest, TRUE));
            $params = array('oauth_body_hash' => $hash);

// Add OAuth signature
            $hmac_method = new OAuthSignatureMethod_HMAC_SHA1();
            $consumer = new OAuthConsumer($this->consumer->getKey(), $this->consumer->secret, NULL);
            $req = OAuthRequest::from_consumer_and_token($consumer, NULL, 'POST', $url, $params);
            $req->sign_request($hmac_method, $consumer, NULL);
            $params = $req->get_parameters();
            $header = $req->to_header();
            $header .= "\nContent-Type: application/xml";
// Connect to tool consumer
            $this->ext_response = $this->do_post_request($url, $xmlRequest, $header);
// Parse XML response
            if ($this->ext_response) {
                try {
                    $this->ext_doc = new DOMDocument();
                    $this->ext_doc->loadXML($this->ext_response);
                    $this->ext_nodes = $this->domnode_to_array($this->ext_doc->documentElement);
                    if (!isset($this->ext_nodes['imsx_POXHeader']['imsx_POXResponseHeaderInfo']['imsx_statusInfo']['imsx_codeMajor']) ||
                        ($this->ext_nodes['imsx_POXHeader']['imsx_POXResponseHeaderInfo']['imsx_statusInfo']['imsx_codeMajor'] != 'success')) {
                        $this->ext_response = NULL;
                    }
                } catch (Exception $e) {
                    $this->ext_response = NULL;
                }
            } else {
                $this->ext_response = NULL;
            }
        }

        return !is_null($this->ext_response);

    }

    /**
     * Get the response from an HTTP POST request.
     *
     * @param string $url    URL to send request to
     * @param array  $params Associative array of parameter values to be passed
     * @param string $header Values to include in the request header (optional, default is none)
     *
     * @return string response contents, empty if the request was not successfull
     */
    private function do_post_request($url, $params, $header = NULL) {

        $ok = FALSE;
        if (is_array($params)) {
            $data = http_build_query($params);
        } else {
            $data = $params;
        }
        $this->ext_request = $data;
// Try using curl if available
        if (function_exists('curl_init')) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            if (!empty($header)) {
                $headers = explode("\n", $header);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            }
            curl_setopt($ch, CURLOPT_POST, TRUE);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            $resp = curl_exec($ch);
            $ok = $resp !== FALSE;
            curl_close($ch);
        }
// Try using fopen if curl was not available or did not work (could have been an SSL certificate issue)
        if (!$ok) {
            $opts = array('method' => 'POST',
                'content' => $data
            );
            if (!empty($header)) {
                $opts['header'] = $header;
            }
            $ctx = stream_context_create(array('http' => $opts));
            $fp = @fopen($url, 'rb', false, $ctx);
            if ($fp) {
                $resp = @stream_get_contents($fp);
                $ok = $resp !== FALSE;
            }
        }
        if ($ok) {
            $response = $resp;
        } else {
            $response = '';
        }

        return $response;

    }

    /**
     * Convert DOM nodes to array.
     *
     * @param DOMElement $node XML element
     *
     * @return array Array of XML document elements
     */
    private function domnode_to_array($node) {

        $output = array();
        switch ($node->nodeType) {
            case XML_CDATA_SECTION_NODE:
            case XML_TEXT_NODE:
                $output = trim($node->textContent);
                break;
            case XML_ELEMENT_NODE:
                for ($i=0, $m=$node->childNodes->length; $i<$m; $i++) {
                    $child = $node->childNodes->item($i);
                    $v = $this->domnode_to_array($child);
                    if (isset($child->tagName)) {
                        $t = $child->tagName;
                        if (!isset($output[$t])) {
                            $output[$t] = array();
                        }
                        $output[$t][] = $v;
                    } else if($v) {
                        $output = (string) $v;
                    }
                }
                if (is_array($output)) {
                    if ($node->attributes->length) {
                        $a = array();
                        foreach ($node->attributes as $attrName => $attrNode) {
                            $a[$attrName] = (string) $attrNode->value;
                        }
                        $output['@attributes'] = $a;
                    }
                    foreach ($output as $t => $v) {
                        if (is_array($v) && count($v)==1 && $t!='@attributes') {
                            $output[$t] = $v[0];
                        }
                    }
                }
                break;
        }

        return $output;

    }

}