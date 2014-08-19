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

class Controller_RenderLabyrinth extends Controller_Template {

    public $template = 'home'; // Default
    public $mapId = 0;
    public $questionsId = array();
    public static $isCumulative = false;
    public static $nodeId = 0;

    private function renderLabyrinth ($action)
    {
        $mapId       = $this->request->param('id', null);
        $this->mapId = $mapId;
        $node        = null;
        $bookmark    = false;
        $continue    = true;

        if ( ! ($mapId AND $this->checkTypeCompatibility($mapId))) {
            Session::instance()->set('redirectURL', $this->request->uri());
            Request::initial()->redirect(URL::base());
        }

        if ($action == 'index') {
            $mapDB = DB_ORM::model('map', array($mapId));
            if ($mapDB->security_id == 4) {
                $sessionId      = Session::instance()->id();
                $checkValue     = Auth::instance()->hash('checkvalue'.$mapId.$sessionId);
                $checkSession   = Session::instance()->get($checkValue);

                if ($checkSession != '1') {
                    $templateData['mapDB']      = $mapDB;
                    $templateData['title']      = 'OpenLabyrinth';
                    $templateData['keyError']   = Session::instance()->get('keyError');

                    Session::instance()->delete('keyError');

                    $this->template = View::factory('labyrinth/security')->set('templateData', $templateData);
                    $continue = false;
                }
            }

            if ($continue) {
                Session::instance()->delete('questionChoices');
                Session::instance()->delete('dragQuestionResponses');
                Session::instance()->delete('counterFunc');
                Session::instance()->delete('stopCommonRules');
                Session::instance()->delete('arrayAddedQuestions');

                $node = DB_ORM::model('map_node')->getRootNodeByMap((int) $mapId);
            }

            $cumulative = DB_ORM::select('Webinar_Map')
                ->where('webinar_id', '=', Session::instance()->get('idScenario'))
                ->where('reference_id', '=', $mapId)
                ->query()
                ->fetch(0);
            if ($cumulative AND $cumulative->cumulative){
                Controller_RenderLabyrinth::$isCumulative = true;
            }
        } elseif ($action == 'resume') {
            $result     = DB_ORM::model('User_Bookmark')->getBookmarkByMapAndUser($mapId, Auth::instance()->get_user()->id);
            $node       = DB_ORM::model('map_node')->getNodeById(Arr::get($result, 'node_id', 0));
            $bookmark   = Arr::get($result, 'id', 0);
        } elseif ($action == 'go') {
            $nodeId   = $this->request->param('id2', null);

            // deprecated if statements 4.08.2014
            if ($nodeId == null) {
                $nodeId = Arr::get($_GET, 'id', null);
                if ($nodeId == null AND $_POST) {
                    $nodeId = Arr::get($_POST, 'id', null);
                    if ($nodeId == null) {
                        Request::initial()->redirect(URL::base());
                        return;
                    }
                }
            }

            $node = DB_ORM::model('map_node')->getNodeById((int) $nodeId);
        }

        if ($continue) {
            $this->renderNode($node, $action, $bookmark, Controller_RenderLabyrinth::$isCumulative);
        }
    }

    private function renderNode ($nodeObj, $action, $bookmark, $cumulative)
    {
        if ($nodeObj == NULL) {
            Request::initial()->redirect(URL::base());
        }

        Controller_RenderLabyrinth::$nodeId = $nodeObj->id;

        $editOnId   = ($action == 'go') ? 'id3' : 'id2';
        $editOn     = $this->request->param($editOnId, null);
        $nodeId     = $nodeObj->id;
        $mapId      = $nodeObj->map_id;
        $isRoot     = ($nodeObj->type_id == 1) ? true : false;
        $scenarioId = DB_ORM::model('User_Session', array(Session::instance()->get('session_id')))->webinar_id;
        $data       = ($action == 'resume')
            ? Model::factory('labyrinth')->execute($nodeId, $bookmark)
            : Model::factory('labyrinth')->execute($nodeId, null, $isRoot, $cumulative);

        // delete $bookmark after use it
        DB_ORM::delete('User_Bookmark')->where('id', '=', $bookmark)->execute();

        if ( ! $data) Request::initial()->redirect(URL::base());

        /* if exist poll node save its time */
        $data['time'] = DB_ORM::model('Webinar_PollNode')->getTime($nodeId, $scenarioId);

        /* ----- patient ----- */
        $data['patients'] = $this->checkPatient($nodeId, $scenarioId, 'go');
        foreach ($data['patients'] as $patient)
        {
            $data['counters'] .= '<ul class="navigation patient-js">'.$patient.'</ul>';
        }
        /* ----- end patient ----- */

        $gotoNode = Session::instance()->get('goto', NULL);
        if ($gotoNode != NULL) {
            Session::instance()->set('goto', NULL);
            Request::initial()->redirect(URL::base().'renderLabyrinth/go/'.$mapId.'/'.$gotoNode);
        }

        $undoNodes = array();
        if (isset($data['traces'][0]) AND $data['traces'][0]->session_id != null) {
            $sessionId              = (int)$data['traces'][0]->session_id;
            $lastNode               = DB_ORM::model('user_sessiontrace')->getLastTraceBySessionId($sessionId);
            $startSession           = DB_ORM::model('user_session')->getStartTimeSessionById($sessionId);
            $timeForNode            = $lastNode[0]['date_stamp'] - $startSession;
            $data['timeForNode']    = $timeForNode;
            $data['session']        = $sessionId;

            if ($data['node']->undo) {
                list ($undoLinks, $undoNodes) = $this->prepareUndoLinks($sessionId, $mapId, $nodeId);
                $data['undoLinks'] = $undoLinks;
            }
            else {
                $undoNodes = Arr::get($this->prepareUndoLinks($sessionId, $mapId, $nodeId), 1 ,null);
            }
        }

        $data['navigation'] = $this->generateNavigation($data['sections']);

        if ( ! isset($data['node_links']['linker'])) {
            $result = $this->generateLinks($data['node'], $data['node_links'], $undoNodes);
            if ($data['node']->link_style->name == 'type in text') {
                $data['links'] = $result['links']['display'];
                if(isset($data['alinkfil']) AND isset($data['alinknod'])) {
                    $data['alinkfil'] = substr($result['links']['alinkfil'], 0, strlen($result['links']['alinkfil']) - 2);
                    $data['alinknod'] = substr($result['links']['alinknod'], 0, strlen($result['links']['alinknod']) - 2);
                }
            } else {
                $data['links'] = $result['links'];
            }
        } else {
            $data['links'] = $data['node_links']['linker'];
        }

        if ($editOn != null AND $editOn == 1) {
            $data['node_edit'] = TRUE;
        } else {
            if (($data['node']->info != '') AND (strpos($data['node_text'],'[[INFO:') === false) AND $data['node']->show_info) {
                $data['node_text'].= '[[INFO:'.$data['node']->id.']]';
            }
            if (($data['node']->info == '') AND (strpos($data['node_text'],'[[INFO:'))) {
                $search = '[[INFO:'.$data['node']->id.']]';
                $data['node_text'] = str_replace($search, '',$data['node_text']);
            }
            $data['node_text'] = $this->parseText($data['node_text'], $mapId);
        }

        $data['trace_links'] = $this->generateReviewLinks($data['traces']);
        $data['skin_path']   = $data['map']->skin->path;
        $data['timer_start'] = 1;
        $data['popup_start'] = 1;

        // Parse text key for each nodes
        foreach (DB_ORM::model('map_popup')->getEnabledMapPopups($mapId) as $popup) {
            $popup->text = $this->parseText($popup->text, $mapId, 'popup');
            $data['map_popups'][] = $popup;
        }

        $skin = 'labyrinth/skin/basic/basic';
        if ($data['map']->skin->enabled AND file_exists($_SERVER['DOCUMENT_ROOT'].'/application/views/labyrinth/skin/'.$data['map']->skin->id.'/skin.php')) {
            $skin = 'labyrinth/skin/basic/basic_template';

            $data['skin'] = View::factory('labyrinth/skin/'.$data['map']->skin->id.'/skin')->set('templateData', $data);
            $skinData = json_decode($data['map']->skin->data, true);
            if ($skinData != null AND isset($skinData['body'])) {
                $data['bodyStyle'] = base64_decode($skinData['body']);
            }
        }

        if ($action = 'index') {
            $data['session'] = (int) $data['traces'][0]->session_id;
        }

        $this->template = View::factory($skin)->set('templateData', $data);
    }

    public function action_resume()
    {
        $this->renderLabyrinth('resume');
    }

    public function action_index()
    {
        $this->renderLabyrinth('index');
//        $mapId       = $this->request->param('id', null);
//        $editOn      = $this->request->param('id2', null);
//        $scenarioId  = Session::instance()->get('webinarId');
//        $this->mapId = $mapId;
//
//        if ( ! ($mapId AND $this->checkTypeCompatibility($mapId))) Request::initial()->redirect(URL::base());
//
//        $mapDB = DB_ORM::model('map', array($mapId));
//        if ($mapDB->security_id == 4)
//        {
//            $sessionId      = Session::instance()->id();
//            $checkValue     = Auth::instance()->hash('checkvalue'.$mapId.$sessionId);
//            $checkSession   = Session::instance()->get($checkValue);
//            if ($checkSession != '1')
//            {
//                $this->template             = View::factory('labyrinth/security');
//                $templateData['mapDB']      = $mapDB;
//                $templateData['title']      = 'OpenLabyrinth';
//                $templateData['keyError']   = Session::instance()->get('keyError');
//
//                Session::instance()->delete('keyError');
//
//                $this->template->set('templateData', $templateData);
//            }
//        }
//        else
//        {
//            Session::instance()->delete('questionChoices');
//            Session::instance()->delete('dragQuestionResponses');
//            Session::instance()->delete('counterFunc');
//            Session::instance()->delete('stopCommonRules');
//            Session::instance()->delete('arrayAddedQuestions');
//
//            $rootNode = DB_ORM::model('map_node')->getRootNodeByMap((int) $mapId);
//
//            if ($rootNode == NULL) Request::initial()->redirect(URL::base());
//
//            $idRootNode = $rootNode->id;
//            $data = Model::factory('labyrinth')->execute($idRootNode, NULL, true);
//
//            if ( ! $data) Request::initial()->redirect(URL::base());
//
//            /* if exist poll node save its time */
//            $data['time'] = DB_ORM::model('Webinar_PollNode')->getTime($idRootNode, $scenarioId);
//
//            /* ----- patient ----- */
//            $data['patients'] = $this->checkPatient($idRootNode, $scenarioId, 'index');
//            foreach ($data['patients'] as $patient)
//            {
//                $data['counters'] .= '<ul class="navigation patient-js">'.$patient.'</ul>';
//            }
//            /* ----- end patient ----- */
//
//            $data['navigation'] = $this->generateNavigation($data['sections']);
//
//            if ( ! isset($data['node_links']['linker']))
//            {
//                if ($data['node']->link_style->name == 'type in text')
//                {
//                    $result = $this->generateLinks($data['node'], $data['node_links']);
//                    $data['links'] = $result['links']['display'];
//                    if(isset($data['alinkfil']) && isset($data['alinknod'])) {
//                         $data['alinkfil'] = substr($result['links']['alinkfil'], 0, strlen($result['links']['alinkfil']) - 2);
//                         $data['alinknod'] = substr($result['links']['alinknod'], 0, strlen($result['links']['alinknod']) - 2);
//                    }
//                } else {
//                    $result = $this->generateLinks($data['node'], $data['node_links']);
//                    $data['links'] = $result['links'];
//                }
//            } else {
//                $data['links'] = $data['node_links']['linker'];
//            }
//
//            if ($editOn != null AND $editOn == 1) $data['node_edit'] = TRUE;
//            else
//            {
//                if (( $data['node']->info != '' ) AND (strpos($data['node_text'],'[[INFO:') === false) AND $data['node']->show_info)
//                {
//                    $data['node_text'] .= '[[INFO:' . $data['node']->id . ']]';
//                }
//                if (( $data['node']->info == '' ) && (strpos($data['node_text'],'[[INFO:')))
//                {
//                    $search = '[[INFO:' . $data['node']->id . ']]';
//                    $data['node_text'] = str_replace($search, '',$data['node_text']);
//                }
//                $data['node_text'] = $this->parseText($data['node_text'], $mapId);
//            }
//
//            $data['trace_links'] = $this->generateReviewLinks($data['traces']);
//            $skin = 'labyrinth/skin/basic/basic';
//            if ($data['map']->skin->enabled){
//                $data['skin_path'] = $data['map']->skin->path;
//                if(file_exists($_SERVER['DOCUMENT_ROOT'] . '/application/views/labyrinth/skin/' . $data['map']->skin->id . '/skin.php')) {
//                    $skin = 'labyrinth/skin/basic/basic_template';
//
//                    $skinContent = View::factory('labyrinth/skin/' . $data['map']->skin->id . '/skin');
//                    $skinContent->set('templateData', $data);
//
//                    $data['skin'] = $skinContent;
//                    $skinData = json_decode($data['map']->skin->data, true);
//                    if($skinData != null && isset($skinData['body'])) {
//                        $data['bodyStyle'] = base64_decode($skinData['body']);
//                    }
//                } else {
//                    $skin = 'labyrinth/skin/basic/basic';
//                }
//            }else{
//                $data['skin_path'] = NULL;
//            }
//
//            $data['session'] = (int)$data['traces'][0]->session_id;
//            foreach (DB_ORM::model('map_popup')->getEnabledMapPopups($mapId) as $popup)
//            {
//                $popup->text = $this->parseText($popup->text);
//                $data['map_popups'][] = $popup;
//            }
//
//            $this->template = View::factory($skin)->set('templateData', $data);
//        }
    }

