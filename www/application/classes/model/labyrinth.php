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

class Model_Labyrinth extends Model {

    public function execute($nodeId, $bookmark = NULL, $isRoot = false) {
        $result = array();

        $result['userId'] = 0;
        if (Auth::instance()->logged_in()) {
            $result['userId'] = Auth::instance()->get_user()->id;
        }
        $node = DB_ORM::model('map_node', array((int) $nodeId));

        if ($node) {
            $result['node'] = $node;
            $result['map'] = DB_ORM::model('map', array((int) $node->map_id));
            if ($node->kfp) {
                $matches = $this->getMatch($nodeId);
            }

            $result['editor'] = FALSE;
            if ($this->checkUser($node->map_id)) {
                $result['editor'] = TRUE;
            }

            $result['node_title'] = $node->title;
            $result['node_text'] = $node->text;
				
            $sessionId = NULL;
            if($bookmark != NULL) {
                $b = DB_ORM::model('user_bookmark', array((int)$bookmark));
                $sessionId = $b->session_id;
                Session::instance()->set('session_id', $sessionId);
                setcookie('OL', $sessionId);
            } else if ($isRoot) {
                $sessionId = DB_ORM::model('user_session')->createSession($result['userId'], $node->map_id, time(), getenv('REMOTE_ADDR'));
                Session::instance()->set('session_id', $sessionId);
                setcookie('OL', $sessionId);
            } else {
                $sessionId = Session::instance()->get('session_id', NULL);
                if ($sessionId == NULL && isset($_COOKIE['OL'])) {
                    $sessionId = $_COOKIE['OL'];
                } else {
                    if ($sessionId == NULL){
                        $sessionId = 'notExist';
                    }
                }
            }

            $conditional = $this->conditional($sessionId, $node);
            $result['previewNodeId'] = DB_ORM::model('user_sessionTrace')->getTopTraceBySessionId($sessionId);
            $result['node_links'] = $this->generateLinks($result['node']);
            $result['sections'] = DB_ORM::model('map_node_section')->getSectionsByMapId($node->map_id);
            if($conditional == null) {
                if ($sessionId != 'notExist'){
                    $traceId = DB_ORM::model('user_sessionTrace')->createTrace($sessionId, $result['userId'], $node->map_id, $node->id);
                } else {
                    $traceId = 'notExist';
                }

                if (substr($result['node_text'], 0, 3) != '<p>') {
                    $result['node_text'] = '<p>' . $result['node_text'] . '</p>';
                }

                $c = $this->counters($traceId, $sessionId, $node, $isRoot);
                if ($c != NULL) {
                    $result['counters'] = $c['counterString'];
                    $result['redirect'] = $c['redirect'];
                    $result['remoteCounters'] = $c['remote'];
                } else {
                    $result['counters'] = '';
                    $result['redirect'] = NULL;
                    $result['remoteCounters'] = '';
                }
            } else {
                $result['node_text'] = '<p>' . $conditional['message'] . '</p>';
                $result['node_links']['linker'] = $conditional['linker'];
                $result['counters'] = '';
                $result['redirect'] = NULL;
                $result['remoteCounters'] = '';
            }

            $result['traces'] = $this->getReviewLinks($sessionId);
            $result['sessionId'] = $sessionId;
        }

        return $result;
    }

    private function checkUser($mapId) {
        if (Auth::instance()->logged_in()) {
            if (DB_ORM::model('map_user')->checkUserById($mapId, Auth::instance()->get_user()->id)) {
                return TRUE;
            }

            $map = DB_ORM::model('map', array((int) $mapId));
            if ($map) {
                if ($map->author_id == Auth::instance()->get_user()->id) {
                    return TRUE;
                }
            }

            return FALSE;
        }

        return FALSE;
    }

    private function getMatch($nodeId) {
        return NULL;
    }

