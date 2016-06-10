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
 * MVP format import/export system
 */
class ImportExport_AdvancedFormatSystem implements ImportExport_FormatSystem
{

    private $folderName;
    private $folderPath;
    private $mediaElements;
    private $manifest;

    /**
     * Export map to mvp format
     * @param array $parameters ('mapId')
     */
    public function export($parameters)
    {
        $mapId = $parameters['mapId'];

        $this->makeTempFolder($parameters['mapName']);
        $metadata['version'] = 'advanced';
        $this->createXMLFile('metadata', $metadata, false, false);

        $map = DB_SQL::select('default')->from('maps')->where('id', '=', $mapId)->query()->fetch(0);
        $this->createXMLFile('map', $map);

        $elements = $elements = DB_SQL::select('default')->from('map_elements')->where('map_id', '=',
            $mapId)->query()->as_array();
        $this->createXMLFile('map_element', $elements);
        $this->copyResourcesFiles($elements);

        $counters = DB_SQL::select('default')->from('map_counters')->where('map_id', '=', $mapId)->query()->as_array();
        $this->createXMLFile('map_counter', $counters);
        $elementsArray = $this->mergeArraysFromDB($counters, 'map_counter_rule');
        $this->createXMLFile('map_counter_rule', $elementsArray);

        $counterCommonRules = DB_SQL::select('default')->from('map_counter_common_rules')->where('map_id', '=',
            $mapId)->query()->as_array();
        $this->createXMLFile('map_counter_commonrules', $counterCommonRules);

        $avatars = DB_SQL::select('default')->from('map_avatars')->where('map_id', '=', $mapId)->query()->as_array();
        $this->createXMLFile('map_avatar', $avatars);
        $this->copyAvatarsImages($avatars);

        $chats = DB_SQL::select('default')->from('map_chats')->where('map_id', '=', $mapId)->query()->as_array();
        $this->createXMLFile('map_chat', $chats);
        $elementsArray = $this->mergeArraysFromDB($chats, 'map_chat_element');
        $this->createXMLFile('map_chat_element', $elementsArray);

        $nodes = DB_SQL::select('default')->from('map_nodes')->where('map_id', '=', $mapId)->query()->as_array();
        $this->createXMLFile('map_node', $nodes);

        $questions = DB_SQL::select('default')->from('map_questions')->where('map_id', '=',
            $mapId)->query()->as_array();
        $this->createXMLFile('map_question', $questions);
        $elementsArray = $this->mergeArraysFromDB($questions, 'map_question_response');
        $this->createXMLFile('map_question_response', $elementsArray);
        $validationArray = $this->mergeArraysFromDB($questions, 'map_question_validation');
        $this->createXMLFile('map_question_validation', $validationArray);

        $contributor = DB_SQL::select('default')->from('map_contributors')->where('map_id', '=',
            $mapId)->query()->as_array();
        $this->createXMLFile('map_contributor', $contributor);

        $feedbackRules = DB_SQL::select('default')->from('map_feedback_rules')->where('map_id', '=',
            $mapId)->query()->as_array();
        $this->createXMLFile('map_feedback_rule', $feedbackRules);

        $key = DB_SQL::select('default')->from('map_keys')->where('map_id', '=', $mapId)->query()->as_array();
        $this->createXMLFile('map_key', $key);

        $vpds = DB_SQL::select('default')->from('map_vpds')->where('map_id', '=', $mapId)->order_by('vpd_type_id',
            'ASC')->query()->as_array();
        $this->createXMLFile('map_vpd', $vpds);
        $elementsArray = $this->mergeArraysFromDB($vpds, 'map_vpd_element');
        $this->createXMLFile('map_vpd_element', $elementsArray);

        $dams = DB_SQL::select('default')->from('map_dams')->where('map_id', '=', $mapId)->query()->as_array();
        $this->createXMLFile('map_dam', $dams);
        $elementsArray = $this->mergeArraysFromDB($dams, 'map_dam_element');
        $this->createXMLFile('map_dam_element', $elementsArray);

        $nodeCounters = DB_SQL::select('default', array(
            'map_node_counters.id',
            'map_node_counters.node_id',
            'map_node_counters.counter_id',
            'map_node_counters.function',
            'map_node_counters.display'
        ))->from('map_counters')->join('RIGHT', 'map_node_counters')->on('map_node_counters.counter_id', '=',
            'map_counters.id')->where('map_id', '=', $mapId)->query()->as_array();
        $this->createXMLFile('map_node_counter', $nodeCounters);

        $links = DB_SQL::select('default')->from('map_node_links')->where('map_id', '=', $mapId)->query()->as_array();
        $this->createXMLFile('map_node_link', $links);

        $sections = DB_SQL::select('default')->from('map_node_sections')->where('map_id', '=',
            $mapId)->query()->as_array();
        $this->createXMLFile('map_node_section', $sections);
        $elementsArray = $this->mergeArraysFromDB($sections, 'map_node_section_node');
        $this->createXMLFile('map_node_section_node', $elementsArray);

        $visualDisplay = DB_SQL::select('default')->from('map_visual_displays')->where('map_id', '=',
            $mapId)->query()->as_array();
        $this->createXMLFile('map_visualdisplay', $visualDisplay);
        $visualDisplayCounters = $this->mergeArraysFromDB($visualDisplay, 'map_visualdisplay_counter');
        $this->createXMLFile('map_visualdisplay_counter', $visualDisplayCounters);
        $visualDisplayImages = $this->mergeArraysFromDB($visualDisplay, 'map_visualdisplay_image');
        $this->createXMLFile('map_visualdisplay_image', $visualDisplayImages);
        $visualDisplayPanels = $this->mergeArraysFromDB($visualDisplay, 'map_visualdisplay_panel');
        $this->createXMLFile('map_visualdisplay_panel', $visualDisplayPanels);
        $this->copyVDImages($visualDisplayImages, $mapId);

        $popups = DB_SQL::select('default')->from('map_popups')->where('map_id', '=', $mapId)->query()->as_array();
        $this->createXMLFile('map_popup', $popups);
        $popupsAssign = $this->mergeArraysFromDB($popups, 'map_popup_assign');
        $this->createXMLFile('map_popup_assign', $popupsAssign);
        $popupsCounters = $this->mergeArraysFromDB($popups, 'map_popup_counter');
        $this->createXMLFile('map_popup_counter', $popupsCounters);
        $popupsStyles = $this->mergeArraysFromDB($popups, 'map_popup_style');
        $this->createXMLFile('map_popup_style', $popupsStyles);

        $metadataStringFields = DB_SQL::select('default')->from('metadata_string_fields')->where('object_id', '=',
            $mapId)->query()->as_array();
        $this->createXMLFile('Metadata_LiteralRecord', $metadataStringFields);

        $metadataStringFields = DB_SQL::select('default')->from('metadata_skos_fields')->where('object_id', '=',
            $mapId)->query()->as_array();
        $this->createXMLFile('Metadata_SkosRecord', $metadataStringFields);

        $this->createXMLFile('media_elements', $this->mediaElements, false, true);
        $this->createXMLFile('manifest', $this->manifest, false, true);

        $result = $this->createZipArchive();
        $this->removeDir();

        return $result ? (DOCROOT . 'tmp/' . $this->folderName . '.zip') : '';
    }

