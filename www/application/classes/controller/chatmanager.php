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

class Controller_ChatManager extends Controller_Base {

    public function before()
    {
        $this->templateData['labyrinthSearch'] = 1;

        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('My Labyrinths'))->set_url(URL::base().'authoredLabyrinth'));
        parent::before();
    }

    public function action_index()
    {
        $mapId = $this->request->param('id', NULL);
        DB_ORM::model('User')->can('edit', array('mapId' => $mapId));

        if ( ! $mapId) Controller::redirect(URL::base());

        $this->templateData['map'] = DB_ORM::model('map', array((int) $mapId));
        $this->templateData['chats'] = DB_ORM::model('map_chat')->getChatsByMap($mapId);

        $ses = Session::instance();
        if($ses->get('warningMessage'))
        {
            $this->templateData['warningMessage'] = $ses->get('warningMessage');
            $this->templateData['listOfUsedReferences'] = $ses->get('listOfUsedReferences');
            $ses->delete('listOfUsedReferences');
            $ses->delete('warningMessage');
        }

        Breadcrumbs::add(Breadcrumb::factory()->set_title($this->templateData['map']->name)->set_url(URL::base() . 'labyrinthManager/global/' . $mapId));
        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Chats'))->set_url(URL::base() . 'chatManager/index/' . $mapId));

        $this->templateData['center'] = View::factory('labyrinth/chat/view')->set('templateData', $this->templateData);
        $this->templateData['left'] = View::factory('labyrinth/labyrinthEditorMenu')->set('templateData', $this->templateData);;
        $this->template->set('templateData', $this->templateData);
    }

    public function action_addChat()
    {
        $mapId = $this->request->param('id', NULL);

        if ($mapId == NULL) Controller::redirect(URL::base());

        DB_ORM::model('User')->can('edit', array('mapId' => $mapId));

        $this->templateData['map']              = DB_ORM::model('map', array((int) $mapId));
        $this->templateData['counters']         = DB_ORM::model('map_counter')->getCountersByMap((int) $mapId);
        $this->templateData['question_count']   = 1;
        $this->templateData['center']           = View::factory('labyrinth/chat/add')->set('templateData', $this->templateData);
        $this->templateData['left']             = View::factory('labyrinth/labyrinthEditorMenu')->set('templateData', $this->templateData);

        unset($this->templateData['right']);
        Breadcrumbs::add(Breadcrumb::factory()->set_title($this->templateData['map']->name)->set_url(URL::base() . 'labyrinthManager/global/' . $mapId));
        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Chats'))->set_url(URL::base() . 'chatManager/index/' . $mapId));
        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('New'))->set_url(URL::base() . 'chatManager/addChat/' . $mapId));

        $this->template->set('templateData', $this->templateData);
    }

    public function action_saveNewChat() {
        $mapId = $this->request->param('id', NULL);

        if ($_POST and $mapId != NULL) {
            DB_ORM::model('map_chat')->addChat($mapId, $_POST);
            Controller::redirect(URL::base() . 'chatManager/index/' . $mapId);
        } else {
            Controller::redirect(URL::base());
        }
    }

    public function action_deleteChat()
    {
        $mapId = $this->request->param('id', NULL);
        $chatId = $this->request->param('id2', NULL);

        if ($mapId AND $chatId)
        {
            DB_ORM::model('User')->can('edit', array('mapId' => $mapId));
            $references = DB_ORM::model('map_node_reference')->getByElementType($chatId, 'CHAT');
            if ($references != NULL)
            {
                $ses = Session::instance();
                $ses->set('listOfUsedReferences', CrossReferences::getListReferenceForView($references));
                $ses->set('warningMessage', 'The chat wasn\'t deleted. The selected chat is used in the following labyrinths:');
            } else {
                DB_ORM::model('map_chat', array((int) $chatId))->delete();
                DB_ORM::model('map_chat_element')->deleteElementsByChatId($chatId);
            }
            Controller::redirect(URL::base() . 'chatManager/index/' . $mapId);
        }
        else Controller::redirect(URL::base());
    }

    public function action_editChat() {
        $mapId = $this->request->param('id', NULL);
        $chatId = $this->request->param('id2', NULL);

        if ($mapId != NULL and $chatId != NULL)
        {
            DB_ORM::model('User')->can('edit', array('mapId' => $mapId));
            $this->templateData['map'] = DB_ORM::model('map', array((int) $mapId));
            $this->templateData['counters'] = DB_ORM::model('map_counter')->getCountersByMap((int) $mapId);
            $this->templateData['chat'] = DB_ORM::model('map_chat', array((int) $chatId));
            $this->templateData['question_count'] = count($this->templateData['chat']->elements);
            $usedElements = DB_ORM::model('map_node_reference')->getByElementType($chatId, 'CHAT');
            $this->templateData['used'] = count($usedElements);

            Breadcrumbs::add(Breadcrumb::factory()->set_title($this->templateData['map']->name)->set_url(URL::base() . 'labyrinthManager/global/' . $mapId));
            Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Chats'))->set_url(URL::base() . 'chatManager/index/' . $mapId));
            Breadcrumbs::add(Breadcrumb::factory()->set_title( $this->templateData['chat']->stem)->set_url(URL::base().'chatManager/editChat/'. $this->templateData['map']->id.'/'.$chatId.'/'.count($this->templateData['chat']->elements)));


            $editChatView = View::factory('labyrinth/chat/edit');
            $editChatView->set('templateData', $this->templateData);

            $leftView = View::factory('labyrinth/labyrinthEditorMenu');
            $leftView->set('templateData', $this->templateData);

            $this->templateData['center'] = $editChatView;
            $this->templateData['left'] = $leftView;
            unset($this->templateData['right']);
            $this->template->set('templateData', $this->templateData);
        } else {
            Controller::redirect(URL::base());
        }
    }

    public function action_updateChat() {
        $mapId = $this->request->param('id', NULL);
        $chatId = $this->request->param('id2', NULL);

        if ($_POST and $mapId != NULL and $chatId != NULL) {
            $references = DB_ORM::model('map_node_reference')->getNotParent($mapId, $chatId, 'CHAT');
            $privete = Arr::get($_POST, 'is_private');
            if($references != NULL && $privete){
                $ses = Session::instance();
                $ses->set('listOfUsedReferences', CrossReferences::getListReferenceForView($references));
                $ses->set('warningMessage', 'The chat wasn\'t set to private. The selected chat is used in the following labyrinths:');
                $_POST['is_private'] = FALSE;
            }
                DB_ORM::model('map_chat')->updateChat($chatId, $_POST);
            Controller::redirect(URL::base() . 'chatManager/index/' . $mapId);
        } else {
            Controller::redirect(URL::base());
        }
    }

}

