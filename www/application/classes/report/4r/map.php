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

class Report_4R_Map extends Report_4R_Element {
    private $map;
    private $sections;
    private $elements;
    private $questions;
    private $webinarId;
    private $webinarStep;
    private $notInUsers;
    private $dateStatistics;
    private $countOfChoices;

    private $choicesMap = array(
        1 => 'C',
        2 => 'D',
        3 => 'E',
        4 => 'F'
    );

    public function __construct(Report_Impl $impl, $mapId, $countOfChoices, $webinarId = null, $webinarStep = null, $notInUsers = null, $dateStatistics = null) {
        parent::__construct($impl);

        if($mapId == null || $mapId <= 0) return;

        $this->map            = DB_ORM::model('map', array((int)$mapId));
        $this->countOfChoices = $countOfChoices;
        $this->webinarId      = $webinarId;
        $this->webinarStep    = $webinarStep;
        $this->notInUsers     = $notInUsers;
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
    public function insert($offset) {
        $localOffset = 0;

        if($this->map == null) return $localOffset;

        $this->implementation->setCursor('A' . $offset);
        $this->implementation->setFontSize('A' . $offset, 16);
        $this->implementation->setValue($this->map->name);
        $localOffset += 1;

        $this->setMapTableHeader($offset + $localOffset);
        $localOffset += 1;

        if($this->elements != null && count($this->elements) > 0) {
            foreach($this->elements as $element) {
                $localOffset += $element->insert($offset + $localOffset);
            }
        }

        if($this->sections != null && count($this->sections) > 0) {
            $localOffset += 1;
            $dataSeriesLabels = array(
                                    'Worksheet!$C$' . ($offset + 1),
                                    'Worksheet!$D$' . ($offset + 1),
                                    'Worksheet!$E$' . ($offset + 1),
                                    'Worksheet!$F$' . ($offset + 1)
                                );

            foreach($this->sections as $section) {
                $xAxisTickValue   = array();
                $dataSeriesValues = array();

                $start = $section->getStartOffset();
                $count = $section->getSectionNodesCount();
                $end   = $start + $count - 1;

                $xAxisTickValue[]      = array('values' => 'Worksheet!$H$' . $start . ':$H$' . $end, 'count' => $count);

                $dataSeriesValues[0][] = array('values' => 'Worksheet!$C$' . $start . ':$C$' . $end, 'count' => $count);
                $dataSeriesValues[1][] = array('values' => 'Worksheet!$D$' . $start . ':$D$' . $end, 'count' => $count);
                $dataSeriesValues[2][] = array('values' => 'Worksheet!$E$' . $start . ':$E$' . $end, 'count' => $count);
                $dataSeriesValues[3][] = array('values' => 'Worksheet!$F$' . $start . ':$F$' . $end, 'count' => $count);

                $endPosition = $count * 3;
                if($endPosition < 10) {
                    $endPosition = 10;
                }

                $sectionModel = $section->getSection();

                $this->implementation->addStackedBarChart('A' . ($offset + $localOffset),
                                                          'H' . ($offset + $localOffset + $endPosition),
                                                          $dataSeriesLabels,
                                                          $xAxisTickValue,
                                                          $dataSeriesValues,
                                                          $sectionModel->name,
                                                          'Frequency');

                $localOffset += $endPosition + 1;
            }
        }

        if($this->questions != null && count($this->questions) > 0) {
            foreach($this->questions as $question) {
                $localOffset += $question->insert($offset + $localOffset) + 1;
            }
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
    private function loadElements() {
        if($this->map == null || $this->map->nodes == null || count($this->map->nodes) <= 0) return;

        $sections = DB_ORM::model('map_node_section')->getAllSectionsByMap($this->map->id);
        if($sections != null && count($sections) > 0) {
            foreach($sections as $section) {
                $reportSection = new Report_4R_Section($this->implementation,
                                                       $this->map->id,
                                                       $section->id,
                                                       $this->countOfChoices,
                                                       $this->webinarId,
                                                       $this->webinarStep,
                                                       $this->notInUsers,
                                                       $this->dateStatistics);

                $this->elements[] = $reportSection;
                $this->sections[] = $reportSection;
            }

            if(count($this->sections) > 0) {
                usort($this->sections, 'Report_4R_Map::sortElements');
            }
        }

        foreach($this->map->nodes as $node) {
            if($node->sections != null && count($node->sections) > 0) continue;

            $this->elements[] = new Report_4R_Node($this->implementation,
                                                   $node->id,
                                                   $node->title,
                                                   $this->countOfChoices);
        }

        if($this->elements == null || count($this->elements) <= 0) return;

        usort($this->elements, 'Report_4R_Map::sortElements');

        $questions = DB_ORM::model('map_question')->getQuestionsByMapAndTypes($this->map->id, array(3, 4, 5));
        if($questions != null && count($questions) > 0) {
            foreach($questions as $question) {
                $this->questions[] = new Report_4R_Question($this->implementation,
                                                            $question->id,
                                                            $this->webinarId,
                                                            $this->webinarStep,
                                                            $this->notInUsers,
                                                            $this->dateStatistics);
            }
        }
    }

    /**
     * Set map table header values
     *
     * @param integer $currentOffset - current document offset
     */
    private function setMapTableHeader($currentOffset) {
        $this->implementation->setCursor('A' . $currentOffset);
        $this->implementation->setValue('Node ID');

        $this->implementation->setCursor('B' . $currentOffset);
        $this->implementation->setValue('Count baseline');

        $this->implementation->setCursor('C' . $currentOffset);
        $this->implementation->setValue('1st choice');

        $this->implementation->setCursor('D' . $currentOffset);
        $this->implementation->setValue('2nd choice');

        $this->implementation->setCursor('E' . $currentOffset);
        $this->implementation->setValue('3rd choice');

        $this->implementation->setCursor('F' . $currentOffset);
        $this->implementation->setValue('4th choice');

        $this->implementation->setCursor('G' . $currentOffset);
        $this->implementation->setValue('1st choice %');

        $this->implementation->setCursor('H' . $currentOffset);
        $this->implementation->setValue('Title');
    }

    /**
     * Sorting elements function
     *
     * @param Report_4R_Element $elementA - element A
     * @param Report_4R_Element $elementB - element B
     * @return integer - comparison flag
     */
    public static function sortElements(Report_4R_Element $elementA, Report_4R_Element $elementB) {
        $result = 0;
        if($elementA == null) {
            $result = -1;
        } else if($elementB == null) {
            $result = 1;
        } else {
            $keyA = $elementA->getKey();
            $keyB = $elementB->getKey();

            if($keyA > $keyB) {
                $result = 1;
            } else if($keyA < $keyB) {
                $result = -1;
            }
        }

        return $result;
    }
}