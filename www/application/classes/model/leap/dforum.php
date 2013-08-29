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


class Model_Leap_DForum extends DB_ORM_Model {

    public function __construct() {
        parent::__construct();

        $this->fields = array(
            'id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
                'unsigned' => TRUE,
            )),
            'name' => new DB_ORM_Field_String($this, array(
                'max_length' => 200,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            'closed' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 1,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            'date' => new DB_ORM_Field_DateTime($this, array(
                'max_length' => 250,
                'nullable' => FALSE,
            )),
            'author_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            'settings' => new DB_ORM_Field_Text($this, array(
                'nullable' => FALSE,
            )),
            'security_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
        );
    }

    public static function data_source() {
        return 'default';
    }

    public static function table() {
        return 'dforum';
    }

    public static function primary_key() {
        return array('id');
    }

    public function createForum($forumName, $security) {
        $this->name = $forumName;
        $this->closed = 0;
        $this->date = date('Y-m-d h:i:s');
        $this->author_id = Auth::instance()->get_user()->id;
        $this->settings = '';
        $this->security_id = $security;

        $this->save();

        return $this->getLastAddedForumRecord();
    }

    public function getAllOpenForums() {
        $builder = DB_SQL::select('default', array(DB_SQL::expr('forum.*'), DB_SQL::expr('u.nickname as author_name')))

            ->from($this->table(), 'forum')
            ->join('LEFT', 'users', 'u')
            ->on('forum.author_id', '=', 'u.id')
            ->where('security_id', '=', 0);

        $result = $builder->query();

        if($result->is_loaded()) {
            $forums = array();

            foreach($result as $key => $record) {
                $lastInfo = $this->getInfoAboutLastMessage($record['id']);
                $message_count = $this->getMessageCountByForum($record['id']);

                $forums[$key] = $record;

                $forums[$key]['lastMessageInfo'] = $lastInfo;
                $forums[$key]['message_count'] = $message_count['message_count'];
            }

            return $forums;
        }

        return NULL;
    }

    public function getAllForums($sortBy = null, $typeSort = null){

        $where = '';
        $join = '';

        $type = ($typeSort == 0) ? 'ASC' : 'DESC';

        $columName = '';

        switch($sortBy) {
            case 1 :
                $columName = 'forum.name';
                break;
            case 2 :
                $columName = 'users_count';
                break;
            case 3 :
                $columName = 'messages_count';
                break;
            case 4 :
                $columName = 'message_date';
                break;
        }

        $orderBy = $columName . ' ' . $type;


        if (Auth::instance()->get_user()->type->name != 'superuser') {

            $join = ' LEFT JOIN
                        dforum_users  as dfuu ON dfuu.id_forum = forum.id
                      LEFT JOIN
                        dforum_groups as dfg ON dfg.id_forum = forum.id
                      LEFT JOIN
                        user_groups   as ug  ON ug.group_id  = dfg.id_group ';

            $where = ' AND (forum.author_id = ' . Auth::instance()->get_user()->id . '
                         OR dfuu.id_user = ' . Auth::instance()->get_user()->id . ' OR ug.user_id = ' . Auth::instance()->get_user()->id .')';
        }

        $connection = DB_Connection_Pool::instance()->get_connection('default');
        $result = $connection->query("
            SELECT
              forum. * , u.nickname AS author_name,
              COUNT( DISTINCT(dmes.id) ) AS messages_count,
              lastm.id AS message_id,
              lastm.date AS message_date,
              lastm.text AS message_text,
              lu.nickname AS message_nickname,
              COUNT(DISTINCT(dfu.id)) AS users_count
            FROM
              dforum_messages AS dmes, dforum_users AS dfu, dforum AS forum
            JOIN
              dforum_messages AS lastm
            LEFT JOIN
              users AS u ON forum.author_id = u.id
            LEFT JOIN
              users AS lu ON lastm.user_id = lu.id
            $join
            WHERE
              dmes.forum_id = forum.id
            AND
              dfu.id_forum = forum.id
            AND
              lastm.date = (
                  SELECT
                    m.date
                  FROM
                    dforum_messages AS m
                  WHERE
                    m.forum_id = forum.id
                  ORDER BY
                    m.date DESC
                  LIMIT 1 )
            $where
            GROUP BY
              forum.id
            ORDER BY
              $orderBy
        ");

        $res = array();

        if($result != null && $result->is_loaded()) {

            foreach($result as $record) {
                $res[] = $record;
            }
            return $res;
        }

        return NULL;
    }

    public function getAllPrivateForums(){
        $result = null;

        if(Auth::instance()->get_user()->type->name == 'superuser') {
            $builder = DB_SQL::select('default', array(DB_SQL::expr('forum.*'), DB_SQL::expr('u.nickname as author_name')))
                ->from($this->table(), 'forum')
                ->where('security_id', '=', 1)
                ->join('LEFT', 'users', 'u')
                ->on('forum.author_id', '=', 'u.id');

            $result = $builder->query();
        } else {
            $connection = DB_Connection_Pool::instance()->get_connection('default');
            $result = $connection->query('SELECT forum.*, u.nickname as author_name FROM ' . $this->table() . ' as forum
                                               LEFT JOIN users as u ON forum.author_id = u.id
                                               LEFT JOIN dforum_users  as dfu ON dfu.id_forum = forum.id
                                               LEFT JOIN dforum_groups as dfg ON dfg.id_forum = forum.id
                                               LEFT JOIN user_groups   as ug  ON ug.group_id  = dfg.id_group
                                               WHERE forum.security_id = 1 AND (forum.author_id = ' . Auth::instance()->get_user()->id . ' OR dfu.id_user = ' . Auth::instance()->get_user()->id . ' OR ug.user_id = ' . Auth::instance()->get_user()->id . ')
                                          GROUP BY forum.id');
        }

        if($result != null && $result->is_loaded()) {
            $forums = array();

            foreach($result as $key => $record){
                $lastInfo = $this->getInfoAboutLastMessage($record['id']);
                $message_count = $this->getMessageCountByForum($record['id']);

                $forums[$key] = $record;

                $forums[$key]['lastMessageInfo'] = $lastInfo;
                $forums[$key]['message_count'] = $message_count['message_count'];
            }

            return $forums;
        }

        return NULL;
    }

    public function getForumById($forumId) {
        $builder = DB_SQL::select('default', array(DB_SQL::expr('forum.*'), DB_SQL::expr('u.nickname as author_name')))

            ->from($this->table(), 'forum')
            ->join('LEFT', 'users', 'u')
            ->on('forum.author_id', '=', 'u.id')
            ->where('forum.id', '=', $forumId);

        $result = $builder->query();

        if ($result->is_loaded()) {
            $forum = array();
            $forum = $result[0];

            $forum['messages'] = $this->getAllMessageByForum($result[0]['id']);

            return $forum;
        }

        return NULL;
    }

    public function getAllMessageByForum($forumId) {
        $builder = DB_SQL::select('default',array(DB_SQL::expr('messages.*'),DB_SQL::expr('u.nickname as author_name'),
            DB_SQL::expr('u.id as author_id')))
            ->from('dforum_messages', 'messages')
            ->join('LEFT', 'users', 'u')
            ->on('u.id', '=', 'messages.user_id')
            ->where('messages.forum_id','=',$forumId)
            ->order_by('messages.id');
        $result = $builder->query();

        if($result->is_loaded()) {
            return $result;
        }

        return NULL;
    }

    public function getInfoAboutLastMessage($forumId, $userId = null) {
        $builder = DB_SQL::select('default', array(DB_SQL::expr('u.nickname'), DB_SQL::expr('u.id'), DB_SQL::expr('dfm.date'), DB_SQL::expr('dfm.text'), DB_SQL::expr('dfm.id as message_id')))
            ->from('dforum_messages', 'dfm')
            ->join('LEFT', 'users', 'u')
            ->on('u.id', '=', 'dfm.user_id')
            ->where('dfm.forum_id', '=', $forumId)
            ->order_by('dfm.id', 'DESC')
            ->limit(1);

        if ($userId != null){
            $builder->where('dfm.user_id', '=', $userId);
        }

        $result = $builder->query();

        if($result->is_loaded()) {
            return $result[0];
        }

        return NULL;
    }

    public function getMessageCountByForum($forumId) {
        $builder = DB_SQL::select('default',array(DB::expr('COUNT(id) as message_count')))
            ->from('dforum_messages', 'messages')
            ->where('messages.forum_id','=',$forumId);
        $result = $builder->query();

        if($result->is_loaded()) {
            return $result[0];
        }

        return NULL;
    }


    public function getLastAddedForumRecord() {
        $builder = DB_SQL::select('default')->from($this->table())->order_by('id', 'DESC')->limit(1);
        $result = $builder->query();

        if($result->is_loaded()) {
            $forum = DB_ORM::model('dforum', array($result[0]['id']));
            return $forum->id;
        }
        return NULL;
    }

    public function getForumNameAndSecurityType($forumId) {
        $this->id = $forumId;
        $this->load();
        $forumName = NULL;
        $security = NULL;

        if($this->is_loaded()) {
            $forumName = $this->name;
            $security = $this->security_id;

            return array($forumName , $security);
        }
        return NULL;
    }

    public function updateForum($forumName, $security, $forumId) {
        $this->id = $forumId;
        $this->load();

        $this->name = $forumName;
        $this->security_id = $security;

        $this->save();
    }

    public function deleteForum($forumId) {
        DB_ORM::model('dforum_messages')->deleteAllMessages($forumId);
        DB_ORM::model('dforum_users')->deleteAllUsers($forumId);
        DB_ORM::model('dforum_groups')->deleteAllGroups($forumId);

        DB_SQL::delete('default')
                ->from($this->table())
                ->where('id', '=', $forumId)
                ->execute();
    }

    public function getForumAuthor($forumId) {
        $this->id = $forumId;
        $this->load();
        $authorInfo = NULL;

        if($this->is_loaded()) {
            $authorInfo = DB_ORM::model('user', array($this->author_id));

            return $authorInfo;
        }
        return $authorInfo;
    }
}