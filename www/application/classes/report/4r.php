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
class Report_4R extends Report {
    private $maps;
    private $countOfChoices;
    private $name;

    private $choicesMap = array(
        1 => 'C',
        2 => 'D',
        3 => 'E',
        4 => 'F'
    );

    /**
     * Default constructor
     *
     * @param Report_Impl $impl - report class implementation
     * @param string $name - report name
     */
    public function __construct(Report_Impl $impl, $name) {
        parent::__construct($impl);

        $this->maps = array();
        $this->countOfChoices = 4;
        $this->name = $name;
    }

    /**
     * Add map for report
     *
     * @param integer $mapId - map ID
     */
    public function add($mapId) {
        if($mapId == null || $mapId <= 0) return;

        $this->maps[] = $mapId;
    }

    /**
     * Generate report
     *
     * @return mixed
     */
    public function generate() {
        if($this->implementation == null || $this->maps == null || count($this->maps) <= 0) return;

        $this->implementation->setCreator('OpenLabyrinth System');
        $this->implementation->setLastModifiedBy('OpenLabyrinth System');
        $this->implementation->setTitle('4R Report');
        $this->implementation->setSubject('4R Statistic');
        $this->implementation->setDescription('4R Statistic');
        $this->implementation->setKeywords('4, 4R, 4R Report, Report');
        $this->implementation->setCategory('Report');

        $this->implementation->setActiveSheet(0);

        $currentOffset = 1;
        foreach($this->maps as $map) {
            list($offset, $data) = $this->generateMapTable($map, $currentOffset);
            $currentOffset      += $offset;
            $currentOffset      += $this->generateMapTableChart($data, $currentOffset, $map);
            $currentOffset      += $this->generateQuestionsTable($map, $currentOffset);
        }
    }