    private function createXMLFile($name, $array, $addToManifest = true, $decode = true)
    {
        if (count($array)) {
            $xml = new SimpleXMLElement('<xml />');
            $arrayXml = $xml->addChild($name);
            $this->createXMLTree($arrayXml, $name, $array, $decode);

            $filePath = $this->folderPath . '/' . $name . '.xml';
            $f = fopen($filePath, 'w');
            if (function_exists('dom_import_simplexml')) {
                $dom = dom_import_simplexml($xml)->ownerDocument;
                $dom->formatOutput = true;
                $xmlObject = $dom;
            } else {
                $xmlObject = $xml;
            }
            $outputXML = str_replace('<?xml version="1.0"?>', '<?xml version="1.0" encoding="UTF-8"?>',
                $xmlObject->saveXML());
            fwrite($f, $outputXML);
            fclose($f);

            if ($addToManifest) {
                $index = 'file_' . count($this->manifest['files']);
                $this->manifest['files'][$index] = $name;
            }
        }
    }

    private function mergeArraysFromDB($rootElements, $model, $key = 'id')
    {
        $elementsArray = array();
        if (count($rootElements)) {
            foreach ($rootElements as $element) {
                $array = DB_ORM::model($model)->exportMVP($element[$key]);
                if (count($array)) {
                    $elementsArray = array_merge($elementsArray, $array);
                }
            }
        }

        return $elementsArray;
    }

