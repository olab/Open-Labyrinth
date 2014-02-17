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

class Controller_DForumManager extends Controller_Base {

    public function before() {
        parent::before();

        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Forums'))->set_url(URL::base() . 'dforumManager'));

        unset($this->templateData['right']);
        $this->template->set('templateData', $this->templateData);
    }

    public function action_index() {
        $sortBy = $this->request->param('id', NULL);
        $typeSort = $this->request->param('id2', 1);

        if (is_null($sortBy)) $sortBy = 4;

        $openForums = DB_ORM::model('dforum')->getAllForums($sortBy,$typeSort);

        $userForumsInfo = array();
        $userTopicsInfo = array();
        if(Auth::instance()->logged_in()) {
            $forumIds = array();
            $topicIds = array();
            if(!isset($openForums)&&!empty($openForums))
                foreach($openForums as $forum) {
                    $forumIds[] = $forum['id'];
                    if(isset($forum['topics']) && count($forum['topics']) > 0) {
                        foreach($forum['topics'] as $topic) {
                            $topicIds[] = $topic['id'];
                        }
                    }
                }

            $userForumsInfo = DB_ORM::model('dforum_users')->getForumUser($forumIds, Auth::instance()->get_user()->id);
            $userTopicsInfo = DB_ORM::model('dtopic_users')->getTopicUser($topicIds, Auth::instance()->get_user()->id);
        }

        $this->templateData['forums'] = $openForums;
        $this->templateData['typeSort'] = $typeSort;
        $this->templateData['sortBy'] = $sortBy;
        $this->templateData['userForumsInfo'] = $userForumsInfo;
        $this->templateData['userTopicsInfo'] = $userTopicsInfo;

        $view = View::factory('dforum/view');
        $view->set('templateData', $this->templateData);

        $this->templateData['center'] = $view;
        $this->template->set('templateData', $this->templateData);
    }

