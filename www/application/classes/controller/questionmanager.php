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

class Controller_QuestionManager extends Controller_Base {

    public function before() {
        $this->templateData['labyrinthSearch'] = 1;

        parent::before();

        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('My Labyrinths'))->set_url(URL::base() . 'authoredLabyrinth'));
    }

    public function action_index() {
        $mapId = $this->request->param('id', NULL);

        if ($mapId != NULL) {
            $this->templateData['map'] = DB_ORM::model('map', array((int) $mapId));
            $this->templateData['counters'] = DB_ORM::model('map_counter')->getCountersByMap($mapId);
            $this->templateData['questions'] = DB_ORM::model('map_question')->getQuestionsByMap((int) $mapId);
            $this->templateData['question_types'] = DB_ORM::model('map_question_type')->getAllTypes();

            Breadcrumbs::add(Breadcrumb::factory()->set_title($this->templateData['map']->name)->set_url(URL::base() . 'labyrinthManager/global/' . $mapId));
            Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Questions'))->set_url(URL::base() . 'questionManager/index/' . $mapId));

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
    
    public function action_question() {
        $mapId = $this->request->param('id', 0);
        $typeId = $this->request->param('id2', 0);
        $questionId = $this->request->param('id3', 0);

        $map = DB_ORM::model('map', array((int)$mapId));
        $type = DB_ORM::model('map_question_type', array((int) $typeId));
        if($map != null && $type != null) {
            $this->templateData['map'] = DB_ORM::model('map', array((int)$mapId));
            $this->templateData['type'] = DB_ORM::model('map_question_type', array((int) $typeId));
            $this->templateData['counters'] = DB_ORM::model('map_counter')->getCountersByMap((int) $mapId);
            $this->templateData['nodes'] = DB_ORM::model('map_node')->getNodesByMap((int) $mapId);
            
            Breadcrumbs::add(Breadcrumb::factory()->set_title($this->templateData['map']->name)->set_url(URL::base() . 'labyrinthManager/global/' . $mapId));
            Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Questions'))->set_url(URL::base() . 'questionManager/index/' . $mapId));
            
            if($questionId != null) {
                Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Edit'))->set_url(URL::base() . 'questionManager/question/' . $mapId . '/' . $typeId . '/' . $questionId));
                $this->templateData['question'] = DB_ORM::model('map_question', array((int)$questionId));
                if($this->templateData['question']->settings != null) {
                    $this->templateData['questionSettings'] = json_decode($this->templateData['question']->settings);

                    if ($this->templateData['question']->type->value == 'area' || $this->templateData['question']->type->value == 'text') {
                        $this->templateData['isCorrect'] = $this->templateData['questionSettings'][1];
                        $this->templateData['question']->settings = $this->templateData['questionSettings'][0];
                    }
                }
            } else {
                Breadcrumbs::add(Breadcrumb::factory()->set_title(__('New'))->set_url(URL::base() . 'questionManager/question/' . $mapId . '/' . $typeId));
            }
            
            $view = View::factory('labyrinth/question/' . $type->template_name);
            $view->set('templateData', $this->templateData);
            
            $leftView = View::factory('labyrinth/labyrinthEditorMenu');
            $leftView->set('templateData', $this->templateData);
            
            $this->templateData['center'] = $view;
            $this->templateData['left'] = $leftView;
            unset($this->templateData['right']);
            $this->template->set('templateData', $this->templateData);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }
    
    public function action_questionPOST() {
        $mapId = $this->request->param('id', 0);
        $postType = Arr::get($_POST, 'question_type', NULL);
        $typeId = ($postType != NULL) ? $postType : $this->request->param('id2', 0);
        $questionId = $this->request->param('id3', 0);
        
        $map = DB_ORM::model('map', array((int)$mapId));
        $type = DB_ORM::model('map_question_type', array((int) $typeId));
        if($_POST != null && $map != null && $type != null) {

            if (isset($_POST['isCorrect'])) {
                $rule = $_POST['settings'];
                $_POST['settings'] = json_encode(array($rule, $_POST['isCorrect']));
            }

            if($questionId == null || $questionId <= 0) {
                DB_ORM::model('map_question')->addQuestion($mapId, $type, $_POST);
            } else {
                DB_ORM::model('map_question')->updateQuestion($questionId, $type, $_POST);
            }
            
            Request::initial()->redirect(URL::base() . 'questionManager/index/' . $mapId);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

    public function action_deleteQuestion() {
        $mapId = $this->request->param('id', NULL);
        $questionId = $this->request->param('id2', NULL);

        if ($mapId != NULL and $questionId != NULL) {
            DB_ORM::model('map_question', array((int) $questionId))->delete();
            DB_ORM::model('map_question_response')->deleteByQuestion($questionId);

            Request::initial()->redirect(URL::base() . 'questionManager/index/' . $mapId);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }
    
    public function action_duplicateQuestion() {
        $mapId = $this->request->param('id', null);
        $questionId = $this->request->param('id2', null);
        
        if($mapId != null && $questionId != null) {
            DB_ORM::model('map_question')->duplicateQuestion($questionId);
            Request::initial()->redirect(URL::base() . 'questionManager/index/' . $mapId);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }
    
    public function action_copyQuestion() {
        $mapId = $this->request->param('id', null);
        
        if($mapId != null && $_POST != null) {
            DB_ORM::model('map_question')->copyQuestion($mapId, $_POST);
            Request::initial()->redirect(URL::base() . 'questionManager/index/' . $mapId);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }
}