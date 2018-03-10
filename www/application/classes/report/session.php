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

/**
 * Class 4R Report
 */
class Report_Session extends Report {
    private $name;
    private $reportId;
    private $session;
    private $map;
    private $counters;
    private $questions;
    private $nodes;
    private $feedbacks;
    private $responses;
    private $startValueCounters;

    private $currentCursor;

    private $excelXPositions = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R');

    /**
     * Default constructor
     *
     * @param Report_Impl $impl - report class implementation
     * @param integer $reportId - report ID
     */
    public function __construct(Report_Impl $impl, $reportId) {
        parent::__construct($impl);

        $this->currentCursor = 1;
        $this->reportId      = $reportId;
        $this->session       = DB_ORM::model('user_session', array($this->reportId));
        $this->map           = $this->session->map;
        $this->counters      = DB_ORM::model('user_sessionTrace')->getCountersValues($this->session->id);
        $this->questions     = DB_ORM::model('map_question')->getQuestionsByMap($this->session->map_id);
        $this->nodes         = DB_ORM::model('map_node')->getNodesByMap($this->session->map_id);
        $this->name          = __('Labyrinth session "') . $this->session->map->name . '"' . ' user ' . $this->session->user->nickname;
        $this->feedbacks     = Model::factory('labyrinth')->getMainFeedback($this->session, $this->counters, $this->session->map_id);
        $this->responses     = array();
        $this->startValueCounters = array();

        $allCounters = DB_ORM::model('map_counter')->getCountersByMap($this->session->map_id);
        if ($allCounters != NULL and count($allCounters) > 0) {
            foreach ($allCounters as $counter) {
                $this->startValueCounters[$counter->id] = $counter->start_value;
            }
        }

        if ($this->questions != NULL) {
            foreach ($this->questions as $question) {
                $response = DB_ORM::model('user_response')->getResponse($this->session->id, $question->id);
                if ($response != NULL) {
                    $this->responses[$question->id] = $response;
                }
            }
        }
    }

    /**
     * Generate report
     *
     * @return mixed
     */
    public function generate() {
        if($this->implementation == null) return;

        $this->implementation->setCreator('OpenLabyrinth System');
        $this->implementation->setLastModifiedBy('OpenLabyrinth System');
        $this->implementation->setTitle('Session Report - ' . $this->reportId);
        $this->implementation->setSubject('Session Report');
        $this->implementation->setDescription('Session Report');
        $this->implementation->setKeywords('Session, User, Session Report, Report');
        $this->implementation->setCategory('Report');

        $this->implementation->setActiveSheet(0);

        $this->implementation->setCursor('A' . $this->currentCursor);
        $this->implementation->setFontSize('A' . $this->currentCursor, 16);
        $this->implementation->setValue($this->name);
        $this->currentCursor += 2;

        $this->setCommonInfoTable();
        $this->currentCursor++;

        $this->setFeedback();
        $this->currentCursor++;

        $this->setQuestionTable();
        $this->currentCursor++;

        $this->setNodesTime();
        $this->currentCursor++;

        $this->setCounters();
    }

    private function setCommonInfoTable() {
        $this->implementation->setCursor('A' . $this->currentCursor);
        $this->implementation->setValue('user');
        $this->implementation->setCursor('B' . $this->currentCursor);
        $this->implementation->setValue($this->session->user->nickname);
        $this->currentCursor++;

        $this->implementation->setCursor('A' . $this->currentCursor);
        $this->implementation->setValue('session');
        $this->implementation->setCursor('B' . $this->currentCursor);
        $this->implementation->setValue($this->session->id);
        $this->currentCursor++;

        $this->implementation->setCursor('A' . $this->currentCursor);
        $this->implementation->setValue('Labyrinth');
        $this->implementation->setCursor('B' . $this->currentCursor);
        $this->implementation->setValue($this->session->map->name . '(' . $this->session->map->id . ')');
        $this->currentCursor++;

        $this->implementation->setCursor('A' . $this->currentCursor);
        $this->implementation->setValue('start time');
        $this->implementation->setCursor('B' . $this->currentCursor);
        $this->implementation->setValue(date('Y.m.d H:i:s', $this->session->start_time));
        $this->currentCursor++;

        $this->implementation->setCursor('A' . $this->currentCursor);
        $this->implementation->setValue('time taken');
        $this->implementation->setCursor('B' . $this->currentCursor);
        $timeTaken = '';
        if (count($this->session->traces) > 0) {
            $max = $this->session->start_time;
            foreach($this->session->traces as $val) {
                if($val->end_date_stamp != null &&  $val->end_date_stamp > $max ) {
                    $max = $val->end_date_stamp;
                } else if($val->date_stamp > $max) {
                    $max = $val->date_stamp;
                }
            }
            $t = $max - $this->session->start_time;
            $timeTaken = date('i:s', $t);
        }
        $this->implementation->setValue($timeTaken);
        $this->currentCursor++;

        $this->implementation->setCursor('A' . $this->currentCursor);
        $this->implementation->setValue('nodes visited');
        $this->implementation->setCursor('B' . $this->currentCursor);

        $mustVisited = 0;
        $mustAvoid   = 0;
        if(count($this->session->traces) > 0) {
            foreach($this->session->traces as $trace) {
                if($trace->node->priority_id == 3) { $mustVisited++; }
                if($trace->node->priority_id == 2) { $mustAvoid++; }
            }
        }
        $this->implementation->setValue( count($this->session->traces) . ' nodes visited ' . __(' nodes visited altogether of which ') . $mustVisited . ' required nodes and ' . $mustAvoid . ' avoid nodes visited');
        $this->currentCursor++;

        if ($progress = DB_ORM::model('Map_Counter')->progress($this->session->traces['0']->counters, $this->session->map->id)) {
            $this->implementation->setCursor('A' . $this->currentCursor);
            $this->implementation->setValue('your score');
            $this->implementation->setCursor('B' . $this->currentCursor);
            $this->implementation->setValue($progress);
            $this->currentCursor++;
        }
    }