    public function action_go()
    {
        $this->renderLabyrinth('go');
//        $mapId       = $this->request->param('id', NULL);
//        $nodeId      = $this->request->param('id2', NULL);
//        $editOn      = $this->request->param('id3', NULL);
//        $bookMark    = $this->request->param('id4', NULL);
//        $this->mapId = $mapId;
//
//        if ( ! ($mapId AND $this->checkTypeCompatibility($mapId))) Request::initial()->redirect(URL::base());
//
//        if ($nodeId == NULL) {
//            $nodeId = Arr::get($_GET, 'id', NULL);
//            if ($nodeId == NULL AND $_POST) {
//                $nodeId = Arr::get($_POST, 'id', NULL);
//                if ($nodeId == NULL) {
//                    Request::initial()->redirect(URL::base());
//                    return;
//                }
//            }
//        }
//
//        $node = DB_ORM::model('map_node')->getNodeById((int) $nodeId);
//        if ($node == NULL) Request::initial()->redirect(URL::base());
//
//        $data = ($bookMark != NULL)
//            ? Model::factory('labyrinth')->execute($node->id, (int) $bookMark)
//            : Model::factory('labyrinth')->execute($node->id);
//
//        /* if exist poll node save its time */
//        $scenarioId  = DB_ORM::model('User_Session', array(Session::instance()->get('session_id')))->webinar_id;
//        $data['time'] = DB_ORM::model('Webinar_PollNode')->getTime($nodeId, $scenarioId);
//
//        /* ----- patient ----- */
//        $data['patients'] = $this->checkPatient($nodeId, $scenarioId, 'go');
//        foreach ($data['patients'] as $patient)
//        {
//            $data['counters'] .= '<ul class="navigation patient-js">'.$patient.'</ul>';
//        }
//        /* ----- end patient ----- */
//
//        $gotoNode = Session::instance()->get('goto', NULL);
//        if ($gotoNode != NULL)
//        {
//            Session::instance()->set('goto', NULL);
//            Request::initial()->redirect(URL::base().'renderLabyrinth/go/'.$mapId.'/'.$gotoNode);
//        }
//
//        if ( ! $data) Request::initial()->redirect(URL::base());
//
//        $undoNodes = array();
//        if (isset($data['traces'][0]) AND $data['traces'][0]->session_id != null)
//        {
//            $sessionId              = (int)$data['traces'][0]->session_id;
//            $lastNode               = DB_ORM::model('user_sessiontrace')->getLastTraceBySessionId($sessionId);
//            $startSession           = DB_ORM::model('user_session')->getStartTimeSessionById($sessionId);
//            $timeForNode            = $lastNode[0]['date_stamp'] - $startSession;
//            $data['timeForNode']    = $timeForNode;
//            $data['session']        = $sessionId;
//
//            if ($data['node']->undo)
//            {
//                list ($undoLinks, $undoNodes) = $this->prepareUndoLinks($sessionId, $mapId, $nodeId);
//                $data['undoLinks'] = $undoLinks;
//            }
//            else $undoNodes = Arr::get($this->prepareUndoLinks($sessionId, $mapId, $nodeId), 1 ,null);
//        }
//
//        $data['navigation'] = $this->generateNavigation($data['sections']);
//        if ( ! isset($data['node_links']['linker']))
//        {
//            if ($data['node']->link_style->name == 'type in text')
//            {
//                $result = $this->generateLinks($data['node'], $data['node_links'], $undoNodes);
//                $data['links'] = $result['links']['display'];
//                if(isset($data['alinkfil']) && isset($data['alinknod'])) {
//                    $data['alinkfil'] = substr($result['links']['alinkfil'], 0, strlen($result['links']['alinkfil']) - 2);
//                    $data['alinknod'] = substr($result['links']['alinknod'], 0, strlen($result['links']['alinknod']) - 2);
//                }
//            }
//            else
//            {
//                $result = $this->generateLinks($data['node'], $data['node_links'], $undoNodes);
//                if( ! empty($result['links'])) $data['links'] = $result['links'];
//                else $data['links'] = "";
//            }
//        }
//        else $data['links'] = $data['node_links']['linker'];
//
//        if ($editOn != NULL and $editOn == 1) $data['node_edit'] = TRUE;
//        else
//        {
//            if (( $data['node']->info != '' ) && (strpos($data['node_text'],'[[INFO:') === false) && $data['node']->show_info)
//            {
//                $data['node_text'] .= '[[INFO:' . $data['node']->id . ']]';
//            }
//
//            if (( $data['node']->info == '' ) && (strpos($data['node_text'],'[[INFO:')))
//            {
//                $search = '[[INFO:' . $data['node']->id . ']]';
//                $data['node_text'] = str_replace($search, '',$data['node_text']);
//            }
//
//            $data['node_text'] = $this->parseText($data['node_text'], $mapId);
//        }
//
//        $data['trace_links'] = $this->generateReviewLinks($data['traces']);
//        $data['skin_path'] = $data['map']->skin->path;
//
//        //Calculate time for Timer and Pop-up messages
//        $data['timer_start'] = 1;
//        $data['popup_start'] = 1;
//
//        // Parse text key for each nodes
//        foreach (DB_ORM::model('map_popup')->getEnabledMapPopups($mapId) as $popup)
//        {
//            $popup->text = $this->parseText($popup->text);
//            $data['map_popups'][] = $popup;
//        }
//
//        $skin = 'labyrinth/skin/basic/basic';
//        if ($data['map']->skin->enabled)
//        {
//            $data['skin_path'] = $data['map']->skin->path;
//            if (file_exists($_SERVER['DOCUMENT_ROOT'].'/application/views/labyrinth/skin/'.$data['map']->skin->id.'/skin.php'))
//            {
//                $skin = 'labyrinth/skin/basic/basic_template';
//
//                $data['skin'] = View::factory('labyrinth/skin/'.$data['map']->skin->id.'/skin')->set('templateData', $data);
//                $skinData = json_decode($data['map']->skin->data, true);
//                if($skinData != null && isset($skinData['body'])) {
//                    $data['bodyStyle'] = base64_decode($skinData['body']);
//                }
//            }
//            else $skin = 'labyrinth/skin/basic/basic';
//        }
//        else $data['skin_path'] = NULL;
//
//        $this->template = View::factory($skin)->set('templateData', $data);
    }

    public function checkPatient($nodeId, $scenarioId, $from)
    {
        $result = array();
        if ($scenarioId)
        {
            $patients = DB_ORM::model('Patient_Scenario')->getPatientsByScenario($scenarioId);
            if ($patients)
            {
                $type = 'check';
                if ($from == 'go')
                {
                    $type = Session::instance()->get('patient_redirect');
                    Session::instance()->set('patient_redirect', false);
                }
                $patientSessions = $this->patient_sessions_update($nodeId, $scenarioId, $patients, $type);
                $this->executePatientRule($patientSessions);
                $result = $this->renderPatient($patientSessions);
            }
        }

        return $result;
    }

    public function executePatientRule($patientSessions)
    {
        $labyrinth      = new Model_Labyrinth;
        $counters       = DB_ORM::model('map_counter')->getCountersByMap($this->mapId);
        $values         = array();
        $patientValues  = array();

        foreach($counters as $counter)
        {
            $values[$counter->id] = $labyrinth->getCounterValueByID($this->mapId, $counter->id);
        }

        foreach ($patientSessions as $patientSession)
        {
            $patientConditions = json_decode($patientSession->patient_condition);
            foreach ($patientConditions as $id=>$data)
            {
                $patientValues[$id] = $data->value;
            }
        }

        $runtimeLogic                   = new RunTimeLogic;
        $runtimeLogic->values           = $values;
        $runtimeLogic->conditionValue   = $patientValues;

        foreach (DB_ORM::select('Patient_Rule')->query()->as_array() as $ruleObj)
        {
            $rule   = $ruleObj->rule;
            $array  = $runtimeLogic->parsingString($rule);
            $resultLogic = $array['result'];

            if (isset($resultLogic['goto']))
            {
                if ($resultLogic['goto'] != NULL)
                {
                    $nodes = DB_ORM::model('map_node')->getAllNode($this->mapId);
                    $inMap = false;
                    foreach ($nodes as $node)
                    {
                        if ( $node->id == $resultLogic['goto']) $inMap = true;
                    }
                    if ($inMap)
                    {
                        $goto = Session::instance()->get('goto', NULL);
                        if ($goto == NULL) Session::instance()->set('goto', $resultLogic['goto']);
                    }
                }
            }

            if (isset($resultLogic['counters']))
            {
                if (count($resultLogic['counters']) > 0)
                {
                    $counterString = $labyrinth->getCounterString($this->mapId);
                    if ($counterString != '')
                    {
                        foreach($resultLogic['counters'] as $key => $v)
                        {
                            $previousValue = $labyrinth->getCounterValueFromString($key, $counterString);
                            $counterString = $labyrinth->setCounterValueToString($key, $counterString, $v);

                            $diff = $v - $previousValue;
                            if ($diff > 0) $diff = '+'.$diff;
                            $countersFunc[$key][] = $diff;
                        }
                        $labyrinth->updateCounterString($this->mapId, $counterString);
                    }
                }
            }

            if (isset($resultLogic['conditions']) AND count($resultLogic['conditions']) > 0)
            {
                foreach($resultLogic['conditions'] as $conditionId => $newValue)
                {
                    foreach ($patientSessions as $patientSession)
                    {
                        $patientConditions = json_decode($patientSession->patient_condition, true);
                        if (isset($patientConditions[$conditionId]))
                        {
                            $patientConditions[$conditionId]['value'] = $newValue;
                            $patientSession->patient_condition = json_encode($patientConditions);
                            $patientSession->save();
                        }
                    }
                }
            }

            if (isset($resultLogic['deactivate']))
            {
                foreach ($patientSessions as $patientSession)
                {
                    $deactivateNodes                = json_decode($patientSession->deactivateNode, true);
                    $deactivateNodes[]              = $resultLogic['deactivate'];
                    $deactivateNodes                = array_unique($deactivateNodes);
                    $patientSession->deactivateNode = json_encode($deactivateNodes);
                    $patientSession->save();
                }
            }
        }
    }

