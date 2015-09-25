<?php
/**
 * Lti_ToolProvider - PHP class to include in an external tool to handle connections with an LTI 1 compliant tool consumer
 * Copyright (C) 2013  Stephen P Vickers
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 *
 * Contact: stephen@spvsoftwareproducts.com
 *
 * Version history:
 *   2.0.00  30-Jun-12  Initial release (replacing version 1.1.01 of BasicLti_ToolProvider)
 *   2.1.00   3-Jul-12  Added option to restrict use of consumer key based on tool consumer GUID value
 *                      Added field to record day of last access for each consumer key
 *   2.2.00  16-Oct-12  Added option to return parameters sent in last extension request
 *                      Released under GNU Lesser General Public License, version 3
 *   2.3.00   2-Jan-13  Removed autoEnable property from Lti_ToolProvider class (including constructor parameter)
 *                      Added Lti_ToolProvider->setParameterConstraint() method
 *                      Changed references to $_REQUEST to $_POST
 *                      Added Lti_Tool_Consumer->getIsAvailable() method
 *                      Deprecated Lti_Context (use Lti_Resource_Link instead), other references to Context deprecated in favour of Resource_Link
 *   2.3.01   2-Feb-13  Added error callback option to Lti_ToolProvider class
 *                      Fixed typo in setParameterConstraint function
 *                      Updated to use latest release of OAuth dependent library
 *                      Added message property to Lti_ToolProvider class to override default message returned on error
 *   2.3.02  18-Apr-13  Tightened up checking of roles - now case sensitive and checks fully qualified URN
 *                      Fixed bug with not updating a resource link before redirecting to a shared resource link
 */

/**
 * OAuth libaray file
 */
require_once('oauth.php');

/**
 * Class to represent an Lti Tool Provider
 *
 * @author  Stephen P Vickers <stephen@spvsoftwareproducts.com>
 * @version 2.3.02
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3
 */
class Lti_ToolProvider {

