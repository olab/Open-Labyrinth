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
defined('SYSPATH') or die('No direct script access.');
require_once(DOCROOT.'/twitter/src/twitter.class.php');

class Notification
{
    function sendTwit(mysqli $connection, $subject, $to = ''){
        $query = 'SELECT * FROM twitter_credits';
        $result = mysqli_query($connection, $query);
        $credits = mysqli_fetch_array($result);
        $twitter = new Twitter($credits['API_key'], $credits['API_secret'], $credits['Access_token'], $credits['Access_token_secret']);
        try {
            $twitter->send($to.' '.$subject);
        } catch (TwitterException $e) {
            echo "Error: ", $e->getMessage();
        }
    }

    function sendEMail($to, $subject, $from){
        $message = 'All you need to know in subject.';
        $headers = 'From: '.$from."\r\n".'Reply-To: '.$from."\r\n".'X-Mailer: PHP/'.phpversion();

        mail($to, $subject, $message, $headers);
    }
}