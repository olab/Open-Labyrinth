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

class Controller_DTopicManager extends Controller_DForumManager {

    public function before() {
        parent::before();

        $actionTopic = array('addtopic','edittopic','savenewtopic','updatetopic','deletetopic');

        if (!in_array(strtolower($this->request->action()), $actionTopic)) {
            $forumInfo = DB_ORM::model('dtopic')->getForumByTopicId($this->request->param('id', NULL));
            $name = $forumInfo['name'];
            $id = $forumInfo['id'];
        } else {
            list($name, ) = DB_ORM::model('dforum')->getForumNameAndSecurityType($this->request->param('id', NULL));
            $id = $this->request->param('id', NULL);
        }

        Breadcrumbs::add(Breadcrumb::factory()->set_title(__($name))->set_url(URL::base() . 'dforumManager/viewForum/'.$id));

        unset($this->templateData['right']);
        $this->template->set('templateData', $this->templateData);
    }

    public function action_addTopic() {
        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Add topic in forum'))->set_url(URL::base() . 'dtopicManager/addTopic'));

        $this->templateData['forum_id'] = $this->request->param('id', NULL);

        list($forumName, ) = DB_ORM::model('dforum')->getForumNameAndSecurityType($this->templateData['forum_id']);

        $this->templateData['existUsers'][0] = DB_ORM::model('user')->getUserByIdAndAuth(Auth::instance()->get_user()->id);
        $this->templateData['users'] = DB_ORM::model('user')->getAllUsersAndAuth('ASC');
        $this->templateData['groups'] = DB_ORM::model('group')->getAllGroups('ASC');
        $this->templateData['status'] = DB_ORM::model('dforum_status')->getAllStatus();
        $this->templateData['forumName'] = $forumName;

        $view = View::factory('dtopic/addEditTopic');
        $view->set('templateData', $this->templateData);

        $this->templateData['center'] = $view;
        $this->template->set('templateData', $this->templateData);
    }

    public function action_editTopic() {
        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Edit topic'))->set_url(URL::base() . 'dtopicManager/editTopic'));

        $this->templateData['forum_id'] = $this->request->param('id', NULL);
        $topicId = $this->request->param('id2', NULL);
        $this->templateData['topic_id'] = $topicId;

        list($this->templateData['name'], $this->templateData['security']) = DB_ORM::model('dtopic')->getTopicNameAndSecurityType($topicId);

        $this->templateData['topic'] = DB_ORM::model('dtopic', array((int)$topicId));

        $this->templateData['existUsers'] = DB_ORM::model('dtopic_users')->getAllUsersInTopicInfo($topicId);
        $existUsersId = DB_ORM::model('dtopic_users')->getAllUsersInTopic($topicId, 'id');
        $this->templateData['users'] = DB_ORM::model('user')->getAllUsersAndAuth('ASC', $existUsersId);

        $this->templateData['existGroups'] = DB_ORM::model('dtopic_groups')->getAllGroupsInTopicInfo($topicId);
        $existGroupsId = DB_ORM::model('dtopic_groups')->getAllGroupsInTopic($topicId, 'id');
        $this->templateData['groups'] = DB_ORM::model('group')->getAllGroups('ASC', $existGroupsId);
        $this->templateData['status'] = DB_ORM::model('dforum_status')->getAllStatus();

        $viewEditForum = View::factory('dtopic/addEditTopic');
        $viewEditForum->set('templateData', $this->templateData);

        $this->templateData['center'] = $viewEditForum;
        $this->template->set('templateData', $this->templateData);
    }

    public function action_saveNewTopic(){
        $topicName = Arr::get($this->request->post(), 'topicname', NULL);
        $firstMessage = Arr::get($this->request->post(), 'firstmessage', NULL);
        $security = Arr::get($this->request->post(), 'security', NULL);
        $status = Arr::get($this->request->post(), 'status', NULL);
        $forum_id = Arr::get($this->request->post(), 'forum_id', NULL);

        $users = Arr::get($this->request->post(), 'users', NULL);
        $groups = Arr::get($this->request->post(), 'groups', NULL);

        $topicId = DB_ORM::model('dtopic')->createTopic($topicName,$security, $status , $forum_id);
        $messageID = DB_ORM::model('dtopic_messages')->createMessage($topicId, $firstMessage);

        DB_ORM::model('dtopic_users')->updateUsers($topicId, $users);
        DB_ORM::model('dtopic_groups')->updateGroups($topicId, $groups);

        if ($status) {
            self::action_mail('createForum',$topicId, $firstMessage, $topicName,'','','fromTopic');
        }
        else {
            // User create topic , will be activated
            self::action_mail('userAddTopic',$topicId, $firstMessage, $topicName,'','','fromTopic');
        }

        if (Auth::instance()->get_user()->type->name != 'superuser') {
            Request::initial()->redirect(URL::base() . 'dforumManager/');
        }
        Request::initial()->redirect(URL::base() . 'dforumManager/editForum/'.$forum_id);
    }