    private function generateLinks($node) {
        if (count($node->links) > 0) {
            $result = array();
            foreach ($node->links as $link) {
                switch ($node->link_type->name) {
                    case 'ordered':
                        $order = $link->order * 10000;
                        if (isset($result[$order])) {
                            $nextIndex = $this->findNextIndex($result, $order + 1);
                            $result[$nextIndex] = $link;
                        } else {
                            $result[$order] = $link;
                        }
                        break;
                    case 'random order':
                        $randomIndex = rand(0, 100000);
                        if (isset($result[$randomIndex])) {
                            $nextIndex = $this->findNextIndex($result, $randomIndex + 1);
                            $result[$nextIndex] = $link;
                        } else {
                            $result[$randomIndex] = $link;
                        }
                        break;
                    case 'random select one *':
                        $randomIndex = rand(0, 100000) * ($link->probability == 0 ? 1 : $link->probability);
                        if (isset($result[$randomIndex])) {
                            $nextIndex = $this->findNextIndex($result, $randomIndex + 1);
                            $result[$nextIndex] = $link;
                        } else {
                            $result[$randomIndex] = $link;
                        }
                        break;
                    default:
                        $result[] = $link;
                        break;
                }
            }

            if ($node->link_type_id == 3){
                if (count($result) > 0){
                    $resultRandomOne = array();
                    $keys = array_keys($result);
                    rsort($keys);
                    $resultRandomOne[0] = $result[$keys[0]];
                    $result = $resultRandomOne;
                }
            }

            return $this->clearArray($result);
        }

        return NULL;
    }

    private function findNextIndex($result, $index){
        if (isset($result[$index])){
            $nextIndex = $this->findNextIndex($result, $index + 1);
        }else{
            $nextIndex = $index;
        }
        return $nextIndex;
    }

    private function clearArray($array) {
        if (count($array) > 0) {
            $result = array();
            $array_keys = array_keys($array);
            sort($array_keys);
            foreach($array_keys as $key){
                $result[] = $array[$key];
            }
            return $result;
        }
        return NULL;
    }

    private function conditional($sessionId, $node) {
        if ($node != NULL and $node->conditional != '') {
            $mode = 'o';
            if (strstr($node->conditional, 'and')) {
                $mode = 'a';
            }

            $nodes = array();
            $conditional = $node->conditional;
            while (strlen($conditional) > 0) {
                if ($conditional[0] == '[') {
                    for ($i = 1; $i < strlen($conditional); $i++) {
                        if ($conditional[$i] == ']') {
                            $id = substr($conditional, 1, $i - 1);
                            if (is_numeric($id)) {
                                $nodes[] = (int) $id;
                            }
                            break;
                        }
                    }
                }

                $conditional = substr($conditional, 1, strlen($conditional));
            }

            $count = DB_ORM::model('user_sessionTrace')->getCountTracks($sessionId, $nodes);

            $message = '<p>Sorry but you haven\'t yet explored all the required options ...</p>';
            if ($node->conditional_message != '') {
                $message = $node->conditional_message;
            }

            if ($mode == 'a') {
                if ($count < count($nodes)) {
                    return array('message' => $message, 'linker' => '<p><a href="javascript:history.back()">&laquo; back</a></p>');
                }
            } else if ($mode == 'o') {
                if ($count <= 0) {
                    return array('message' => $message, 'linker' => '<p><a href="javascript:history.back()">&laquo; back</a></p>');
                }
            }
        }

        return NULL;
    }

