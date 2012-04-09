<?php

defined('SYSPATH') or die('No direct script access.');

class Model_Labyrinth extends Model {

    public function execute($nodeId, $bookmark = NULL) {
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
			} else if ($node->type->name == 'root') {
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

            DB_ORM::model('user_sessionTrace')->createTrace($sessionId, $result['userId'], $node->map_id, $node->id);
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

            $c = $this->counters($sessionId, $node);
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
                        if (isset($result[$link->order])) {
                            $result[$link->order + 1] = $link;
                        } else {
                            $result[$link->order] = $link;
                        }
                        break;
                    case 'random order':
                        $randomIndex = rand();
                        if (isset($result[$randomIndex])) {
                            $result[$randomIndex + 1] = $link;
                        } else {
                            $result[$randomIndex] = $link;
                        }
                        break;
                    case 'random select one *':
                        $randomIndex = rand() * ($link->probability == 0 ? 1 : $link->probability);
                        if (isset($result[$randomIndex])) {
                            $result[$randomIndex + 1] = $link;
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

    private function clearArray($array) {
        if (count($array) > 0) {
            $result = array();
            for ($i = 0, $j = 0; $i < count($array); $j++) {
                if (isset($array[$j])) {
                    $result[] = $array[$j];
                    $i++;
                }
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

    private function counters($sessionId, $node) {
        if ($node != NULL) {
            $counters = DB_ORM::model('map_counter')->getCountersByMap($node->map_id);
            if (count($counters) > 0) {
                $updateCounter = '';
                $oldCounter = '';
                $counterString = '';
                $remoteCounterString = '';
                $rootNode = DB_ORM::model('map_node')->getRootNodeByMap($node->map_id);
                $redirect = NULL;
                foreach ($counters as $counter) {
                    $currentCountersState = '';
                    if ($rootNode != NULL) {
                        $currentCountersState = DB_ORM::model('user_sessionTrace')->getCounterByIDs($sessionId, $rootNode->map_id, $rootNode->id);
                        $oldCounter = $currentCountersState;
                    }
					
                    $label = $counter->name;
                    if ($counter->icon_id != 0) {
                        $label = '<img src="' . URL::base() . $counter->icon->path . '">';
                    }

                    $thisCounter = 0;
                    if ($node->type->name == 'root') {
                        $thisCounter = $counter->start_value;
                    } else if ($currentCountersState != '') {
                        $s = strpos($currentCountersState, '[CID=' . $counter->id . ',') + 1;
                        $tmp = substr($currentCountersState, $s, strlen($currentCountersState));
                        $e = strpos($tmp, ']') + 1;
                        $tmp = substr($tmp, 0, $e - 1);
                        $tmp = str_replace('CID=' . $counter->id . ',V=', '', $tmp);
                        if (is_numeric($tmp)) {
                            $thisCounter = (int) $tmp;
                        }
                    }

                    $counterFunction = '';
                    if (count($node->counters) > 0) {
                        foreach ($node->counters as $nodeCounter) {
                            if ($counter->id == $nodeCounter->counter->id) {
                                $counterFunction = $nodeCounter->function;
                                break;
                            }
                        }
                    }

                    if ($counterFunction != '') {
                        if ($counterFunction[0] == '=') {
                            $thisCounter = (int) substr($counterFunction, 1, strlen($counterFunction));
                        } else if ($counterFunction[0] == '-') {
                            $thisCounter -= (int) substr($counterFunction, 1, strlen($counterFunction));
                        } else if ($counterFunction[0] == '+') {
                            $thisCounter += (int) substr($counterFunction, 1, strlen($counterFunction));
                        }
                    }

                    if ($counterFunction != '') {
                        $func = '<sup>[' . $counterFunction . ']</sup>';
                    } else {
                        $func = '<sup>[no]</sup>';
                    }

                    if ($counter->visible) {
                        $popup = '<a href="#" onclick="window.open("' . URL::base() . 'renderLabyrinth/", "Counter", ' . "'toolbar=no, directories=no, location=no, status=no, menubar=no, resizable=yes, scrollbars=yes, width=400, height=350);" . ' return false;">';
                        $counterString .= '<p>' . $popup . $label . '</a>(' . $thisCounter . ') ' . $func . '</p>';
                        $remoteCounterString .= '<counter id="'.$counter->id.'" name="'.$counter->name.'" value="'.$thisCounter.'"></counter>';
                    }

                    $rules = DB_ORM::model('map_counter_rule')->getRulesByCounterId($counter->id);

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
                                    $redirect = $rule->redirect_node_id;
                                }
                            }
                        }
                    }

                    $updateCounter .= '[CID=' . $counter->id . ',V=' . $thisCounter . ']';
                }

                DB_ORM::model('user_sessionTrace')->updateCounter($sessionId, $node->map_id, $node->id, $oldCounter);
                DB_ORM::model('user_sessionTrace')->updateCounter($sessionId, $rootNode->map_id, $rootNode->id, $updateCounter);

                return array('counterString' => $counterString, 'redirect' => $redirect, 'remote' => $remoteCounterString);
            }

            return '';
        }

        return '';
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
                                        $thisCounter = (int) $tmp;

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
                            $thisCounter = (int) $tmp;
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
            DB_ORM::model('user_response')->updateResponse($sessionId, $questionId, $r);

            if ($question->show_answer) {
                if ($question->type->value != 'text' and $question->type->value != 'area') {
                    if ($qResp->is_correct) {
                        if ($qResp->response != '') {
                            return '<p><img src="' . URL::base() . 'images/tick.jpg"> correct (' . $qResp->response . ')</p>';
                        } else {
                            return '<p><img src="' . URL::base() . 'images/tick.jpg"> ' . $question->feedback . '</p>';
                        }
                    } else {
                        if ($qResp->response != '') {
                            return '<p><img src="' . URL::base() . 'images/cross.jpg"> incorrect (' . $qResp->response . ')</p>';
                        } else {
                            return '<p><img src="' . URL::base() . 'images/cross.jpg"> ' . $question->feedback . '</p>';
                        }
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
?>