    public function action_updateTopic(){
        $topicName = Arr::get($this->request->post(), 'topicname', NULL);
        $securityType = Arr::get($this->request->post(), 'security', NULL);
        $status = Arr::get($this->request->post(), 'status', NULL);
        $topic_id = Arr::get($this->request->post(), 'topic_id', NULL);
        $forum_id = Arr::get($this->request->post(), 'forum_id', NULL);

        list($oldName, ) = DB_ORM::model('dtopic')->getTopicNameAndSecurityType($topic_id);

        $users = Arr::get($this->request->post(), 'users', NULL);
        $groups = Arr::get($this->request->post(), 'groups', NULL);

        DB_ORM::model('dtopic')->updateTopic($topicName, $securityType, $status, $topic_id);
        DB_ORM::model('dtopic_users')->updateUsers($topic_id, $users);
        DB_ORM::model('dtopic_groups')->updateGroups($topic_id, $groups);

        $topicNames = $oldName . '-' . $topicName;

        if ($oldName != $topicName)
        {
            self::action_mail('updateForum',$topic_id,$topicNames, '','','fromTopic');
        }

        Request::initial()->redirect(URL::base() . 'dforumManager/editForum/'. $forum_id);
    }

    public function action_viewTopic() {

        $this->templateData['topic'] = DB_ORM::model('dtopic')->getTopicById($this->request->param('id', 0));

        Breadcrumbs::add(Breadcrumb::factory()->set_title(__($this->templateData['topic']['name']))->set_url(URL::base() . 'dtopicManager/ViewTopic/' . $this->request->param('id', 0)));

        $viewForumView = View::factory('dtopic/viewTopic');
        $viewForumView->set('templateData', $this->templateData);

        $this->templateData['center'] = $viewForumView;
        $this->template->set('templateData', $this->templateData);
    }

    public function action_deleteTopic() {
        $forumId = $this->request->param('id', NULL);
        $topicId = $this->request->param('id2', NULL);

        $from = $this->request->param('id3', NULL);

        list($topicName, ) = DB_ORM::model('dtopic')->getTopicNameAndSecurityType($topicId);

        $messages = DB_ORM::model('dtopic')->getAllMessageByTopic($topicId);

        if ($messages) {
            foreach ($messages as $message) {
                DB_ORM::model('dtopic_messages')->deleteMessage($message['id']);
            }
        }

        $groups = DB_ORM::model('dtopic_groups')->getAllGroupsInTopic($topicId);

        if ($groups) {
            foreach($groups as $group) {
                DB_ORM::model('dtopic_groups')->deleteGroupInTopic($topicId, $group['id_group']);
            }
        }

        DB_ORM::model('dtopic')->deleteTopic($topicId);

        self::action_mail('deleteForum',$topicId,'',$topicName,'','','fromTopic');

        $red = is_null($from) ? '' : 'editForum/' . $forumId;

        Request::initial()->redirect(URL::base() . 'dforumManager/' . $red);
    }

    public function action_addMessage(){
        $message = Arr::get($this->request->post(), 'message', NULL);
        $topicId = Arr::get($this->request->post(), 'topic', NULL);

        $messageId = DB_ORM::model('dtopic_messages')->createMessage($topicId, $message);

        list($forumName, ) = DB_ORM::model('dtopic')->getTopicNameAndSecurityType($topicId);

        self::action_mail('addMsg', $topicId,  $message, $forumName, '', $messageId, 'fromTopic');

        Request::initial()->redirect(URL::base() . 'dtopicManager/viewTopic/' . $topicId . '#m-' . $messageId);
    }

    public function action_editMessage(){
        $topicId = $this->request->param('id', NULL);
        $messageId =   $this->request->param('id2', NULL);

        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Edit message id:').' '. $messageId)->set_url(URL::base() . 'dtopicManager/editMessage/' . $messageId));

        $this->templateData['message'] = DB_ORM::model('dtopic_messages')->getMessage($messageId);

        $this->templateData['message_id'] = $messageId;
        $this->templateData['topic_id'] = $topicId;

        $viewForumView = View::factory('dtopic/editMessage');
        $viewForumView->set('templateData', $this->templateData);

        $this->templateData['center'] = $viewForumView;
        $this->template->set('templateData', $this->templateData);
    }

    public function action_updateMessage() {
        $message_id = Arr::get($this->request->post(), 'message_id', NULL);
        $topicId = Arr::get($this->request->post(), 'topic', NULL);
        $message_text = Arr::get($this->request->post(), 'message', NULL);

        $oldMessage = DB_ORM::model('dtopic_messages')->getMessage($message_id);

        DB_ORM::model('dtopic_messages')->updateMessage($message_id, $message_text);

        list($forumName, ) = DB_ORM::model('dtopic')->getTopicNameAndSecurityType($topicId);

        $message = $oldMessage . '-' . $message_text;

        if ($oldMessage != $message_text) {
            self::action_mail('updateMsg',$topicId, $message, $forumName,'','','fromTopic');
        }

        Request::initial()->redirect(URL::base() . 'dtopicManager/viewTopic/' . $topicId . '#m-' . $message_id);
    }

    public function action_deleteMessage(){

        $topicId = $this->request->param('id', NULL);
        $messageId =   $this->request->param('id2', NULL);

        $oldMessage = DB_ORM::model('dtopic_messages')->getMessage($messageId);

        DB_ORM::model('dtopic_messages')->deleteMessage($messageId);

        list($forumName, ) = DB_ORM::model('dtopic')->getTopicNameAndSecurityType($topicId);

        self::action_mail('deleteMsg',$topicId, $oldMessage, $forumName,'','','fromTopic');

        Request::initial()->redirect(URL::base() . 'dtopicManager/viewTopic/' . $topicId);
    }

}