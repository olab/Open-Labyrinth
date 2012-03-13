<?php defined('SYSPATH') or die('No direct script access.');

class Model_User extends Model_Auth_User {

    protected $_belongs_to = array(
        'language' => array(
            'model' => 'language',
            'foreign_key' => 'language_id'),
        'type' => array(
            'model' => 'type',
            'foreign_key' => 'type_id'),
    );
    
    protected $_has_many = array(
        'user_tokens' => array('model' => 'user_token'),
	'roles'       => array('model' => 'role', 'through' => 'roles_users'),
        'groups'      => array('model' => 'group', 'through' => 'usersGroups'),
    );

    public function CreateUser($username, $password, $nickname, $email, $type, $language) {
        if ($username != '' and
                $password != '' and
                $nickname != '' and
                $email != '' and
                $type != 0 and
                $language != 0) {

            $users = ORM::factory('user');
            $users->username = $username;
            $users->password = $password;
            $users->email = $email;
            $users->nickname = $nickname;
            $users->language_id = $language;
            $users->type_id = $type;

            $users->create();

            $users->add('roles', ORM::factory('role', 1));
        }
    }

    public function UpdateUser($id, $password, $nickname, $email, $type, $language) {
        $user = ORM::factory('user', $id);  
        if ($user) {
            if ($password != '') {
                $user->password = $password;
            }
            
            if($nickname != '') {
                $user->nickname = $nickname;
            }
            
            if($email != '') {
                $user->email = $email;
            }
            
            if($type != null) {
                $user->type_id = $type;
            }
            
            if($language != null) {
                $user->language_id = $language;
            }
            
            $user->update();
        }
    }

}