    private function createXMLTree($xml, $name, $array, $decode)
    {
        if (count($array)) {
            foreach ($array as $key => $value) {
                if (is_array($value)) {
                    $this->createXMLTree($xml->addChild($name . '_' . $key), $name . '_' . $key, $value, $decode);
                } else {
                    if (!is_numeric($value)) {
                        if ($decode) {
                            $value = base64_encode($value);
                        }
                    }
                    $xml->addChild($key, $value);
                }
            }
        }
    }

    /**
     * Create zip archive
     * @return boolean
     */
    private function createZipArchive()
    {
        if (!is_dir($this->folderPath)) {
            return false;
        }

        $dest = DOCROOT . 'tmp/' . $this->folderName . '.zip';
        $zip = new ZipArchive();

        if ($h = opendir($this->folderPath)) {
            if ($zip->open($dest, ZIPARCHIVE::CREATE)) {
                while (false !== ($f = readdir($h))) {
                    if (strstr($f, '.') && file_exists($this->folderPath . '/' . $f) && strcmp($f,
                            '.') != 0 && strcmp($f, '..') != 0
                    ) {
                        $zip->addFile($this->folderPath . '/' . $f, $f);
                    }
                }
            }
            closedir($h);
        }

        if (is_dir($this->folderPath . '/media') && $zip) {
            if ($h = opendir($this->folderPath . '/media')) {
                if ($zip->addEmptyDir('media')) {
                    while (false !== ($f = readdir($h))) {
                        if (strstr($f, '.') && file_exists($this->folderPath . '/media/' . $f) && strcmp($f,
                                '.') != 0 && strcmp($f, '..') != 0
                        ) {
                            $zip->addFile($this->folderPath . '/media/' . $f, 'media/' . $f);
                        }
                    }

                    if (is_dir($this->folderPath . '/media/vdImages')) {
                        $vdImagesOpenDir = opendir($this->folderPath . '/media/vdImages');
                        if ($vdImagesOpenDir AND $zip->addEmptyDir('media/vdImages')) {
                            while (false !== ($file = readdir($vdImagesOpenDir))) {
                                if (strstr($file,
                                        '.') && file_exists($this->folderPath . '/media/vdImages/' . $file) && strcmp($file,
                                        '.') != 0 && strcmp($file, '..') != 0
                                ) {
                                    $zip->addFile($this->folderPath . '/media/vdImages/' . $file,
                                        'media/vdImages/' . $file);
                                }
                            }

                            $vdThumbsOpenDir = opendir($this->folderPath . '/media/vdImages/thumbs');
                            if ($vdThumbsOpenDir AND $zip->addEmptyDir('media/vdImages/thumbs')) {
                                while (false !== ($fileThumbs = readdir($vdThumbsOpenDir))) {
                                    if (strstr($fileThumbs,
                                            '.') && file_exists($this->folderPath . '/media/vdImages/thumbs/' . $fileThumbs) && strcmp($fileThumbs,
                                            '.') != 0 && strcmp($fileThumbs, '..') != 0
                                    ) {
                                        $zip->addFile($this->folderPath . '/media/vdImages/thumbs/' . $fileThumbs,
                                            'media/vdImages/thumbs/' . $fileThumbs);
                                    }
                                }
                            }
                            closedir($vdThumbsOpenDir);
                        }
                        closedir($vdImagesOpenDir);
                    }
                }
                closedir($h);
            }
        }

        $zip->close();

        return true;
    }

