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

                    $posAdditionalIF = strpos($then, 'IF');
                    if ($posAdditionalIF !== false){
                        $then = trim(substr($string, $posTHEN + 5, strlen($string) - $posTHEN));
                        $posENDIF = strpos($then, 'ENDIF');
                        if ($posENDIF !== false){
                            $newStr = trim(substr($then, $posAdditionalIF, $posENDIF));
                            $newResult = $this->parsingString($newStr);
                            $dataArray = $newResult['result'];
                            $errors = $newResult['errors'];
                        } else {
                            $errors[] = 'ENDIF missed condition';
                        }
                    } else {
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
            $comparisonPart = $this->transformationOfComparisonPart($str);

            if ($comparisonPart['operation'] == '!='){
                if ($comparisonPart['part1'] != $comparisonPart['part2']){
                    $array['result'] = 1;
                }else{
                    $array['result'] = 0;
                }
            }elseif ($comparisonPart['operation'] == '>='){
                if ($comparisonPart['part1'] >= $comparisonPart['part2']){
                    $array['result'] = 1;
                }else{
                    $array['result'] = 0;
                }
            }elseif ($comparisonPart['operation'] == '<='){
                if ($comparisonPart['part1'] <= $comparisonPart['part2']){
                    $array['result'] = 1;
                }else{
                    $array['result'] = 0;
                }
            }elseif ($comparisonPart['operation'] == '>'){
                if ($comparisonPart['part1'] > $comparisonPart['part2']){
                    $array['result'] = 1;
                }else{
                    $array['result'] = 0;
                }
            }elseif ($comparisonPart['operation'] == '<'){
                if ($comparisonPart['part1'] < $comparisonPart['part2']){
                    $array['result'] = 1;
                }else{
                    $array['result'] = 0;
                }
            }elseif($comparisonPart['operation'] == '='){
                if ($comparisonPart['part1'] == $comparisonPart['part2']){
                    $array['result'] = 1;
                }else{
                    $array['result'] = 0;
                }
            }
        }
        return $array;
    }

    public function transformationOfComparisonPart($str){
        $array = array();
        $operations = array('!=' => 2, '>=' => 2, '<=' => 2, '>' => 1, '<' => 1, '=' => 1);
        $findOP = false;
        foreach($operations as $op => $len){
            $pos = strpos($str, $op);
            if ($pos !== false){
                $findOP = true;
                $array['operation'] = $op;
                break;
            }
        }
        if ($findOP){
            $part1 = substr($str, 0, $pos - 1);
            $part1 = trim($this->replaceCounterByValue($part1));
            $array['part1'] = $this->calculatePart($part1);
            $part2 = substr($str, $pos + $len, strlen($str));
            $part2 = trim($this->replaceCounterByValue($part2));
            $array['part2'] = $this->calculatePart($part2);
        }
        return $array;
    }

    public function calculatePart($str){
        $countOperations = $this->findAllOperations($str);
        for($i = 0; $i < $countOperations; $i++){
            $opArray = $this->findOperationByWeight($str);
            $firstStrPart = substr($str, 0, $opArray['pos']);
            $firstOperPos = $this->findOperation($firstStrPart, true);
            $secondStrPart = substr($str, $opArray['pos'] + strlen($opArray['oper']), strlen($str));
            $secondOperPos = $this->findOperation($secondStrPart, false, false);
            if ($secondOperPos == 0){
                $secondOperPos = strlen($secondStrPart);
            }
            $firstValue = trim(substr($firstStrPart, $firstOperPos, strlen($firstStrPart)));
            $firstStringCheck = $this->getStringValue($firstValue);
            if ($firstStringCheck != null){
                $firstSendValue = $firstStringCheck;
            } else {
                $firstSendValue = $firstValue;
            }
            $secondValue = trim(substr($secondStrPart, 0, $secondOperPos));
            $secondStringCheck = $this->getStringValue($secondValue);
            if ($secondStringCheck != null){
                $secondSendValue = $secondStringCheck;
            } else {
                $secondSendValue = $secondValue;
            }

            $result = $this->algorithmicOperation($opArray['oper'], $firstSendValue, $secondSendValue);
            $str = str_replace($firstValue.$opArray['oper'].$secondValue, $result, $str);
        }
        return $str;
    }

    public function replaceCounterByValue($str){
        $countCR = substr_count($str, "[[CR:");
        for($i = 0; $i < $countCR; $i++){
            $posCR = strpos($str, '[[CR:');
            $posCRClose = strpos($str, ']]');
            $id = substr($str, $posCR + 5, ($posCRClose - $posCR - 5));
            $str = str_replace('[[CR:'.$id.']]', $this->values[$id], $str);
        }
        return $str;
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

    public function findOperation($str, $last = false, $plusLen = true){
        $firstPos = 0;
        $operations = array(' * ', ' / ', ' + ', ' - ', ' MOD ', ' DIV ');
        foreach($operations as $op){
            if (!$last){
                $pos = strpos($str, $op);
            } else {
                $pos = strrpos($str, $op);
            }
            if ($pos !== false){
                if ($plusLen){
                    $pos += strlen($op);
                }
                if ($firstPos == null){
                    $firstPos = $pos;
                } else {
                    if (!$last){
                        if ($firstPos > $pos){
                            $firstPos = $pos;
                        }
                    } else {
                        if ($firstPos < $pos){
                            $firstPos = $pos;
                        }
                    }
                }
            }
        }
        return $firstPos;
    }

    public function findOperationByWeight($str){
        $firstPos = null;
        $oper = null;
        $operWeight = null;
        $pos = null;
        $operations = array(
            ' * ' => array('weight' => 2),
            ' / ' => array('weight' => 2),
            ' + ' => array('weight' => 1),
            ' - ' => array('weight' => 1),
            ' MOD ' => array('weight' => 3),
            ' DIV ' => array('weight' => 3));
        foreach($operations as $op => $values){
            $pos = strpos($str, $op);
            if ($pos !== false){
                if ($firstPos == null){
                    $oper = $op;
                    $firstPos = $pos;
                    $operWeight = $values['weight'];
                } else {
                    if ($operWeight < $values['weight']){
                        $firstPos = $pos;
                        $oper = $op;
                        $operWeight = $values['weight'];
                    }
                }
            }
        }
        $array['pos'] = $firstPos;
        $array['oper'] = $oper;
        return $array;
    }

    public function algorithmicOperation($str, $result, $value){
        $str = trim($str);
        if (!empty($str)){
            if (strpos($str, '*') !== false){
                $result = $result * $value;
            }elseif(strpos($str, '/') !== false){
                $result = round($result / $value, 1);
            }elseif(strpos($str, '+') !== false){
                if (is_numeric($result)){
                    $result = $result + $value;
                } else {
                    $result = $result . $value;
                }
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
                $str = trim($this->replaceCounterByValue($str));
                $result = $this->calculatePart($str);
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