    private function setFeedback() {
        if(isset($this->feedbacks['general'])) {
            $this->implementation->setCursor('A' . $this->currentCursor);
            $this->implementation->setValue('general feedback');
            $this->implementation->setCursor('B' . $this->currentCursor);
            $this->implementation->setValue($this->feedbacks['general']);
            $this->currentCursor++;
        }

        if(isset($this->feedbacks['timeTaken']) and count($this->feedbacks['timeTaken']) > 0) {
            $this->implementation->setCursor('A' . $this->currentCursor);
            $this->implementation->setValue('feedback for time taken');
            $this->implementation->setCursor('B' . $this->currentCursor);
            $m = '';
            foreach($this->feedbacks['timeTaken'] as $msg) { $m .= $msg . ' '; }
            $this->implementation->setValue($m);
            $this->currentCursor++;
        }

        if(isset($this->feedbacks['nodeVisit']) and count($this->feedbacks['nodeVisit']) > 0) {
            $this->implementation->setCursor('A' . $this->currentCursor);
            $this->implementation->setValue('feedback for nodes visit');
            $this->implementation->setCursor('B' . $this->currentCursor);
            $m = '';
            foreach($this->feedbacks['nodeVisit'] as $msg) { $m .= $msg . ' '; }
            $this->implementation->setValue($m);
            $this->currentCursor++;
        }

        if(isset($this->feedbacks['mustVisit']) and count($this->feedbacks['mustVisit']) > 0) {
            $this->implementation->setCursor('A' . $this->currentCursor);
            $this->implementation->setValue('feedback for must visit');
            $this->implementation->setCursor('B' . $this->currentCursor);
            $m = '';
            foreach($this->feedbacks['mustVisit'] as $msg) { $m .= $msg . ' '; }
            $this->implementation->setValue($m);
            $this->currentCursor++;
        }

        if(isset($this->feedbacks['mustAvoid']) and count($this->feedbacks['mustAvoid']) > 0) {
            $this->implementation->setCursor('A' . $this->currentCursor);
            $this->implementation->setValue('feedback for must avoid');
            $this->implementation->setCursor('B' . $this->currentCursor);
            $m = '';
            foreach($this->feedbacks['mustAvoid'] as $msg) { $m .= $msg . ' '; }
            $this->implementation->setValue($m);
            $this->currentCursor++;
        }

        if(isset($this->feedbacks['counters']) and count($this->feedbacks['counters']) > 0) {
            $this->implementation->setCursor('A' . $this->currentCursor);
            $this->implementation->setValue('feedback for counters');
            $this->implementation->setCursor('B' . $this->currentCursor);
            $m = '';
            foreach($this->feedbacks['counters'] as $msg) { $m .= $msg . ' '; }
            $this->implementation->setValue($m);
            $this->currentCursor++;
        }
    }