    public function patient_path_update($id_node, $session, $redirect, $same)
    {
        $path               = json_decode($session->path);
        $lastNodeId         = $path ? end($path) : $id_node;
        $lastNodeObj        = DB_ORM::model('Map_Node', array($lastNodeId));
        $lastNodeMapId      = $lastNodeObj->map_id;
        $nextMap            = $this->mapId != $lastNodeMapId;
        $lastNodeCurrentMap = $lastNodeObj->end;

        if ($redirect == 'check' AND $path AND ! ($lastNodeCurrentMap AND $nextMap) AND $same)
        {
            Session::instance()->set('patient_redirect', 'redirect');
            Request::initial()->redirect(URL::base().'renderLabyrinth/go/'.$this->mapId.'/'.$lastNodeId);
        }

        $path[] = (int)$id_node;
        $path   = json_encode($path);

        $session->path = $path;
        $session->save();
    }

    public function patient_condition_update($id_node, $patientSession)
    {
        $pc  = json_decode($patientSession->patient_condition, true);
        foreach (DB_ORM::model('Patient_ConditionRelation')->get_conditions($patientSession->id_patient) as $condition)
        {
            $id_condition = (int)$condition->id;
            $change = DB_ORM::select('Patient_ConditionChange')->where('id_condition', '=', $id_condition)->where('id_node', '=', $id_node)->query()->fetch(0);
            $pc[$id_condition]['name'] = (string) $condition->name;
            $pc[$id_condition]['value'] = isset($pc[$id_condition]['value']) ? $pc[$id_condition]['value'] : (int) $condition->value;
            $pc[$id_condition]['appear'] = 0;
            if ($change)
            {
                $pc[$id_condition]['value'] += (int)$change->value;
                $pc[$id_condition]['appear'] = $change->appear;
            }
        }
        $pc = json_encode($pc);
        $patientSession->patient_condition = $pc;
        $patientSession->save();
        return $patientSession;
    }

    public function patient_sessions_update ($nodeId, $scenarioId, $patients, $redirect = false)
    {
        if (Auth::instance()->logged_in() AND $scenarioId) $userId = Auth::instance()->get_user()->id;
        else return false;

        $patientSessions = array();
        $whose          = '';
        $whoseId        = 0;

        foreach ($patients as $patient)
        {
            if ($patient->type == 'Longitudinal same set' OR $patient->type == 'Parallel same set' AND ! $whoseId)
            {
                $groupId        = $this->whosePatient($scenarioId, $userId);
                $whose          = $groupId ? 'group' : 'user';
                $whoseId        = $groupId ? $groupId : $userId;
            }
            $same           = $patient->type == 'Longitudinal same set' OR $patient->type == 'Longitudinal different set';
            $patientId      = $patient->id;
            $patientSession = DB_ORM::model('Patient_Sessions')->getSession($patientId, $whose, $whoseId, $scenarioId);

            if ( ! $patientSession) $patientSession = DB_ORM::model('Patient_Sessions')->create($patientId, $whose, $whoseId, $scenarioId);
            $this->patient_path_update($nodeId, $patientSession, $redirect, $same);

            if ($redirect != 'redirect') $patientSession = $this->patient_condition_update($nodeId, $patientSession);
            $patientSessions[] = $patientSession;
        }

        return $patientSessions;
    }

    public function whosePatient($scenarioId, $userId)
    {
        $groupId = 0;
        $userGroups = DB_ORM::select('User_Group')->where('user_id', '=', $userId)->query()->as_array();

        foreach ($userGroups as $userGroup)
        {
            $scenarioGroup = DB_ORM::select('Webinar_Group')->where('webinar_id', '=', $scenarioId)->where('group_id', '=', $userGroup->group_id)->query()->fetch(0);
            if ($scenarioGroup)
            {
                $groupId = $scenarioGroup->group_id;
                break;
            }
        }
        return $groupId;
    }

