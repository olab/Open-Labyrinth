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


class Model_Leap_DTopic_Messages extends DB_ORM_Model {

    public function __construct() {
        parent::__construct();

        $this->fields = array(
            'id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            'topic_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
                'savable' => TRUE
            )),
            'text' => new DB_ORM_Field_Text($this, array(
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            'user_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            'date' => new DB_ORM_Field_DateTime($this, array(
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            'type' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 1,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            'isEdit' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 1,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
        );
    }

    public static function data_source() {
        return 'default';
    }

    public static function table() {
        return 'dtopic_messages';
    }
    public static function primary_key() {
        return array('id');
    }

    public function createMessage($topicId, $message, $type = 0) {
        $this->topic_id = $topicId;
        $this->text = $message;
        $this->user_id = Auth::instance()->get_user()->id;
        $this->date = date('Y-m-d H:i:s');

        $this->type = $type;

        $this->save();

        return $this->getLastAddedRecord();
    }

    public function deleteMessage($messageId) {
        $builder = DB_ORM::delete('dtopic_messages')->where('id', '=', (int)$messageId);
        $builder->execute();
    }

    public function deleteAllMessages($topicId) {
        DB_SQL::delete('default')
                ->from($this->table())
                ->where('topic_id', '=', $topicId)
                ->execute();
    }

    public function updateMessage($messageId, $messageText) {

        $this->id = $messageId;
        $this->load();

        $this->text = $messageText;
        $this->date = date('Y-m-d H:i:s');
        $this->isEdit = 1;

        $this->save();
    }

    public function getLastAddedRecord(){
        $builder = DB_SQL::select('default')->from($this->table())->order_by('id', 'DESC')->limit(1);
        $result = $builder->query();

        if($result->is_loaded()) {
            $forum = DB_ORM::model('dtopic_messages', array($result[0]['id']));
            return $forum->id;
        }
        return NULL;
    }

    public function getMessage($messageId) {
        $this->id = $messageId;
        $this->load();
        $message = NULL;

        if ($this->is_loaded()) {
            $message = $this->text;
        }

        return $message;
    }

    public function getNewMessages($forumId, $lastMessageId) {
        $builder = DB_SQL::select('default',array(DB::expr('dforum_messages.*') , DB::expr('u.nickname as nickname') ))
            ->from($this->table())
            ->join('LEFT','dforum_messages_forum','dfm' )
            ->on('dfm.id_message','=','dforum_messages.id')
            ->join('LEFT','users','u' )
            ->on('dforum_messages.user_id','=','u.id')
            ->where('dforum_messages.id','>',$lastMessageId)
            ->where('dfm.id_forum','=',$forumId);
        $result = $builder->query();


        if($result->is_loaded()) {
            $messages = array();

            foreach($result as $record) {
                $messages[] = $record;
            }

            return $messages;
        }

        return NULL;
    }

    public function getEditedMessages($forumId) {
        $builder = DB_SQL::select('default',array(DB::expr('dforum_messages.*') , DB::expr('u.nickname as nickname') ))
            ->from($this->table())
            ->join('LEFT','dforum_messages_forum','dfm' )
            ->on('dfm.id_message','=','dforum_messages.id')
            ->join('LEFT','users','u' )
            ->on('dforum_messages.user_id','=','u.id')
            ->where('dforum_messages.isEdit','=', 1)
            ->where('dfm.id_forum','=',$forumId);
        $result = $builder->query();


        if($result->is_loaded()) {

            $messages = array();

            foreach($result as $record) {
                $messages[] = $record;
            }
            return $messages;
        }

        return NULL;
    }


}