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

    public function execute ($nodeId, $bookmark = NULL, $isRoot = false)
    {
        $result             = array();
        $result['userId']   = (Auth::instance()->logged_in()) ? Auth::instance()->get_user()->id : 0;
        $node               = DB_ORM::model('map_node', array((int) $nodeId));

        if ($node)
        {
            if ($node->kfp) $matches = $this->getMatch($nodeId);

            $result['node']         = $node;
            $result['map']          = DB_ORM::model('map', array((int) $node->map_id));
            $result['editor']       = $this->checkUser($node->map_id) ? TRUE : FALSE;
            $result['node_title']   = $node->title;
            $result['node_text']    = $node->text;

            $clearAnnotation = strip_tags($node->annotation, '<img>');

            if ($this->checkUser($node->map_id, true) & (strlen($clearAnnotation) > 0))  $result['node_annotation'] = $node->annotation;

            $sessionId = NULL;
            if($bookmark != NULL) {
                $b = DB_ORM::model('user_bookmark', array((int)$bookmark));
                $sessionId = $b->session_id;
                Session::instance()->set('session_id', $sessionId);
                setcookie('OL', $sessionId);
            } else if ($isRoot) {
                $sessionId = DB_ORM::model('user_session')->createSession($result['userId'], $node->map_id, time(), getenv('REMOTE_ADDR'), Session::instance()->get('webinarId', null), Session::instance()->get('step', null));

                $result['webinarId']   = Session::instance()->get('webinarId', null);
                $result['webinarStep'] = Session::instance()->get('step', null);

                Session::instance()->delete('webinarId')->delete('step');

                Session::instance()->set('session_id', $sessionId);
                setcookie('OL', $sessionId);
            } else {
                $sessionId = Session::instance()->get('session_id', NULL);
                if ($sessionId == NULL) $sessionId = isset($_COOKIE['OL']) ? $_COOKIE['OL'] : 'notExist';
            }

            $webinarSession = DB_ORM::model('user_session', array((int)$sessionId));
            if($webinarSession != null && $webinarSession->webinar_id != null && $webinarSession->webinar_step != null) {
                $result['webinarId']   = $webinarSession->webinar_id;
                $result['webinarStep'] = $webinarSession->webinar_step;
            }

            $conditional                = $this->conditional($sessionId, $node);
            $result['previewNodeId']    = DB_ORM::model('user_sessionTrace')->getTopTraceBySessionId($sessionId);
            $result['node_links']       = $this->generateLinks($result['node']);
            $result['sections']         = DB_ORM::model('map_node_section')->getSectionsByMapId($node->map_id);

            $previewSessionTrace        = DB_ORM::model('user_sessionTrace', array((int)$result['previewNodeId']));
            $result['c_debug']          = $this->addQuestionResponsesAndChangeCounterValues($node->map_id, $sessionId, $previewSessionTrace->node_id);

            $result['counters']         = '';
            $result['redirect']         = NULL;
            $result['remoteCounters']   = '';

            if ($conditional == NULL)
            {
                $traceId = ($sessionId != 'notExist')
                    ? DB_ORM::model('user_sessionTrace')->createTrace($sessionId, $result['userId'], $node->map_id, $node->id)
                    : 'notExist';

                if (substr($result['node_text'], 0, 3) != '<p>') $result['node_text'] = '<p>'.$result['node_text'].'</p>';

                $c = $this->counters($traceId, $sessionId, $node, $isRoot);
                if ($c != NULL) {
                    if (isset($c['no-entry']))
                    {
                        $result['node_text'] = '<p>' . $c['message'] . '</p>';
                        $result['node_links']['linker'] = $c['linker'];
                    }
                    else
                    {
                        $result['counters'] = $c['counterString'];
                        $result['c_debug'] = array_replace_recursive($c['c_debug'], $result['c_debug']);
                        $result['redirect'] = $c['redirect'];
                        $result['remoteCounters'] = $c['remote'];
                    }
                }
            } else {
                $result['node_text'] = '<p>' . $conditional['message'] . '</p>';
                $result['node_links']['linker'] = $conditional['linker'];
            }

            $this->clearQuestionResponses();

            $trace = $this->getReviewLinks($sessionId);
            $result['traces'] = $trace;

            // Records trace info for visual display counters
            if (Arr::get($trace, 0, false)) Session::instance()->set('traceCountersValues', $trace[0]->counters);

            $result['sessionId'] = $sessionId;
        }

        $result['c_debug']=$this->render_c_debug($result['c_debug']);

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
                        $v_previous = 'Previous_value: '.$cmr.'<br>';
                        $v_previous .= 'Popup #'.$id_p.': '.$cmr.$this->check_sign($v_popup).'<br>';
                        $cmr += $v_popup;
                    }
                }
            }

            $result[$id_c]['info'] .= $v_previous;

            // -- question value -- //
            if ($v_question)
            {
                $result[$id_c]['info'] .= 'Question #'.$counter['question_id'].': '.$cmr.$this->check_sign($v_question).'<br>';
                $cmr += $v_question;
            }
            // -- counter value -- //
            if ($v_counter)
            {
                $result[$id_c]['info'] .= 'Counter: '.$cmr.$this->check_sign($v_counter).'<br>';
                $cmr += $v_counter;
            }
            // -- rule value -- //
            if ($v_counter_rule)
            {
                if (trim($v_counter_rule[0]) == '=')
                {
                    $result[$id_c]['info'] .= 'Counter rule: '.$v_counter_rule.'<br>';
                    $cmr = $v_counter_rule;
                }
                else
                {
                    $result[$id_c]['info'] .= 'Counter rule: '.$cmr.$this->check_sign($v_counter_rule).'<br>';
                    $cmr += $v_counter_rule;
                }
            }
            // -- global rule value -- //
            foreach ($global_rules as $id_g_r => $g_rule)
            {
                $g_r_result = Arr::get($g_rule, 'result', array());
                foreach (Arr::get($g_r_result, 'counters', array()) as $id_c_g_r=>$outcome)
                {
                    $v_global_rule = ($id_c_g_r == $id_c) ? $outcome - $counter['counter_value'] : FALSE;
                    if ($v_global_rule)
                    {
                        $result[$id_c]['info'] .= 'Global rule #'.$id_g_r.': '.$cmr.$this->check_sign($v_global_rule).'<br>';
                        $cmr += $v_global_rule;
                    }
                }
            }
            /*-------------------------------- end render info -------------------------------------------------------*/
        }

        Session::instance()->delete('c_debug');
        return $result;
    }

    private function check_sign ($str)
    {
        $str = trim($str);
        if      (strlen($str) == 0) $str = ' + 0';
        elseif  ($str[0] == '-')    $str = str_replace('-', ' - ',$str);
        elseif  ($str[0] == '+')    $str = str_replace('+', ' + ',$str);
        else                        $str = ' + '.$str;

        return $str;
    }

    private function checkUser($mapId, $allowReviewers = false) {
        if (Auth::instance()->logged_in()) {
            if (Auth::instance()->get_user()->type->name != 'learner'){
                if (DB_ORM::model('map_user')->checkUserById($mapId, Auth::instance()->get_user()->id)) {
                    return TRUE;
                }

                $map = DB_ORM::model('map', array((int) $mapId));
                if ($map) {
                    if ($map->author_id == Auth::instance()->get_user()->id) {
                        return TRUE;
                    }
                }
                if(Auth::instance()->get_user()->type->name == 'superuser') {
                    return TRUE;
                }

                if ($allowReviewers){
                    if(Auth::instance()->get_user()->type->name == 'reviewer') {
                        return TRUE;
                    }
                }
            }

            return FALSE;
        }

        return FALSE;
    }

    private function getMatch($nodeId) {
        return NULL;
    }

    private function generateLinks ($node)
    {
        if (count($node->links) > 0)
        {
            $result = array();
            foreach ($node->links as $link)
            {
                switch ($node->link_type->name)
                {
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

            if ($node->link_type_id == 3)
            {
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

    public function addQuestionResponsesAndChangeCounterValues($mapID, $sessionId, $nodeId = null)
    {
        // array that contain info for counter debugger
        $c_debug = array();

        $questionChoices = Session::instance()->get('questionChoices');
        $questionChoices = ($questionChoices != NULL) ? json_decode($questionChoices, true) : NULL;

        $countersFunc = Session::instance()->get('countersFunc');
        $countersFunc = ($countersFunc != NULL) ? json_decode($countersFunc, true) : NULL;

        $counterIDs = array();
        if (isset($questionChoices['counter_ids']))
        {
            $counterIDs = $questionChoices['counter_ids'];
            unset($questionChoices['counter_ids']);
        }

        $counterString = $this->getCounterString($mapID);
        if (count($questionChoices) > 0){
            foreach($questionChoices as $qID => $questions){
                if (count($questions) > 0){
                    foreach($questions as $q){
                        DB_ORM::model('user_response')->createResponse($sessionId, $qID, $q['response'], $nodeId);
                        if (count($counterIDs) > 0){
                            $score = trim($q['score']);
                            $value = $this->getCounterValueFromString($counterIDs[$qID], $counterString);

                            //get info for c_debug
                            $c_debug[$counterIDs[$qID]]['previous_value'] = $value;
                            $c_debug[$counterIDs[$qID]]['question_value'] = $score;
                            $c_debug[$counterIDs[$qID]]['question_id']    = $qID;

                            $valueStr = (string)$score;
                            $value = $this->calculateCounterFunction($value, $score);

                            if (($valueStr[0] != '-') && ($valueStr[0] != '=')){
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
        if($sliderQuestionChoices != null && count($sliderQuestionChoices) > 0) {
            $slidersSum = 0;
            foreach($sliderQuestionChoices as $qID => $sliderValue) {
                $slidersSum += $sliderValue;
                DB_ORM::model('user_response')->createResponse($sessionId, $qID, $sliderValue, $nodeId);
                $question = DB_ORM::model('map_question', array((int)$qID));
                if ($question != null){
                    if(count($question->responses) > 0) {
                        foreach($question->responses as $response) {
                            if($sliderValue >= $response->from && $sliderValue <= $response->to) {
                                $score = $response->score;
                                $value = $this->getCounterValueFromString($question->counter->id, $counterString);

                                //get info for c_debug
                                $c_debug[$question->counter->id]['previous_value'] = $value;
                                $c_debug[$question->counter->id]['question_value'] = $score;
                                $c_debug[$question->counter->id]['question_id']    = $qID;

                                $valueStr = (string)$score;
                                $value = $this->calculateCounterFunction($value, $score);
                                if (($valueStr[0] != '-') && ($valueStr[0] != '=')){
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
        if($draggingQuestionResponses != null && count($draggingQuestionResponses) > 0) {
            foreach($draggingQuestionResponses as $responseJSON) {
                $responseObject = json_decode($responseJSON, true);
                if($responseObject == null) continue;

                if(isset($responseObject['id']) && isset($responseObject['responses'])) {
                    DB_ORM::model('user_response')->createResponse($sessionId, $responseObject['id'], json_encode($responseObject['responses']), $nodeId);
                }
            }

            Session::instance()->delete('dragQuestionResponses');
        }

        $this->updateCounterString($mapID, $counterString);

        Session::instance()->set('countersFunc', json_encode($countersFunc));

        $arrayAddedQuestions = Session::instance()->get('arrayAddedQuestions', array());
        if (count($arrayAddedQuestions) > 0) {
            foreach($arrayAddedQuestions as $questionId => $value)
            {
                $checkForResponseTextQuestion = DB_ORM::select('User_Response')->where('session_id', '=',$sessionId)->where('question_id', '=', $questionId)->query()->fetch(0);
                if ( ! $checkForResponseTextQuestion) $this->question($questionId, '', NULL, $nodeId);

                $question = DB_ORM::model('map_question', array((int) $questionId));

                if ($question->settings != '')  list($rule, $isCorrect) = json_decode($question->settings);

                $stopRules = Session::instance()->get('stopCommonRules', array());
                if ( ! in_array('QU_'.$questionId, $stopRules)) {
                    if ($question->settings != '' && $isCorrect == 1) {
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
                        $runtimelogic->questionResponse = NULL;

                        $array = $runtimelogic->parsingString($rule);
                        $resultLogic = $array['result'];
                        if (isset($resultLogic['goto'])){
                            if ($resultLogic['goto'] != NULL){
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
                        }

                        if (isset($resultLogic['counters'])){
                            if (count($resultLogic['counters']) > 0){
                                $counterString = $this->getCounterString($mapID);
                                if ($counterString != ''){
                                    foreach($resultLogic['counters'] as $key => $value){
                                        $previousValue = $this->getCounterValueFromString($key, $counterString);
                                        $counterString = $this->setCounterValueToString($key, $counterString, $value);

                                        $diff = $value - $previousValue;
                                        if ($diff > 0){
                                            $diff = '+'.$diff;
                                        }
                                        $countersFunc[$key][] = $diff;
                                    }
                                    $this->updateCounterString($mapID, $counterString);
                                }
                            }
                        }

                        if (isset($resultLogic['stop'])){
                            $stopRules[] = 'QU_'.$questionId;
                            Session::instance()->set('stopCommonRules', $stopRules);
                        }

                    }
                }
            }
        }

        $sctResponses = Session::instance()->get('sctResponses', array());
        foreach ($sctResponses as $idQuestion=>$idResponse)
        {
            DB_ORM::insert('User_Response')
                ->column('question_id', $idQuestion)
                ->column('response', $idResponse)
                ->column('session_id', $sessionId)
                ->column('node_id', $nodeId)
                ->execute();
        }
        Session::instance()->delete('sctResponses');

        Session::instance()->delete('arrayAddedQuestions');
        return $c_debug;
    }

    private function clearQuestionResponses(){
        Session::instance()->delete('questionChoices');
        Session::instance()->delete('countersFunc');
        Session::instance()->delete('sliderQuestionResponses');
    }

    private function counters ($traceId, $sessionId, $node, $isRoot = false) {
        if ($traceId != null && $node != NULL) {
            $counters = DB_ORM::model('map_counter')->getCountersByMap($node->map_id);
            if (count($counters) > 0) {
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

                $countersFunc = Session::instance()->get('countersFunc');
                $countersFunc = ($countersFunc != NULL) ? json_decode($countersFunc, true) : NULL;

                //$sliderQuestionChoices = Session::instance()->get('sliderQuestionResponses');

                foreach ($counters as $counter) {
                    // if exist main counter get ID of it
                    $if_main = $counter->status == 1;
                    if ($if_main) $main_counter['id'] = $counter->id;

                    $countersArray[$counter->id]['counter'] = $counter;
                    $currentCountersState = '';
                    if ($rootNode != NULL)
                    {
                        $currentCountersState = DB_ORM::model('user_sessionTrace')->getCounterByIDs($sessionId, $rootNode->map_id, $rootNode->id);
                        $oldCounter = $currentCountersState;
                    }
					
                    $label = $counter->name;
                    if ($counter->icon_id != 0) $label = '<img src="'.URL::base().$counter->icon->path.'">';
                    $countersArray[$counter->id]['label'] = $label;

                    $thisCounter = null;
                    if ($isRoot)
                    {
                        $thisCounter = $counter->start_value;
                        if ($if_main) $main_counter['value'] = $thisCounter;
                    }
                    elseif ($currentCountersState != '')
                    {
                        $s = strpos($currentCountersState, '[CID=' . $counter->id . ',') + 1;
                        $tmp = substr($currentCountersState, $s, strlen($currentCountersState));
                        $e = strpos($tmp, ']') + 1;
                        $tmp = substr($tmp, 0, $e - 1);
                        $tmp = str_replace('CID=' . $counter->id . ',V=', '', $tmp);
                        $thisCounter = $tmp;

                        // get current max counter value
                        if ($if_main)
                        {
                            preg_match('/(MCID=)(?<id>\d+),V=(?<value>\d+)/', $currentCountersState, $matches);
                            $main_counter['value'] = $matches['value'];
                        }
                    }

                    $c_debug[$counter->id]['previous_value'] = $thisCounter;

                    $counterFunction = '';
                    $appearOnNode = 1;

                    if (count($node->counters) > 0) {
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
                        if ($counterFunction[0] == '=') {
                            $thisCounter = substr($counterFunction, 1, strlen($counterFunction));
                        } else if ($counterFunction[0] == '-') {
                            $thisCounter -= substr($counterFunction, 1, strlen($counterFunction));
                        } else {
                            $thisCounter += $counterFunction;
                            // we need only positive values
                            if ($if_main) $main_counter['value'] += $counterFunction;
                        }
                    }

                    $countersArray[$counter->id]['value'] = $thisCounter;
                    if ($counterFunction != ''){
                        $countersArray[$counter->id]['func'][] = $counterFunction;
                    }

                    if (isset($countersFunc[$counter->id])){
                        if (count($countersFunc[$counter->id]) > 0){
                            if (isset($countersArray[$counter->id]['func'])){
                                if (count($countersArray[$counter->id]['func']) > 0){
                                    $countersArray[$counter->id]['func'] = array_merge($countersArray[$counter->id]['func'], $countersFunc[$counter->id]);
                                }
                            } else {
                                $countersArray[$counter->id]['func'] = $countersFunc[$counter->id];
                            }

                        }
                    }

                    if (($counter->visible != 0) & ($appearOnNode == 1)) {
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
                                // if main counter and firs spot not sign, add rule value
                                if($if_main AND (is_int( (int) $counterFunction[0]) OR $counterFunction[0]='+') ) $main_counter['value'] += $rule->counter_value;
                                $c_debug[$counter->id]['counter_rule_value'] = $rule->counter_value;
                                $redirect = $rule->redirect_node_id;
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
                    if (count($countersArray) > 0){
                        foreach($countersArray as $key => $counter){
                            $values[$key] = $counter['value'];
                        }
                    }
                    $runtimelogic = new RunTimeLogic();
                    $runtimelogic->values = $values;
                    $stopRules = Session::instance()->get('stopCommonRules', array());
                    foreach($commonRules as $rule){
                        if (!in_array('RULE_'.$rule->id, $stopRules)){
                            $array = $runtimelogic->parsingString($rule->rule);
                            $c_debug['global_rules'][$rule->id] = $array;
                            $resultLogic = $array['result'];

                            if (isset($resultLogic['goto'])){
                                if ($resultLogic['goto'] != NULL){
                                    if ($redirect == NULL){
                                        $redirect = $resultLogic['goto'];
                                    }
                                }
                            }
                            if (isset($resultLogic['counters']) AND count($resultLogic['counters']) > 0)
                            {
                                foreach($resultLogic['counters'] as $key => $c)
                                {
                                    $previousValue = $c;
                                    $funcStr = $c - $previousValue;
                                    if ($funcStr > 0) $funcStr = '+'.$funcStr;
                                    $countersArray[$key]['func'][] = $funcStr;
                                    $countersArray[$key]['value'] = $c;
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

                $visualDisplay = DB_ORM::model('map_visualdisplay')->getMapDisplaysShowOnAllPages($node->map_id);
                foreach($visualDisplay as $display) {
                    $counterString .= '<div class="visualDisplayCounterContainer" style="margin-bottom: 10px; position: relative; text-align: right">';
                    $counterString .= $this->getVisualDisplayHTML($display->id);
                    $counterString .= '</div>';
                }

                $counterString .="<ul class=\"navigation\">";
                foreach($countersArray as $counter)
                {
                    if (Arr::get($counter,'visible', FALSE))
                    {
                        $c_debug[$counter['counter']->id]['name'] = $counter['label'];
                        $c_debug[$counter['counter']->id]['current_value'] = $counter['value'];
                        $counterString .= '<li><a data-toggle="modal" href="#" data-target="#counter-debug">'.$counter['label'].'</a> ('.$counter['value'].')</li>';
                        $remoteCounterString .= '<counter id="'.$counter['counter']->id.'" name="'.$counter['counter']->name.'" value="'.$counter['value'].'"></counter>';
                    }
                    if (isset($counter['redirect']) AND $redirect == NULL) $redirect = $counter['redirect'];

                    $updateCounter .= '[CID='.$counter['counter']->id.',V='.$counter['value'].']';
                }
                $updateCounter .='[MCID='.$main_counter['id'].',V='.$main_counter['value'].']';
                $counterString .="</ul>";
                DB_ORM::model('user_sessionTrace')->updateCounter($sessionId, $node->map_id, $node->id, $oldCounter, $traceId);
                DB_ORM::model('user_sessionTrace')->updateCounter($sessionId, $rootNode->map_id, $rootNode->id, $updateCounter);

                if ($redirect != NULL && $redirect != $node->id){
                    Request::initial()->redirect(URL::base().'renderLabyrinth/go/'.$node->map_id.'/'.$redirect);
                }

                return array(
                    'c_debug' => $c_debug,
                    'counterString' => $counterString,
                    'redirect' => $redirect,
                    'remote' => $remoteCounterString,
                );
            }
            return '';
        }

        return '';
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

    private function calculateCounterFunction($counter, $function){
        if (strlen($function) > 0) {
            if ($function[0] == '=') {
                $counter = substr($function, 1, strlen($function));
            } else if ($function[0] == '-') {
                $counter -= substr($function, 1, strlen($function));
            } else {
                $counter += $function;
            }
        }
        return $counter;
    }

    private function getReviewLinks($sesionId)
    {
        $traces = DB_ORM::model('user_sessionTrace')->getTraceBySessionID($sesionId);

        if ($traces != NULL) return $traces;
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
                $response = htmlspecialchars(base64_decode($response));
                $sessionId = $this->getSessionID();
                DB_ORM::model('user_response')->createResponse($sessionId, $question->id, $response, $nodeId);

                $countersFunc = Session::instance()->get('countersFunc');
                $countersFunc = ($countersFunc != NULL) ? json_decode($countersFunc, true) : NULL;
                if ($question->settings != '') {
                    list($rule, $isCorrect) = json_decode($question->settings);
                }

                if ($question->settings != '' && $isCorrect == 1)
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
                    if (isset($resultLogic['goto'])){
                        if ($resultLogic['goto'] != NULL){
                            $nodes = DB_ORM::model('map_node')->getAllNode($mapID);
                            $inMap = false;

                            foreach ($nodes as $node) {
                                if ( $node->id == $resultLogic['goto']) {
                                    $inMap = true;
                                }
                            }

                            if ($inMap) {
                                $goto = Session::instance()->get('goto', NULL);

                                if ($goto == NULL) {
                                    Session::instance()->set('goto', $resultLogic['goto']);
                                }
                            }
                        }
                    }

                    if (isset($resultLogic['counters'])){
                        if (count($resultLogic['counters']) > 0){
                            $counterString = $this->getCounterString($mapID);
                            if ($counterString != ''){
                                foreach($resultLogic['counters'] as $key => $value){
                                    $previousValue = $this->getCounterValueFromString($key, $counterString);
                                    $counterString = $this->setCounterValueToString($key, $counterString, $value);

                                    $diff = $value - $previousValue;
                                    if ($diff > 0){
                                        $diff = '+'.$diff;
                                    }
                                    $countersFunc[$key][] = $diff;
                                }
                                $this->updateCounterString($mapID, $counterString);
                            }
                        }
                    }

                    Session::instance()->set('countersFunc', json_encode($countersFunc));

                    if (isset($resultLogic['correct'])){
                        $returnStr .= '<img src="' . URL::base() . 'images/tick.jpg"> ';
                    }

                    if (isset($resultLogic['incorrect'])){
                        $returnStr .= '<img src="' . URL::base() . 'images/cross.jpg"> ';
                    }
                }

                $arrayAddedQuestions = Session::instance()->get('arrayAddedQuestions', array());
                if (isset($arrayAddedQuestions[$questionId])) {
                    unset($arrayAddedQuestions[$questionId]);
                    Session::instance()->set('arrayAddedQuestions', $arrayAddedQuestions);
                }
            }

            if ($question->type->value == 'pcq'){
                $qChoices[$questionId] = array();
                $qChoices[$questionId][$response]['score'] = $responseObj->score;
                $qChoices[$questionId][$response]['response'] = $responseObj->response;

                $qChoices['counter_ids'][$questionId] = $question->counter_id;

                $arrayAddedQuestions = Session::instance()->get('arrayAddedQuestions', array());
                if (isset($arrayAddedQuestions[$questionId])) {
                    unset($arrayAddedQuestions[$questionId]);
                    Session::instance()->set('arrayAddedQuestions', $arrayAddedQuestions);
                }
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

                $qChoices['counter_ids'][$questionId] = $question->counter_id;

                $arrayAddedQuestions = Session::instance()->get('arrayAddedQuestions', array());
                if (isset($arrayAddedQuestions[$questionId])) {
                    unset($arrayAddedQuestions[$questionId]);
                    Session::instance()->set('arrayAddedQuestions', $arrayAddedQuestions);
                }
            }

            Session::instance()->set('questionChoices', json_encode($qChoices));

            if ($question->show_answer) {
                if ($question->type->value != 'text' and $question->type->value != 'area') {
                    switch ($responseObj->is_correct){
                        case 0:
                            $returnStr .= '<img src="' . URL::base() . 'images/cross.jpg"> ';
                            break;
                        case 1:
                            $returnStr .= '<img src="' . URL::base() . 'images/tick.jpg"> ';
                            break;
                    }
                    $returnStr .= ($responseObj->feedback != null && strlen($responseObj->feedback) > 0 ? ('(' . $responseObj->feedback . ')') : '');
                }
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

    public function getCounterValueByID($mapId, $id){
        $result = NULL;
        if ($mapId != NULL){
            $sessionId = $this->getSessionID();

            $counter = DB_ORM::model('map_counter', array($id));

            if (($sessionId != NULL) && ($sessionId != 'notExist')){
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
                    $result = $tmp;
                } else {
                    $result = $counter->start_value;
                }
            } else {
                $result = $counter->start_value;
            }
        }

        return $result;
    }

    public function getCounterString($mapId){
        $currentCountersState = '';
        if ($mapId != NULL){
            $sessionId = $this->getSessionID();

            $rootNode = DB_ORM::model('map_node')->getRootNodeByMap($mapId);
            if ($rootNode != NULL){
                $currentCountersState = DB_ORM::model('user_sessionTrace')->getCounterByIDs($sessionId, $rootNode->map_id, $rootNode->id);
            }

        }

        return $currentCountersState;
    }

    public function updateCounterString($mapId, $string){
        if ($mapId != NULL){
            $sessionId = $this->getSessionID();

            $rootNode = DB_ORM::model('map_node')->getRootNodeByMap($mapId);
            if ($rootNode != NULL){
                DB_ORM::model('user_sessionTrace')->updateCounter($sessionId, $rootNode->map_id, $rootNode->id, $string);
            }
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

    public function getSessionID(){
        $sessionId = Session::instance()->get('session_id', NULL);
        if ($sessionId == NULL && isset($_COOKIE['OL'])) {
            $sessionId = $_COOKIE['OL'];
        } else {
            if ($sessionId == NULL){
                $sessionId = 'notExist';
            }
        }
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