    /**
     * Remove temp directory
     * @return none
     */
    private function removeDir()
    {
        if (!is_dir($this->folderPath)) {
            return;
        }

        $mediaFolder = $this->folderPath . '/media';
        if (is_dir($mediaFolder) AND ($h = opendir($mediaFolder))) {
            if ($vdImagesOpenDSir = opendir($this->folderPath . '/media/vdImages')) {
                if ($vdThumbsOpenDSir = opendir($this->folderPath . '/media/vdImages/thumbs')) {
                    while (false !== ($fileThumbs = readdir($vdThumbsOpenDSir))) {
                        if (strstr($fileThumbs,
                                '.') && file_exists($this->folderPath . '/media/vdImages/thumbs/' . $fileThumbs) && strcmp($fileThumbs,
                                '.') != 0 && strcmp($fileThumbs, '..') != 0
                        ) {
                            unlink($this->folderPath . '/media/vdImages/thumbs/' . $fileThumbs);
                        }
                    }
                    closedir($vdThumbsOpenDSir);
                    rmdir($this->folderPath . '/media/vdImages/thumbs');
                }

                while (false !== ($file = readdir($vdImagesOpenDSir))) {
                    if (strstr($file,
                            '.') && file_exists($this->folderPath . '/media/vdImages/' . $file) && strcmp($file,
                            '.') != 0 && strcmp($file, '..') != 0
                    ) {
                        unlink($this->folderPath . '/media/vdImages/' . $file);
                    }
                }
                closedir($vdImagesOpenDSir);
                rmdir($this->folderPath . '/media/vdImages');
            }

            while (false !== ($f = readdir($h))) {
                if (file_exists($this->folderPath . '/media/' . $f) AND strcmp($f, '.') != 0 AND strcmp($f,
                        '..') != 0
                ) {
                    unlink($this->folderPath . '/media/' . $f);
                }
            }
            closedir($h);
            rmdir($mediaFolder);
        }

        if ($h = opendir($this->folderPath)) {
            while (false !== ($f = readdir($h))) {
                if (file_exists($this->folderPath . '/' . $f) && strcmp($f, '.') != 0 && strcmp($f, '..') != 0) {
                    unlink($this->folderPath . '/' . $f);
                }
            }
            closedir($h);
            rmdir($this->folderPath);
        }
    }

    /**
     * Import map from mvp format
     * @param array $parameters
     */
    var $labyrinthArray = array();

