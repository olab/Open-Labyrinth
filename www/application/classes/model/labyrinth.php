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

    private $bookmark;
    private $nodeId = 0;

    public function execute ($nodeId, $bookmark = NULL, $isRoot = false, $cumulative = false)
    {
        $this->bookmark     = $bookmark;
        $this->nodeId       = $nodeId;
        $result             = array();
        $result['userId']   = (Auth::instance()->logged_in()) ? Auth::instance()->get_user()->id : 0;
        $node               = DB_ORM::model('map_node', array((int) $nodeId));
        $scenarioSectionId  = Session::instance()->get('webinarSectionId', false);

        if ($node)
        {
            if ($node->kfp) $matches = NULL; // if delete matches, program throw exception 6.6.2014

            $result['node']         = $node;
            $result['map']          = DB_ORM::model('map', array((int) $node->map_id));
            $result['editor']       = $this->checkUser($node->map_id) ? TRUE : FALSE;
            $result['node_title']   = $node->title;
            $result['node_text']    = $node->text;

            $clearAnnotation = strip_tags($node->annotation, '<img>');

            if (strlen($clearAnnotation) > 0) {
                $result['node_annotation'] = $node->annotation;
            }

            $sessionId = NULL;
            if ($bookmark) {
                $sessionId = DB_ORM::model('user_bookmark', array((int)$bookmark))->session_id;
                Session::instance()->set('session_id', $sessionId);
                setcookie('OL', $sessionId);
            } else if ($isRoot OR Session::instance()->get('webinarSection', false) == 'section') {
                $sessionId = DB_ORM::model('user_session')
                    ->createSession($result['userId'], $node->map_id, time(), getenv('REMOTE_ADDR'), Session::instance()->get('webinarId', null), Session::instance()->get('step', null));

                $result['webinarId']   = Session::instance()->get('webinarId', null);
                $result['webinarStep'] = Session::instance()->get('step', null);

                Session::instance()->delete('webinarId')->delete('step')->delete('webinarSection');
                Session::instance()->set('session_id', $sessionId);
                setcookie('OL', $sessionId);
            } else {
                $sessionId = Session::instance()->get('session_id', NULL);
                if ($sessionId == NULL) $sessionId = isset($_COOKIE['OL'])
                    ? $_COOKIE['OL']
                    : $sessionId = DB_ORM::model('user_session')->createSession($result['userId'], $node->map_id, time(), getenv('REMOTE_ADDR'));
            }

            $scenarioSession = DB_ORM::model('user_session', array((int)$sessionId));
            if($scenarioSession != null AND $scenarioSession->webinar_id != null AND $scenarioSession->webinar_step != null) {
                $result['webinarId']   = $scenarioSession->webinar_id;
                $result['webinarStep'] = $scenarioSession->webinar_step;
            }

            $section = false;
            if($scenarioSectionId){
                foreach (DB_ORM::select('Map_Node_Section_Node')->where('section_id', '=', $scenarioSectionId)->query()->as_array() as $sectionObj) {
                    $section[] = $sectionObj->node_id;
                }
            }

            $conditional                = $this->conditional($sessionId, $node);
            $result['previewNodeId']    = DB_ORM::model('user_sessionTrace')->getTopTraceBySessionId($sessionId);
            $result['node_links']       = $this->generateLinks($result['node'], $section);
            $result['sections']         = DB_ORM::model('map_node_section')->getSectionsByMapId($node->map_id);
            $result['counters']         = '';
            $result['redirect']         = NULL;
            $result['remoteCounters']   = '';
            $previewSessionTrace        = DB_ORM::model('user_sessionTrace', array((int)$result['previewNodeId']));
            $result['c_debug']          = $this->addQuestionResponsesAndChangeCounterValues($node->map_id, $sessionId, $previewSessionTrace->node_id);

            if ($conditional == NULL) {
                if ($sessionId AND $bookmark) {
                    $traceObj = DB_ORM::select('user_sessionTrace')->where('session_id', '=', $sessionId)->query()->fetch(0);
                    $traceId = $traceObj ? $traceObj->id : 'notExist';
                } elseif ($sessionId) {
                    $traceId = DB_ORM::model('user_sessionTrace')->createTrace($sessionId, $result['userId'], $node->map_id, $node->id);
                } else {
                    $traceId = 'notExist';
                }

                //                comment 27.08.2014
                //                if (substr($result['node_text'], 0, 3) != '<p>') {
                //                    $result['node_text'] = '<p>'.$result['node_text'].'</p>';
                //                }

                $c = $this->counters($traceId, $sessionId, $node, $isRoot, $cumulative);
                if ($c != NULL) {
                    if (isset($c['no-entry'])) {
                        $result['node_text'] = '<p>'.$c['message'].'</p>';
                        $result['node_links']['linker'] = $c['linker'];
                    } else {
                        $result['jsonRule'] = $c['jsonRule'];
                        $result['counters'] = $c['counterString'];
                        $result['c_debug'] = array_replace_recursive($c['c_debug'], $result['c_debug']);
                        $result['redirect'] = $c['redirect'];
                        $result['remoteCounters'] = $c['remote'];
                    }
                }
            } else {
                $result['node_text'] = '<p>'.$conditional['message'].'</p>';
                $result['node_links']['linker'] = $conditional['linker'];
            }

            $this->clearQuestionResponses();

            $trace = $this->getReviewLinks($sessionId);
            $result['traces'] = $trace;

            // Records trace info for visual display counters
            if (Arr::get($trace, 0, false)) {
                Session::instance()->set('traceCountersValues', $trace[0]->counters);
            }

            $result['sessionId'] = $sessionId;
        }

        $result['c_debug'] = $this->render_c_debug($result['c_debug']);

        return $result;
    }

    private function render_c_debug ($data)
    {
        $result         = array();
        $global_rules   = Arr::get($data, 'global_rules', array());

        // $data contains counter info and global rule, we need only counters info
        if ($global_rules) unset ($data['global_rules']);

        foreach($data as $id_c=>$counter)
        {
            /*-------------------------------- render data for view --------------------------------------------------*/
            $result[$id_c]['title'] = Arr::get($counter,'name').' = '.Arr::get($counter, 'current_value');
            $result[$id_c]['description'] = DB_ORM::model('map_counter', array($id_c))->description;
            $result[$id_c]['info'] = '';
            /*-------------------------------- end render data for view ----------------------------------------------*/

            /*-------------------------------- render info -----------------------------------------------------------*/
            $v_question     = (isset($counter['question_id'])) ? $counter['question_value'] : FALSE;
            $v_counter      = Arr::get($counter, 'counter_value', FALSE);
            $v_counter_rule = Arr::get($counter, 'counter_rule_value', FALSE);

            // counter main result
            $cmr = $counter['previous_value'];

            // -- previous value -- //
            $v_previous = 'Previous_value: '.$cmr.'<br>';

            // -- popup value, and rewrite previous value -- //
            foreach (Session::instance()->get('c_debug', array()) as $id_p=>$popup)
            {
                foreach ($popup as $id_counter=>$v_popup)
                {
                    if ($id_counter == $id_c)
                    {
                        // popup  value apply to counter in previous view, '-' display counter state in previous view
                        $cmr -= $v_popup;
                        $v_previous = 'Previous value: '.$cmr.'<br>';
                        $v_previous .= '<span class="colored-bl popup-color"></span>Popup #'.$id_p.': '.$cmr.$this->check_sign($v_popup, 'popup-color').'<br>';
                        $cmr += $v_popup;
                    }
                }
            }

            $result[$id_c]['info'] .= $v_previous;

            // -- question value -- //
            if ($v_question)
            {
                $result[$id_c]['info'] .= '<span class="colored-bl question-color"></span>Question #'.$counter['question_id'].': '.$cmr.$this->check_sign($v_question, 'question-color').'<br>';
                $cmr += $v_question;
            }
            // -- counter value -- //
            if ($v_counter)
            {
                $result[$id_c]['info'] .= '<span class="colored-bl counter-color"></span>Counter: '.$cmr.$this->check_sign($v_counter, 'counter-color').'<br>';
                $cmr += $v_counter;
            }
            // -- rule value -- //
            if ($v_counter_rule)
            {
                $result[$id_c]['info'] .= '<span class="colored-bl counter-rule-color"></span>Counter rule change: '.$cmr.' <span class="counter-rule-color">'.$v_counter_rule.'</span><br>';
                $cmr = $v_counter_rule;
            }
            // -- global rule value -- //
            foreach ($global_rules as $id_g_r => $g_rule)
            {
                $g_r_result = Arr::get($g_rule, 'result', array());
                foreach (Arr::get($g_r_result, 'counters', array()) as $id_c_g_r=>$outcome)
                {
                    $v_global_rule = ($id_c_g_r == $id_c) ? $outcome - $cmr : FALSE;
                    if ($v_global_rule)
                    {
                        $result[$id_c]['info'] .= '<span class="colored-bl global-rule-color"></span>Global rule #'.$id_g_r.': '.$cmr.$this->check_sign($v_global_rule, 'global-rule-color').'<br>';
                        $cmr += $v_global_rule;
                    }
                }
            }
            /*-------------------------------- end render info -------------------------------------------------------*/
        }

        Session::instance()->delete('c_debug');
        return $result;
    }

    private function check_sign ($str, $type)
    {
        $str = trim($str);

        if      (strlen($str) == 0) $str = ' + 0';
        elseif  ($str[0] == '-')    $str = str_replace('-', ' - ',$str);
        elseif  ($str[0] == '+')    $str = str_replace('+', ' + ',$str);
        elseif  ($str[0] == '=')    $str = str_replace('=', ' = ',$str);
        else                        $str = ' + '.$str;

        return '<span class="'.$type.'">'.$str.'</span>';
    }

    private function checkUser($mapId, $allowReviewers = false)
    {
        $user = Auth::instance()->get_user();
        if (Auth::instance()->logged_in() AND $user->type->name != 'learner') {
            $map = DB_ORM::model('map', array((int) $mapId));

            if ((DB_ORM::model('map_user')->checkUserById($mapId, $user->id)) OR
                ($map AND $map->author_id == $user->id) OR
                ($user->type->name == 'superuser') OR
                ($allowReviewers AND $user->type->name == 'reviewer')) return TRUE;
        }
        return FALSE;
    }

    private function getOrderInSections($mapId) {
        $result = array();
        $sections = DB_ORM::model('map_node_section')->getAllSectionsByMap($mapId);

        foreach ($sections as $section) {
            $orderBy = $section->orderBy;

            if ($orderBy == 'random') {
                continue;
            }

            foreach ($section->nodes as $node) {
                $nodeObj = DB_ORM::model('map_node', $node->node_id);

                if($orderBy == 'x') {
                    $coordinate = $nodeObj->x;
                } else {
                    $coordinate = $nodeObj->y;
                }

                if (isset($result[$coordinate])){
                    $coordinate += 1;
                }
                $result[$coordinate] = $node->node_id;
            }
        }

        ksort($result);
        $result = array_values($result);
        $result = array_flip($result);
        return $result;
    }

    private function generateLinks($node, $scenarioSection)
    {
        $orderInSection = $this->getOrderInSections($node->map_id);

        if (count($node->links)) {
            $withPosition = array();
            $withOutPosition = array();
            $orderType = $node->link_type->name;

            foreach ($node->links as $link) {
                if ($scenarioSection AND ! in_array($link->node_id_2, $scenarioSection)) {
                    continue;
                }

                if ($orderType == 'ordered' AND count($orderInSection)) {

                    $position = $orderInSection
                        ? Arr::get($orderInSection, $link->node_id_2, false)
                        : false;

                    if ($position === false) {
                        $withOutPosition[] = $link;
                    } else {
                        $withPosition[$position] = $link;
                    }
                } else {
                    switch ($node->link_type->name) {
                        case 'ordered':
                            $order = $link->order * 10000;
                            if (isset($withPosition[$order])) {
                                $nextIndex = $this->findNextIndex($withPosition, $order + 1);
                                $withPosition[$nextIndex] = $link;
                            } else {
                                $withPosition[$order] = $link;
                            }
                            break;
                        case 'random order':
                            $randomIndex = rand(0, 100000);
                            if (isset($withPosition[$randomIndex])) {
                                $nextIndex = $this->findNextIndex($withPosition, $randomIndex + 1);
                                $withPosition[$nextIndex] = $link;
                            } else {
                                $withPosition[$randomIndex] = $link;
                            }
                            break;
                        case 'random select one *':
                            $randomIndex = rand(0, 100000) * ($link->probability == 0 ? 1 : $link->probability);
                            if (isset($withPosition[$randomIndex])) {
                                $nextIndex = $this->findNextIndex($withPosition, $randomIndex + 1);
                                $withPosition[$nextIndex] = $link;
                            } else {
                                $withPosition[$randomIndex] = $link;
                            }
                            break;
                        default:
                            $withOutPosition[] = $link;
                            break;
                    }
                }
            }

            ksort($withPosition);
            $result = array_merge($withPosition, $withOutPosition);

            if ($node->link_type_id == 3) {
                $amount = count($result);
                if($amount){
                    $rand = rand(0, $amount - 1);
                    $result = array($result[$rand]);
                }
            }
            return $result;
        }
        return NULL;
    }

    private function findNextIndex($result, $index){
        return isset($result[$index]) ? $this->findNextIndex($result, $index + 1) : $index;
    }

    private function conditional($sessionId, $node)
    {
        if ($node != NULL and $node->conditional != '') {
            $mode = strstr($node->conditional, 'and') ? 'a' : 'o';
            $nodes = array();
            $conditional = $node->conditional;
            while (strlen($conditional)) {
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
            $message = ($node->conditional_message != '')
                ? $node->conditional_message
                : '<p>Sorry but you haven\'t yet explored all the required options ...</p>';

            if (($mode == 'a' AND $count < count($nodes)) OR ($mode == 'o' AND $count <= 0)) {
                return array(
                    'message' => $message,
                    'linker' => '<p><a href="javascript:history.back()">&laquo; back</a></p>'
                );
            }
        }
        return NULL;
    }

    public function addQuestionResponsesAndChangeCounterValues($mapID, $sessionId, $nodeId = null)
    {
        // array that contain info for counter debugger
        $c_debug            = array();
        $questionChoices    = Session::instance()->get('questionChoices');
        $questionChoices    = ($questionChoices != NULL) ? json_decode($questionChoices, true) : NULL;
        $countersFunc       = Session::instance()->get('countersFunc');
        $countersFunc       = ($countersFunc != NULL) ? json_decode($countersFunc, true) : NULL;
        $counterIDs         = array();
        $sctResponses       = Session::instance()->get('sctResponses', array());
        Session::instance()->delete('sctResponses');

        if (isset($questionChoices['counter_ids'])) {
            $counterIDs = $questionChoices['counter_ids'];
            unset($questionChoices['counter_ids']);
        }

        foreach ($sctResponses as $idQuestion=>$idResponse) {
            DB_ORM::insert('User_Response')
                ->column('question_id', $idQuestion)
                ->column('response', $idResponse)
                ->column('session_id', $sessionId)
                ->column('node_id', $nodeId)
                ->execute();
        }

        $counterString = $this->getCounterString($mapID);
        if (count($questionChoices)) {
            foreach($questionChoices as $qID => $questions) {
                if (count($questions)) {
                    foreach($questions as $q) {
                        DB_ORM::model('user_response')->createResponse($sessionId, $qID, $q['response'], $nodeId);
                        if (count($counterIDs)) {
                            $score = trim($q['score']);
                            $value = $this->getCounterValueFromString($counterIDs[$qID], $counterString);

                            //get info for c_debug
                            $c_debug[$counterIDs[$qID]]['previous_value'] = $value;
                            $c_debug[$counterIDs[$qID]]['question_value'] = $score;
                            $c_debug[$counterIDs[$qID]]['question_id']    = $qID;

                            $valueStr = (string)$score;
                            $value = $this->calculateCounterFunction($value, $score);

                            if (($valueStr[0] != '-') && ($valueStr[0] != '=')) {
                                $valueStr = '+'.$score;
                            }

                            $countersFunc[$counterIDs[$qID]][] = $valueStr;
                            $counterString = $this->setCounterValueToString($counterIDs[$qID], $counterString, $value);
                        }
                    }
                }
            }
        }

        $sliderQuestionChoices = Session::instance()->get('sliderQuestionResponses');
        if (count($sliderQuestionChoices)) {
            $slidersSum = 0;
            foreach($sliderQuestionChoices as $qID => $sliderValue) {
                $slidersSum += $sliderValue;
                DB_ORM::model('user_response')->createResponse($sessionId, $qID, $sliderValue, $nodeId);
                $question = DB_ORM::model('map_question', array((int)$qID));
                if ($question != null) {
                    if (count($question->responses)) {
                        foreach ($question->responses as $response) {
                            if ($sliderValue >= $response->from && $sliderValue <= $response->to) {
                                $score = $response->score;
                                $value = $this->getCounterValueFromString($question->counter->id, $counterString);

                                //get info for c_debug
                                $c_debug[$question->counter->id]['previous_value'] = $value;
                                $c_debug[$question->counter->id]['question_value'] = $score;
                                $c_debug[$question->counter->id]['question_id']    = $qID;

                                $valueStr = (string)$score;
                                $value = $this->calculateCounterFunction($value, $score);
                                if (($valueStr[0] != '-') && ($valueStr[0] != '=')) {
                                    $valueStr = '+'.$score;
                                }

                                $countersFunc[$question->counter->id][] = $valueStr;
                                $counterString = $this->setCounterValueToString($question->counter->id, $counterString, $value);
                            }
                        }
                    } else {
                        $countersFunc[$question->counter->id][] = '='.$sliderValue;
                        $counterString = $this->setCounterValueToString($question->counter->id, $counterString, $slidersSum);
                    }
                }
            }
        }

        $draggingQuestionResponses = Session::instance()->get('dragQuestionResponses');
        if(count($draggingQuestionResponses)) {
            foreach($draggingQuestionResponses as $responseJSON) {
                $responseObject = json_decode($responseJSON, true);
                if ($responseObject == null) continue;

                if (isset($responseObject['id']) AND isset($responseObject['responses'])) {
                    DB_ORM::model('user_response')->createResponse($sessionId, $responseObject['id'], json_encode($responseObject['responses']), $nodeId);
                }
            }
            Session::instance()->delete('dragQuestionResponses');
        }

        $this->updateCounterString($mapID, $counterString);

        Session::instance()->set('countersFunc', json_encode($countersFunc));

        $arrayAddedQuestions = Session::instance()->get('arrayAddedQuestions', array());
        if (count($arrayAddedQuestions)) {
            foreach($arrayAddedQuestions as $questionId => $value) {
                $question = DB_ORM::model('map_question', array((int) $questionId));

                if ($question->settings != '')  {
                    list($rule, $isCorrect) = json_decode($question->settings);
                }

                $stopRules = Session::instance()->get('stopCommonRules', array());
                if ( ! in_array('QU_'.$questionId, $stopRules) AND $question->settings != '' AND $isCorrect == 1) {
                    $mapID = $question->map_id;
                    $counters = DB_ORM::model('map_counter')->getCountersByMap($mapID);
                    $values = array();
                    if (count($counters)) {
                        foreach($counters as $counter) {
                            $values[$counter->id] = $this->getCounterValueByID($mapID, $counter->id);
                        }
                    }
                    $runtimeLogic = new RunTimeLogic();
                    $runtimeLogic->values = $values;
                    $runtimeLogic->questionResponse = NULL;
                    $array = $runtimeLogic->parsingString($rule);
                    $resultLogic = $array['result'];
                    if (isset($resultLogic['goto']) AND $resultLogic['goto'] != NULL) {
                        $nodes = DB_ORM::model('map_node')->getAllNode($mapID);
                        $inMap = false;

                        foreach ($nodes as $node) {
                            if ( $node->id == $resultLogic['goto']) $inMap = true;
                        }

                        if ($inMap) {
                            $goto = Session::instance()->get('goto', NULL);
                            if ($goto == NULL) Session::instance()->set('goto', $resultLogic['goto']);
                        }
                    }

                    if (isset($resultLogic['counters']) AND count($resultLogic['counters'])) {
                        $counterString = $this->getCounterString($mapID);
                        if ($counterString != '') {
                            foreach ($resultLogic['counters'] as $key => $v) {
                                $previousValue = $this->getCounterValueFromString($key, $counterString);
                                $counterString = $this->setCounterValueToString($key, $counterString, $v);

                                $diff = $v - $previousValue;
                                if ($diff > 0) $diff = '+'.$diff;
                                $countersFunc[$key][] = $diff;
                            }
                            $this->updateCounterString($mapID, $counterString);
                        }
                    }

                    if (isset($resultLogic['stop'])) {
                        $stopRules[] = 'QU_'.$questionId;
                        Session::instance()->set('stopCommonRules', $stopRules);
                    }
                }
            }
        }

        Session::instance()->delete('arrayAddedQuestions');

        return $c_debug;
    }

    private function clearQuestionResponses(){
        Session::instance()->delete('questionChoices');
        Session::instance()->delete('countersFunc');
        Session::instance()->delete('sliderQuestionResponses');
    }

    private function counters ($traceId, $sessionId, $node, $isRoot = false, $cumulative = false)
    {
        if ($traceId AND $node) {
            $counters = DB_ORM::model('map_counter')->getCountersByMap($node->map_id);
            if (count($counters)) {
                $countersArray          = array();
                $updateCounter          = '';
                $oldCounter             = '';
                $counterString          = '';
                $remoteCounterString    = '';
                $rootNode               = DB_ORM::model('map_node')->getRootNodeByMap($node->map_id);
                $redirect               = NULL;
                $main_counter['id']     = '';
                $main_counter['value']  = '';
                $c_debug                = array();
                $counterValue           = 0;
                $countersFunc           = Session::instance()->get('countersFunc');
                $countersFunc           = ($countersFunc != NULL) ? json_decode($countersFunc, true) : NULL;
                $jsonRule               = array();
                $continue               = true;
                $visualDisplay          = DB_ORM::model('map_visualdisplay')->getMapDisplaysShowOnAllPages($node->map_id);

                if ($rootNode == NULL) return '';

                if ($cumulative) {
                    $sessionObj = DB_ORM::select('User_Session')
                        ->where('webinar_id', '=', Controller_RenderLabyrinth::$scenarioId)
                        ->where('map_id', '=', $node->map_id)
                        ->where('notCumulative', '=', 0)
                        ->order_by('id', 'DESC')
                        ->query()
                        ->fetch(1);

                    if ($sessionObj) {
                        $sessionTrace = DB_ORM::select('User_SessionTrace')->where('session_id', '=', $sessionObj->id)->query()->fetch(0);

                        $countersTrace = $sessionTrace->counters;
                        DB_ORM::update('User_SessionTrace')->set('counters', $countersTrace)->where('session_id', '=', $sessionId)->execute();

                        $htmlCounters = $this->htmlCounters($visualDisplay, $counters, $countersTrace);
                        $counterString = $htmlCounters['htmlCounters'];
                        $remoteCounterString = $htmlCounters['htmlCountersRemote'];

                        $continue = false;
                    }
                }

                if ($this->bookmark) {
                    $traceObj = DB_ORM::model('user_sessiontrace', array($traceId));
                    $countersTrace = $traceObj->counters;

                    $htmlCounters = $this->htmlCounters($visualDisplay, $counters, $countersTrace);
                    $counterString = $htmlCounters['htmlCounters'];
                    $remoteCounterString = $htmlCounters['htmlCountersRemote'];
                } elseif ($continue) {
                    foreach ($counters as $counter) {
                        $if_main = $counter->status == 1;
                        if ($if_main) {
                            $main_counter['id'] = $counter->id; // if exist main counter get ID of it
                        }

                        $countersArray[$counter->id]['counter'] = $counter;
                        $currentCountersState = DB_ORM::model('user_sessionTrace')->getCounterByIDs($sessionId, $rootNode->map_id, $rootNode->id);
                        $oldCounter = $currentCountersState;

                        $label = $counter->name;
                        if ($counter->icon_id != 0) $label = '<img src="'.URL::base().$counter->icon->path.'">';
                        $countersArray[$counter->id]['label'] = $label;

                        $thisCounter = null;
                        if ($isRoot) {
                            $thisCounter = $counter->start_value;
                            if ($if_main) $main_counter['value'] = $thisCounter;
                        } elseif ($currentCountersState != '') {
                            $s = strpos($currentCountersState, '[CID=' . $counter->id . ',') + 1;
                            $tmp = substr($currentCountersState, $s, strlen($currentCountersState));
                            $e = strpos($tmp, ']') + 1;
                            $tmp = substr($tmp, 0, $e - 1);
                            $tmp = str_replace('CID=' . $counter->id . ',V=', '', $tmp);
                            $thisCounter = $tmp;

                            // get current max counter value
                            if ($if_main) {
                                preg_match('/(MCID=)(?<id>\d+),V=(?<value>\d+)/', $currentCountersState, $matches);
                                $main_counter['value'] = Arr::get($matches, 'value', 0);
                            }
                        }

                        $c_debug[$counter->id]['previous_value'] = $thisCounter;

                        $counterFunction = '';
                        $appearOnNode = 1;

                        if (count($node->counters)) {
                            foreach ($node->counters as $nodeCounter) {
                                if ($counter->id == $nodeCounter->counter->id) {
                                    $counterFunction = $nodeCounter->function;
                                    $appearOnNode = $nodeCounter->display;
                                    break;
                                }
                            }
                        }

                        $c_debug[$counter->id]['counter_value'] = $counterFunction;

                        if ($counterFunction != '') {
                            if ($counterFunction[0] == '=') $thisCounter = substr($counterFunction, 1, strlen($counterFunction));
                            else if ($counterFunction[0] == '-') $thisCounter -= substr($counterFunction, 1, strlen($counterFunction));
                            else {
                                $thisCounter += (int)$counterFunction;
                                if ($if_main) {
                                    $main_counter['value'] += (int)$counterFunction; // we need only positive values
                                }
                            }
                        }
                        $countersArray[$counter->id]['value'] = $thisCounter;

                        if ($counterFunction != '') $countersArray[$counter->id]['func'][] = $counterFunction;

                        if (isset($countersFunc[$counter->id]) AND count($countersFunc[$counter->id])) {
                            $countersArray[$counter->id]['func'] = (isset($countersArray[$counter->id]['func']) AND count($countersArray[$counter->id]['func']))
                                ? array_merge($countersArray[$counter->id]['func'], $countersFunc[$counter->id])
                                : $countersFunc[$counter->id];
                        }

                        $countersArray[$counter->id]['visible'] = (($counter->visible != 0) & ($appearOnNode == 1)) ? true : false;

                        $rules = DB_ORM::model('map_counter_rule')->getRulesByCounterId($counter->id);
                        $redirect = NULL;
                        foreach ($rules as $rule) {
                            $resultExp = FALSE;

                            switch ($rule->relation->value) {
                                case 'eq':
                                    if ($thisCounter == $rule->value) $resultExp = TRUE;
                                    break;
                                case 'neq':
                                    if ($thisCounter != $rule->value) $resultExp = TRUE;
                                    break;
                                case 'leq':
                                    if ($thisCounter <= $rule->value) $resultExp = TRUE;
                                    break;
                                case 'lt':
                                    if ($thisCounter < $rule->value) $resultExp = TRUE;
                                    break;
                                case 'geq':
                                    if ($thisCounter >= $rule->value) $resultExp = TRUE;
                                    break;
                                case 'gt':
                                    if ($thisCounter > $rule->value) $resultExp = TRUE;
                                    break;
                            }

                            if ($resultExp == TRUE AND $rule->function == 'redir')
                            {

                                $thisCounter = $this->calculateCounterFunction($thisCounter, $rule->counter_value);
                                $counterValue = $thisCounter;
                                // if main counter and firs spot not sign, add rule value
                                if($if_main AND strlen($counterFunction) AND (is_int( (int) $counterFunction[0]) OR $counterFunction[0]=='+')) {
                                    $main_counter['value'] += $rule->counter_value;
                                }
                                $c_debug[$counter->id]['counter_rule_value'] = $rule->counter_value;
                                $redirect = $rule->redirect_node_id;
                            }
                        }

                        if ($redirect != NULL) {
                            $countersArray[$counter->id]['redirect'] = $redirect;
                        }
                    }

                    $redirect = NULL;
                    $commonRules = DB_ORM::model('map_counter_commonrules')->getRulesByMapId($node->map_id);
                    if (count($commonRules)){
                        $values = array();
                        foreach($countersArray as $key => $counter){
                            $values[$key] = $counter['value'];
                        }

                        $runtimeLogic = new RunTimeLogic();
                        $runtimeLogic->values = $values;
                        $stopRules = Session::instance()->get('stopCommonRules', array());

                        foreach($commonRules as $rule){
                            if ( ! in_array('RULE_'.$rule->id, $stopRules)){

                                preg_match_all('/QU_ANSWER:(?<id>[^\]]*)\]\]\s*,\s*\'(?<response>[^\']*)/', $rule->rule, $matches);

                                // prepare response for $jsonRule
                                foreach(Arr::get($matches, 'response', array()) as $index=>$response) {
                                    if ($rule->lightning) {
                                        $response = str_replace('[', '', $response);
                                        $response = str_replace(']', '', $response);
                                        $response = str_replace('"', '', $response);
                                        $jsonRule[$response] = $matches['id'][$index];
                                    }
                                }

                                $array = $runtimeLogic->parsingString($rule->rule, $sessionId);
                                $c_debug['global_rules'][$rule->id] = $array;
                                $resultLogic = $array['result'];

                                if (isset($resultLogic['goto']) AND $resultLogic['goto'] != NULL AND $redirect == NULL) {
                                    $redirect = $resultLogic['goto'];
                                }

                                if (isset($resultLogic['counters']) AND count($resultLogic['counters'])) {
                                    foreach($resultLogic['counters'] as $key => $c) {
                                        $previousValue = $c;
                                        $funcStr = $c - $previousValue;
                                        if ($funcStr > 0) {
                                            $funcStr = '+'.$funcStr;
                                        }
                                        $countersArray[$key]['func'][] = $funcStr;
                                        $countersArray[$key]['value'] = $c;
                                    }
                                }

                                if (isset($resultLogic['conditions']) AND count($resultLogic['conditions'])) {
                                    foreach ($resultLogic['conditions'] as $conditionId => $value) {
                                        DB_ORM::update('Conditions_Assign')
                                            ->set('value', '=', $value)
                                            ->where('scenario_id', '=', Controller_RenderLabyrinth::$scenarioId)
                                            ->where('condition_id', '=', $conditionId)
                                            ->execute();
                                    }
                                }

                                if (isset($resultLogic['stop'])){
                                    $stopRules[] = 'RULE_'.$rule->id;
                                    Session::instance()->set('stopCommonRules', $stopRules);
                                }

                                if (isset($resultLogic['no-entry'])){
                                    $message = '<p>Sorry but you haven\'t yet explored all the required options ...</p>';
                                    return array('no-entry' => 1, 'message' => $message, 'linker' => '<p><a href="javascript:history.back()">&laquo; back</a></p>');
                                }
                            }
                        }
                    }

                    foreach($visualDisplay as $display) {
                        $counterString .= '<div class="visualDisplayCounterContainer">';
                        $counterString .= $this->getVisualDisplayHTML($display->id);
                        $counterString .= '</div>';
                    }

                    $counterString .='<ul class="navigation">';
                    foreach($countersArray as $counter)
                    {
                        $counterObj = Arr::get($counter, 'counter', false);
                        if ($counterObj) {
                            $displayValue = ($counterValue != 0) ? $counterValue : $counter['value'];

                            if (Arr::get($counter,'visible', false)) {
                                $c_debug[$counterObj->id]['name'] = $counter['label'];
                                $c_debug[$counterObj->id]['current_value'] = $counter['value'];
                                $counterString .= '<li><a data-toggle="modal" href="#" data-target="#counter-debug">'.$counter['label'].'</a> ('.$displayValue.')</li>';
                                $remoteCounterString .= '<counter id="'.$counterObj->id.'" name="'.$counterObj->name.'" value="'.$counter['value'].'"></counter>';
                            }

                            if (isset($counter['redirect']) AND $redirect == NULL) {
                                $redirect = $counter['redirect'];
                            }

                            $updateCounter .= '[CID='.$counterObj->id.',V='.$displayValue.']';
                        }
                    }
                    $updateCounter .= '[MCID='.$main_counter['id'].',V='.$main_counter['value'].']';

                    DB_ORM::model('user_sessionTrace')->updateCounter($sessionId, $node->map_id, $node->id, $oldCounter, $traceId);
                    DB_ORM::model('user_sessionTrace')->updateCounter($sessionId, $rootNode->map_id, $rootNode->id, $updateCounter);

                    // Cron must execute after user_sessionTrace update
                    DB_ORM::model('Cron')->parseRule($node->map_id);
                    $conditionString = $this->htmlCondition();
                    $counterString .= '</ul>'.$conditionString;

                    Session::instance()->delete('questionChoices');
                    if ($redirect != NULL && $redirect != $node->id){
                        Request::initial()->redirect(URL::base().'renderLabyrinth/go/'.$node->map_id.'/'.$redirect);
                    }
                }

                return array(
                    'c_debug'       => $c_debug,
                    'counterString' => $counterString,
                    'redirect'      => $redirect,
                    'remote'        => $remoteCounterString,
                    'jsonRule'      => json_encode($jsonRule)
                );
            }
            return '';
        }
        return '';
    }

    private function htmlCounters($visualDisplay, $counters, $countersTrace){
        $counterString       = '';
        $remoteCounterString = '';
        $result              = array();
        $conditionString     = $this->htmlCondition(); // change conditions, by the visit node

        foreach($visualDisplay as $display) {
            $counterString .= '<div class="visualDisplayCounterContainer">';
            $counterString .= $this->getVisualDisplayHTML($display->id);
            $counterString .= '</div>';
        }

        $counterString .='<ul class="navigation">';
        foreach($counters as $counter)
        {
            if ($counter->visible)
            {
                $label = ($counter->icon_id != 0) ? '<img src="'.URL::base().$counter->icon->path.'">' : $counter->name;
                $valueConvert = strpos($countersTrace, 'CID='.$counter->id.',V=');
                $valueConvert = substr($countersTrace, $valueConvert + strlen('CID='.$counter->id.',V='));
                $value = (int)$valueConvert;

                $counterString .= '<li><a data-toggle="modal" href="#" data-target="#counter-debug">'.$label.'</a> ('.$value.')</li>';
                $remoteCounterString .= '<counter id="'.$counter->id.'" name="'.$counter->name.'" value="'.$value.'"></counter>';
            }
        }
        $counterString .="</ul>".$conditionString;

        $result['htmlCounters'] = $counterString;
        $result['htmlCountersRemote'] = $remoteCounterString;

        return $result;
    }

    private function htmlCondition() {
        $scenarioId = Controller_RenderLabyrinth::$scenarioId;
        $conditionString = '';
        $conditionChangeObj = DB_ORM::select('Conditions_Change')
            ->where('scenario_id', '=', $scenarioId)
            ->where('node_id', '=', $this->nodeId)
            ->query()
            ->as_array();

        if ($conditionChangeObj) {
            $conditionString = '<ul class="navigation">';
            foreach ($conditionChangeObj as $changeObj) {
                $assignObj      = DB_ORM::select('Conditions_Assign')
                    ->where('scenario_id', '=', $scenarioId)
                    ->where('condition_id', '=', $changeObj->condition_id)
                    ->query()
                    ->fetch(0);
                $currentValue   = $assignObj->value;
                $result         = $currentValue;

                $change = trim($changeObj->value);
                if ($change) {
                    $sign   = $change[0];
                    $change = (int) preg_replace('/\D/', '', $change);
                    if ($sign == '=') {
                        $result = $change;
                    } elseif ($sign == '-') {
                        $result -= $change;
                    } else {
                        $result += $change;
                    }
                    $assignObj->value = $result;
                    $assignObj->save();
                }

                if ($changeObj->appears){
                    $conditionString .= '<li>'.DB_ORM::model('Conditions',array($changeObj->condition_id))->name.' ('.$result.')</li>';
                }
            }
            $conditionString .= '</ul>';
        }

        return $conditionString;
    }

    private static function getVisualDisplayHTML($visualDisplayId) {
        $visualDisplay = DB_ORM::model('map_visualdisplay', array((int) $visualDisplayId));
        $result = '';

        $traceCountersValues = Session::instance()->get('traceCountersValues');

        if($visualDisplay != null) {
            $result .= '<div class="visual-display-container" style="position:relative; display:block; height: 100%; width: 100%;">';

            if($visualDisplay->panels != null && count($visualDisplay->panels) > 0) {
                foreach($visualDisplay->panels as $panel) {
                    $result .= '
                    <div style="
                        position: absolute;
                        top: '.$panel->y.'px;
                        left: '.$panel->x.'px;
                        z-index: '.$panel->z_index.';
                        background-color: ' . $panel->background_color . ';
                        width: '.$panel->width.'px;
                        height: '.$panel->height.'px;
                        border: '.$panel->border_size.'px solid '.$panel->border_color.';
                        border-radius: '.$panel->border_radius.'px;
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

                    $result .= '<div style="position: absolute; top: '.$counter->label_y.'px;
                                                left: ' . $counter->label_x . 'px;
                                             z-index: ' . $counter->label_z_index . ';
                                      -moz-transform: rotate(' . $counter->label_angle . 'deg);
                                   -webkit-transform: rotate(' . $counter->label_angle . 'deg);
                                        -o-transform: rotate(' . $counter->label_angle . 'deg);
                                       -ms-transform: rotate(' . $counter->label_angle . 'deg);
                                           transform: rotate(' . $counter->label_angle . 'deg);
                                         font-family: ' . $labelFont[0] . ';
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
                                ">' . $thisCounter . '</div>';
                }
            }

            $result .= '</div>';
        }

        return $result;
    }

    private function calculateCounterFunction ($counter, $function)
    {
        if (strlen($function) > 0)
        {
            if      ($function[0] == '=') $counter = substr($function, 1, strlen($function));
            else if ($function[0] == '-') $counter -= substr($function, 1, strlen($function));
            else    $counter += $function;
        }
        return $counter;
    }

    private function getReviewLinks($sesionId)
    {
        $traces = DB_ORM::model('user_sessionTrace')->getTraceBySessionID($sesionId);
        return ($traces != NULL) ? $traces : NULL;
    }

    public function review($nodeId) {
        $sessionId = Session::instance()->get('session_id', NULL);

        if ($sessionId == NULL AND isset($_COOKIE['OL'])) {
            $sessionId = $_COOKIE['OL'];
        }

        if ($sessionId AND $nodeId) {
            $node = DB_ORM::model('map_node', array((int) $nodeId));
            if ($node) {
                $rootNode = DB_ORM::model('map_node')->getRootNodeByMap((int) $node->map_id);
                $counter  = DB_ORM::model('user_sessionTrace')->getCounterByIDs($sessionId, (int) $node->map_id, $node->id);
                if (isset($rootNode->id)) DB_ORM::model('user_sessionTrace')->updateCounter($sessionId, $rootNode->map_id, $rootNode->id, $counter);
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
                                        } else {
                                            return $element->response;
                                        }
                                    }
                                }
                            } else {
                                return $element->response;
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

    public function question($questionId, $response, $questionStatus, $nodeId) {
        $returnStr = '';
        $question = DB_ORM::model('map_question', array((int) $questionId));

        if ($question)
        {
            $responseObj = null;
            if (($question->type->value != 'text') AND ($question->type->value != 'area')) {
                foreach ($question->responses as $resp) {
                    if ($resp->id == $response) {
                        $responseObj = $resp;
                        break;
                    }
                }
            }

            $qChoices = json_decode(Session::instance()->get('questionChoices'), true);

            if (($question->type->value == 'text') OR ($question->type->value == 'area'))
            {
                $response = htmlspecialchars(base64_decode($response));
                DB_ORM::model('user_response')->createResponse($this->getSessionID(), $question->id, $response, $nodeId);

                $countersFunc = Session::instance()->get('countersFunc');
                $countersFunc = ($countersFunc != NULL) ? json_decode($countersFunc, true) : NULL;
                if ($question->settings != '') list($rule, $isCorrect) = json_decode($question->settings);

                if ($question->settings != '' AND $isCorrect == 1)
                {
                    $mapID = $question->map_id;
                    $counters = DB_ORM::model('map_counter')->getCountersByMap($mapID);
                    $values = array();
                    if (count($counters) > 0){
                        foreach($counters as $counter){
                            $values[$counter->id] = $this->getCounterValueByID($mapID, $counter->id);
                        }
                    }
                    $runtimelogic = new RunTimeLogic();
                    $runtimelogic->values = $values;
                    $runtimelogic->questionResponse = $response;

                    $array = $runtimelogic->parsingString($rule);
                    $resultLogic = $array['result'];
                    if (isset($resultLogic['goto']) AND $resultLogic['goto'] != NULL)
                    {
                        $nodes = DB_ORM::model('map_node')->getAllNode($mapID);
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

                    if (isset($resultLogic['counters']) AND count($resultLogic['counters']) > 0)
                    {
                        $counterString = $this->getCounterString($mapID);
                        if ($counterString != '')
                        {
                            foreach($resultLogic['counters'] as $key => $value)
                            {
                                $previousValue = $this->getCounterValueFromString($key, $counterString);
                                $counterString = $this->setCounterValueToString($key, $counterString, $value);

                                $diff = $value - $previousValue;
                                $countersFunc[$key][] = ($diff > 0) ? '+'.$diff : $diff;
                            }
                            $this->updateCounterString($mapID, $counterString);
                        }
                    }

                    Session::instance()->set('countersFunc', json_encode($countersFunc));

                    if (isset($resultLogic['correct'])) $returnStr .= '<img src="'.URL::base().'images/tick.jpg"> ';
                    if (isset($resultLogic['incorrect'])) $returnStr .= '<img src="'.URL::base().'images/cross.jpg"> ';
                }

                $arrayAddedQuestions = Session::instance()->get('arrayAddedQuestions', array());
                if (isset($arrayAddedQuestions[$questionId]))
                {
                    unset($arrayAddedQuestions[$questionId]);
                    Session::instance()->set('arrayAddedQuestions', $arrayAddedQuestions);
                }
            }

            if ($question->type->value == 'pcq')
            {
                $qChoices[$questionId] = array();
                $qChoices[$questionId][$response]['score'] = $responseObj->score;
                $qChoices[$questionId][$response]['response'] = $responseObj->response;
                $qChoices['counter_ids'][$questionId] = $question->counter_id;

                $arrayAddedQuestions = Session::instance()->get('arrayAddedQuestions', array());
                if (isset($arrayAddedQuestions[$questionId]))
                {
                    unset($arrayAddedQuestions[$questionId]);
                    Session::instance()->set('arrayAddedQuestions', $arrayAddedQuestions);
                }
            }

            if ($question->type->value == 'mcq')
            {
                if ($questionStatus == 1){
                    $qChoices[$questionId][$response]['score'] = $responseObj->score;
                    $qChoices[$questionId][$response]['response'] = $responseObj->response;
                }
                else if (isset($qChoices[$questionId][$response])) unset($qChoices[$questionId][$response]);

                $qChoices['counter_ids'][$questionId] = $question->counter_id;

                $arrayAddedQuestions = Session::instance()->get('arrayAddedQuestions', array());
                if (isset($arrayAddedQuestions[$questionId]))
                {
                    unset($arrayAddedQuestions[$questionId]);
                    Session::instance()->set('arrayAddedQuestions', $arrayAddedQuestions);
                }
            }
            Session::instance()->set('questionChoices', json_encode($qChoices));

            if ($question->show_answer AND $question->type->value != 'text' AND $question->type->value != 'area')
            {
                switch ($responseObj->is_correct){
                    case 0:
                        $returnStr .= '<img src="'.URL::base().'images/cross.jpg"> ';
                        break;
                    case 1:
                        $returnStr .= '<img src="'.URL::base().'images/tick.jpg"> ';
                        break;
                }
                $returnStr .= ($responseObj->feedback != null && strlen($responseObj->feedback) > 0 ? ('('.$responseObj->feedback.')') : '');
            }
        }

        return $returnStr;
    }

    public function getMainFeedback($session, $counters, $mapId) {
        $rules = DB_ORM::model('map_feedback_rule')->getRulesByMap($mapId);

        $result = array();
        $map = DB_ORM::model('map', array((int) $mapId));
        if ($map != NULL and $map->feedback != '') {
            $result['general'] = $map->feedback;
        }

        if ($rules != NULL and count($rules) > 0) {
            $mustVisited = 0;
            $mustAvoid   = 0;
            if(count($session->traces) > 0) {
                foreach($session->traces as $trace) {
                    if($trace->node->priority_id == 3) { $mustVisited++; }
                    if($trace->node->priority_id == 2) { $mustAvoid++; }
                }
            }

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
                            if (Model_Labyrinth::calculateRule($rule->operator->value, $mustVisited, $rule->value)) {
                                $result['mustVisit'][] = $rule->message;
                            }
                        }
                        break;
                    case 'must avoid':
                        if (count($session->traces) > 0) {
                            if (Model_Labyrinth::calculateRule($rule->operator->value, $mustAvoid, $rule->value)) {
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

    public function getCounterValueByID ($mapId, $id, $onlyValue = false)
    {
        $result = NULL;
        if ($mapId)
        {
            $sessionId = $this->getSessionID();

            $counter = DB_ORM::model('map_counter', array($id));

            if ($sessionId != NULL){
                $currentCountersState = '';
                $rootNode = DB_ORM::model('map_node')->getRootNodeByMap($mapId);
                if ($rootNode != NULL){
                    $currentCountersState = DB_ORM::model('user_sessionTrace')->getCounterByIDs($sessionId, $rootNode->map_id, $rootNode->id);
                }

                if ($currentCountersState != ''){
                    $s = strpos($currentCountersState, '[CID=' . $counter->id . ',') + 1;
                    $tmp = substr($currentCountersState, $s, strlen($currentCountersState));
                    $e = strpos($tmp, ']') + 1;
                    $tmp = substr($tmp, 0, $e - 1);
                    $tmp = str_replace('CID=' . $counter->id . ',V=', '', $tmp);
                    if ($onlyValue) {
                        $start = strpos($tmp, 'V=');
                        if ($start !== false) {
                            $tmp = substr($tmp, $start + 2);
                        }
                    }
                    $result = $tmp;
                } else {
                    $result = $counter->start_value;
                }
            }
            else $result = $counter->start_value;
        }
        return $result;
    }

    public function getCounterString($mapId)
    {
        $currentCountersState = '';
        if ($mapId != NULL)
        {
            $rootNode = DB_ORM::model('map_node')->getRootNodeByMap($mapId);
            if ($rootNode != NULL) $currentCountersState = DB_ORM::model('user_sessionTrace')->getCounterByIDs($this->getSessionID(), $rootNode->map_id, $rootNode->id);
        }
        return $currentCountersState;
    }

    public function updateCounterString($mapId, $string)
    {
        if ($mapId != NULL)
        {
            $rootNode = DB_ORM::model('map_node')->getRootNodeByMap($mapId);
            if ($rootNode != NULL) DB_ORM::model('user_sessionTrace')->updateCounter($this->getSessionID(), $rootNode->map_id, $rootNode->id, $string);
        }
        return true;
    }

    public function getCounterValueFromString($cid, $string){
        $return = NULL;
        $pattern = '\\[CID='.$cid.',V=(\d+)\\]';
        if ($c=preg_match_all ("/".$pattern."/is", $string, $matches)){
            if (count($matches[0]) > 0){
                $return = $matches[1][0];
            }
        }
        return $return;
    }

    public function setCounterValueToString($cid, $string, $value){
        $pattern = '\\[CID='.$cid.',V=\d+\\]';
        $string = preg_replace("/".$pattern."/is", '[CID='.$cid.',V='.$value.']', $string);
        return $string;
    }

    public function getSessionID()
    {
        $sessionId = Session::instance()->get('session_id', NULL);
        if ($sessionId == NULL AND isset($_COOKIE['OL'])) $sessionId = $_COOKIE['OL'];
        return $sessionId;
    }

    public function popup_counters ($map_id, $popup_id)
    {
        $counterString  = $this->getCounterString($map_id);
        $counters       = DB_ORM::model('map_popup_counter')->getCountersScore($popup_id);
        foreach ($counters as $c)
        {
            $value          = $this->getCounterValueFromString($c->counter_id, $counterString);
            $value          = $this->calculateCounterFunction($value, $c->function);
            $counterString  = $this->setCounterValueToString($c->counter_id, $counterString, $value);

            $c_debug = Session::instance()->get('c_debug');
            $c_debug[$popup_id][$c->counter_id] = $c->function;
            Session::instance()->set('c_debug', $c_debug);
        }
        $this->updateCounterString($map_id, $counterString);
    }
}