    private function setQuestionTable() {
        if($this->questions == NULL) { return; }

        $this->implementation->setCursor('A' . $this->currentCursor);
        $this->implementation->setValue('Questions');
        $this->currentCursor++;

        $this->implementation->setCursor('A' . $this->currentCursor);
        $this->implementation->setValue('ID');
        $this->implementation->setCursor('B' . $this->currentCursor);
        $this->implementation->setValue('type');
        $this->implementation->setCursor('C' . $this->currentCursor);
        $this->implementation->setValue('stem');
        $this->implementation->setCursor('D' . $this->currentCursor);
        $this->implementation->setValue('response');
        $this->implementation->setCursor('E' . $this->currentCursor);
        $this->implementation->setValue('correct');
        $this->implementation->setCursor('F' . $this->currentCursor);
        $this->implementation->setValue('feedback');
        $this->currentCursor++;

        foreach($this->questions as $question) {
            $responseMap = array();
            if($question->type->value == 'dd' && count($question->responses) > 0) {
                foreach($question->responses as $r) {
                    $responseMap[$r->id] = $r;
                }
            }

            $this->implementation->setCursor('A' . $this->currentCursor);
            $this->implementation->setValue($question->id);
            $this->implementation->setCursor('B' . $this->currentCursor);
            $this->implementation->setValue($question->type->title);
            $this->implementation->setCursor('C' . $this->currentCursor);
            $this->implementation->setValue($question->stem);
            $this->implementation->setCursor('D' . $this->currentCursor);
            $responses = '';
            if(isset($this->responses[$question->id])) {
                if(count($this->responses[$question->id]) > 0) {
                    foreach($this->responses[$question->id] as $response){
                        if($question->type->value == 'dd') {
                            $jsonObj = json_decode($response->response, true);
                            if($jsonObj != null && count($jsonObj) > 0) {
                                foreach($jsonObj as $o) {
                                    if(isset($responseMap[$o])) {
                                        $responses .= $responseMap[$o]->response . ' ';
                                    }
                                }
                            }
                        } else {
                            $responses .= $response->response . ' ';
                        }
                    }
                } else {
                    $responses = 'no responses';
                }
            } else {
                $responses = 'no responses';
            }
            $this->implementation->setValue($responses);

            $this->implementation->setCursor('E' . $this->currentCursor);
            $correct = '';
            if($question->type->value != 'text' and $question->type->value != 'area' and $question->type->value != 'dd' ) {
                if(count($question->responses) > 0) {
                    foreach($question->responses as $resp) {
                        if($resp->is_correct == 1) {
                            $correct .= $resp->response . ' ';
                        }
                    }
                } else {
                    $correct = 'n/a';
                }
            } else {
                $correct = 'n/a';
            }
            $this->implementation->setValue($correct);

            $this->implementation->setCursor('F' . $this->currentCursor);
            $this->implementation->setValue($question->feedback);

            $this->currentCursor++;
        }
    }

    private function setNodesTime() {
        if (count($this->session->traces) <= 0) { return; }

        $this->implementation->setCursor('A' . $this->currentCursor);
        $this->implementation->setValue('node');
        $this->implementation->setCursor('B' . $this->currentCursor);
        $this->implementation->setValue('node ID');
        $this->implementation->setCursor('C' . $this->currentCursor);
        $this->implementation->setValue('time elapsed (in seconds)');
        $this->implementation->setCursor('D' . $this->currentCursor);
        $this->implementation->setValue('time spent on node');
        $this->currentCursor++;

        $startValuesCursor = $this->currentCursor;
        for ($i = 0; $i < count($this->session->traces); $i++) {
            $this->implementation->setCursor('A' . $this->currentCursor);
            $this->implementation->setValue($this->session->traces[$i]->node->title . '(' . $this->session->traces[$i]->node_id . ')');
            $this->implementation->setCursor('B' . $this->currentCursor);
            $this->implementation->setValue($this->session->traces[$i]->node->id);
            $this->implementation->setCursor('C' . $this->currentCursor);
            $this->implementation->setValue($this->session->traces[$i]->date_stamp - $this->session->start_time);
            $this->implementation->setCursor('D' . $this->currentCursor);
            $timeSpent = 0;
            if($this->session->traces[$i]->end_date_stamp != null) {
                $timeSpent = $this->session->traces[$i]->end_date_stamp - $this->session->traces[$i]->date_stamp;
            } else {
                $timeSpent = ($i > 0) ? ($this->session->traces[$i]->date_stamp - $this->session->traces[$i - 1]->date_stamp)
                                      : 0;
            }
            $this->implementation->setValue($timeSpent);

            $this->currentCursor++;
        }
        $endValuesCursor = $startValuesCursor;
        if($startValuesCursor < $this->currentCursor) { $endValuesCursor = $this->currentCursor - 1; }

        $this->addNodePathAnalysisChart($startValuesCursor, $endValuesCursor);
    }

