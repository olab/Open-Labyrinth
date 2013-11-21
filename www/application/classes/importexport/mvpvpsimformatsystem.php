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
 * Class MVP2Map - MVP2 Map container class
 */
class MVP2Map {
    /**
     * @var array - MVP2 Node section ID to database node section ID map
     */
    private $NS2DBMap = array();

    /**
     * @var array - Database node section ID to MVP2 Node Section ID
     */
    private $DB2NSMap = array();

    /**
     * @var array - MVP2 Activity Node ID to database node ID map
     */
    private $AN2DBMap = array();

    /**
     * @var array - MVP2 Activity Node Content ID to database node ID map
     */
    private $ANC2DBMap = array();

    /**
     * @var array - Database node ID to MVP2 Activity Node ID map
     */
    private $DB2ANMap = array();

    /**
     * @var array - MVP2 Activity counter ID to database counter ID
     */
    private $AC2DBMap = array();

    /**
     * @var array -  Database counter ID to MVP2 Activity counter ID
     */
    private $DB2ACMap = array();

    /**
     * @var array - MVP2 Activity Link ID to database link ID
     */
    private $AL2DBMap = array();

    /**
     * @var array - Database link ID to activity link ID
     */
    private $DB2ALMap = array();

    /**
     * @var array - MV2 Virtual Patient Path to database node ID
     */
    private $VP2DBMap = array();

    /**
     * Media file name to media name
     *
     * @var array
     */
    private $MF2MNMap = array();

    /**
     * Media name to media file name
     *
     * @var array
     */
    private $MN2MFMap = array();

    /**
     * Media name to database ID map
     *
     * @var array
     */
    private $MF2DBMap = array();

    /**
     * Question ID to database question ID map
     *
     * @var array
     */
    private $Q2DBMap = array();

    public function addQuestion($questionId, $databaseId) {
        $this->Q2DBMap[$questionId] = $databaseId;
    }

    public function getQuestionDatabaseId($questionId) {
        return isset($this->Q2DBMap[$questionId]) ? $this->Q2DBMap[$questionId]
                                                  : null;
    }

    public function addMediaFile($mediaFileName, $mediaName) {
        $this->MF2MNMap[$mediaFileName] = $mediaName;
        $this->MN2MFMap[$mediaName]     = $mediaFileName;
    }

    public function addMediaDatabaseFile($mediaName, $databaseId) {
        $this->MF2DBMap[$mediaName] = $databaseId;
    }

    public function getMediaDatabaseId($mediaName) {
        return isset($this->MF2DBMap[$mediaName]) ? $this->MF2DBMap[$mediaName]
                                                  : null;
    }

    public function getMediaNames() {
        return array_keys($this->MF2DBMap);
    }

    public function addActivityLink($nodeAId, $nodeBId, $databaseId) {
        $this->AL2DBMap[$nodeAId . $nodeBId] = $databaseId;
        $this->DB2ALMap[$databaseId] = array('nodeA' => $nodeAId, 'nodeB' => $nodeBId);
    }

    public function getMediaFilename($mediaName) {
        return isset($this->MN2MFMap[$mediaName]) ? $this->MN2MFMap[$mediaName]
                                                  : null;
    }

    public function getActivityLinkDatabaseId($nodeAId, $nodeBId) {
        return isset($this->AL2DBMap[$nodeAId . $nodeBId]) ? $this->AL2DBMap[$nodeAId . $nodeBId]
                                                           : null;
    }

    public function addVirtualPatientPath($vpId, $databaseId) {
        $this->VP2DBMap[$vpId] = $databaseId;
    }

    public function getVirtualPatineDatabaseId($vpId) {
        return isset($this->VP2DBMap[$vpId]) ? $this->VP2DBMap[$vpId]
                                             : null;
    }

