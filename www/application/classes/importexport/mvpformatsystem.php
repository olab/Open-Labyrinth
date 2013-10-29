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
class ImportExport_MVPFormatSystem implements ImportExport_FormatSystem {
    
    private $folderName;
    private $folderPath;
    private $mediaElements;
    private $manifest;
    
    /**
     * Export map to mvp format
     * @param array $parameters ('mapId')
     */
    public function export($parameters) {
        $mapId = (int)$parameters['mapId'];
        if($mapId <= 0) return '';
        
        $this->makeTempFolder($parameters['mapName']);

        $metadata['version'] = 3;
        $this->createXMLFile('metadata', $metadata, false, false);

        $map = DB_ORM::model('map')->exportMVP($mapId);
        $this->createXMLFile('map', $map);

        $elements = DB_ORM::model('map_element')->exportMVP($map['id']);
        $this->createXMLFile('map_element', $elements);
        $this->copyResourcesFiles($elements);

        $counters = DB_ORM::model('map_counter')->exportMVP($map['id']);
        $this->createXMLFile('map_counter', $counters);

        $counter_commonrules = DB_ORM::model('map_counter_commonrules')->exportMVP($map['id']);
        $this->createXMLFile('map_counter_commonrules', $counter_commonrules);

        $avatars = DB_ORM::model('map_avatar')->exportMVP($map['id']);
        $this->createXMLFile('map_avatar', $avatars);
        $this->copyAvatarsImages($avatars);

        $chats = DB_ORM::model('map_chat')->exportMVP($map['id']);
        $this->createXMLFile('map_chat', $chats);
        $elementsArray = $this->mergeArraysFromDB($chats, 'map_chat_element');
        $this->createXMLFile('map_chat_element', $elementsArray);

        $nodes = DB_ORM::model('map_node')->exportMVP($map['id']);
        $this->createXMLFile('map_node', $nodes);

        $questions = DB_ORM::model('map_question')->exportMVP($map['id']);
        $this->createXMLFile('map_question', $questions);
        $elementsArray = $this->mergeArraysFromDB($questions, 'map_question_response');
        $this->createXMLFile('map_question_response', $elementsArray);

        $contributor = DB_ORM::model('map_contributor')->exportMVP($map['id']);
        $this->createXMLFile('map_contributor', $contributor);

        $feedback_rules = DB_ORM::model('map_feedback_rule')->exportMVP($map['id']);
        $this->createXMLFile('map_feedback_rule', $feedback_rules);

        $key = DB_ORM::model('map_key')->exportMVP($map['id']);
        $this->createXMLFile('map_key', $key);

        $vpds = DB_ORM::model('map_vpd')->exportMVP($map['id']);
        $this->createXMLFile('map_vpd', $vpds);
        $elementsArray = $this->mergeArraysFromDB($vpds, 'map_vpd_element');
        $this->createXMLFile('map_vpd_element', $elementsArray);

        $dams = DB_ORM::model('map_dam')->exportMVP($map['id']);
        $this->createXMLFile('map_dam', $dams);
        $elementsArray = $this->mergeArraysFromDB($dams, 'map_dam_element');
        $this->createXMLFile('map_dam_element', $elementsArray);

        $node_counters = DB_ORM::model('map_node_counter')->exportMVP($map['id']);
        $this->createXMLFile('map_node_counter', $node_counters);
        $links = DB_ORM::model('map_node_link')->exportMVP($map['id']);
        $this->createXMLFile('map_node_link', $links);
        $sections = DB_ORM::model('map_node_section')->exportMVP($map['id']);
        $this->createXMLFile('map_node_section', $sections);
        $elementsArray = $this->mergeArraysFromDB($sections, 'map_node_section_node');
        $this->createXMLFile('map_node_section_node', $elementsArray);

        //$users = DB_ORM::model('map_user')->exportMVP($map['id']);
        //$this->createXMLFile('map_user', $users);

        $elementsArray = $this->mergeArraysFromDB($counters, 'map_counter_rule');
        $this->createXMLFile('map_counter_rule', $elementsArray);

        $this->createXMLFile('media_elements', $this->mediaElements, false, true);
        $this->createXMLFile('manifest', $this->manifest, false, true);

        $result = $this->createZipArchive();
        
        $this->removeDir();
        
        return $result ? (DOCROOT . 'tmp/' . $this->folderName . '.zip') : '';
    }

