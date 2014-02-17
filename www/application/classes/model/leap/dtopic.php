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


class Model_Leap_DTopic extends DB_ORM_Model {

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
            'status' => new DB_ORM_Field_Integer($this, array(
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
            'forum_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            'node_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 1,
                'nullable' => TRUE
            ))
        );
    }

    public static function data_source() {
        return 'default';
    }

    public static function table() {
        return 'dtopic';
    }

    public static function primary_key() {
        return array('id');
    }

    public function createTopic($topicName, $security, $status, $forum_id, $nodeId = null) {
        $this->name = $topicName;
        $this->status = $status;
        $this->date = date('Y-m-d h:i:s');
        $this->author_id = Auth::instance()->get_user()->id;
        $this->settings = '';
        $this->security_id = $security;
        $this->forum_id = $forum_id;
        $this->node_id = $nodeId;

        $this->save();

        return $this->getLastAddedTopicRecord();
    }

    public function getForumByTopicId($topicId) {

        $builder = DB_SQL::select('default', array(DB_SQL::expr('forum.*'), DB_SQL::expr('u.email as forumAuthor_email')))
            ->from($this->table(),'topic')
            ->join('LEFT', 'dforum', 'forum')
            ->on('forum.id', '=', 'topic.forum_id')
            ->join('LEFT','users','u')
            ->on('u.id','=','forum.author_id')
            ->where('topic.id', '=', $topicId)
            ->limit(1);

        $result = $builder->query();

        $forumInfo = array();
        if($result->is_loaded()) {
            foreach($result as $record){
                $forumInfo[] = $record;
            }
            return $forumInfo[0];
        }
        return $forumInfo;
    }

    public function getAllTopicsByForumId($forumId) {

        $builder = DB_SQL::select('default', array(DB_SQL::expr('topic.*'), DB_SQL::expr('u.nickname as author_name')))
                ->from($this->table(),'topic')
                ->join('LEFT', 'users', 'u')
                ->on('topic.author_id', '=', 'u.id')
                ->where('forum_id', '=', $forumId)
                ->order_by('date','DESC');

        $result = $builder->query();

        $topics = array();
        if($result->is_loaded()) {
            foreach($result as $key =>$record) {
                $topics[$key] = $record;
                //TODO : fixed array(array) to add new row in database
                $topics[$key]['status_name'] = ($record['status']) ? DB_ORM::model('dforum_status')->getStatusNameById($record['status']) : array(array('name' => 'Inactive'));
            }
        }
        return $topics;
    }

    public function getAllowedTopics($userId) {

        $builder = DB_SQL::select('default', array(DB_SQL::expr('topic.id')))
            ->from('dtopic', 'topic')
            ->join('LEFT', 'dtopic_users', 'mu')
            ->on('mu.id_topic', '=', 'topic.id')
//            ->where('status', 'NOT IN', array(0,2))
             ->where('author_id', '=', $userId, 'OR')
            ->where('mu.id_user', '=', $userId)
            ->order_by('topic.id', 'DESC');

        $result = $builder->query();

        $res = array();

        if ($result->is_loaded()) {
            foreach ($result as $record => $val) {
                $res[] =  $val['id'];
            }
        }
        return $res;

    }

    public function getFullAllTopicsByForumId($forumId) {

        $where = '';
        $join = '';
        $orderBy = 'message_date DESC';

        // Get only visible topics
        if (Auth::instance()->get_user()->type->name != 'superuser') {
            $where = ' AND topic.security_id = 0';
        }

        $connection = DB_Connection_Pool::instance()->get_connection('default');
        $result = $connection->query("
            SELECT
              topic. * , u.nickname AS author_name,
              COUNT( DISTINCT(dmes.id) ) AS messages_count,
              lastm.id AS message_id,
              lastm.date AS message_date,
              lastm.text AS message_text,
              lu.nickname AS message_nickname,
              COUNT(DISTINCT(dfu.id)) AS users_count
            FROM
              dtopic_messages AS dmes, dtopic_users AS dfu, dtopic AS topic
            JOIN
              dtopic_messages AS lastm
            LEFT JOIN
              users AS u ON topic.author_id = u.id
            LEFT JOIN
              users AS lu ON lastm.user_id = lu.id
            $join
            WHERE
              topic.forum_id = $forumId
            AND
              dmes.topic_id = topic.id
            AND
              dfu.id_topic = topic.id
            AND
              lastm.date = (
                  SELECT
                    m.date
                  FROM
                    dtopic_messages AS m
                  WHERE
                    m.topic_id = topic.id
                  ORDER BY
                    m.date DESC
                  LIMIT 1 )
            $where
            GROUP BY
              topic.id
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

    public function getTopicNameAndSecurityType($topicId) {
        $this->id = $topicId;
        $this->load();
        $topicName = NULL;
        $security = NULL;

        if($this->is_loaded()) {
            $topicName = $this->name;
            $security = $this->security_id;

            return array($topicName , $security);
        }
        return NULL;
    }

    public function getAllMessageByTopic($topicId) {
        $builder = DB_SQL::select('default',array(DB_SQL::expr('messages.*'),DB_SQL::expr('u.nickname as author_name'),
            DB_SQL::expr('u.id as author_id')))
            ->from('dtopic_messages', 'messages')
            ->join('LEFT', 'users', 'u')
            ->on('u.id', '=', 'messages.user_id')
            ->where('messages.topic_id','=',$topicId)
            ->order_by('messages.id', 'DESC');
        $result = $builder->query();

        if($result->is_loaded()) {
            return $result;
        }

        return NULL;
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

    public function getTopicById($topicId) {
        $builder = DB_SQL::select('default', array(DB_SQL::expr('topic.*'), DB_SQL::expr('u.nickname as author_name')))

            ->from($this->table(), 'topic')
            ->join('LEFT', 'users', 'u')
            ->on('topic.author_id', '=', 'u.id')
            ->where('topic.id', '=', $topicId);

        $result = $builder->query();

        if ($result->is_loaded()) {
            $topic = array();
            $topic = $result[0];

            $topic['messages'] = $this->getAllMessageByTopic($result[0]['id']);

            return $topic;
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


    public function getLastAddedTopicRecord() {
        $builder = DB_SQL::select('default')->from($this->table())->order_by('id', 'DESC')->limit(1);
        $result = $builder->query();

        if($result->is_loaded()) {
            $topic = DB_ORM::model('dtopic', array($result[0]['id']));
            return $topic->id;
        }
        return NULL;
    }



    public function updateTopic($topicName, $security, $status, $topicId) {
        $this->id = $topicId;
        $this->load();

        $this->name = $topicName;
        $this->status = $status;
        $this->security_id = $security;
        if($status != 1) {
            $this->node_id = null;
        }

        $this->save();
    }

    public function deleteTopic($topicId) {
        DB_ORM::model('dtopic_messages')->deleteAllMessages($topicId);
        DB_ORM::model('dtopic_users')->deleteAllUsers($topicId);
        DB_ORM::model('dtopic_groups')->deleteAllGroups($topicId);

        DB_SQL::delete('default')
                ->from($this->table())
                ->where('id', '=', $topicId)
                ->execute();
    }
}