    /**
     * Add MVP2 Node section ID and database node section ID to map
     *
     * @param $nodeSectionId - MVP2 Node section
     * @param $databaseNodeSectionId - database node section ID
     */
    public function addNodeSection($nodeSectionId, $databaseNodeSectionId) {
        $this->NS2DBMap[$nodeSectionId]         = $databaseNodeSectionId;
        $this->DB2NSMap[$databaseNodeSectionId] = $nodeSectionId;
    }

    /**
     * Get database map node section ID by MVP2 Node Section ID
     *
     * @param $nodeSectionId - MVP2 Node Section ID
     * @return - Database map node section ID
     */
    public function getActivityNodeSectionDatabaseId($nodeSectionId) {
        return isset($this->NS2DBMap[$nodeSectionId]) ? $this->NS2DBMap[$nodeSectionId]
                                                      : null;
    }

    /**
     * Add activity node data
     *
     * @param $activityNodeId - MVP2 Activity Node ID
     * @param $databaseId - Database map node ID
     * @param $data - MVP2 Activity Node Content
     */
    public function addActivityNode($activityNodeId, $databaseId, $content) {
        $fullData = array('activityNodeId' => $activityNodeId,
                          'databaseId'     => $databaseId,
                          'content'        => $content);

        $this->AN2DBMap[$activityNodeId] = $fullData;
        $this->ANC2DBMap[$content]       = $fullData;
        $this->DB2ANMap[$databaseId]     = $fullData;
    }

    /**
     * Get database node ID by activity node ID
     *
     * @param $activityNodeId - MVP2 activity node ID
     * @return - database node ID or null
     */
    public function getActivityNodeDatabaseId($activityNodeId) {
        return isset($this->AN2DBMap[$activityNodeId]) ? $this->AN2DBMap[$activityNodeId]['databaseId']
                                                       : null;
    }

    /**
     * Get database node ID by activity content node ID
     *
     * @param $activityContentNodeId - MVP2 activity content node ID
     * @return - database node ID or null
     */
    public function getActivityContentNodeDatabaseId($activityContentNodeId) {
        return isset($this->ANC2DBMap[$activityContentNodeId]) ? $this->ANC2DBMap[$activityContentNodeId]['databaseId']
                                                               : null;
    }

    /**
     * Add MVP2 Activity counter ID and database node section ID to map
     *
     * @param $activityCounterId - MVP2 Node section
     * @param $databaseId - database counter ID
     */
    public function addActivityCounter($activityCounterId, $databaseId) {
        $this->AC2DBMap[$activityCounterId] = $databaseId;
        $this->DB2ACMap[$databaseId]        = $activityCounterId;
    }

    /**
     * Get database counter ID by activity counter ID
     *
     * @param $activityNodeId - MVP2 activity counter ID
     * @return - database counter ID or null
     */
    public function getActivityCounterDatabaseId($activityCounterId) {
        return isset($this->AC2DBMap[$activityCounterId]) ? $this->AC2DBMap[$activityCounterId]
                                                          : null;
    }
}

/**
 * MVP2 format import/export system
 */
class ImportExport_MVPvpSimFormatSystem implements ImportExport_FormatSystem {
    private $mvp2Map = null;

    public function __construct() {
        $this->mvp2Map = new MVP2Map();
    }

