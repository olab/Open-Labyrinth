<?php                     
  
  Class UserHandler {
    
    private $user;
    private $olabUser; 
    private $db;
    
    //initialises user_handler
    public function __construct() {
      $this->user = null;
      $this->olabUser = new stdClass(); 
      $this->db = new LTIDatabase(); 
    }
    
    //returns the user
    public function getUser() {
      return $this->user;
    }
    
    //sets the user
    public function setUser($user) {
      $this->user = $user;
    }
    
    //gets the lti user details
    public function ltiUser($context) {
              
      $bltiUser = $context->row;
      $bltiInfo = $context->info;
                   
      $this->user = new stdClass();    
      $this->user->id = $bltiInfo['user_id'];     
      $this->user->name = $bltiUser['name']; 
      //$this->user->context_id = $bltiUser['context_id'];  
      $this->user->consumerrole = $bltiInfo['roles']; 
      $this->user->role = $this->getUserRole();  
      $this->user->key = $context->getUserKey();
      $this->user->consumer_key = $context->getConsumerKey();   
      $this->user->resource_key = $context->getResourceKey();
        
      $this->user->key = str_replace(":".$this->user->id, "", $this->user->key);
    }
    
    //matches the tool consumer's roles with olab's roles
    private function getUserRole() {
      
      if($this->user->consumerrole == 'Instructor') {
        return 'author';
	  } else if($this->user->consumerrole == 'Director') {
        return 'author';
	  } else if($this->user->consumerrole == 'Staff') {
        return 'reviewer';
	  } else if($this->user->consumerrole == 'Teacher') {
        return 'reviewer';
      } else {
        return 'learner';
      }
    }
    
    //Creates the user if it doesn't exist
    //1. Gets the id of the role 
    //2. Creates the new user in database
    //3. Gets the new user
    private function createOpenUser() {
      $query = "SELECT id FROM user_types WHERE `name`='".$this->user->role."'";
      $this->db->setQuery($query);
      
      $temp = $this->db->loadObject();
      if($temp == FALSE || $temp == NULL) {
        $this->user->roleid = 1;
      } else {
        $this->user->roleid = $temp->id;
      }
      
      $query = "SELECT id FROM `oauth_providers` WHERE `name`='consumer'";
      $this->db->setQuery($query);
      $oauth = $this->db->loadObject();
      $oauthId = 0;
      if($oauth != FALSE && $oauth != NULL) {
        $oauthId = $oauth->id;
      }
      
      $query = "INSERT INTO `users` (`username`, `password`, `nickname`, `language_id`, `type_id`, `oauth_provider_id`) VALUES ";
      $query .= "('".$this->user->id."', '".$this->createPassword($this->user->key)."', '".$this->user->consumerrole."', '1', ".$this->user->roleid.", ".$oauthId.")";
      $this->db->setQuery($query);
      $this->db->query();
      
      $query = "SELECT * FROM `users` WHERE `username`='".$this->user->id."'";
      $this->db->setQuery($query);
      $this->olabUser = $this->db->loadObject(); 
      
    }
    
    
    //Gets the user from the database. If the user doesn't exist
    //then it calls the function to create a new one .
    public function fetchOpenUser() {
      // Establish the context
      $query = "SELECT * FROM `users` WHERE `username`='".$this->user->id."'";
      $this->db->setQuery($query);
      $this->olabUser = $this->db->loadObject();   
      
      if($this->olabUser == NULL || $this->olabUser == FALSE) {
        $this->createOpenUser(); 
      }  
      
    }
    
    
    //Function to log-in the user. The code is from
    //application/controller/home.php
    public function loginUser() {
    
      $status = Auth::instance()->login($this->user->id, $this->user->key);
      $redirectURL = URL::base();    
      if (!$status) {                                                                    
          throw new Exception('You have entered the wrong username/password combination. Please try again.'); 
          exit();
      } else {
          $authUser = Auth::instance()->get_user();
          $loginCredentials = array("username"=>$this->user->id, "password"=>$this->user->key);
          $redirectURL = URL::base() . '?oauth=consumer' . Arr::get($loginCredentials, 'redirectURL', ''); // URL::base() . Arr::get($_POST, 'redirectURL', '');      
      }
      
      Request::initial()->redirect($redirectURL);
      return;
    }
    
    
    //Function to create the hashed password as it
    //is saved in database from Openlabyrinth
    public function createPassword($password) {
      $hashed = Auth::instance()->hash($password);
      return $hashed;
    }
    
  }

?>