  const CONNECTION_ERROR_MESSAGE = 'Sorry, there was an error connecting you to the application.';

/**
 * Lti version for messages.
 */
  const LTI_VERSION = 'LTI-1p0';
/**
 * Use ID value only.
 */
  const ID_SCOPE_ID_ONLY = 0;
/**
 * Prefix an ID with the consumer key.
 */
  const ID_SCOPE_GLOBAL = 1;
/**
 * Prefix the ID with the consumer key and context ID.
 */
  const ID_SCOPE_CONTEXT = 2;
/**
 * Prefix the ID with the consumer key and resource ID.
 */
  const ID_SCOPE_RESOURCE = 3;
/**
 * Character used to separate each element of an ID.
 */
  const ID_SCOPE_SEPARATOR = ':';

/**
 *  True if the last request was successful.
 */
  public $isOK = TRUE;

/**
 *  Lti_Tool_Consumer object.
 */
  public $consumer = NULL;
/**
 *  Return URL provided by tool consumer.
 */
  public $return_url = NULL;
/**
 *  Lti_User object.
 */
  public $user = NULL;
/**
 *  Lti_Resource_Link object.
 */
  public $resource_link = NULL;
/**
 *  Lti_Context object.
 *
 *  @deprecated Use resource_link instead
 *  @see Lti_ToolProvider::$resource_link
 */
  public $context = NULL;
/**
 *  Data connector object.
 */
  public $data_connector = NULL;
/**
 *  Default email domain.
 */
  public $defaultEmail = '';
/**
 *  Scope to use for user IDs.
 */
  public $id_scope = self::ID_SCOPE_ID_ONLY;
/**
 *  True if shared resource link arrangements are permitted.
 */
  public $allowSharing = FALSE;
/**
 *  Message for last request processed.
 */
  public $message = self::CONNECTION_ERROR_MESSAGE;
/**
 *  Error message for last request processed.
 */
  public $reason = NULL;

/**
 *  URL to redirect user to on successful completion of the request.
 */
  private $redirectURL = NULL;
/**
 *  Callback functions for handling requests.
 */
  private $callbackHandler = NULL;
/**
 *  HTML to be displayed on successful completion of the request.
 */
  private $output = NULL;
/**
 *  URL to redirect user to if the request is not successful.
 */
  private $error = NULL;
/**
 *  True if debug messages explaining the cause of errors are to be returned to the tool consumer.
 */
  private $debugMode = TRUE;
/**
 *  Array of LTI parameter constraints for auto validation checks.
 */
  private $constraints = NULL;
/**
 *  Names of LTI parameters to be retained in the settings property.
 */
  private $lti_settings_names = array('ext_resource_link_content', 'ext_resource_link_content_signature',
                                      'lis_result_sourcedid', 'lis_outcome_service_url',
                                      'ext_ims_lis_basic_outcome_url', 'ext_ims_lis_resultvalue_sourcedids',
                                      'ext_ims_lis_memberships_id', 'ext_ims_lis_memberships_url',
                                      'ext_ims_lti_tool_setting', 'ext_ims_lti_tool_setting_id', 'ext_ims_lti_tool_setting_url');

/**
 * Class constructor
 *
 * @param mixed   $callbackHandler String containing name of callback function for connect request, or associative array of callback functions for each request type
 * @param mixed   $data_connector  String containing table name prefix, or database connection object, or array containing one or both values (optional, default is a blank prefix and MySQL)
 */
  function __construct($callbackHandler, $data_connector) {

    if (!is_array($callbackHandler)) {
      $this->callbackHandler['connect'] = $callbackHandler;
    } else if (isset($callbackHandler['connect'])) {
      $this->callbackHandler = $callbackHandler;
    } else if (count($callbackHandler) > 0) {
      $callbackHandlers = array_values($callbackHandler);
      $this->callbackHandler['connect'] = $callbackHandlers[0];
    }
    $this->data_connector = $data_connector;
    $this->constraints = array();
    $this->context = &$this->resource_link;


  }

/**
 * Process a launch request
 *
 * @return mixed Returns TRUE or FALSE, a redirection URL or HTML
 */
  public function execute() {

    // Set return URL if available
    if (isset($_POST['launch_presentation_return_url'])) {
      $this->return_url = $_POST['launch_presentation_return_url'];
    }

    if ($this->authenticate()) {
      $this->doCallback();
    }
    $this->result();
  }



/**
 * Add a parameter constraint to be checked on launch
 *
 * @param string Name of parameter to be checked
 * @param boolean True if parameter is required
 * @param int Maximum permitted length of parameter value (optional, default is NULL)
 */
  public function setParameterConstraint($name, $required, $max_length = NULL) {

    $name = trim($name);
    if (strlen($name) > 0) {
      $this->constraints[$name] = array('required' => $required, 'max_length' => $max_length);
    }

  }

/**
 * Get an array of defined tool consumers
 *
 * @return array Array of Lti_Tool_Consumer objects
 */
  public function getConsumers() {

#
### Initialise data connector
#
    //$this->data_connector = Lti_Data_Connector::getDataConnector($this->data_connector); //+-
    return $this->data_connector->Tool_Consumer_list();

  }

/**
 * Get an array of fully qualified user roles
 *
 * @param string Comma-separated list of roles
 *
 * @return array Array of roles
 */
  static function parseRoles($rolesString) {

    $rolesArray = explode(',', $rolesString);
    $roles = array();
    foreach ($rolesArray as $role) {
      $role = trim($role);
      if (!empty($role)) {
        if (substr($role, 0, 4) != 'urn:') {
          $role = 'urn:lti:role:ims/lis/' . $role;
        }
        $roles[] = $role;
      }
    }

    return $roles;

  }

###
###  PRIVATE METHODS
###

/**
 * Call any callback function for the requested action.
 *
 * This function may set the redirectURL and output properties.
 *
 * @return boolean True if no error reported
 */
  private function doCallback()
  {
    if (isset($this->callbackHandler['connect'])) {
      $result = $this->lti_do_connect();
          //call_user_func($this->callbackHandler['connect'], $this);

        #
        ### Callback function may return HTML, a redirect URL, or a boolean value
        #
      if (is_string($result)) {
        if ((substr($result, 0, 7) == 'http://') || (substr($result, 0, 8) == 'https://')) {
          $this->redirectURL = $result;
        } else {
          if (is_null($this->output)) {
            $this->output = '';
          }
          $this->output .= $result;
        }
      } else if (is_bool($result)) {
        $this->isOK = $result;
      }
    }

    return $this->isOK;

  }