    public function import($parameters) {
        if(!isset($parameters['tmpFolder']) ||
           !isset($parameters['filesFolder']) ||
           !isset($parameters['filesShortFolder'])) { return true; }

        $tmpFolder        = $parameters['tmpFolder'];
        $filesFolder      = $parameters['filesFolder'];
        $filesShortFolder = $parameters['filesShortFolder'];

        $metadata = $this->xml2array($tmpFolder . '/metadata.xml');
        if($metadata == null) { return false; }

        $map = $this->createMap($metadata);
        if($map == null) { return false; }

        $this->createContributors($metadata, $map);

        unset($metadata);

        $activityModel = $this->xml2array($tmpFolder . '/activitymodel.xml');
        if($activityModel != null) { $this->processActivityModel($activityModel, $map); }

        unset($activityModel);

        $dataAvailabilityModel = $this->xml2array($tmpFolder . '/dataavailabilitymodel.xml');
        if($dataAvailabilityModel != null) { $this->processDataAvailabilityModel($dataAvailabilityModel, $map); }

        unset($dataAvailabilityModel);

        $this->copyMediaDirectory($tmpFolder . '/media', $filesFolder, $map);
        $virtualPatientData = $this->xml2array($tmpFolder . '/virtualpatientdata.xml');
        if($virtualPatientData != null) { $this->processVirtualPatientData($virtualPatientData, $map, $filesFolder, $filesShortFolder); }

        unset($virtualPatientData);

        //var_dump($metadata);

        return true;
    }

    public function export($parameters) {
        return '';
    }

    private function copyMediaDirectory($folder, $filesFolder, $map) {
        if($handle = opendir($folder)) {
            while(false !== ($file = readdir($handle))) {
                if($file == '.' || $file == '..') { continue; }
                $path = $folder . '/' . $file;
                $pathInfo = pathinfo($path);

                $this->mvp2Map->addMediaFile($pathInfo['basename'], strtoupper($pathInfo['filename']));

                $fileDst = $filesFolder . '/' . $map->id;

                if(!file_exists($fileDst)) { mkdir($fileDst); }
                copy($path, $fileDst . '/' . $pathInfo['basename']);
            }

            closedir($handle);
        }
    }

    private function createMap($metadata) {
        if(!isset($metadata['general']) ||
           !isset($metadata['general']['title']) ||
           !isset($metadata['general']['title']['string'])) { return null; }

        $labyrinthData          = array();
        $labyrinthData['title'] = $metadata['general']['title']['string'];
        if(isset($metadata['general']['language'])) {
            $language = DB_ORM::model('language')->getLanguageByName(strtoupper($metadata['general']['language']));

            if($language != null) { $labyrinthData['language_id'] = $language->id; }
        }
        $labyrinthData['author']      = Auth::instance()->get_user()->id;
        $labyrinthData['description'] = '';
        $labyrinthData['keywords']    = '';

        return DB_ORM::model('map')->createMap($labyrinthData, false);
    }

    private function createContributors($metadata, $map) {
        if(!isset($metadata['lifeCycle']) &&
           !isset($metadata['lifeCycle']['contribute']) &&
           count($metadata['lifeCycle']['contribute']) <= 0) { return; }

        $index = 1;
        foreach($metadata['lifeCycle']['contribute'] as $data) {
            if(!isset($data['entity']) || empty($data['entity'])) { continue; }

            $contributorData = array('map_id' => $map->id, 'name' => $data['entity'], 'order' => $index);
            if(isset($data['role']) && isset($data['role']['value'])) {
                $role = DB_ORM::model('map_contributor_role')->getRoleByName($data['role']['value']);
                if($role != null) { $contributorData['role_id'] = $role->id; }
            }

            DB_ORM::model('map_contributor')->createContributorFromValues($contributorData);

            $index++;
        }
    }

    private function processDataAvailabilityModel($dataAvailabilityModel, $map) {
        if(!isset($dataAvailabilityModel['DAMNode']) || count($dataAvailabilityModel['DAMNode']) <= 0) { return; }

        foreach($dataAvailabilityModel['DAMNode'] as $damNode) {
            if(isset($damNode['@attributes']) &&
               isset($damNode['@attributes']['id']) &&
               isset($damNode['DAMNodeItem'])) {
                $damId = $damNode['@attributes']['id'];

                $path = null;
                if(isset($damNode['DAMNodeItem']['ItemPath'])) {
                    $path = $damNode['DAMNodeItem']['ItemPath'];
                } else if(count($damNode['DAMNodeItem']) > 0) {
                    foreach($damNode['DAMNodeItem'] as $item) {
                        if(isset($item['ItemPath']) && strpos($item['ItemPath'], '/VirtualPatientData') !== false) {
                            $path = $item['ItemPath'];
                            break;
                        }
                    }
                }

                $databaseId = $this->mvp2Map->getActivityContentNodeDatabaseId('/DataAvailabilityModel/DAMNode[@id = \'' . $damId . '\']');
                if($databaseId != null) { $this->mvp2Map->addVirtualPatientPath($path, $databaseId); }
            }
        }

    }

