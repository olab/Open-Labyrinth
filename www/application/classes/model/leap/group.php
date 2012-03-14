<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for groups table in database 
 */
class Model_Leap_Group extends DB_ORM_Model {

    public function __construct() {
        parent::__construct();

        $this->fields = array(
            'id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
                'unsigned' => TRUE,
            )),
            
            'name' => new DB_ORM_Field_String($this, array(
                'max_length' => 100,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
        );
        
        $this->relations = array(
            'users' => new DB_ORM_Relation_HasMany($this, array(
                'child_key' => array('group_id'),
                'child_model' => 'user_group',
                'parent_key' => array('id'),
            )),
        );
    }

    public static function data_source() {
        return 'default';
    }

    public static function table() {
        return 'groups';
    }

    public static function primary_key() {
        return array('id');
    }

    public function getAllGroupsId() {
        $builder = DB_SQL::select('default')->from($this->table())->column('id');
        $result = $builder->query();
        
        $ids = array();
        if ($result->is_loaded()) {
            foreach ($result as $record) {
                $ids[] = (int)$record['id'];
            }
        }

        return $ids;
    }
    
    public function getAllGroups() {
        $result = array();
        $ids = $this->getAllGroupsId();
        
        foreach($ids as $id) {
            $result[] = DB_ORM::model('group', array($id));
        }
        
        return $result;
    }
    
    public function createGroup($name) {
        $this->name = $name;
        $this->save();
    }
    
    public function getAllUsersInGroup($groupId) {
        $this->id = $groupId;
        $this->load();
        
        $result = array();
        foreach($this->users as $user) {
            $result[] = DB_ORM::model('user', array($user->user_id));
        }
        
        return $result;
    }
    
    public function getAllUsersOutGroup($groupId) {
        $this->id = $groupId;
        $this->load();
        
        $userIds = array();
        foreach($this->users as $user) {
            $userIds[] = (int)$user->user_id;
        }
        
        return DB_ORM::model('user')->getAllUserWithNotId($userIds);
    }
    
    public function updateGroup($id, $name) {
        $this->id = $id;
        $this->load();
        
        $this->name = $name;
        $this->save();
    }
}

?>
