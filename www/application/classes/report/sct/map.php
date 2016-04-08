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

class Report_SCT_Map extends Report_Element
{

    private $map;
    private $webinarId;
    private $expertWebinarId;
    private $questions;
    private $experts;
    private $includeUsers;
    private $latest;

    public function __construct(Report_Impl $impl, $mapId, $webinarId, $expertWebinarId, $sectionId, $latest = true)
    {
        parent::__construct($impl);

        if ($mapId <= 0) {
            return;
        }

        $this->map = DB_ORM::model('map', array((int)$mapId));
        $this->webinarId = $webinarId;
        $this->expertWebinarId = $expertWebinarId;
        $this->questions = array();
        $this->experts = array();
        $this->includeUsers = array();
        $this->sectionId = $sectionId;
        $this->latest = $latest;

        $this->loadElements();
    }

    /**
     * Insert element into report
     *
     * @return integer
     */
    public function insert($offset, $filename = null)
    {
        $localRow = $offset;
        $column = 0;
        $firstColumnCounter = 0;

        if ($this->map == null) {
            return $localRow;
        }

        // scenario title
        $this->fillCell($column, $localRow, $this->map->name, 16);
        $localRow++;

        // displayed scenario from where taken  experts
        $this->fillCell($column, $localRow,
            'Experts from \'' . DB_ORM::model('Webinar', array($this->expertWebinarId))->title . '\' scenario.');
        $localRow += 2;

        // get all nodes
        $nodesObj = $this->sectionId
            ? DB_ORM::select('Map_Node_Section_Node')->where('section_id', '=', $this->sectionId)->query()->as_array()
            : DB_ORM::select('Map_Node')->where('map_id', '=', $this->map->id)->query()->as_array();

        // get all SCT question by map id
        $questions = DB_ORM::model('map_question')->getQuestionsByMapAndTypes($this->map->id, array(7));

        foreach ($nodesObj as $nodeObj) {
            $nodeName = $this->sectionId
                ? DB_ORM::model('Map_Node', array($nodeObj->node_id))->title
                : $nodeObj->title;
            $nodeId = $this->sectionId
                ? $nodeObj->node_id
                : $nodeObj->id;

            foreach ($questions as $question) {
                $firstColumnCounter++;
                $localTablesRow = $localRow + 8;
                $lastUserResponse = 0;
                $atLeastOneResponse = 0;

                $responseScore = $this->getScoreForEachResponse($question->id, $nodeId);

                // ----- create second table ----- //
                $this->fillCell(0, $localTablesRow, 'Reference panel score weights');
                $headerRow = $localTablesRow;

                if ($firstColumnCounter == 1) {
                    $column++;
                }
                $column++;
                $localTablesRow++;

                foreach ($this->includeUsers as $userId) {
                    $lastUserResponse++;

                    $sessionObj = DB_ORM::select('User_Session')
                        ->where('user_id', '=', $userId)
                        ->where('map_id', '=', $this->map->id)
                        ->where('webinar_id', '=', $this->webinarId)
                        ->order_by('id', $this->latest ? 'DESC' : 'ASC')
                        ->limit(1)
                        ->query()
                        ->as_array();

                    $sessionObj = Arr::get($sessionObj, 0, false);

                    $userResponse = 'no response';
                    if ($sessionObj) {
                        $userResponseObj = DB_ORM::select('User_Response')
                            ->where('question_id', '=', $question->id)
                            ->where('node_id', '=', $nodeId)
                            ->where('session_id', '=', $sessionObj->id)
                            ->limit(1)
                            ->query()
                            ->fetch(0);
                        if ($userResponseObj) {
                            $atLeastOneResponse++;
                            $userResponse = $userResponseObj->response;
                        }
                    }

                    if ($atLeastOneResponse == 0 AND $lastUserResponse == count($this->includeUsers)) {
                        $column--;
                        continue 2;
                    }

                    if ($userResponse != 'no response') {
                        $userResponse = Arr::get($responseScore, $userResponse, 0);
                    }

                    $this->fillCell(0, $localTablesRow, DB_ORM::model('User', array($userId))->nickname);
                    $this->fillCell($column, $localTablesRow, $userResponse);
                    $localTablesRow++;
                }

                $calculateColumn = $this->getNameFromNumber($column);

                $this->fillCell(1, $localTablesRow, 'STDEVA');
                $this->fillCell($column, $localTablesRow,
                    '=STDEVA(' . $calculateColumn . ($headerRow + 1) . ':' . $calculateColumn . ($localTablesRow - 1) . ')');
                $localTablesRow++;
                $this->fillCell(1, $localTablesRow, 'Average');
                $this->fillCell($column, $localTablesRow,
                    '=AVERAGE(' . $calculateColumn . ($headerRow + 1) . ':' . $calculateColumn . ($localTablesRow - 2) . ')');
                // ----- end create second table ----- //

                // ----- create first table ----- //
                $headerRow = $localRow;
                $localTablesRow = $localRow;
                $this->fillCell(0, $localRow, 'Reference panel score weights');
                $localTablesRow++;

                for ($i = 0; $i < 5; $i++) {
                    $this->fillCell(0, $localTablesRow + $i, 'Response' . $this->getNameFromNumber($i));
                }

                foreach ($responseScore as $score) {
                    $this->fillCell($column, $headerRow - 1, $nodeName . ': (ID -' . $nodeId . ')');
                    $this->fillCell($column, $headerRow, $question->stem . ': (ID -' . $question->id . ')');
                    $this->fillCell($column, $localTablesRow, $score);
                    $localTablesRow++;
                }

                $calculateColumn = $this->getNameFromNumber($column);

                $this->fillCell(1, $localTablesRow, 'STDEVA');
                $this->fillCell($column, $localTablesRow,
                    '=STDEVA(' . $calculateColumn . ($localRow + 1) . ':' . $calculateColumn . ($localTablesRow - 1) . ')');
                $localTablesRow++;
                $this->fillCell(1, $localTablesRow, 'Average');
                $this->fillCell($column, $localTablesRow,
                    '=AVERAGE(' . $calculateColumn . ($localRow + 1) . ':' . $calculateColumn . ($localTablesRow - 2) . ')');
                $offset = $localTablesRow + 2;
                // ----- end create first table ----- //
            }

            if (!empty($filename)) {
                $data = Controller_WebinarManager::getReportProgressData($filename);
                $counter = $data['counter'];
                $progress_filename = $data['progress_filename'];
                $counter++;

                file_put_contents($progress_filename, json_encode(array(
                    'is_done' => false,
                    'counter' => $counter,
                )));
            }
        }

        // clear last column
        for ($i = $offset; $i < $offset + count($this->includeUsers); $i++) {
            $this->fillCell($column + 1, $i, '');
        }

        return $offset + 4;
    }