    private function addNodePathAnalysisChart($start, $end) {
        $dataSeriesLabels = array(
            array('type'   => 'String',
                  'source' => 'Worksheet!$A$' . ($start - 1),
                  'format' => null,
                  'count'  => 1)
        );

        for($i = $start; $i <= $end; $i++) {
            $dataSeriesLabels[] = array('type'   => 'String',
                                        'source' => 'Worksheet!$B$' . $i,
                                        'format' => null,
                                        'count'  => 1);
        }

        $xAxisValues = array(
            array('type'   => 'String',
                  'source' => 'Worksheet!$B$' . $start . ':$B$' . $end,
                  'format' => null,
                  'count'  => $end - $start)
        );

        $dataSeriesValues = array(
            array('type'   => 'String',
                  'source' => 'Worksheet!$D$' . $start . ':$D$' . $end,
                  'format' => null,
                  'count'  => $end - $start)
        );

        $this->currentCursor++;

        $startPosition = 'A' . $this->currentCursor;
        $this->currentCursor += 12;
        $endPosition   = 'I' . $this->currentCursor;
        $this->currentCursor++;
        $this->implementation->addColumnBarChart($startPosition, $endPosition, $dataSeriesLabels, $xAxisValues, $dataSeriesValues, 'Node Path Analysis', 'time on node(s)');
    }

    private function setCounters() {
        $this->implementation->setCursor('A' . $this->currentCursor);
        $this->implementation->setValue('node ID');

        $i = $this->currentCursor + 1;
        foreach($this->session->traces as $trace) {
            $this->implementation->setCursor('A' . $i);
            $this->implementation->setValue($trace->node_id);
            $i++;
        }
        $this->implementation->setCursor('A' . $i);
        $this->implementation->setValue($this->session->traces[0]->node_id);

        $excelXAxisPosition = 1;
        $i = $excelXAxisPosition;
        foreach($this->counters as $counter) {
            $j = $this->currentCursor;
            $this->implementation->setCursor($this->excelXPositions[$i] . $j);
            $this->implementation->setValue($counter[0]);

            $j++;
            if((isset($this->startValueCounters[$counter[2]]))) {
                $this->implementation->setCursor($this->excelXPositions[$i] . $j);
                $this->implementation->setValue($this->startValueCounters[$counter[2]]);
                $j++;
            }

            if(isset($counter[1])) {
                if(count($counter[1]) > 0) {
                    for($k = 1; $k < count($counter[1]); $k++) {
                        $this->implementation->setCursor($this->excelXPositions[$i] . $j);
                        $this->implementation->setValue($counter[1][$k]);
                        $j++;
                    }
                }

                $this->implementation->setCursor($this->excelXPositions[$i] . $j);
                $this->implementation->setValue($counter[1][0]);
                $j++;
            }

            $i++;
        }

        $counterStartCursor  = $this->currentCursor;
        $this->currentCursor += count($this->session->traces) + 2;
        $this->currentCursor++;

        $startPosition = 'A' . $this->currentCursor;
        $endPosition   = 'I' . ($this->currentCursor + 12);

        $dataSeriesLabels = array();
        $dataSeriesValues = array();

        $excelXAxisPosition = 1;
        $i = $excelXAxisPosition;
        $j = $counterStartCursor;
        foreach($this->counters as $counter) {
            $dataSeriesLabels[] = array(
                'type'   => 'String',
                'source' => 'Worksheet!$' . $this->excelXPositions[$i] . '$' . $j,
                'format' => null,
                'count'  => 1
            );

            $dataSeriesValues[] = array(
                'type'   => 'Number',
                'source' => 'Worksheet!$' . $this->excelXPositions[$i] . '$' . ($counterStartCursor + 1) . ':$' . $this->excelXPositions[$i] . '$' . ($counterStartCursor + count($this->session->traces) + 1),
                'format' => null,
                'count'  => count($this->session->traces) + 1
            );

            $i++;
        }

        $xAxisValue = array(
            array(
                'type'   => 'String',
                'source' => 'Worksheet!$A$' . ($counterStartCursor + 1) . ':$A$' . ($counterStartCursor + count($this->session->traces) + 1),
                'format' => null,
                'count'  => count($this->session->traces) + 1
            )
        );

        $this->implementation->addLineChart($startPosition, $endPosition, $dataSeriesLabels, $xAxisValue, $dataSeriesValues, 'Counters', 'Values');
    }

    /**
     * Get report
     *
     * @return mixed
     */
    public function get() {
        if($this->implementation == null) return;

        $this->implementation->download($this->name);
    }


}