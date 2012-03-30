<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for user_responses table in database 
 */
class Model_Leap_User_Response extends DB_ORM_Model {

    public function __construct() {
        parent::__construct();

        $this->fields = array(
            'id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 10,
                'nullable' => FALSE,
            )),
            
            'question_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 10,
                'nullable' => FALSE,
                'unsigned' => TRUE,
            )),
            
            'session_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 10,
                'nullable' => FALSE,
                'unsigned' => TRUE,
            )),
            
            'response' => new DB_ORM_Field_String($this, array(
                'max_length' => 1000,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
        );
    }

    public static function data_source() {
        return 'default';
    }

    public static function table() {
        return 'user_responses';
    }

    public static function primary_key() {
        return array('id');
    }
    
    public function createResponse($sessionId, $questionId, $response) {
        $this->question_id = $questionId;
        $this->session_id = $sessionId;
        $this->response = $response;
        
        $this->save();
    }
    
    public function updateResponse($sessionId, $questionId, $response) {
        $builder = DB_SQL::select('default')
                ->from($this->table())
                ->where('session_id', '=', $sessionId, 'AND')
                ->where('question_id', '=', $questionId);
        $result = $builder->query();
        
        if($result->is_loaded()) {
            $resp = DB_ORM::model('user_response', array((int)$result[0]['id']));
            if($resp) {
                $resp->response = $response;
                $resp->save();
            }
        } else {
            $this->createResponse($sessionId, $questionId, $response);
        }
    }
    
    public function getResponce($sessionId, $questionId) {
        $builder = DB_SQL::select('default')
                ->from($this->table())
                ->where('session_id', '=', $sessionId, 'AND')
                ->where('question_id', '=', $questionId);
        $result = $builder->query();
        
        if($result->is_loaded()) {
            return DB_ORM::model('user_response', array((int)$result[0]['id']));
        }
        
        return NULL;
    }
}

?>