    private function processActivityModel($activityModelData, $map) {
        $this->processActivityProperties($activityModelData, $map);
        $this->processActivityNodes($activityModelData, $map);
        $this->processActivityLinks($activityModelData, $map);
        $this->processActivityNodeCoordinates($activityModelData, $map);

        //TODO: Add NodeLinksID only order field
    }

    private function processVirtualPatientData($virtualPatientData, $map, $filesFolder, $filesShortFolder) {
        $this->processMedia($virtualPatientData, $map, $filesFolder, $filesShortFolder);
        $this->processVPDText($virtualPatientData, $map);
        $this->setRootNode($virtualPatientData, $map);
        $this->processQuestions($virtualPatientData, $map);
    }

    private function processQuestions($virtualPatientData, $map) {
        if(!isset($virtualPatientData['XtensibleInfo']) ||
           !isset($virtualPatientData['XtensibleInfo']['QuestionChoices'])) { return; }

        foreach($virtualPatientData['XtensibleInfo']['QuestionChoices'] as $questionChoices) {
            if(isset($questionChoices['BranchChoice']) && count($questionChoices['BranchChoice']) > 0) {
                $addedQuestion = array();
                foreach($questionChoices['BranchChoice'] as $branchChoice) {
                    if(!isset($branchChoice['@attributes'])) { continue; }


                    if(isset($branchChoice['@attributes']['questionID'])) {
                        $questionDatabaseId = $this->mvp2Map->getQuestionDatabaseId($branchChoice['@attributes']['questionID']);
                        if($questionDatabaseId == null) { $questionDatabaseId = $this->createQuestion($branchChoice['@attributes']['questionID'], $map, 4); }

                        DB_ORM::model('map_question_response')->addFullResponses($questionDatabaseId, array('response' => $branchChoice['choiceText'],
                                                                                                            'feedback' => isset($branchChoice['patientResponse'][0]) ? $branchChoice['patientResponse'][0] : ''));

                        $parentNodeId = $branchChoice['parentNodeID'];
                        $databaseNodeId = $this->mvp2Map->getActivityNodeDatabaseId($parentNodeId);
                        if($databaseNodeId != null) {
                            if(!isset($addedQuestion[$questionDatabaseId])) {
                                $addedQuestion[$questionDatabaseId] = array();
                            }

                            if(!isset($addedQuestion[$questionDatabaseId][$parentNodeId])) {
                                $addedQuestion[$questionDatabaseId][$parentNodeId] = true;
                                $node = DB_ORM::model('map_node', array((int)$databaseNodeId));

                                $node->text .= '[[QU:' . $questionDatabaseId . ']]';
                                DB_ORM::model('map_node')->updateNodeText($databaseNodeId, $node->text);
                            }
                        }
                    }
                }
            }
        }

        if(isset($virtualPatientData['XtensibleInfo']['QuestionChoices']['MCQ']) && count($virtualPatientData['XtensibleInfo']['QuestionChoices']['MCQ']) > 0) {
            foreach($virtualPatientData['XtensibleInfo']['QuestionChoices']['MCQ'] as $mcq) {
                $addedQuestion = array();
                foreach($mcq as $q) {
                    if(!isset($q['@attributes'])) { continue; }

                    if(isset($q['@attributes']['questionID'])) {
                        $questionDatabaseId = $this->mvp2Map->getQuestionDatabaseId($q['@attributes']['questionID']);
                        if($questionDatabaseId == null) { $questionDatabaseId = $this->createQuestion($q['@attributes']['questionID'], $map, 3); }

                        DB_ORM::model('map_question_response')->addFullResponses($questionDatabaseId, array('response' => $q['choiceText'],
                                                                                                            'feedback' => isset($q['patientResponse'][0]) ? $q['patientResponse'][0] : ''));

                        $parentNodeId = $q['@attributes']['NodeId'];
                        $databaseNodeId = $this->mvp2Map->getActivityNodeDatabaseId($parentNodeId);
                        if($databaseNodeId != null) {
                            if(!isset($addedQuestion[$questionDatabaseId])) {
                                $addedQuestion[$questionDatabaseId] = array();
                            }

                            if(!isset($addedQuestion[$questionDatabaseId][$parentNodeId])) {
                                $addedQuestion[$questionDatabaseId][$parentNodeId] = true;
                                $node = DB_ORM::model('map_node', array((int)$databaseNodeId));

                                $node->text .= '[[QU:' . $questionDatabaseId . ']]';
                                DB_ORM::model('map_node')->updateNodeText($databaseNodeId, $node->text);
                            }
                        }
                    }
                }
            }
        }

        if(isset($virtualPatientData['XtensibleInfo']['QuestionChoices']['Inquiry']) && count($virtualPatientData['XtensibleInfo']['QuestionChoices']['Inquiry']) > 0) {
            foreach($virtualPatientData['XtensibleInfo']['QuestionChoices']['Inquiry'] as $mcq) {
                $addedQuestion = array();
                foreach($mcq as $q) {
                    if(!isset($q['@attributes'])) { continue; }

                    if(isset($q['@attributes']['questionID'])) {
                        $questionDatabaseId = $this->mvp2Map->getQuestionDatabaseId($q['@attributes']['questionID']);
                        if($questionDatabaseId == null) { $questionDatabaseId = $this->createQuestion($q['@attributes']['questionID'], $map, 3); }

                        DB_ORM::model('map_question_response')->addFullResponses($questionDatabaseId, array('response' => $q['choiceText'],
                                                                                                            'feedback' => isset($q['patientResponse'][0]) ? $q['patientResponse'][0] : ''));

                        $parentNodeId = $q['@attributes']['NodeId'];
                        $databaseNodeId = $this->mvp2Map->getActivityNodeDatabaseId($parentNodeId);
                        if($databaseNodeId != null) {
                            if(!isset($addedQuestion[$questionDatabaseId])) {
                                $addedQuestion[$questionDatabaseId] = array();
                            }

                            if(!isset($addedQuestion[$questionDatabaseId][$parentNodeId])) {
                                $addedQuestion[$questionDatabaseId][$parentNodeId] = true;
                                $node = DB_ORM::model('map_node', array((int)$databaseNodeId));

                                $node->text .= '[[QU:' . $questionDatabaseId . ']]';
                                DB_ORM::model('map_node')->updateNodeText($databaseNodeId, $node->text);
                            }
                        }
                    }
                }
            }
        }
    }

