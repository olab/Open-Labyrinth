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

class Controller_QuestionManager extends Controller_Base
{

    public function before()
    {
        $this->templateData['labyrinthSearch'] = 1;

        parent::before();

        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('My Labyrinths'))->set_url(URL::base() . 'authoredLabyrinth'));
    }

    public function action_index()
    {
        $mapId = $this->request->param('id', null);

        if (!$mapId) {
            Controller::redirect(URL::base());
        }

        DB_ORM::model('User')->can('edit', array('mapId' => $mapId));

        $this->templateData['map'] = DB_ORM::model('map', array((int)$mapId));
        $this->templateData['counters'] = DB_ORM::model('map_counter')->getCountersByMap($mapId);
        $this->templateData['questions'] = DB_ORM::model('map_question')->getQuestionsByMap((int)$mapId);
        $this->templateData['question_types'] = DB_ORM::model('map_question_type')->getAllTypes();

        if (Auth::instance()->get_user()->type->name == 'superuser') {
            $this->templateData['isSuperuser'] = true;
        }

        $ses = Session::instance();
        if ($ses->get('warningMessage')) {
            $this->templateData['warningMessage'] = $ses->get('warningMessage');
            $this->templateData['listOfUsedReferences'] = $ses->get('listOfUsedReferences');
            $ses->delete('listOfUsedReferences');
            $ses->delete('warningMessage');
        }

        $this->templateData['center'] = View::factory('labyrinth/question/view')->set('templateData',
            $this->templateData);
        $this->templateData['left'] = View::factory('labyrinth/labyrinthEditorMenu')->set('templateData',
            $this->templateData);
        $this->template->set('templateData', $this->templateData);

        Breadcrumbs::add(Breadcrumb::factory()->set_title($this->templateData['map']->name)->set_url(URL::base() . 'labyrinthManager/global/' . $mapId));
        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Questions'))->set_url(URL::base() . 'questionManager/index/' . $mapId));
    }

    public function action_question()
    {
        $mapId = $this->request->param('id', 0);
        $typeId = $this->request->param('id2', 0);
        $questionId = $this->request->param('id3', 0);
        $map = DB_ORM::model('map', array((int)$mapId));
        $type = DB_ORM::model('map_question_type', array((int)$typeId));

        if (!($map AND $type)) {
            Controller::redirect(URL::base());
        }

        DB_ORM::model('User')->can('edit', array('mapId' => $mapId));

        $this->templateData['map'] = DB_ORM::model('map', array((int)$mapId));
        $this->templateData['type'] = DB_ORM::model('map_question_type', array((int)$typeId));
        $this->templateData['counters'] = DB_ORM::model('map_counter')->getCountersByMap((int)$mapId);
        $this->templateData['nodes'] = DB_ORM::model('map_node')->getNodesByMap((int)$mapId);
        $this->templateData['validation'] = DB_ORM::model('Map_Question_Validation')->getRecord($questionId);

        Breadcrumbs::add(Breadcrumb::factory()->set_title($this->templateData['map']->name)->set_url(URL::base() . 'labyrinthManager/global/' . $mapId));
        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Questions'))->set_url(URL::base() . 'questionManager/index/' . $mapId));

        if ($questionId) {
            $questionObj = DB_ORM::model('map_question', array((int)$questionId));

            $this->templateData['question'] = $questionObj;
            $this->templateData['used'] = count(DB_ORM::model('map_node_reference')->getByElementType($questionId,
                'QU'));

            if ($questionObj->settings) {
                $jsonSettings = json_decode($questionObj->settings);
                $this->templateData['questionSettings'] = $jsonSettings;

                if ($questionObj->type->value == 'area' OR $questionObj->type->value == 'text') {
                    $this->templateData['isCorrect'] = Arr::get($jsonSettings, 1, 0);
                    $this->templateData['question']->settings = Arr::get($jsonSettings, 0, '');
                }
            }
            Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Edit'))->set_url(URL::base() . 'questionManager/question/' . $mapId . '/' . $typeId . '/' . $questionId));
        } else {
            Breadcrumbs::add(Breadcrumb::factory()->set_title(__('New'))->set_url(URL::base() . 'questionManager/question/' . $mapId . '/' . $typeId));
        }

        if ($type->value == 'area' OR $type->value == 'text' OR $type->value == 'cml') {
            $validation = DB_ORM::model('Map_Question_Validation');
            $validators = array_merge($validation->one_parameter, $validation->two_parameter,
                $validation->three_parameter);
            ksort($validators);
            $this->templateData['validators'] = $validators;
        }

        if (!empty($questionObj) && in_array($typeId, [
            Model_Leap_Map_Question::ENTRY_TYPE_PCQ_GRID,
            Model_Leap_Map_Question::ENTRY_TYPE_MCQ_GRID
        ])) {
            $this->templateData['attributes'] = $this->createAttributesArray($questionObj);
        }

        $this->templateData['correctness'] = [
            [
                'value' => Model_Leap_Map_Question_Response::IS_CORRECT_CORRECT,
                'name' => __('Correct'),
            ],
            [
                'value' => Model_Leap_Map_Question_Response::IS_CORRECT_NEUTRAL,
                'name' => __('Neutral'),
            ],
            [
                'value' => Model_Leap_Map_Question_Response::IS_CORRECT_INCORRECT,
                'name' => __('Incorrect'),
            ],
        ];

        $this->templateData['center'] = View::factory('labyrinth/question/' . $type->template_name)->set('templateData',
            $this->templateData);
        $this->templateData['left'] = View::factory('labyrinth/labyrinthEditorMenu')->set('templateData',
            $this->templateData);
        $this->template->set('templateData', $this->templateData);
    }

    private function createAttributesArray(Model_Leap_Map_Question $question)
    {
        $result = [];
        foreach ($question->subQuestions as $subQuestion) {
            foreach ($subQuestion->responses as $subQuestionResponse) {
                $result[$subQuestion->id][$subQuestionResponse->parent_id]['feedback'] = $subQuestionResponse->feedback;
                $result[$subQuestion->id][$subQuestionResponse->parent_id]['correctness'] = $subQuestionResponse->is_correct;
                $result[$subQuestion->id][$subQuestionResponse->parent_id]['score'] = $subQuestionResponse->score;
            }
        }

        return $result;
    }

    public function action_questionSJT()
    {
        $post = $this->request->post();
        $stem = Arr::get($post, 'stem', '');
        $feedback = Arr::get($post, 'feedback', '');
        $score = Arr::get($post, 'score', '');
        $responses = Arr::get($post, 'responses', '');
        $mapId = $this->request->param('id', 0);
        $questionId = $this->request->param('id2', 0);

        if ($questionId) {
            DB_ORM::update('Map_Question')->set('stem', $stem)->set('feedback', $feedback)->where('id', '=',
                $questionId)->execute();
            foreach ($responses as $index => $response) {
                $isIndex = strpos($index, 'i');
                if ($isIndex !== false) {
                    $responseId = str_replace('i', '', $index);
                    DB_ORM::update('Map_Question_Response')->set('response', $response)->where('id', '=',
                        $responseId)->execute();
                    foreach (Arr::get($score, $index, array()) as $position => $points) {
                        DB_ORM::update('SJTResponse')->set('points', $points)->where('position', '=',
                            $position)->where('response_id', '=', $responseId)->execute();
                    }
                }
            }
        } else {
            $questionId = DB_ORM::insert('Map_Question')->column('map_id', $mapId)->column('stem',
                $stem)->column('entry_type_id', 8)->column('feedback', $feedback)->execute();
            foreach ($responses as $index => $response) {
                $responseId = DB_ORM::insert('Map_Question_Response')->column('question_id',
                    $questionId)->column('response', $response)->execute();
                foreach (Arr::get($score, $index, array()) as $position => $points) {
                    DB_ORM::insert('SJTResponse')->column('response_id', $responseId)->column('position',
                        $position)->column('points', $points)->execute();
                }
            }
        }
        Controller::redirect(URL::base() . 'questionManager/index/' . $mapId);
    }

    public function action_questionPOST()
    {
        $post = $this->request->post();
        $mapId = $this->request->param('id', 0);
        $postType = Arr::get($post, 'question_type', null);
        $validator = Arr::get($post, 'validator', 'no validator');
        $secondParameter = Arr::get($post, 'second_parameter');
        $errorMessage = Arr::get($post, 'error_message');
        $typeId = ($postType != null) ? $postType : $this->request->param('id2', 0);
        $questionId = $this->request->param('id3', 0);
        $map = DB_ORM::model('map', array((int)$mapId));
        $type = DB_ORM::model('map_question_type', array((int)$typeId));

        if (!($post AND $map AND $type)) {
            Controller::redirect(URL::base());
        }

        $post['settings'] = json_encode(array(Arr::get($post, 'settings', ''), Arr::get($post, 'isCorrect', 0)));

        if ($questionId) {
            $references = DB_ORM::model('map_node_reference')->getNotParent($mapId, $questionId, 'QU');
            $private = Arr::get($post, 'is_private');

            if ($references AND $private) {
                $ses = Session::instance();
                $ses->set('listOfUsedReferences', CrossReferences::getListReferenceForView($references));
                $ses->set('warningMessage',
                    'The question wasn\'t set to private. The selected question is used in the following labyrinths:');
                $post['is_private'] = false;
            }

            DB_ORM::model('map_question')->updateQuestion($questionId, $type, $post);
        } else {
            $questionId = DB_ORM::model('map_question')->addQuestion($mapId, $type, $post);
        }

        $validator == 'no validator'
            ? DB_ORM::model('Map_Question_Validation')->deleteByQuestionId($questionId)
            : DB_ORM::model('Map_Question_Validation')->update($questionId, $validator, $secondParameter,
            $errorMessage);

        Controller::redirect(URL::base() . 'questionManager/index/' . $mapId);
    }

    public function action_questionGridPOST()
    {
        $post = $this->request->post();
        $mapId = $this->request->param('id', 0);
        $postType = Arr::get($post, 'question_type', null);
        $existingSubQuestions = Arr::get($post, 'existingSubQuestions', []);
        $existingSubQuestionsOrder = Arr::get($post, 'existingSubQuestionsOrder', []);
        $subQuestions = Arr::get($post, 'subQuestions', []);
        $subQuestionsOrder = Arr::get($post, 'subQuestionsOrder', []);
        $responses = Arr::get($post, 'responses', []);
        $responsesOrder = Arr::get($post, 'responsesOrder', []);
        $goToAttributes = Arr::get($post, 'goToAttributes', false);
        $existingResponses = Arr::get($post, 'existingResponses', []);
        $existingResponsesOrder = Arr::get($post, 'existingResponsesOrder', []);
        $attributes = Arr::get($post, 'attributes', []);
        $typeId = ($postType != null) ? $postType : $this->request->param('id2', 0);
        $questionId = $this->request->param('id3', 0);
        $map = DB_ORM::model('map', array((int)$mapId));
        $type = DB_ORM::model('map_question_type', array((int)$typeId));

        if (!($post AND $map AND $type)) {
            Controller::redirect(URL::base());
        }

        if (!empty($questionId)) {
            $references = DB_ORM::model('map_node_reference')->getNotParent($mapId, $questionId, 'QU');
            $private = Arr::get($post, 'is_private');

            if ($references AND $private) {
                $ses = Session::instance();
                $ses->set('listOfUsedReferences', CrossReferences::getListReferenceForView($references));
                $ses->set('warningMessage',
                    'The question wasn\'t set to private. The selected question is used in the following labyrinths:');
                $post['is_private'] = false;
            }

            /** @var Model_Leap_Map_Question $question */
            $question = DB_ORM::model('Map_Question', array($questionId));

            if (!empty($typeId)) {
                $question->entry_type_id = $typeId;
            }
            $question->stem = Arr::get($post, 'stem', $question->stem);
            $question->feedback = Arr::get($post, 'feedback', $question->feedback);
            $question->show_answer = Arr::get($post, 'showAnswer', $question->show_answer);
            $question->counter_id = Arr::get($post, 'counter', $question->counter_id);
            $question->num_tries = Arr::get($post, 'tries', $question->num_tries);
            $question->show_submit = Arr::get($post, 'showSubmit', $question->show_submit);
            $question->redirect_node_id = Arr::get($post, 'redirectNode', $question->redirect_node_id);
            $question->submit_text = Arr::get($post, 'submitButtonText', $question->submit_text);
            $question->type_display = Arr::get($post, 'typeDisplay', $question->submit_text);
            $question->is_private = Arr::get($post, 'is_private', false);
            $question->settings = json_encode(Arr::get($post, 'settingsJSON', []));

            $question->save();
        } else {
            $questionId = DB_ORM::insert('map_question')
                ->column('map_id', $mapId)
                ->column('entry_type_id', $type->id)
                ->column('stem', Arr::get($post, 'stem', ''))
                ->column('feedback', Arr::get($post, 'feedback', ''))
                ->column('show_answer', (int)Arr::get($post, 'showAnswer', 0))
                ->column('counter_id', (int)Arr::get($post, 'counter', 0))
                ->column('num_tries', (int)Arr::get($post, 'tries', 1))
                ->column('show_submit', (int)Arr::get($post, 'showSubmit', 0))
                ->column('redirect_node_id', (int)Arr::get($post, 'redirectNode', null))
                ->column('submit_text', Arr::get($post, 'submitButtonText', 'Submit'))
                ->column('type_display', (int)Arr::get($post, 'typeDisplay', 0))
                ->column('is_private', (int)Arr::get($post, 'is_private', false) ? 1 : 0)
                ->column('settings', json_encode(Arr::get($post, 'settingsJSON', [])))
                ->execute();
        }

        if (empty($questionId) || !is_numeric($questionId)) {
            throw new Exception('Invalid Question id');
        }

        //save attributes
        foreach ($attributes as $subQuestionId => $subQuestionArray) {
            foreach ($subQuestionArray as $responseId => $responseArray) {

                $subQuestionResponse = DB_ORM::select('Map_Question_Response')
                    ->where('parent_id', '=', $responseId)
                    ->where('question_id', '=', $subQuestionId)
                    ->limit(1)
                    ->query()
                    ->fetch(0);

                if (empty($subQuestionResponse)) {
                    $subQuestionResponse = new Model_Leap_Map_Question_Response();
                    $subQuestionResponse->parent_id = $responseId;
                    $subQuestionResponse->question_id = $subQuestionId;
                }

                if (isset($responseArray['feedback'])) {
                    $subQuestionResponse->feedback = trim($responseArray['feedback']);
                }

                if (isset($responseArray['correctness'])) {
                    $subQuestionResponse->is_correct = (int)$responseArray['correctness'];
                }

                if (isset($responseArray['score'])) {
                    $subQuestionResponse->score = (int)$responseArray['score'];
                }

                $subQuestionResponse->save();
            }
        }
        //end save attributes

        //save sub-questions
        foreach ($existingSubQuestions as $subQuestion_id => $subQuestion_value) {
            DB_ORM::update('Map_Question')
                ->where('id', '=', $subQuestion_id)
                ->set('stem', $subQuestion_value)
                ->set('order', (int)$existingSubQuestionsOrder[$subQuestion_id])
                ->execute();
        }

        $delete_query = DB_ORM::delete('Map_Question')
            ->where('parent_id', '=', $questionId);
        $subQuestionsIds = array_keys($existingSubQuestions);
        if (!empty($subQuestionsIds)) {
            $delete_query->where('id', 'NOT IN', $subQuestionsIds);
        }
        $delete_query->execute();

        foreach ($subQuestions as $key => $subQuestion_value) {
            DB_ORM::insert('Map_Question')
                ->column('stem', $subQuestion_value)
                ->column('parent_id', $questionId)
                ->column('order', $subQuestionsOrder[$key])
                ->execute();
        }
        //end save sub-questions

        //save responses
        foreach ($existingResponses as $response_id => $response_value) {
            DB_ORM::update('Map_Question_Response')
                ->where('id', '=', $response_id)
                ->set('response', $response_value)
                ->set('order', (int)$existingResponsesOrder[$response_id])
                ->execute();
        }

        $delete_query = DB_ORM::delete('Map_Question_Response')
            ->where('question_id', '=', $questionId);
        $response_ids = array_keys($existingResponses);
        if (!empty($response_ids)) {
            $delete_query->where('id', 'NOT IN', $response_ids);
        }
        $delete_query->execute();

        foreach ($responses as $key => $response) {
            DB_ORM::insert('Map_Question_Response')
                ->column('response', $response)
                ->column('question_id', $questionId)
                ->column('order', $responsesOrder[$key])
                ->execute();
        }
        //end save responses

        $redirectUrl = URL::base() . 'questionManager/question/' . $mapId . '/' . $typeId . '/' . $questionId;
        if ($goToAttributes) {
            $redirectUrl .= '#goToAttributes';
        }
        Controller::redirect($redirectUrl);
    }

    public function action_deleteQuestion()
    {
        $mapId = $this->request->param('id', null);
        $questionId = $this->request->param('id2', null);

        if ($mapId != null and $questionId != null) {
            DB_ORM::model('User')->can('edit', array('mapId' => $mapId));

            $references = DB_ORM::model('map_node_reference')->getByElementType($questionId, 'QU');
            if ($references != null) {
                $ses = Session::instance();
                $ses->set('listOfUsedReferences', CrossReferences::getListReferenceForView($references));
                $ses->set('warningMessage',
                    'The question wasn\'t deleted. The selected question is used in the following labyrinths:');
            } else {
                DB_ORM::model('map_question', array((int)$questionId))->delete();
                DB_ORM::model('map_question_response')->deleteByQuestion($questionId);
            }
            Controller::redirect(URL::base() . 'questionManager/index/' . $mapId);
        } else {
            Controller::redirect(URL::base());
        }
    }

    public function action_duplicateQuestion()
    {
        $mapId = $this->request->param('id', null);
        $questionId = $this->request->param('id2', null);

        if ($mapId AND $questionId) {
            DB_ORM::model('map_question')->duplicateQuestion($questionId);
            Controller::redirect(URL::base() . 'questionManager/index/' . $mapId);
        } else {
            Controller::redirect(URL::base());
        }
    }

    public function action_copyQuestion()
    {
        $mapId = $this->request->param('id', null);

        if ($mapId != null && $_POST != null) {
            DB_ORM::model('map_question')->copyQuestion($mapId, $_POST);
            Controller::redirect(URL::base() . 'questionManager/index/' . $mapId);
        } else {
            Controller::redirect(URL::base());
        }
    }

    public function action_deleteResponseSCT()
    {
        $idResponse = $this->request->param('id');
        DB_ORM::delete('Map_Question_Response')->where('id', '=', $idResponse)->execute();
        Controller::redirect($this->request->referrer());
    }

    public function action_globalQuestions()
    {
        $mapId = $this->request->param('id', null);

        DB_ORM::model('User')->can('edit', array('mapId' => $mapId));

        if ($mapId) {
            $this->templateData['map'] = DB_ORM::model('map', array((int)$mapId));

            $allTypes = DB_ORM::model('map_question_type')->getAllTypes();
            $array = array();
            foreach ($allTypes as $key => $type) {
                $array[$key]['id'] = $type->id;
                $array[$key]['title'] = $type->title;
            }

            $this->templateData['question_types'] = $array;
            $this->templateData['questions'] = $this->getFieldsArray('global/questions/', 'question');

            if (Auth::instance()->get_user()->type->name == 'superuser') {
                $this->templateData['isSuperuser'] = true;
            }

            $this->templateData['center'] = View::factory('labyrinth/question/global')->set('templateData',
                $this->templateData);
            $this->templateData['left'] = View::factory('labyrinth/labyrinthEditorMenu')->set('templateData',
                $this->templateData);
            $this->template->set('templateData', $this->templateData);

            Breadcrumbs::add(Breadcrumb::factory()->set_title($this->templateData['map']->name)->set_url(URL::base() . 'labyrinthManager/global/' . $mapId));
            Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Global questions'))->set_url(URL::base() . 'questionManager/index/' . $mapId));
        } else {
            Controller::redirect(URL::base());
        }
    }

    public function action_exportQuestion()
    {
        $mapId = $this->request->param('id', null);
        $questionId = $this->request->param('id2', null);
        if ($mapId AND $questionId) {
            $rand = uniqid();
            $tmpFolder = 'tmp/' . $rand . '/';
            if (mkdir($tmpFolder)) {
                $questionName = 'question_' . $rand . '.xml';
                $responseName = 'response_' . $rand . '.xml';
                $question = DB_ORM::model('map_question')->getQuestionById($questionId);
                $question[0]['name_file'] = $questionName;
                $this->createXMLFile($tmpFolder, $questionName, $question);
                $elementsArray = $this->mergeArraysFromDB($question, 'map_question_response');
                $this->createXMLFile($tmpFolder, $responseName, $elementsArray);
                $this->createZipArchive($tmpFolder, $rand);
                $this->removeDirectory($tmpFolder);

                $zipFile = 'tmp/' . $rand . '.zip';
                header("Cache-Control: public");
                header("Content-Description: File Transfer");
                header("Content-Disposition: attachment; filename=" . $question[0]['stem'] . '.zip');
                header("Content-Type: application/zip");
                header("Content-Transfer-Encoding: binary");
                readfile('tmp/' . $rand . '.zip');
                unlink($zipFile);
            } else {
                Controller::redirect(URL::base() . 'questionManager/index/' . $mapId);
            }
        } else {
            Controller::redirect(URL::base());
        }
    }

    private function createZipArchive($folderPath, $name)
    {
        $dest = 'tmp/' . $name . '.zip';
        $zip = new ZipArchive();

        if ($h = opendir($folderPath)) {
            if ($zip->open($dest, ZIPARCHIVE::CREATE)) {
                while (false !== ($f = readdir($h))) {
                    if (strstr($f, '.') AND file_exists($folderPath . '/' . $f) AND strcmp($f, '.') != 0 AND strcmp($f,
                            '..') != 0
                    ) {
                        $zip->addFile($folderPath . '/' . $f, $f);
                    }
                }
            }
            closedir($h);
        }
        $zip->close();

        return true;
    }

    private function removeDirectory($dir)
    {
        if ($objs = glob($dir . "/*")) {
            foreach ($objs as $obj) {
                is_dir($obj) ? removeDirectory($obj) : unlink($obj);
            }
        }
        rmdir($dir);
    }

    private function createXMLFile($path, $name, $array)
    {
        if (count($array)) {
            $xml = new SimpleXMLElement('<xml />');
            $arrayXml = $xml->addChild($name);
            $this->createXMLTree($arrayXml, $name, $array);

            $filePath = $path . $name;
            $f = fopen($filePath, 'w');
            if (function_exists('dom_import_simplexml')) {
                $dom = dom_import_simplexml($xml)->ownerDocument;
                $dom->formatOutput = true;
                $xmlObject = $dom;
            } else {
                $xmlObject = $xml;
            }
            $outputXML = str_replace('<?xml version="1.0"?>', '<?xml version="1.0" encoding="UTF-8"?>',
                $xmlObject->saveXML());
            fwrite($f, $outputXML);
            fclose($f);
        }
    }

    private function createXMLTree($xml, $name, $array)
    {
        if (count($array)) {
            foreach ($array as $key => $value) {
                if (is_array($value)) {
                    $this->createXMLTree($xml->addChild($name . '_' . $key), $name . '_' . $key, $value);
                } else {
                    if (!is_numeric($value)) {
                        $value = base64_encode($value);
                    }
                    $xml->addChild($key, $value);
                }
            }
        }
    }

    private function mergeArraysFromDB($rootElements, $model, $key = 'id')
    {
        $elementsArray = array();
        if (count($rootElements) > 0) {
            foreach ($rootElements as $element) {
                $array = DB_ORM::model($model)->exportMVP($element[$key]);
                if (count($array) > 0) {
                    $elementsArray = array_merge($elementsArray, $array);
                }
            }
        }

        return $elementsArray;
    }

    public function action_importQuestion()
    {
        $mapId = $this->request->param('id', null);
        $questionFile = base64_decode($this->request->param('id2', null));
        if ($mapId) {
            if (file_exists('global/questions/' . $questionFile)) {
                $response = array();
                $questions = $this->getFieldsArray('global/questions/', 'question');
                $xmlResponse = explode('_', $questionFile);
                $xmlResponse = explode('.', $xmlResponse[1]);
                $responseFile = 'response_' . $xmlResponse[0] . '.xml';

                if (is_file('global/questions/' . $responseFile)) {
                    $xmlFile = file_get_contents('global/questions/' . $responseFile);
                    $responses = simplexml_load_string($xmlFile);
                    $json = json_encode($responses);
                    $array = json_decode($json, true);
                    foreach (Arr::get($array, $responseFile, array()) as $value) {
                        $response[] = $value;
                    }
                }

                foreach ($questions as $question) {
                    if (base64_decode($question->name_file) == $questionFile) {
                        DB_ORM::model('map_question')->importQuestion($question, $response);
                    }
                }
            }
            Controller::redirect(URL::base() . 'questionManager/index/' . $mapId);
        } else {
            Controller::redirect(URL::base());
        }
    }

    private function getFieldsArray($dir, $name)
    {
        $dataArray = array();
        if (is_dir($dir)) {
            $listOfFile = scandir($dir);
            $listFileQuestion = array();

            foreach ($listOfFile as $file) {
                $part = explode('_', $file);
                if ($part[0] == $name) {
                    $listFileQuestion[] = $file;
                }
            }

            $data = array();
            foreach ($listFileQuestion as $file) {
                $xmlFile = file_get_contents($dir . $file);
                $ob = simplexml_load_string($xmlFile);
                foreach ($ob as $files) {
                    $data[] = $files;
                }
            }

            foreach ($data as $key => $tags) {
                foreach ($tags as $tag) {
                    $dataArray[$key] = $tag;
                }
            }
        }

        return $dataArray;
    }

}