    private function counters($traceId, $sessionId, $node, $isRoot = false) {
        if ($traceId != null && $node != NULL) {
            $counters = DB_ORM::model('map_counter')->getCountersByMap($node->map_id);
            if (count($counters) > 0) {
                $countersArray = array();
                $updateCounter = '';
                $oldCounter = '';
                $counterString = '';
                $remoteCounterString = '';
                $rootNode = DB_ORM::model('map_node')->getRootNodeByMap($node->map_id);
                $redirect = NULL;

                $questionChoices = Session::instance()->get('questionChoices');
                $questionChoices = ($questionChoices != NULL) ? json_decode($questionChoices, true) : NULL;

                $sliderQuestionChoices = Session::instance()->get('sliderQuestionResponses');
                Session::instance()->set('sliderQuestionResponses', array());

                foreach ($counters as $counter) {
                    $countersArray[$counter->id]['counter'] = $counter;
                    $currentCountersState = '';
                    if ($rootNode != NULL) {
                        $currentCountersState = DB_ORM::model('user_sessionTrace')->getCounterByIDs($sessionId, $rootNode->map_id, $rootNode->id);
                        $oldCounter = $currentCountersState;
                    }
					
                    $label = $counter->name;
                    if ($counter->icon_id != 0) {
                        $label = '<img src="' . URL::base() . $counter->icon->path . '">';
                    }
                    $countersArray[$counter->id]['label'] = $label;

                    $thisCounter = null;
                    if ($isRoot) {
                        $thisCounter = $counter->start_value;
                    } elseif ($currentCountersState != '') {
                        $s = strpos($currentCountersState, '[CID=' . $counter->id . ',') + 1;
                        $tmp = substr($currentCountersState, $s, strlen($currentCountersState));
                        $e = strpos($tmp, ']') + 1;
                        $tmp = substr($tmp, 0, $e - 1);
                        $tmp = str_replace('CID=' . $counter->id . ',V=', '', $tmp);
                        $thisCounter = $tmp;
                    }

                    $counterFunction = '';
                    $apperOnNode = 1;
                    if (count($node->counters) > 0) {
                        foreach ($node->counters as $nodeCounter) {
                            if ($counter->id == $nodeCounter->counter->id) {
                                $counterFunction = $nodeCounter->function;
                                $apperOnNode = $nodeCounter->display;
                                break;
                            }
                        }
                    }

                    if ($counterFunction != '') {
                        if ($counterFunction[0] == '=') {
                            $thisCounter = substr($counterFunction, 1, strlen($counterFunction));
                        } else if ($counterFunction[0] == '-') {
                            $thisCounter -= substr($counterFunction, 1, strlen($counterFunction));
                        } else if ($counterFunction[0] == '+') {
                            $thisCounter += substr($counterFunction, 1, strlen($counterFunction));
                        }
                    }

                    if (($questionChoices != NULL) && (isset($questionChoices['counter_ids']) > 0)){
                        $qID = array();
                        if (count($questionChoices['counter_ids']) > 0){
                            foreach($questionChoices['counter_ids'] as $key => $cID){
                                if ($cID == $counter->id){
                                    $qID[] = $key;
                                }
                            }
                        }

                        if (count($qID) > 0){
                            foreach($qID as $questionID){
                                if (isset($questionChoices[$questionID])){
                                    if (count($questionChoices[$questionID]) > 0){
                                        foreach($questionChoices[$questionID] as $value){
                                            $value = trim($value['score']);
                                            if ($value[0] == '=') {
                                                $thisCounter = substr($value, 1, strlen($value));
                                            } else if ($value[0] == '-') {
                                                $thisCounter -= substr($value, 1, strlen($value));
                                            } else {
                                                $thisCounter += $value;
                                                $value = '+'.$value;
                                            }
                                            $countersArray[$counter->id]['func'][] = $value;
                                        }
                                    }
                                }
                            }
                        }
                    }

                    if($sliderQuestionChoices != null && count($sliderQuestionChoices) > 0 && $node->type_id != 1) {
                        foreach($sliderQuestionChoices as $questionId => $sliderValue) {
                            $question = DB_ORM::model('map_question', array((int)$questionId));
                            if($question != null && count($question->responses) > 0) {
                                foreach($question->responses as $response) {
                                    if($sliderValue >= $response->from && $sliderValue <= $response->to) {
                                        $thisCounter += $response->score;
                                    }
                                }
                            } else {
                                $thisCounter = $sliderValue;
                            }
                        }
                    }

                    $countersArray[$counter->id]['value'] = $thisCounter;
                    if ($counterFunction != ''){
                        $countersArray[$counter->id]['func'][] = $counterFunction;
                    }

                    if (($counter->visible != 0) & ($apperOnNode == 1)) {
                        $countersArray[$counter->id]['visible'] = true;

                    } else {
                        $countersArray[$counter->id]['visible'] = false;
                    }

                    $rules = DB_ORM::model('map_counter_rule')->getRulesByCounterId($counter->id);

                    $redirect = NULL;
                    if ($rules != NULL and count($rules) > 0) {
                        foreach ($rules as $rule) {
                            $resultExp = FALSE;

                            switch ($rule->relation->value) {
                                case 'eq':
                                    if ($thisCounter == $rule->value)
                                        $resultExp = TRUE;
                                    break;
                                case 'neq':
                                    if ($thisCounter != $rule->value)
                                        $resultExp = TRUE;
                                    break;
                                case 'leq':
                                    if ($thisCounter <= $rule->value)
                                        $resultExp = TRUE;
                                    break;
                                case 'lt':
                                    if ($thisCounter < $rule->value)
                                        $resultExp = TRUE;
                                    break;
                                case 'geq':
                                    if ($thisCounter >= $rule->value)
                                        $resultExp = TRUE;
                                    break;
                                case 'gt':
                                    if ($thisCounter > $rule->value)
                                        $resultExp = TRUE;
                                    break;
                            }

                            if ($resultExp == TRUE) {
                                if ($rule->function == 'redir') {
                                    $thisCounter = $this->calculateCounterFunction($thisCounter, $rule->counter_value);
                                    $redirect = $rule->redirect_node_id;
                                }
                            }
                        }
                        if ($redirect != NULL){
                            $countersArray[$counter->id]['redirect'] = $redirect;
                        }
                    }
                }

                if (isset($questionChoices['counter_ids'])){
                    unset($questionChoices['counter_ids']);
                }
                if (count($questionChoices) > 0){
                    foreach($questionChoices as $qID => $questions){
                        if (count($questions) > 0){
                            foreach($questions as $q){
                                DB_ORM::model('user_response')->createResponse($sessionId, $qID, $q['response']);
                            }
                        }
                    }
                }

                Session::instance()->delete('questionChoices');

                $redirect = NULL;
                $commonRules = DB_ORM::model('map_counter_commonrules')->getRulesByMapId($node->map_id);
                if (count($commonRules) > 0){
                    $values = array();
                    foreach($countersArray as $key => $counter){
                        $values[$key] = $counter['value'];
                    }
                    $runtimelogic = new RunTimeLogic();
                    $runtimelogic->values = $values;
                    foreach($commonRules as $rule){
                        $array = $runtimelogic->parsingString($rule->rule);
                        $resultLogic = $array['result'];
                        if (isset($resultLogic['goto'])){
                            if ($resultLogic['goto'] != NULL){
                                if ($redirect == NULL){
                                    $redirect = $resultLogic['goto'];
                                }
                            }
                        }
                        if (isset($resultLogic['counters'])){
                            if (count($resultLogic['counters']) > 0){
                                foreach($resultLogic['counters'] as $key => $c){
                                    $previousValue = $countersArray[$key]['value'];
                                    $funcStr = $c - $previousValue;
                                    $countersArray[$key]['func'][] = $funcStr;
                                    $countersArray[$key]['value'] = $c;
                                }
                            }
                        }
                    }
                }
                $counterString .="<ul>";
                foreach($countersArray as $key => $counter){
                    if (isset($counter['func'])){
                        if (count($counter['func']) > 0) {
                            $func = '<sup>[' . implode(', ', $counter['func']) . ']</sup>';
                        } else {
                            $func = '<sup>[no]</sup>';
                        }
                    } else {
                        $func = '<sup>[no]</sup>';
                    }


                    if ($counter['visible']){
                        $popup = '<a href="javascript:void(0)" onclick=\'window.open("' . URL::base() . 'renderLabyrinth/", "Counter", "toolbar=no, directories=no, location=no, status=no, menubar=no, resizable=yes, scrollbars=yes, width=400, height=350"); return false;\'>';
                        $counterString .= '<li><i class="icon-cardio"></i>' . $popup . $counter['label'] . '</a>(' . $counter['value'] . ') ' . $func . '</li>';
                        $remoteCounterString .= '<counter id="'.$counter['counter']->id.'" name="'.$counter['counter']->name.'" value="'.$counter['value'].'"></counter>';
                    }

                    if (isset($counter['redirect'])){
                        if ($redirect == NULL){
                            $redirect = $counter['redirect'];
                        }
                    }

                    $updateCounter .= '[CID=' . $counter['counter']->id . ',V=' . $counter['value'] . ']';
                }
                $counterString .="</ul>";
                DB_ORM::model('user_sessionTrace')->updateCounter($sessionId, $node->map_id, $node->id, $oldCounter, $traceId);
                DB_ORM::model('user_sessionTrace')->updateCounter($sessionId, $rootNode->map_id, $rootNode->id, $updateCounter);

                if ($redirect != NULL){
                    Request::initial()->redirect(URL::base().'renderLabyrinth/go/'.$node->map_id.'/'.$redirect);
                }

                return array('counterString' => $counterString, 'redirect' => $redirect, 'remote' => $remoteCounterString);
            }

            return '';
        }

        return '';
    }