        /*-------------------------------------------------------------------
     * This function is called when a successful LTI call is made. Is is
     * passed a class (tool_provider) that can access all the details of
     * the LTI call
     *
     * Parameters
     *  tool_provider - intance of of BasicLti_Tool_Provider
     *-----------------------------------------------------------------*/
    private function lti_do_connect()
    {
        Auth::instance()->logout();

        $admin      = $this->user->isAdmin();
        $author     = $this->user->isStaff();
        $learner    =  $this->user->isLearner();
        $role       = 1;
        if ($admin) $role = 4;
        elseif ($author) $role = 2;
        elseif ($learner) $role = 1;

        if($this->consumer->role != $role){
            $this->message = 'User has chosen wrong type of permission (the role).';
            return FALSE;
        }

        $isLti      = 1;
        $languageId = 1;
        $uiMode     = 'easy';
        $password   = $this->consumer->secret;
        $nickname   = $this->user->fullname;
        $userId = $this->user->getId();
        $key = $this->resource_link->getKey();
        $username = $userId . '_' . $key;
        $user = DB_ORM::model('user')->getUserByName($username);
        $email      = $this->user->email;

        $trimPattern = " \t\n\r\0\x0B\"";

        if(isset($this->resource_link->settings['custom__group'])){
            $groupName = trim($this->resource_link->settings['custom__group'], $trimPattern);
        }elseif(isset($this->resource_link->settings['custom_group'])){
            $groupName = trim($this->resource_link->settings['custom_group'], $trimPattern);
        }else{
            $groupName = null;
        }

        if(!empty($groupName)) {
            $group = DB_ORM::model('Group')->getByName($groupName);
            $groupId = (!empty($group)) ? $group->id : null;
        }

        if ($user) {
            $id = $user->id;
            DB_ORM::model('User')->updateUser($id, $password, $nickname, $email, $role, $languageId, $uiMode, $isLti);
            if(!empty($groupId)) {
                $alreadyInGroup = DB_ORM::model('User_Group')->userExist($id, $groupId);
                if(!$alreadyInGroup){
                    DB_ORM::model('User_Group')->add($groupId, $id);
                }
            }
        } else {
            $id = DB_ORM::model('User')->createUser($username, $password, $nickname, $email, $role, $languageId, $uiMode, $isLti);
            if(!empty($groupId)) {
                DB_ORM::model('User_Group')->add($groupId, $id);
            }
        }

        $status = Auth::instance()->login($username, $this->consumer->secret);
        $redirectURL = URL::site(NULL, TRUE);
        if ($status) {
            $redirectURL .= Arr::get($_SERVER, 'REDIRECT_URL', '');
        }

        $ses = Session::instance();
        $ses->set('lti_tool_provider',$this);
        // Return URL for re-direction by Tool Provider class
        return $redirectURL;
    }