    private function createQuestion($questionID, $map, $type) {
        $questionDatabaseId = DB_ORM::model('map_question')->createEmptyQuestion($map->id, $type);
        $this->mvp2Map->addQuestion($questionID, $questionDatabaseId);

        return $questionDatabaseId;
    }

    private function setRootNode($virtualPatientData, $map) {
        if(!isset($virtualPatientData['XtensibleInfo']) || !isset($virtualPatientData['XtensibleInfo']['startNode'])) { return; }

        $databaseNodeId = $this->mvp2Map->getActivityNodeDatabaseId($virtualPatientData['XtensibleInfo']['startNode']);
        if($databaseNodeId != null) { DB_ORM::model('map_node')->setRootNode($map->id, $databaseNodeId); }
    }

    private function processMedia($virtualPatientData, $map, $filesFolder, $filesShortFolder) {
        if(!isset($virtualPatientData['XtensibleInfo']) ||
           !isset($virtualPatientData['XtensibleInfo']['mediaInfoSection']) ||
           count($virtualPatientData['XtensibleInfo']['mediaInfoSection']) <= 0) { return; }

        foreach($virtualPatientData['XtensibleInfo']['mediaInfoSection'] as $mediaInfo) {
            if(count($mediaInfo) <= 0) { continue; }

            $addedNames = array();
            foreach($mediaInfo as $mediaID) {
                if(!isset($mediaID['@attributes']) || !isset($mediaID['@attributes']['id'])) { continue; }

                $name   = strtoupper($mediaID['@attributes']['id']);
                $width  = isset($mediaID['mediaDisplayWidth']) ? $mediaID['mediaDisplayWidth'] : null;
                $height = isset($mediaID['mediaDisplayHeight']) ? $mediaID['mediaDisplayHeight'] : null;

                if(!isset($addedNames[$name])) {
                    $addedNames[$name] = true;

                    $mediaFullName = $this->mvp2Map->getMediaFilename($name);
                    if($mediaFullName != null) {
                        $fullPath = $filesFolder . '/' . $map->id . '/' . $mediaFullName;
                        if(file_exists($fullPath)) {
                            $path = $filesShortFolder . '/' . $map->id . '/' . $mediaFullName;
                            $mime = File::mime($fullPath);

                            $databaseId = DB_ORM::model('map_element')->addFile($map->id, array('name'   => $name,
                                                                                                'mime'   => $mime,
                                                                                                'path'   => $path,
                                                                                                'width'  => $width,
                                                                                                'height' => $height));

                            $this->mvp2Map->addMediaDatabaseFile($name, $databaseId);
                        }
                    }
                }
            }
        }
    }