    public function import($tmpFolder)
    {
        $manifest = $this->xml2array($tmpFolder, 'manifest');
        if (count($manifest['manifest_files'])) {
            foreach ($manifest['manifest_files'] as $file) {
                $this->labyrinthArray[$file] = $this->xml2array($tmpFolder, $file);
                if (count($this->labyrinthArray[$file])) {
                    $this->labyrinthArray[$file] = $this->addToDB($file, $this->labyrinthArray[$file]);
                }
            }
        }
        $nodeContentElements = array(
            'MR' => 'map_element',
            'CHAT' => 'map_chat',
            'DAM' => 'map_dam',
            'AV' => 'map_avatar',
            'VPD' => 'map_vpd',
            'QU' => 'map_question',
            'CR' => 'map_counter',
            'CD' => 'map_visualdisplay',
            'NODE' => 'map_node',
            'BUTTON' => 'map_node',
        );

        $searchArray = array();
        $replaceArray = array();
        foreach ($nodeContentElements as $modelKey => $model) {
            if (isset($this->labyrinthArray[$model]) AND count($this->labyrinthArray[$model])) {
                foreach ($this->labyrinthArray[$model] as $key => $array) {
                    $searchArray[] = '[[' . $modelKey . ':' . $key . ']]';
                    $replaceArray[] = '[[' . $modelKey . ':' . $array['database_id'] . ']]';
                }
            }
        }
        $searchArray[] = 'renderLabyrinth/index/' . $this->labyrinthArray['map']['id'];
        $replaceArray[] = 'renderLabyrinth/index/' . $this->labyrinthArray['map']['database_id'];

        if (isset($this->labyrinthArray['map_node']) AND count($this->labyrinthArray['map_node'])) {
            $searchNodeIDArray = array();
            $replaceNodeIDArray = array();

            $searchNodeLinksArray = array();
            $replaceNodeLinksArray = array();

            $searchFilesLinks = array();
            $replaceFilesLinks = array();

            foreach ($this->labyrinthArray['map_node'] as $key => $node) {
                $searchNodeIDArray[] = '[' . $key . ']';
                $replaceNodeIDArray[] = '[' . $node['database_id'] . ']';

                $searchNodeLinksArray[] = 'renderLabyrinth/go/' . $this->labyrinthArray['map']['id'] . '/' . $key;
                $replaceNodeLinksArray[] = 'renderLabyrinth/go/' . $this->labyrinthArray['map']['database_id'] . '/' . $node['database_id'];

                $searchFilesLinks[] = 'files/' . $this->labyrinthArray['map']['id'] . '/';
                $replaceFilesLinks[] = 'files/' . $this->labyrinthArray['map']['database_id'] . '/';
            }

            foreach ($this->labyrinthArray['map_node'] as $key => $node) {
                $md5Text = md5($node['text']);
                $node['text'] = str_replace($searchArray, $replaceArray, $node['text']);
                $node['text'] = str_replace($searchNodeLinksArray, $replaceNodeLinksArray, $node['text']);
                $node['text'] = str_replace($searchFilesLinks, $replaceFilesLinks, $node['text']);
                $node['text'] = str_replace('[[INFO:' . $key . ']]', '[[INFO:' . $node['database_id'] . ']]',
                    $node['text']);
                preg_match('/\b(?:(?:https?):\/\/|www\.)(.)+(\/renderLabyrinth|\/files\/)/i', $node['text'],
                    $oldDomainLink);
                if (!empty($oldDomainLink[0])) {
                    $oldDomainName = parse_url($oldDomainLink[0], PHP_URL_HOST);
                    $node['text'] = str_replace($oldDomainName, $_SERVER['HTTP_HOST'], $node['text']);
                }

                $newMd5Text = md5($node['text']);
                $md5Conditional = md5($node['conditional']);
                $node['conditional'] = str_replace($searchNodeIDArray, $replaceNodeIDArray, $node['conditional']);
                $newMd5Conditional = md5($node['conditional']);

                if (($md5Text != $newMd5Text) || ($md5Conditional != $newMd5Conditional)) {
                    $nodeDB = DB_ORM::model('map_node', array((int)$node['database_id']));
                    if ($nodeDB->is_loaded()) {
                        if ($md5Text != $newMd5Text) {
                            $nodeDB->text = $node['text'];
                        }
                        if ($md5Conditional != $newMd5Conditional) {
                            $nodeDB->conditional = $node['conditional'];
                        }
                        $nodeDB->save();
                    }
                }
            }
        }

        if (isset($this->labyrinthArray['map_counter_commonrules'])) {
            foreach ($this->labyrinthArray['map_counter_commonrules'] as $rule) {
                $md5Rule = md5($rule['rule']);
                $rule['rule'] = str_replace($searchArray, $replaceArray, $rule['rule']);
                $newMd5Rule = md5($rule['rule']);
                if ($md5Rule != $newMd5Rule) {
                    $ruleDB = DB_ORM::model('map_counter_commonrules', array((int)$rule['database_id']));
                    if ($ruleDB->is_loaded()) {
                        $ruleDB->rule = $rule['rule'];
                        $ruleDB->save();
                    }
                }
            }
        }

        if (isset($this->labyrinthArray['map_question'])) {
            foreach ($this->labyrinthArray['map_question'] as $q) {
                $md5Q = md5($q['settings']);
                $q['settings'] = str_replace($searchArray, $replaceArray, $q['settings']);
                $newMd5Q = md5($q['settings']);
                if ($md5Q != $newMd5Q) {
                    $qDB = DB_ORM::model('map_question', array((int)$q['database_id']));
                    if ($qDB->is_loaded()) {
                        $qDB->settings = $q['settings'];
                        $qDB->save();
                    }
                }
            }
        }

        $mediaElements = $this->xml2array($tmpFolder, 'media_elements');
        if (isset($mediaElements['media_elements_avatars']) AND count($mediaElements['media_elements_avatars'])) {
            foreach ($mediaElements['media_elements_avatars'] as $avatar) {
                $filePath = $tmpFolder . 'media/' . $avatar;
                if (file_exists($filePath)) {
                    copy($filePath, DOCROOT . 'avatars/' . $avatar);
                }
            }
        }

        if (!file_exists(DOCROOT . '/files/' . $this->labyrinthArray['map']['database_id'])) {
            mkdir(DOCROOT . '/files/' . $this->labyrinthArray['map']['database_id'], DEFAULT_FOLDER_MODE, true);
        }

        if (isset($mediaElements['media_elements_files']) AND count($mediaElements['media_elements_files'])) {
            foreach ($mediaElements['media_elements_files'] as $file) {
                $filePath = $tmpFolder . 'media/' . $file;
                if (file_exists($filePath)) {
                    copy($filePath, DOCROOT . 'files/' . $this->labyrinthArray['map']['database_id'] . '/' . $file);
                }
            }
        }

        if (!file_exists(DOCROOT . '/files/' . $this->labyrinthArray['map']['database_id'] . '/vdImages')) {
            mkdir(DOCROOT . '/files/' . $this->labyrinthArray['map']['database_id'] . '/vdImages', DEFAULT_FOLDER_MODE, true);
        }

        if (isset($mediaElements['media_elements_vdimages']) AND count($mediaElements['media_elements_vdimages'])) {
            foreach ($mediaElements['media_elements_vdimages'] as $file) {
                $filePath = $tmpFolder . 'media/vdImages/' . $file;
                if (file_exists($filePath)) {
                    copy($filePath,
                        DOCROOT . 'files/' . $this->labyrinthArray['map']['database_id'] . '/vdImages/' . $file);
                }
            }
        }

        if (!file_exists(DOCROOT . '/files/' . $this->labyrinthArray['map']['database_id'] . '/vdImages/thumbs')) {
            mkdir(DOCROOT . '/files/' . $this->labyrinthArray['map']['database_id'] . '/vdImages/thumbs', DEFAULT_FOLDER_MODE, true);
        }

        if (isset($mediaElements['media_elements_vdimages_thumbs']) AND count($mediaElements['media_elements_vdimages_thumbs'])) {
            foreach ($mediaElements['media_elements_vdimages_thumbs'] as $file) {
                $filePath = $tmpFolder . 'media/vdImages/thumbs/' . $file;
                if (file_exists($filePath)) {
                    copy($filePath,
                        DOCROOT . 'files/' . $this->labyrinthArray['map']['database_id'] . '/vdImages/thumbs/' . $file);
                }
            }
        }

        return true;
    }

