<?php

defined('SYSPATH') or die('No direct script access.');

class Controller_RenderLabyrinth extends Controller_Template {

    public $template = 'home'; // Default

    public function action_index() {
        $mapId = $this->request->param('id', NULL);
        $editOn = $this->request->param('id2', NULL);
        if ($mapId != NULL) {
            $rootNode = DB_ORM::model('map_node')->getRootNodeByMap((int) $mapId);

            if ($rootNode != NULL) {
                $data = Model::factory('labyrinth')->execute($rootNode->id);
                if ($data['redirect'] != NULL) {
                    Request::initial()->redirect(URL::base() . 'renderLabyrinth/go/' . $mapId . '/' . $data['redirect']);
                }
                if ($data) {
                    $data['navigation'] = $this->generateNavigation($data['sections']);

                    if ($data['node']->link_style->name == 'type in text') {
                        $result = $this->generateLinks($data['node'], $data['node_links']);
                        $data['links'] = $result['display'];
                        $data['alinkfil'] = substr($result['alinkfil'], 0, strlen($result['alinkfil']) - 2);
                        $data['alinknod'] = substr($result['alinknod'], 0, strlen($result['alinknod']) - 2);
                    } else {
                        $data['links'] = $this->generateLinks($data['node'], $data['node_links']);
                    }

                    if ($editOn != NULL and $editOn == 1) {
                        $data['node_edit'] = TRUE;
                    } else {
                        $data['node_text'] = $this->parseText($data['node_text']);
                    }

                    $data['trace_links'] = $this->generateReviewLinks($data['traces']);

                    $this->template = View::factory('labyrinth/skin/' . $data['map']->skin->path);
                    $this->template->set('templateData', $data);
                } else {
                    Request::initial()->redirect(URL::base());
                }
            } else {
                Request::initial()->redirect(URL::base());
            }
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

    public function action_go() {
        $mapId = $this->request->param('id', NULL);
        $nodeId = $this->request->param('id2', NULL);
        $editOn = $this->request->param('id3', NULL);
        if ($mapId != NULL) {
            if ($nodeId == NULL) {
                $nodeId = Arr::get($_GET, 'id', NULL);
                if ($nodeId == NULL) {
                    Request::initial()->redirect(URL::base());
                    return;
                }
            }
            $node = DB_ORM::model('map_node')->getNodeById((int) $nodeId);

            if ($node != NULL) {
                $data = Model::factory('labyrinth')->execute($node->id);
                if ($data) {
                    $data['navigation'] = $this->generateNavigation($data['sections']);
                    if ($data['redirect'] != NULL) {
                        Request::initial()->redirect(URL::base() . 'renderLabyrinth/go/' . $mapId . '/' . $data['redirect']);
                    }

                    if ($data['node']->link_style->name == 'type in text') {
                        $result = $this->generateLinks($data['node'], $data['node_links']);
                        $data['links'] = $result['display'];
                        $data['alinkfil'] = substr($result['alinkfil'], 0, strlen($result['alinkfil']) - 2);
                        $data['alinknod'] = substr($result['alinknod'], 0, strlen($result['alinknod']) - 2);
                    } else {
                        $data['links'] = $this->generateLinks($data['node'], $data['node_links']);
                    }

                    if ($editOn != NULL and $editOn == 1) {
                        $data['node_edit'] = TRUE;
                    } else {
                        $data['node_text'] = $this->parseText($data['node_text']);
                    }
                    $data['trace_links'] = $this->generateReviewLinks($data['traces']);

                    $this->template = View::factory('labyrinth/skin/' . $data['map']->skin->path);
                    $this->template->set('templateData', $data);
                } else {
                    Request::initial()->redirect(URL::base());
                }
            } else {
                Request::initial()->redirect(URL::base());
            }
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

    public function action_updateNode() {
        $mapId = $this->request->param('id', NULL);
        $nodeId = $this->request->param('id2', NULL);

        if ($_POST and $mapId != NULL and $nodeId != NULL) {
            DB_ORM::model('map_node')->updateNode($nodeId, $_POST);
            Request::initial()->redirect(URL::base() . 'renderLabyrinth/go/' . $mapId . '/' . $nodeId);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

    public function action_info() {
        $nodeId = $this->request->param('id', NULL);
        if ($nodeId != NULL) {
            $node = DB_ORM::model('map_node', array((int) $nodeId));

            $infoView = View::factory('labyrinth/node/info');
            $infoView->set('info', $node->info);

            $this->template = $infoView;
        }
    }

    public function action_counterPopup() {
        $counterName = $this->request->param('id', NULL);
        $counterValue = $this->request->param('id2', NULL);
        $counterDesc = $this->request->param('id3', NULL);
        $counterLabel = $this->request->param('id4', NULL);

        if ($counterName != NULL) {
            $popupView = View::factory('labyrinth/counter/popup');
            $popupView->set('name', $counterName);
            $popupView->set('currentValue', $counterValue);
            $popupView->set('description', $counterDesc);
            $popupView->set('icon', $counterLabel);

            $this->template = $popupView;
        }
    }

    public function action_review() {
        $mapId = $this->request->param('id', NULL);
        $nodeId = $this->request->param('id2', NULL);

        if ($mapId != NULL and $nodeId != NULL) {
            Model::factory('labyrinth')->review($nodeId);
            Request::initial()->redirect(URL::base() . 'renderLabyrinth/go/' . $mapId . '/' . $nodeId);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

    public function action_chatAnswer() {
        $chatId = $this->request->param('id', NULL);
        $elemId = $this->request->param('id2', NULL);
        $sessionId = $this->request->param('id3', NULL);
        $mapId = $this->request->param('id4', NULL);

        if ($chatId != NULL and $elemId != NULL and $sessionId != NULL and $mapId != NULL) {
            $this->auto_render = false;
            echo Model::factory('labyrinth')->getChatResponce($sessionId, $mapId, $chatId, $elemId);
        } else {
            Response::factory()->body('');
        }
    }

    public function action_questionResponce() {
        $optionNumber = $this->request->param('id', NULL);
        $sessionId = $this->request->param('id2', NULL);
        $questionId = $this->request->param('id3', NULL);

        if ($optionNumber != NULL and $sessionId != NULL and $questionId != NULL) {
            $this->auto_render = false;
            echo Model::factory('labyrinth')->question($sessionId, $questionId, $optionNumber);
        }
    }

    private function generateLinks($node, $links) {
        $result = NULL;
        if (is_array($links) and count($links) > 0) {

            foreach ($links as $link) {
                $title = $link->node_2->title;
                if ($link->text != '') {
                    $title = $link->text;
                }
                switch ($node->link_style->name) {
                    case 'text (default)':
                        $result .= '<p><a href="' . URL::base() . 'renderLabyrinth/go/' . $node->map_id . '/' . $link->node_id_2 . '">' . $title . '</a></p>';
                        break;
                    case 'dropdown':
                        $result .= '<option value="' . $link->node_id_2 . '">' . $title . '</option>';
                        break;
                    case 'dropdown + confidence':
                        $result .= '<option value="' . $link->node_id_2 . '">' . $title . '</option>';
                        break;
                    case 'type in text':
                        if (isset($result['alinkfil'])) {
                            $result['alinkfil'] .= '"' . strtolower($title) . '", ';
                            $result['alinknod'] .= $link->node_id_2 . ', ';
                        } else {
                            $result['alinkfil'] = '"' . strtolower($title) . '", ';
                            $result['alinknod'] = $link->node_id_2 . ', ';
                        }
                        break;
                }
            }

            switch ($node->link_style->name) {
                case 'dropdown':
                    $result .= '<select name="links">' . $result . '</select>';
                    break;
                case 'dropdown + confidence':
                    $result .= '<form method="post" action="mnode.asp"><select name="&chr(34)&"id"&chr(34)&">' . $result . '</select>';
                    $result .= '<select name="conf">';
                    $result .= '<option value="">select how confident you are ...</option>';
                    $result .= '<option value="4">I am very confident</option>';
                    $result .= '<option value="3">I am quite confident</option>';
                    $result .= '<option value="2">I am quite unconfident</option>';
                    $result .= '<option value="1">I am very unconfident</option>';
                    $result .= '</select><input type="submit" name="submit" value="go" /></form>';
                    break;
                case 'type in text':
                    $result['display'] = '<form action="' . URL::base() . 'renderLabyrinth/go/' . $node->map_id . '" name="form2"><input name="filler" type="text" size="25" value="" onKeyUp="javascript:Populate(this.form);"><input type="hidden" name="id" value="' . $node->id . '" /><input type="submit" name="submit" value="go" /></form>';
                    break;
            }

            if ($node->end and $node->link_style->name == 'type in text') {
                var_dump('COOL');
                $result['display'] .= '<p><a href="'.URL::base().'reportManager/showReport/'.Session::instance()->get('session_id').'">end session and view report</a></p>';
            } else if ($node->end) {
                var_dump('COOL');
                $result .= '<p><a href="'.URL::base().'reportManager/showReport/'.Session::instance()->get('session_id').'">end session and view report</a></p>';
            }

            return $result;
        } else {
            if ($node->end and $node->link_style->name == 'type in text') {
                $result['display'] .= '<p><a href="'.URL::base().'reportManager/showReport/'.Session::instance()->get('session_id').'">end session and view report</a></p>';
                return $result;
            } else if ($node->end) {
                $result .= '<p><a href="'.URL::base().'reportManager/showReport/'.Session::instance()->get('session_id').'">end session and view report</a></p>';
                return $result;
            }

            if ($links != '') {
                return $links;
            }
        }
        return NULL;
    }

    private function generateNavigation($sections) {
        if (count($sections) > 0) {
            $result = '';
            foreach ($sections as $section) {
                if ($section->map->section->name == 'visible') {
                    $result .= "<p>" . $section->name . "</p>";
                } else if ($section->map->section->name == 'navigable') {
                    $result .= '<p><a href="';
                    if (count($section->nodes) > 0) {
                        $result .= URL::base() . 'renderLabyrinth/go/' . $section->map_id . '/' . $section->nodes[0]->node->id;
                    } else {
                        $result .= URL::base() . 'renderLabyrinth/index/' . $section->map_id;
                    }
                    $result .= '">' . $section->name . '</a></p>';
                }
            }

            return $result;
        }

        return NULL;
    }
    
    public static function parseText($text) {
        $result = $text;

        $codes = array('MR', 'FL', 'CHAT', 'DAM', 'AV', 'VPD', 'QU');

        foreach ($codes as $code) {
            $regExp = '/[\[' . $code . ':\d\]]+/';
            if (preg_match_all($regExp, $text, $matches)) {
                foreach ($matches as $match) {
                    foreach ($match as $value) {
                        if (stristr($value, '[[' . $code . ':')) {
                            $m = explode(':', $value);
                            $id = substr($m[1], 0, strlen($m[1]) - 2);
                            if (is_numeric($id)) {
                                $replaceString = '';
                                switch ($code) {
                                    case 'MR':
                                        $media = DB_ORM::model('map_element', array((int) $id));
                                        if ($media->mime == 'application/x-shockwave-flash') {
                                            $replaceString = Controller_RenderLabyrinth::getSwfHTML($id);
                                        } else {
                                            $replaceString = Controller_RenderLabyrinth::getImageHTML($id);
                                        }
                                        break;
                                    case 'AV':
                                        $replaceString = Controller_RenderLabyrinth::getAvatarHTML($id);
                                        break;
                                    case 'CHAT':
                                        $replaceString = Controller_RenderLabyrinth::getChatHTML($id);
                                        break;
                                    case 'QU':
                                        $replaceString = Controller_RenderLabyrinth::getQuestionHTML($id);
                                        break;
                                    case 'VPD':
                                        $replaceString = Controller_RenderLabyrinth::getVpdHTML($id);
                                        break;
                                    case 'DAM':
                                        $replaceString = Controller_RenderLabyrinth::getDamHTML($id);
                                        break;
                                }

                                $result = str_replace('[[' . $code . ':' . $id . ']]', $replaceString, $result);
                            }
                        }
                    }
                }
            }
        }

        return $result;
    }

    private static function getImageHTML($id) {
        $image = DB_ORM::model('map_element', array((int) $id));
        if ($image) {
            return '<img src="' . URL::base() . $image->path . '">';
        }

        return '';
    }
    
    private static function getSwfHTML($id) {
        $swf = DB_ORM::model('map_element', array((int) $id));
        if ($swf) {
            $userBrowser = Controller_RenderLabyrinth::getUserBroswer();
            if (substr($userBrowser, 0, 2) == "ie") {
                return "<object classid='clsid:D27CDB6E-AE6D-11cf-96B8-444553540000' codebase='http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0'>
                <param name='movie' value='".URL::base().$swf->path."' />
                <param name='allowScriptAccess' value='sameDomain' />
                <param name='quality' value='high' />
                </object>";
            } else {
                return "<object type='application/x-shockwave-flash' data='".URL::base().$swf->path."'>
                <param name='allowScriptAccess' value='sameDomain' />
                <param name='quality' value='high' />
                </object>";
            }
            
            return '';
        }

        return '';
    }

    private static function getAvatarHTML($id) {
        $avatar = DB_ORM::model('map_avatar', array((int) $id));
        if ($avatar) {
            return '<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0" width="300" height="300" id="avatar_' . $avatar->id . '" align="middle">
                        <param name="allowScriptAccess" value="sameDomain">
                        <param name="movie" value="' . URL::base() . 'documents/avatar.swf">
                        <param name="quality" value="high">
                        <param name="flashVars" value="fSkin=' . $avatar->skin_1 . '&fSkinOut=' . $avatar->skin_2 . '&fBkd=' . $avatar->bkd . '&fCloth=' . $avatar->cloth . '&fNose=' . $avatar->nose . '&fHair=' . $avatar->hair . '&fAccessory1=' . $avatar->accessory_1 . '&fAccessory2=' . $avatar->accessory_2 . '&fAccessory3=' . $avatar->accessory_3 . '&fEnvironment=' . $avatar->environment . '&fOutfit=' . $avatar->outfit . '&fMouth=' . $avatar->mouth . '&fSex=' . $avatar->sex . '&fBubble=' . $avatar->bubble . '&fBubbleText=' . $avatar->bubble_text . '&fAge=' . $avatar->age . '&fEyes=' . $avatar->eyes . '&fWeather=' . $avatar->weather . '&fHairColor=' . $avatar->hair_color . '">
                        <embed src="' . URL::base() . 'documents/avatar.swf" flashvars="fSkin=' . $avatar->skin_1 . '&fSkinOut=' . $avatar->skin_2 . '&fBkd=' . $avatar->bkd . '&fCloth=' . $avatar->cloth . '&fNose=' . $avatar->nose . '&fHair=' . $avatar->hair . '&fAccessory1=' . $avatar->accessory_1 . '&fAccessory2=' . $avatar->accessory_2 . '&fAccessory3=' . $avatar->accessory_3 . '&fEnvironment=' . $avatar->environment . '&fOutfit=' . $avatar->outfit . '&fMouth=' . $avatar->mouth . '&fSex=' . $avatar->sex . '&fBubble=' . $avatar->bubble . '&fBubbleText=' . $avatar->bubble_text . '&fAge=' . $avatar->age . '&fEyes=' . $avatar->eyes . '&fWeather=' . $avatar->weather . '&fHairColor=' . $avatar->hair_color . '" quality="high" bgcolor="#ffffff" width="300" height="300" name="avatar_' . $avatar->id . '" align="middle" allowscriptaccess="sameDomain" allowfullscreen="false" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer">
                      </object>';
        }

        return '';
    }

    private static function getChatHTML($id) {
        $chat = DB_ORM::model('map_chat', array((int) $id));

        if ($chat) {
            $result = '<table bgcolor="#eeeeee" width="100%">';
            if (count($chat->elements) > 0) {
                foreach ($chat->elements as $element) {
                    $result .= "<tr><td><p><a onclick='ajaxChatShowAnswer(" . $chat->id . ", " . $element->id . ");return false;' href='#' id='ChatQuestion" . $element->id . "'>" . $element->question . "</a></p></td></tr>";
                    $result .= "<tr><td><div id='ChatAnswer" . $element->id . "'></div></td></tr>";
                }
            }
            $result .= '</table>';

            return $result;
        }

        return '';
    }

    private static function getQuestionHTML($id) {
        $question = DB_ORM::model('map_question', array((int) $id));

        if ($question) {
            $result = '';

            if ($question->type->value == 'text') {
                $result = "<input type='text' size='" . $question->width . "' name='qresponse_" . $question->id . "' value='" . $question->feedback . "' id='qresponse_" . $question->id . "' onblur='ajaxFunction(" . $question->id . ");' />";
                $result .= "<div id='AJAXresponse'></div>";
            } else if ($question->type->value == 'area') {
                $result = "<textarea cols='" . $question->width . "' rows='" . $question->height . "' name='qresponse_" . $question->id . "' id='qresponse_" . $question->id . "' onblur='ajaxFunction(" . $question->id . ");'>" . $question->feedback . "</textarea>";
                $result .= "<div id='AJAXresponse'></div>";
            } else {
                if (count($question->responses) > 0) {
                    $result = '<table>';
                    $i = 1;
                    $divIDS = 'new Array(';
                    foreach ($question->responses as $responce) {
                        $divIDS .= $responce->id . ',';
                    }
                    $divIDS = substr($divIDS, 0, strlen($divIDS) - 1);
                    $divIDS .= ')';
                    foreach ($question->responses as $responce) {
                        $result .= "<tr><td><p>" . $responce->response . "</p></td>";
                        $result .= "<td><div id='click" . $responce->id . "'><input type='radio' name='option' OnClick='ajaxMCQ(" . $question->id . "," . $responce->id . "," . count($question->responses) . "," . $question->num_tries . "," . $divIDS . ");' /></div></td>";
                        $result .= "<td><div id='AJAXresponse" . $responce->id . "'></div></td></tr>";
                        $i++;
                    }
                    $result .= '</table>';
                }
            }

            $result = "<table bgcolor='#eeeeee' width='100%'><tr><td><p>" . $question->stem . "</p><p><form onsubmit='return false;'>" . $result . "</form></p></td></tr></table>";

            return $result;
        }

        return '';
    }

    public static function getVpdHTML($id) {
        $result = '';
        $vpd = DB_ORM::model('map_vpd', array((int) $id));
        if ($vpd != NULL) {
            switch ($vpd->type->name) {
                case 'VPDText':
                    $vpdType = Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'VPDTextType');
                    $vpdText = Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'VPDText');
                    switch ($vpdType) {
                        case 'narrative':
                            $result .= '<p>' . $vpdText . '</p>';
                            break;
                        case 'chief complaint':
                            $result .= '<p>Chief complaint: ' . $vpdText . '</p>';
                            break;
                        case 'history':
                            $result .= '<p>History: ' . $vpdText . '</p>';
                            break;
                        case 'problem':
                            $result .= '<p>Problem: ' . $vpdText . '</p>';
                            break;
                        case 'allergy':
                            $result .= '<p>Allergy: ' . $vpdText . '</p>';
                            break;
                    }
                    break;
                case 'PatientDiagnoses':
                    $result .= "<table width='100%' border=1 cellspacing='0' cellpadding='6' RULES=NONE FRAME=BOX><tr><td align='left' valign='top' width='30%'><p><strong>Patient Data</strong></p></td><td align='left' valign='top'><p>";
                    $demogText = Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'DemogText');
                    if ($demogText != '') {
                        $result .= $demogText . ' : ' . Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'CoreDemogType');
                    } else {
                        $result .= Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'DemogTitle') . ' : ' . Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'DemogDesc');
                    }
                    $result .= '</p></td></tr></table>';
                    break;
                case 'AuthorDiagnoses':
                    $result .= "<table width='100%' border='1' cellspacing='0' cellpadding='6' RULES='NONE' FRAME='BOX'><tr><td align='left' valign='top' width='30%'><p><strong>Diagnosis</strong></p></td><td align='left' valign='top'><p>";
                    $result .= 'Diagnosis: ' . Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'aDiagTitle') . '<br/>';
                    $result .= 'Description: ' . Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'aDiagDesc');
                    $result .= '</p></td></tr></table>';
                    break;
                case 'Medication':
                    $result .= "<table width='100%' border='1' cellspacing='0' cellpadding='6' RULES='NONE' FRAME='BOX'><tr><td align='left' valign='top' width='30%'><p><strong>Medication</strong></p></td><td align='left' valign='top'><p>";
                    $result .= "Medication: " . Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'MedicTitle') . "<br />";
                    $result .= "Dose: " . Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'MedicDose') . "</p></td><td><p>";
                    $result .= "Route: " . Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'MedicRoute') . "<br />";
                    $result .= "Frequency: " . Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'MedicFreq') . "</p></td>";
                    if (Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'MedicFreq') != '') {
                        $result .= "<td valign='top'><p>Reference: " . Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'MedicSource') . "<br />";
                        $result .= "ID: " . Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'MedicSourceID') . "</p></td>";
                    }
                    $result .= '</tr></table>';
                    break;
                case 'InterviewItem':
                    $result .= "<table border='1' width='100%' cellspacing='0' cellpadding='6' RULES='NONE' FRAME='BOX'><tr><td width='10%' align='left' valign='top' width='30%'><p><strong>Interview Item</strong></p></td><td align='left' valign='top'><p>";
                    if (Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'trigger') == 'on') {
                        $result .= '<script type="text/javascript">function toggle_visibility(id) {
                                    var e = document.getElementById(id);
                                    if(e.style.display == "none")
                                    e.style.display = "block";
                                    else
                                    e.style.display = "none";
                                    }</script>';
                        $result .= '<p><a href="#" onclick="toggle_visibility(' . "'vpdQ_" . $vpd->id . "'" . ');">Q: ' . Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'QAQuestion') . '</a></p>';
                        $result .= "<div id='vpdQ_" . $vpd->id . "' style='display:none'><p>A: " . Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'QAAnswer') . "</p></div>";
                    } else {
                        $result .= '<p>Q: "' . Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'QAQuestion') . '"</p>';
                        $result .= "<p>A: " . Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'QAAnswer') . "</p>";
                    }

                    if (Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'QAMedia') != '') {
                        $mId = (int) Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'QAMedia');
                        $mediaElement = DB_ORM::model('map_element', array((int) $mId));
                        if ($mediaElement->mime == 'application/x-shockwave-flash') {
                            $result .= Controller_RenderLabyrinth::getSwfHTML($id);
                        } else {
                            $result .= $this->getImageHTML($mId);
                        }
                    }
                    $result .= '</p></td></tr></table>';
                    break;
                case 'PhysicalExam':
                    $result .= "<table width='100%' border='1' cellspacing='0' cellpadding='6' RULES='NONE' FRAME='BOX'><tr><td align='left' valign='top' width='30%'><p><strong>Physical Examination</strong></p></td><td align='left' valign='top'><p>";
                    $result .= "Examination: ".Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'ExamName')."<br />";
                    if(Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'ExamDesc') != '') {
                        $result .= "Description: ".Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'ExamDesc')."<br />";
                    }
                    $result .= "Body part: ".Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'BodyPart')."<br />";
                    $result .= "Orientation: - ".Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'ProxDist')
                            .' - '.Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'ProxDist').
                            ' - '.Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'RightLeft').
                            ' - '.Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'FrontBack').
                            ' - '.Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'InfSup').
                            "</p></td><td valign='top'><p>";
                    
                    if (Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'FindMedia') != '') {
                        $mId = (int) Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'FindMedia');
                        $mediaElement = DB_ORM::model('map_element', array((int) $mId));
                        if ($mediaElement->mime == 'application/x-shockwave-flash') {
                            $result .= Controller_RenderLabyrinth::getSwfHTML($id);
                        } else {
                            $result .= $this->getImageHTML($mId);
                        }
                    }
                    
                    $result .= '</p></td></tr></table>';
                    break;
                case 'DiagnosticTest':
                    $result .= "<table width='100%' border='1' cellspacing='0' cellpadding='6' RULES='NONE' FRAME='BOX'><tr><td align='left' valign='top'><p><strong>Diagnostic Test</strong></p></td><td align='left' valign='top'><p>";
                    $result .= "Test: ".Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'TestName')."<br />";
                    $result .= "Description: ".Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'TestDesc')."</p></td><td valign='top'><p>";
                    if (Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'TestResult') != '') {
                        $result .= "Result: ".Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'TestResult')."<br />";
                    }
                    $result .= "Units: ".Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'TestUnits')."</p></td>";
                    $result .= "<td valign='top'><p>Normal values: ".Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'TestNorm')."<br />";
                    
                    if (Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'TestMedia') != '') {
                        $mId = (int) Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'TestMedia');
                        $mediaElement = DB_ORM::model('map_element', array((int) $mId));
                        if ($mediaElement->mime == 'application/x-shockwave-flash') {
                            $result .= Controller_RenderLabyrinth::getSwfHTML($id);
                        } else {
                            $result .= Controller_RenderLabyrinth::getImageHTML($mId);
                        }
                    }
                    
                    $result .= "</p></td></tr></table>";
                    break;
                case 'DifferentialDiagnostic':
                    $result .= "<table width='100%' border='1' cellspacing='0' cellpadding='6' RULES='NONE' FRAME='BOX'><tr><td align='left' valign='top'><p><strong>Differential Diagnosis</strong></p></td><td align='left' valign='top'><p>";
                    $result .= "Diagnosis: ".Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'DiagTitle')."<br />Description: ".Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'DiagDesc')."<br />";
                    $result .= "Likelihood: ".Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'Likelihood')."</p></td>";
                    $result .= "</tr></table>";
                    break;
                case 'Intervention':
                    $result .= "<table width='100%' border='1' cellspacing='0' cellpadding='6' RULES='NONE' FRAME='BOX'><tr><td align='left' valign='top'><p><strong>Intervention</strong></p></td><td align='left' valign='top'><p>";
                
                    $result .= "Intervention: ".Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'IntervTitle')."<br />";
                    if (Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'IntervDesc') != '') { $result .= "Description: ".Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'IntervDesc')."</p></td><td valign='top'><p>"; }
                    if (Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'iMedicTitle') != '') { $result .= "Medication: ".Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'iMedicTitle')."<br />"; }
                    if (Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'iMedicDose') != '') { $result .= "Dose: ".Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'iMedicDose')."<br />"; }
                    if (Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'iMedicRoute') != '') { $result .= "Route: ".Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'iMedicRoute')."<br />"; }
                    if (Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'iMedicFreq') != '') { $result .= "Frequency: ".Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'iMedicFreq')."<br />"; }
                    if (Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'iMedicSource') != '') { $result .= "Source: ".Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'iMedicSource')."<br />"; }
                    if (Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'iMedicSourceID') != '') { $result .= "Source ID: ".Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'iMedicSourceID')."</p></td><td valign='top'><p>"; }
                    if (Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'Appropriateness') != '') { $result .= "Appropriateness: ".Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'Appropriateness')."<br />"; }
                    if (Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'ResultTitle') != '') { $result .= "Results: ".Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'ResultTitle')." - ".Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'ResultDesc')."<br />"; }
                        
                    if (Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'iTestMedia') != '') {
                        $mId = (int) Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'iTestMedia');
                        $mediaElement = DB_ORM::model('map_element', array((int) $mId));
                        if ($mediaElement->mime == 'application/x-shockwave-flash') {
                            $result .= Controller_RenderLabyrinth::getSwfHTML($id);
                        } else {
                            $result .= $this->getImageHTML($mId);
                        }
                    }
                    
                    $result .= "</p></td></tr></table>";
                    break;
            }
        }

        return $result;
    }
    
    public static function getDamHTML($id) {
        $dam = DB_ORM::model('map_dam', array((int)$id));
        $result = '';
        
        if($dam != NULL) {
            if(count($dam->elements) > 0) {
                foreach($dam->elements as $damElement) {
                    switch($damElement->element_type) {
                        case 'vpd':
                            $result .= '[[VPD:'.$damElement->element_id.']]';
                            break;
                        case 'dam':
                            $result .= '[[DAM:'.$damElement->element_id.']]';
                            break;
                        case 'mr':
                            $result .= '[[MR:'.$damElement->element_id.']]';
                            break;
                    }
                    
                    $result = Controller_RenderLabyrinth::parseText($result);
                }
            }
        }
        
        return $result;
    }

    private static function getValueByElementKey($elements, $name) {
        if (count($elements) > 0) {
            foreach ($elements as $element) {
                if ($element->key == $name) {
                    return $element->value;
                }
            }
        }

        return '';
    }
    
    private static function getUserBroswer() {
        if ( stristr($_SERVER['HTTP_USER_AGENT'], 'Firefox') ) return 'firefox';
        elseif ( stristr($_SERVER['HTTP_USER_AGENT'], 'Chrome') ) return 'chrome';
        elseif ( stristr($_SERVER['HTTP_USER_AGENT'], 'Safari') ) return 'safari';
        elseif ( stristr($_SERVER['HTTP_USER_AGENT'], 'Opera') ) return 'opera';
        elseif ( stristr($_SERVER['HTTP_USER_AGENT'], 'MSIE 6.0') ) return 'ie6';
        elseif ( stristr($_SERVER['HTTP_USER_AGENT'], 'MSIE 7.0') ) return 'ie7';
        elseif ( stristr($_SERVER['HTTP_USER_AGENT'], 'MSIE 8.0') ) return 'ie8';
    }

    private function generateReviewLinks($traces) {
        if ($traces != NULL and count($traces) > 0) {
            $result = '';
            foreach ($traces as $trace) {
                $result .= '<p><a href=' . URL::base() . 'renderLabyrinth/review/' . $trace->node->map_id . '/' . $trace->node->id . '>' . $trace->node->title . '</a></p>';
            }

            return $result;
        }

        return '';
    }

}

?>