    private function processVPDText($virtualPatientData, $map) {
        if(!isset($virtualPatientData['VPDText']) || count($virtualPatientData['VPDText']) <= 0) { return; }

        $mediaNames = $this->mvp2Map->getMediaNames();
        foreach($virtualPatientData['VPDText'] as $vpdText) {
            if(!isset($vpdText['@attributes']) || !isset($vpdText['@attributes']['id'])) { continue; }

            if(isset($vpdText['div']) && count($vpdText['div']) <= 0) { continue; }
            $nodeText       = str_replace('&gt;', '>', str_replace('&lt;', '<', $vpdText['div']));
            $databaseNodeId = $this->mvp2Map->getVirtualPatineDatabaseId('/VirtualPatientData/VPDText[@id = \'' . $vpdText['@attributes']['id'] . '\']');
            if($databaseNodeId != null) {
                $nodeText = $this->replaceNodeTextByMedia($nodeText, $mediaNames);
                DB_ORM::model('map_node')->updateNodeText($databaseNodeId, $nodeText);
            }
        }

    }

    private function replaceNodeTextByMedia($nodeText, $mediaNames) {
        if(count($mediaNames) <= 0) { return $nodeText; }

        $result = $nodeText;
        foreach($mediaNames as $mediaName) {
            if(preg_match("/" . $mediaName . "/i", $nodeText)) {
                $mediaDatabaseId = $this->mvp2Map->getMediaDatabaseId($mediaName);
                if($mediaDatabaseId != null) {
                    $result = preg_replace('/<img.*(' . $mediaName . ')+.*?\/>/i', '[[MR:' . $mediaDatabaseId . ']]', $result);
                }
            }
        }

        return $result;
    }

