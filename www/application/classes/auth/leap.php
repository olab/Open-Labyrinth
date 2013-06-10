<?php
/**
 * Open Labyrinth [ http://www.openlabyrinth.ca ]
 *
 * Open Labyrinth is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Open Labyrinth is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Open Labyrinth.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @copyright Copyright 2012 Open Labyrinth. All Rights Reserved.
 *
 */
defined('SYSPATH') or die('No direct access allowed.');

class Auth_Leap extends Auth {

    protected function _login($username, $password, $remember) {
        if (is_string($username) and
                is_string($password) and
                $username != '' and
                $password != '') {

            $user = DB_ORM::model('user');
            $user->getUserByName($username);

            $hashPassword = '';
            if(is_string($password)) {
                $hashPassword = $this->hash($password);
            }
            
            if ($user->password === $hashPassword || $user->password === $password) {

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

        return ($user->password === $this->hash($password));
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