    public function action_viewForum() {

        $this->templateData['forum'] = DB_ORM::model('dforum')->getForumById($this->request->param('id', 0));
        $this->templateData['topics'] = DB_ORM::model('dtopic')->getAllTopicsByForumId($this->request->param('id', 0));

        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('View').' '. $this->templateData['forum']['name'])->set_url(URL::base() . 'dforumManager/ViewForum/' . $this->request->param('id', 0)));

        $viewForumView = View::factory('dforum/viewForum');
        $viewForumView->set('templateData', $this->templateData);

        $this->templateData['center'] = $viewForumView;
        $this->template->set('templateData', $this->templateData);
    }

    public function action_addForum(){
        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Add forum'))->set_url(URL::base() . 'dforumManager/addForum'));

        $this->templateData['existUsers'][0] = DB_ORM::model('user')->getUserByIdAndAuth(Auth::instance()->get_user()->id);
        $this->templateData['users'] = DB_ORM::model('user')->getAllUsersAndAuth('ASC');
        $this->templateData['groups'] = DB_ORM::model('group')->getAllGroups('ASC');
        $this->templateData['status'] = DB_ORM::model('dforum_status')->getAllStatus();

        $view = View::factory('dforum/addEditForum');
        $view->set('templateData', $this->templateData);

        $this->templateData['center'] = $view;
        $this->template->set('templateData', $this->templateData);
    }

    public function action_editForum(){
        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Edit forum'))->set_url(URL::base() . 'dforumManager/editForum'));

        $this->templateData['forum_id'] = $this->request->param('id', NULL);

        list($this->templateData['name'], $this->templateData['security']) = DB_ORM::model('dforum')->getForumNameAndSecurityType($this->templateData['forum_id']);

        $this->templateData['forum'] = DB_ORM::model('dforum', array((int)$this->templateData['forum_id']));
        $this->templateData['topics'] = DB_ORM::model('dtopic')->getAllTopicsByForumId($this->templateData['forum_id']);

        $this->templateData['status'] = DB_ORM::model('dforum_status')->getAllStatus();

        $this->templateData['existUsers'] = DB_ORM::model('dforum_users')->getAllUsersInForumInfo($this->templateData['forum_id']);
        $existUsersId = DB_ORM::model('dforum_users')->getAllUsersInForum($this->templateData['forum_id'], 'id');
        $this->templateData['users'] = DB_ORM::model('user')->getAllUsersAndAuth('ASC', $existUsersId);

        $this->templateData['existGroups'] = DB_ORM::model('dforum_groups')->getAllGroupsInForumInfo($this->templateData['forum_id']);
        $existGroupsId = DB_ORM::model('dforum_groups')->getAllGroupsInForum($this->templateData['forum_id'], 'id');
        $this->templateData['groups'] = DB_ORM::model('group')->getAllGroups('ASC', $existGroupsId);

        $viewEditForum = View::factory('dforum/addEditForum');
        $viewEditForum->set('templateData', $this->templateData);

        $this->templateData['center'] = $viewEditForum;
        $this->template->set('templateData', $this->templateData);
    }

    public function action_updateForum(){
        $forumName = Arr::get($this->request->post(), 'forumname', NULL);
        $securityType = Arr::get($this->request->post(), 'security', NULL);
        $status = Arr::get($this->request->post(), 'status', NULL);
        $forumId = Arr::get($this->request->post(), 'forum_id', NULL);

        list($oldName, ) = DB_ORM::model('dforum')->getForumNameAndSecurityType($forumId);

        $users = Arr::get($this->request->post(), 'users', NULL);
        $groups = Arr::get($this->request->post(), 'groups', NULL);

        DB_ORM::model('dforum')->updateForum($forumName, $securityType,$status, $forumId);
        DB_ORM::model('dforum_users')->updateUsers($forumId, $users);
        DB_ORM::model('dforum_groups')->updateGroups($forumId, $groups);

        $forumNames = $oldName . '-' . $forumName;

        if ($oldName != $forumName)
        {
            self::action_mail('updateForum',$forumId,$forumNames);
        }

        Request::initial()->redirect(URL::base() . 'dforumManager');
    }

    public function action_deleteForum() {
        $forumId = $this->request->param('id', NULL);

        list($forumName, ) = DB_ORM::model('dforum')->getForumNameAndSecurityType($forumId);

        $messages = DB_ORM::model('dforum')->getAllMessageByForum($forumId);

        if ($messages) {
            foreach ($messages as $message) {
                DB_ORM::model('dforum_messages')->deleteMessage($message['id']);
            }
        }

        $groups = DB_ORM::model('dforum_groups')->getAllGroupsInForum($forumId);

        if ($groups) {
            foreach($groups as $group) {
                DB_ORM::model('dforum_groups')->deleteGroupInForum($forumId, $group['id_group']);
            }
        }

        DB_ORM::model('dforum')->deleteForum($forumId);

        self::action_mail('deleteForum',$forumId,'',$forumName);

        Request::initial()->redirect(URL::base() . 'dforumManager');
    }


    public function action_saveNewForum(){
        $forumName = Arr::get($this->request->post(), 'forumname', NULL);
        $firstMessage = Arr::get($this->request->post(), 'firstmessage', NULL);
        $status = Arr::get($this->request->post(), 'status', NULL);
        $security = Arr::get($this->request->post(), 'security', NULL);
        $sendNotification = Arr::get($this->request->post(), 'sentNotifications', 'off') == 'off' ? 0: 1;

        $users = Arr::get($this->request->post(), 'users', NULL);
        $groups = Arr::get($this->request->post(), 'groups', NULL);

        $forumId = DB_ORM::model('dforum')->createForum($forumName,$security, $status);
        $messageID = DB_ORM::model('dforum_messages')->createMessage($forumId, $firstMessage);

        DB_ORM::model('dforum_users')->updateUsers($forumId, $users, $sendNotification);
        DB_ORM::model('dforum_groups')->updateGroups($forumId, $groups, $sendNotification);

        self::action_mail('createForum',$forumId, $firstMessage, $forumName);

        Request::initial()->redirect(URL::base() . 'dforumManager');
    }

    public function action_addMessage(){
        $message = Arr::get($this->request->post(), 'message', NULL);
        $forumId = Arr::get($this->request->post(), 'forum', NULL);

        $messageId = DB_ORM::model('dforum_messages')->createMessage($forumId, $message);

        list($forumName, ) = DB_ORM::model('dforum')->getForumNameAndSecurityType($forumId);

        self::action_mail('addMsg', $forumId,  $message, $forumName, '', $messageId);

        Request::initial()->redirect(URL::base() . 'dforumManager/viewForum/' . $forumId . '#m-' . $messageId);
    }

    public function action_deleteMessage(){

        $messageId = $this->request->param('id', NULL);
        $forumId =   $this->request->param('id2', NULL);

        $oldMessage = DB_ORM::model('dforum_messages')->getMessage($messageId);

        DB_ORM::model('dforum_messages')->deleteMessage($messageId);

        list($forumName, ) = DB_ORM::model('dforum')->getForumNameAndSecurityType($forumId);

        self::action_mail('deleteMsg',$forumId, $oldMessage, $forumName);

        Request::initial()->redirect(URL::base() . 'dforumManager/viewForum/' . $forumId);
    }

    public function action_editMessage(){
        $messageId = $this->request->param('id', NULL);
        $forumId =   $this->request->param('id2', NULL);

        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Edit message id:').' '. $messageId)->set_url(URL::base() . 'dforumManager/editMessage/' . $messageId));

        $this->templateData['message'] = DB_ORM::model('dforum_messages')->getMessage($messageId);
        $this->templateData['message_id'] = $messageId;
        $this->templateData['forum_id'] = $forumId;

        $viewForumView = View::factory('dforum/editMessage');
        $viewForumView->set('templateData', $this->templateData);

        $this->templateData['center'] = $viewForumView;
        $this->template->set('templateData', $this->templateData);
    }

    public function action_updateMessage() {
        $message_id = Arr::get($this->request->post(), 'message_id', NULL);
        $forumId = Arr::get($this->request->post(), 'forum', NULL);
        $message_text = Arr::get($this->request->post(), 'message', NULL);

        $oldMessage = DB_ORM::model('dforum_messages')->getMessage($message_id);

        DB_ORM::model('dforum_messages')->updateMessage($message_id, $message_text);

        list($forumName, ) = DB_ORM::model('dforum')->getForumNameAndSecurityType($forumId);

        $message = $oldMessage . '-' . $message_text;

        if ($oldMessage != $message_text) {
            self::action_mail('updateMsg',$forumId, $message, $forumName);
        }

        Request::initial()->redirect(URL::base() . 'dforumManager/viewForum/' . $forumId . '#m-' . $message_id);
    }

    private static function prepareUsersMail($forumId, $usersEmail = '', $type = '') {
        $result = array();
        if ($usersEmail != '' && $type == 'deleteUser') {
            // get groups users emails
            $groups = DB_ORM::model('dforum_groups')->getAllGroupsInForum($forumId);

            $groupUsers = array();

            if ($groups) {
                $groupData = array();
                $groupIds = array();
                foreach ($groups as $group) {
                    $groupIds[] = $group['id_group'];
                }
                $groupData[] = DB_ORM::model('user_group')->getAllUsersByGroupIN($groupIds);

                foreach ($groupData as $key => $groupUser) {
                    foreach($groupUser as $user) {
                        $groupUsers[] = $user;
                    }
                }
            }

            $result = array();
            foreach ($usersEmail as $userEmail) {
                if (!in_array($userEmail , $groupUsers)) {
                    $result[] = $userEmail;
                }
            }
        }
        else if ($usersEmail != '' && $type == 'groupAdd') {
            $result = array();
            $result = DB_ORM::model('user_group')->getAllUsersByGroupIN($usersEmail);
            $result = array_unique($result);
        } else if ($usersEmail != '' && $type == 'groupDelete') {
            $result = array();
            $groupEmail = DB_ORM::model('user_group')->getAllUsersByGroupIN($usersEmail);
            $groupEmail = array_unique($groupEmail);

            $users = DB_ORM::model('dforum_users')->getAllUsersInForumInfo($forumId, true);

            $allUsers = array();

            // get users emails
            foreach ($users as $user) {
                $allUsers[] = $user['email'];
            }

            foreach ($groupEmail as $gEmail) {
                if (!in_array($gEmail , $allUsers)) {
                    $result[] = $gEmail;
                }
            }
        } else {
            $users = DB_ORM::model('dforum_users')->getAllUsersInForumInfo($forumId, true);

            $allUsers = array();

            // get users emails
            foreach ($users as $user) {
                $allUsers[] = $user['email'];
            }

            // get groups users emails
            $groups = DB_ORM::model('dforum_groups')->getAllGroupsInForum($forumId);

            $groupUsers = array();

            if ($groups) {
                $groupData = array();
                $groupIds = array();
                foreach ($groups as $group) {
                    $groupIds[] = $group['id_group'];
                }
                $groupData[] = DB_ORM::model('user_group')->getAllUsersByGroupIN($groupIds);

                foreach ($groupData as $key => $groupUser) {
                    foreach($groupUser as $user) {
                        $groupUsers[] = $user;
                    }
                }
            }

            $result = array_merge($allUsers,$groupUsers);

            $result = array_unique($result);

            $key = null;
            $key = array_search(Auth::instance()->get_user()->email, $result);

            if ($key !== FALSE) {
                unset($result[$key]);
            }
        }

        return $result;
    }

    private static function prepareUsersMailforTopic($topicId, $usersEmail = '', $type = '') {
        $result = array();
        if ($usersEmail != '' && $type == 'deleteUser') {
            // get groups users emails
            $groups = DB_ORM::model('dtopic_groups')->getAllGroupsInTopic($topicId);

            $groupUsers = array();

            if ($groups) {
                $groupData = array();
                $groupIds = array();
                foreach ($groups as $group) {
                    $groupIds[] = $group['id_group'];
                }
                $groupData[] = DB_ORM::model('user_group')->getAllUsersByGroupIN($groupIds);

                foreach ($groupData as $key => $groupUser) {
                    foreach($groupUser as $user) {
                        $groupUsers[] = $user;
                    }
                }
            }

            $result = array();
            foreach ($usersEmail as $userEmail) {
                if (!in_array($userEmail , $groupUsers)) {
                    $result[] = $userEmail;
                }
            }
        }
        else if ($usersEmail != '' && $type == 'groupAdd') {
            $result = array();
            $result = DB_ORM::model('user_group')->getAllUsersByGroupIN($usersEmail);
            $result = array_unique($result);
        } else if ($usersEmail != '' && $type == 'groupDelete') {
            $result = array();
            $groupEmail = DB_ORM::model('user_group')->getAllUsersByGroupIN($usersEmail);
            $groupEmail = array_unique($groupEmail);

            $users = DB_ORM::model('dtopic_users')->getAllUsersInTopicInfo($topicId, true);

            $allUsers = array();

            // get users emails
            foreach ($users as $user) {
                $allUsers[] = $user['email'];
            }

            foreach ($groupEmail as $gEmail) {
                if (!in_array($gEmail , $allUsers)) {
                    $result[] = $gEmail;
                }
            }
        } else {
            $users = DB_ORM::model('dtopic_users')->getAllUsersInTopicInfo($topicId, true);

            $allUsers = array();

            // get users emails
            foreach ($users as $user) {
                $allUsers[] = $user['email'];
            }

            // get groups users emails
            $groups = DB_ORM::model('dtopic_groups')->getAllGroupsInTopic($topicId);

            $groupUsers = array();

            if ($groups) {
                $groupData = array();
                $groupIds = array();
                foreach ($groups as $group) {
                    $groupIds[] = $group['id_group'];
                }
                $groupData[] = DB_ORM::model('user_group')->getAllUsersByGroupIN($groupIds);

                foreach ($groupData as $key => $groupUser) {
                    foreach($groupUser as $user) {
                        $groupUsers[] = $user;
                    }
                }
            }

            $result = array_merge($allUsers,$groupUsers);

            $result = array_unique($result);

            $key = null;
            $key = array_search(Auth::instance()->get_user()->email, $result);

            if ($key !== FALSE) {
                unset($result[$key]);
            }
        }

        return $result;
    }


    public static function action_mail($action, $forumId, $message = '', $forumName = '', $emails = '', $messageId = null, $isTopic = null ) {
        $emailConfig = Kohana::$config->load('dforum_email');
        $openUrl = (is_null($isTopic)) ? 'dforumManager/viewForum/' : 'dtopicManager/viewTopic/';
        $URL = URL::base('http', true) . $openUrl . $forumId;

        switch($action) :

            case 'addMsg' :

                $result = (is_null($isTopic)) ? self::prepareUsersMail($forumId) : self::prepareUsersMailforTopic($forumId);

                // send mail to Author of action
                $subject = $emailConfig['add_msg']['author'] . ' "' . $forumName . '"';
                $nickname = Auth::instance()->get_user()->nickname;

                $mail_body  = $nickname . ' ' . $emailConfig['add_msg']['action'] . ' "' . $forumName . '"<br/>';
                $mail_body .= '---------------------------------------<br/>';
                $mail_body .= $message . '<br/>';
                $mail_body .= '---------------------------------------<br/>';
                $mail_body .=  "URL: " . $URL . ($messageId != null ? '#m-' . $messageId : '');

                $header  = 'MIME-Version: 1.0' . "\r\n";
                $header .= 'Content-type: text/html;' . "\r\n";
                $header .= "From: ".  $emailConfig['from_name'] . " <" . $emailConfig['mail_from'] . ">\r\n";

                // send mail to other Users
                $subject = $emailConfig['add_msg']['other'] . ' "' . $forumName . '"';
                foreach($result as $userEmail)
                {
                    if ($userEmail != '')
                    {
                        mail($userEmail, $subject, $mail_body, $header);
                    }
                }

                break;

            case 'deleteMsg' :

                $result = (is_null($isTopic)) ? self::prepareUsersMail($forumId) : self::prepareUsersMailforTopic($forumId);

                // send mail to Author of action
                $subject = $emailConfig['delete_msg']['author'] . ' "' . $forumName . '"';
                $nickname = Auth::instance()->get_user()->nickname;

                $mail_body  = $nickname . ' ' . $emailConfig['delete_msg']['action'] . ' "' . $forumName . '"<br/>';
                $mail_body .= '---------------------------------------<br/>';
                $mail_body .= $message . '<br/>';
                $mail_body .= '---------------------------------------<br/>';
                $mail_body .=  "URL: " . $URL . ($messageId != null ? '#m-' . $messageId : '');

                $header  = 'MIME-Version: 1.0' . "\r\n";
                $header .= 'Content-type: text/html;' . "\r\n";
                $header .= "From: ".  $emailConfig['from_name'] . " <" . $emailConfig['mail_from'] . ">\r\n";

                // send mail to other Users
                $subject = $emailConfig['delete_msg']['other'] . ' "' . $forumName . '"';
                foreach($result as $userEmail)
                {
                    if ($userEmail != '')
                    {
                        mail($userEmail, $subject, $mail_body, $header);
                    }
                }

                break;

            case 'updateMsg' :

                list($oldMessage, $newMessage) = explode('-', $message);
                $message = 'Old message:' . "\r\n" . $oldMessage . "\r\n\r\n" . 'New message:' .  "\r\n" . $newMessage;

                $result = (is_null($isTopic)) ? self::prepareUsersMail($forumId) : self::prepareUsersMailforTopic($forumId);

                // send mail to Author of action
                $subject = $emailConfig['update_msg']['author'] . ' "' . $forumName . '"';
                $nickname = Auth::instance()->get_user()->nickname;

                $mail_body  = $nickname . ' ' . $emailConfig['update_msg']['action'] . ' "' . $forumName . '"<br/>';
                $mail_body .= '---------------------------------------<br/>';
                $mail_body .= $message . '<br/>';
                $mail_body .= '---------------------------------------<br/>';
                $mail_body .=  "URL: " . $URL . ($messageId != null ? '#m-' . $messageId : '');

                $header  = 'MIME-Version: 1.0' . "\r\n";
                $header .= 'Content-type: text/html;' . "\r\n";
                $header .= "From: ".  $emailConfig['from_name'] . " <" . $emailConfig['mail_from'] . ">\r\n";

                // send mail to other Users
                $subject = $emailConfig['update_msg']['other'] . ' "' . $forumName . '"';
                foreach($result as $userEmail)
                {
                    if ($userEmail != '')
                    {
                        mail($userEmail, $subject, $mail_body, $header);
                    }
                }

                break;

            case 'createForum' :

                $result = (is_null($isTopic)) ? self::prepareUsersMail($forumId) : self::prepareUsersMailforTopic($forumId);

                // send mail to Author of action
                $subject = $emailConfig['create_forum']['author'] . ' "' . $forumName . '"';
                $nickname = Auth::instance()->get_user()->nickname;

                $mail_body  = $nickname . ' ' . $emailConfig['create_forum']['action'] . ' "' . $forumName . '"<br/>';
                $mail_body .= '---------------------------------------<br/>';
                $mail_body .= $message . '<br/>';
                $mail_body .= '---------------------------------------<br/>';
                $mail_body .=  "URL: " . $URL;

                $header  = 'MIME-Version: 1.0' . "\r\n";
                $header .= 'Content-type: text/html;' . "\r\n";
                $header .= "From: ".  $emailConfig['from_name'] . " <" . $emailConfig['mail_from'] . ">\r\n";

                // send mail to other Users
                $subject = $emailConfig['create_forum']['other'] . ' "' . $forumName . '"';
                foreach($result as $userEmail)
                {
                    if ($userEmail != '')
                    {
                        mail($userEmail, $subject, $mail_body, $header);
                    }
                }

                break;


            case 'userAddTopic' :

                $result = self::prepareUsersMailforTopic($forumId);

                //send mail to author of Forum
                $subject = $emailConfig['create_forum']['activate_admin'];
                $nickname = Auth::instance()->get_user()->nickname;
                $forumInfo =  DB_ORM::model('dtopic')->getForumByTopicId($forumId);

                $emailTo = $forumInfo['forumAuthor_email'];

                $URL = URL::base('http', true) . 'dforumManager/editForum/' . $forumInfo['id'];

                $mail_body  = $nickname . ' ' . $emailConfig['create_forum']['action'] . ' "' . $forumName . '"<br/>';
                $mail_body .= '---------------------------------------<br/>';
                $mail_body .= $message . '<br/>';
                $mail_body .= '---------------------------------------<br/>';
                $mail_body .=  "URL: " . $URL;

                $header  = 'MIME-Version: 1.0' . "\r\n";
                $header .= 'Content-type: text/html;' . "\r\n";
                $header .= "From: ".  $emailConfig['from_name'] . " <" . $emailConfig['mail_from'] . ">\r\n";
                mail($emailTo, $subject, $mail_body, $header);


                // send mail to Author of action
                $subject = $emailConfig['create_forum']['author'] . ' "' . $forumName . '"' . $emailConfig['create_forum']['activate_user'];
                $nickname = Auth::instance()->get_user()->nickname;
                $URL = URL::base('http', true) . 'dforumManager/';

                $mail_body  = $nickname . ' ' . $emailConfig['create_forum']['action'] . ' "' . $forumName . '"<br/>';
                $mail_body .= '---------------------------------------<br/>';
                $mail_body .= $message . '<br/>';
                $mail_body .= '---------------------------------------<br/>';
                $mail_body .=  "URL: " . $URL;

                $header  = 'MIME-Version: 1.0' . "\r\n";
                $header .= 'Content-type: text/html;' . "\r\n";
                $header .= "From: ".  $emailConfig['from_name'] . " <" . $emailConfig['mail_from'] . ">\r\n";

                // send mail to other Users
                $subject = $emailConfig['create_forum']['other'] . ' "' . $forumName . '"';
                foreach($result as $userEmail)
                {
                    if ($userEmail != '')
                    {
                        mail($userEmail, $subject, $mail_body, $header);
                    }
                }

                break;

            case 'updateForum' :

                list($oldForumName, $newForumName) = explode('-', $message);
                $message = 'Old Forum Name:' . "\r\n" . $oldForumName . "\r\n\r\n" . 'New Forum Name:' .  "\r\n" . $newForumName;

                $result = (is_null($isTopic)) ? self::prepareUsersMail($forumId) : self::prepareUsersMailforTopic($forumId);

                // send mail to Author of action
                $subject = $emailConfig['update_forum']['author'] . ' "' . $oldForumName . '"(old name)';
                $nickname = Auth::instance()->get_user()->nickname;

                $mail_body  = $nickname . ' ' . $emailConfig['update_forum']['action'] . ' "' . $forumName . '"<br/>';
                $mail_body .= '---------------------------------------<br/>';
                $mail_body .= $message . '<br/>';
                $mail_body .= '---------------------------------------<br/>';
                $mail_body .=  "URL: " . $URL;

                $header  = 'MIME-Version: 1.0' . "\r\n";
                $header .= 'Content-type: text/html;' . "\r\n";
                $header .= "From: ".  $emailConfig['from_name'] . " <" . $emailConfig['mail_from'] . ">\r\n";

                // send mail to other Users
                $subject = $emailConfig['update_forum']['other'] . ' "' . $oldForumName . '"(old name)';
                foreach($result as $userEmail)
                {
                    if ($userEmail != '')
                    {
                        mail($userEmail, $subject, $mail_body, $header);
                    }
                }

                break;

            case 'deleteForum' :
                $URL = URL::base('http', true) . 'dforumManager/';

                $result = (is_null($isTopic)) ? self::prepareUsersMail($forumId) : self::prepareUsersMailforTopic($forumId);

                // send mail to Author of action
                $subject = $emailConfig['delete_forum']['author'] . ' "' . $forumName . '"';
                $nickname = Auth::instance()->get_user()->nickname;

                $mail_body  = $nickname . ' ' . $emailConfig['delete_forum']['action'] . ' "' . $forumName . '"<br/>';
                $mail_body .= '---------------------------------------<br/>';
                $mail_body .= $message . '<br/>';
                $mail_body .= '---------------------------------------<br/>';
                $mail_body .=  "URL: " . $URL;

                $header  = 'MIME-Version: 1.0' . "\r\n";
                $header .= 'Content-type: text/html;' . "\r\n";
                $header .= "From: ".  $emailConfig['from_name'] . " <" . $emailConfig['mail_from'] . ">\r\n";

                // send mail to other Users
                $subject = $emailConfig['delete_forum']['other'] . ' "' . $forumName . '"';
                foreach($result as $userEmail)
                {
                    if ($userEmail != '')
                    {
                        mail($userEmail, $subject, $mail_body, $header);
                    }
                }
                break;

            case 'addUserToForum' :

                list($forumName, ) = (is_null($isTopic)) ? DB_ORM::model('dforum')->getForumNameAndSecurityType($forumId) :
                                                           DB_ORM::model('dtopic')->getTopicNameAndSecurityType($forumId) ;

                $subject = $emailConfig['addUserToForum']['other'] . ' "' . $forumName . '"';
                $nickname = Auth::instance()->get_user()->nickname;
                $emailTo =  $emails;

                $mail_body  = $nickname . ' ' . $emailConfig['addUserToForum']['action'] . ' "' . $forumName . '"<br/>';
                $mail_body .=  "URL: " . $URL;

                $header  = 'MIME-Version: 1.0' . "\r\n";
                $header .= 'Content-type: text/html;' . "\r\n";
                $header .= "From: ".  $emailConfig['from_name'] . " <" . $emailConfig['mail_from'] . ">\r\n";

                mail($emailTo, $subject, $mail_body, $header);

                break;

            case 'deleteUserFromForum' :

                $result = (is_null($isTopic)) ? self::prepareUsersMail($forumId) : self::prepareUsersMailforTopic($forumId);

                if (count($result) > 0)
                {

                    list($forumName, ) = (is_null($isTopic)) ? DB_ORM::model('dforum')->getForumNameAndSecurityType($forumId) :
                                                               DB_ORM::model('dtopic')->getTopicNameAndSecurityType($forumId) ;

                    $subject = $emailConfig['deleteUserFromForum']['other'] . ' "' . $forumName . '"';
                    $nickname = Auth::instance()->get_user()->nickname;

                    $mail_body  = $nickname . ' ' . $emailConfig['deleteUserFromForum']['action'] . ' "' . $forumName . '"<br/>';
                    $mail_body .=  "URL: " . $URL;

                    $header  = 'MIME-Version: 1.0' . "\r\n";
                    $header .= 'Content-type: text/html;' . "\r\n";
                    $header .= "From: ".  $emailConfig['from_name'] . " <" . $emailConfig['mail_from'] . ">\r\n";

                    foreach ($result as $userEmail)
                    {
                        if ($userEmail != '')
                        {
                            mail($userEmail, $subject, $mail_body, $header);
                        }
                    }
                }

                break;

            case 'addGroupToForum' :

                $result = (is_null($isTopic)) ? self::prepareUsersMail($forumId) : self::prepareUsersMailforTopic($forumId);

                if (count($result) > 0)
                {
                    list($forumName, ) = (is_null($isTopic)) ? DB_ORM::model('dforum')->getForumNameAndSecurityType($forumId) :
                                                               DB_ORM::model('dtopic')->getTopicNameAndSecurityType($forumId) ;

                    $subject = $emailConfig['addGroupToForum']['other'] . ' "' . $forumName . '"';
                    $nickname = Auth::instance()->get_user()->nickname;

                    $mail_body  = $nickname . ' ' . $emailConfig['addGroupToForum']['action'] . ' "' . $forumName . '"<br/>';
                    $mail_body .=  "URL: " . $URL;

                    $header  = 'MIME-Version: 1.0' . "\r\n";
                    $header .= 'Content-type: text/html;' . "\r\n";
                    $header .= "From: ".  $emailConfig['from_name'] . " <" . $emailConfig['mail_from'] . ">\r\n";

                    foreach ($result as $userEmail)
                    {
                        if ($userEmail != '')
                        {
                            mail($userEmail, $subject, $mail_body, $header);
                        }
                    }
                }
                break;


            case 'deleteGroupFromForum' :

                $result = (is_null($isTopic)) ? self::prepareUsersMail($forumId) : self::prepareUsersMailforTopic($forumId);

                if (count($result) > 0)
                {
                    list($forumName, ) = (is_null($isTopic)) ? DB_ORM::model('dforum')->getForumNameAndSecurityType($forumId) :
                                                               DB_ORM::model('dtopic')->getTopicNameAndSecurityType($forumId) ;

                    $subject = $emailConfig['deleteGroupFromForum']['other'] . ' "' . $forumName . '"';
                    $nickname = Auth::instance()->get_user()->nickname;

                    $mail_body  = $nickname . ' ' . $emailConfig['deleteGroupFromForum']['action'] . ' "' . $forumName . '"<br/>';
                    $mail_body .=  "URL: " . $URL;

                    $header  = 'MIME-Version: 1.0' . "\r\n";
                    $header .= 'Content-type: text/html;' . "\r\n";
                    $header .= "From: ".  $emailConfig['from_name'] . " <" . $emailConfig['mail_from'] . ">\r\n";

                    foreach ($result as $userEmail)
                    {
                        if ($userEmail != '')
                        {
                            mail($userEmail, $subject, $mail_body, $header);
                        }
                    }
                }
                break;

        endswitch;
    }

    public function action_ajaxGetNewMessages() {
        $this->auto_render = false;
        $result = null;
        if (Request::initial()->is_ajax()) {
            $lastMessageId = Arr::get($this->request->post(), 'lastMessageId', null);
            $forumId = Arr::get($this->request->post(), 'forumId', null);

            $array = DB_ORM::model('dforum_messages')->getNewMessages($forumId,$lastMessageId);
            $result = json_encode($array);
        }

        echo $result;
    }

    public function action_ajaxGetEditedMessages() {
        $this->auto_render = false;
        $result = null;
        if (Request::initial()->is_ajax()) {

            $forumId = Arr::get($this->request->post(), 'forumId', null);

            $array = DB_ORM::model('dforum_messages')->getEditedMessages($forumId);
            $result = json_encode($array);
        }

        echo $result;
    }

    public function action_ajaxGetMessagesId() {
        $this->auto_render = false;
        $result = null;
        if (Request::initial()->is_ajax()) {

            $forumId = Arr::get($this->request->post(), 'forumId', null);

            $array = DB_ORM::model('dforum_messages_forum')->getAllMessagesIdByForumId($forumId);
            $result = json_encode($array);
        }

        echo $result;
    }

    public function action_ajaxUpdateNotification() {
        $this->auto_render = false;

        if(Request::initial()->is_ajax() && Auth::instance()->logged_in()) {
            $forumId = Arr::get($this->request->post(), 'forumId', null);
            $userId = Auth::instance()->get_user()->id;

            DB_ORM::model('dforum_users')->updateNotifications($forumId, $userId, Arr::get($this->request->post(), 'notification', 1));
        }
    }

    public function action_ajaxUpdateTopicNotification() {
        $this->auto_render = false;

        if(Request::initial()->is_ajax() && Auth::instance()->logged_in()) {
            $topicId = Arr::get($this->request->post(), 'topicId', null);
            $userId = Auth::instance()->get_user()->id;

            DB_ORM::model('dtopic_users')->updateNotifications($topicId, $userId, Arr::get($this->request->post(), 'notification', 1));
        }
    }

}