    private function processActivityNodeCoordinates($activityModelData, $map) {
        if(!isset($activityModelData['XtensibleInfo']) ||
           !isset($activityModelData['XtensibleInfo']['NodeCoordinates']) ||
           count($activityModelData['XtensibleInfo']['NodeCoordinates']) <= 0) { return; }

        foreach($activityModelData['XtensibleInfo']['NodeCoordinates'] as $nodeCoordinates) {
            if(count($nodeCoordinates) > 0) {
                foreach($nodeCoordinates as $nodeCoordinate) {
                    if(!isset($nodeCoordinate['@attributes']) || !isset($nodeCoordinate['@attributes']['id'])) { continue; }

                    $databaseNodeId = $this->mvp2Map->getActivityNodeDatabaseId($nodeCoordinate['@attributes']['id']);
                    if($databaseNodeId == null) { continue; }

                    $x     = isset($nodeCoordinate['Xcoordinate']) ? (int)$nodeCoordinate['Xcoordinate'] : 0;
                    $y     = isset($nodeCoordinate['Ycoordinate']) ? (int)$nodeCoordinate['Ycoordinate'] : 0;
                    $color = isset($nodeCoordinate['nodeColor']) ? ('#' . $nodeCoordinate['nodeColor']) : '#FFFFFF';

                    DB_ORM::model('map_node')->updateNodeStyle($databaseNodeId, array('x'   => $x,
                                                                                      'y'   => $y,
                                                                                      'rgb' => $color));
                }
            }
        }
    }

    private function processActivityProperties($activityModelData, $map) {
        if(!isset($activityModelData['Properties'])) { return; }
        $this->processActivityCounters($activityModelData, $map);

        //TODO: Add timers
    }

    private function processActivityCounters($activityModelData, $map) {
        if(!isset($activityModelData['Properties']['Counters']) || count($activityModelData['Properties']['Counters']) <= 0) { return; }

        foreach($activityModelData['Properties']['Counters'] as $activityCounter) {
            if(count($activityCounter) > 0) {
                foreach($activityCounter as $counterData) {
                    if(isset($counterData['@attributes']) && isset($counterData['@attributes']['id'])) {
                        $name = isset($counterData['CounterLabel']) ? $counterData['CounterLabel']
                                                                    : '';
                        $startValue = isset($counterData['CounterInitValue']) ? $counterData['CounterInitValue']
                                                                              : 0;
                        $counter = DB_ORM::model('map_counter')->addCounter($map->id, array('cName' => $name, 'cStartV' => $startValue));
                        $this->mvp2Map->addActivityCounter($counterData['@attributes']['id'], $counter->id);
                    }
                }
            }
        }
    }

    private function processActivityLinks($activityModelData, $map) {
        if(!isset($activityModelData['Links']) || !isset($activityModelData['Links']['Link']) || count($activityModelData['Links']['Link']) <= 0) { return; }

        foreach($activityModelData['Links']['Link'] as $activityLink) {
            $nodeA = $this->getActivityNodeFromPath($activityLink['ActivityNodeA']);
            $nodeB = $this->getActivityNodeFromPath($activityLink['ActivityNodeB']);

            $label = '';
            if(isset($activityLink['@attributes']) && isset($activityLink['@attributes']['label'])) { $label = $activityLink['@attributes']['label']; }

            $databaseNodeA = $this->mvp2Map->getActivityNodeDatabaseId($nodeA);
            $databaseNodeB = $this->mvp2Map->getActivityNodeDatabaseId($nodeB);

            if($databaseNodeA != null && $databaseNodeB != null) {
                $link = DB_ORM::model('map_node_link')->addFullLink($map->id, array('text'      => $label,
                                                                                    'node_id_1' => $databaseNodeA,
                                                                                    'node_id_2' => $databaseNodeB));

                $this->mvp2Map->addActivityLink($nodeA, $nodeB, $link);
            }
        }
    }

    private function getActivityNodeFromPath($activityNodePath) {
        if($activityNodePath == null || empty($activityNodePath)) { return null; }

        return str_replace('\']', '', str_replace('/ActivityModel/ActivityNodes/NodeSection/ActivityNode[@id=\'', '', $activityNodePath));
    }

