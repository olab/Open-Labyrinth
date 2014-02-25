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

class Report_SCT_Map extends Report_SCT_Element {
    private $map;
    private $sections;
    private $elements;
    private $questions;
    private $webinarId;
    private $webinarStep;
    private $notInUsers;
    private $dateStatistics;
    private $countOfChoices;
    public $scoreForResponse;
    public $experts;

    private $choicesMap = array(
        1 => 'C',
        2 => 'D',
        3 => 'E',
        4 => 'F'
    );

    public function __construct(Report_Impl $impl, $mapId, $countOfChoices, $webinarId = null, $webinarStep = null, $notInUsers = null, $dateStatistics = null, $experts = array()) {
        parent::__construct($impl);

        if($mapId == null || $mapId <= 0) return;

        $this->map            = DB_ORM::model('map', array((int)$mapId));
        $this->countOfChoices = $countOfChoices;
        $this->webinarId      = $webinarId;
        $this->webinarStep    = $webinarStep;
        $this->notInUsers     = $notInUsers;
        $this->dateStatistics = $dateStatistics;
        $this->experts        = $experts;

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
    public function insert($offset)
    {
        $localOffset = 0;
        $user_response = array();
        $amount_of_user = 0;

        if($this->map == null) return $localOffset;

        $this->implementation->setCursor('A' . $offset);
        $this->implementation->setFontSize('A' . $offset, 16);
        $this->implementation->setValue($this->map->name);
        $localOffset = 2;
        $columnOffset = 0; // start from letter B

        if($this->questions != null && count($this->questions) > 0)
        {
            // -------- insert response value table --------//
            for ($i=0; $i<5; $i++)
            {
                $this->implementation->setCursor('A'.($offset + $localOffset + $i + 1));
                $this->implementation->setValue('Response '.$this->getNameFromNumber($i));
                $this->implementation->setAutoWidth('A');
            }
            $eva_start_row  = $offset + $localOffset;
            $eva_end_column = 0;
            foreach ($this->questions as $question)
            {
                $response_indent = $columnOffset+2;
                $id_question = $question->question->id;
                $this->implementation->setCursor($this->getNameFromNumber($response_indent).($offset + $localOffset));
                $this->implementation->setFontSize($this->getNameFromNumber($response_indent).($offset + $localOffset), 12);
                $this->implementation->setValue($question->question->stem.' (ID - '.$id_question.')');
                $this->implementation->setAutoWidth($this->getNameFromNumber($response_indent));

                for ($i=0; $i<count($question->questionResponses); $i++)
                {
                    $id_response = $question->questionResponses[$i]->id;
                    $this->scoreForResponse();
                    $this->scoreForResponse[$id_question][$id_response];
                    $this->implementation->setCursor($this->getNameFromNumber($response_indent).($offset + $localOffset + $i + 1));
                    $this->implementation->setValue($this->scoreForResponse[$id_question][$id_response]);
                    $eva_end_row = $offset + $localOffset + $i + 1;
                }

                $columnOffset++;
                $eva_end_column = $columnOffset;

                // -------- question user value -------- //
                $all_sessions = DB_ORM::select('User_Response')->where('question_id', '=', $id_question)->query();
                foreach ($all_sessions as $session)
                {
                    $id_session = $session->session_id;
                    $id_user = DB_ORM::model('User_Session', array($id_session))->user_id;
                    $id_response = $session->response;
                    $user_response[$id_user][$id_question] = $this->scoreForResponse[$id_question][$id_response];
                }
                // -------- end question user value -------- //

            }

            // ----- create total ------//
            $this->implementation->setCursor($this->getNameFromNumber($columnOffset+2).($offset + $localOffset + 7));
            $this->implementation->setFontSize($this->getNameFromNumber($columnOffset+2).($offset + $localOffset + 7), 12);
            $this->implementation->setValue('Total');
            // ----- end create total ------//

            $columnOffset = 0;
            $average      = array();

            // ----- create STDEVA -------//
            $localOffset += 6;
            $eva_end_row = $eva_start_row + 5;

            $this->implementation->setCursor($this->getNameFromNumber($columnOffset + 1).($offset + $localOffset));
            $this->implementation->setValue('STDEVA');

            for ($i=2; $i<$eva_end_column+2; $i++)
            {
                $letter = $this->getNameFromNumber($i);
                $this->implementation->setCursor($this->getNameFromNumber($columnOffset + $i).($offset + $localOffset));
                $this->implementation->setValue('=STDEVA('.$letter.$eva_start_row.':'.$letter.$eva_end_row.')');
            }
            // ----- end create STDEVA -------//

            $localOffset += 2;
            $stdeva_start_row  = $offset + $localOffset;
            $stdeva_end_row    = 0;
            $stdeva_end_column = 0;
            foreach ($user_response as $id_user=>$value)
            {
                $amount_of_user = count($user_response);
                $i_for_user     = 0;
                $total          = 0;

                $this->implementation->setCursor($this->getNameFromNumber($columnOffset + $i_for_user).($offset + $localOffset));
                $this->implementation->setValue(DB_ORM::model('User', array($id_user))->nickname);
                $i_for_user+=2;
                foreach ($value as $k=>$v)
                {
                    $this->implementation->setCursor($this->getNameFromNumber($columnOffset + $i_for_user).($offset + $localOffset));
                    $this->implementation->setValue($v);
                    $total += $v;
                    $average[$k] = (isset($average[$k])) ? $average[$k]+$v : $v;
                    $i_for_user++;
                    $stdeva_end_column = $i_for_user;
                }

                $this->implementation->setCursor($this->getNameFromNumber($columnOffset + $i_for_user).($offset + $localOffset));
                $this->implementation->setValue($total);
                $stdeva_end_row = $offset+$localOffset;
                $localOffset++;
            }
            $columnOffset++;

            // ------ STDEVA ------//
            $this->implementation->setCursor($this->getNameFromNumber($columnOffset).($offset + $localOffset));
            $this->implementation->setValue('STDEVA');

            for ($i=2; $i<$stdeva_end_column; $i++)
            {
                $letter = $this->getNameFromNumber($i);
                $this->implementation->setCursor($this->getNameFromNumber($columnOffset + $i - 1).($offset + $localOffset));
                $this->implementation->setValue('=STDEVA('.$letter.$stdeva_start_row.':'.$letter.$stdeva_end_row.')');
            }
            // ------ END STDEVA ------//

            // --------- average ---------//
            $localOffset ++;
            $i_for_average = 0;

            $this->implementation->setCursor($this->getNameFromNumber($columnOffset + $i_for_average).($offset + $localOffset));
            $this->implementation->setValue('Average');

            foreach ($average as $v)
            {
                $this->implementation->setCursor($this->getNameFromNumber($columnOffset + $i_for_average + 1).($offset + $localOffset));
                $this->implementation->setValue($v/$amount_of_user);
                $i_for_average++;
            }

            // --------- end average ---------//
            $localOffset +=3;
            // -------- end insert response value table --------//
        }
        return $localOffset;
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

        $questions = DB_ORM::model('map_question')->getQuestionsByMapAndTypes($this->map->id, array(7));

        if($questions != null && count($questions) > 0)
        {
            foreach($questions as $question)
            {
                $this->questions[] = new Report_SCT_Question(
                    $this->implementation,
                    $question->id,
                    $this->webinarId,
                    $this->webinarStep,
                    $this->notInUsers,
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

    public function scoreForResponse ()
    {
        $score = array();
        foreach ($this->questions as $questions)
        {
            $id_question = $questions->question->id;
            foreach (DB_ORM::select('Map_Question_Response')->where('question_id', '=', $id_question)->order_by('order')->query() as $response)
            {
                $score[$id_question][$response->id] = 0;
            }

            $user_responses_db = DB_ORM::select('User_Response')->where('question_id', '=', $id_question)->query();
            foreach ($user_responses_db as $response)
            {
                $user_id = DB_ORM::model('User_Session', array($response->session_id))->user_id;
                if (in_array($user_id, $this->experts)) $score[$id_question][$response->response] += 1;
            }
            $max_score = max($score[$id_question]);
            foreach ($score[$id_question] as $k=>$v)
            {
                $this->scoreForResponse[$id_question][$k] = round($v / $max_score, 2);
            }

        }
    }
}