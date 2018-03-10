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
require_once('notification.php');

class cronRules
{
    private $rules;
    private $scenarioByMapId;
    private $refreshSessionTrace;
    private $connection;

    function __construct(mysqli $connection, $mapId = false){
        $this->connection = $connection;
        if ($mapId) {
            $this->fetchCronRule($mapId);
        } else {
            $this->fetchCronRules();
        }

        $this->executeRules($connection);
    }

    private function fetchCronRule($mapId){
        $query = "SELECT map_counter_common_rules.rule, map_counter_common_rules.id, map_counter_common_rules.map_id, cron.activate
                  FROM map_counter_common_rules
                  INNER JOIN cron
                  ON map_counter_common_rules.id=cron.rule_id
                  WHERE map_counter_common_rules.isCorrect = 1 AND map_counter_common_rules.map_id = $mapId";
        $this->rules = mysqli_query($this->connection, $query);
    }

    private function fetchCronRules(){
        $query = "SELECT map_counter_common_rules.rule, map_counter_common_rules.id, map_counter_common_rules.map_id, cron.activate
                  FROM map_counter_common_rules
                  INNER JOIN cron
                  ON map_counter_common_rules.id=cron.rule_id
                  WHERE map_counter_common_rules.isCorrect = 1";
        $this->rules = mysqli_query($this->connection, $query);
    }

    private function fetchScenariosIdByMapId($mapId){
        $query = "SELECT webinar_maps.webinar_id
                  FROM webinar_maps
                  INNER JOIN map_node_sections
                  ON webinar_maps.reference_id = map_node_sections.id
                  WHERE which = 'section' OR (reference_id = $mapId AND which = 'labyrinth')
                  GROUP BY webinar_maps.webinar_id";
        $result = mysqli_query($this->connection, $query);
        while ($row = mysqli_fetch_array($result)) {
            $this->scenarioByMapId[$mapId][] = $row['webinar_id'];
        }
    }

    private function executeRules(){
        while ($row = mysqli_fetch_array($this->rules)) {
            $rule           = $row['rule'];
            $ruleId         = $row['id'];
            $activate       = $row['activate'];
            $mapId          = $row['map_id'];
            $currentTime    = time();

            $this->fetchScenariosIdByMapId($mapId);

            if ($activate == NULL) {
                $this->prepareRule($rule, $mapId, $ruleId);
            } else if ($currentTime >= $activate) {
                $this->executeRule($rule, $mapId);
            }
        }
    }

    function prepareRule($string, $mapId, $id){
        $tryActivate = function ($ruleId, $time, $con) {
            $time = strtotime($time);
            $request = "UPDATE cron
                        SET activate = $time
                        WHERE rule_id = $ruleId AND activate IS NULL";
            mysqli_query($con,$request);
        };

        $pattern = 'ALERT\([^\)]*\),|\s*(ACTIVATE|DEACTIVATE)\s*\(\[\[STEP:\d+\]\],.*\)\s*,';
        if (preg_match_all("/".$pattern."/is", $string, $matches) AND count($matches[0])){
            foreach($matches[0] as $match){
                $activatePattern = '\s*(?<action>ACTIVATE|DEACTIVATE)\(\[\[STEP:(?<stepId>\d+)\]\],(?<activate>.[^\)]*)';
                if (preg_match("/".$activatePattern."/is", $match, $actions)) {
                    $stepId   = $actions['stepId'];
                    $activate = $actions['activate'];
                    if ($stepId AND $activate) {
                        $activate = trim($activate);
                        if ($activate == 'NOW') {
                            $this->executeRule($string, $mapId);
                        } elseif ($activate[0] == '+') {
                            $tryActivate($id, $activate.'minutes', $this->connection);
                        } else {
                            $tryActivate($id, $activate, $this->connection);
                        }
                    }
                }
            }
        }
    }

