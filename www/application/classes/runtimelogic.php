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

class RunTimeLogic {

    var $values             = array();
    var $questionId         = null;
    var $questionResponse   = null;
    var $errors             = array();
    var $sessionId          = 0;

    public function parsingString($string, $sessionId = 0)
    {
        $this->sessionId = $sessionId;

        if ( ! empty($string))
        {
            $parseFullString = false;
            $finalResult     = array();
            $changedCounters = array();
            $errors          = array();
            $r               = array();
            $pattern         = '(.*?);\s*(?=IF|\s*$)';

            if ($c = preg_match_all("/".$pattern."/is", $string, $matches))
            {
                if (count($matches[0]) > 0)
                {
                    foreach($matches[0] as $newIF){
                        $resultCon = $this->replaceConditions($newIF);
                        $resultStr = $this->replaceFunctions($resultCon);
                        ob_start();
                        $result = eval($resultStr['str']);
                        $checkErrors = ob_get_contents();
                        ob_end_clean();

                        if ($checkErrors != '') $this->errors[] = $newIF;
                        else
                        {
                            if (isset($result['counters'])){
                                if (count($result['counters']) > 0){
                                    foreach($result['counters'] as $key => $value){
                                        $this->values[$key] = $value;
                                        $changedCounters[$key] = $value;
                                    }
                                }
                            }

                            $finalResult = array_merge($result, $finalResult);
                            if (isset($finalResult['stop'])){
                                if ($finalResult['stop'] == 1){
                                    break;
                                }
                            }

                            if (isset($finalResult['break'])){
                                if ($finalResult['break'] == 1){
                                    break;
                                }
                            }
                        }
                    }
                    $finalResult['counters'] = $changedCounters;
                } else {
                    $parseFullString = true;
                }
            } else {
                $parseFullString = true;
            }

            if ($parseFullString){
                $resultCon = $this->replaceConditions($string);
                $resultStr = $this->replaceFunctions($resultCon);
                ob_start();
                $finalResult = @eval($resultStr['str']);
                $checkErrors = ob_get_contents();
                ob_end_clean();
                if ($checkErrors != ''){
                    $this->errors[] = $string;
                }
            }

            if (empty($finalResult)){
                $finalResult = null;
            }
            $array['result'] = $finalResult;
            $array['errors'] = $this->errors;
            return $array;
        }
        return null;
    }

    public function parsingPatientRule($string)
    {
        if ( ! empty($string))
        {
            $parseFullString = false;
            $finalResult     = array();
            $changedCounters = array();
            $errors          = array();
            $r               = array();
            $pattern         = '(.*?);\s*(?=IF|\s*$)';

            if ($c = preg_match_all("/".$pattern."/is", $string, $matches))
            {
                if (count($matches[0]) > 0)
                {
                    foreach($matches[0] as $newIF){
                        $resultCon = $this->replaceConditions($newIF);
                        $resultStr = $this->replaceFunctions($resultCon);
                        ob_start();
                        $result = eval($resultStr['str']);
                        $checkErrors = ob_get_contents();
                        ob_end_clean();
                        if ($checkErrors != ''){
                            $this->errors[] = $newIF;
                        } else {
                            if (isset($result['counters'])){
                                if (count($result['counters']) > 0){
                                    foreach($result['counters'] as $key => $value){
                                        $this->values[$key] = $value;
                                        $changedCounters[$key] = $value;
                                    }
                                }
                            }

                            $finalResult = array_merge($result, $finalResult);
                            if (isset($finalResult['stop'])){
                                if ($finalResult['stop'] == 1){
                                    break;
                                }
                            }

                            if (isset($finalResult['break'])){
                                if ($finalResult['break'] == 1){
                                    break;
                                }
                            }
                        }
                    }
                    $finalResult['counters'] = $changedCounters;
                }
            }
            else
            {
                $resultCon = $this->replaceConditions($string);
                $resultStr = $this->replaceFunctions($resultCon);
                ob_start();
                $finalResult = @eval($resultStr['str']);
                $checkErrors = ob_get_contents();
                ob_end_clean();
                if ($checkErrors != '') $this->errors[] = $string;
            }

            if (empty($finalResult)) $finalResult = null;

            $array['result'] = $finalResult;
            $array['errors'] = $this->errors;
            return $array;
        }
        return null;
    }

