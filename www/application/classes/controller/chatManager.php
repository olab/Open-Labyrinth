<?php defined('SYSPATH') or die('No direct script access.');

class Controller_ChatManager extends Controller_Base {
    
    public function action_index() {
        $mapId = $this->request->param('id', NULL);
        
        if($mapId != NULL) {
            $this->templateData['map'] = DB_ORM::model('map', array((int)$mapId));
            $this->templateData['chats'] = DB_ORM::model('map_chat')->getChatsByMap($mapId);
            
            $chatView = View::factory('labyrinth/chat/view');
            $chatView->set('templateData', $this->templateData);
            
            $leftView = View::factory('labyrinth/labyrinthEditorMenu');
            $leftView->set('templateData', $this->templateData);

            $this->templateData['center'] = $chatView;
            $this->templateData['left'] = $leftView;
            unset($this->templateData['right']);
            $this->template->set('templateData', $this->templateData);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }
    
    public function action_addChat() {
        $mapId = $this->request->param('id', NULL);
        $chatQuestionCount = $this->request->param('id2', NULL);
        
        if($mapId != NULL) {
            $this->templateData['map'] = DB_ORM::model('map', array((int)$mapId));
            $this->templateData['counters'] = DB_ORM::model('map_counter')->getCountersByMap((int)$mapId);
            
            if($chatQuestionCount != NULL) {
                $this->templateData['question_count'] = $chatQuestionCount;
            } else {
                $this->templateData['question_count'] = 2;
            }
            
            $addChatView = View::factory('labyrinth/chat/add');
            $addChatView->set('templateData', $this->templateData);
            
            $leftView = View::factory('labyrinth/labyrinthEditorMenu');
            $leftView->set('templateData', $this->templateData);

            $this->templateData['center'] = $addChatView;
            $this->templateData['left'] = $leftView;
            unset($this->templateData['right']);
            $this->template->set('templateData', $this->templateData);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }
    
    public function action_addChatQuestion() {
        $mapId = $this->request->param('id', NULL);
        $chatQuestionCount = $this->request->param('id2', NULL);
        
        if($mapId != NULL) {
            if($chatQuestionCount != NULL) {
                Request::initial()->redirect(URL::base().'chatManager/addChat/'.$mapId.'/'.$chatQuestionCount);
            }
        } else {
            Request::initial()->redirect(URL::base());
        }
    }
    
    public function action_removeAddChatQuestion() {
        $mapId = $this->request->param('id', NULL);
        $chatQuestionCount = $this->request->param('id2', NULL);
        
        if($mapId != NULL) {
            if($chatQuestionCount != NULL) {
                $chatQuestionCount--;
                Request::initial()->redirect(URL::base().'chatManager/addChat/'.$mapId.'/'.$chatQuestionCount);
            }
        } else {
            Request::initial()->redirect(URL::base());
        }
    }
    
    public function action_saveNewChat() {
        $mapId = $this->request->param('id', NULL);
        $chatQuestionCount = $this->request->param('id2', NULL);
        
        if($_POST and $mapId != NULL) {
            if($chatQuestionCount != NULL) {
                DB_ORM::model('map_chat')->addChat($mapId, $chatQuestionCount, $_POST);
                Request::initial()->redirect(URL::base().'chatManager/index/'.$mapId);
            }
        } else {
            Request::initial()->redirect(URL::base());
        }
    }
    
    public function action_deleteChat() {
        $mapId = $this->request->param('id', NULL);
        $chatId = $this->request->param('id2', NULL);
        
        if($mapId != NULL and $chatId != NULL) {
            DB_ORM::model('map_chat', array((int)$chatId))->delete();
            //DB_ORM::model('map_chat_element')->deleteElementsByChatId($chatId);
            Request::initial()->redirect(URL::base().'chatManager/index/'.$mapId);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }
    
    public function action_editChat() {
        $mapId = $this->request->param('id', NULL);
        $chatId = $this->request->param('id2', NULL);
        $chatQuestionCount = $this->request->param('id3', NULL);
        
        if($mapId != NULL and $chatId != NULL and $chatQuestionCount != NULL) {
            $this->templateData['map'] = DB_ORM::model('map', array((int)$mapId));
            $this->templateData['counters'] = DB_ORM::model('map_counter')->getCountersByMap((int)$mapId);
            $this->templateData['question_count'] = $chatQuestionCount;
            $this->templateData['chat'] = DB_ORM::model('map_chat', array((int)$chatId));
            
            $editChatView = View::factory('labyrinth/chat/edit');
            $editChatView->set('templateData', $this->templateData);
            
            $leftView = View::factory('labyrinth/labyrinthEditorMenu');
            $leftView->set('templateData', $this->templateData);

            $this->templateData['center'] = $editChatView;
            $this->templateData['left'] = $leftView;
            unset($this->templateData['right']);
            $this->template->set('templateData', $this->templateData);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }
    
    public function action_addEditChatQuestion() {
        $mapId = $this->request->param('id', NULL);
        $chatId = $this->request->param('id2', NULL);
        $chatQuestionCount = $this->request->param('id3', NULL);
        
        if($mapId != NULL and $chatId != NULL and $chatQuestionCount!= NULL) {
            Request::initial()->redirect(URL::base().'chatManager/editChat/'.$mapId.'/'.$chatId.'/'.$chatQuestionCount);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }
    
    public function action_removeEditChatQuestion() {
        $mapId = $this->request->param('id', NULL);
        $chatId = $this->request->param('id2', NULL);
        $chatQuestionCount = $this->request->param('id3', NULL);
        $senderNumber = $this->request->param('id4', NULL);
        
        if($mapId != NULL and $chatId != NULL and $chatQuestionCount!= NULL and $senderNumber != NULL) {
            DB_ORM::model('map_chat_element')->deleteElemtnsByNumber($chatId, $senderNumber - 1);
            $chatQuestionCount--;
            Request::initial()->redirect(URL::base().'chatManager/editChat/'.$mapId.'/'.$chatId.'/'.$chatQuestionCount);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }
    
    public function action_updateChat() {
        $mapId = $this->request->param('id', NULL);
        $chatId = $this->request->param('id2', NULL);
        $chatQuestionCount = $this->request->param('id3', NULL);
        
        if($_POST and $mapId != NULL and $chatId != NULL and $chatQuestionCount!= NULL) {
            DB_ORM::model('map_chat')->updateChat($chatId, $chatQuestionCount, $_POST);
            Request::initial()->redirect(URL::base().'chatManager/index/'.$mapId);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }
}
    
?>
