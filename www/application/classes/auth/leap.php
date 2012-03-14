<?php defined('SYSPATH') or die('No direct access allowed.');

class Auth_Leap extends Auth {

    protected function _login($username, $password, $remember) {
        if (is_string($username) and
                is_string($password) and
                $username != '' and
                $password != '') {

            $user = DB_ORM::model('user');
            $user->getUserByName($username);

            if ($user->password === $password) {

                // "Remember me" commented: OL does not use this function
                if ($remember === TRUE) {
                    /* $data = array(
                      'user_id' => $user->id,
                      'expires' => time() + $this->_config['lifetime'],
                      'user_agent' => sha1(Request::$user_agent),
                      );

                      Cookie::set('authautologin', $token->token, $this->_config['lifetime']); */
                }

                $this->complete_login($user);

                return TRUE;
            }

            return FALSE;
        }

        return FALSE;
    }

    public function check_password($password) {
        $user = $this->get_user();

        if (!$user)
            return FALSE;

        return ($user->password === $password);
    }

    public function password($user) {
        if (!is_object($user)) {
            $username = $user;

            $user = DB_ORM::model('user');
            $user->username = $username;
            $user->load();
        }

        return $user->password;
    }

}

?>