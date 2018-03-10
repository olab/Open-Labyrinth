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
 * but WITHOUT ANY WARRANTY; withoeut even the implied warranty of
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

return array ("mail_from" => "admin@localhost.com", "from_name" => "Open-Labyrinth",  

			"add_msg" => array(
                'author' => 'OpenLabyrinth: You added message in to the forum',
                'other' => 'OpenLabyrinth: Added new message in to the forum',
                'action' => 'commented on'
             ),
			"delete_msg" => array(
                'author' => 'OpenLabyrinth: You deleted message from forum',
                'other'  => 'OpenLabyrinth: The message deleted from forum',
                'action' => 'delete from'
            ),
			"update_msg" => array(
                'author' => 'OpenLabyrinth: You edited message in to the forum',
                'other'  => 'OpenLabyrinth: Edit message in to the forum',
                'action' => 'updated message on'
            ),
            "update_forum" => array(
                'author' => 'OpenLabyrinth: You edited forum -',
                'other' => 'OpenLabyrinth: Forum edited -',
                'action' => 'updated forum'
            ),
			"create_forum" => array(
                'author' => 'OpenLabyrinth: You Create new forum',
                'activate_user' => '. Please wait until the administrator approved it.',
                'activate_admin' => 'OpenLabyrinth: User create new topic in forum, please approve it.',
                'other' => 'OpenLabyrinth: Referred to the forum',
                'action' => 'created new forum'
            ),
			"delete_forum" => array(
                'author' => 'OpenLabyrinth: You deleted Forum from OpenLabyrinth -',
                'other' => 'OpenLabyrinth: Forum from OpenLabyrinth has deleted -',
                'action' => 'delete forum'
            ),
			'deleteUserFromForum' => array(
                'author' => 'OpenLabyrinth: You delete user(s) from forum',
                'other' => 'OpenLabyrinth: You have removed from the forum -',
                'action' => 'delete user from forum'
            ),
            'addUserToForum' => array(
                'author' =>  'OpenLabyrinth: You add user to forum',
                'other' =>  'OpenLabyrinth: Referred to the forum',
                'action' => 'add user to forum'
            ),
            'addGroupToForum' => array(
                'author' =>  'OpenLabyrinth: You add group to forum',
                'other' =>  'OpenLabyrinth: Group has been added to the forum',
                'action' => 'add group to forum'
            ),
            'deleteGroupFromForum' => array(
                'author' =>  'OpenLabyrinth: You delete group to forum',
                'other' =>  'OpenLabyrinth: Group has been deleted to the forum',
                'action' => 'delete group to forum'
            )
);