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

        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('My Labyrinths'))->set_url(URL::base().'authoredLabyrinth'));
    }

    public function action_index() {
        $mapId = $this->request->param('id', NULL);

        if ( ! $mapId ) Request::initial()->redirect(URL::base());

        DB_ORM::model('User')->can('edit', array('mapId' => $mapId));

        $this->templateData['map']              = DB_ORM::model('map', array((int) $mapId));
        $this->templateData['counters']         = DB_ORM::model('map_counter')->getCountersByMap($mapId);
        $this->templateData['questions']        = DB_ORM::model('map_question')->getQuestionsByMap((int) $mapId);
        $this->templateData['question_types']   = DB_ORM::model('map_question_type')->getAllTypes();

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

        $this->templateData['center'] = View::factory('labyrinth/question/view')->set('templateData', $this->templateData);
        $this->templateData['left'] = View::factory('labyrinth/labyrinthEditorMenu')->set('templateData', $this->templateData);
        $this->template->set('templateData', $this->templateData);

        Breadcrumbs::add(Breadcrumb::factory()->set_title($this->templateData['map']->name)->set_url(URL::base() . 'labyrinthManager/global/' . $mapId));
        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Questions'))->set_url(URL::base() . 'questionManager/index/' . $mapId));
    }
    
    public function action_question()
    {
        $mapId      = $this->request->param('id', 0);
        $typeId     = $this->request->param('id2', 0);
        $questionId = $this->request->param('id3', 0);
        $map        = DB_ORM::model('map', array((int)$mapId));
        $type       = DB_ORM::model('map_question_type', array((int) $typeId));

        if ( ! ($map AND $type)) {
            Request::initial()->redirect(URL::base());
        }

        DB_ORM::model('User')->can('edit', array('mapId' => $mapId));

        $this->templateData['map']          = DB_ORM::model('map', array((int)$mapId));
        $this->templateData['type']         = DB_ORM::model('map_question_type', array((int) $typeId));
        $this->templateData['counters']     = DB_ORM::model('map_counter')->getCountersByMap((int) $mapId);
        $this->templateData['nodes']        = DB_ORM::model('map_node')->getNodesByMap((int) $mapId);
        $this->templateData['validation']   = DB_ORM::model('Map_Question_Validation')->getRecord($questionId);

        Breadcrumbs::add(Breadcrumb::factory()->set_title($this->templateData['map']->name)->set_url(URL::base().'labyrinthManager/global/'.$mapId));
        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Questions'))->set_url(URL::base().'questionManager/index/'.$mapId));

        if ($questionId) {
            $questionObj = DB_ORM::model('map_question', array((int)$questionId));

            $this->templateData['question'] = $questionObj;
            $this->templateData['used']     = count(DB_ORM::model('map_node_reference')->getByElementType($questionId, 'QU'));

            if ($questionObj->settings) {
                $jsonSettings = json_decode($questionObj->settings);
                $this->templateData['questionSettings'] = $jsonSettings;

                if ($questionObj->type->value == 'area' OR $questionObj->type->value == 'text') {
                    $this->templateData['isCorrect'] = Arr::get($jsonSettings, 1, 0);
                    $this->templateData['question']->settings = Arr::get($jsonSettings, 0, '');
                }
            }
            Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Edit'))->set_url(URL::base().'questionManager/question/'.$mapId.'/'.$typeId.'/'.$questionId));
        } else {
            Breadcrumbs::add(Breadcrumb::factory()->set_title(__('New'))->set_url(URL::base().'questionManager/question/'.$mapId.'/'.$typeId));
        }

        if ($type->value == 'area' OR $type->value == 'text' OR $type->value == 'cml') {
            $validation = DB_ORM::model('Map_Question_Validation');
            $validators = array_merge($validation->one_parameter, $validation->two_parameter, $validation->three_parameter);
            ksort($validators);
            $this->templateData['validators'] = $validators;
        }

        $this->templateData['center']   = View::factory('labyrinth/question/'.$type->template_name)->set('templateData', $this->templateData);
        $this->templateData['left']     = View::factory('labyrinth/labyrinthEditorMenu')->set('templateData', $this->templateData);
        $this->template->set('templateData', $this->templateData);
    }

    public function action_questionSJT()
    {
        $post       = $this->request->post();
        $stem       = Arr::get($post, 'stem', '');
        $feedback   = Arr::get($post, 'feedback', '');
        $score      = Arr::get($post, 'score', '');
        $responses  = Arr::get($post, 'responses', '');
        $mapId      = $this->request->param('id', 0);
        $questionId = $this->request->param('id2', 0);

        if ($questionId) {
            DB_ORM::update('Map_Question')->set('stem', $stem)->set('feedback', $feedback)->where('id', '=', $questionId)->execute();
            foreach ($responses as $index => $response) {
                $isIndex = strpos($index, 'i');
                if ($isIndex !== false) {
                    $responseId = str_replace('i', '', $index);
                    DB_ORM::update('Map_Question_Response')->set('response', $response)->where('id', '=', $responseId)->execute();
                    foreach (Arr::get($score, $index, array()) as $position => $points) {
                        DB_ORM::update('SJTResponse')->set('points', $points)->where('position', '=', $position)->where('response_id', '=', $responseId)->execute();
                    }
                }
            }
        } else {
            $questionId = DB_ORM::insert('Map_Question')->column('map_id', $mapId)->column('stem', $stem)->column('entry_type_id', 8)->column('feedback', $feedback)->execute();
            foreach ($responses as $index => $response) {
                $responseId = DB_ORM::insert('Map_Question_Response')->column('question_id', $questionId)->column('response', $response)->execute();
                foreach (Arr::get($score, $index, array()) as $position => $points) {
                    DB_ORM::insert('SJTResponse')->column('response_id', $responseId)->column('position', $position)->column('points', $points)->execute();
                }
            }
        }
        Request::initial()->redirect(URL::base().'questionManager/index/'.$mapId);
    }

    public function action_questionPOST()
    {
        $post             = $this->request->post();
        $mapId            = $this->request->param('id', 0);
        $postType         = Arr::get($post, 'question_type', null);
        $validator        = Arr::get($post, 'validator', 'no validator');
        $secondParameter  = Arr::get($post, 'second_parameter');
        $errorMessage     = Arr::get($post, 'error_message');
        $typeId           = ($postType != null) ? $postType : $this->request->param('id2', 0);
        $questionId       = $this->request->param('id3', 0);
        $map              = DB_ORM::model('map', array((int)$mapId));
        $type             = DB_ORM::model('map_question_type', array((int)$typeId));

        if ( ! ($post AND $map AND $type)) Request::initial()->redirect(URL::base());

        $post['settings'] = json_encode(array(Arr::get($post, 'settings', ''), Arr::get($post, 'isCorrect', 0)));

        if ($questionId) {
            $references = DB_ORM::model('map_node_reference')->getNotParent($mapId, $questionId, 'QU');
            $private    = Arr::get($post, 'is_private');

            if ($references AND $private) {
                $ses = Session::instance();
                $ses->set('listOfUsedReferences', CrossReferences::getListReferenceForView($references));
                $ses->set('warningMessage', 'The question wasn\'t set to private. The selected question is used in the following labyrinths:');
                $post['is_private'] = FALSE;
            }

            DB_ORM::model('map_question')->updateQuestion($questionId, $type, $post);
        } else {
            $questionId = DB_ORM::model('map_question')->addQuestion($mapId, $type, $post);
        }

        $validator == 'no validator'
            ? DB_ORM::model('Map_Question_Validation')->deleteByQuestionId($questionId)
            : DB_ORM::model('Map_Question_Validation')->update($questionId, $validator, $secondParameter, $errorMessage);

        Request::initial()->redirect(URL::base().'questionManager/index/'.$mapId);
    }

    public function action_deleteQuestion()
    {
        $mapId      = $this->request->param('id', NULL);
        $questionId = $this->request->param('id2', NULL);

        if ($mapId != NULL and $questionId != NULL)
        {
            DB_ORM::model('User')->can('edit', array('mapId' => $mapId));

            $references = DB_ORM::model('map_node_reference')->getByElementType($questionId, 'QU');
            if($references != NULL){
                $ses = Session::instance();
                $ses->set('listOfUsedReferences', CrossReferences::getListReferenceForView($references));
                $ses->set('warningMessage', 'The question wasn\'t deleted. The selected question is used in the following labyrinths:');
            } else {
                DB_ORM::model('map_question', array((int) $questionId))->delete();
                DB_ORM::model('map_question_response')->deleteByQuestion($questionId);
            }
            Request::initial()->redirect(URL::base() . 'questionManager/index/' . $mapId);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }
    
    public function action_duplicateQuestion()
    {
        $mapId      = $this->request->param('id', null);
        $questionId = $this->request->param('id2', null);
        
        if ($mapId AND $questionId)
        {
            DB_ORM::model('map_question')->duplicateQuestion($questionId);
            Request::initial()->redirect(URL::base().'questionManager/index/'.$mapId);
        }
        else Request::initial()->redirect(URL::base());
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

    public function action_deleteResponseSCT ()
    {
        $idResponse = $this->request->param('id');
        DB_ORM::delete('Map_Question_Response')->where('id', '=', $idResponse)->execute();
        Request::initial()->redirect($this->request->referrer());
    }

    public function action_globalQuestions(){
        $mapId = $this->request->param('id', NULL);
        if ($mapId != NULL)
        {
            DB_ORM::model('User')->can('edit', array('mapId' => $mapId));

            $this->templateData['map'] = DB_ORM::model('map', array((int) $mapId));
            $allTypes = DB_ORM::model('map_question_type')->getAllTypes();
            $array = array();
            foreach($allTypes as $key=>$type){
                $array[$key]['id'] = $type->id;
                $array[$key]['title'] = $type->title;
            }
            $this->templateData['question_types'] = $array;
            $this->templateData['questions'] = $this->getFiledsArray('global/questions/', 'question');

            if (Auth::instance()->get_user()->type->name == 'superuser') {
                $this->templateData['isSuperuser'] = true;
            }

            Breadcrumbs::add(Breadcrumb::factory()->set_title($this->templateData['map']->name)->set_url(URL::base() . 'labyrinthManager/global/' . $mapId));
            Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Global questions'))->set_url(URL::base() . 'questionManager/index/' . $mapId));

            $questionView = View::factory('labyrinth/question/global');
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

    public function action_exportQuestion()
    {
        $mapId      = $this->request->param('id', NULL);
        $questionId = $this->request->param('id2', NULL);
        if ($mapId AND $questionId) {
            $rand       = uniqid();
            $tmpFolder  = 'tmp/'.$rand.'/';
            if (mkdir($tmpFolder)) {
                $questionName = 'question_'.$rand.'.xml';
                $responseName = 'response_'.$rand.'.xml';
                $question = DB_ORM::model('map_question')->getQuestionById($questionId);
                $question[0]['name_file'] = $questionName;
                $this->createXMLFile($tmpFolder, $questionName, $question);
                $elementsArray = $this->mergeArraysFromDB($question, 'map_question_response');
                $this->createXMLFile($tmpFolder, $responseName, $elementsArray);
                $this->createZipArchive($tmpFolder, $rand);
                $this->removeDirectory($tmpFolder);

                $zipFile = 'tmp/'.$rand.'.zip';
                $pathInfo = pathinfo($zipFile);
                header("Cache-Control: public");
                header("Content-Description: File Transfer");
                header("Content-Disposition: attachment; filename=".$question[0]['stem'].'.zip');
                header("Content-Type: application/zip");
                header("Content-Transfer-Encoding: binary");
                readfile('tmp/'.$rand.'.zip');
                unlink($zipFile);
            } else {
                Request::initial()->redirect(URL::base().'questionManager/index/'.$mapId);
            }
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

    private function createZipArchive($folderPath, $name)
    {
        $dest = 'tmp/'.$name.'.zip';
        $zip = new ZipArchive();

        if ($h = opendir($folderPath)) {
            if ($zip->open($dest, ZIPARCHIVE::CREATE)) {
                while (false !== ($f = readdir($h))) {
                    if (strstr($f, '.') AND file_exists($folderPath . '/' . $f) AND strcmp($f, '.') != 0 AND strcmp($f, '..') != 0) {
                        $zip->addFile($folderPath.'/'.$f, $f);
                    }
                }
            }
            closedir($h);
        }
        $zip->close();
        return true;
    }

    private function removeDirectory($dir) {
        if ($objs = glob($dir."/*")) {
            foreach($objs as $obj) {
                is_dir($obj) ? removeDirectory($obj) : unlink($obj);
            }
        }
        rmdir($dir);
    }

    private function createXMLFile($path, $name, $array)
    {
        if (count($array)){
            $xml = new SimpleXMLElement('<xml />');
            $arrayXml = $xml->addChild($name);
            $this->createXMLTree($arrayXml, $name, $array);

            $filePath =  $path . $name;
            $f = fopen($filePath, 'w');
            $dom = dom_import_simplexml($xml)->ownerDocument;
            $dom->formatOutput = true;
            $outputXML = str_replace('<?xml version="1.0"?>', '<?xml version="1.0" encoding="UTF-8"?>', $dom->saveXML());
            fwrite($f, $outputXML);
            fclose($f);
        }
    }

    private function createXMLTree($xml, $name, $array)
    {
        if (count($array)){
            foreach($array as $key => $value){
                if(is_array($value)){
                    $this->createXMLTree($xml->addChild($name.'_'.$key), $name.'_'.$key, $value);
                }else {
                    if ( ! is_numeric($value)) {
                        $value = base64_encode($value);
                    }
                    $xml->addChild($key, $value);
                }
            }
        }
    }

    private function mergeArraysFromDB($rootElements, $model, $key = 'id'){
        $elementsArray = array();
        if (count($rootElements) > 0){
            foreach($rootElements as $element){
                $array = DB_ORM::model($model)->exportMVP($element[$key]);
                if (count($array) > 0){
                    $elementsArray = array_merge($elementsArray, $array);
                }
            }
        }
        return $elementsArray;
    }

    public function action_importQuestion(){
        $mapId = $this->request->param('id', NULL);
        $questionFile = base64_decode($this->request->param('id2', NULL));
        if($mapId != NULL){
            if(file_exists('global/questions/' . $questionFile)){
                $response = NULL;
                $questions = $this->getFiledsArray('global/questions/', 'question');
                $xmlResponse = explode('_',$questionFile);
                $xmlResponse = explode('.',$xmlResponse[1]);
                if(is_file('global/questions/' . 'response_' . $xmlResponse[0].'.xml')){
                    $xmlfile = file_get_contents('global/questions/response_' . $xmlResponse[0].'.xml');
                    $responses = simplexml_load_string($xmlfile);
                    $json    = json_encode($responses);
                    $array   = json_decode($json, true);
                    $array   = DB_ORM::model('MVPFormatSystem')->convertValuesInArray($array[$questionFile]);
                    foreach($array as $key=>$item){
                        if($key == 'response_' . $xmlResponse[0] . '.xml'){
                            $response =  $item;
                        }
                    }
                }
                foreach($questions as $question){
                    if($question->name_file == $questionFile){
                        DB_ORM::model('map_question')->importQuestion($question, $response);
                    }
                }
            }
            Request::initial()->redirect(URL::base() . 'questionManager/index/' . $mapId);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

    private function getFiledsArray($dir, $name){
        if(is_dir($dir)) {
            $listOfFile = scandir($dir);
            $listFileQuestion = array();
            foreach($listOfFile as $file){
                $part  = explode('_', $file);
                if($part[0] == $name){
                    $listFileQuestion[] = $file;
                }
            }
            $data = array();
            foreach($listFileQuestion as $file){
                $xmlfile = file_get_contents($dir . $file);
                $ob = simplexml_load_string($xmlfile);
                foreach($ob as $files){
                    $data[] = $files;
                }
            }
            $dataarray = array();
            foreach($data as $key=>$tags){
                foreach($tags as $tag){
                    $dataarray[$key] = $tag;
                }
            }
        }
        return $dataarray;
    }

}