    /**
     * Generate questions table
     *
     * @param integer $mapId - map id
     * @param integer $currentOffset - current offset
     * @return int - offset
     */
    private function generateQuestionsTable($mapId, $currentOffset) {
        $offset    = 0;
        $questions = DB_ORM::model('map_question')->getQuestionsByMapAndTypes($mapId, array(3, 4, 5));

        if($questions == null || count($questions) <= 0) return $offset;

        $questionMap = array();

        foreach($questions as $question) {
            $questionMap[$question->id] = $question;
        }

        $data = array();
        foreach($questions as $question) {
            if($question->responses != null && count($question->responses) > 0) {
                foreach($question->responses as $response) {
                    if($question->entry_type_id == 5) {
                        $data[$question->id][$response->from . '-' . $response->to] = null;
                    } else {
                        $data[$question->id][$response->response] = null;
                    }
                }
            }

            $responses = DB_ORM::model('user_response')->getResponsesByQuestion($question->id);

            if($responses != null && count($responses) > 0) {
                foreach($responses as $response) {
                    if($question->entry_type_id == 5) {
                        if($question->responses != null && count($question->responses) > 0) {
                            foreach($question->responses as $r) {
                                if($response->response >= $r->from && $response->response <= $r->to) {
                                    $data[$question->id][$r->from . '-' . $r->to][$response->node_id] = isset($data[$question->id][$r->from . '-' . $r->to][$response->node_id]) ? ($data[$question->id][$r->from . '-' . $r->to][$response->node_id] + 1)
                                                                                                                                                                                 : 1;
                                }
                            }
                        }
                    } else {
                        $data[$question->id][$response->response][$response->node_id] = isset($data[$question->id][$response->response][$response->node_id]) ? ($data[$question->id][$response->response][$response->node_id] + 1)
                                                                                                                                                             : 1;
                    }

                }
            }
        }

        if(count($data) > 0) {
            $dataNodes = array();
            foreach($data as $questionId => $responses) {
                if($responses != null && count($responses) > 0) {
                    foreach($responses as $response => $nodes) {
                        if($nodes != null && count($nodes) > 0) {
                            foreach($nodes as $nodeId => $count) {
                                foreach($responses as $r => $n) {
                                    if(!isset($dataNodes[$nodeId][$questionId][$r])) {
                                        $dataNodes[$nodeId][$questionId][$r] = 0;
                                    }
                                }

                                $dataNodes[$nodeId][$questionId][$response] = $count;
                            }
                        }
                    }
                }
            }

            $localOffset = $currentOffset;
            foreach($dataNodes as $nodeId => $questionIDs) {
                if($questionIDs != null && count($questionIDs)) {
                    foreach($questionIDs as $questionId => $responses) {
                        if(isset($questionMap[$questionId]) && $responses != null && count($responses) > 0) {
                            $this->implementation->setCursor('A' . $localOffset);
                            $this->implementation->setFontSize('A' . $localOffset, 14);
                            $this->implementation->setValue($questionMap[$questionId]->stem . ' - ' . $nodeId);

                            $localOffset++;
                            $this->implementation->setCursor('A' . $localOffset);
                            $this->implementation->setValue('Responses');
                            $this->implementation->setCursor('B' . $localOffset);
                            $this->implementation->setValue('Count');
                            $localOffset++;

                            $start = $localOffset;
                            foreach($responses as $response => $count) {
                                $this->implementation->setCursor('A' . $localOffset);
                                $this->implementation->setValue($response);
                                $this->implementation->setCursor('B' . $localOffset);
                                $this->implementation->setValue($count);

                                $localOffset++;
                            }

                            $end = $localOffset - 1;

                            $this->implementation->addHorizontalBarChart('A' . ($localOffset + 1),
                                                                         'H' . ($localOffset + count($responses) + 7),
                                                                         array(array('values' => 'Worksheet!$A$' . $start . ':$A$' . $end, 'count' => ($end - $start))),
                                                                         array(array('values' => 'Worksheet!$A$' . $start . ':$A$' . $end, 'count' => ($end - $start))),
                                                                         array(array('values' => 'Worksheet!$B$' . $start . ':$B$' . $end, 'count' => ($end - $start))),
                                                                         $questionMap[$questionId]->stem . ' - ' . $nodeId,
                                                                         'Count of choices');
                            $localOffset += count($responses) + 8;
                            $offset      += 10 + count($responses) * 2;
                        }
                    }
                }
            }
        }

        return $offset;
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
     * Get report
     *
     * @return mixed
     */
    public function get() {
        $this->implementation->download($this->name . ' 4R Report');
    }

    /**
     * Generate map table
     *
     * @param integer $mapId - map ID
     */
    private function generateMapTable($mapId, $currentOffset) {
        $offset = 0;

        if($mapId == null || $mapId <= 0) return array($offset, null);

        $map = DB_ORM::model('map', array((int)$mapId));
        if($map == null) return array($offset, null);

        $this->implementation->setCursor('A' . $currentOffset);
        $this->implementation->setFontSize('A' . $currentOffset, 16);
        $this->implementation->setValue($map->name);

        $this->setMapTableHeader($currentOffset + 1);

        $offset  = 3;

        $data    = $this->calculateMapData($mapId);
        $offset += $this->insertMapTableRows($data, $currentOffset + 2);

        return array($offset, $data);
    }

    /**
     * Generate map table chart
     *
     * @param array(mixed) $data - map data
     * @param integer $currentOffset - current offset
     * @param integer $mapId - current map ID
     * @return int - offset
     */
    private function generateMapTableChart($data, $currentOffset, $mapId) {
        $offset = 0;

        $map = DB_ORM::model('map', array($mapId));

        if($data == null || count($data) <= 0 || $map == null) return $offset;

        $startDataOffset = $currentOffset - count($data) - 1;
        if($startDataOffset <= 0) return $offset;

        $sectionCount = null;
        $index        = 0;

        $dataSeriesLabels = array(
            'Worksheet!$C$' . ($startDataOffset - 1),
            'Worksheet!$D$' . ($startDataOffset - 1),
            'Worksheet!$E$' . ($startDataOffset - 1),
            'Worksheet!$F$' . ($startDataOffset - 1)
        );

        $xAxisTickValue   = array();
        $dataSeriesValues = array();

        foreach($data as $row) {
            if($row['isInSection'] == 1 && $row['sectionCount'] > 0) {
                $sectionCount          = $row['sectionCount'];
                $startSeriesPosition   = $startDataOffset + $index;
                $endSeriesPosition     = $startSeriesPosition + $sectionCount - 1;

                $xAxisTickValue[]      = array('values' => 'Worksheet!$H$' . $startSeriesPosition . ':$H$' . $endSeriesPosition, 'count' => $sectionCount);

                $dataSeriesValues[0][] = array('values' => 'Worksheet!$C$' . $startSeriesPosition . ':$C$' . $endSeriesPosition, 'count' => $sectionCount);
                $dataSeriesValues[1][] = array('values' => 'Worksheet!$D$' . $startSeriesPosition . ':$D$' . $endSeriesPosition, 'count' => $sectionCount);
                $dataSeriesValues[2][] = array('values' => 'Worksheet!$E$' . $startSeriesPosition . ':$E$' . $endSeriesPosition, 'count' => $sectionCount);
                $dataSeriesValues[3][] = array('values' => 'Worksheet!$F$' . $startSeriesPosition . ':$F$' . $endSeriesPosition, 'count' => $sectionCount);
            }

            if($sectionCount > 0) {
                $sectionCount--;
            }

            $index++;
        }

        $this->implementation->addStackedBarChart('A' . $currentOffset,
                                                  'H' . ($currentOffset + count($data)),
                                                  $dataSeriesLabels,
                                                  $xAxisTickValue,
                                                  $dataSeriesValues,
                                                  $map->name,
                                                  'Number of visits');

        $offset = count($data) + 1;

        return $offset;
    }

    /**
     * Insert map table rows
     *
     * @param array(mixed) $data - map data
     * @param integer $currentOffset - current offset
     * @return int - offset
     */
    private function insertMapTableRows($data, $currentOffset) {
        $result = 0;
        if($data == null || count($data) <= 0) return $result;

        $result             = count($data);

        $offset             = $currentOffset;
        $sectionCount       = null;
        $sectionCountCursor = null;
        foreach($data as $row) {
            $this->implementation->setCursor('A' . $offset);
            $this->implementation->setValue($row['node']->id);

            if($row['isInSection'] == 1 && $row['sectionCount'] > 0) {
                $this->implementation->setCursor('B' . $offset);
                $this->implementation->setValue($row['sectionCount']);

                $sectionCount       = $row['sectionCount'];
                $sectionCountCursor = 'B' . $offset;
            }

            if(count($row['choices']) > 0) {
                $choiceIndex = 1;
                foreach($row['choices'] as $choice) {
                    if(isset($this->choicesMap[$choiceIndex])) {
                        $this->implementation->setCursor($this->choicesMap[$choiceIndex] . $offset);
                        $this->implementation->setValue($choice);

                        if($choiceIndex == 1) {
                            $this->implementation->setCursor('G' . $offset);
                            $this->implementation->setCellsFormat('G' . $offset, PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
                            if($sectionCount > 0) {
                                $this->implementation->setValue('=' . $this->choicesMap[$choiceIndex] . $offset . '/' . $sectionCountCursor);

                                $sectionCount--;
                            } else {
                                $this->implementation->setValue(0);
                            }
                        }
                    }

                    $choiceIndex++;
                }
            }

            $this->implementation->setCursor('H' . $offset);
            $this->implementation->setValue($row['node']->title);

            $offset++;
        }

        return $result;
    }

    /**
     * Calculating map data
     *
     * @param integer $mapId - map ID
     * @return array - map data
     */
    private function calculateMapData($mapId) {
        $result             = array();

        $rootNode           = DB_ORM::model('map_node')->getRootNodeByMap($mapId);
        $nodes              = DB_ORM::model('map_node')->getNodesByMap($mapId);
        $nodesMap           = $this->generateNodesMap($nodes);
        ksort($nodesMap);

        $sections           = DB_ORM::model('map_node_section')->getAllSectionsByMap($mapId);
        $sectionsMap        = $this->generateSectionsMap($sections);
        $sectionsNodesMap   = $this->generateSectionsNodesMap($sections);
        $sectionsFirstNodes = $this->getSectionFirstNodes($sections);

        $trace              = DB_ORM::model('user_sessiontrace')->getUniqueTraceByMapId($mapId);

        $result[] = $this->generateMapDataElement($rootNode, false, null, $this->generateEmptyChoicesArray());

        $sortingNodes = array();
        foreach($nodes as $node) {
            if($node->id == $rootNode->id || isset($sectionsNodesMap[$node->id])) continue;

            $sortingNodes[] = $node->id;
        }

        // Check with array_merge
        foreach($sectionsFirstNodes as $firstSectionNode) { $sortingNodes[] = $firstSectionNode; }

        $sortingNodesCount = count($sortingNodes);

        for($j = 0; $j < $sortingNodesCount; $j++) {
            for($i = 0; $i < $sortingNodesCount - 1; $i++) {
                $current = $sortingNodes[$i];
                $next    = $sortingNodes[$i + 1];
                if(isset($sortingNodes[$i]['nodeId'])) {
                    $current = $sortingNodes[$i]['nodeId'];
                }

                if(isset($sortingNodes[$i + 1]['nodeId'])) {
                    $next = $sortingNodes[$i + 1]['nodeId'];
                }

                if($current > $next) {
                    $tmp = $sortingNodes[$i];
                    $sortingNodes[$i] = $sortingNodes[$i + 1];
                    $sortingNodes[$i + 1] = $tmp;
                }
            }
        }

        foreach($sortingNodes as $node) {
            if(isset($node['nodeId'])) {
                if(isset($sectionsMap[$node['sectionId']])) {
                    $section = $sectionsMap[$node['sectionId']];

                    if($section->nodes != null && count($section->nodes) > 0) {
                        $sectionCount = count($section->nodes);
                        $isFirst = true;
                        $usersSectionTraces = $this->generateSectionUsersTrace($trace, $section->nodes);
                        foreach($section->nodes as $sectionNode) {
                            if(!isset($nodesMap[$sectionNode->node_id])) continue;

                            $choices = $this->generateEmptyChoicesArray();

                            for($i = 1; $i <= $this->countOfChoices; $i++) {
                                $choices[$i] = $this->calculateUserCountChoice($usersSectionTraces, $i, $sectionNode->node_id);
                            }

                            $result[] = $this->generateMapDataElement($nodesMap[$sectionNode->node_id], true, $isFirst ? $sectionCount : null, $choices);
                            $isFirst  = false;
                        }
                    }
                }
            } else if(isset($nodesMap[$node])) {
                $result[] = $this->generateMapDataElement($nodesMap[$node], false, null, $this->generateEmptyChoicesArray());
            }
        }

        return $result;
    }

    /**
     * Calculating unique user choice
     *
     * @param array $traces - trace
     * @param integer $choiceNumber - choice number
     * @param integer $nodeId - current node ID
     * @return int - count of choices
     */
    private function calculateUserCountChoice($traces, $choiceNumber, $nodeId) {
        $result = 0;
        if($traces == null || count($traces) <= 0) return $result;

        foreach($traces as $userId => $trace) {
            $index = 1;
            foreach($trace as $t) {
                if($index == $choiceNumber && $t['node_id'] == $nodeId) {
                    $result++;
                }
                $index++;
            }
        }

        return $result;
    }

    /**
     * Get only section user trace
     *
     * @param array $traces - trace
     * @param array $sectionNodes - section nodes array
     * @return array - section nodes trace
     */
    private function generateSectionUsersTrace($traces, $sectionNodes) {
        $result = array();
        if($traces == null || count($traces) <= 0 || $sectionNodes == null || count($sectionNodes) <= 0) return $result;

        $sectionNodesMap = array();
        foreach($sectionNodes as $sectionNode) {
            $sectionNodesMap[$sectionNode->node_id] = $sectionNode;
        }

        foreach($traces as $trace) {
            if(isset($sectionNodesMap[$trace['node_id']])) {
                $result[$trace['user_id']][] = $trace;
            }
        }

        return $result;
    }

    /**
     * Generate map data element
     *
     * @param Map_Node $node - node
     * @param bool $isInSection - is node in section
     * @param null|integer $sectionCount - count nodes in section
     * @param array $choices - user choices
     * @return array - map data element
     */
    private function generateMapDataElement($node, $isInSection = false, $sectionCount = null, $choices = array()) {
        return array('node'         => $node,
                     'isInSection'  => $isInSection,
                     'sectionCount' => $sectionCount,
                     'choices'      => $choices);
    }

    /**
     * Generation zero choices array
     *
     * @return array
     */
    private function generateEmptyChoicesArray() {
        $result = array();

        for($i = 1; $i <= $this->countOfChoices; $i++) {
            $result[$i] = 0;
        }

        return $result;
    }

    /**
     * Generate nodes map
     *
     * @param Map_Node $nodes - nodes
     * @return array - nodes map (array([nodeId] => node))
     */
    private function generateNodesMap($nodes) {
        $result = array();
        if($nodes == null || count($nodes) <= 0) return $result;

        foreach($nodes as $node) {
            $result[$node->id] = $node;
        }

        return $result;
    }

    /**
     * Generate sections map
     *
     * @param array(Map_Node_Section) $sections - array of node sections
     * @return array - sections map array([SectionId] => section)
     */
    private function generateSectionsMap($sections) {
        $result = array();
        if($sections == null || count($sections) <= 0) return $result;

        foreach($sections as $section) {
            $result[$section->id] = $section;
        }

        return $result;
    }

    /**
     * Generate sections map nodes
     *
     * @param array(Map_Node_Section) $sections - array of node sections
     * @return array - map of section nodes array([NodeId] => sectionId)
     */
    private function generateSectionsNodesMap($sections) {
        $result = array();
        if($sections == null || count($sections) <= 0) return $result;

        foreach($sections as $section) {
            if($section->nodes == null || count($section->nodes) <= 0) continue;

            foreach($section->nodes as $sectionNode) {
                $result[$sectionNode->node_id] = $section->id;
            }
        }

        return $result;
    }

    /**
     * Return first section nodes
     *
     * @param array(Map_Node_Section) $sections - array of node sections
     * @return array - array of first section nodes
     */
    private function getSectionFirstNodes($sections) {
        $result = array();
        if($sections == null || count($sections) <= 0) return $result;

        foreach($sections as $section) {
            if($section->nodes == null || count($section->nodes) <= 0) continue;

            $result[] = array('nodeId'    => $section->nodes[0]->node_id,
                              'sectionId' => $section->id);
        }

        return $result;
    }
}