    public function replaceFunctions($string)
    {
        $error          = false;
        $search         = array();
        $replace        = array();
        $onNode         = 1;
        $i              = 0;
        $previousNodeId = 0;

        $pattern = 'MATCH\s*\\(\\[\\[CR:(\d+)\\]\\].*?,.*?(".*?"|\'.*?\').*?,*(\d*|)\\)';
        if ($c=preg_match_all ("/".$pattern."/is", $string, $matches)){
            if (count($matches[0]) > 0){
                foreach($matches[0] as $key => $match){
                    $search[$i] = $match;
                    $register = ($matches[3][$key] == 1) ? 'strpos' : 'stripos';
                    $replace[$i] = ' ('.$register.'("'.$this->getValue($matches[1][$key]).'", '.$matches[2][$key].') !== false) ';
                    $i++;
                }
            }
        }

        $pattern = 'NOT-MATCH\s*\\(\\[\\[QU_ANSWER:*(\d+)*\\]\\].*?,.*?(".*?"|\'.*?\').*?,*(\d*|)\\)';
        if ($c=preg_match_all ("/".$pattern."/is", $string, $matches)){
            if (count($matches[0]) > 0){
                foreach($matches[0] as $key => $match){
                    $search[$i] = $match;
                    $questionAnswers = $this->getQUAnswer($matches[1][$key]);
                    if ($questionAnswers != '') {
                        if (gettype($questionAnswers) == 'array') {
                            $answerStrPos = $this->strposa($matches[2][$key], $questionAnswers);
                            $answerStrPos = !empty($answerStrPos) ? $answerStrPos : 0;

                            $replace[$i] = " ( $answerStrPos == false) ";
                        } else {
                            $register = ($matches[3][$key] == 1) ? 'strpos' : 'stripos';
                            $replace[$i] = ' ('.$register.'(\''.$questionAnswers.'\', '.$matches[2][$key].') === false OR \''.$questionAnswers.'\'!='.$matches[2][$key].')';
                        }
                    } else {
                        $replace[$i] = " (false) ";
                    }
                    $i++;
                }
            }
        }

        $pattern = 'MATCH\s*\\(\\[\\[QU_ANSWER:*(\d+)*\\]\\].*?,.*?(".*?"|\'.*?\').*?,*(\d*|)\\)';
        if ($c=preg_match_all ("/".$pattern."/is", $string, $matches)){
            if (count($matches[0]) > 0){
                foreach($matches[0] as $key => $match){
                    $search[$i] = $match;
                    $questionAnswersMatch = $this->getQUAnswer($matches[1][$key]);
                    if (gettype($questionAnswersMatch) == 'array') {
                        $answerStrPosMatch = $this->strposa($matches[2][$key], $questionAnswersMatch);
                        $answerStrPosMatch = !empty($answerStrPosMatch) ? $answerStrPosMatch : 0;

                        $replace[$i] = " ( $answerStrPosMatch != false) ";
                    } else {
                        $register = ($matches[3][$key] == 1) ? 'strpos' : 'stripos';
                        $replace[$i] = '('.$register.'(\''.$questionAnswersMatch.'\', '.$matches[2][$key].') !== false OR \''.$questionAnswersMatch.'\'=='.$matches[2][$key].')';
                    }
                    $i++;
                }
            }
        }

        $pattern = 'MATCH\s*\\(\\[\\[QU_ANSWER:*(\d+)*\\]\\].*?,.*?(".*?"|\'.*?\').*?,*(\d*|)\\)\s*ON_NODE\s*\((.*?[^)]*)';
        if ($c=preg_match_all ("/".$pattern."/is", $string, $matches)){
            if (count($matches[0]) > 0){
                foreach($matches[0] as $key => $match){
                    $search[$i] = $match;
                    $onNode = json_decode($matches[4][0], true);
                    $questionAnswersMatch = $this->getQUAnswer($matches[1][$key], $onNode);
                    if ($this->sessionId != 0)
                    {
                        $previousNodeId = DB_ORM::model('User_SessionTrace')->getPreviousTrace($this->sessionId);
                        $previousNodeId = $previousNodeId->node_id;
                    }
                    $onNode = (int) in_array($previousNodeId, $onNode);

                    if (gettype($questionAnswersMatch) == 'array') {
                        $answerStrPosMatch = $this->strposa($matches[2][$key], $questionAnswersMatch);
                        $answerStrPosMatch = !empty($answerStrPosMatch) ? $answerStrPosMatch : 0;

                        $replace[$i] = " ( $answerStrPosMatch != false) ";
                    } else {
                        $register = ($matches[3][$key] == 1) ? 'strpos' : 'stripos';
                        $replace[$i] = '('.$register.'(\''.$questionAnswersMatch.'\', '.$matches[2][$key].') !== false OR \''.$questionAnswersMatch.'\'=='.$matches[2][$key].')';
                    }
                    $i++;
                }
            }
        }

        $pattern = 'UPPER\s*\\(.*?\\[\\[CR:(\d+)\\]\\].*?\\)';
        if ($c=preg_match_all ("/".$pattern."/is", $string, $matches)){
            if (count($matches[0]) > 0){
                foreach($matches[0] as $key => $match){
                    $search[$i] = $match;
                    $replace[$i] = ' strtoupper("'.$this->getValue($matches[1][$key]).'") ';
                    $i++;
                }
            }
        }

        $pattern = 'LOWER\s*\\(.*?\\[\\[CR:(\d+)\\]\\].*?\\)';
        if ($c=preg_match_all ("/".$pattern."/is", $string, $matches)){
            if (count($matches[0]) > 0){
                foreach($matches[0] as $key => $match){
                    $search[$i] = $match;
                    $replace[$i] = ' strtolower("'.$this->getValue($matches[1][$key]).'") ';
                    $i++;
                }
            }
        }

        $pattern = 'PROPER\s*\\(.*?\\[\\[CR:(\d+)\\]\\].*?\\)';
        if ($c=preg_match_all ("/".$pattern."/is", $string, $matches)){
            if (count($matches[0]) > 0){
                foreach($matches[0] as $key => $match){
                    $search[$i] = $match;
                    $replace[$i] = ' ucfirst("'.$this->getValue($matches[1][$key]).'") ';
                    $i++;
                }
            }
        }

        $pattern = '\\[\\[CR:(\d+)\\]\\]\sDIV\s(\d+)';
        if ($c=preg_match_all ("/".$pattern."/is", $string, $matches)){
            if (count($matches[0]) > 0){
                foreach($matches[0] as $key => $match){
                    $search[$i] = $match;
                    $replace[$i] = ' intval("'.$this->getValue($matches[1][$key]).'" / '.$matches[2][$key].') ';
                    $i++;
                }
            }
        }

        $string = str_replace($search, $replace, $string);

        $pattern = '\sMOD\s';
        $string = preg_replace("/".$pattern."/", ' % ', $string);

        $pattern = '\\[\\[CR:(\d+)\\]\\]';
        $string = preg_replace_callback("/".$pattern."/is", array($this, 'replaceCounter'), $string);

        $pattern = '\\[\\[QU_ANSWER\\]\\]';
        $string = preg_replace_callback("/".$pattern."/", array($this, 'replaceQuestionAnswer'), $string);

        $pattern = '(?<=[^=|!|<|>])=(?=[^=])';
        $string = preg_replace("/".$pattern."/is", ' == ', $string);

        $pattern = '\s====\s';
        $string = preg_replace("/".$pattern."/is", ' = ', $string);

        $pattern = '\s*ON_NODE\s*\([^\)]*\)';
        $string = preg_replace("/".$pattern."/is", ' AND '.$onNode , $string);

        return array('str' => $string, 'error' => $error);
    }

