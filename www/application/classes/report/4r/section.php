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

class Report_4R_Section extends Report_4R_Element {
    private $mapId;
    private $sectionId;
    private $section;
    private $countOfChoices;
    private $webinarId;
    private $webinarStep;
    private $sectionNodes;
    private $notInUsers;
    private $dateStatistics;
    private $startOffset;

    private $choicesMap = array(
        1 => 'C',
        2 => 'D',
        3 => 'E',
        4 => 'F'
    );

    /**
     * Default constructor
     *
     * @param Report_Impl $impl - report implementation
     */
    public function __construct(Report_Impl $impl, $mapId, $sectionId, $countOfChoices, $webinarId = null, $webinarStep = null, $notInUsers = null,$dateStatistics = null) {
        parent::__construct($impl);

        $this->mapId          = $mapId;
        $this->sectionId      = $sectionId;
        $this->section        = DB_ORM::model('map_node_section', array((int)$this->sectionId));
        $this->countOfChoices = $countOfChoices;
        $this->webinarId      = $webinarId;
        $this->webinarStep    = $webinarStep;
        $this->notInUsers     = $notInUsers;
        $this->dateStatistics = $dateStatistics;

        $this->loadSectionNodes();
    }

    /**
     * Insert node row
     *
     * @return integer - offset
     */
    public function insert($offset) {
        if($this->implementation == null || $this->sectionNodes == null || count($this->sectionNodes) <= 0) return 0;

        $this->startOffset   = $offset;
        $countOfSectionNodes = count($this->sectionNodes);
        $localOffset         = 0;


        $statisticsModel = 'statistics_';
        if (is_null($this->dateStatistics)) {
            $statisticsModel = '';
        }

        $sessions            = DB_ORM::model($statisticsModel.'user_session')->getSessions($this->mapId,
                                                                          $this->webinarId,
                                                                          $this->webinarStep,
                                                                          $this->notInUsers,
                                                                          $this->dateStatistics);
        $traces              = ($sessions != null && count($sessions) > 0) ? DB_ORM::model($statisticsModel.'user_sessionTrace')->getUniqueTraceBySessions($sessions)
                                                                           : null;
        $tracesData          = $this->calculateSectionData($traces);

        $this->implementation->setCursor('B' . $offset);
        $this->implementation->setValue($countOfSectionNodes);

        foreach($this->sectionNodes as $sectionNode) {
            $firstChoiceValue = 0;

            $this->implementation->setCursor('A' . ($offset + $localOffset));
            $this->implementation->setValue($sectionNode->id);
            for($i = 1; $i <= $this->countOfChoices; $i++) {
                if(isset($this->choicesMap[$i])) {
                    $count = 0;
                    if(isset($tracesData[$sectionNode->id][$i])) {
                        $count = $tracesData[$sectionNode->id][$i];

                        if($i == 1) {
                            $firstChoiceValue = $count;
                        }
                    }

                    $this->implementation->setCursor($this->choicesMap[$i] . ($offset + $localOffset));
                    $this->implementation->setValue($count);
                }
            }
            $this->implementation->setCursor('G' . ($offset + $localOffset));
            $this->implementation->setCellsFormat('G' . ($offset + $localOffset), $this->implementation->getStyle('PERCENTAGE_00'));
            $this->implementation->setValue($firstChoiceValue / $countOfSectionNodes);
            $this->implementation->setCursor('H' . ($offset + $localOffset));
            $this->implementation->setValue($sectionNode->title);

            $localOffset += 1;
        }

        return $countOfSectionNodes;
    }

    /**
     * Get start offset
     *
     * @return mixed
     */
    public function getStartOffset() {
        return $this->startOffset;
    }

    /**
     * Get section nodes count
     *
     * @return integer - section nodes count
     */
    public function getSectionNodesCount() {
        return count($this->sectionNodes);
    }

    /**
     * Get section
     *
     * @return mixed
     */
    public function getSection() {
        return $this->section;
    }

    /**
     * Get key node ID
     *
     * @return integer - node ID
     */
    public function getKey() {
        return ($this->sectionNodes != null && count($this->sectionNodes) > 0) ? $this->sectionNodes[0]->id
                                                                               : null;
    }

    /**
     * Load section nodes
     */
    private function loadSectionNodes() {
        if($this->section == null || $this->section->nodes == null || count($this->section->nodes) <= 0) return;

        foreach($this->section->nodes as $sectionNode) {
            $this->sectionNodes[] = DB_ORM::model('map_node', array((int)$sectionNode->node_id));
        }
    }

    /**
     * Calculate session data
     *
     * @param $traces
     * @return array
     */
    private function calculateSectionData($traces) {
        $data = array();

        if($traces == null || count($traces) <= 0) return $data;

        $sectionNodesMap = array();
        foreach($this->sectionNodes as $sectionNode) {
            $sectionNodesMap[$sectionNode->id] = $sectionNode;
        }

        $parsedTrace = array();
        foreach($traces as $traceData) {
            if(isset($sectionNodesMap[$traceData['node_id']])) {
                $parsedTrace[$traceData['session_id']][] = $traceData;
            }
        }

        foreach($this->sectionNodes as $sectionNode) {
            for($i = 1; $i <= $this->countOfChoices; $i++) {
                $data[$sectionNode->id][$i] = $this->getVisitStatistic($parsedTrace, $sectionNode->id, $i);
            }
        }

        return $data;
    }

    /**
     * Calculate visits of nodes
     *
     * @param $traces
     * @param $nodeId
     * @param $choice
     * @return int
     */
    private function getVisitStatistic($traces, $nodeId, $choice) {
        $count = 0;
        foreach($traces as $sessionId => $traceData) {
            if(count($traceData) > 0) {
                $index = 1;
                foreach($traceData as $data) {
                    if($index == $choice && $data['node_id'] == $nodeId) {
                        $count += 1;
                    }

                    $index++;
                }
            }
        }

        return $count;
    }
}