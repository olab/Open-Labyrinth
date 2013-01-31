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
                if ($sessionId == NULL) {
                    $sessionId = $_COOKIE['OL'];
                }
            }

            $result['previewNodeId'] = DB_ORM::model('user_sessionTrace')->getTopTraceBySessionId($sessionId);

            $traceId = DB_ORM::model('user_sessionTrace')->createTrace($sessionId, $result['userId'], $node->map_id, $node->id);
            $result['node_links'] = $this->generateLinks($result['node']);
            $result['sections'] = DB_ORM::model('map_node_section')->getSectionsByMapId($node->map_id);

            $conditional = $this->conditional($sessionId, $node);
            if ($conditional != NULL) {
                $result['node_text'] = $conditional['message'];
                $result['node_links'] = $conditional['linker'];
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
                if ($count >= count($nodes)) {
                    return array('message' => $message, 'linker' => '<p><a href="javascript:history.back()">back</a></p>');
                }
            } else if ($mode == 'o') {
                if ($count >= 1) {
                    return array('message' => $message, 'linker' => '<p><a href="javascript:history.back()">back</a></p>');
                }
            }
        }

        return NULL;
    }

    private function counters($traceId, $sessionId, $node, $isRoot = false) {
        if ($traceId != null && $traceId > 0 && $node != NULL) {
            $counters = DB_ORM::model('map_counter')->getCountersByMap($node->map_id);
            if (count($counters) > 0) {
                $countersArray = array();
                $updateCounter = '';
                $oldCounter = '';
                $counterString = '';
                $remoteCounterString = '';
                $rootNode = DB_ORM::model('map_node')->getRootNodeByMap($node->map_id);
                $redirect = NULL;
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

                    $thisCounter = 0;
                    if ($isRoot) {
                        $thisCounter = $counter->start_value;
                    } elseif ($currentCountersState != '') {
                        $s = strpos($currentCountersState, '[CID=' . $counter->id . ',') + 1;
                        $tmp = substr($currentCountersState, $s, strlen($currentCountersState));
                        $e = strpos($tmp, ']') + 1;
                        $tmp = substr($tmp, 0, $e - 1);
                        $tmp = str_replace('CID=' . $counter->id . ',V=', '', $tmp);
                        if (is_numeric($tmp)) {
                            $thisCounter = $tmp;
                        }
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
                        $counterString .= '<p>' . $popup . $counter['label'] . '</a>(' . $counter['value'] . ') ' . $func . '</p>';
                        $remoteCounterString .= '<counter id="'.$counter['counter']->id.'" name="'.$counter['counter']->name.'" value="'.$counter['value'].'"></counter>';
                    }

                    if (isset($counter['redirect'])){
                        if ($redirect == NULL){
                            $redirect = $counter['redirect'];
                        }
                    }

                    $updateCounter .= '[CID=' . $counter['counter']->id . ',V=' . $counter['value'] . ']';
                }

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
        if ($sessionId == NULL) {
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

    public function question($sessionId, $questionId, $response) {
        $question = DB_ORM::model('map_question', array((int) $questionId));

        if ($question) {
            $r = $response;
            $qResp = NULL;
            if ($question->type->value != 'text' and $question->type->value != 'area') {
                if ($question->counter_id > 0) {
                    $rootNode = DB_ORM::model('map_node')->getRootNodeByMap((int) $question->map_id);
                    $currentCountersState = DB_ORM::model('user_sessionTrace')->getCounterByIDs($sessionId, $rootNode->map_id, $rootNode->id);
                    if ($currentCountersState != '') {
                        $s = strpos($currentCountersState, '[CID=' . $question->counter_id . ',') + 1;
                        $tmp = substr($currentCountersState, $s, strlen($currentCountersState));
                        $e = strpos($tmp, ']') + 1;
                        $tmp = substr($tmp, 0, $e - 1);
                        $tmp = str_replace('CID=' . $question->counter_id . ',V=', '', $tmp);
                        if (is_numeric($tmp)) {
                            $thisCounter = $tmp;
                            if (count($question->responses) > 0) {
                                foreach ($question->responses as $resp) {
                                    if ($resp->id == $r) {
                                        $r = $resp->response;
                                        $newValue = $thisCounter;
                                        $newValue += $resp->score;

                                        $newCountersState = str_replace('[CID=' . $question->counter_id . ',V=' . $thisCounter . ']', '[CID=' . $question->counter_id . ',V=' . $newValue . ']', $currentCountersState);
                                        $qResp = $resp;
                                        DB_ORM::model('user_sessionTrace')->updateCounter($sessionId, $rootNode->map_id, $rootNode->id, $newCountersState);
                                        break;
                                    }
                                }
                            }
                        }
                    }
                }
            }
			
			if ($qResp == NULL) {
				if (count($question->responses) > 0) {
					foreach ($question->responses as $resp) {
                        if ($resp->id == $r) {
							$qResp = $resp;
							$r = $resp->response;
						}
					}
				}
			}
			
            DB_ORM::model('user_response')->updateResponse($sessionId, $questionId, $r);

            if ($question->show_answer) {
                if ($question->type->value != 'text' and $question->type->value != 'area') {
                    if ($qResp->is_correct) {
                        return '<p><img src="' . URL::base() . 'images/tick.jpg"> correct (' . $qResp->feedback . ')</p>';
                    } else {
                        return '<p><img src="' . URL::base() . 'images/cross.jpg"> incorrect (' . $qResp->feedback . ')</p>';
                    }
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


//RUN TIME LOGIC
//-------------------------------------------------
class RunTimeLogic {
    var $values = array();

    public function parsingString($string){
        $posIF = 0;
        $posTHEN = 0;
        $str = '';
        $dataArray = array();
        $errors = array();
        $posIF = strpos($string, 'IF');

        if ($posIF !== false){
            $posTHEN = strpos($string, 'THEN');
            if ($posTHEN !== false){
                $str = substr($string, $posIF + 3, ($posTHEN - $posIF - 4));

                $str = $this->findParentheses($str);
                $ifTrue = $this->parseStringForLogicOperaiotns($str);

                if ($ifTrue){
                    $posELSE = strpos($string, 'ELSE');
                    if ($posELSE !== false){
                        $then = trim(substr($string, $posTHEN + 5, ($posELSE - $posTHEN - 5)));
                    }else{
                        $then = trim(substr($string, $posTHEN + 5, strlen($string) - $posTHEN));
                    }
                    $actionArray = explode(',', $then);
                    if (count($actionArray) > 0){
                        foreach($actionArray as $action){
                            $then = $this->parseAction($action);
                            switch($then['action']){
                                case 'goto':
                                    if ($then['id'] != null){
                                        $dataArray['goto'] = $then['id'];
                                    } else {
                                        $errors[] = 'Tag [[NODE:<em>node_id</em>]] is missed';
                                    }
                                    break;
                                case 'no-entry':
                                    $dataArray['no-entry'] = true;
                                    break;
                                case 'operation':
                                    $dataArray['counters'][$then['id']] = $then['result'];
                                    break;
                                default:
                                    $errors[] = 'THEN missed condition';
                            }
                        }
                    }
                }else{
                    $posELSEIF = strpos($string, 'ELSEIF');
                    $posELSE = strpos($string, 'ELSE');
                    if ($posELSEIF !== false){
                        $newString = substr($string, $posELSEIF + 4, strlen($string) - $posELSEIF);
                        $newDataArray = $this->parsingString($newString);
                        $dataArray = $newDataArray['result'];
                        $errors = array_merge($errors, $newDataArray['errors']);
                    } elseif($posELSE !== false){
                        $else = trim(substr($string, $posELSE + 5, strlen($string) - $posELSE));
                        $actionArray = explode(',', $else);
                        if (count($actionArray) > 0){
                            foreach($actionArray as $action){
                                $else = $this->parseAction($action);
                                switch($else['action']){
                                    case 'goto':
                                        if ($else['id'] != null){
                                            $dataArray['goto'] = $else['id'];
                                        } else {
                                            $errors[] = 'Tag [[NODE:<em>node_id</em>]] is missed';
                                        }
                                        break;
                                    case 'no-entry':
                                        $dataArray['no-entry'] = true;
                                        break;
                                    case 'operation':
                                        $dataArray['counters'][$else['id']] = $else['result'];
                                        break;
                                    default:
                                        $errors[] = 'THEN missed condition';
                                }
                            }
                        }
                    } else {
                        $dataArray['nothing'] = true;
                    }
                }
            }else{
                $errors[] = 'THEN not found';
            }
        }else{
            $errors[] = 'IF not found';
        }
        $array['result'] = $dataArray;
        $array['errors'] = $errors;
        return $array;
    }

    public function findParentheses($str){
        $posParentheses = strrpos($str, '(');
        if ($posParentheses !== false){
            $posParenthesesEND = strpos($str, ')');
            if ($posParenthesesEND !== false){
                $parenthesesStr = substr($str, $posParentheses + 1, ($posParenthesesEND - $posParentheses - 1));
                $significantOperations = array(' AND ', ' OR ');
                $find = false;
                foreach($significantOperations as $op){
                    $posOP = strpos($parenthesesStr, $op);
                    if ($posOP !== false){
                        $find = true;
                        break;
                    }
                }
                if ($find){
                    $ifTrue = $this->parseStringForLogicOperaiotns(trim($parenthesesStr));
                    if ($ifTrue){
                        $ifTrue = 'CR_TRUE';
                    }else{
                        $ifTrue = 'CR_FALSE';
                    }
                    $str = str_replace('('.$parenthesesStr.')', $ifTrue, $str);
                    $str = $this->findParentheses($str);
                }
            }
        }
        return $str;
    }

    public function parseStringForLogicOperaiotns($str){
        $dataArray = array();
        $ifTrue = false;
        $arrayOR = explode(' OR ', $str);
        if (count($arrayOR) >= 1){
            $i = 0;
            foreach($arrayOR as $or){
                $arrayAND = explode(' AND ', $or);
                if (count($arrayAND) > 1){
                    $ands = array();
                    foreach($arrayAND as $and){
                        $ands[] = $this->parseValueExpression($and);
                    }
                    $dataArray['or'][]['and'] = $ands;
                }else{
                    $dataArray['or'][] = $this->parseValueExpression($or);
                }
                $i++;
            }

            if (count($dataArray['or']) > 0){
                foreach($dataArray['or'] as $key => $find){
                    if (isset($find['and'])){
                        if (count($find['and']) > 0){
                            $dataArray['or'][$key]['result'] = 1;
                            foreach($find['and'] as $and){
                                if ($and['result'] != 1){
                                    $dataArray['or'][$key]['result'] = 0;
                                    break;
                                }
                            }
                        }
                    }
                }
            }

            if (count($dataArray['or']) > 0){
                foreach($dataArray['or'] as $or){
                    if ($or['result'] == 1){
                        $ifTrue = true;
                        break;
                    }
                }
            }
        }
        return $ifTrue;
    }

    public function parseValueExpression($str){
        $array = array();
        $result = null;

        $posMATCH = strpos($str, 'MATCH');
        $posUPPER = strpos($str, 'UPPER');
        $posLOWER = strpos($str, 'LOWER');
        $posPROPER = strpos($str, 'PROPER');
        $posCRTRUE = strpos($str, 'CR_TRUE');
        $posCRFALSE = strpos($str, 'CR_FALSE');
        if ($posCRTRUE !== false){
            $array['result'] = 1;
        } elseif ($posCRFALSE !== false){
            $array['result'] = 0;
        } elseif ($posMATCH !== false){
            $id = $this->getId($str);
            $string = $this->getStringValue($str);

            $match = strpos($this->values[$id], $string);
            if ($match !== false){
                $array['result'] = 1;
            }else{
                $array['result'] = 0;
            }
        } elseif ($posUPPER !== false){
            $id = $this->getId($str);
            $string = $this->getStringValue($str);

            $upper = strcmp(strtoupper($this->values[$id]), $string);
            $posExp = strpos($str, '!=');
            if ($posExp !== false){
                if ($upper == 0){
                    $upper = 1;
                } else {
                    $upper = 0;
                }
            }

            if ($upper == 0){
                $array['result'] = 1;
            } else {
                $array['result'] = 0;
            }
        } elseif ($posLOWER !== false){
            $id = $this->getId($str);
            $string = $this->getStringValue($str);

            $upper = strcmp(strtolower($this->values[$id]), $string);
            $posExp = strpos($str, '!=');
            if ($posExp !== false){
                if ($upper == 0){
                    $upper = 1;
                } else {
                    $upper = 0;
                }
            }

            if ($upper == 0){
                $array['result'] = 1;
            } else {
                $array['result'] = 0;
            }
        } elseif ($posPROPER !== false){
            $id = $this->getId($str);
            $string = $this->getStringValue($str);

            $proper = strcmp(ucfirst($this->values[$id]), $string);
            $posExp = strpos($str, '!=');
            if ($posExp !== false){
                if ($proper == 0){
                    $proper = 1;
                } else {
                    $proper = 0;
                }
            }

            if ($proper == 0){
                $array['result'] = 1;
            } else {
                $array['result'] = 0;
            }
        } else {
            $countOperations = $this->findAllOperations($str) + 1;
            $countCR = substr_count($str, "[[CR:");
            for($i = 0; $i < $countCR; $i++){
                $posCR = strpos($str, '[[CR:');
                $posCRClose = strpos($str, ']]');
                $id = substr($str, $posCR + 5, ($posCRClose - $posCR - 5));
                $result = $this->algorithmicOperation(substr($str, 0, $posCR), $result, $this->values[$id]);
                $str = substr($str, $posCRClose + 2, strlen($str));
                $countOperations--;
            }

            $str = trim($str);
            for($i = 0; $i < $countOperations; $i++){
                $pos = $this->findOperation($str);
                $value = trim(substr($str, $pos, strlen($str)));
                if ($value[0] == '"'){
                    $text = $this->getStringValue($value);
                    $result = $result . $text;
                } else {
                    $result = $this->algorithmicOperation($str, $result, $value);
                }
                $str = $value;
            }

            if (strpos($str, '!=') !== false){
                if ($result != $this->getValue('!=', $str)){
                    $array['result'] = 1;
                }else{
                    $array['result'] = 0;
                }
            }elseif (strpos($str, '>=') !== false){
                if ($result >= $this->getValue('>=', $str)){
                    $array['result'] = 1;
                }else{
                    $array['result'] = 0;
                }
            }elseif (strpos($str, '<=') !== false){
                if ($result <= $this->getValue('<=', $str)){
                    $array['result'] = 1;
                }else{
                    $array['result'] = 0;
                }
            }elseif (strpos($str, '>') !== false){
                if ($result > $this->getValue('>', $str)){
                    $array['result'] = 1;
                }else{
                    $array['result'] = 0;
                }
            }elseif (strpos($str, '<') !== false){
                if ($result < $this->getValue('<', $str)){
                    $array['result'] = 1;
                }else{
                    $array['result'] = 0;
                }
            }elseif(strpos($str, '=') !== false){
                if ($result == $this->getValue('=', $str)){
                    $array['result'] = 1;
                }else{
                    $array['result'] = 0;
                }
            }
        }
        return $array;
    }

    public function findAllOperations($str){
        $count = 0;
        $count += substr_count($str, ' + ');
        $count += substr_count($str, ' - ');
        $count += substr_count($str, ' / ');
        $count += substr_count($str, ' * ');
        $count += substr_count($str, ' MOD ');
        $count += substr_count($str, ' DIV ');
        return $count;
    }

    public function findOperation($str){
        $firstPos = null;
        $pos = null;
        $operations = array('*' => 1, '/' => 1, '+' => 1, '-' => 1, 'MOD' => 3, 'DIV' => 3);
        foreach($operations as $op => $len){
            $pos = strpos($str, $op);
            if ($pos !== false){
                $pos += $len;
                if ($firstPos == null){
                    $firstPos = $pos;
                } else {
                    if ($firstPos > $pos){
                        $firstPos = $pos;
                    }
                }
            }
        }
        return $firstPos;
    }

    public function algorithmicOperation($str, $result, $value){
        $str = trim($str);
        if (!empty($str)){
            if (strpos($str, '*') !== false){
                $result = $result * $value;
            }elseif(strpos($str, '/') !== false){
                $result = round($result / $value, 1);
            }elseif(strpos($str, '+') !== false){
                $result = $result + $value;
            }elseif(strpos($str, '-') !== false){
                $result = $result - $value;
            }elseif(strpos($str, 'MOD') !== false){
                $result = $result % $value;
            }elseif(strpos($str, 'DIV') !== false){
                $result = intval($result / $value);
            }
        } else {
            $result = $value;
        }
        return $result;
    }

    public function parseAction($str){
        $array = array();
        $posCR = strpos($str, '[[CR:');
        $posGOTO = strpos($str, 'GOTO');
        $posNOENTRY = strpos($str, 'NO-ENTRY');
        if ($posCR !== false){
            $result = null;
            $array['action'] = 'operation';
            $destID = $this->getId($str);
            $posEqual = strpos($str, '=');
            if ($posEqual !== false){
                $str = substr($str, $posEqual + 1, strlen($str));
                $countOperations = $this->findAllOperations($str) + 1;
                $countCR = substr_count($str, "[[CR:");
                if (($countOperations == 1) && ($countCR == 0)){
                    $result = trim($str);
                } else {
                    for($i = 0; $i < $countCR; $i++){
                        $posCR = strpos($str, '[[CR:');
                        $posCRClose = strpos($str, ']]');
                        $id = substr($str, $posCR + 5, ($posCRClose - $posCR - 5));
                        $result = $this->algorithmicOperation(substr($str, 0, $posCR), $result, $this->values[$id]);
                        $str = substr($str, $posCRClose + 2, strlen($str));
                        $countOperations--;
                    }
                    if ($countCR == 0){
                        $result = $this->algorithmicOperation($str, $result, $str);
                    }
                    $str = trim($str);
                    for($i = 0; $i < $countOperations; $i++){
                        $pos = $this->findOperation($str);
                        $value = trim(substr($str, $pos, strlen($str)));
                        if ($value[0] == '"'){
                            $text = $this->getStringValue($value);
                            $result = $result . $text;
                        } else {
                            $result = $this->algorithmicOperation($str, $result, $value);
                        }
                        $str = $value;
                    }
                }
            }
            $array['id'] = $destID;
            $array['result'] = $result;
        } elseif ($posGOTO !== false){
            $array['action'] = 'goto';
            $posNode = strpos($str, '[[NODE:');
            if ($posNode !== false){
                $posNodeClose = strpos($str, ']]');
                $array['id'] = (int) substr($str, $posNode + 7, $posNodeClose - $posNode - 7);
            } else {
                $array['id'] = null;
            }
        } elseif ($posNOENTRY !== false) {
            $array['action'] = 'no-entry';
        }
        return $array;
    }

    public function getId($str){
        $posCR = strpos($str, '[[CR:');
        $posCRClose = strpos($str, ']]');
        $id = substr($str, $posCR + 5, ($posCRClose - $posCR - 5));
        return $id;
    }

    public function getValue($exp, $str){
        $pos = strpos($str, $exp);
        $value = substr($str, $pos + strlen($exp), strlen($str));
        $stringCheck = $this->getStringValue($value);
        if ($stringCheck != null){
            $value = $stringCheck;
        }
        return trim($value);
    }

    public function getStringValue($str){
        $firstQuote = strpos($str, '"');
        if ($firstQuote !== false){
            $string = substr($str, $firstQuote + 1, strlen($str) - $firstQuote);
            $secondQuote = strpos($string, '"');
            $string = substr($string, 0, $secondQuote);
            return $string;
        } else {
            return null;
        }
    }
}
//-------------------------------------------------
//RUN TIME LOGIC
?>

