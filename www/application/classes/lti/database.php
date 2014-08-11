<?php

//to modify accordingly 

define('DB_NAME', 'olab');
define('DB_HOSTNAME', 'localhost');
define('DB_USERNAME', 'admin');
define('DB_PASSWORD', 'psw');

Class LTIDatabase
{
    public $db = null;       //connection to the database
    private $query = null;    //the sql string
    private $result = null;   //the result of the query's execution
    private $data = null;     //the data returned from a selected query
  
    
    /**
     * Creates a new connection to the database
     */     
    public function __construct() {
      $this->db = mysqli_connect(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD);
      mysqli_select_db($this->db, DB_NAME); 
    }
    
    
    /**
     * Singleton pattern
     * If there's an object already created use this          
     */         
    public function getDB() {
      if($this->db == null) {
        new LTIDatabase();
      }  
      
      return $this;
    }
    
    
    /**
     * Sets the query to this object
     */         
    public function setQuery($query) {
      $this->data = null;
      $this->query = $query;
    }
    
    
    /**
     * Executes the query and returns the result     
     */         
    public function query() {
      mysqli_query($this->db, "SET NAMES 'utf8'");
      $this->result = mysqli_query($this->db, $this->query);
      return $this->result;
    }
    
    
    /**
     * Returns only one object from the result.
     * If the query had errors returns false.     
     */         
    public function loadObject() {
      
      $this->query();
      
      if($this->result == FALSE || $this->result == NULL) {
        return FALSE;
      }
      $this->data = mysqli_fetch_object($this->result);
      return $this->data;  
    } 
    
    
    /**
     * Returns the list of objects from the result.
     * If the query has errors returns false.     
     */         
    public function loadObjectList() {
      $this->query();
      
      if($this->result == FALSE || $this->result == NULL) {
        return FALSE;
      }
      
      $this->data = FALSE;
      
      while($row = mysqli_fetch_object($this->result)) {
        $this->data[] = $row;
      }
      
      return $this->data;      
    }
    
    
}