    /**
     * Get key for searching or sorting
     *
     * @return mixed
     */
    public function getKey()
    {
        return $this->map->id;
    }

    /**
     * Load map elements
     */
    private function loadElements()
    {
        if ($this->map == null OR count($this->map->nodes) <= 0) {
            return;
        }

        foreach (DB_ORM::select('Webinar_User')->where('webinar_id', '=', $this->expertWebinarId)->where('expert', '=',
            1)->query()->as_array() as $wUserObj) {
            $this->experts[] = $wUserObj->user_id;
        }
        foreach (DB_ORM::select('Webinar_User')->where('webinar_id', '=', $this->webinarId)->where('include_4r', '=',
            1)->query()->as_array() as $wUserObj) {
            $this->includeUsers[] = $wUserObj->user_id;
        }
    }

    public function getScoreForEachResponse($questionId, $nodeId)
    {
        $score = array();

        // get all response in correct order by question id
        foreach (DB_ORM::select('Map_Question_Response')->where('question_id', '=',
            $questionId)->order_by('order')->query()->as_array() as $response) {
            $score[$response->id] = 0;
        }

        // if response < 5, add
        for ($i = count($score); $i < 5; $i++) {
            $score[] = 0;
        }

        // get experts response
        foreach ($this->experts as $expertId) {
            $sessionObj = DB_ORM::select('User_Session')
                ->where('user_id', '=', $expertId)
                ->where('map_id', '=', $this->map->id)
                ->where('webinar_id', '=', $this->expertWebinarId)
                ->order_by('id', $this->latest ? 'DESC' : 'ASC')
                ->limit(1)
                ->query()
                ->as_array();

            $sessionObj = Arr::get($sessionObj, 0, false);

            if ($sessionObj) {
                $userResponse = DB_ORM::select('User_Response')
                    ->where('question_id', '=', $questionId)
                    ->where('node_id', '=', $nodeId)
                    ->where('session_id', '=', $sessionObj->id)
                    ->limit(1)
                    ->query()
                    ->fetch(0);

                if ($userResponse AND isset($score[$userResponse->response])) {
                    $score[$userResponse->response] += 1;
                }
            }
        }

        // calculate each score for response
        $max_score = max($score);
        foreach ($score as $response => $voters) {
            $score[$response] = ($max_score == 0) ? 0 : round($voters / $max_score, 2);
        }

        return $score;
    }
}