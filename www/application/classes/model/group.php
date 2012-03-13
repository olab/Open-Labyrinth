<?php defined('SYSPATH') or die('No direct script access.');

class Model_Group extends ORM { 
    
    protected $_has_many = array(
        'users'      => array('model' => 'user', 'through' => 'usersGroups'),
    );
    
    public function CreateGroup($name) {
        if($name != '') {
            $this->name = $name;
            $this->create();
        }
    }
    
    public function UpdateGroup($id, $name) {
        $group = $this->where('id', '=', $id)->find();
        if($name != '') {
            $group->name = $name;
            $group->update();
        }
    }
}