<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for user_groups table in database 
 */
class Model_Leap_User_Group extends DB_ORM_Model {

    public function __construct() {
        parent::__construct();

        $this->fields = array(
            'id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
                'unsigned' => TRUE,
            )),
            
            'user_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
                'unsigned' => TRUE,
            )),
            
            'group_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
                'unsigned' => TRUE,
            )),
        );
    }
    
    public static function data_source() {
        return 'default';
    }

    public static function table() {
        return 'user_groups';
    }

    public static function primary_key() {
        return array('id');
    }
    
    public function add($groupId, $userId) {
        $this->group_id = $groupId;
        $this->user_id = $userId;
        
        $this->save();
    }
    
    public function remove($groupId, $userId) {
        $builder = DB_SQL::select('default')
                ->from($this->table())
                ->where('group_id', '=', $groupId, 'AND')
                ->where('user_id', '=', $userId, 'AND')
                ->column('id');
        $result = $builder->query();
        
        $this->id = (int)$result[0]['id'];
        $this->delete();
    }
}

?>
