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

class Report_Multi_Map extends Report_Multi_Element {

    private $map;
    private $sections;
    private $elements;
    private $questions;
    private $webinarId;
    private $webinarStep;
    private $dateStatistics;

    public function __construct(Report_Impl $impl, $mapId, $countOfChoices, $webinarId = null, $webinarStep = null, $dateStatistics = null) {
        parent::__construct($impl);

        if($mapId == null || $mapId <= 0) return;

        $this->map            = DB_ORM::model('map', array((int)$mapId));
        $this->countOfChoices = $countOfChoices;
        $this->webinarId      = $webinarId;
        $this->webinarStep    = $webinarStep;
        $this->dateStatistics = $dateStatistics;

        $this->elements       = array();
        $this->sections       = array();
        $this->questions      = array();

        $this->loadElements();
    }

    /**
     * Insert element into report
     *
     * @return integer
     */
    public function insert($row)
    {
        if ($this->map == null) return $row;

        $headerRow = $row;
        $row++;

        $include_users = DB_ORM::select('webinar_user')->where('webinar_id', '=', $this->webinarId)->where('include_4R', '=', 1)->query()->as_array();
        $questionIdForTable = array();

        // --- table user answers --- //
        foreach ($include_users as $wUser)
        {
            $userSession = DB_ORM::select('user_session')
                ->where('user_id', '=', $wUser->user_id)
                ->where('map_id', '=', $this->map->id)
                ->where('webinar_id', '=', $this->webinarId)
                ->query()
                ->as_array();

            if ( ! $userSession) continue;

            // get last session id
            $sessionId  = $userSession[count($userSession)-1]->id;
            $userNick   = DB_ORM::model('user', array($userSession[count($userSession)-1]->user_id))->nickname;
            $column     = 0;

            $this->fillCell($column, $row, $userNick);
            $column++;

            foreach ($this->questions as $multiQuestion)
            {
                $userResponseObj = DB_ORM::select('user_response')
                    ->where('question_id', '=', $multiQuestion->question->id)
                    ->where('session_id', '=', $sessionId)
                    ->query()
                    ->fetch(0);

                if (! $userResponseObj) continue;

                $questionIdForTable[] = $userResponseObj->question_id;

                $this->fillCell($column, $headerRow, 'Node ('.$userResponseObj->node_id.'). Question ('.$userResponseObj->question_id.')');
                $this->fillCell($column, $row, $userResponseObj->response);
                $column++;
            }
            $row++;
        }
        $row++;
        // --- end table user answers --- //

        // --- table question score --- //
        $this->fillCell(0, $row, 'Answers', 16);
        $row++;

        $headerRow = $row;
        $row++;

        $questionIdForTable = array_unique($questionIdForTable);
        asort($questionIdForTable);

        foreach ($questionIdForTable as $questionId)
        {
            $column         = 0;
            $questionObj    = DB_ORM::model('Map_Question', array($questionId));

            $this->fillCell($column, $row, $questionObj->id.') '.$questionObj->stem);
            $column++;

            foreach (DB_ORM::select(('Map_Question_Response'))->where('question_id', '=', $questionId)->query()->as_array() as $responseObj)
            {
                $innerColumn = $column;
                $this->fillCell($innerColumn, $headerRow, 'Responses');
                $this->fillCell($innerColumn, $row, $responseObj->response);
                $innerColumn++;
                $this->fillCell($innerColumn, $headerRow, 'Correct');
                $this->fillCell($innerColumn, $row, $responseObj->is_correct);
                $innerColumn++;
                $this->fillCell($innerColumn, $headerRow, 'Score');
                $this->fillCell($innerColumn, $row, $responseObj->score);
                $row++;
            }
            $row++;
        }
        // --- end table question score --- //

        return $row;
    }

    public function fillCell ($column, $row, $value, $fontSize = 12)
    {
        $column = $this->getNameFromNumber($column);

        $this->implementation->setAutoWidth($column);
        $this->implementation->setCursor($column.$row);
        $this->implementation->setValue($value);
        $this->implementation->setFontSize($column.$row, $fontSize);
    }

    /**
     * Get key for searching or sorting
     *
     * @return mixed
     */
    public function getKey() {
        return $this->map->id;
    }

    /**
     * Load map elements
     */
    private function loadElements()
    {
        if($this->map == null || $this->map->nodes == null || count($this->map->nodes) <= 0) return;

        $questions = DB_ORM::model('map_question')->getQuestionsByMapAndTypes($this->map->id, array(1,2,3,4,5,6,7));

        if( $questions != null && count($questions) > 0)
        {
            foreach($questions as $question)
            {
                $this->questions[] = new Report_Multi_Question(
                    $this->implementation,
                    $question->id,
                    $this->webinarId,
                    $this->webinarStep,
                    $this->dateStatistics
                );
            }
        }
    }

    /**
     * Sorting elements function
     *
     * @param Report_4R_Element $elementA - element A
     * @param Report_4R_Element $elementB - element B
     * @return integer - comparison flag
     */
    public static function sortElements(Report_SCT_Element $elementA, Report_SCT_Element $elementB) {
        $result = 0;

        if($elementA == null) $result = -1;
        else if($elementB == null) $result = 1;
        else {
            $keyA = $elementA->getKey();
            $keyB = $elementB->getKey();

            if($keyA > $keyB) $result = 1;
            else if($keyA < $keyB) $result = -1;
        }

        return $result;
    }
}