    public function action_checkKey() {
        $mapId       = $this->request->param('id', NULL);
        $securityKey = Arr::get($_POST, 'securityKey', false);
        if ($mapId AND $securityKey) {
            $checkKey = DB_ORM::model('map_key')->checkKey($mapId, $securityKey);
            if ($checkKey) {
                $sessionId = Session::instance()->id();
                $checkValue = Auth::instance()->hash('checkvalue'.$mapId.$sessionId);
                Session::instance()->set($checkValue, '1');
            } else {
                Session::instance()->set('keyError', 'Invalid key');
            }
            Request::initial()->redirect(URL::base().'renderLabyrinth/index/'.$mapId);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

    public function renderPatient ($patientSessions)
    {
        if ( ! Auth::instance()->logged_in()) return array();

        $data = array();
        $ajax = $this->request->is_ajax();
        foreach ($patientSessions as $patientSession)
        {
            $conditions = json_decode($patientSession->patient_condition);
            if ($conditions)
            {
                $condition_li = '';
                foreach ($conditions as $condition)
                {
                    if ( ! $condition->appear AND ! $ajax) continue;
                    $condition_li .= '<li>'.$condition->name.': '.$condition->value.'</li>';
                }

                if ($condition_li)
                {
                    $li = '<li>'.DB_ORM::model('Patient', array($patientSession->id_patient))->name.'</li>'.$condition_li;
                    if ($ajax) $data[] = $li;
                    else $data[$patientSession->id] = $li;
                }
            }
        }
        return $data;
    }

    public function action_dataPatientAjax()
    {
        $idPatients             = json_decode(Arr::get($_GET, 'patients', array()));
        $sessions               = array();
        $data['deactivateNode'] = array();

        foreach ($idPatients as $id)
        {
            $session                = DB_ORM::model('Patient_Sessions', array($id));
            $sessions[]             = $session;
            $data['deactivateNode'] = array_merge((array) json_decode($session->deactivateNode), $data['deactivateNode']);
        }
        $data['conditions'] = $this->renderPatient($sessions);
        exit(json_encode($data));
    }

    public function action_updateNode() {
        $mapId = $this->request->param('id', NULL);
        $nodeId = $this->request->param('id2', NULL);

        if ($_POST AND $mapId AND $nodeId) {
            DB_ORM::model('map_node')->updateNode($nodeId, $_POST);
            Request::initial()->redirect(URL::base() . 'renderLabyrinth/go/' . $mapId . '/' . $nodeId);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

    public function action_info() {
        $nodeId = $this->request->param('id', NULL);
        if ($nodeId != NULL) {
            $node = DB_ORM::model('map_node', array((int) $nodeId));
            $info = self::parseText($node->info);
            $this->template = View::factory('labyrinth/node/info')->set('info', $info);
        }
    }

    public function action_mapinfo()
    {
        $mapId = $this->request->param('id', NULL);
        if ($mapId) {
            $this->templateData['map'] = DB_ORM::model('map', array((int) $mapId));
            $this->templateData['center'] = View::factory('labyrinth/labyrinthInfo')->set('templateData', $this->templateData);
            $this->template->set('templateData', $this->templateData);
        } else {
            Request::initial()->redirect(URL::base() . 'openLabyrinth');
        }
    }

    public function action_review() {
        $mapId  = $this->request->param('id', NULL);
        $nodeId = $this->request->param('id2', NULL);

        if ($mapId AND $nodeId) {
            Model::factory('labyrinth')->review($nodeId);
            Request::initial()->redirect(URL::base().'renderLabyrinth/go/'.$mapId.'/'.$nodeId);
        }
        else {
            Request::initial()->redirect(URL::base());
        }
    }

    public function action_undo()
    {
        $mapId = $this->request->param('id', NULL);
        $nodeId = $this->request->param('id2', NULL);

        if ($mapId AND $nodeId ) {
            Request::initial()->redirect(URL::base().'renderLabyrinth/go/'.$mapId.'/'.$nodeId);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

    public function action_chatAnswer()
    {
        $chatId     = $this->request->param('id', NULL);
        $elemId     = $this->request->param('id2', NULL);
        $sessionId  = $this->request->param('id3', NULL);
        $mapId      = $this->request->param('id4', NULL);

        if ($chatId AND $elemId AND $sessionId AND $mapId)
        {
            $this->auto_render = false;
            echo Model::factory('labyrinth')->getChatResponce($sessionId, $mapId, $chatId, $elemId);
        } else {
            Response::factory()->body('');
        }
    }

    public function action_questionResponse()
    {
        $optionNumber   = $this->request->param('id', NULL);
        $questionId     = $this->request->param('id2', NULL);
        $nodeId         = $this->request->param('id3', NULL);
        $questionStatus = $this->request->param('id4', NULL);

        if ($optionNumber AND $questionId)
        {
            $this->auto_render = false;
            if( ! $nodeId) {
                $mapId = DB_ORM::model('Map_Question', array($questionId))->map_id;
                $nodeId = DB_ORM::model('Map_Node')->getRootNodeByMap($mapId);
            }
            echo Model::factory('labyrinth')->question($questionId, $optionNumber, $questionStatus, $nodeId);
            exit;
        }
    }

    public function action_saveSliderQuestionResponse()
    {
        $this->auto_render = false;
        $questionId             = $this->request->param('id', null);
        $responses              = Session::instance()->get('sliderQuestionResponses');
        $responses[$questionId] = Arr::get($_POST, 'value', 0);
        Session::instance()->set('sliderQuestionResponses', $responses);
    }

    public function action_remote() {
        $mapId = $this->request->param('id', NULL);
        $mode = $this->request->param('id2', NULL);

        $this->auto_render = false;

        if ($mapId != NULL and $mode != NULL) {
            switch ($mode) {
                case 'u':
                    $username = $this->request->param('id3', NULL);
                    $password = $this->request->param('id4', NULL);
                    $nodeId = $this->request->param('id5', NULL);
                    if ($this->checkRemoteUser($username, $password)) {
                        if ($nodeId != NULL) {
                            echo '<?xml version="1.0" encoding=UTF-8?>' . $this->remote_go($nodeId, $mapId);
                        } else {
                            $rootNode = DB_ORM::model('map_node')->getRootNodeByMap((int) $mapId);
                            echo '<?xml version="1.0" encoding=UTF-8?>' . $this->remote_go($rootNode->id, $mapId);
                        }
                    } else {
                        echo '<?xml version="1.0" encoding=UTF-8?><labyrinth>Not a valid service: no registration for this username and password</labyrinth>';
                    }
                    break;
                case 'i':
                    if ($this->checkRemoteIP($mapId)) {
                        $nodeId = $this->request->param('id3', NULL);
                        if ($nodeId != NULL) {
                            echo '<?xml version="1.0" encoding=UTF-8?>' . $this->remote_go($nodeId, $mapId);
                        } else {
                            $rootNode = DB_ORM::model('map_node')->getRootNodeByMap((int) $mapId);
                            echo '<?xml version="1.0" encoding=UTF-8?>' . $this->remote_go($rootNode->id, $mapId);
                        }
                    } else {
                        echo '<?xml version="1.0" encoding=UTF-8?><labyrinth>Not a valid service: no registration for this IP (' . getenv('REMOTE_ADDR') . ')</labyrinth>';
                    }
                    break;
            }
        } else {
            echo '';
        }
    }

    public function action_addBookmark()
    {
        $sessionId  = Session::instance()->get('session_id');
        $nodeId     = $this->request->param('id', NULL);
        $user       = Auth::instance()->get_user();

        if ($user AND $sessionId AND $nodeId) {
            DB_ORM::model('User_Bookmark')->addBookmark($nodeId, $sessionId, $user->id);
        }

        exit;
    }

    private function remote_go($nodeId, $mapId) {
        if ($mapId != NULL) {
            $node = DB_ORM::model('map_node')->getNodeById((int) $nodeId);

            if ($node != NULL) {
                $data = Model::factory('labyrinth')->execute($node->id);
                if ($data) {
                    $data['navigation'] = $this->generateNavigation($data['sections']);

                    if (!isset($data['node_links']['linker'])){
                        if ($data['node']->link_style->name == 'type in text') {
                            $result = $this->generateLinks($data['node'], $data['node_links']);
                            $data['links'] = $result['links']['display'];
                            $data['alinkfil'] = substr($result['links']['alinkfil'], 0, strlen($result['links']['alinkfil']) - 2);
                            $data['alinknod'] = substr($result['links']['alinknod'], 0, strlen($result['links']['alinknod']) - 2);
                            $data['remote_links'] = $this->generateRemoteLinks($data['node_links']);
                        } else {
                            $result = $this->generateLinks($data['node'], $data['node_links']);
                            $data['links'] = $result['links'];
                            $data['remote_links'] = $this->generateRemoteLinks($data['node_links']);
                        }
                    } else {
                        $data['links'] = $data['node_links']['linker'];
                    }

                    $data['node_text'] = $this->parseText($data['node_text'], $mapId);

                    $data['trace_links'] = $this->generateReviewLinks($data['traces']);

                    if ($data) {
                        $data['links'] = str_replace('Array', '', $data['links']);
                        $data['trace_links'] = '<a href="#" onclick="toggle_visibility(' . "'track'" . ');"><p class="style2"><strong>Review your pathway</strong></p></a><div id="track" style="display:none">' . $data['trace_links'] . '</div>';
                        $rootNode = DB_ORM::model('map_node')->getRootNodeByMap((int) $mapId);
                        $out = "<labyrinth>";
                        $out .= "<mnodetitle>" . urlencode($data['node_title']) . "</mnodetitle>";
                        $out .= "<javascripttime></javascripttime>";
                        $out .= "<mapname>" . urlencode($data['map']->name) . "</mapname>";
                        $out .= "<mapid>" . urlencode($data['map']->id) . "</mapid>";
                        $out .= "<mnodeid>" . urlencode($data['node']->id) . "</mnodeid>";
                        $out .= "<timestring></timestring>";
                        $out .= "<message>" . urlencode($data['node_text']) . "</message>";
                        $out .= "<colourbar></colourbar>";
                        $out .= "<linker>" . urlencode($data['links']) . "</linker>";
                        $out .= "<links>" . urlencode($data['remote_links']) . "</links>";
                        $out .= "<counters>" . $data['remoteCounters'] . "</counters>";
                        $out .= "<tracestring>" . urlencode($data['trace_links']) . "</tracestring>";
                        $out .= "<rootnode>" . urlencode($rootNode->id) . "</rootnode>";
                        $out .= "<infolink>" . urlencode($data['node']->info) . "</infolink>";
                        $out .= "<usermode></usermode>";
                        $out .= "<dam></dam>";
                        $out .= "<mysession>" . urlencode(Session::instance()->get('session_id')) . "</mysession>";
                        $out .= "<counterstring>" . urlencode($data['counters']) . "</counterstring>";
                        $out .= "<navme></navme>";
                        $out .= "<maptype>" . urlencode($data['map']->type->name) . "</maptype>";
                        $out .= "<remoteredir></remoteredir>";
                        $out .= "</labyrinth>";

                        return $out;
                    }
                } else {
                    return '';
                }
            } else {
                return '';
            }
        } else {
            return '';
        }
    }

    public function action_reset()
    {
        $mapId       = $this->request->param('id', null);
        $webinarId   = $this->request->param('id2', null);
        $webinarStep = $this->request->param('id3', null);

        if ($webinarId AND $webinarStep) {
            Session::instance()->set('webinarId', $webinarId);
            Session::instance()->set('step', $webinarStep);
        }

        Request::initial()->redirect(URL::base().'renderLabyrinth/index/'.$mapId);
    }

    public function action_ajaxDraggingQuestionResponse()
    {
        $this->auto_render = false;
        $post           = $this->request->post();
        $questionId     = Arr::get($post, 'questionId', null);
        $responsesJSON  = Arr::get($post, 'responsesJSON', null);

        if ($questionId) {
            $prevResponses = Session::instance()->get('dragQuestionResponses', array());

            $isNew = true;
            if (count($prevResponses)) {
                foreach($prevResponses as $key => $response) {
                    $object = json_decode($response, true);
                    if (isset($object['id']) AND (int)$object['id'] == (int)$questionId) {
                        $prevResponses[$key] = '{"id": '.$questionId.', "responses": '.$responsesJSON.'}';
                        $isNew = false;
                        break;
                    }
                }
            }

            if ($isNew) $prevResponses[] = '{"id": '.$questionId.', "responses": '.$responsesJSON.'}';

            Session::instance()->set('dragQuestionResponses', $prevResponses);
        }
    }

    public function action_downloadFile() {
        $fileId = $this->request->param('id', null);

        if($fileId != null) {
            $file = DB_ORM::model('map_element', array((int)$fileId));
            $filename = DOCROOT . $file->path; // of course find the exact filename....
            header('Pragma: public');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Cache-Control: private', false); // required for certain browsers
            header('Content-Type: ' . $file->mime);

            header('Content-Disposition: attachment; filename="'. basename($filename) . '";');
            header('Content-Transfer-Encoding: binary');
            header('Content-Length: ' . filesize($filename));

            readfile($filename);

            exit;
        }
    }

    public function action_popupAction()
    {
        $this->auto_render = false;
        Model::factory('labyrinth')->popup_counters($this->request->param('id', NULL), Arr::get($_POST, 'popupId', null));
    }

    private function checkRemoteUser($username, $password)
    {
        $username = Model::factory('utilites')->deHash($username);
        $password = Model::factory('utilites')->deHash($password);
        $user     = DB_ORM::model('user')->getUserByName($username);

        return ($user AND $user->password == $password AND $user->type->name == 'remote service') ? TRUE : FALSE;
    }

    private function checkRemoteIP($mapId)
    {
        return (($id = DB_ORM::model('remoteService')->checkService(getenv('REMOTE_ADDR'))) != FALSE AND DB_ORM::model('remoteMap')->checkMap($id, $mapId)) ? TRUE : FALSE;
    }

    private function generateRemoteLinks($links) {
        $result = '';
        if (count($links) > 0) {
            foreach ($links as $link) {
                $result .= $link->node_id_2 . ',' . $link->text . ';';
            }
        }
        return $result;
    }

    private function generateLinks($node, $links, $undoNodes = null)
    {
        $result             = NULL;
        $result['links']    = '';
        $nextNodeTemplate   = 'This is the end of step.';
        $endNodeTemplate    = '<div><a href="'.URL::base().'reportManager/finishAndShowReport/'.Session::instance()->get('session_id').'/'.$node->map_id.'">End Session and View Feedback</a></div>';
        $wSectionId         = Session::instance()->get('webinarSectionId', 0);

        if ($wSectionId)
        {
            $endNodes = DB_ORM::model('Map_Node_Section_Node')->getEndNode($wSectionId);
            foreach ($endNodes as $endNode)
            {
                if ($endNode->node_id != $node->id) continue;

                $userSession = DB_ORM::model('User_Session', array(Session::instance()->get('session_id')));
                $wId         = $userSession->webinar_id;
                $wStep       = $userSession->webinar_step;
                $wMapObj     = DB_ORM::select('Webinar_Map')->where('webinar_id', '=', $wId)->where('which', '=', 'section')->where('reference_id', '=', $wSectionId)->query()->fetch(0);

                // get next obj, if its in current webinar step, try to create link
                if ($wMapObj)
                {
                    $wMapObj = DB_ORM::model('Webinar_Map', array($wMapObj->id + 1));
                    if ($wMapObj->step = $wStep)
                    {
                        $href = URL::base().'webinarManager/play/'.$wId.'/'.$wStep.'/'.$wMapObj->reference_id.'/'.$wMapObj->which;
                        switch($wMapObj->which)
                        {
                            case 'labyrinth':
                                $mapRootNode = DB_ORM::model('Map_Node')->getRootNodeByMap($wMapObj->reference_id);
                                if ($mapRootNode) $nextNodeTemplate = '<a href='.$href.'>'.$mapRootNode->title.'</a>';
                                break;
                            case 'section':
                                $inNode = DB_ORM::model('Map_Node_Section_Node')->getInNode($wMapObj->reference_id);
                                if ($inNode) $nextNodeTemplate = '<a href='.$href.'>'.DB_ORM::model('Map_Node', array($inNode->node_id))->title.'</a>';
                                break;
                        }
                    }
                }

                switch($endNode->node_type)
                {
                    case 'out':
                        $result['links'] = $endNodeTemplate;
                        break;
                    case 'crucial':
                        $result['links'] = $nextNodeTemplate;
                        break;
                }
                Session::instance()->delete('webinarSectionId');
                return $result;
            }
        }

        if (is_array($links) and count($links) > 0)
        {
            $result['remote_links'] = '';
            $result['links']        = '';
            foreach ($links as $link)
            {
                if (isset($undoNodes[$link->node_2->id])) continue;

                $title = ($link->text != '' and $link->text != ' ') ? $link->text : $link->node_2->title;

                switch ($node->link_style->name)
                {
                    case 'hyperlinks':
                        $content = ($link->image_id != 0) ? '<img src="'.URL::base().$link->image->path.'">' : $title;
                        $result['links'] .= '<li><a href="'.URL::base().'renderLabyrinth/go/'.$node->map_id.'/'.$link->node_id_2.'">'.$content.'</a></li>';
                        break;
                    case 'dropdown':
                    case 'dropdown + confidence':
                        $result['links'] .= '<option value="'.$link->node_id_2.'">'.$title.'</option>';
                        break;
                    case 'type in text':
                        $result['links']['alinkfil'] .= '"'.strtolower($title).'", ';
                        $result['links']['alinknod'] .= '"'.$link->node_id_2.'", ';
                        break;
                    case 'buttons':
                        $result['links'] .= '<div><a href="'.URL::base().'renderLabyrinth/go/'.$node->map_id.'/'.$link->node_id_2.'" class="btn">'.$title.'</a></div>';
                        break;
                }
            }

            switch ($node->link_style->name)
            {
                case 'hyperlinks':
                    $result['links'] = '<ul class="links navigation">'.$result['links'].'</ul>';
                    break;
                case 'dropdown':
                    $result['links'] = '<select name="links" onchange='.chr(34)."jumpMenu('parent',this,0)".chr(34).' name="linkjump"><option value="">select ...</option>'.$result['links'].'</select>';
                    break;
                case 'dropdown + confidence':
                    $result['links'] = '<form method="post" action="'.URL::base().'renderLabyrinth/go/'.$node->map_id.'"><select name="id">'.$result['links'].'</select>';
                    $result['links'] .= '<select name="conf">';
                    $result['links'] .= '<option value="">select how confident you are ...</option>';
                    $result['links'] .= '<option value="4">I am very confident</option>';
                    $result['links'] .= '<option value="3">I am quite confident</option>';
                    $result['links'] .= '<option value="2">I am quite unconfident</option>';
                    $result['links'] .= '<option value="1">I am very unconfident</option>';
                    $result['links'] .= '</select><input type="submit" name="submit" value="go" /></form>';
                    break;
                case 'type in text':
                    $result['links']['display'] = '<form action="'.URL::base().'renderLabyrinth/go/'.$node->map_id.'" name="form2"><input name="filler" type="text" size="25" value="" onKeyUp="javascript:Populate(this.form);"><input type="hidden" name="id" value="'.$node->id.'" /><input type="submit" name="submit" value="go" /></form>';
                    break;
            }

            if ($node->end and $node->link_style->name == 'type in text') $result['links']['display'] .= $endNodeTemplate;
            else if ($node->end) $result['links'] .= $endNodeTemplate;

            return $result;
        }
        else
        {
            if ($node->end and $node->link_style->name == 'type in text')
            {
                if( ! isset($result['links']['display'])) $result['links']['display'] = '';
                $result['links']['display'] .= $endNodeTemplate;
                return $result;
            }
            else if ($node->end)
            {
                $result['links'] .= $endNodeTemplate;
                return $result;
            }

            if ($links != '') return $links;
        }
        return NULL;
    }

    private function generateNavigation($sections) {
        $result = '';
        if (count($sections) > 0) {
            $result = '<ul class="navigation">';
            foreach ($sections as $section) {
                if ($section->map->section->name == 'visible') $result .= "<li>".$section->name."</li>";
                elseif ($section->map->section->name == 'navigable') {
                    $result .= '<li><a href="';

                    if (count($section->nodes) > 0) $result .= URL::base().'renderLabyrinth/go/'.$section->map_id.'/'.$section->nodes[0]->node->id;
                    else $result .= URL::base().'renderLabyrinth/index/'.$section->map_id;

                    $result .= '">' . $section->name . '</a></li>';
                }
            }
            $result .= '</ul>';

        }
        return $result;
    }

    public static function parseText($text, $mapId = NULL, $elementType = '')
    {
        $codes  = array('MR', 'FL', 'CHAT', 'DAM', 'AV', 'VPD', 'QU', 'INFO', 'CD', 'CR');

        foreach ($codes as $code)
        {
            $regExp = '/[\['.$code.':\d\]]+/';
            if (preg_match_all($regExp, $text, $matches))
            {
                foreach ($matches as $match)
                {
                    foreach ($match as $value)
                    {
                        if (stristr($value, '[[' . $code . ':'))
                        {
                            $m = explode(':', $value);
                            $id = substr($m[1], 0, strlen($m[1]) - 2);
                            if (is_numeric($id))
                            {
                                $replaceString = '';
                                switch ($code) {
                                    case 'MR':
                                        $media = DB_ORM::model('map_element', array((int) $id));
                                        if ($media->mime == 'application/x-shockwave-flash') {
                                            $replaceString = Controller_RenderLabyrinth::getSwfHTML($id);
                                        } elseif (strstr($media->mime, 'audio')) {
                                            $replaceString = Controller_RenderLabyrinth::getAudioHTML($id, $elementType);
                                        } else if(in_array($media->mime, array('image/jpg', 'image/jpeg', 'image/gif', 'image/png'))) {
                                            $replaceString = Controller_RenderLabyrinth::getImageHTML($id);
                                        } else {
                                            $replaceString = Controller_RenderLabyrinth::getFileLink($id);
                                        }
                                        break;
                                    case 'AV':
                                        $replaceString = Controller_RenderLabyrinth::getAvatarHTML($id);
                                        break;
                                    case 'CHAT':
                                        $replaceString = Controller_RenderLabyrinth::getChatHTML($id);
                                        break;
                                    case 'QU':
                                        $replaceString = Controller_RenderLabyrinth::getQuestionHTML($id);
                                        break;
                                    case 'VPD':
                                        $replaceString = Controller_RenderLabyrinth::getVpdHTML($id);
                                        break;
                                    case 'DAM':
                                        $replaceString = Controller_RenderLabyrinth::getDamHTML($id);
                                        break;
                                    case 'INFO':
                                        $replaceString = Controller_RenderLabyrinth::getInfoHTML($id);
                                        break;
                                    case 'CD':
                                        $replaceString = Controller_RenderLabyrinth::getVisualDisplayHTML($id);
                                        break;
                                    case 'CR':
                                        $replaceString = Controller_RenderLabyrinth::getCounterHTML($mapId, $id);
                                        break;
                                }

                                $text = str_replace('<p>[[' . $code . ':' . $id . ']]</p>', $replaceString, $text);
                            }
                        }
                    }
                }
            }
        }
        return $text;
    }

    private static function getInfoHTML($id) {
        $info = '<a href="#" onclick="window.open(\'' . URL::base() . 'renderLabyrinth/info/' . $id . '\', \'info\', \'toolbar=no, directories=no, location=no, status=no, menubat=no, resizable=no, scrollbars=yes, width=500, height=400\'); return false;"><img src="' . URL::base() . 'images/info_lblu.gif" border="0" alt="info"></a>';
        return $info;
    }

    private static function getImageHTML($id)
    {
        $image = DB_ORM::model('map_element', array((int) $id));
        return $image ? '<img src="'.URL::base().$image->path.'">' : '';
    }

    private static function getFileLink($id)
    {
        $file = DB_ORM::model('map_element', array((int) $id));
        return $file ? '<a class="file-link" href="'.URL::base().'renderlabyrinth/downloadFile/'.$file->id.'">'.$file->name.'</a>' : '';
    }

    private static function getAudioHTML($id, $elementType = '') {
        $audio = DB_ORM::model('map_element', array((int) $id));
        $attributes = $elementType == 'popup' ? '' : ' autoplay="autoplay" autobuffer';
        return $audio ? '<audio src="'.URL::base().$audio->path.'" controls preload="auto"'.$attributes.'></audio>' : '';
    }

    private static function getSwfHTML($id)
    {
        $swf = DB_ORM::model('map_element', array((int) $id));
        if ($swf) {
            $userBrowser = Controller_RenderLabyrinth::getUserBroswer();
            if (substr($userBrowser, 0, 2) == "ie")
            {
                return "<object classid='clsid:D27CDB6E-AE6D-11cf-96B8-444553540000' codebase='http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0'>
                <param name='movie' value='".URL::base().$swf->path."' />
                <param name='allowScriptAccess' value='sameDomain' />
                <param name='quality' value='high' />
                </object>";
            }
            else
            {
                return "<object type='application/x-shockwave-flash' data='".URL::base().$swf->path."'>
                <param name='allowScriptAccess' value='sameDomain' />
                <param name='quality' value='high' />
                </object>";
            }
        }
        return '';
    }

    private static function getAvatarHTML($id)
    {
        $avatar = DB_ORM::model('map_avatar', array((int) $id));
        return ($avatar->image != null AND file_exists(DOCROOT.URL::base().'/avatars/'.$avatar->image))
            ? '<img src="'.URL::base().'avatars/'.$avatar->image.'" />'
            : '<img src="'.URL::base().'avatars/default.png" />';
    }

    private static function getChatHTML($id)
    {
        $chat = DB_ORM::model('map_chat', array((int) $id));
        $result = '';
        if ($chat)
        {
            $result = '<table bgcolor="#eeeeee" width="100%">';
            if (count($chat->elements) > 0)
            {
                foreach ($chat->elements as $element)
                {
                    $result .= "<tr><td><p><a onclick='ajaxChatShowAnswer(" . $chat->id . ", " . $element->id . ");return false;' href='#' id='ChatQuestion" . $element->id . "'>" . $element->question . "</a></p></td></tr>";
                    $result .= "<tr><td><div id='ChatAnswer" . $element->id . "'></div></td></tr>";
                }
            }
            $result .= '</table>';
        }

        return $result;
    }

    private static function getQuestionHTML ($id)
    {
        $question        = DB_ORM::model('map_question', array((int) $id));
        $result          = '';
        $q_type          = $question->type->value;
        $qTitle          = $question->type->title;
        $previousAnswers = '';

        if ($question) {
            // ----- get validator data ----- //
            $validator = '';
            $errorMsg  = '';
            $parameter = '';

            $getValidator = function($id, &$validator, &$errorMsg, &$parameter) {
                $validationObj = DB_ORM::model('Map_Question_Validation')->getRecord($id);

                if ($validationObj) {
                    $validator = 'data-validator="'.$validationObj->validator.'" ';
                    $errorMsg  = 'data-errorMsg="'.$validationObj->error_message.'" ';
                    $parameter = 'data-parameter="'.$validationObj->second_parameter.'" ';
                }
            };
            // ----- end get validator data ----- //

            // ----- previous answer ----- //
            $getPreviousAnswers = function(&$previousAnswers, $mapId, $questionId, $nodeId, $cumulativeType) {
                $idScenario     = Session::instance()->get('idScenario');
                $responses      = '';
                $responsesSQL   = array();

                if ($idScenario AND Controller_RenderLabyrinth::$isCumulative){
                    $responsesSQL = DB_SQL::select('default')
                        ->from('user_sessions', 's')
                        ->join('LEFT', 'user_responses', 'r')
                        ->on('s.id', '=', 'r.session_id')
                        ->where('s.webinar_id', '=', $idScenario)
                        ->where('s.map_id', '=', $mapId)
                        ->where('r.question_id', '=', $questionId)
                        ->where('r.node_id', '=', $nodeId)
                        ->query();
                } elseif ($cumulativeType) {
                    $responsesSQL = DB_SQL::select('default')
                        ->from('user_sessions', 's')
                        ->join('LEFT', 'user_responses', 'r')
                        ->on('s.id', '=', 'r.session_id')
                        ->where('s.map_id', '=', $mapId)
                        ->where('r.question_id', '=', $questionId)
                        ->where('r.node_id', '=', $nodeId)
                        ->query();
                }

                foreach ($responsesSQL as $response) {
                    if (isset($response['response'])) {
                        $responses .= $response['response'].'<br>';
                    }
                }

                if ($responses) {
                    $previousAnswers = '<p class="previous-answers">Previous answer:<br>'.$responses.'</p>';
                }
            };

            // ----- end answer ----- //

            if ($q_type == 'text') {
                $getValidator($id, $validator, $errorMsg, $parameter);
                $getPreviousAnswers($previousAnswers, $question->map_id, $question->id, Controller_RenderLabyrinth::$nodeId, false);

                $result = '<input autocomplete="off" '.$validator.$errorMsg.$parameter.'class="lightning-single" type="text" size="'.$question->width.'" name="qresponse_'.$question->id.'" placeholder="'.$question->prompt.'" id="qresponse_'.$question->id.'" ';
                $submitText = 'Submit';
                if ($question->show_submit == 1) {
                    if ($question->submit_text != null) $submitText = $question->submit_text;
                    $result .= '/>
                        <span id="questionSubmit'.$question->id.'" style="display:none;font-size:12px">Answer has been sent.</span>
                        <button onclick="$(this).hide();$(\'#questionSubmit'.$question->id.'\').show();$(\'#qresponse_'.$question->id.'\').attr(\'disabled\', \'disabled\');">'.$submitText.'</button>';
                } else {
                    $result .= 'onKeyUp="if (event.keyCode == 13) {$(\'#questionSubmit'.$question->id.'\').show(); $(\'#qresponse_'.$question->id.'\').attr(\'disabled\', \'disabled\'); }"/><span id="questionSubmit'.$question->id.'" style="display:none;font-size:12px">Answer has been sent.</span>';
                }
                $result .= '<div id="AJAXresponse'.$question->id.'"></div>';
                Controller_RenderLabyrinth::addQuestionIdToSession($id);
            } else if ($q_type == 'area') {
                $addClass = '';
                $cumulative = false;

                // ----- only for cumulative ----- //
                if ($qTitle == 'Cumulative') {
                    $addClass = ' cumulative';
                    $cumulative = true;
                }
                // ----- end only for cumulative ----- //

                $getValidator($id, $validator, $errorMsg, $parameter);
                $getPreviousAnswers($previousAnswers, $question->map_id, $question->id, Controller_RenderLabyrinth::$nodeId, $cumulative);

                $result =
                    '<textarea autocomplete="off" '.$validator.$errorMsg.$parameter.'class="lightning-multi'.$addClass.'" cols="'.$question->width.'" rows="'.$question->height.'" name="qresponse_'.$question->id.'" id="qresponse_'.$question->id .'" placeholder="'.$question->prompt.'"></textarea>'.
                    '<p>'.
                        '<span id="questionSubmit'.$question->id.'" style="display:none; font-size:12px">Answer has been sent.</span>'.
                        '<button onclick="$(this).hide();$(\'#questionSubmit'.$question->id.'\').show();$(\'#qresponse_'.$question->id.'\').attr(\'readonly\', \'readonly\');">Submit</button>
                    </p>';
                $result .= '<div id="AJAXresponse'.$question->id.'"></div>';

                Controller_RenderLabyrinth::addQuestionIdToSession($id);
            } else if($q_type == 'mcq') {
                if (count($question->responses)) {
                    $displayType = ($question->type_display == 1) ? ' horizontal' : '';
                    $result = '<div class="questionResponces'.$displayType.'">';
                    $result .= '<ul class="navigation">';
                    $i = 1;
                    foreach ($question->responses as $response)
                    {
                        $result .= '<li>';
                        $result .= '<span id="click'.$response->id.'"><input type="checkbox" class="lightning-choice" name="option-'.$id.'" data-question="'.$question->id.'" data-response="'.$response->id.'" data-tries="'.$question->num_tries.'" data-val="'.$response->response.'"/></span>';
                        $result .= '<span class="text">'.$response->response.'</span>';
                        $result .= '<span id="AJAXresponse'.$response->id.'"></span>';
                        $result .= '</li>';
                        $i++;
                    }
                    $result .= '</ul></div>';
                    if($question->show_submit == 1 && $question->redirect_node_id) {
                        $result .=
                           '<div class="questionSubmitButton">
                            <a href="'.URL::base().'renderLabyrinth/go/'.$question->map_id.'/'.$question->redirect_node_id.'"><input type="button" value="'.$question->submit_text.'"/></a>
                            </div>';
                    }
                }
            } else if($q_type == 'pcq') {
                if (count($question->responses)) {
                    $displayType = ($question->type_display == 1) ? ' horizontal' : '';
                    $result = '<div class="questionResponces questionForm_'.$question->id.$displayType.'">';
                    $result .= '<ul class="navigation">';
                    $i = 1;

                    foreach ($question->responses as $response) {
                        $result .= '<li>';
                        $result .= '<span id="click'.$response->id.'"><input type="radio" class="lightning-choice" name="option-'.$id.'" data-question="'.$question->id.'" data-response="'.$response->id.'" data-tries="'.$question->num_tries.'" data-val="'.$response->response.'"/></span>';
                        $result .= '<span>'.$response->response.'</span>';
                        $result .= '<span id="AJAXresponse' . $response->id . '"></span>';
                        $result .= '</li>';
                        $i++;
                    }

                    if ($question->show_submit == 1 && $question->redirect_node_id != null && $question->redirect_node_id > 0)
                    {
                        $result .=
                           '<div class="questionSubmitButton">
                            <a href="'.URL::base().'renderLabyrinth/go/'.$question->map_id.'/'.$question->redirect_node_id.'"><input type="button" value="'.$question->submit_text.'" /></a>
                            </div>';
                    }
                    $result .= '</div>';
                }
            } else if ($q_type == 'sct') {
                $disposable = ($question->num_tries == 1) ? ' disposable' : '';
                $horizontal = ($question->type_display == 1) ? ' horizontal' : '';
                $result .= '<ul class="navigation'.$horizontal.'">';

                foreach($question->responses as $response)
                {
                    $result .= '<li>';
                    $result .= '<label>';
                    $result .= '<input class="sct-question'.$disposable.'" data-response="'.$response->id.'" data-question="'.$response->question_id.'" data-val="'.$response->response.'" type="radio" name="option-'.$id.'"/>';
                    $result .= $response->response;
                    $result .= '</label>';
                    $result .= '</li>';

                };
                $result .= '</ul>';
            } else if($q_type == 'slr') {
                if($question->settings != null)
                {
                    $settings = json_decode($question->settings);
                    $sliderValue = $settings->minValue;

                    if ($question->counter_id > 0) {
                        $sliderValue = Controller_RenderLabyrinth::getCurrentCounterValue($question->map_id, $question->counter_id);
                    } else if (property_exists($settings, 'defaultValue')) {
                        $sliderValue = $settings->defaultValue;
                    }

                    if ($sliderValue > $settings->maxValue) {
                        $sliderValue = $settings->maxValue;
                    } else if ($sliderValue < $settings->minValue) {
                        $sliderValue = $settings->minValue;
                    }

                    if($settings->showValue == 1) {
                        $result .= '<div style="margin-bottom: 22px;position:relative">
                                        <input autocomplete="off" type="text" id="sliderQuestionR_'.$question->id.'" value="'.$settings->minValue.'" style="float: left; height: 20px; padding: 0; margin: 0; font-size: 11px; width: 40px;" ' . ($settings->abilityValue == 0 ? 'disabled' : '') . '/>
                                        <div style="font-size: 12px;position: absolute;' . ($settings->orientation == 'hor' ? "top: 21px;left: 51px;" : "top: 2px;left: 74px;") . '">' . $settings->minValue . '</div>
                                        <script>
                                            var slider' . $question->id . ' = new dhtmlxSlider({
                                                size: 300,
                                                value: '    . $sliderValue                       . ',
                                                min: '      . $settings->minValue                       . ',
                                                max: '      . $settings->maxValue                       . ',
                                                skin: "'    . $settings->sliderSkin                     . '",
                                                step: '     . $settings->stepValue                      . ',
                                                vertical: ' . ($settings->orientation == 'hor' ? 0 : 1) . ',
                                                onChange: function(n) { $("#sliderQuestionR_' . $question->id . '").val(n); },
                                                onSlideEnd: function(value) { sendSliderValue(' . $question->id . ', value); }
                                            });
                                            slider' . $question->id . '.init();
                                            $("#sliderQuestionR_' . $question->id . '").val(' . $sliderValue . ');
                                            $("#sliderQuestionR_' . $question->id . '").change(function() {
                                                var value = $(this).val();
                                                if(value > ' . $settings->maxValue . ') {
                                                    value = ' . $settings->maxValue . ';
                                                    $(this).val(value);
                                                } else if(value < ' . $settings->minValue . ') {
                                                    value = ' . $settings->minValue . ';
                                                    $(this).val(value);
                                                }

                                                slider' . $question->id . '.setValue(value);
                                                sendSliderValue(' . $question->id . ', value);
                                            });
                                        </script>
                                        <div style="font-size: 12px;position: absolute;' . ($settings->orientation == 'hor' ? "top: 21px;left: 330px;" : "top: 284px;left: 74px;") . '">' . $settings->maxValue . '</div>
                                    </div>';
                    } else {
                        $result .= '<div style="margin-bottom: 22px;position:relative">
                                        <div style="font-size: 12px;position: absolute;' . ($settings->orientation == 'hor' ? "top: 21px;left: 5px;" : "top: 2px;left: 34px;") . '">' . $settings->minValue . '</div>
                                        <script>
                                            var slider = new dhtmlxSlider({
                                                size: 300,
                                                value: '    . $sliderValue                       . ',
                                                min: '      . $settings->minValue                       . ',
                                                max: '      . $settings->maxValue                       . ',
                                                skin: "'    . $settings->sliderSkin                     . '",
                                                step: '     . $settings->stepValue                      . ',
                                                vertical: ' . ($settings->orientation == 'hor' ? 0 : 1) . ',
                                                onSlideEnd: function(value) { sendSliderValue(' . $question->id . ', value); }
                                            });
                                            slider.init();
                                        </script>
                                        <div style="font-size: 12px;position: absolute;' . ($settings->orientation == 'hor' ? "top: 21px;left: 290px;" : "top: 284px;left: 34px;") . '">' . $settings->maxValue . '</div>
                                    </div>';
                    }
                }
            } else if ($q_type == 'dd') {
                $result .= '<ul class="drag-question-container" id="qresponse_'.$question->id.'" questionId="'.$question->id.'">';
                foreach ($question->responses as $response) {
                    $result .= '<li class="sortable" responseId="'.$response->id.'">'.$response->response.'</li>';
                }
                $result .= '</ul>';

                if ($question->show_submit == 1) {
                    $submitText = ($question->submit_text != null) ? $question->submit_text : 'Submit';
                    $result .= '<span id="questionSubmit'.$question->id.'" style="display:none;font-size:12px">Answer has been sent.</span>
                        <button onclick="ajaxDrag('.$question->id.');$(this).hide();" >'.$submitText.'</button>';
                }
            } else if ($q_type == 'sjt') {
                $result .= '<ul class="drag-question-container" id="qresponse_'.$question->id.'" questionId="'.$question->id.'">';
                foreach ($question->responses as $response){
                    $result .= '<li class="sortable" responseId="'.$response->id.'">'.$response->response.'</li>';
                }
                $result .= '</ul>';
            }
            $result = '<div class="questions">'.$question->stem.$result.'</div>';
        }
        return $result;
    }

    public function action_ajaxScriptConcordanceTesting ()
    {
        $this->auto_render  = false;
        $idResponse         = $this->request->post('idResponse');
        $idQuestion         = $this->request->post('idQuestion');
        $data               = Session::instance()->get('sctResponses');
        $data[$idQuestion]  = $idResponse;

        Session::instance()->set('sctResponses', $data);
        exit;
    }

    private static function addQuestionIdToSession($id)
    {
        $arrayAddedQuestions = Session::instance()->get('arrayAddedQuestions', array());
        $arrayAddedQuestions[$id] = true;
        Session::instance()->set('arrayAddedQuestions', $arrayAddedQuestions);
    }

    private static function getCurrentCounterValue($mapId, $counterId)
    {
        $rootNode  = DB_ORM::model('map_node')->getRootNodeByMap($mapId);
        $counter   = DB_ORM::model('map_counter', array((int)$counterId));
        $sessionId = Session::instance()->get('session_id', NULL);

        if ($sessionId == NULL AND isset($_COOKIE['OL'])) $sessionId = $_COOKIE['OL'];
        else if ($sessionId == NULL) $sessionId = 'notExist';


        $currentCountersState = '';
        if ($rootNode != NULL) $currentCountersState = DB_ORM::model('user_sessionTrace')->getCounterByIDs($sessionId, $rootNode->map_id, $rootNode->id);

        $thisCounter = null;
        if ($currentCountersState != '')
        {
            $s           = strpos($currentCountersState, '[CID=' . $counter->id . ',') + 1;
            $tmp         = substr($currentCountersState, $s, strlen($currentCountersState));
            $e           = strpos($tmp, ']') + 1;
            $tmp         = substr($tmp, 0, $e - 1);
            $tmp         = str_replace('CID=' . $counter->id . ',V=', '', $tmp);
            $thisCounter = $tmp;
        }

        return $thisCounter != null ? $thisCounter : 0;
    }

    public static function getVpdHTML($id) {
        $result = '';
        $vpd = DB_ORM::model('map_vpd', array((int) $id));
        if ($vpd != NULL) {
            switch ($vpd->type->name) {
                case 'VPDText':
                    $vpdType = Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'VPDTextType');
                    $vpdText = Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'VPDText');
                    switch ($vpdType) {
                        case 'narrative':
                            $result .= $vpdText;
                            break;
                        case 'chief complaint':
                            $result .= '<p>Chief complaint: ' . $vpdText . '</p>';
                            break;
                        case 'history':
                            $result .= '<p>History: ' . $vpdText . '</p>';
                            break;
                        case 'problem':
                            $result .= '<p>Problem: ' . $vpdText . '</p>';
                            break;
                        case 'allergy':
                            $result .= '<p>Allergy: ' . $vpdText . '</p>';
                            break;
                    }
                    break;
                case 'PatientDiagnoses':
                    $result .= "<table width='100%' border=1 cellspacing='0' cellpadding='6' RULES=NONE FRAME=BOX><tr><td align='left' valign='top' width='30%'><p><strong>Patient Data</strong></p></td><td align='left' valign='top'><p>";
                    $demogText = Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'DemogText');
                    if ($demogText != '') {
                        $result .= $demogText . ' : ' . Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'CoreDemogType');
                    } else {
                        $result .= Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'DemogTitle') . ' : ' . Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'DemogDesc');
                    }
                    $result .= '</p></td></tr></table>';
                    break;
                case 'AuthorDiagnoses':
                    $result .= "<table width='100%' border='1' cellspacing='0' cellpadding='6' RULES='NONE' FRAME='BOX'><tr><td align='left' valign='top' width='30%'><p><strong>Diagnosis</strong></p></td><td align='left' valign='top'><p>";
                    $result .= 'Diagnosis: ' . Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'aDiagTitle') . '<br/>';
                    $result .= 'Description: ' . Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'aDiagDesc');
                    $result .= '</p></td></tr></table>';
                    break;
                case 'Medication':
                    $result .= "<table width='100%' border='1' cellspacing='0' cellpadding='6' RULES='NONE' FRAME='BOX'><tr><td align='left' valign='top' width='30%'><p><strong>Medication</strong></p></td><td align='left' valign='top'><p>";
                    $result .= "Medication: " . Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'MedicTitle') . "<br />";
                    $result .= "Dose: " . Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'MedicDose') . "</p></td><td><p>";
                    $result .= "Route: " . Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'MedicRoute') . "<br />";
                    $result .= "Frequency: " . Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'MedicFreq') . "</p></td>";
                    if (Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'MedicFreq') != '') {
                        $result .= "<td valign='top'><p>Reference: " . Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'MedicSource') . "<br />";
                        $result .= "ID: " . Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'MedicSourceID') . "</p></td>";
                    }
                    $result .= '</tr></table>';
                    break;
                case 'InterviewItem':
                    $result .= "<table border='1' width='100%' cellspacing='0' cellpadding='6' RULES='NONE' FRAME='BOX'><tr><td width='10%' align='left' valign='top' width='30%'><p><strong>Interview Item</strong></p></td><td align='left' valign='top'><p>";
                    if (Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'trigger') == 'on') {
                        $result .= '<script type="text/javascript">function toggle_visibility(id) {
                                    var e = document.getElementById(id);
                                    if(e.style.display == "none")
                                    e.style.display = "block";
                                    else
                                    e.style.display = "none";
                                    }</script>';
                        $result .= '<p><a href="#" onclick="toggle_visibility(' . "'vpdQ_" . $vpd->id . "'" . ');">Q: ' . Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'QAQuestion') . '</a></p>';
                        $result .= "<div id='vpdQ_" . $vpd->id . "' style='display:none'><p>A: " . Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'QAAnswer') . "</p></div>";
                    } else {
                        $result .= '<p>Q: "' . Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'QAQuestion') . '"</p>';
                        $result .= "<p>A: " . Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'QAAnswer') . "</p>";
                    }

                    if (Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'QAMedia') != '') {
                        $mId = (int) Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'QAMedia');
                        $mediaElement = DB_ORM::model('map_element', array((int) $mId));
                        if ($mediaElement->mime == 'application/x-shockwave-flash') {
                            $result .= Controller_RenderLabyrinth::getSwfHTML($id);
                        } else {
                            $result .= self::getImageHTML($mId);
                        }
                    }
                    $result .= '</p></td></tr></table>';
                    break;
                case 'PhysicalExam':
                    $result .= "<table width='100%' border='1' cellspacing='0' cellpadding='6' RULES='NONE' FRAME='BOX'><tr><td align='left' valign='top' width='30%'><p><strong>Physical Examination</strong></p></td><td align='left' valign='top'><p>";
                    $result .= "Examination: " . Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'ExamName') . "<br />";
                    if (Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'ExamDesc') != '') {
                        $result .= "Description: " . Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'ExamDesc') . "<br />";
                    }
                    $result .= "Body part: " . Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'BodyPart') . "<br />";
                    $result .= "Orientation: - " . Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'ProxDist')
                            . ' - ' . Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'ProxDist') .
                            ' - ' . Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'RightLeft') .
                            ' - ' . Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'FrontBack') .
                            ' - ' . Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'InfSup') .
                            "</p></td><td valign='top'><p>";

                    if (Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'FindMedia') != '') {
                        $mId = (int) Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'FindMedia');
                        $mediaElement = DB_ORM::model('map_element', array((int) $mId));
                        if ($mediaElement->mime == 'application/x-shockwave-flash') {
                            $result .= Controller_RenderLabyrinth::getSwfHTML($id);
                        } else {
                            $result .= self::getImageHTML($mId);
                        }
                    }

                    $result .= '</p></td></tr></table>';
                    break;
                case 'DiagnosticTest':
                    $result .= "<table width='100%' border='1' cellspacing='0' cellpadding='6' RULES='NONE' FRAME='BOX'><tr><td align='left' valign='top'><p><strong>Diagnostic Test</strong></p></td><td align='left' valign='top'><p>";
                    $result .= "Test: " . Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'TestName') . "<br />";
                    $result .= "Description: " . Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'TestDesc') . "</p></td><td valign='top'><p>";
                    if (Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'TestResult') != '') {
                        $result .= "Result: " . Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'TestResult') . "<br />";
                    }
                    $result .= "Units: " . Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'TestUnits') . "</p></td>";
                    $result .= "<td valign='top'><p>Normal values: " . Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'TestNorm') . "<br />";

                    if (Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'TestMedia') != '') {
                        $mId = (int) Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'TestMedia');
                        $mediaElement = DB_ORM::model('map_element', array((int) $mId));
                        if ($mediaElement->mime == 'application/x-shockwave-flash') {
                            $result .= Controller_RenderLabyrinth::getSwfHTML($id);
                        } else {
                            $result .= Controller_RenderLabyrinth::getImageHTML($mId);
                        }
                    }

                    $result .= "</p></td></tr></table>";
                    break;
                case 'DifferentialDiagnostic':
                    $result .= "<table width='100%' border='1' cellspacing='0' cellpadding='6' RULES='NONE' FRAME='BOX'><tr><td align='left' valign='top'><p><strong>Differential Diagnosis</strong></p></td><td align='left' valign='top'><p>";
                    $result .= "Diagnosis: " . Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'DiagTitle') . "<br />Description: " . Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'DiagDesc') . "<br />";
                    $result .= "Likelihood: " . Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'Likelihood') . "</p></td>";
                    $result .= "</tr></table>";
                    break;
                case 'Intervention':
                    $result .= "<table width='100%' border='1' cellspacing='0' cellpadding='6' RULES='NONE' FRAME='BOX'><tr><td align='left' valign='top'><p><strong>Intervention</strong></p></td><td align='left' valign='top'><p>";

                    $result .= "Intervention: " . Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'IntervTitle') . "<br />";
                    if (Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'IntervDesc') != '') {
                        $result .= "Description: " . Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'IntervDesc') . "</p></td><td valign='top'><p>";
                    }
                    if (Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'iMedicTitle') != '') {
                        $result .= "Medication: " . Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'iMedicTitle') . "<br />";
                    }
                    if (Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'iMedicDose') != '') {
                        $result .= "Dose: " . Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'iMedicDose') . "<br />";
                    }
                    if (Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'iMedicRoute') != '') {
                        $result .= "Route: " . Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'iMedicRoute') . "<br />";
                    }
                    if (Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'iMedicFreq') != '') {
                        $result .= "Frequency: " . Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'iMedicFreq') . "<br />";
                    }
                    if (Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'iMedicSource') != '') {
                        $result .= "Source: " . Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'iMedicSource') . "<br />";
                    }
                    if (Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'iMedicSourceID') != '') {
                        $result .= "Source ID: " . Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'iMedicSourceID') . "</p></td><td valign='top'><p>";
                    }
                    if (Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'Appropriateness') != '') {
                        $result .= "Appropriateness: " . Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'Appropriateness') . "<br />";
                    }
                    if (Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'ResultTitle') != '') {
                        $result .= "Results: " . Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'ResultTitle') . " - " . Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'ResultDesc') . "<br />";
                    }

                    if (Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'iTestMedia') != '') {
                        $mId = (int) Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'iTestMedia');
                        $mediaElement = DB_ORM::model('map_element', array((int) $mId));
                        if ($mediaElement->mime == 'application/x-shockwave-flash') {
                            $result .= Controller_RenderLabyrinth::getSwfHTML($id);
                        } else {
                            $result .= self::getImageHTML($mId);
                        }
                    }

                    $result .= "</p></td></tr></table>";
                    break;
            }
        }

        return $result;
    }

    public static function getDamHTML($id)
    {
        $dam = DB_ORM::model('map_dam', array((int) $id));
        $result = '';

        if ($dam != NULL  AND count($dam->elements) > 0)
        {
            foreach ($dam->elements as $damElement)
            {
                switch ($damElement->element_type)
                {
                    case 'vpd':
                        $result .= '[[VPD:'.$damElement->element_id.']]';
                        break;
                    case 'dam':
                        $result .= '[[DAM:'.$damElement->element_id.']]';
                        break;
                    case 'mr':
                        $result .= '[[MR:'.$damElement->element_id.']]';
                        break;
                }
                $result = Controller_RenderLabyrinth::parseText($result);
            }
        }
        return $result;
    }

    private static function getVisualDisplayHTML($visualDisplayId) {
        $visualDisplay = DB_ORM::model('map_visualdisplay', array((int) $visualDisplayId));
        $result = '';

        $traceCountersValues = Session::instance()->get('traceCountersValues');

        if($visualDisplay != null)
        {
            $result .= '<div class="visual-display-container" style="position:relative; display:block; height: 100%; width: 100%;">';

            if($visualDisplay->panels != null && count($visualDisplay->panels) > 0) {
                foreach($visualDisplay->panels as $panel) {
                    $result .= '<div style="position: absolute; top: '.$panel->y.'px;
                                                        left: ' . $panel->x . 'px;
                                                     z-index: ' . $panel->z_index . ';
                                            background-color: ' . $panel->background_color . ';
                                                       width: ' . $panel->width . 'px;
                                                      height: ' . $panel->height . 'px;
                                                border-width: ' . $panel->border_size . 'px;
                                                border-style: solid;
                                                border-color: ' . $panel->border_color . ';
                                               border-radius: ' . $panel->border_radius . 'px;
                                       -webkit-border-radius: ' . $panel->border_radius . 'px;
                                          -moz-border-radius: ' . $panel->border_radius . 'px;
                                              -moz-transform: rotate(' . $panel->angle . 'deg);
                                           -webkit-transform: rotate(' . $panel->angle . 'deg);
                                                -o-transform: rotate(' . $panel->angle . 'deg);
                                               -ms-transform: rotate(' . $panel->angle . 'deg);
                                                   transform: rotate(' . $panel->angle . 'deg);">
                                </div>';
                }
            }

            if($visualDisplay->images != null && count($visualDisplay->images) > 0) {
                foreach($visualDisplay->images as $image) {
                    if(!file_exists(DOCROOT . '/files/' . $visualDisplay->map_id . '/vdImages/' . $image->name)) continue;

                    $result .= '<div style="position: absolute;
                                                 top: ' . $image->y . 'px;
                                                left: ' . $image->x . 'px;
                                               width: ' . $image->width . 'px;
                                              height: ' . $image->height . 'px;
                                             z-index: ' . $image->z_index . ';
                                      -moz-transform: rotate(' . $image->angle . 'deg);
                                   -webkit-transform: rotate(' . $image->angle . 'deg);
                                        -o-transform: rotate(' . $image->angle . 'deg);
                                       -ms-transform: rotate(' . $image->angle . 'deg);
                                           transform: rotate(' . $image->angle . 'deg);
                                ">
                                    <img style="width: 100%" src="' . URL::base() . 'files/' . $visualDisplay->map_id . '/vdImages/' . $image->name . '" />
                                </div>';
                }
            }

            if($visualDisplay->counters != null && count($visualDisplay->counters) > 0) {
                foreach($visualDisplay->counters as $counter) {
                    $thisCounter = $counter->counter->start_value;

                    if($traceCountersValues != null) {
                        $s = strpos($traceCountersValues, '[CID=' . $counter->counter_id . ',') + 1;
                        $tmp = substr($traceCountersValues, $s, strlen($traceCountersValues));
                        $e = strpos($tmp, ']') + 1;
                        $tmp = substr($tmp, 0, $e - 1);
                        $tmp = str_replace('CID=' . $counter->counter_id . ',V=', '', $tmp);
                        $thisCounter = $tmp;
                    }

                    $labelFont = explode('%#%', $counter->label_font_style);
                    $valueFont = explode('%#%', $counter->value_font_style);

                    $result .= '<div style="position: absolute;
                                                 top: ' . $counter->label_y . 'px;
                                                left: ' . $counter->label_x . 'px;
                                             z-index: ' . $counter->label_z_index . ';
                                      -moz-transform: rotate(' . $counter->label_angle . 'deg);
                                   -webkit-transform: rotate(' . $counter->label_angle . 'deg);
                                        -o-transform: rotate(' . $counter->label_angle . 'deg);
                                       -ms-transform: rotate(' . $counter->label_angle . 'deg);
                                           transform: rotate(' . $counter->label_angle . 'deg);
                                         font-family: '.$labelFont[0].';
                                           font-size: ' . $labelFont[1] . 'px;
                                         font-weight: ' . $labelFont[2] . ';
                                               color: ' . $labelFont[3] . ';
                                          font-style: ' . $labelFont[4] . ';
                                     text-decoration: ' . $labelFont[5] . ';
                                ">' . $counter->label_text . '</div>
                                <div style="position: absolute;
                                                 top: ' . $counter->value_y . 'px;
                                                left: ' . $counter->value_x . 'px;
                                             z-index: ' . $counter->value_z_index . ';
                                      -moz-transform: rotate(' . $counter->value_angle . 'deg);
                                   -webkit-transform: rotate(' . $counter->value_angle . 'deg);
                                        -o-transform: rotate(' . $counter->value_angle . 'deg);
                                       -ms-transform: rotate(' . $counter->value_angle . 'deg);
                                           transform: rotate(' . $counter->value_angle . 'deg);
                                         font-family: ' .  $valueFont[0] . ';
                                           font-size: ' . $valueFont[1] . 'px;
                                         font-weight: ' . $valueFont[2] . ';
                                               color: ' . $valueFont[3] . ';
                                          font-style: ' . $valueFont[4] . ';
                                     text-decoration: ' . $valueFont[5] . ';
                                ">'.$thisCounter.'</div>';
                }
            }
            $result .= '</div>';
        }

        return $result;
    }

    private static function getCounterHTML($mapId, $id){
        $counterValue = Model::factory('labyrinth')->getCounterValueByID($mapId, $id);
        return $counterValue;
    }

    private static function getValueByElementKey($elements, $name) {
        if (count($elements) > 0) {
            foreach ($elements as $element) {
                if ($element->key == $name) {
                    return $element->value;
                }
            }
        }

        return '';
    }

    private static function getUserBroswer() {
        if (stristr($_SERVER['HTTP_USER_AGENT'], 'Firefox'))
            return 'firefox';
        elseif (stristr($_SERVER['HTTP_USER_AGENT'], 'Chrome'))
            return 'chrome';
        elseif (stristr($_SERVER['HTTP_USER_AGENT'], 'Safari'))
            return 'safari';
        elseif (stristr($_SERVER['HTTP_USER_AGENT'], 'Opera'))
            return 'opera';
        elseif (stristr($_SERVER['HTTP_USER_AGENT'], 'MSIE 6.0'))
            return 'ie6';
        elseif (stristr($_SERVER['HTTP_USER_AGENT'], 'MSIE 7.0'))
            return 'ie7';
        elseif (stristr($_SERVER['HTTP_USER_AGENT'], 'MSIE 8.0'))
            return 'ie8';
    }

    private function generateReviewLinks($traces)
    {
        $result = '';
        if (count($traces)) {
            $result = '<ul class="links navigation">';
            foreach ($traces as $trace) {
                $result .= '<li><a href='.URL::base().'renderLabyrinth/review/'.$trace->node->map_id.'/'.$trace->node->id.'>'.$trace->node->title.'</a></li>';
            }
            $result .= '</ul>';

        }
        return $result;
    }


    private function prepareUndoLinks ($sessionId, $mapId, $id_node = FALSE)
    {
        $undo   = DB_ORM::model('Map_Node', array($id_node))->undo;
        $traces = DB_ORM::model('user_sessiontrace')->getUniqueTraceBySessions(array($sessionId), $id_node, $undo);
        $nodes  = array();
        $html   = '';

        $current_node   = ($id_node) ? $id_node : Arr::get($traces[count($traces)-1], 'node_id', 0);
        $id_section     = DB_ORM::model('Map_Node_Section_Node')->getIdSection($current_node);

        if (count($traces) > 0)
        {
            $html .= '<ul class="links navigation">';
            foreach ($traces as $trace)
            {
                $id_node            = $trace['node_id'];

                if (($id_section AND $id_section != DB_ORM::model('Map_Node_Section_Node')->getIdSection($id_node)) OR
                    DB_ORM::model('Map_Node', array($id_node))->undo == 0) continue;


                $nodes[$id_node]    = $id_node;
                $trace['node_name'] = DB_ORM::model('map_node')->getNodeName($id_node);

                $html .= '<li class="undo">';
                $html .= $trace['node_name'];
                $html .= '<a href='.URL::base().'renderLabyrinth/undo/'.$mapId.'/'.$id_node.'> [undo]</a>';
                $html .= '</li>';
            }
            $html .= '</ul>';
        }
        return array($html, $nodes);
    }

    /**
     * @param $idMap
     * @return bool - compatibility user type to map type
     */
    // this method is cloned to collectionManager.php
    public function checkTypeCompatibility ($idMap)
    {
        $logged         = Auth::instance()->logged_in();
        $userType       = false;
        $idUser         = false;
        $map            = DB_ORM::model('Map', array($idMap));
        $labyrinthType  = $map->security_id;
        $idScenario     = false;
        $assignUser     = false;
        if ($logged) {
            $user       = Auth::instance()->get_user();
            $userType   = $user->type_id;
            $idUser     = $user->id;
            $idScenario = Session::instance()->get('webinarId')
                ? Session::instance()->get('webinarId')
                : DB_ORM::model('User_Session', array(Session::instance()->get('session_id')))->webinar_id;
            $assignUser = DB_ORM::model('Map_User')->assignOrNot($idMap, $idUser);
        }

        // save in the session, scenario or not
        Session::instance()->set('idScenario', $idScenario);

        // first check by author_id, second check for author right
        $owner = $map->author_id == $idUser;
        if ( ! $owner AND $userType == 2) $owner = (bool) DB_ORM::select('Map_User')->where('user_id', '=', $idUser)->where('map_id', '=', $idMap)->query()->as_array();

        switch ($userType)
        {
            case '1':
                if ($assignUser OR
                    ($labyrinthType == 1) OR
                    ($labyrinthType == 2 AND $idScenario) OR
                    ($labyrinthType == 3 AND ($owner OR $idScenario))) return true;
                return false;
            case '2':
            case '3':
            case '6':
                if ($assignUser OR
                    ($labyrinthType == 1) OR
                    ($labyrinthType == 2) OR
                    ($labyrinthType == 3 AND ($owner OR $idScenario))) return true;
                return false;
            case '4':
                return true;
            default:
                return ($labyrinthType == 1);
        }
    }

    public function action_savePoll()
    {
        $onNode = $this->request->param('id');
        $toNode = $this->request->param('id2');

        DB_ORM::model('Webinar_Poll')->savePoll($onNode, $toNode);
        exit;
    }

    public function action_getNodeIdByPoll()
    {
        $onNode = $this->request->param('id');
        $range  = $this->request->param('id2');
        $nodeId = DB_ORM::model('Webinar_Poll')->getNodeIdByPoll($onNode, $range);
        exit ($nodeId);
    }

    public function action_ajaxTextQuestionSave()
    {
        $response   = $this->request->post('response');
        $questionId = $this->request->post('questionId');
        $nodeId     = $this->request->post('nodeId');
        $dbId       = $this->request->post('dbId');
        $sessionId  = Session::instance()->get('session_id');

        if ($dbId) DB_ORM::model('User_Response')->updateById($dbId, $response);
        else $dbId = DB_ORM::model('User_Response')->createResponse($sessionId, $questionId, $response, $nodeId);

        echo $dbId;
        exit;
    }
}