    private function processActivityNodes($activityModelData, $map) {
        if(!isset($activityModelData['ActivityNodes']) || count($activityModelData['ActivityNodes']) <= 0) { return; }

        foreach($activityModelData['ActivityNodes'] as $nodeSection) {
            $nodeSectionId = $this->createNodeSection($nodeSection, $map);

            $databaseNodeSectionId = $this->mvp2Map->getActivityNodeSectionDatabaseId($nodeSectionId);
            if(isset($nodeSection['ActivityNode']) && count($nodeSection['ActivityNode']) > 0) {
                foreach($nodeSection['ActivityNode'] as $activityNode) {
                    $nodeId = $this->createNode($activityNode, $map);
                    $databaseNodeId = $this->mvp2Map->getActivityNodeDatabaseId($nodeId);
                    if($databaseNodeId != null) {
                        $this->createNodeRule($activityNode, $nodeId);
                        DB_ORM::model('map_node_section_node')->addNode($databaseNodeId, $databaseNodeSectionId);
                    }
                }
            }
        }
    }

    /**
     * Create map node section
     *
     * @param $nodeSection - MVP2 node section data
     * @param $map - map
     * @return - MVP2 node section ID
     */
    private function createNodeSection($nodeSection, $map) {
        if(!isset($nodeSection['@attributes']) || !isset($nodeSection['@attributes']['id'])) { return null; }

        $nodeSectionData = array();
        if(isset($nodeSection['@attributes']['label'])) { $nodeSectionData['sectionname'] = $nodeSection['@attributes']['label']; }

        $nodeSectionId = DB_ORM::model('map_node_section')->createSection($map->id, $nodeSectionData);
        $this->mvp2Map->addNodeSection($nodeSection['@attributes']['id'], $nodeSectionId->id);

        return $nodeSection['@attributes']['id'];
    }

    private function createNode($activityNode, $map) {
        if(!isset($activityNode['@attributes']) ||
           !isset($activityNode['@attributes']['id']) ||
           !isset($activityNode['Content'])) { return null; }

        $node = DB_ORM::model('map_node')->createNode(array('map_id' => $map->id,
                                                            'mnodetitle' => (isset($activityNode['@attributes']['label']) ? $activityNode['@attributes']['label'] : '')));

        $this->mvp2Map->addActivityNode($activityNode['@attributes']['id'], $node->id, $activityNode['Content']);

        return $activityNode['@attributes']['id'];
    }

    private function createNodeRule($activityNode, $nodeId) {
        if(!isset($activityNode['Rules']) || count($activityNode['Rules']) <= 0) { return; }

        $databaseNodeId = $this->mvp2Map->getActivityNodeDatabaseId($nodeId);
        if($databaseNodeId == null) { return; }

        foreach($activityNode['Rules'] as $rule) {
            if(isset($rule['CounterPath'])) {
                $databaseCounterId = $this->mvp2Map->getActivityCounterDatabaseId($this->getCounterIdFromPath($rule['CounterPath']));
                if($databaseCounterId != null) {
                    $function = '=';
                    if(isset($rule['CounterOperator'])) { $function = $rule['CounterOperator']; }
                    if(isset($rule['CounterRuleValue'])) { $function .= $rule['CounterRuleValue']; }

                    DB_ORM::model('map_node_counter')->addNodeCounter($databaseNodeId, $databaseCounterId, $function);
                }
            }

        }
    }

    private function getCounterIdFromPath($counterPath) {
        if($counterPath == null || empty($counterPath)) { return null; }

        return str_replace('\']', '', str_replace('/ActivityModel/Properties/Counters/Counter[@id = \'', '', $counterPath));
    }

    private function xml2array($file) {
        if($file == null || empty($file) || !file_exists($file)) return null;

        $xmlContent = file_get_contents($file);
        $xmlContent = str_replace('vpSim:', '', $xmlContent);
        $xmlObject  = simplexml_load_string($xmlContent);
        $jsonObject = json_encode($xmlObject);

        return json_decode($jsonObject, true);
    }
}