    private function replaceCounter($matches){
        return '"'.$this->getValue($matches[1]).'"';
    }

    private function replaceQuestionAnswer($matches){
        return '\''.$this->getQUAnswer().'\'';
    }

    public function getValue($id){
        $value = 0;
        if (isset($this->values[$id])){
            $value = $this->values[$id];
        }
        return $value;
    }

    public function getQUAnswer($id = null, $nodesId = array())
    {
        $return = '';
        if ($id == null) $id = $this->questionId;

        if ($this->questionResponse != null) $return = $this->questionResponse;
        else
        {
            $sessionId = Session::instance()->get('session_id', $id);
            $responses = DB_ORM::model('user_response')->getResponse($sessionId, $id, $nodesId);
            $numberOfResponses = count($responses);

            if ($responses != null && $numberOfResponses > 1) {
                foreach ($responses as $key => $value) {
                    $return[] = $value->response;
                }
            } elseif ($numberOfResponses == 1) {
                foreach ($responses as $key => $value) {
                    $return = $value->response;
                }
            }
        }
        return $return;
    }

    public function replaceConditions($string)
    {
        $pattern = '\s(THEN)\s(?=\\[\\[CR|BREAK|STOP|GOTO|NO\\-ENTRY|CORRECT|INCORRECT)(.*?)(?=ELSE|ENDIF|;\s*IF|$)';
        $string = preg_replace_callback("/".$pattern."/is", array($this, 'replaceThen'), $string);

        $pattern = '\s(ELSE)\s(?=\\[\\[CR|BREAK|STOP|GOTO|NO\\-ENTRY|CORRECT|INCORRECT)(.*?)(?=ENDIF|;\s*IF|$)';
        $string = preg_replace_callback("/".$pattern."/is", array($this, 'replaceThen'), $string);

        $pattern = '\sENDIF\s';
        $string = preg_replace("/".$pattern."/", ' } ', $string);

        $pattern = '\sELSEIF\s';
        $string = preg_replace("/".$pattern."/", ' } elseif ( ', $string);

        $pattern = '\sELSE\s';
        $string = preg_replace("/".$pattern."/", ' } else { ', $string);

        $pattern = ';\s*IF\s';
        $string = preg_replace("/".$pattern."/", ' } if ( ', $string);

        $pattern = '\s*IF\s';
        $string = preg_replace("/".$pattern."/", ' if ( ', $string);

        $pattern = '\sTHEN\s';
        $string = preg_replace("/".$pattern."/", ' ) { ', $string);

        return $string. ' } return $r; ';
    }