    private function calculateCounterFunction($counter, $function){
        if ($function[0] == '=') {
            $counter = substr($function, 1, strlen($function));
        } else if ($function[0] == '-') {
            $counter -= substr($function, 1, strlen($function));
        } else if ($function[0] == '+') {
            $counter += substr($function, 1, strlen($function));
        }
        return $counter;
    }

    private function getReviewLinks($sesionId) {
        $traces = DB_ORM::model('user_sessionTrace')->getTraceBySessionID($sesionId);

        if ($traces != NULL) {
            return $traces;
        }

        return NULL;
    }

    public function review($nodeId) {
        $sessionId = Session::instance()->get('session_id', NULL);
        if ($sessionId == NULL && isset($_COOKIE['OL'])) {
            $sessionId = $_COOKIE['OL'];
        }

        if ($sessionId != NULL and $nodeId != NULL) {
            $node = DB_ORM::model('map_node', array((int) $nodeId));
            if ($node) {
                $rootNode = DB_ORM::model('map_node')->getRootNodeByMap((int) $node->map_id);
                $counter = DB_ORM::model('user_sessionTrace')->getCounterByIDs($sessionId, (int) $node->map_id, $node->id);
                DB_ORM::model('user_sessionTrace')->updateCounter($sessionId, $rootNode->map_id, $rootNode->id, $counter);
            }
        }
    }