    private function xml2array($filePath, $fileName)
    {
        $array = null;
        $globalPath = $filePath . '/' . $fileName . '.xml';
        if (file_exists($globalPath)) {
            $xmlFile = file_get_contents($globalPath);
            $ob = simplexml_load_string($xmlFile);
            $json = json_encode($ob);
            $array = json_decode($json, true);
            $array = $this->convertValuesInArray($array[$fileName]);
        }

        return $array;
    }

    private function addToDB($modelName, $data)
    {
        $returnData = null;
        if (isset($data[$modelName . '_0']) && (is_array($data[$modelName . '_0']))) {
            foreach ($data as $d) {
                $returnData[$d['id']] = $d;
                $returnData[$d['id']]['database_id'] = $this->insertInDB($modelName, $d);
            }
        } else {
            $returnData['id'] = $data['id'];
            $returnData['database_id'] = $this->insertInDB($modelName, $data);
        }

        return $returnData;
    }

    private function insertInDB($modelName, $data)
    {
        $builder = DB_ORM::insert($modelName);
        $skipColumns = array('id');

        if ($modelName == 'map') {
            $data['name'] = DB_ORM::model('map')->getMapName($data['name']);
            $data['author_id'] = Auth::instance()->get_user()->id;
            $data['skin_id'] = 1;
        }

        if ($modelName == 'map_node') {
            $data['x'] = ($data['x'] == '') ? null : (int)$data['x'];
            $data['y'] = ($data['y'] == '') ? null : (int)$data['y'];
            $data['rgb'] = ($data['rgb'] == '') ? null : $data['rgb'];
            $data['kfp'] = ($data['kfp'] == '') ? 0 : 1;
            $data['undo'] = ($data['undo'] == '') ? 0 : 1;
            $data['end'] = ($data['end'] == '') ? 0 : 1;
        }

        if ($modelName == 'map_dam_element') {
            if (isset($this->labyrinthArray['map_dam'][$data['dam_id']])) {
                $data['dam_id'] = $this->labyrinthArray['map_dam'][$data['dam_id']]['database_id'];
            }

            switch ($data['element_type']) {
                case 'vpd':
                    if (isset($this->labyrinthArray['map_vpd'][$data['element_id']])) {
                        $data['element_id'] = $this->labyrinthArray['map_vpd'][$data['element_id']]['database_id'];
                    }
                    break;
                case 'mr':
                    if (isset($this->labyrinthArray['map_element'][$data['element_id']])) {
                        $data['element_id'] = $this->labyrinthArray['map_element'][$data['element_id']]['database_id'];
                    }
                    break;
                case 'dam':
                    if (isset($this->labyrinthArray['map_dam'][$data['element_id']])) {
                        $data['element_id'] = $this->labyrinthArray['map_dam'][$data['element_id']]['database_id'];
                    }
                    break;
            }
        }

        if ($modelName == 'map_element') {
            $data['path'] = 'files/' . $this->labyrinthArray['map']['database_id'] . '/' . $data['name'];
        }

        if ($modelName == 'map_node_link') {
            if (isset($this->labyrinthArray['map_node'][$data['node_id_1']])) {
                $data['node_id_1'] = $this->labyrinthArray['map_node'][$data['node_id_1']]['database_id'];
            }
            if (isset($this->labyrinthArray['map_node'][$data['node_id_2']])) {
                $data['node_id_2'] = $this->labyrinthArray['map_node'][$data['node_id_2']]['database_id'];
            }
        }

        if ($modelName == 'map_node_section_node' AND isset($this->labyrinthArray['map_node_section'][$data['section_id']])) {
            $data['section_id'] = $this->labyrinthArray['map_node_section'][$data['section_id']]['database_id'];
        }

        if (isset($data['map_id'])) {
            $data['map_id'] = $this->labyrinthArray['map']['database_id'];
        }

        if ($modelName == 'Metadata_LiteralRecord' OR $modelName == 'Metadata_SkosRecord') {
            $data['object_id'] = $this->labyrinthArray['map']['database_id'];
        }

        if (isset($data['chat_id']) AND isset($this->labyrinthArray['map_chat'][$data['chat_id']])) {
            $data['chat_id'] = $this->labyrinthArray['map_chat'][$data['chat_id']]['database_id'];
        }

        if (isset($data['counter_id'])) {
            $data['counter_id'] = isset($this->labyrinthArray['map_counter'][$data['counter_id']])
                ? $this->labyrinthArray['map_counter'][$data['counter_id']]['database_id']
                : 0;
        }

        if (isset($data['node_id']) AND isset($this->labyrinthArray['map_node'][$data['node_id']])) {
            $data['node_id'] = $this->labyrinthArray['map_node'][$data['node_id']]['database_id'];
        }

        if (isset($data['question_id']) AND isset($this->labyrinthArray['map_question'][$data['question_id']])) {
            $data['question_id'] = $this->labyrinthArray['map_question'][$data['question_id']]['database_id'];
        }

        if (isset($data['vpd_id']) AND isset($this->labyrinthArray['map_vpd'][$data['vpd_id']])) {
            $data['vpd_id'] = $this->labyrinthArray['map_vpd'][$data['vpd_id']]['database_id'];
        }

        if (isset($data['redirect_node_id'])) {
            if ($data['redirect_node_id'] == '') {
                $data['redirect_node_id'] = null;
            } elseif (isset($this->labyrinthArray['map_node'][$data['redirect_node_id']])) {
                $data['redirect_node_id'] = $this->labyrinthArray['map_node'][$data['redirect_node_id']]['database_id'];
            }
        }

        if (isset($data['visual_id']) AND isset($this->labyrinthArray['map_visualdisplay'][$data['visual_id']])) {
            $data['visual_id'] = $this->labyrinthArray['map_visualdisplay'][$data['visual_id']]['database_id'];
        }

        if (isset($data['popup_id']) AND isset($this->labyrinthArray['map_popup'][$data['popup_id']])) {
            $data['popup_id'] = $this->labyrinthArray['map_popup'][$data['popup_id']]['database_id'];
        }

        if (isset($data['map_popup_id']) AND isset($this->labyrinthArray['map_popup'][$data['map_popup_id']])) {
            $data['map_popup_id'] = $this->labyrinthArray['map_popup'][$data['map_popup_id']]['database_id'];
            if ($modelName == 'map_popup_assign') {
                $data['assign_to_id'] = $this->labyrinthArray['map_node'][$data['assign_to_id']]['database_id'];
                if ($data['redirect_to_id']) {
                    $data['redirect_to_id'] = $this->labyrinthArray['map_node'][$data['redirect_to_id']]['database_id'];
                }
            }
        }

        $model = DB_ORM::model($modelName);
        foreach ($data as $key => $value) {
            if (!in_array($key, $skipColumns) AND $model->is_field($key)) {
                $builder->column($key, $value);
            }
        }

        return $builder->execute();
    }