    private function replaceThen($matches){
        $resultStr = null;
        $actionArray = explode(',', $matches[2]);

        if (count($actionArray) > 0){
            foreach($actionArray as $action){
                $resultStr .= $this->parseAction($action);
            }
        }
        if ($matches[1] == 'THEN'){
            $resultStr = ' ) { '.$resultStr;
        } else{
            $resultStr = ' } else { '.$resultStr;
        }
        return $resultStr;
    }

    public function parseAction($str){
        $startStr = $str;
        $returnString = '';
        $posCR = strpos($str, '[[CR:');
        $posGOTO = strpos($str, 'GOTO');
        $posNOENTRY = strpos($str, 'NO-ENTRY');
        $posBREAK = strpos($str, 'BREAK');
        $posSTOP = strpos($str, 'STOP');
        $posCorrect = strpos($str, 'CORRECT');
        $posIncorrect = strpos($str, 'INCORRECT');
        if ($posCR !== false){
            $result = null;
            $id = null;
            $pattern = '\\[\\[CR:(\d+)\\]\\]\s*=';
            if ($c=preg_match_all ("/".$pattern."/is", $str, $matches)){
                if (count($matches[0]) > 0){
                    $id = $matches[1][0];
                }
            }
            $posEqual = strpos($str, '=');
            if ($posEqual !== false){
                $str = substr($str, $posEqual + 1, strlen($str));
                $resultArray = $this->replaceFunctions($str);
                if ($resultArray['error'] == false){
                    ob_start();
                    $result = eval('return '.$resultArray['str'].';');
                    $checkErrors = ob_get_contents();
                    ob_end_clean();
                    if ($checkErrors != ''){
                        $this->errors[] = $startStr;
                    }

                }
            }

            $returnString = ' $r["counters"]["'.$id.'"] ==== "'.$result.'"; ';
        } elseif ($posGOTO !== false){
            $id = null;

            $pattern = 'GOTO\s*\\[\\[NODE:(\d+)\\]\\]';
            if ($c=preg_match_all ("/".$pattern."/is", $str, $matches)){
                if (count($matches[0]) > 0){
                    $id = $matches[1][0];
                }
            }
            $returnString = ' $r["goto"] ==== "'.$id.'"; ';
        } elseif ($posNOENTRY !== false) {
            $returnString = ' $r["no-entry"] ==== "1"; ';
        } elseif ($posBREAK !== false){
            $returnString = ' $r["break"] ==== "1"; return $r; ';
        } elseif ($posSTOP !== false){
            $returnString = ' $r["stop"] ==== "1"; return $r; ';
        } elseif ($posIncorrect !== false){
            $returnString = ' $r["incorrect"] ==== "1"; ';
        } elseif ($posCorrect !== false){
            $returnString = ' $r["correct"] ==== "1"; ';
        }
        return $returnString;
    }

    public function strposa($haystack, $needle, $offset=0) {
        if (is_array($needle)) {
            foreach ($needle as $key => $value) {
                if (strpos($haystack, $value) !== false) {
                    return true;
                }
            }
        }else {
            if (strpos($haystack, $needle) !== false) {
                return true;
            }
        }
    }
}