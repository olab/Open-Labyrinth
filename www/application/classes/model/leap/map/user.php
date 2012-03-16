<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for map_users table in database 
 */
class Model_Leap_Map_User extends DB_ORM_Model {

    public function __construct() {
        parent::__construct();

        $this->fields = array(
            'id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
                'unsigned' => TRUE,
            )),
            
            'map_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            
            'user_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
        );
        
        $this->relations = array(
            'user' => new DB_ORM_Relation_BelongsTo($this, array(
                'child_key' => array('user_id'),
                'parent_key' => array('id'),
                'parent_model' => 'user',
            )),
        );
    }

    public static function data_source() {
        return 'default';
    }

    public static function table() {
        return 'map_users';
    }

    public static function primary_key() {
        return array('id');
    }
    
    public function getAllUsers($mapId) {
        $builder = DB_SQL::select('default')->from($this->table())->where('map_id', '=', $mapId);
        $result = $builder->query();
        
        if($result->is_loaded()) {
            $users = array();
            foreach($result as $record) {
                $users[] = DB_ORM::model('user', array((int)$record['user_id']));
            }
            
            return $users;
        }
        
        return NULL;
    }
    
    public function checkUser($users, $userId) {
        if(count($users) > 0) {
            foreach($users as $record) {
                if($record->user_id == $userId) {
                    return TRUE;
                }
            }
            
            return FALSE;
        }
        
        return FALSE;
    }
}

?>