    private function convertValuesInArray($array)
    {
        if (count($array) > 0) {
            foreach ($array as $key => $value) {
                if (count($value) > 0) {
                    if (is_array($value)) {
                        $value = $this->convertValuesInArray($value);
                    } elseif (!is_numeric($value)) {
                        $value = base64_decode($value);
                    } else {
                        if (is_numeric($value[0])) {
                            $value = is_float($value) ? floatval($value) : intval($value);
                        }
                    }
                } else {
                    $value = '';
                }

                $array[$key] = $value;
            }
        }

        return $array;
    }

    /**
     * Create temp folder
     */
    private function makeTempFolder($mapName = null)
    {
        $this->folderPath = DOCROOT . 'tmp/';

        $this->folderName = ($mapName != null)
            ? preg_replace("/[^A-Za-z0-9]/", "", $mapName) . '_advanced_' . rand()
            : 'advanced_' . rand();

        if (is_dir($this->folderPath . $this->folderName)) {
            $this->folderName .= '_' . rand();
        }

        $this->folderPath .= $this->folderName;
        mkdir($this->folderPath);
    }

    /**
     * Create media folder
     * @return none
     */
    private function makeMediaFolder()
    {
        if (!is_dir($this->folderPath)) {
            return;
        }

        if (!is_dir($this->folderPath . '/media')) {
            mkdir($this->folderPath . '/media');
        }
    }

