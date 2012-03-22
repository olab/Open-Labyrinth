<?php defined('SYSPATH') or die('No direct script access.');

class Controller_QuestionManager extends Controller_Base {
    
    public function action_index() {
        $mapId = $this->request->param('id', NULL);
        
        if($mapId != NULL) {
            $this->templateData['map'] = DB_ORM::model('map', array((int)$mapId));
            $this->templateData['questions'] = DB_ORM::model('map_question')->getQuestionsByMap((int)$mapId);
            $this->templateData['question_types'] = DB_ORM::model('map_question_type')->getAllTypes();

            $questionView = View::factory('labyrinth/question/view');
            $questionView->set('templateData', $this->templateData);

            $leftView = View::factory('labyrinth/labyrinthEditorMenu');
            $leftView->set('templateData', $this->templateData);

            $this->templateData['center'] = $questionView;
            $this->templateData['left'] = $leftView;
            unset($this->templateData['right']);
            $this->template->set('templateData', $this->templateData);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }
    
    public function action_addQuestion() {
        $mapId = $this->request->param('id', NULL);
        $templateType = $this->request->param('id2', NULL);
        
        if($mapId != NULL and $templateType != NULL) {
            $type = DB_ORM::model('map_question_type', array((int)$templateType));
            
            if($type) {
                $this->templateData['map'] = DB_ORM::model('map', array((int)$mapId));
                $this->templateData['questionType'] = $templateType;
                $this->templateData['args'] = $type->template_args;
                $this->templateData['counters'] = DB_ORM::model('map_counter')->getCountersByMap((int)$mapId);
               
                
                $editView = View::factory('labyrinth/question/'.$type->template_name);
                $editView->set('templateData', $this->templateData);

                $leftView = View::factory('labyrinth/labyrinthEditorMenu');
                $leftView->set('templateData', $this->templateData);

                $this->templateData['center'] = $editView;
                $this->templateData['left'] = $leftView;
                unset($this->templateData['right']);
                $this->template->set('templateData', $this->templateData);
            }
        } else {
            Request::initial()->redirect(URL::base());
        }
    }
    
    public function action_editQuestion() {
        $mapId = $this->request->param('id', NULL);
        $templateType = $this->request->param('id2', NULL);
        $questionId = $this->request->param('id3', NULL);
        
        if($mapId != NULL and $templateType != NULL and $questionId != NULL) {
            $type = DB_ORM::model('map_question_type', array((int)$templateType));
            
            if($type) {
                $this->templateData['map'] = DB_ORM::model('map', array((int)$mapId));
                $this->templateData['questionType'] = $templateType;
                $this->templateData['args'] = $type->template_args;
                $this->templateData['counters'] = DB_ORM::model('map_counter')->getCountersByMap((int)$mapId);
                $this->templateData['question'] = DB_ORM::model('map_question', array((int)$questionId));
               
                
                $editView = View::factory('labyrinth/question/'.$type->template_name);
                $editView->set('templateData', $this->templateData);

                $leftView = View::factory('labyrinth/labyrinthEditorMenu');
                $leftView->set('templateData', $this->templateData);

                $this->templateData['center'] = $editView;
                $this->templateData['left'] = $leftView;
                unset($this->templateData['right']);
                $this->template->set('templateData', $this->templateData);
            }
        } else {
            Request::initial()->redirect(URL::base());
        }
    }
    
    public function action_updateQuestion() {
        $mapId = $this->request->param('id', NULL);
        $templateType = $this->request->param('id2', NULL);
        $questionId = $this->request->param('id3', NULL);
        
        if($_POST and $mapId != NULL and $templateType != NULL and $questionId != NULL) {
            $type = DB_ORM::model('map_question_type', array((int)$templateType));
            
            if($type) {
                DB_ORM::model('map_question')->updateQuestion($questionId, $type, $_POST);
            }
            
            Request::initial()->redirect(URL::base().'questionManager/index/'.$mapId);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }
    
    public function action_saveNewQuestion() {
        $mapId = $this->request->param('id', NULL);
        $templateType = $this->request->param('id2', NULL);
        
        if($_POST and $mapId != NULL and $templateType != NULL) {
            $type = DB_ORM::model('map_question_type', array((int)$templateType));
            
            if($type) {
                DB_ORM::model('map_question')->addQuestion($mapId, $type, $_POST);
            }
            
            Request::initial()->redirect(URL::base().'questionManager/index/'.$mapId);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }
    
    public function action_deleteQuestion() {
        $mapId = $this->request->param('id', NULL);
        $questionId = $this->request->param('id2', NULL);
        
        if($mapId != NULL and $questionId != NULL) {
            DB_ORM::model('map_question', array((int)$questionId))->delete();
            DB_ORM::model('map_question_response')->deleteByQuestion($questionId);
            
            Request::initial()->redirect(URL::base().'questionManager/index/'.$mapId);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }
}
    
?>