    static public function sendScore($score){
        $ses = Session::instance();
        $tool_provider = $ses->get('lti_tool_provider');
        $ses->delete('lti_tool_provider');
        $context = $tool_provider->resource_link;
        if ($context->hasOutcomesService()) {
            $outcome = new Lti_Outcome($tool_provider->user->lti_result_sourcedid);
            $outcome->setValue($score);
            //file_put_contents($_SERVER['DOCUMENT_ROOT'].'/testtxtfile.txt', var_export($score, true), FILE_APPEND);
            $context->doOutcomesService(Lti_ResourceLink::EXT_WRITE, $outcome);
        }
        return $tool_provider->return_url;
    }


/**
 * Perform the result of an action.
 *
 * This function may redirect the user to another URL rather than returning a value.
 *
 * @return string Output to be displayed (redirection, or display HTML or message)
 */
  private function result() {

    $ok = FALSE;
    if (!$this->isOK && isset($this->callbackHandler['error'])) {
      $ok = call_user_func($this->callbackHandler['error'], $this);
    }
    if (!$ok) {
      if (!$this->isOK) {
#
### If not valid, return an error message to the tool consumer if a return URL is provided
#
        if (!empty($this->return_url)) {
          $this->error = $this->return_url;
          if (strpos($this->error, '?') === FALSE) {
            $this->error .= '?';
          } else {
            $this->error .= '&';
          }
          if ($this->debugMode && !is_null($this->reason)) {
            $this->error .= 'lti_errormsg=' . urlencode("Debug error: $this->reason");
          } else {
            $this->error .= 'lti_errormsg=' . urlencode($this->message);
            if (!is_null($this->reason)) {
              $this->error .= '&lti_errorlog=' . urlencode("Debug error: $this->reason");
            }
          }
        } else if ($this->debugMode) {
          $this->error = $this->reason;
        }
        if (is_null($this->error)) {
          $this->error = $this->message;
        }
        if ((substr($this->error, 0, 7) == 'http://') || (substr($this->error, 0, 8) == 'https://')) {
          header("Location: {$this->error}");
        } else {
          echo "Error: {$this->error}";
        }
      } else if (!is_null($this->redirectURL)) {
        header("Location: {$this->redirectURL}");
      } else if (!is_null($this->output)) {
        echo $this->output;
      }
    }
    }

