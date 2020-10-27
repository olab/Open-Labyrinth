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


class Model_Leap_DForum_Messages_Forum extends DB_ORM_Model {

    public function __construct() {
        parent::__construct();

        $this->fields = array(
            'id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            'id_forum' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            'id_message' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
        );
    }

    public static function data_source() {
        return 'default';
    }

    public static function table() {
        return 'dforum_messages_forum';
    }

    public static function primary_key() {
        return array('id');
    }

    public function createRelations($forumId, $messageId) {
        $this->id_forum = $forumId;
        $this->id_message = $messageId;

        $this->save();
    }

    public function getAllMessagesIdByForumId($forumId) {
        $builder = DB_SQL::select('default')
            ->from('dforum_messages_forum')
            ->where('id_forum', '=', (int)$forumId);
        $result = $builder->query();


        if ($result->is_loaded())
        {
            $ids = array();
            foreach($result as $record){
                $ids[] = $record['id_message'];
            }
            return $ids;
        }

        return null;
    }

    public function deleteRelations($messageId) {
        $builder = DB_ORM::delete('dforum_messages_forum')->where('id_message', '=', (int)$messageId);
        $builder->execute();
    }

}