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
    
    /**
     * Export map to mvp format
     * @param array $parameters ('mapId')
     */
    public function export($parameters) {
        $mapId = (int)$parameters['mapId'];
        if($mapId <= 0) return '';
        
        $this->makeTempFolder();
        
        $map = DB_ORM::model('map', array($mapId));
        $counters = DB_ORM::model('map_counter')->getCountersByMap($map->id);
        $nodes = DB_ORM::model('map_node')->getNodesByMap($map->id);
        $links = DB_ORM::model('map_node_link')->getLinksByMap($map->id);
        $sections = DB_ORM::model('map_node_section')->getAllSectionsByMap($map->id);
        $avatars = DB_ORM::model('map_avatar')->getAvatarsByMap($map->id);
        $questions = DB_ORM::model('map_question')->getQuestionsByMap($map->id);
        $vpds = DB_ORM::model('map_vpd')->getAllVpdByMap($map->id);
        $elements = DB_ORM::model('map_element')->getAllMediaFiles($map->id);
        
        $this->generateMetadata($map);
        $this->generateActivityModel($counters, $nodes, $sections, $links);
        $this->generateVPD($nodes, $avatars, $questions, $vpds);
        $this->generateImsmanifest($elements);
        
        $result = $this->createZipArchive();
        
        $this->removeDir();
        
        return $result ? (DOCROOT . 'tmp/' . $this->folderName . '.zip') : '';
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
    public function import($parameters) {
        echo 'import';
    }
    
    /**
     * Create temp folder 
     */
    private function makeTempFolder() {
        $this->folderPath = DOCROOT . 'tmp/';
        $this->folderName = 'mvp_' . rand();
        
        if(is_dir($this->folderPath . $this->folderName)) {
            $this->folderName .= '_' . rand() . '_' . rand();
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
     * Remove temp folder 
     */
    private function removeTempFolder() {
        
    }
    
    /**
     * Create meradata.xml file
     * @param Model_map $map
     */
    private function generateMetadata($map) {
        if(!is_dir($this->folderPath) || $map == null) return;
        
        $filePath = $this->folderPath . '/metadata.xml';
        $f = fopen($filePath, 'w');
        
        $language = $map->language_id == 1 ? 'en' : 'fr';
        
        $content = '<?xml version="1.0" encoding="UTF-8"?>
                    <lom xmlns="http://ltsc.ieee.org/xsd/LOM" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                        xmlns:hx="http://ns.medbiq.org/lom/extend/v1/"
                        xsi:schemaLocation="http://ltsc.ieee.org/xsd/LOM healthcarelom.xsd">
                        <general>
                            <version>3</version>
                            <title>
                                <string language="' . $language . '">' . $map->name . '</string>
                            </title>
                            <language>' . $language . '</language>
                            <description>
                                <string language="' . $language . '"'. (strlen($map->abstract) > 0 ? '>' . $map->abstract . '</string>' : '/>') . '
                            </description>
                            <keyword>
                                <string language="' . $language . '"' . (strlen($map->keywords) > 0 ? '>' . $map->keywords . '</string>' : '/>') . '
                            </keyword>
                            <startScore>' . $map->startScore . '</startScore>
                            <threshold>' . $map->threshold . '</threshold>
                            <type_id>' . $map->type_id . '</type_id>
                            <units>' . $map->units . '</units>
                            <security_id>' . $map->security_id . '</security_id>
                            <guid>
                                <string language="' . $language . '"' . (strlen($map->guid) > 0 ? '>' . $map->guid . '</string>' : '/>') . '
                            </guid>
                            <timing>' . $map->timing . '</timing>
                            <delta_time>' . $map->delta_time . '</delta_time>
                            <show_bar>' . $map->show_bar . '</show_bar>
                            <show_score>' . $map->show_score . '</show_score>
                            <skin_id>' . $map->skin_id . '</skin_id>
                            <enabled>' . $map->enabled . '</enabled>
                            <section_id>' . $map->section_id . '</section_id>
                            <source_id>' . $map->source_id . '</source_id>
                            <feedback>
                                <string language="' . $language . '"' . (strlen($map->feedback) > 0 ? '>' . $map->feedback . '</string>' : '/>') . '
                            </feedback>
                            <dev_notes>
                                <string language="' . $language . '"' . (strlen($map->dev_notes) > 0 ? '>' . $map->dev_notes . '</string>' : '/>') . '
                            </dev_notes>
                            <source>
                                <string language="' . $language . '"' . (strlen($map->source) > 0 ? '>' . $map->source . '</string>' : '/>') . '
                            </source>
                        </general>
                    </lom>';
        
        fwrite($f, $content);
        fclose($f);
    }
    
    /**
     * Create activitymodel.xml file
     * @param array(map_counter) $counters
     * @param array(map_node) $nodes
     * @param array(map_node_section) $sections
     * @param array(map_node_link) $links
     */
    private function generateActivityModel($counters, $nodes, $sections, $links) {
        if(!is_dir($this->folderPath)) return;
        
        $filePath = $this->folderPath . '/activitymodel.xml';
        $f = fopen($filePath, 'w');
        
        $content = '<?xml version="1.0" encoding="UTF-8" ?>
                    <ActivityModel xmlns="http://ns.medbiq.org/activitymodel/v1/" 
                                   xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
                                   xsi:schemaLocation="http://ns.medbiq.org/activitymodel/v1/activitymodel.xsd" 
                                   xmlns:ol="http://www.w3.org/2001/XMLSchema-instance">';
        
        $content .= $this->generateProperties($counters);
        $content .= $this->generateActivityNodes($nodes, $sections);
        $content .= $this->generateLinks($links);
        $content .= $this->generateActivityModelXtensibleInfo($nodes);
        
        $content .= '</ActivityModel>';
        
        fwrite($f, $content);
        fclose($f);
    }
    
    /**
     * Create virtualpatientdata.xml file
     * @param array(map_node) $nodes
     * @param array(map_avatar) $avatars
     * @param array(map_question) $questions 
     * @param array(map_vpd) $questions 
     */
    private function generateVPD($nodes, $avatars, $questions, $vpds) {
        if(!is_dir($this->folderPath)) return;
        
        $filePath = $this->folderPath . '/virtualpatientdata.xml';
        $f = fopen($filePath, 'w');
        
        $content = '<?xml version="1.0" encoding="UTF-8" ?> 
                    <VirtualPatientData xmlns="http://ns.medbiq.org/virtualpatientdata/v1/" 
                                        xmlns:xsi="http://ns.medbiq.org/virtualpatientdata/v1/XMLSchema-instance" 
                                        xmlns:ol="http://www.w3.org/2001/XMLSchema-instance" 
                                        ol:schemaLocation="">';
        
        $content .= $this->generateVPDText($nodes, $vpds);
        $content .= $this->generateVPDXtensibleInfo($avatars, $questions);
        $content .= $this->generateVPDPatientDemographics($vpds);
        $content .= $this->generateVPDMedication($vpds);
        $content .= $this->generateVPDInterviewItem($vpds);
        $content .= $this->generateVPDPhysicalExam($vpds);
        $content .= $this->genenrateVPDIntervention($vpds);
        
        $content .= '</VirtualPatientData>';
        
        fwrite($f, $content);
        fclose($f);
    }
    
    /**
     * Generate activity model properties section
     * @param array(map_counter) $counters
     * @return string 
     */
    private function generateProperties($counters) {
        if(count($counters) <= 0) return '';
        
        $result = '<Properties>
                        <Counters>';
        
        foreach($counters as $counter) {
            $result .= '<Counter id="' . $counter->id . '" isVisible="' . ($counter->visible == 1 ? 'true' : 'false') . '">
                            <CounterLabel>' . $counter->name . '</CounterLabel> 
                            <CounterInitValue>' . $counter->start_value . '</CounterInitValue>';
            
            $rules = DB_ORM::model('map_counter_rule')->getRulesByCounterId($counter->id);
            if(count($rules) > 0) {
                $result .= '<CounterRules>';
                foreach($rules as $rule) {
                    $result .= '<Rule>
                                    <Relation>' . $rule->relation->value . '</Relation> 
                                    <Value>' . $rule->value . '</Value> 
                                    <RuleRedirect>/ActivityModel/ActivityNodes/NodeSection/ActivityNode[@id=\'' . $rule->redirect_node_id . '\']</RuleRedirect> 
                                    <RuleMessage>' . ($rule->message != null && strlen($rule->message) > 0 ? $rule->message : 'no message') . '</RuleMessage>
                                    <Counter>' . $rule->counter . '</Counter>
                                    ' . (strlen($rule->counter_value) > 0 ? ('<CounterValue>' . $rule->counter_value . '</CounterValue>') : '') . '
                                </Rule>';
                }
                $result .= '</CounterRules>';
            }
            
            $result .= '</Counter>';
        }
        
        $result .= '    </Counters>
                    </Properties>';
        
        return $result;
    }
    
    /**
     * Generate activity model activity nodes section
     * @param array(map_node) $nodes
     * @param array(map_node_section) $sections
     * @return string 
     */
    private function generateActivityNodes($nodes, $sections) {
        if(count($nodes) <= 0) return '';
        
        $result = '<ActivityNodes>';
        
        if(count($sections) > 0) {
            foreach($sections as $section) {
                $result .= '<NodeSection id="' . $section->id . '" label="' . $section->name . '">';
                if(count($section->nodes) > 0) {
                    foreach($section->nodes as $sectionNode) {
                        $result .= '<ActivityNode id="" label="' . $sectionNode->node->title . '">
                                        <Content>/DataAvailabilityModel/DAMNode[@id=\'OLNode_' . $sectionNode->node_id . '\']</Content> 
                                        <Rules>
                                            <Probability>' . ($sectionNode->node->probability ? 'on' : 'off') . '</Probability> 
                                            <NavigateGlobal>on</NavigateGlobal>
                                            ' . ($sectionNode->order > 0 ? ('<order>' . $sectionNode->order . '</order>') : '') . '
                                        </Rules>
                                    </ActivityNode>';
                    }
                }     
                $result .= '</NodeSection>';
            }
        }
        
        $result .= '<NodeSection id="" label="unallocated">';
        foreach($nodes as $node) {
            $result .= '<ActivityNode id="' . $node->id . '" label="' . $node->title . '">
                            <Content>/DataAvailabilityModel/DAMNode[@id=\'OLNode_' . $node->id . '\']</Content> 
                            <Rules>';
            if(count($node->counters) > 0) {
                foreach($node->counters as $nodeCounter) {
                    $function = $this->parseCounterFunction($nodeCounter->function);
                    if(isset($function['operator']) && isset($function['value'])) {
                        $result .= '<CounterActionRule>
                                        <CounterOperator>' . $function['operator'] . '</CounterOperator> 
                                        <CounterRuleValue>' . $function['value'] . '</CounterRuleValue> 
                                        <CounterPath>/ActivityModel/Properties/Counters/Counter[@id=\'' . $nodeCounter->counter_id . '\']</CounterPath> 
                                        <CounterRuleEnabled>' . ($nodeCounter->display <= 0 ? 'off' : 'on') . '</CounterRuleEnabled> 
                                    </CounterActionRule>';
                    }
                }
                                
            }
            $result .= '        <Probability>' . ($node->probability ? 'on' : 'off') . '</Probability> 
                                <NavigateGlobal>on</NavigateGlobal> 
                                ' . (strlen($node->conditional) > 0 ? ('<Conditional>' . $node->conditional . '</Conditional>') : '') . '
                                ' . (strlen($node->conditional_message) > 0 ? ('<ConditionalMessage>' . $node->conditional_message . '</ConditionalMessage>') : '') . '
                                ' . (strlen($node->info) > 0 ? ('<Info>' . $node->info . '</Info>') : '') . '
                            </Rules>
                        </ActivityNode>';
        }
        $result .= '    </NodeSection>
                    </ActivityNodes>';
        
        return $result;
    }
    
    /**
     * Generate activity model links section
     * @param array(map_node_link) $links
     * @return string 
     */
    private function generateLinks($links) {
        if(count($links) <= 0) return '';
        
        $result = '<Links>';
        foreach($links as $link) {
            $result .= '<Link label="' . $link->text . '" display="true">
                            <ActivityNodeA>/ActivityModel/ActivityNodes/NodeSection/ActivityNode[@id=\'' . $link->node_id_1 . '\']</ActivityNodeA> 
                            <ActivityNodeB>/ActivityModel/ActivityNodes/NodeSection/ActivityNode[@id=\'' . $link->node_id_2 . '\']</ActivityNodeB> 
                            ' . ($link->image_id > 0 ? ('<ImageID>' . $link->image_id . '</ImageID>') : '') . '
                            ' . ($link->order > 0 ? ('<Order>' . $link->order . '</Order>') : '') . '
                            ' . ($link->probability > 0 ? ('<Probability>' . $link->probability . '</Probability>') : '') . '
                        </Link>';
        }
        $result .= '</Links>';
        
        return $result;
    }
    
    /**
     * Generate activity model XtensibleInfo section
     * @param array(map_node) $nodes
     * @return string 
     */
    private function generateActivityModelXtensibleInfo($nodes) {
        if(count($nodes) <= 0) return '';
        
        $result = '<XtensibleInfo>
                        <ol:OL_xtensible>';
        foreach($nodes as $node) {
            $result .= '<ol:OL_node ID="' . $node->id . '" 
                                    undoLinks="' . ($node->undo ? 'y' : 'n') . '" 
                                    nodeProbs="' . ($node->probability ? 'y' : 'n') . '" 
                                    nodePriority="' . ($node->priority_id == 1 ? '' : ($node->priority_id == 2 ? 'neg' : 'pos')) . '" 
                                    linkSorting="' . ($node->link_type_id == 1 ? 'o' : ($node->link_type_id == 3 ? '1' : 'r')) . '" 
                                    linkPresentation="' . ($node->link_style_id == 2 ? 'drop' : ($node->link_style_id == 3 ? 'conf' : ($node->link_style_id == 4 ? 'fill' : ''))) . '" 
                                    mnodeType="' . ($node->type_id == 1 ? 'root' : ($node->end ? 'end' : 'child')) . '" 
                                    mnodeX="' . $node->x . '" 
                                    mnodeY="' . $node->y . '" 
                                    mnodeRGB="' . $node->rgb . '">
                            <ol:OL_infoText>
                                <div xmlns="http://www.w3.org/1999/xhtml"' . (strlen($node->info) > 0 ? ('>' . $node->info . '</div>') : '/>') . ' 
                            </ol:OL_infoText>
                        </ol:OL_node>';
        }
        $result .= '    </ol:OL_xtensible>
                    </XtensibleInfo>';
        
        return $result;
    }
    
    /**
     * Parse counter node function
     * @param string $function
     * @return array 
     */
    private function parseCounterFunction($function) {
        if(strlen($function) <= 0 || strlen($function) < 2) return array();
        
        $operator = substr($function, 0, 1);
        $value = substr($function, 1, strlen($function) - 1);
        
        return array('operator' => $operator, 'value' => $value);
    }
    
    /**
     * Generate VPD VPDText section
     * @param array(map_node) $nodes
     * @param array(map_vpd) $vpds
     * @return string 
     */
    private function generateVPDText($nodes, $vpds) {
        if(count($nodes) <= 0 && count($vpds) <= 0) return '';
        
        $result = '';
        foreach($nodes as $node) {
            $result .= '<VPDText id="NGR_' . $node->id . '" textType="narrative">
                            <div xmlns="http://www.w3.org/1999/xhtml"' . (strlen($node->text) > 0 ? ('>' . base64_encode($node->text) . '</div>') : '/>' ) . ' 
                        </VPDText>';
        }
        
        if(count($vpds) > 0) {
            foreach($vpds as $vpd) {
                if($vpd->vpd_type_id != 1) continue;

                $elements = $this->parseVPDTextElements($vpd->elements);   
                if(isset($elements['type']) && isset($elements['text'])) {
                    $result .= '<VPDText id="TEXT_' . $vpd->id . '" textType="' . $elements['type'] . '">
                                    <div xmlns="http://www.w3.org/1999/xhtml"' . (strlen($elements['text']) > 0 ? ('>' . $elements['text'] . '</div>') : '/>' ) . ' 
                                </VPDText>';
                }
            }
        }
        
        return $result;
    }
    
    /**
     * Parse vpd text elements 
     * @param array(map_vpd_element) $vpdElements
     * @return array 
     */
    private function parseVPDTextElements($vpdElements) {
        if(count($vpdElements) <= 0) return array();
        
        $result = array();
        foreach($vpdElements as $e) {
            switch($e->key) {
                case 'VPDTextType':
                    $result['type'] = $e->value;
                    break;
                case 'VPDText':
                    $result['text'] = $e->value;
                    break;
            }
        }
        
        return $result;
    }
    
    /**
     * Generate VPD XtensibleInfo section
     * @param array(map_avatar) $avatars
     * @param array(map_question) $questions
     * @return string 
     */
    private function generateVPDXtensibleInfo($avatars, $questions) {
        if(count($avatars) <= 0 && count($questions) <= 0) return '';
        
        $result = '<XtensibleInfo>
                        <ol:OL_xtensible>';
        
        $result .= $this->generateVPDXtensibleInfoAvatars($avatars);
        $this->copyAvatarsImages($avatars);
        $result .= $this->generateVPDXtensibleInfoQuestions($questions);
        
        $result .= '    </ol:OL_xtensible>
                    </XtensibleInfo>';
        
        return $result;
    }
    
    /**
     * Generate VPD XtensibleInfo OL_avatar section
     * @param array(map_avatar) $avatars
     * @return string 
     */
    private function generateVPDXtensibleInfoAvatars($avatars) {
        if(count($avatars) <= 0) return '';
        
        $avatarNoseMap = array('' => '', 'nostrils' => 'A', 'petit' => 'B', 'wide' => 'C');
        $avatarHairMap = array('' => 'A', 'none' => 'A', 'shaved' => 'B', 'longblonde' => 'C', 'short' => 'D', 
                               'curly' => 'E', 'bob' => 'F', 'longred' => 'G', 'grandpa' => 'H', 'granny' => 'I', 
                               'youngman' => 'K', 'long' => 'L');
        $avatarEnvironmentMap = array('' => 'A', 'none' => 'A', 'ambulancebay' => 'B', 'bedpillow' => 'F', 'hospital' => 'G', 
                                      'waitingroom' => 'H', 'insideambulance' => 'J', 'xray' => 'O', 'ca' => 'R', 'medivachelicopter' => 'S', 
                                      'heartmonitor' => 'V', 'stopsign' => 'BB', 'bedside' => 'CC', 'ambulance2' => 'DD', 'machine' => 'FF',
                                      'livingroom' => 'D', 'basicoffice' => 'K', 'basicroom' => 'N', 'corridor' => 'Y', 'room' => 'AA', 
                                      'pillowb' => 'GG', 'concourse' => 'JJ', 'officecubicle' => 'KK', 'residentialstreet' => 'C', 'highstreet' => 'E',
                                      'cityskyline' => 'I', 'lakeside' => 'L', 'suburbs' => 'M', 'summer' => 'T', 'longroad' => 'U',
                                      'downtown' => 'P', 'winter' => 'Q', 'outsidelake' => 'W', 'field' => 'X', 'roadside' => 'Z',
                                      'forestriver' => 'HH', 'parkinglot' => 'II', 'stopsign' => 'BB', 'yieldsign' => 'EE');
        $avatarSexMap = array('' => '', 'male' => 'A', 'female' => 'B');
        $avatarMouthMap = array('' => '', 'smile' => 'A', 'indifferent' => 'B', 'frown' => 'C');
        $avatarOutfitMap = array('' => '', 'none' => 'A', 'woolyjumper' => 'B', 'shirtandtie' => 'C', 'nurse' => 'D',
                                 'scrubs_blue' => 'E', 'scrubs_green' => 'F', 'vest' => 'G', 'gown' => 'H', 'pyjamas_female' => 'I',
                                 'pyjamas_male' => 'J', 'doctor_male' => 'K', 'doctor_female' => 'L', 'neck' => 'M', 'blackshirt' => 'N',
                                 'winterjacket' => 'O', 'vneck' => 'P', 'fleece' => 'Q', 'sweater' => 'R');
        $avatarBubbleMap = array('' => 'A', 'none' => 'A', 'normal' => 'B', 'think' => 'C', 'shout' => 'D');
        $avatarAccessoryMap = array('' => '', 'none' => 'A', 'glasses' => 'B', 'sunglasses' => 'T', 'bindi' => 'C',
                                    'moustache' => 'D', 'freckles' => 'E', 'blusher' => 'G', 'earrings' => 'H', 'beads' => 'I',
                                    'neckerchief' => 'J', 'redscarf' => 'V', 'beanie' => 'Y', 'buttonscarf' => 'AA', 'baseballcap' => 'CC',
                                    'winterhat' => 'DD', 'mask' => 'F', 'stethoscope' => 'K', 'oxygenmask' => 'L', 'surgeoncap' => 'M',
                                    'eyepatch' => 'N', 'scratches' => 'O', 'splitlip' => 'P', 'blackeyeleft' => 'Q', 'blackeyeright' => 'R',
                                    'headbandage' => 'S', 'sunglasses' => 'T', 'neckbrace' => 'U', 'tearssmall' => 'W', 'tearslarge' => 'BB', 
                                    'sweat' => 'X');
        $avatarAgeMap = array('' => '', '20' => 'A', '40' => 'B', '60' => 'C');
        $avatarEyesMap = array('' => '', 'open' => 'A', 'close' => 'B');
        
        $result = '<ol:OL_avatars>';
        foreach($avatars as $avatar) {
            $result .= '<ol:OL_avatar ID="' . $avatar->id . '" 
                                      AvatarSkin1="' . $avatar->skin_1 . '" 
                                      AvatarSkin2="' . $avatar->skin_2 . '" 
                                      AvatarCloth="' . $avatar->cloth . '" 
                                      AvatarNose="' . (isset($avatarNoseMap[$avatar->nose]) ? $avatarNoseMap[$avatar->nose] : $avatar->nose) . '" 
                                      AvatarHair="' . (isset($avatarHairMap[$avatar->hair]) ? $avatarHairMap[$avatar->hair] : $avatar->hair) . '" 
                                      AvatarEnvironment="' . (isset($avatarEnvironmentMap[$avatar->environment]) ? $avatarEnvironmentMap[$avatar->environment] : $avatar->environment) . '" 
                                      AvatarBkd="' . $avatar->bkd . '" 
                                      AvatarSex="' . (isset($avatarSexMap[$avatar->sex]) ? $avatarSexMap[$avatar->sex] : $avatar->sex) . '" 
                                      AvatarMouth="' . (isset($avatarMouthMap[$avatar->mouth]) ? $avatarMouthMap[$avatar->mouth] : $avatar->mouth) . '" 
                                      AvatarOutfit="' . (isset($avatarOutfitMap[$avatar->outfit]) ? $avatarOutfitMap[$avatar->outfit] : $avatar->outfit) . '" 
                                      AvatarBubble="' . (isset($avatarBubbleMap[$avatar->bubble]) ? $avatarBubbleMap[$avatar->bubble] : $avatar->bubble) . '" 
                                      AvatarAccessory1="' . (isset($avatarAccessoryMap[$avatar->accessory_1]) ? $avatarAccessoryMap[$avatar->accessory_1] : $avatar->accessory_1) . '" 
                                      AvatarAccessory2="' . (isset($avatarAccessoryMap[$avatar->accessory_2]) ? $avatarAccessoryMap[$avatar->accessory_2] : $avatar->accessory_2) . '" 
                                      AvatarAccessory3="' . (isset($avatarAccessoryMap[$avatar->accessory_3]) ? $avatarAccessoryMap[$avatar->accessory_3] : $avatar->accessory_3) . '" 
                                      AvatarAge="' . (isset($avatarAgeMap[$avatar->age]) ? $avatarAgeMap[$avatar->age] : $avatar->age) . '" 
                                      AvatarEyes="' . (isset($avatarEyesMap[$avatar->eyes]) ? $avatarEyesMap[$avatar->eyes] : $avatar->eyes) . '" 
                                      AvatarHairColor="' . $avatar->hair_color . '" 
                                      AvatarImage="' . (strlen($avatar->image) > 0 ? ('avatar_' . $avatar->image) : '' ) . '">
                            <ol:OL_AvatarBubbleText>
                                <div xmlns="http://www.w3.org/1999/xhtml"' . (strlen($avatar->bubble_text) > 0 ? ('>' . $avatar->bubble_text . '</div>') : '/>') . ' 
                            </ol:OL_AvatarBubbleText>
                        </ol:OL_avatar>';
        }
        $result .= '</ol:OL_avatars>';
        
        return $result;
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
            if($avatar->image == 'ntr') continue;
            
            $avatar->image = ($avatar->image == '') ? 'default.png' : $avatar->image;
            $avatarImagePath = DOCROOT . 'avatars/' . $avatar->image;
            if(file_exists($avatarImagePath) && is_dir($this->folderPath . '/media')) {
                copy($avatarImagePath, $this->folderPath . '/media/' . 'avatar_' . $avatar->image);
            }
        }
    }
    
    /**
     * Generate VPD XtensibleInfo OL_question section
     * @param array(map_question) $questions
     * @return string 
     */
    private function generateVPDXtensibleInfoQuestions($questions) {
        if(count($questions) <= 0) return '';
        
        $result = '<ol:OL_questions>';
        foreach($questions as $question) {
            $result .= '<ol:OL_question ID="' . $question->id . '" 
                                        QuestionEntryType="' . $question->type->value . '" 
                                        QuestionWidth="' . $question->width . '"
                                        QuestionHeight="' . $question->height . '" 
                                        Feedback="' . $question->feedback . '" 
                                        ShowAnswer="' . ($question->show_answer ? 'y' : 'n') . '" 
                                        ScoreCounter="' . $question->counter_id . '" 
                                        NumTries="' . $question->num_tries . '">
                            <ol:OL_QuestionStem>
                                <div xmlns="http://www.w3.org/1999/xhtml"' . (strlen($question->stem) > 0 ? ('>' . $question->stem . '</div>') : '/>') . '
                            </ol:OL_QuestionStem>';
            if(count($question->responses) > 0) {
                $i = 1;;
                foreach($question->responses as $response) {
                    $result .= '<ol:Resp' . $i . 't>
                                    <div xmlns="http://www.w3.org/1999/xhtml"' . (strlen($response->response) > 0 ? ('>' . $response->response . '</div>') : '/>') . '
                                </ol:Resp' . $i . 't>
                                <ol:Resp' . $i . 'y>
                                    <div xmlns="http://www.w3.org/1999/xhtml"' . ($response->is_correct ? '>c</div>' : '/>') . '
                                </ol:Resp' . $i . 'y>
                                <ol:Resp' . $i . 's>
                                    <div xmlns="http://www.w3.org/1999/xhtml">' . ($response->score != null ? $response->score : 0) . '</div>
                                </ol:Resp' . $i . 's>
                                <ol:Resp' . $i . 'f>
                                    <div xmlns="http://www.w3.org/1999/xhtml"' . (strlen($response->feedback) > 0 ? ('>' . $response->feedback . '</div>') : '/>') . '
                                </ol:Resp' . $i . 'f>';
                    $i++;
                }
            }
            $result .= '</ol:OL_question>';
        }
        $result .= '</ol:OL_questions>';
        
        return $result;
    }
    
    /**
     * Generate PatientDemographics section
     * @param array(map_vpd) $vpds
     * @return string 
     */
    private function generateVPDPatientDemographics($vpds) {
        if(count($vpds) <= 0) return '';
        
        $result = '';
        foreach($vpds as $vpd) {
            if($vpd->vpd_type_id != 2) continue;
            
            $values = $this->parseVPDPatientDemographicElements($vpd->elements);
            
            if(isset($values['title']) && isset($values['desc'])) {
                $result .= '<PatientDemographic id="' . $vpd->id . '">
                                <DemographicCharacteristic>
                                    <Title>' . $values['title'] . '</Title>
                                    <Description>' . $values['desc'] . '</Description>
                                </DemographicCharacteristic>
                            </PatientDemographic>';
            }
        }
        
        return strlen($result) > 0 ? ('<PatientDemographics>' . $result . '</PatientDemographics>') : '';
    }
    
    /**
     * Parse PatientDemographics elements
     * @param array(map_vpd_element) $elements
     * @return array 
     */
    private function parseVPDPatientDemographicElements($elements) {
        if(count($elements) <= 0 || count($elements) < 2) return array();
        
        $result = array();
        foreach($elements as $e) {
            switch($e->key) {
                case 'DemogTitle':
                    $result['title'] = $e->value;
                    break;
                case 'DemogDesc':
                    $result['desc'] = $e->value;
                    break;
            }                
        }
        
        return $result;
    }
    
    /**
     * Generate VPD Medication section
     * @param array(map_vpd) $vpds 
     * @return string
     */
    private function generateVPDMedication($vpds) {
        if(count($vpds) <= 0) return '';
        
        $result = '';
        foreach($vpds as $vpd) {
            if($vpd->vpd_type_id != 4) continue;
            
            $values = $this->parseVPDMedicationElements($vpd->elements);
            $result .= '<Medication id="' . $vpd->id . '">
                            <MedicationName source="' . $values['source'] . '" sourceID="' . $values['sourceID'] . '">' . $values['title'] . '</MedicationName>
                            <Dose>' . $values['dose'] . '</Dose>
                            <Route>' . $values['route'] . '</Route>
                            <Frequency>' . $values['freq'] . '</Frequency>
                        </Medication>';
        }
        
        return $result;
    }
    
    /**
     * Parse VPD Medication Elements
     * @param array(map_vpd_element) $elements
     * @return array 
     */
    private function parseVPDMedicationElements($elements) {
        $result = array('title' => '', 'dose' => '', 'route' => '', 'freq' => '', 'source' => '', 'sourceID' => '');
        
        if(count($elements) <= 0) return $result;
        
        foreach($elements as $e) {
            switch($e->key) {
                case 'MedicTitle':
                    $result['title'] = $e->value;
                    break;
                case 'MedicDose':
                    $result['dose'] = $e->value;
                    break;
                case 'MedicRoute':
                    $result['route'] = $e->value;
                    break;
                case 'MedicFreq':
                    $result['freq'] = $e->value;
                    break;
                case 'MedicSource':
                    $result['source'] = $e->value;
                    break;
                case 'MedicSourceID':
                    $result['sourceID'] = $e->value;
                    break;
            }
        }
        
        return $result;
    }
    
    /**
     * Generate VPD InterviewItem sections
     * @param array(map_vpd) $vpds
     * @return string 
     */
    private function generateVPDInterviewItem($vpds) {
        if(count($vpds) <= 0) return '';
        
        $result = '';
        foreach($vpds as $vpd) {
            if($vpd->vpd_type_id != 5) continue;
            
            $values = $this->parseVPDInterviewItemElements($vpd->elements);
            
            $result .= '<InterviewItem id="' . $vpd->id . '" mediaID="' . $values['media'] . '" trigger="' . $values['trigger'] . '">
                            <Question>' . $values['question'] . '</Question>
                            <Response>' . $values['answer'] . '</Response>
                        </InterviewItem>';
        }
        
        return $result;
    }
    
    /**
     * Parse VPD InterviewItem Elements
     * @param array(map_vpd_element) $elements
     * @return array 
     */
    private function parseVPDInterviewItemElements($elements) {
        $result = array('question' => '', 'answer' => '', 'media' => '', 'trigger' => '');
        
        if(count($elements) <= 0) return $result;
        
        foreach($elements as $e) {
            switch($e->key) {
                case 'QAQuestion':
                    $result['question'] = $e->value;
                    break;
                case 'QAAnswer':
                    $result['answer'] = $e->value;
                    break;
                case 'QAMedia':
                    $result['media'] = $e->value;
                    break;
                case 'trigger':
                    $result['trigger'] = $e->value;
                    break;
            }
        }
        
        return $result;
    }
    
    /**
     * Generate VPD PhysicalExam sections
     * @param array(map_vpd) $vpds
     * @return string 
     */
    private function generateVPDPhysicalExam($vpds) {
        if(count($vpds) <= 0) return '';
        
        $result = '';
        foreach($vpds as $vpd) {
            if($vpd->vpd_type_id != 6) continue;
            
            $values = $this->parseVPDPhysicalExamElements($vpd->elements);
            
            $result .= '<PhysicalExam id="' . $vpd->id . '">
                            <ExamName>' . $values['ExamName'] . '</ExamName>
                            <ExamDesc>' . $values['ExamDesc'] . '</ExamDesc>
                            <Action>' . $values['Action'] . '</Action>
                            <LocationOnBody>
                                <ProximalOrDistal>' . $values['ProxDist'] . '</ProximalOrDistal>
                            </LocationOnBody>
                            <Finding>' . $values['FindName'] . '</Finding>
                            <Description>' . $values['FindDesc'] . '</Description>
                            <FindMedia>' . $values['FindMedia'] . '</FindMedia>
                        </PhysicalExam>';
        }
        
        return $result;
    }
    
    /**
     * Parse VPD PhysicalExam Elements
     * @param array(map_vpd_element) $elements
     * @return array 
     */
    private function parseVPDPhysicalExamElements($elements) {
        $result = array('ExamName' => '', 'ExamDesc' => '', 'BodyPart' => '', 'Action' => '', 'ProxDist' => '', 
                        'FindName' => '', 'FindDesc' => '', 'FindMedia' => '');
        
        if(count($elements) <= 0) return $result;
        
        foreach($elements as $e) {
            switch($e->key) {
                case 'ExamName':
                    $result['ExamName'] = $e->value;
                    break;
                case 'ExamDesc':
                    $result['ExamDesc'] = $e->value;
                    break;
                case 'BodyPart':
                    $result['BodyPart'] = $e->value;
                    break;
                case 'Action':
                    $result['Action'] = $e->value;
                    break;
                case 'ProxDist':
                    $result['ProxDist'] = $e->value;
                    break;
                case 'FindName':
                    $result['FindName'] = $e->value;
                    break;
                case 'FindDesc':
                    $result['FindDesc'] = $e->value;
                    break;
                case 'FindMedia':
                    $result['FindMedia'] = $e->value;
                    break;
                
            }
        }
        
        return $result;
    }
    
    /**
     * Generate VPD Intervention sections
     * @param array(map_vpd) $vpds
     * @return string 
     */
    private function genenrateVPDIntervention($vpds) {
        if(count($vpds) <= 0) return '';
        
        $result = '';
        foreach($vpds as $vpd) {
            if($vpd->vpd_type_id != 9) continue;
            
            $values = $this->parseVPDInterventionElements($vpd->elements);
            
            $result .= '<Intervention id="' . $vpd->id . '">
                            <InterventionName>' . $values['IntervTitle'] . '-'. $values['IntervDesc'] . '</InterventionName>
                            <Medication source="' . $values['iMedicSource'] . '"
                                        sourceID="' . $values['iMedicSourceID'] . '"
                                        Appropriateness="' . $values['Appropriateness'] . '"
                                        Results="' . $values['ResultTitle'] . '">
                                <MedicationName>' . $values['iMedicTitle'] . '</MedicationName>
                                <Dose>' . $values['iMedicDose'] . '</Dose>
                                <Route>' . $values['iMedicRoute'] . '</Route>
                                <Frequency>' . $values['iMedicFreq'] . '</Frequency>
                            </Medication>
                            <iTestMedia>' . $values['iTestMedia'] . '</iTestMedia>
                            <Submit>' . $values['Submit'] . '</Submit>
                        </Intervention>';
        }
        
        return $result;
    }
    
    /**
     * Parse VPD Intervention Elements
     * @param array(map_vpd_element) $elements
     * @return array 
     */
    private function parseVPDInterventionElements($elements) {
        $result = array('IntervTitle' => '', 'IntervDesc' => '', 'iMedicTitle' => '', 'iMedicDose' => '', 'iMedicRoute' => '',
                        'iMedicFreq' => '', 'iMedicSource' => '', 'iMedicSourceID' => '', 'Appropriateness' => '', 'ResultTitle' => '',
                        'iTestMedia' => '', 'Submit' => '');
        
        if(count($elements) <= 0) return $result;
        
        foreach($elements as $e) {
            switch($e->key) {
                case 'IntervTitle':
                    $result['IntervTitle'] = $e->value;
                    break;
                case 'IntervDesc':
                    $result['IntervDesc'] = $e->value;
                    break;
                case 'iMedicTitle':
                    $result['iMedicTitle'] = $e->value;
                    break;
                case 'iMedicDose':
                    $result['iMedicDose'] = $e->value;
                    break;
                case 'iMedicRoute':
                    $result['iMedicRoute'] = $e->value;
                    break;
                case 'iMedicFreq':
                    $result['iMedicFreq'] = $e->value;
                    break;
                case 'iMedicSource':
                    $result['iMedicSource'] = $e->value;
                    break;
                case 'iMedicSourceID':
                    $result['iMedicSourceID'] = $e->value;
                    break;
                case 'Appropriateness':
                    $result['Appropriateness'] = $e->value;
                    break;
                case 'ResultTitle':
                    $result['ResultTitle'] = $e->value;
                    break;
                case 'iTestMedia':
                    $result['iTestMedia'] = $e->value;
                    break;
                case 'Submit':
                    $result['Submit'] = $e->value;
                    break;
            }
        }
        
        return $result;
    }
    
    /**
     * Create imsmanifest.xml file
     * @param array(map_element) $elements
     */
    private function generateImsmanifest($elements) {
        if(!is_dir($this->folderPath)) return;
        
        $filePath = $this->folderPath . '/imsmanifest.xml';
        $f = fopen($filePath, 'w');
        
        $content = '<?xml version="1.0" encoding="UTF-8" ?> 
                    <manifest xmlns="http://www.imsglobal.org/xsd/imscp_v1p1" xmlns:lom="http://ltsc.ieee.org/xsd/LOM"
                              xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                              xmlns:adlcp="http://www.adlnet.org/xsd/adlcp_v1p3"
                              xmlns:imsss="http://www.imsglobal.org/xsd/imsss"
                              xmlns:adlnav="http://www.adlnet.org/xsd/adlnav_v1p3">';

        $content .= $this->generateResources($elements);
        $this->copyResourcesFiles($elements);
        
        $content .= '</manifest>';
        
        fwrite($f, $content);
        fclose($f);
    }
    
    /**
     * Generate VPD Resources section
     * @param array(map_element) $vpds 
     * @return string
     */
    private function generateResources($elements) {
        if(count($elements) <= 0) return '';
        
        $result = '<resources>';
        foreach($elements as $e) {
            $path = DOCROOT . $e->path;
            
            $pathInfo = pathinfo($path);
            
            $result .= '<resource identifier="' . $e->id . '" 
                                  type="' . $e->mime . '" 
                                  href="media/' . $pathInfo['filename'] . '.' . $pathInfo['extension'] . '"
                                  adlcp:scormType="asset"
                                  width="' . $e->width . '" 
                                  height="' . $e->height . '" 
                                  h_align="' . $e->h_align . '" 
                                  v_align="' . $e->v_align . '" 
                                  width_type="' . $e->width_type . '" 
                                  height_type="' . $e->height_type . '">
                            <file href="media/' . $pathInfo['filename'] . '.' . $pathInfo['extension'] . '"/>
                            <args>' . $e->args . '</args>
                        </resource>';
        }
        $result .= '</resources>';
        
        return $result;
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
            $elementPath = DOCROOT . $e->path;
            $pathInfo = pathinfo($elementPath);
            if(file_exists($elementPath) && is_dir($this->folderPath . '/media')) {
                copy($elementPath, $this->folderPath . '/media/' . $pathInfo['filename'] . '.' . $pathInfo['extension']);
            }
        }
    }
}

?>