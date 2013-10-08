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

class Report_4R_Question extends Report_4R_Element {
    private $question;
    private $webinarId;
    private $webinarStep;
    private $notInUsers;
    private $dateStatistics;
    private $questionResponses;

    /**
     * Default constructor
     *
     * @param Report_Impl $impl - report implementation
     */
    public function __construct(Report_Impl $impl, $questioId, $webinarId = null, $webinarStep = null, $notInUsers = null, $dateStatistics = null) {
        parent::__construct($impl);

        $this->question    = DB_ORM::model('map_question', array((int)$questioId));
        $this->webinarId   = $webinarId;
        $this->webinarStep = $webinarStep;
        $this->notInUsers  = $notInUsers;
        $this->dateStatistics = $dateStatistics;
        $this->questionResponses = DB_ORM::model('map_question_response')->getResponsesByQuestion($questioId);
    }

    /**
     * Insert node row
     *
     * @return integer - offset
     */
    public function insert($offset) {
        if($this->implementation == null || $this->question == null) return 0;

        $statisticsModel = 'statistics_';
        if (is_null($this->dateStatistics)) {
            $statisticsModel = '';
        }

        $sessions      = DB_ORM::model($statisticsModel.'user_session')->getSessions($this->question->map_id, $this->webinarId, $this->webinarStep, $this->notInUsers);
        $responses     = DB_ORM::model($statisticsModel.'user_response')->getResponses($this->question->id, $sessions);

        $localOffset   = 0;
        $responsesData = $this->calculateChoices($responses);

        if($this->questionResponses != null && count($this->questionResponses) > 0) {
            if(count($responsesData) > 0) {
                foreach($responsesData as $nodeId => $data) {
                    $this->implementation->setCursor('A' . ($offset + $localOffset));
                    $this->implementation->setFontSize('A' . ($offset + $localOffset), 14);
                    $this->implementation->setURL($this->question->stem . '(ID - ' . $this->question->id . ')' . ' - ' . $nodeId, URL::base(true) . 'renderLabyrinth/go/' . $this->question->map_id . '/' . $nodeId);
                    $localOffset += 1;

                    $this->implementation->setCursor('A' . ($offset + $localOffset));
                    $this->implementation->setValue('Responses');
                    $this->implementation->setCursor('B' . ($offset + $localOffset));
                    $this->implementation->setValue('Count');
                    $localOffset += 1;

                    $start = $offset + $localOffset;
                    foreach($this->questionResponses as $questionResponse) {
                        $this->implementation->setCursor('A' . ($offset + $localOffset));

                        if($this->question->entry_type_id == 5) {
                            $this->implementation->setValue($questionResponse->from . ' - ' . $questionResponse->to);
                        } else {
                            $this->implementation->setValue($questionResponse->response);
                        }

                        $this->implementation->setCursor('B' . ($offset + $localOffset));

                        if($this->question->entry_type_id == 5) {
                            $this->implementation->setValue(isset($data[$questionResponse->from . '-' . $questionResponse->to]) ? $data[$questionResponse->from . '-' . $questionResponse->to] : 0);
                        } else {
                            $this->implementation->setValue(isset($data[$questionResponse->response]) ? $data[$questionResponse->response] : 0);
                        }

                        $localOffset += 1;
                    }
                    $countResponses = count($this->question->responses);
                    $end = $start;
                    if($countResponses > 0) {
                        $end += $countResponses - 1;
                    }

                    $endPosition = $countResponses * 3;
                    if($endPosition < 10) {
                        $endPosition = 14;
                    }

                    $this->implementation->addHorizontalBarChart('A' . ($offset + $localOffset + 1),
                                                                 'H' . ($offset + $localOffset + $endPosition),
                                                                 array(array('values' => 'Worksheet!$A$' . $start . ':$A$' . $end, 'count' => $countResponses)),
                                                                 array(array('values' => 'Worksheet!$A$' . $start . ':$A$' . $end, 'count' => $countResponses)),
                                                                 array(array('values' => 'Worksheet!$B$' . $start . ':$B$' . $end, 'count' => $countResponses)),
                                                                 $this->question->stem . ' - ' . $nodeId,
                                                                 'Count of choices');

                    $localOffset += $endPosition + 1;
                }
            } else {
                $this->implementation->setCursor('A' . ($offset + $localOffset));
                $this->implementation->setFontSize('A' . ($offset + $localOffset), 14);
                $this->implementation->setValue($this->question->stem . '(ID - ' . $this->question->id . ')');
                $localOffset += 1;

                $this->implementation->setCursor('A' . ($offset + $localOffset));
                $this->implementation->setValue('Responses');
                $this->implementation->setCursor('B' . ($offset + $localOffset));
                $this->implementation->setValue('Count');
                $localOffset += 1;

                $start = $offset + $localOffset;
                foreach($this->questionResponses as $questionResponse) {
                    $this->implementation->setCursor('A' . ($offset + $localOffset));

                    if($this->question->entry_type_id == 5) {
                        $this->implementation->setValue($questionResponse->from . ' - ' . $questionResponse->to);
                    } else {
                        $this->implementation->setValue($questionResponse->response);
                    }

                    $this->implementation->setCursor('B' . ($offset + $localOffset));
                    $this->implementation->setValue(0);

                    $localOffset += 1;
                }
                $countResponses = count($this->question->responses);
                $end = $start;
                if($countResponses > 0) {
                    $end += $countResponses - 1;
                }

                $endPosition = $countResponses * 3;
                if($endPosition < 10) {
                    $endPosition = 14;
                }

                $this->implementation->addHorizontalBarChart('A' . ($offset + $localOffset + 1),
                                                             'H' . ($offset + $localOffset + $endPosition),
                                                             array(array('values' => 'Worksheet!$A$' . $start . ':$A$' . $end, 'count' => $countResponses)),
                                                             array(array('values' => 'Worksheet!$A$' . $start . ':$A$' . $end, 'count' => $countResponses)),
                                                             array(array('values' => 'Worksheet!$B$' . $start . ':$B$' . $end, 'count' => $countResponses)),
                                                             $this->question->stem,
                                                             'Count of choices');

                $localOffset += $endPosition + 1;
            }
        }

        return $localOffset;
    }

    /**
     * Get key node ID
     *
     * @return integer - node ID
     */
    public function getKey() {
        return $this->question->id;
    }

    private function calculateChoices($responses) {
        $data = array();

        if($responses == null || count($responses) <= 0) return $data;

        foreach($responses as $response) {
            if($this->question->entry_type_id == 5) {
                if($this->questionResponses != null && count($this->questionResponses) > 0) {
                    foreach($this->questionResponses as $r) {
                        if((int)$response->response >= (int)$r->from && (int)$response->response <= (int)$r->to) {
                            $data[$response->node_id][$r->from . '-' . $r->to] = isset($data[$response->node_id][$r->from . '-' . $r->to]) ? ($data[$response->node_id][$r->from . '-' . $r->to] + 1)
                                                                                                                                           : 1;
                        }
                    }
                }
            } else {
                $data[$response->node_id][$response->response] = isset($data[$response->node_id][$response->response]) ? ($data[$response->node_id][$response->response] + 1)
                                                                                                                       : 1;
            }

        }

        return $data;
    }
}