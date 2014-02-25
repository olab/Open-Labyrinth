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

class Report_SCT_Question extends Report_SCT_Element {
    public $question;
    private $webinarId;
    private $webinarStep;
    private $notInUsers;
    private $dateStatistics;
    public $questionResponses;
    public $user_response;

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
    public function insert($columnOffset)
    {
        if ($this->implementation == null || $this->question == null) return 0;

        $statisticsModel = 'statistics_';
        if (is_null($this->dateStatistics)) $statisticsModel = '';

        $sessions      = DB_ORM::model($statisticsModel.'user_session')->getSessions($this->question->map_id, $this->webinarId, $this->webinarStep, $this->notInUsers);
        $responses     = DB_ORM::model($statisticsModel.'user_response')->getResponses($this->question->id, $sessions);
        $id_user       = DB_ORM::model('User_Session',$sessions)->user_id;
        $this->user_response[$id_user] = 0;

        return $columnOffset++;
    }

    /**
     * Get key node ID
     *
     * @return integer - node ID
     */
    public function getKey() {
        return $this->question->id;
    }

    private function calculateChoices($responses)
    {
        $data = array();

        if($responses == null || count($responses) <= 0) return $data;

        foreach ($responses as $response)
        {
            if($this->question->entry_type_id == 7)
            {
                if($this->questionResponses != null && count($this->questionResponses) > 0)
                {
                    foreach($this->questionResponses as $r)
                    {
                        if((int)$response->response >= (int)$r->from && (int)$response->response <= (int)$r->to)
                        {
                            $data[$response->node_id][$r->from.'-'.$r->to] = isset($data[$response->node_id][$r->from.'-'.$r->to]) ? ($data[$response->node_id][$r->from.'-'.$r->to] + 1)
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