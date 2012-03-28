<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for user_sessions table in database 
 */
class Model_Leap_User_Session extends DB_ORM_Model {

    public function __construct() {
        parent::__construct();

        $this->fields = array(
            'id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 10,
                'nullable' => FALSE,
            )),
            
            'user_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 10,
                'nullable' => FALSE,
                'unsigned' => TRUE,
            )),
            
            'map_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 10,
                'nullable' => FALSE,
                'unsigned' => TRUE,
            )),
            
            'start_time' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            
            'user_ip' => new DB_ORM_Field_String($this, array(
                'max_length' => 50,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
        );
    }

    public static function data_source() {
        return 'default';
    }

    public static function table() {
        return 'user_sessions';
    }

    public static function primary_key() {
        return array('id');
    }
    
    public function createSession($userId, $mapId, $startTime, $userIp) {
        $builder = DB_ORM::insert('user_session')
                ->column('user_id', $userId)
                ->column('map_id', $mapId)
                ->column('start_time', $startTime)
                ->column('user_ip', $userIp);
        
        return $builder->execute();
    }
}

?>