    /**
     * Check the authenticity of the LTI launch request.
     *
     * The consumer, resource link and user objects will be initialised if the request is valid.
     *
     * @return boolean True if the request has been successfully validated.
     */
    private function authenticate() {
        $doSaveConsumer = FALSE;

        // Check all required launch parameter constraints
        $this->isOK = isset($_POST['oauth_consumer_key']);
        if ($this->isOK) {
            $this->isOK = isset($_POST['lti_message_type']) && ($_POST['lti_message_type'] == 'basic-lti-launch-request');
        }
        if ($this->isOK) {
            $this->isOK = isset($_POST['lti_version']) && ($_POST['lti_version'] == self::LTI_VERSION);
        }
        if ($this->isOK) {
            $this->isOK = isset($_POST['resource_link_id']) && (strlen(trim($_POST['resource_link_id'])) > 0);
        }

        // Check consumer key
        if ($this->isOK) {
            $this->consumer = new Lti_ToolConsumer($_POST['oauth_consumer_key'], $this->data_connector);
            $this->isOK = ! is_null($this->consumer->created);
            if ($this->debugMode && !$this->isOK) {
                $this->reason = 'Invalid consumer key.';
            }
        }

    $now = time();
    if ($this->isOK) {
      $today = date('Y-m-d', $now);
      if (is_null($this->consumer->last_access)) {
        $doSaveConsumer = TRUE;
      } else {
        $last = date('Y-m-d', $this->consumer->last_access);
        $doSaveConsumer = $doSaveConsumer || ($last != $today);
      }
      $this->consumer->last_access = $now;
      $this->isOK = $this->consumer->enabled;
      if ($this->debugMode && !$this->isOK) {
        $this->reason = 'Tool consumer has not been enabled by the tool provider.';
      }
    }
    if ($this->isOK && $this->consumer->protected) {
      if (!is_null($this->consumer->consumer_guid)) {
        $this->isOK = isset($_POST['tool_consumer_instance_guid']) && !empty($_POST['tool_consumer_instance_guid']) &&
           ($this->consumer->consumer_guid == $_POST['tool_consumer_instance_guid']);
        if ($this->debugMode && !$this->isOK) {
          $this->reason = 'Request is from an invalid tool consumer.';
        }
      } else {
        $this->isOK = isset($_POST['tool_consumer_instance_guid']);
        if ($this->debugMode && !$this->isOK) {
          $this->reason = 'A tool consumer GUID must be included in the launch request.';
        }
      }
    }
    if ($this->isOK) {
      $this->isOK = is_null($this->consumer->enable_from) || ($this->consumer->enable_from <= $now);
      if ($this->isOK) {
        $this->isOK = is_null($this->consumer->enable_until) || ($this->consumer->enable_until > $now) || ($this->consumer->without_end_date == true);
        if ($this->debugMode && !$this->isOK) {
          $this->reason = 'Tool consumer access has expired.';
        }
      } else if ($this->debugMode) {
        $this->reason = 'Tool consumer access is not yet available.';
      }
    }

    if ($this->isOK) {
        try {
            $store  = new Lti_OAuthDataStore($this);
            $server = new OAuthServer($store);
            $method = new OAuthSignatureMethod_HMAC_SHA1();

            $server->add_signature_method($method);
            $request = OAuthRequest::from_request();
            $res = $server->verify_request($request);
        } catch (Exception $e) {
            $this->isOK = FALSE;
            if (empty($this->reason)) {
              $this->reason = 'OAuth signature check failed - perhaps an incorrect secret or timestamp.';
            }
        }
    }
#
### Validate launch parameters
#
    if ($this->isOK) {
      $invalid_parameters = array();
      foreach ($this->constraints as $name => $constraint) {
        $ok = TRUE;
        if ($constraint['required']) {
          if (!isset($_POST[$name]) || (strlen(trim($_POST[$name])) <= 0)) {
            $invalid_parameters[] = $name;
            $ok = FALSE;
          }
        }
        if ($ok && !is_null($constraint['max_length']) && isset($_POST[$name])) {
          if (strlen(trim($_POST[$name])) > $constraint['max_length']) {
            $invalid_parameters[] = $name;
          }
        }
      }
      if (count($invalid_parameters) > 0) {
        $this->isOK = FALSE;
        if (empty($this->reason)) {
          $this->reason = 'Invalid parameter(s): ' . implode(', ', $invalid_parameters) . '.';
        }
      }
    }

    if ($this->isOK) {
      $this->consumer->defaultEmail = $this->defaultEmail;
        #
        ### Set the request context/resource link
        #
      $this->resource_link = new Lti_ResourceLink($this->consumer, trim($_POST['resource_link_id']));

      if (isset($_POST['context_id'])) {
        $this->resource_link->lti_context_id = trim($_POST['context_id']);
      }
      $this->resource_link->lti_resource_id = trim($_POST['resource_link_id']);
      $title = '';
      if (isset($_POST['context_title'])) {
        $title = trim($_POST['context_title']);
      }
      if (isset($_POST['resource_link_title']) && (strlen(trim($_POST['resource_link_title'])) > 0)) {
        if (!empty($title)) {
          $title .= ': ';
        }
        $title .= trim($_POST['resource_link_title']);
      }
      if (empty($title)) {
        $title = "Course {$this->resource_link->getId()}";
      }
      $this->resource_link->title = $title;
        // Save LTI parameters
      foreach ($this->lti_settings_names as $name) {
        if (isset($_POST[$name])) {
          $this->resource_link->setSetting($name, $_POST[$name]);
        } else {
          $this->resource_link->setSetting($name, NULL);
        }
      }
        // Delete any existing custom parameters
      foreach ($this->resource_link->getSettings() as $name => $value) {
        if (strpos($name, 'custom_') === 0) {
          $this->resource_link->setSetting($name);
        }
      }
        // Save custom parameters
      foreach ($_POST as $name => $value) {
        if (strpos($name, 'custom_') === 0) {
          $this->resource_link->setSetting($name, $value);
        }
      }

        //Set the user instance
      $user_id = '';
      if (isset($_POST['user_id'])) {
        $user_id = trim($_POST['user_id']);
      }
      $this->user = new Lti_User($this->resource_link, $user_id);
        #
        ### Set the user name
        #
      $firstname = (isset($_POST['lis_person_name_given'])) ? $_POST['lis_person_name_given'] : '';
      $lastname = (isset($_POST['lis_person_name_family'])) ? $_POST['lis_person_name_family'] : '';
      $fullname = (isset($_POST['lis_person_name_full'])) ? $_POST['lis_person_name_full'] : '';
      $this->user->setNames($firstname, $lastname, $fullname);
        #
        ### Set the user email
        #
      $email = (isset($_POST['lis_person_contact_email_primary'])) ? $_POST['lis_person_contact_email_primary'] : '';
      $this->user->setEmail($email, $this->defaultEmail);
        #
        ### Set the user roles
        #
      if (isset($_POST['roles'])) {
        $this->user->roles = Lti_ToolProvider::parseRoles($_POST['roles']);
      }

      // Save the user instance
      $saveUser = false;
      if (isset($_POST['lis_result_sourcedid'])) {
        if ($this->user->lti_result_sourcedid != $_POST['lis_result_sourcedid']) {
          $this->user->lti_result_sourcedid = $_POST['lis_result_sourcedid'];
          $saveUser = true;
        }
      } else if (!empty($this->user->lti_result_sourcedid)) {
        $this->user->delete();
      }
        #
        ### Initialise the consumer and check for changes
        #
      if ($this->consumer->lti_version != $_POST['lti_version']) {
        $this->consumer->lti_version = $_POST['lti_version'];
        $doSaveConsumer = TRUE;
      }
      if (isset($_POST['tool_consumer_instance_name'])) {
        if ($this->consumer->consumer_name != $_POST['tool_consumer_instance_name']) {
          $this->consumer->consumer_name = $_POST['tool_consumer_instance_name'];
          $doSaveConsumer = TRUE;
        }
      }
      if (isset($_POST['tool_consumer_info_product_family_code'])) {
        $version = $_POST['tool_consumer_info_product_family_code'];
        if (isset($_POST['tool_consumer_info_version'])) {
          $version .= "-{$_POST['tool_consumer_info_version']}";
        }
        // do not delete any existing consumer version if none is passed
        if ($this->consumer->consumer_version != $version) {
          $this->consumer->consumer_version = $version;
          $doSaveConsumer = TRUE;
        }
      } else if (isset($_POST['ext_lms']) && ($this->consumer->consumer_name != $_POST['ext_lms'])) {
        $this->consumer->consumer_version = $_POST['ext_lms'];
        $doSaveConsumer = TRUE;
      }
      if (isset($_POST['tool_consumer_instance_guid']) && is_null($this->consumer->consumer_guid)) {
        $this->consumer->consumer_guid = $_POST['tool_consumer_instance_guid'];
        $doSaveConsumer = TRUE;
      }
      if (isset($_POST['launch_presentation_css_url'])) {
        if ($this->consumer->css_path != $_POST['launch_presentation_css_url']) {
          $this->consumer->css_path = $_POST['launch_presentation_css_url'];
          $doSaveConsumer = TRUE;
        }
      } else if (isset($_POST['ext_launch_presentation_css_url']) &&
         ($this->consumer->css_path != $_POST['ext_launch_presentation_css_url'])) {
        $this->consumer->css_path = $_POST['ext_launch_presentation_css_url'];
        $doSaveConsumer = TRUE;
      } else if (!empty($this->consumer->css_path)) {
        $this->consumer->css_path = NULL;
        $doSaveConsumer = TRUE;
      }
    }
        #
        ### Persist changes to consumer
        #
    if ($doSaveConsumer) {
      $this->consumer->save();
    }

    if ($this->isOK) {
      $this->isOK = $this->checkForShare(); //Check if a share arrangement is in place for this resource link

      // Persist changes to resource link
      $this->resource_link->save();
      if($saveUser){

          $consumer_key = $this->consumer->getKey();
          DB_ORM::delete('lti_user')->where('consumer_key', '=', $consumer_key)->execute();

          $this->user->save();
      }
    }
    return $this->isOK;
  }

/**
 * Check if a share arrangement is in place.
 *
 * @return boolean True if no error is reported
 */
  private function checkForShare() {

    $ok = TRUE;
    $doSaveResourceLink = TRUE;

    $key = $this->resource_link->primary_consumer_key;
    $id = $this->resource_link->primary_resource_link_id;

    $shareRequest = isset($_POST['custom_share_key']) && !empty($_POST['custom_share_key']);
    if ($shareRequest) {
      if (!$this->allowSharing) {
        $ok = FALSE;
        $this->reason = 'Your sharing request has been refused because sharing is not being permitted.';
      } else {
// Check if this is a new share key
        $share_key = new Lti_ResourceLinkShareKey($this->resource_link, $_POST['custom_share_key']);
        if (!is_null($share_key->primary_consumer_key) && !is_null($share_key->primary_resource_link_id)) {
// Update resource link with sharing primary resource link details
          $key = $share_key->primary_consumer_key;
          $id = $share_key->primary_resource_link_id;
          $ok = ($key != $this->consumer->getKey()) || ($id != $this->resource_link->getId());
          if ($ok) {
            $this->resource_link->primary_consumer_key = $key;
            $this->resource_link->primary_resource_link_id = $id;
            $this->resource_link->share_approved = $share_key->auto_approve;
            $ok = $this->resource_link->save();
            if ($ok) {
              $doSaveResourceLink = FALSE;
              $this->user->getResourceLink()->primary_consumer_key = $key;
              $this->user->getResourceLink()->primary_resource_link_id = $id;
              $this->user->getResourceLink()->share_approved = $share_key->auto_approve;
              $this->user->getResourceLink()->updated = time();
// Remove share key
              $share_key->delete();
            } else {
              $this->reason = 'An error occurred initialising your share arrangement.';
            }
          } else {
            $this->reason = 'It is not possible to share your resource link with yourself.';
          }
        }
        if ($ok) {
          $ok = !is_null($key);
          if (!$ok) {
            $this->reason = 'You have requested to share a resource link but none is available.';
          } else {
            $ok = (!is_null($this->user->getResourceLink()->share_approved) && $this->user->getResourceLink()->share_approved);
            if (!$ok) {
              $this->reason = 'Your share request is waiting to be approved.';
            }
          }
        }
      }
    } else {
// Check no share is in place
      $ok = is_null($key);
      if (!$ok) {
        $this->reason = 'You have not requested to share a resource link but an arrangement is currently in place.';
      }
    }

// Look up primary resource link
    if ($ok && !is_null($key)) {
      $consumer = new Lti_ToolConsumer($key, $this->data_connector);
      $ok = !is_null($consumer->created);
      if ($ok) {
        $resource_link = new Lti_ResourceLink($consumer, $id);
        $ok = !is_null($resource_link->created);
      }
      if ($ok) {
        if ($doSaveResourceLink) {
          $this->resource_link->save();
        }
        $this->resource_link = $resource_link;
      } else {
        $this->reason = 'Unable to load resource link being shared.';
      }
    }

    return $ok;

  }

}
?>