    private function copyVDImages($images, $mapId)
    {
        if (!is_dir($this->folderPath) AND count($images) == 0) {
            return;
        }

        if (!is_dir($this->folderPath . '/media/vdImages')) {
            mkdir($this->folderPath . '/media/vdImages', DEFAULT_FOLDER_MODE, true);
        }
        if (!is_dir($this->folderPath . '/media/vdImages/thumbs')) {
            mkdir($this->folderPath . '/media/vdImages/thumbs');
        }

        foreach ($images as $image) {
            $imageName = Arr::get($image, 'name', false);
            if ($imageName) {
                $imagePath = DOCROOT . 'files/' . $mapId . '/vdImages/' . $imageName;
                if (file_exists($imagePath)) {
                    copy($imagePath, $this->folderPath . '/media/vdImages/' . $imageName);
                    $index = (isset($this->mediaElements['vdimages'])) ? count($this->mediaElements['vdimages']) : 0;
                    $this->mediaElements['vdimages']['vdimages_' . $index] = $imageName;

                    // create vd thumbs file
                    $imageThumbsPath = DOCROOT . 'files/' . $mapId . '/vdImages/thumbs/' . $imageName;
                    if (file_exists($imageThumbsPath)) {
                        copy($imageThumbsPath, $this->folderPath . '/media/vdImages/thumbs/' . $imageName);
                        $index = (isset($this->mediaElements['vdimages_thumbs'])) ? count($this->mediaElements['vdimages_thumbs']) : 0;
                        $this->mediaElements['vdimages_thumbs']['vdimages_thumbs_' . $index] = $imageName;
                    }
                }
            }
        }
    }

    /**
     * Copy all exist avatars generated images
     * @param array(map_avatar) $avatars
     */
    private function copyAvatarsImages($avatars)
    {
        if (!is_dir($this->folderPath) || count($avatars) <= 0) {
            return;
        }

        $this->makeMediaFolder();
        foreach ($avatars as $avatar) {
            if (($avatar['image'] != 'ntr') AND ($avatar['image'] != '')) {
                $avatarImagePath = DOCROOT . 'avatars/' . $avatar['image'];
                if (file_exists($avatarImagePath) && is_dir($this->folderPath . '/media')) {
                    copy($avatarImagePath, $this->folderPath . '/media/' . $avatar['image']);
                    $index = (isset($this->mediaElements['avatars'])) ? count($this->mediaElements['avatars']) : 0;
                    $this->mediaElements['avatars']['avatar_' . $index] = $avatar['image'];
                }
            }
        }
    }

    /**
     * Copy all resource file to media folder
     * @param array(map_element) $elements
     */
    private function copyResourcesFiles($elements)
    {
        if (count($elements) <= 0) {
            return;
        }

        $this->makeMediaFolder();

        foreach ($elements as $e) {
            $elementPath = DOCROOT . $e['path'];
            if (file_exists($elementPath) AND is_dir($this->folderPath . '/media')) {
                $pathInfo = pathinfo($elementPath);
                $extension = Arr::get($pathInfo, 'extension');
                $extension = $extension ? '.' . $extension : '';

                copy($elementPath, $this->folderPath . '/media/' . $pathInfo['filename'] . $extension);
                $index = (isset($this->mediaElements['files'])) ? count($this->mediaElements['files']) : 0;
                $this->mediaElements['files']['file_' . $index] = $pathInfo['filename'] . $extension;
            }
        }
    }
}