    private function createXMLFile($name, $array, $addToManifest = true, $decode = true){
        if (count($array) > 0){
            $xml = new SimpleXMLElement('<xml />');
            $arrayXml = $xml->addChild($name);
            $this->createXMLTree($arrayXml, $name, $array, $decode);

            $filePath = $this->folderPath . '/'.$name.'.xml';
            $f = fopen($filePath, 'w');
            $dom = dom_import_simplexml($xml)->ownerDocument;
            $dom->formatOutput = true;
            $outputXML = str_replace('<?xml version="1.0"?>', '<?xml version="1.0" encoding="UTF-8"?>', $dom->saveXML());
            fwrite($f, $outputXML);
            fclose($f);

            if ($addToManifest){
                $index = 'file_'.count($this->manifest['files']);
                $this->manifest['files'][$index] = $name;
            }
        }
    }

    private function mergeArraysFromDB($rootElements, $model, $key = 'id'){
        $elementsArray = array();
        if (count($rootElements) > 0){
            foreach($rootElements as $element){
                $array = DB_ORM::model($model)->exportMVP($element[$key]);
                if (count($array) > 0){
                    $elementsArray = array_merge($elementsArray, $array);
                }
            }
        }
        return $elementsArray;
    }

    private function createXMLTree($xml, $name, $array, $decode){
        if (count($array) > 0){
            foreach($array as $key => $value){
                if(is_array($value)){
                    $this->createXMLTree($xml->addChild($name.'_'.$key), $name.'_'.$key, $value, $decode);
                }else{
                    if (!is_numeric($value)){
                        if ($decode){
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
    private function createZipArchive() {
        if(!is_dir($this->folderPath)) return false;
        
        $dest = DOCROOT . 'tmp/' . $this->folderName . '.zip';
        $zip = new ZipArchive();
        
        if($h = opendir($this->folderPath)) {
            if($zip->open($dest, ZIPARCHIVE::CREATE)) {
                while(false !== ($f = readdir($h))) {
                    if(strstr($f, '.') && file_exists($this->folderPath . '/' . $f) && strcmp($f, '.') != 0 && strcmp($f, '..') != 0) {
                        $zip->addFile($this->folderPath . '/' . $f, $f);
                    }
                }
            }
            
            closedir($h);
        }
        
        if(is_dir($this->folderPath . '/media') && $zip != null) {
            if($h = opendir($this->folderPath . '/media')) {
                if($zip->addEmptyDir('media')) {
                    while(false !== ($f = readdir($h))) {
                        if(strstr($f, '.') && file_exists($this->folderPath . '/media/' . $f) && strcmp($f, '.') != 0 && strcmp($f, '..') != 0) {
                            $zip->addFile($this->folderPath . '/media/' . $f, 'media/' . $f);
                        }
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
    private function removeDir() {
        if(!is_dir($this->folderPath)) return;
        
        if(is_dir($this->folderPath . '/media')) {
            if($h = opendir($this->folderPath . '/media')) {
                while(false !== ($f = readdir($h))) {
                    if(strstr($f, '.') && file_exists($this->folderPath . '/media/' . $f) && strcmp($f, '.') != 0 && strcmp($f, '..') != 0) {
                        unlink($this->folderPath . '/media/' . $f);
                    }
                }

                closedir($h);
                rmdir($this->folderPath . '/media');
            }
        }
        
        if($h = opendir($this->folderPath)) {
            while(false !== ($f = readdir($h))) {
                if(strstr($f, '.') && file_exists($this->folderPath . '/' . $f) && strcmp($f, '.') != 0 && strcmp($f, '..') != 0) {
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
    public function import($tmpFolder) {
        $manifest = $this->xml2array($tmpFolder, 'manifest');
        if (count($manifest['manifest_files']) > 0){
            foreach($manifest['manifest_files'] as $file){
                $this->labyrinthArray[$file] = $this->xml2array($tmpFolder, $file);
                if (count($this->labyrinthArray[$file]) > 0){
                    $this->labyrinthArray[$file] = $this->addToDB($file, $this->labyrinthArray[$file]);
                }
            }
        }

        $nodeContentElements = array('MR' => 'map_element',
            'CHAT' => 'map_chat',
            'DAM' => 'map_dam',
            'AV' => 'map_avatar',
            'VPD' => 'map_vpd',
            'QU' => 'map_question',
            'CR' => 'map_counter',
            'NODE' => 'map_node');

        $searchArray = array();
        $replaceArray = array();
        foreach($nodeContentElements as $modelKey => $model){
            if (isset($this->labyrinthArray[$model])){
                if (count($this->labyrinthArray[$model]) > 0){
                    foreach($this->labyrinthArray[$model] as $key => $array){
                        $searchArray[] = '[['.$modelKey.':'.$key.']]';
                        $replaceArray[] = '[['.$modelKey.':'.$array['database_id'].']]';
                    }
                }
            }
        }

        $searchArray[] = 'renderLabyrinth/index/'.$this->labyrinthArray['map']['id'];
        $replaceArray[] = 'renderLabyrinth/index/'.$this->labyrinthArray['map']['database_id'];

        if (isset($this->labyrinthArray['map_node'])){
            if (count($this->labyrinthArray['map_node']) > 0){
                $searchNodeIDArray = array();
                $replaceNodeIDArray = array();

                $searchNodeLinksArray = array();
                $replaceNodeLinksArray = array();
                foreach($this->labyrinthArray['map_node'] as $key => $node){
                    $searchNodeIDArray[] = '['.$key.']';
                    $replaceNodeIDArray[] = '['.$node['database_id'].']';

                    $searchNodeLinksArray[] = 'renderLabyrinth/go/'.$this->labyrinthArray['map']['id'].'/'.$key;
                    $replaceNodeLinksArray[] = 'renderLabyrinth/go/'.$this->labyrinthArray['map']['database_id'].'/'.$node['database_id'];
                }
                foreach($this->labyrinthArray['map_node'] as $key => $node){
                    $md5Text = md5($node['text']);
                    $node['text'] = str_replace($searchArray, $replaceArray, $node['text']);
                    $node['text'] = str_replace($searchNodeLinksArray, $replaceNodeLinksArray, $node['text']);
                    $node['text'] = str_replace('[[INFO:'.$key.']]', '[[INFO:'.$node['database_id'].']]', $node['text']);
                    $newMd5Text = md5($node['text']);

                    $md5Conditional = md5($node['conditional']);
                    $node['conditional'] = str_replace($searchNodeIDArray, $replaceNodeIDArray, $node['conditional']);
                    $newMd5Conditional = md5($node['conditional']);

                    if (($md5Text != $newMd5Text) || ($md5Conditional != $newMd5Conditional)){
                        $nodeDB = DB_ORM::model('map_node', array((int) $node['database_id']));
                        if ($nodeDB->is_loaded()){
                            if ($md5Text != $newMd5Text){
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
        }

        if (isset($this->labyrinthArray['map_counter_commonrules'])){
            foreach($this->labyrinthArray['map_counter_commonrules'] as $key => $rule){
                $md5Rule = md5($rule['rule']);
                $rule['rule'] = str_replace($searchArray, $replaceArray, $rule['rule']);
                $newMd5Rule = md5($rule['rule']);
                if ($md5Rule != $newMd5Rule){
                    $ruleDB = DB_ORM::model('map_counter_commonrules', array((int) $rule['database_id']));
                    if ($ruleDB->is_loaded()){
                        $ruleDB->rule = $rule['rule'];
                        $ruleDB->save();
                    }
                }
            }
        }

        if (isset($this->labyrinthArray['map_question'])){
            foreach($this->labyrinthArray['map_question'] as $key => $q){
                $md5Q = md5($q['settings']);
                $q['settings'] = str_replace($searchArray, $replaceArray, $q['settings']);
                $newMd5Q = md5($q['settings']);
                if ($md5Q != $newMd5Q){
                    $qDB = DB_ORM::model('map_question', array((int) $q['database_id']));
                    if ($qDB->is_loaded()){
                        $qDB->settings = $q['settings'];
                        $qDB->save();
                    }
                }
            }
        }

        $mediaElements = $this->xml2array($tmpFolder, 'media_elements');
        if (isset($mediaElements['media_elements_avatars'])){
            if (count($mediaElements['media_elements_avatars']) > 0){
                foreach($mediaElements['media_elements_avatars'] as $avatar){
                    $filePath = $tmpFolder . 'media/' . $avatar;
                    if (file_exists($filePath)){
                        copy($filePath, DOCROOT . 'avatars/' . $avatar);
                    }
                }
            }
        }

        if (!file_exists(DOCROOT.'/files/'.$this->labyrinthArray['map']['database_id'])) {
            mkdir(DOCROOT.'/files/'.$this->labyrinthArray['map']['database_id'], 0777, true);
        }

        if (isset($mediaElements['media_elements_files'])){
            if (count($mediaElements['media_elements_files']) > 0){
                foreach($mediaElements['media_elements_files'] as $file){
                    $filePath = $tmpFolder . 'media/' . $file;
                    if (file_exists($filePath)){
                        copy($filePath, DOCROOT . 'files/' . $this->labyrinthArray['map']['database_id'] . '/' . $file);
                    }
                }
            }
        }
        return true;
    }

    private function xml2array($filePath, $fileName){
        $array = null;
        $globalPath = $filePath.'/'.$fileName.'.xml';
        if (file_exists($globalPath)){
            $xmlfile = file_get_contents($globalPath);
            $ob = simplexml_load_string($xmlfile);
            $json = json_encode($ob);
            $array = json_decode($json, true);
            $array = $this->convertValuesInArray($array[$fileName]);
        }
        return $array;
    }

    private function addToDB($modelName, $data){
        $returnData = null;
        if (isset($data[$modelName.'_0']) && (is_array($data[$modelName.'_0']))){
            foreach($data as $d){
                $returnData[$d['id']] = $d;
                $returnData[$d['id']]['database_id'] = $this->insertInDB($modelName, $d);
            }
        } else {
            $returnData['id'] = $data['id'];
            $returnData['database_id'] = $this->insertInDB($modelName, $data);
        }
        return $returnData;
    }

    private function insertInDB($modelName, $data){
        $builder = DB_ORM::insert($modelName);
        $skipColumns = array('id');

        if ($modelName == 'map'){
            $data['name'] = DB_ORM::model('map')->getMapName($data['name']);
            $data['author_id'] = Auth::instance()->get_user()->id;
            $data['skin_id'] = 1;
        }

        if ($modelName == 'map_node'){
            $data['x'] = ($data['x'] == '') ? NULL : (int) $data['x'];
            $data['y'] = ($data['y'] == '') ? NULL : (int) $data['y'];
            $data['rgb'] = ($data['rgb'] == '') ? NULL : $data['rgb'];
            $data['kfp'] = ($data['kfp'] == '') ? 0 : 1;
            $data['undo'] = ($data['undo'] == '') ? 0 : 1;
            $data['end'] = ($data['end'] == '') ? 0 : 1;
        }

        if ($modelName == 'map_dam_element'){
            if (isset($this->labyrinthArray['map_dam'][$data['dam_id']])){
                $data['dam_id'] = $this->labyrinthArray['map_dam'][$data['dam_id']]['database_id'];
            }

            switch ($data['element_type']){
                case 'vpd':
                    if (isset($this->labyrinthArray['map_vpd'][$data['element_id']])){
                        $data['element_id'] = $this->labyrinthArray['map_vpd'][$data['element_id']]['database_id'];
                    }
                    break;
                case 'mr':
                    if (isset($this->labyrinthArray['map_element'][$data['element_id']])){
                        $data['element_id'] = $this->labyrinthArray['map_element'][$data['element_id']]['database_id'];
                    }
                    break;
                case 'dam':
                    if (isset($this->labyrinthArray['map_dam'][$data['element_id']])){
                        $data['element_id'] = $this->labyrinthArray['map_dam'][$data['element_id']]['database_id'];
                    }
                    break;
            }
        }

        if ($modelName == 'map_element'){
            $data['path'] = 'files/' . $this->labyrinthArray['map']['database_id'] . '/' . $data['name'];
        }

        if ($modelName == 'map_node_link'){
            if (isset($this->labyrinthArray['map_node'][$data['node_id_1']])){
                $data['node_id_1'] = $this->labyrinthArray['map_node'][$data['node_id_1']]['database_id'];
            }

            if (isset($this->labyrinthArray['map_node'][$data['node_id_2']])){
                $data['node_id_2'] = $this->labyrinthArray['map_node'][$data['node_id_2']]['database_id'];
            }
        }

        if ($modelName == 'map_node_section_node'){
            if (isset($this->labyrinthArray['map_node_section'][$data['section_id']])){
                $data['section_id'] = $this->labyrinthArray['map_node_section'][$data['section_id']]['database_id'];
            }
        }

        if (isset($data['map_id'])){
            $data['map_id'] = $this->labyrinthArray['map']['database_id'];
        }

        if (isset($data['chat_id'])){
            if (isset($this->labyrinthArray['map_chat'][$data['chat_id']])){
                $data['chat_id'] = $this->labyrinthArray['map_chat'][$data['chat_id']]['database_id'];
            }
        }

        if (isset($data['counter_id'])){
            if (isset($this->labyrinthArray['map_counter'][$data['counter_id']])){
                $data['counter_id'] = $this->labyrinthArray['map_counter'][$data['counter_id']]['database_id'];
            } else {
                $data['counter_id'] = NULL;
            }
        }

        if (isset($data['node_id'])){
            if (isset($this->labyrinthArray['map_node'][$data['node_id']])){
                $data['node_id'] = $this->labyrinthArray['map_node'][$data['node_id']]['database_id'];
            }
        }

        if (isset($data['question_id'])){
            if (isset($this->labyrinthArray['map_question'][$data['question_id']])){
                $data['question_id'] = $this->labyrinthArray['map_question'][$data['question_id']]['database_id'];
            }
        }

        if (isset($data['vpd_id'])){
            if (isset($this->labyrinthArray['map_vpd'][$data['vpd_id']])){
                $data['vpd_id'] = $this->labyrinthArray['map_vpd'][$data['vpd_id']]['database_id'];
            }
        }

        if (isset($data['redirect_node_id'])){
            if ($data['redirect_node_id'] == ''){
                $data['redirect_node_id'] = NULL;
            } else if (isset($this->labyrinthArray['map_node'][$data['redirect_node_id']])) {
                $data['redirect_node_id'] = $this->labyrinthArray['map_node'][$data['redirect_node_id']]['database_id'];
            }
        }

        $model = DB_ORM::model($modelName);
        foreach($data as $key => $value){
            if (!in_array($key, $skipColumns) && $model->is_field($key)){
                $builder->column($key, $value);
            }
        }

        return $builder->execute();
    }

    private function convertValuesInArray($array){
        if (count($array) > 0){
            foreach($array as $key => $value){
                if (count($value) > 0){
                    if (is_array($value)){
                        $value = $this->convertValuesInArray($value);
                    } else if (!is_numeric($value)){
                        $value = base64_decode($value);
                    } else {
                        if (is_numeric($value[0])){
                            if (is_float($value)){
                                $value = floatval($value);
                            } else {
                                $value = intval($value);
                            }
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
    private function makeTempFolder($mapName = null) {
        $this->folderPath = DOCROOT . 'tmp/';
        if ($mapName != null){
            $this->folderName = preg_replace("/[^A-Za-z0-9]/","",$mapName) . '_mvp_' . rand();
        } else {
            $this->folderName = 'mvp_' . rand();
        }
        
        if(is_dir($this->folderPath . $this->folderName)) {
            $this->folderName .= '_' . rand();
        }
        
        $this->folderPath .= $this->folderName;
        mkdir($this->folderPath);
    }
    
    /**
     * Create media folder
     * @return none 
     */
    private function makeMediaFolder() {
        if(!is_dir($this->folderPath)) return;
        
        if(!is_dir($this->folderPath . '/media')) {
            mkdir($this->folderPath . '/media');
        }
    }
    
    /**
     * Copy all exist avatars generated images
     * @param array(map_avatar) $avatars
     * @return none 
     */
    private function copyAvatarsImages($avatars) {
        if(!is_dir($this->folderPath) || count($avatars) <= 0) return;
        
        $this->makeMediaFolder();
        foreach($avatars as $avatar) {
            if (($avatar['image'] != 'ntr') && ($avatar['image'] != '')){
                $avatarImagePath = DOCROOT . 'avatars/' . $avatar['image'];
                if(file_exists($avatarImagePath) && is_dir($this->folderPath . '/media')) {
                    copy($avatarImagePath, $this->folderPath . '/media/' . $avatar['image']);
                    $index = (isset($this->mediaElements['avatars'])) ? count($this->mediaElements['avatars']) : 0;
                    $this->mediaElements['avatars']['avatar_'.$index] = $avatar['image'];
                }
            }
        }
    }

    /**
     * Copy all resource file to media folder
     * @param array(map_element) $elements
     * @return none 
     */
    private function copyResourcesFiles($elements) {
        if(count($elements) <= 0) return;
        
        $this->makeMediaFolder();
        
        foreach($elements as $e) {
            $elementPath = DOCROOT . $e['path'];
            if(file_exists($elementPath) && is_dir($this->folderPath . '/media')) {
                $pathInfo = pathinfo($elementPath);
                copy($elementPath, $this->folderPath . '/media/' . $pathInfo['filename'] . '.' . $pathInfo['extension']);
                $index = (isset($this->mediaElements['files'])) ? count($this->mediaElements['files']) : 0;
                $this->mediaElements['files']['file_'.$index] = $pathInfo['filename'] . '.' . $pathInfo['extension'];
            }
        }
    }
}

?>