    public function getChatResponce($sessionId, $mapId, $chatId, $elementId) {
        $chat = DB_ORM::model('map_chat', array((int) $chatId));

        if ($chat) {
            if (count($chat->elements) > 0) {
                foreach ($chat->elements as $element) {
                    if ($element->id == $elementId) {
                        $rootNode = DB_ORM::model('map_node')->getRootNodeByMap((int) $mapId);
                        $counterStr = DB_ORM::model('user_sessionTrace')->getCounterByIDs($sessionId, (int) $rootNode->map_id, $rootNode->id);

                        if ($counterStr != '') {

                            $counters = DB_ORM::model('map_counter')->getCountersByMap($rootNode->map_id);
                            if (count($counters) > 0) {
                                foreach ($counters as $counter) {
                                    $s = strpos($counterStr, '[CID=' . $counter->id . ',') + 1;
                                    $tmp = substr($counterStr, $s, strlen($counterStr));
                                    $e = strpos($tmp, ']') + 1;
                                    $tmp = substr($tmp, 0, $e - 1);
                                    $tmp = str_replace('CID=' . $counter->id . ',V=', '', $tmp);
                                    if (is_numeric($tmp)) {
                                        $thisCounter = $tmp;

                                        if ($chat->counter_id == $counter->id) {
                                            if ($element->function != '') {
                                                $tmpCounter = $thisCounter;
                                                if ($element->function[0] == '=') {
                                                    $tmpCounter = (int) substr($element->function, 1, strlen($element->function));
                                                } else if ($element->function[0] == '-') {
                                                    $tmpCounter -= (int) substr($element->function, 1, strlen($element->function));
                                                } else if ($element->function[0] == '+') {
                                                    $tmpCounter += (int) substr($element->function, 1, strlen($element->function));
                                                }

                                                $counterStr = str_replace('[CID=' . $counter->id . ',V=' . $thisCounter . ']', '[CID=' . $counter->id . ',V=' . $tmpCounter . ']', $counterStr);
                                                DB_ORM::model('user_sessionTrace')->updateCounter($sessionId, $rootNode->map_id, $rootNode->id, $counterStr);
                                                return $element->response;
                                            }
                                        }
                                    }
                                }
                            }
                        } else {
							return $element->response;
						}
                    }
                }
            }
        }

        return '';
    }

    public function question($questionId, $response, $questionStatus) {
        $question = DB_ORM::model('map_question', array((int) $questionId));

        if ($question) {
            $responseObj = null;
            if (($question->type->value != 'text') && ($question->type->value != 'area')) {
                foreach ($question->responses as $resp) {
                    if ($resp->id == $response) {
                        $responseObj = $resp;
                        break;
                    }
                }
            }

            $qChoices = json_decode(Session::instance()->get('questionChoices'), true);

            if (($question->type->value == 'text') || ($question->type->value == 'area')) {
                $qChoices[$questionId] = array();
                $qChoices[$questionId][$response]['response'] = $response;
            }

            if ($question->type->value == 'pcq'){
                $qChoices[$questionId] = array();
                $qChoices[$questionId][$response]['score'] = $responseObj->score;
                $qChoices[$questionId][$response]['response'] = $responseObj->response;
            }

            if ($question->type->value == 'mcq') {
                if ($questionStatus == 1){
                    $qChoices[$questionId][$response]['score'] = $responseObj->score;
                    $qChoices[$questionId][$response]['response'] = $responseObj->response;
                } else {
                    if (isset($qChoices[$questionId][$response])){
                        unset($qChoices[$questionId][$response]);
                    }
                }
            }
            $qChoices['counter_ids'][$questionId] = $question->counter_id;

            Session::instance()->set('questionChoices', json_encode($qChoices));

            if ($question->show_answer) {
                if ($question->type->value != 'text' and $question->type->value != 'area') {
                    $returnStr = '';
                    switch ($responseObj->is_correct){
                        case 0:
                            $returnStr .= '<img src="' . URL::base() . 'images/cross.jpg"> ';
                            break;
                        case 1:
                            $returnStr .= '<img src="' . URL::base() . 'images/tick.jpg"> ';
                            break;
                    }
                    $returnStr .= ($responseObj->feedback != null && strlen($responseObj->feedback) > 0 ? ('(' . $responseObj->feedback . ')') : '');
                    return $returnStr;
                }
            }
        }

        return '';
    }

