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

        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Discussion Forums'))->set_url(URL::base() . 'dforumManager'));

        unset($this->templateData['right']);
        $this->template->set('templateData', $this->templateData);
    }

    public function action_index() {

        $openForums = DB_ORM::model('dforum')->getAllOpenForums();
        $privateForums = DB_ORM::model('dforum')->getAllPrivateForums();

        if (count($openForums) <= 0) $openForums = array();
        if (count($privateForums) <= 0) $privateForums = array();

        $this->templateData['forums'] = array_merge($openForums, $privateForums);

        $view = View::factory('dforum/view');
        $view->set('templateData', $this->templateData);

        $this->templateData['center'] = $view;
        $this->template->set('templateData', $this->templateData);
    }

    public function action_viewForum() {

        $this->templateData['forum'] = DB_ORM::model('dforum')->getForumById($this->request->param('id', 0));

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

        $view = View::factory('dforum/addEditForum');
        $view->set('templateData', $this->templateData);

        $this->templateData['center'] = $view;
        $this->template->set('templateData', $this->templateData);
    }

    public function action_editForum(){
        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Edit forum'))->set_url(URL::base() . 'dforumManager/editForum'));

        $this->templateData['forum_id'] = $this->request->param('id', NULL);

        list($this->templateData['name'], $this->templateData['security']) = DB_ORM::model('dforum')->getForumNameAndSecurityType($this->templateData['forum_id']);

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
        $forumId = Arr::get($this->request->post(), 'forum_id', NULL);

        list($oldName, ) = DB_ORM::model('dforum')->getForumNameAndSecurityType($forumId);

        $users = Arr::get($this->request->post(), 'users', NULL);
        $groups = Arr::get($this->request->post(), 'groups', NULL);

        DB_ORM::model('dforum')->updateForum($forumName, $securityType, $forumId);
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
        $security = Arr::get($this->request->post(), 'security', NULL);

        $users = Arr::get($this->request->post(), 'users', NULL);
        $groups = Arr::get($this->request->post(), 'groups', NULL);

        $forumId = DB_ORM::model('dforum')->createForum($forumName,$security);
        $messageID = DB_ORM::model('dforum_messages')->createMessage($forumId, $firstMessage);

        DB_ORM::model('dforum_users')->updateUsers($forumId, $users);
        DB_ORM::model('dforum_groups')->updateGroups($forumId, $groups);

        self::action_mail('createForum',$forumId, $firstMessage, $forumName);

        Request::initial()->redirect(URL::base() . 'dforumManager');
    }

    public function action_addMessage(){
        $message = Arr::get($this->request->post(), 'message', NULL);
        $forumId = Arr::get($this->request->post(), 'forum', NULL);

        $messageId = DB_ORM::model('dforum_messages')->createMessage($forumId, $message);

        list($forumName, ) = DB_ORM::model('dforum')->getForumNameAndSecurityType($forumId);

        self::action_mail('addMsg', $forumId,  $message, $forumName, '', $messageId);

        Request::initial()->redirect(URL::base() . 'dforumManager/viewForum/' . $forumId);
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

        Request::initial()->redirect(URL::base() . 'dforumManager/viewForum/' . $forumId);
    }

    private static function prepareUsersMail($forumId, $usersEmail = '', $type = '') {
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

            $users = DB_ORM::model('dforum_users')->getAllUsersInForumInfo($forumId);

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
            $users = DB_ORM::model('dforum_users')->getAllUsersInForumInfo($forumId);

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

            if (isset($key)) {
                unset($result[$key]);
            }
        }

        return $result;
    }

    public static function action_mail($action, $forumId, $message = '', $forumName = '', $emails = '', $messageId = null) {
        $emailConfig = Kohana::$config->load('dforum_email');
        $URL = URL::base('http', true) . 'dforumManager/viewForum/' . $forumId;

        switch($action) :

            case 'addMsg' :

                $result = self::prepareUsersMail($forumId);

                // send mail to Author of action
                $subject = $emailConfig['add_msg']['author'] . ' "' . $forumName . '"';
                $nickname = Auth::instance()->get_user()->nickname;
                $emailTo =  Auth::instance()->get_user()->email;

                $mail_body  = $nickname . ' ' . $emailConfig['add_msg']['action'] . ' "' . $forumName . '"<br/>';
                $mail_body .= '---------------------------------------<br/>';
                $mail_body .= $message . '<br/>';
                $mail_body .= '---------------------------------------<br/>';
                $mail_body .=  "URL: " . $URL . ($messageId != null ? '#m-' . $messageId : '');

                $header  = 'MIME-Version: 1.0' . "\r\n";
                $header .= 'Content-type: text/html;' . "\r\n";
                $header .= "From: ".  $emailConfig['from_name'] . " <" . $emailConfig['mail_from'] . ">\r\n";

                mail($emailTo, $subject, $mail_body, $header);

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

                $result = self::prepareUsersMail($forumId);

                // send mail to Author of action
                $subject = $emailConfig['delete_msg']['author'] . ' "' . $forumName . '"';
                $nickname = Auth::instance()->get_user()->nickname;
                $emailTo =  Auth::instance()->get_user()->email;

                $mail_body  = $nickname . ' ' . $emailConfig['delete_msg']['action'] . ' "' . $forumName . '"<br/>';
                $mail_body .= '---------------------------------------<br/>';
                $mail_body .= $message . '<br/>';
                $mail_body .= '---------------------------------------<br/>';
                $mail_body .=  "URL: " . $URL . ($messageId != null ? '#m-' . $messageId : '');

                $header  = 'MIME-Version: 1.0' . "\r\n";
                $header .= 'Content-type: text/html;' . "\r\n";
                $header .= "From: ".  $emailConfig['from_name'] . " <" . $emailConfig['mail_from'] . ">\r\n";

                mail($emailTo, $subject, $mail_body, $header);

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

                $result = self::prepareUsersMail($forumId);

                // send mail to Author of action
                $subject = $emailConfig['update_msg']['author'] . ' "' . $forumName . '"';
                $nickname = Auth::instance()->get_user()->nickname;
                $emailTo =  Auth::instance()->get_user()->email;

                $mail_body  = $nickname . ' ' . $emailConfig['update_msg']['action'] . ' "' . $forumName . '"<br/>';
                $mail_body .= '---------------------------------------<br/>';
                $mail_body .= $message . '<br/>';
                $mail_body .= '---------------------------------------<br/>';
                $mail_body .=  "URL: " . $URL . ($messageId != null ? '#m-' . $messageId : '');

                $header  = 'MIME-Version: 1.0' . "\r\n";
                $header .= 'Content-type: text/html;' . "\r\n";
                $header .= "From: ".  $emailConfig['from_name'] . " <" . $emailConfig['mail_from'] . ">\r\n";

                mail($emailTo, $subject, $mail_body, $header);

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

                $result = self::prepareUsersMail($forumId);

                // send mail to Author of action
                $subject = $emailConfig['create_forum']['author'] . ' "' . $forumName . '"';
                $nickname = Auth::instance()->get_user()->nickname;
                $emailTo =  Auth::instance()->get_user()->email;

                $mail_body  = $nickname . ' ' . $emailConfig['create_forum']['action'] . ' "' . $forumName . '"<br/>';
                $mail_body .= '---------------------------------------<br/>';
                $mail_body .= $message . '<br/>';
                $mail_body .= '---------------------------------------<br/>';
                $mail_body .=  "URL: " . $URL;

                $header  = 'MIME-Version: 1.0' . "\r\n";
                $header .= 'Content-type: text/html;' . "\r\n";
                $header .= "From: ".  $emailConfig['from_name'] . " <" . $emailConfig['mail_from'] . ">\r\n";

                mail($emailTo, $subject, $mail_body, $header);

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

                $result = self::prepareUsersMail($forumId);

                // send mail to Author of action
                $subject = $emailConfig['update_forum']['author'] . ' "' . $oldForumName . '"(old name)';
                $nickname = Auth::instance()->get_user()->nickname;
                $emailTo =  Auth::instance()->get_user()->email;

                $mail_body  = $nickname . ' ' . $emailConfig['update_forum']['action'] . ' "' . $forumName . '"<br/>';
                $mail_body .= '---------------------------------------<br/>';
                $mail_body .= $message . '<br/>';
                $mail_body .= '---------------------------------------<br/>';
                $mail_body .=  "URL: " . $URL;

                $header  = 'MIME-Version: 1.0' . "\r\n";
                $header .= 'Content-type: text/html;' . "\r\n";
                $header .= "From: ".  $emailConfig['from_name'] . " <" . $emailConfig['mail_from'] . ">\r\n";

                mail($emailTo, $subject, $mail_body, $header);

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
                $URL = URL::base() . 'dforumManager/';

                $result = self::prepareUsersMail($forumId);

                // send mail to Author of action
                $subject = $emailConfig['delete_forum']['author'] . ' "' . $forumName . '"';
                $nickname = Auth::instance()->get_user()->nickname;
                $emailTo =  Auth::instance()->get_user()->email;

                $mail_body  = $nickname . ' ' . $emailConfig['delete_forum']['action'] . ' "' . $forumName . '"<br/>';
                $mail_body .= '---------------------------------------<br/>';
                $mail_body .= $message . '<br/>';
                $mail_body .= '---------------------------------------<br/>';
                $mail_body .=  "URL: " . $URL;

                $header  = 'MIME-Version: 1.0' . "\r\n";
                $header .= 'Content-type: text/html;' . "\r\n";
                $header .= "From: ".  $emailConfig['from_name'] . " <" . $emailConfig['mail_from'] . ">\r\n";

                mail($emailTo, $subject, $mail_body, $header);

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

                list($forumName, ) = DB_ORM::model('dforum')->getForumNameAndSecurityType($forumId);

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

                $result = self::prepareUsersMail($forumId, $emails,'deleteUser');

                if (count($result) > 0)
                {

                    list($forumName, ) = DB_ORM::model('dforum')->getForumNameAndSecurityType($forumId);

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

                $result = self::prepareUsersMail($forumId, $emails, 'groupAdd');

                if (count($result) > 0)
                {
                    list($forumName, ) = DB_ORM::model('dforum')->getForumNameAndSecurityType($forumId);

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

                $result = self::prepareUsersMail($forumId, $emails, 'groupDelete');

                if (count($result) > 0)
                {
                    list($forumName, ) = DB_ORM::model('dforum')->getForumNameAndSecurityType($forumId);

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

}