    private function executeRule($string, $mapId){
        // ----- parse if ----- //
        $parseIf = array();
        $pattern = '\s*IF\s*(?<condition>.*)\s*THEN';
        if (preg_match("/".$pattern."/is", $string, $matches)) {
            $parseIf = $this->assessCounters($matches['condition'], $mapId);
        }
        // ----- end parse if ----- //

        // ----- parse then ----- //
        $parseThen = '';
        $pattern = '\s*ALERT\(\s*"\s*(?<alert>[^"]*)"\),';
        if (preg_match("/".$pattern."/is", $string, $matches)) {
            $string = str_replace($matches[0], '', $string);
            $parseThen .= '$r["alert"] = "'.$matches['alert'].'";';
        }

        $pattern = '\s*(?<action>ACTIVATE|DEACTIVATE)\s*\(\[\[STEP:(?<step>\d+)\]\],.*\)\s*,';
        if (preg_match("/".$pattern."/is", $string, $matches)) {
            $string = str_replace($matches[0], '', $string);
            $parseThen .= ' $r["'.strtolower($matches['action']).'"] = "'.$matches['step'].'";';
        }

        $pattern = '\s(THEN)\s(?=\[\[COND|\[\[CR|BREAK|STOP)(.*?)(?=ELSE|ENDIF|;\s*IF|$)';
        if (preg_match("/".$pattern."/is", $string, $matches)) {
            $actionArray = explode(',', $matches[2]);
            foreach($actionArray as $action) {
                $parseThen .= $this->parseAction($action);
            }
        }

        // ----- end parse then ----- //
        foreach ($parseIf as $scenarioId => $if) {
            $result = eval('if('.$if.'){'.$parseThen.'}');
            if ($result) {
                if(isset($result['alert'])){
                    $query = "SELECT users.email
                              FROM users
                              INNER JOIN webinar_users
                              ON users.id = webinar_users.user_id
                              WHERE webinar_users.webinar_id = $scenarioId";
                    $eMails = mysqli_query($this->connection, $query);
                    $notification = new Notification();
                    while ($row = mysqli_fetch_array($eMails)) {
                        $notification->sendEMail($row['email'], $result['alert'], 'webmaster@example.com');
                    }
                    $notification->sendTwit($this->connection, $result['alert']);
                }

                if(isset($result['activate'])){
                    $this->changeStep($result['activate'], 'activate');
                }

                if(isset($result['deactivate'])){
                    $this->changeStep($result['deactivate'], 'deactivate');
                }

                if(isset($result['conditions']) AND count($result['conditions'])){
                    foreach($result['conditions'] as $id => $value){
                        $query = "UPDATE conditions_assign
                                  SET value = $value
                                  WHERE condition_id = $id AND scenario_id = $scenarioId";
                        mysqli_query($this->connection, $query);
                    }
                }

                if (isset($result['counters']) AND count($result['counters']) AND isset($this->refreshSessionTrace[$scenarioId])){
                    foreach ($this->refreshSessionTrace[$scenarioId] as $sessionTrace => $counterStr) {
                        foreach ($result['counters'] as $id => $value) {
                            $pattern = 'CID='.$id.',V=\d+';
                            $counterStr = preg_replace('/'.$pattern.'/is', 'CID='.$id.',V='.$value, $counterStr);
                        }

                        $query = "UPDATE user_sessiontraces
                                  SET counters = '$counterStr'
                                  WHERE id = $sessionTrace";
                        mysqli_query($this->connection, $query);
                    }
                }

                if(isset($result['break'])){

                }

                if(isset($result['stop'])){

                }
            }
        }
    }

    private function assessCounters($string, $mapId){
        $whichScenarioPerform = array();
        $conditionValue = NULL;
        $conditionId = NULL;
        $counterValue = NULL;
        $counterId = NULL;

        $conditionPattern = '\[\[COND:(?<id>\d+)\]\]\s*=\s*(?<value>\d+)';
        if(preg_match("/".$conditionPattern."/is", $string, $matches)) {
            $conditionValue = $matches['value'];
            $conditionId = $matches['id'];
        }

        $counterPattern = '\[\[CR:(?<id>\d+)\]\]\s*=\s*(?<value>\d+)';
        if(preg_match("/".$counterPattern."/is", $string, $matches)) {
            $counterValue = $matches['value'];
            $counterId = $matches['id'];
        }

        if (count($this->scenarioByMapId[$mapId])){
            foreach ($this->scenarioByMapId[$mapId] as $scenarioId){
                // ----- replace condition ----- //
                $query = "SELECT *
                          FROM conditions_assign
                          WHERE condition_id = $conditionId AND scenario_id = $scenarioId AND value = $conditionValue";
                $result = mysqli_query($this->connection, $query);
                $string = preg_replace("/".$conditionPattern."/is", $result->num_rows, $string);
                // ----- end replace condition ----- //

                // ----- get session by scenario ----- //
                $query = "SELECT user_sessiontraces.counters, user_sessiontraces.id
                          FROM user_sessions
                          INNER JOIN user_sessiontraces
                          ON user_sessions.id = user_sessiontraces.session_id
                          WHERE user_sessions.webinar_id = $scenarioId
                          GROUP BY user_sessiontraces.session_id";
                $result = mysqli_query($this->connection, $query);
                $counterReplacement = 0;
                while ($row = mysqli_fetch_array($result)) {
                    $countersStr = $row['counters'];
                    $pos = strpos($countersStr, "[CID=$counterId,V=$counterValue]");
                    if ($pos !== false) {
                        $counterReplacement = 1;
                        $this->refreshSessionTrace[$scenarioId][$row['id']] = $row['counters'];
                    }
                }
                // ----- end get session by scenario ----- //

                // ----- replace counters ----- //
                $counterPattern = '\[\[CR:'.$counterId.'\]\]\s*=\s*'.$counterValue;
                $string = preg_replace("/".$counterPattern."/is", $counterReplacement, $string);
                // ----- end replace counters ----- //

                $whichScenarioPerform[$scenarioId] = $string;
            }
        }
        return $whichScenarioPerform;
    }

    private function parseAction($str)
    {
        $posBREAK       = strpos($str, 'BREAK');
        $posSTOP        = strpos($str, 'STOP');

        if ($posBREAK !== false) {
            $returnString = ' $r["break"] = "1"; return $r; ';
        } elseif ($posSTOP !== false) {
            $returnString = ' $r["stop"] = "1"; return $r; ';
        } else {
            $pattern = '\[\[(?<counter>CR|COND):(?<id>\d+)\\]\\]\s*=(?<value>\d+)';
            preg_match("/".$pattern."/is", $str, $matches);
            $counter = $matches['counter'] == 'CR' ? 'counters' : 'conditions';
            $id      = $matches['id'];
            $value   = $matches['value'];
            $returnString = ' $r["'.$counter.'"]["'.$id.'"] = "'.$value.'"; ';
        }
        return $returnString;
    }

    public function changeStep($stepId, $action = 'activate'){
        $set = ($action == 'activate') ? $stepId : 0;
        $query = "UPDATE webinars
                  INNER JOIN webinar_steps ON webinars.id = webinar_steps.webinar_id
                  SET webinars.current_step = $set
                  WHERE webinar_steps.id = $stepId";
        mysqli_query($this->connection, $query);
    }
}