    public function getMainFeedback($session, $counters, $mapId) {
        $rules = DB_ORM::model('map_feedback_rule')->getRulesByMap($mapId);

        $result = array();
        $map = DB_ORM::model('map', array((int) $mapId));
        if ($map != NULL and $map->feedback != '') {
            $result['general'] = $map->feedback;
        }

        if ($rules != NULL and count($rules) > 0) {
            foreach ($rules as $rule) {
                switch ($rule->type->name) {
                    case 'time taken':
                        if ($map->timing) {
                            $max = $session->start_time;
                            if (count($session->traces) > 0) {
                                foreach ($session->traces as $val) {
                                    if ($val->date_stamp > $max) {
                                        $max = $val->date_stamp;
                                    }
                                }
                            }
                            $delta = $max - $session->start_time;
                            if (Model_Labyrinth::calculateRule($rule->operator->value, $delta, $rule->value)) {
                                $result['timeTaken'][] = $rule->message;
                            }
                        }
                        break;
                    case 'node visit':
                        $r = FALSE;
                        if (count($session->traces) > 0) {
                            foreach ($session->traces as $trace) {
                                if ($trace->node_id == $rule->value) {
                                    $r = TRUE;
                                    break;
                                }
                            }
                        }

                        if ($r) {
                            $result['nodeVisit'][] = $rule->message;
                        }
                        break;
                    case 'must visit':
                        if (count($session->traces) > 0) {
                            $nodesIDs = array();
                            foreach ($session->traces as $trace) {
                                $nodesIDs[] = $trace->node_id;
                            }

                            $count = count(array_unique($nodesIDs));
                            if (Model_Labyrinth::calculateRule($rule->operator->value, $count, $rule->value)) {
                                $result['mustVisit'][] = $rule->message;
                            }
                        }
                        break;
                    case 'must avoid':
                        if (count($session->traces) > 0) {
                            $nodesIDs = array();
                            foreach ($session->traces as $trace) {
                                $nodesIDs[] = $trace->node_id;
                            }

                            $count = count(DB_ORM::model('map_node')->getNodesByMap($mapId)) - count(array_unique($nodesIDs));
                            if (Model_Labyrinth::calculateRule($rule->operator->value, $count, $rule->value)) {
                                $result['mustAvoid'][] = $rule->message;
                            }
                        }
                        break;
                    case 'counter value':
                        if(count($counters) > 0 ) {
                            foreach($counters as $counter) {
                                if($counter[2] == $rule->counter_id) {
                                    if(Model_Labyrinth::calculateRule($rule->operator->value, $counter[1][0], $rule->value)) {
                                        $result['counters'][] = $rule->message;
                                    }
                                }
                            }
                        }
                        break;
                }
            }
        }

        return $result;
    }

    public static function calculateRule($operator, $value1, $value2) {
        switch ($operator) {
            case 'eq':
                if ($value1 == $value2)
                    return TRUE;
                return FALSE;
            case 'neq':
                if ($value1 != $value2)
                    return TRUE;
                return FALSE;
            case 'leq':
                if ($value1 <= $value2)
                    return TRUE;
                return FALSE;
            case 'lt':
                if ($value1 < $value2)
                    return TRUE;
                return FALSE;
            case 'geq':
                if ($value1 >= $value2)
                    return TRUE;
                return FALSE;
            case 'gt':
                if ($value1 > $value2)
                    return TRUE;
                return FALSE;
            default:
                return FALSE;
        }
    }
}

?>