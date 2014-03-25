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

class Report_Multi_Question extends Report_Multi_Element {
    private $webinarId;
    private $webinarStep;
    private $dateStatistics;
    public $question;
    public $questionResponses;
    public $user_response;

    /**
     * Default constructor
     *
     * @param Report_Impl $impl - report implementation
     */
    public function __construct(Report_Impl $impl, $questioId, $webinarId = null, $webinarStep = null, $dateStatistics = null)
    {
        parent::__construct($impl);

        $this->question             = DB_ORM::model('map_question', array((int)$questioId));
        $this->webinarId            = $webinarId;
        $this->webinarStep          = $webinarStep;
        $this->dateStatistics       = $dateStatistics;
        $this->questionResponses    = DB_ORM::model('map_question_response')->getResponsesByQuestion($questioId);
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

        $sessions      = DB_ORM::model($statisticsModel.'user_session')->getSessions($this->question->map_id, $this->webinarId, $this->webinarStep);
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
}