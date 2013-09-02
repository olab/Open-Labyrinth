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

class Controller_RenderLabyrinth extends Controller_Template {

    public $template = 'home'; // Default

    public function action_index() {
        $continue = true;
        $mapId = $this->request->param('id', NULL);
        $editOn = $this->request->param('id2', NULL);
        if ($mapId != NULL) {
            $mapDB = DB_ORM::model('map', array($mapId));
            if ($mapDB->security_id == 4) {
                $sessionId = Session::instance()->id();
                $checkValue = Auth::instance()->hash('checkvalue' . $mapId . $sessionId);
                $checkSession = Session::instance()->get($checkValue);
                if ($checkSession != '1') {
                    $this->template = View::factory('labyrinth/security');
                    $templateData['mapDB'] = $mapDB;
                    $templateData['title'] = 'OpenLabyrinth';
                    $templateData['keyError'] = Session::instance()->get('keyError');
                    Session::instance()->delete('keyError');
                    $this->template->set('templateData', $templateData);
                    $continue = false;
                }
            }
            if ($continue) {
                Session::instance()->delete('questionChoices');
                Session::instance()->delete('counterFunc');
                Session::instance()->delete('stopCommonRules');
                $rootNode = DB_ORM::model('map_node')->getRootNodeByMap((int) $mapId);

                if ($rootNode != NULL) {
                    $data = Model::factory('labyrinth')->execute($rootNode->id, NULL, true);
                    if ($data) {
                        $data['navigation'] = $this->generateNavigation($data['sections']);

                        if (!isset($data['node_links']['linker'])){
                            if ($data['node']->link_style->name == 'type in text') {
                                $result = $this->generateLinks($data['node'], $data['node_links']);
                                $data['links'] = $result['links']['display'];
                                if(isset($data['alinkfil']) && isset($data['alinknod'])) {
                                     $data['alinkfil'] = substr($result['links']['alinkfil'], 0, strlen($result['links']['alinkfil']) - 2);
                                     $data['alinknod'] = substr($result['links']['alinknod'], 0, strlen($result['links']['alinknod']) - 2);
                                }
                            } else {
                                $result = $this->generateLinks($data['node'], $data['node_links']);
                                $data['links'] = $result['links'];
                            }
                        } else {
                            $data['links'] = $data['node_links']['linker'];
                        }

                        if ($editOn != NULL and $editOn == 1) {
                            $data['node_edit'] = TRUE;
                        } else {

                            if (( $data['node']->info != '' ) && (strpos($data['node_text'],'[[INFO:') === false))
                            {
                                $data['node_text'] .= '[[INFO:' . $data['node']->id . ']]';
                            }

                            if (( $data['node']->info == '' ) && (strpos($data['node_text'],'[[INFO:')))
                            {
                                $search = '[[INFO:' . $data['node']->id . ']]';
                                $data['node_text'] = str_replace($search, '',$data['node_text']);
                            }

                            $data['node_text'] = $this->parseText($data['node_text'], $mapId);

                        }

                        $data['trace_links'] = $this->generateReviewLinks($data['traces']);
                        if ($data['map']->skin->enabled){
                            $data['skin_path'] = $data['map']->skin->path;
                        }else{
                            $data['skin_path'] = NULL;
                        }
                        $data['session'] = (int)$data['traces'][0]->session_id;
                        $data['messages_labyrinth'] = DB_ORM::model('map_popup')->getEnabledLabyrinthMessageByMap($mapId);

                        $this->template = View::factory('labyrinth/skin/basic/basic');
                        $this->template->set('templateData', $data);
                    } else {
                        Request::initial()->redirect(URL::base());
                    }
                } else {
                    Request::initial()->redirect(URL::base());
                }
            }
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

    public function action_checkKey() {
        $mapId = $this->request->param('id', NULL);
        if (($mapId != NULL) & (isset($_POST['securityKey']))) {
            $checkKey = DB_ORM::model('map_key')->checkKey($mapId, $_POST['securityKey']);
            if ($checkKey) {
                $sessionId = Session::instance()->id();
                $checkValue = Auth::instance()->hash('checkvalue' . $mapId . $sessionId);
                Session::instance()->set($checkValue, '1');
            } else {
                Session::instance()->set('keyError', 'Invalid key');
            }
            Request::initial()->redirect(URL::base() . 'renderLabyrinth/index/' . $mapId);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

    public function action_go() {
        $mapId = $this->request->param('id', NULL);
        $nodeId = $this->request->param('id2', NULL);
        $editOn = $this->request->param('id3', NULL);
        $bookMark = $this->request->param('id4', NULL);

        $gotoNode = Session::instance()->get('goto', NULL);

        if ($gotoNode != NULL) {
            Session::instance()->set('goto', NULL);

            Request::initial()->redirect(URL::base() . 'renderLabyrinth/go/' . $mapId . '/' .  $gotoNode);
        }

        if ($mapId != NULL) {
            if ($nodeId == NULL) {
                $nodeId = Arr::get($_GET, 'id', NULL);
                if ($nodeId == NULL) {
                    if ($_POST) {
                        $nodeId = Arr::get($_POST, 'id', NULL);
                        if ($nodeId == NULL) {
                            Request::initial()->redirect(URL::base());
                            return;
                        }
                    }
                }
            }
            $node = DB_ORM::model('map_node')->getNodeById((int) $nodeId);

            if ($node != NULL) {
                if ($bookMark != NULL) {
                    $data = Model::factory('labyrinth')->execute($node->id, (int) $bookMark);
                } else {
                    $data = Model::factory('labyrinth')->execute($node->id);
                }
                if ($data) {
                    $data['navigation'] = $this->generateNavigation($data['sections']);
                    if (!isset($data['node_links']['linker'])){
                        if ($data['node']->link_style->name == 'type in text') {
                            $result = $this->generateLinks($data['node'], $data['node_links']);
                            $data['links'] = $result['links']['display'];
                            if(isset($data['alinkfil']) && isset($data['alinknod'])) {
                                 $data['alinkfil'] = substr($result['links']['alinkfil'], 0, strlen($result['links']['alinkfil']) - 2);
                                 $data['alinknod'] = substr($result['links']['alinknod'], 0, strlen($result['links']['alinknod']) - 2);
                            }
                        } else {
                            $result = $this->generateLinks($data['node'], $data['node_links']);
                            if(!empty($result['links']))
                                $data['links'] = $result['links'];
                            else $data['links'] = "";
                        }
                    } else {
                        $data['links'] = $data['node_links']['linker'];
                    }

                    if ($editOn != NULL and $editOn == 1) {
                        $data['node_edit'] = TRUE;
                    } else {

                        if (( $data['node']->info != '' ) && (strpos($data['node_text'],'[[INFO:') === false))
                        {
                            $data['node_text'] .= '[[INFO:' . $data['node']->id . ']]';
                        }

                        if (( $data['node']->info == '' ) && (strpos($data['node_text'],'[[INFO:')))
                        {
                            $search = '[[INFO:' . $data['node']->id . ']]';
                            $data['node_text'] = str_replace($search, '',$data['node_text']);
                        }

                        $data['node_text'] = $this->parseText($data['node_text'], $mapId);
                    }
                    $data['trace_links'] = $this->generateReviewLinks($data['traces']);
                    $data['skin_path'] = $data['map']->skin->path;

                    //Calculate time for Timer and Pop-up messages
                    $data['timer_start'] = 1;

                    $data['popup_start'] = 1;
                    $data['messages_labyrinth'] = DB_ORM::model('map_popup')->getEnabledLabyrinthMessageByMap($mapId);

                    if ( isset($data['traces'][0]) && $data['traces'][0]->session_id != null ) {
                        $sessionId = (int)$data['traces'][0]->session_id;

                        $lastNode = DB_ORM::model('user_sessiontrace')->getLastTraceBySessionId($sessionId);
                        $startSession = DB_ORM::model('user_session')->getStartTimeSessionById($sessionId);

                        $timeForNode = $lastNode[0]['date_stamp'] - $startSession;

                        $data['timeForNode'] = $timeForNode;
                        $data['session'] = $sessionId;

                        if ($data['node']->undo) {
                            $undoLinks = $this->prepareUndoLinks($sessionId,$mapId);

                            $data['undoLinks'] = $undoLinks;
                        }

                    }

                    $this->template = View::factory('labyrinth/skin/basic/basic');
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
            $info = self::parseText( $node->info);
            $infoView = View::factory('labyrinth/node/info');
            $infoView->set('info', $info);

            $this->template = $infoView;
        }
    }

    public function action_mapinfo()
    {
        $mapId = $this->request->param('id', NULL);
        if ($mapId) {
            $this->templateData['map'] = DB_ORM::model('map', array((int) $mapId));

            $infoView = View::factory('labyrinth/labyrinthInfo');
            $infoView->set('templateData', $this->templateData);

            $this->templateData['center'] = $infoView;
            unset($this->templateData['right']);
            $this->template->set('templateData', $this->templateData);
        } else {
            Request::initial()->redirect(URL::base() . 'openLabyrinth');
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

    public function action_undo() {
        $mapId = $this->request->param('id', NULL);
        $nodeId = $this->request->param('id2', NULL);

        if ($mapId != NULL and $nodeId != NULL) {
            Model::factory('labyrinth')->review($nodeId);
            Model::factory('labyrinth')->undo($nodeId);
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

    public function action_questionResponse() {
        $optionNumber   = $this->request->param('id', NULL);
        $questionId     = $this->request->param('id2', NULL);
        $nodeId         = $this->request->param('id3', null);
        $questionStatus = $this->request->param('id4', NULL);

        if ($optionNumber != NULL and $questionId != NULL) {
            $this->auto_render = false;
            
            echo Model::factory('labyrinth')->question($questionId, $optionNumber, $questionStatus, $nodeId);
        }
    }

    public function action_saveSliderQuestionResponse() {
        $this->auto_render = false;

        $questionId = $this->request->param('id', null);
        $value      = Arr::get($_POST, 'value', 0);

        $responses = Session::instance()->get('sliderQuestionResponses');
        $responses[$questionId] = $value;

        Session::instance()->set('sliderQuestionResponses', $responses);
    }

    public function action_remote() {
        $mapId = $this->request->param('id', NULL);
        $mode = $this->request->param('id2', NULL);

        $this->auto_render = false;

        if ($mapId != NULL and $mode != NULL) {
            switch ($mode) {
                case 'u':
                    $username = $this->request->param('id3', NULL);
                    $password = $this->request->param('id4', NULL);
                    $nodeId = $this->request->param('id5', NULL);
                    if ($this->checkRemoteUser($username, $password)) {
                        if ($nodeId != NULL) {
                            echo '<?xml version="1.0" encoding=UTF-8?>' . $this->remote_go($nodeId, $mapId);
                        } else {
                            $rootNode = DB_ORM::model('map_node')->getRootNodeByMap((int) $mapId);
                            echo '<?xml version="1.0" encoding=UTF-8?>' . $this->remote_go($rootNode->id, $mapId);
                        }
                    } else {
                        echo '<?xml version="1.0" encoding=UTF-8?><labyrinth>Not a valid service: no registration for this username and password</labyrinth>';
                    }
                    break;
                case 'i':
                    if ($this->checkRemoteIP($mapId)) {
                        $nodeId = $this->request->param('id3', NULL);
                        if ($nodeId != NULL) {
                            echo '<?xml version="1.0" encoding=UTF-8?>' . $this->remote_go($nodeId, $mapId);
                        } else {
                            $rootNode = DB_ORM::model('map_node')->getRootNodeByMap((int) $mapId);
                            echo '<?xml version="1.0" encoding=UTF-8?>' . $this->remote_go($rootNode->id, $mapId);
                        }
                    } else {
                        echo '<?xml version="1.0" encoding=UTF-8?><labyrinth>Not a valid service: no registration for this IP (' . getenv('REMOTE_ADDR') . ')</labyrinth>';
                    }
                    break;
            }
        } else {
            echo '';
        }
    }

    public function action_addBookmark() {
        $sessionId = $this->request->param('id', NULL);
        $nodeId = $this->request->param('id2', NULL);

        if ($sessionId != NULL and $nodeId != NULL) {
            DB_ORM::model('user_bookmark')->addBookmark($nodeId, $sessionId);
        }
    }

    private function remote_go($nodeId, $mapId) {
        if ($mapId != NULL) {
            $node = DB_ORM::model('map_node')->getNodeById((int) $nodeId);

            if ($node != NULL) {
                $data = Model::factory('labyrinth')->execute($node->id);
                if ($data) {
                    $data['navigation'] = $this->generateNavigation($data['sections']);

                    if (!isset($data['node_links']['linker'])){
                        if ($data['node']->link_style->name == 'type in text') {
                            $result = $this->generateLinks($data['node'], $data['node_links']);
                            $data['links'] = $result['links']['display'];
                            $data['alinkfil'] = substr($result['links']['alinkfil'], 0, strlen($result['links']['alinkfil']) - 2);
                            $data['alinknod'] = substr($result['links']['alinknod'], 0, strlen($result['links']['alinknod']) - 2);
                            $data['remote_links'] = $this->generateRemoteLinks($data['node_links']);
                        } else {
                            $result = $this->generateLinks($data['node'], $data['node_links']);
                            $data['links'] = $result['links'];
                            $data['remote_links'] = $this->generateRemoteLinks($data['node_links']);
                        }
                    } else {
                        $data['links'] = $data['node_links']['linker'];
                    }

                    $data['node_text'] = $this->parseText($data['node_text'], $mapId);

                    $data['trace_links'] = $this->generateReviewLinks($data['traces']);

                    if ($data) {
                        $data['links'] = str_replace('Array', '', $data['links']);
                        $data['trace_links'] = '<a href="#" onclick="toggle_visibility(' . "'track'" . ');"><p class="style2"><strong>Review your pathway</strong></p></a><div id="track" style="display:none">' . $data['trace_links'] . '</div>';
                        $rootNode = DB_ORM::model('map_node')->getRootNodeByMap((int) $mapId);
                        $out = "<labyrinth>";
                        $out .= "<mnodetitle>" . urlencode($data['node_title']) . "</mnodetitle>";
                        $out .= "<javascripttime></javascripttime>";
                        $out .= "<mapname>" . urlencode($data['map']->name) . "</mapname>";
                        $out .= "<mapid>" . urlencode($data['map']->id) . "</mapid>";
                        $out .= "<mnodeid>" . urlencode($data['node']->id) . "</mnodeid>";
                        $out .= "<timestring></timestring>";
                        $out .= "<message>" . urlencode($data['node_text']) . "</message>";
                        $out .= "<colourbar></colourbar>";
                        $out .= "<linker>" . urlencode($data['links']) . "</linker>";
                        $out .= "<links>" . urlencode($data['remote_links']) . "</links>";
                        $out .= "<counters>" . $data['remoteCounters'] . "</counters>";
                        $out .= "<tracestring>" . urlencode($data['trace_links']) . "</tracestring>";
                        $out .= "<rootnode>" . urlencode($rootNode->id) . "</rootnode>";
                        $out .= "<infolink>" . urlencode($data['node']->info) . "</infolink>";
                        $out .= "<usermode></usermode>";
                        $out .= "<dam></dam>";
                        $out .= "<mysession>" . urlencode(Session::instance()->get('session_id')) . "</mysession>";
                        $out .= "<counterstring>" . urlencode($data['counters']) . "</counterstring>";
                        $out .= "<navme></navme>";
                        $out .= "<maptype>" . urlencode($data['map']->type->name) . "</maptype>";
                        $out .= "<remoteredir></remoteredir>";
                        $out .= "</labyrinth>";

                        return $out;
                    }
                } else {
                    return '';
                }
            } else {
                return '';
            }
        } else {
            return '';
        }
    }

    public function action_reset() {
        $mapId       = $this->request->param('id', null);
        $webinarId   = $this->request->param('id2', null);
        $webinarStep = $this->request->param('id3', null);

        if($webinarId != null && $webinarStep != null && $webinarId > 0 && $webinarStep > 0) {
            Session::instance()->set('webinarId', $webinarId);
            Session::instance()->set('step', $webinarStep);
        }

        Request::initial()->redirect(URL::base() . 'renderLabyrinth/index/' . $mapId);
    }

    private function checkRemoteUser($username, $password) {
        $username = Model::factory('utilites')->deHash($username);
        $password = Model::factory('utilites')->deHash($password);

        $user = DB_ORM::model('user')->getUserByName($username);
        if ($user) {
            if ($user->password == $password and $user->type->name == 'remote service') {
                return TRUE;
            }
        }

        return FALSE;
    }

    private function checkRemoteIP($mapId) {
        if (($id = DB_ORM::model('remoteService')->checkService(getenv('REMOTE_ADDR'))) != FALSE) {
            if (DB_ORM::model('remoteMap')->checkMap($id, $mapId)) {
                return TRUE;
            }
        }
        return FALSE;
    }

    private function generateRemoteLinks($links) {
        if (count($links) > 0) {
            $result = '';
            foreach ($links as $link) {
                $result .= $link->node_id_2 . ',' . $link->text . ';';
            }

            return $result;
        }

        return '';
    }

    private function generateLinks($node, $links) {
        $result = NULL;
        $result['links'] = '';
        $endNodeTemplate = '<div><a href="' . URL::base() . 'reportManager/showReport/' . Session::instance()->get('session_id') . '">End Session and View Feedback</a></div>';
        if (is_array($links) and count($links) > 0) {
            $result['remote_links'] = '';
            $result['links'] = '';
            foreach ($links as $link) {
                $title = $link->node_2->title;
                if ($link->text != '' and $link->text != ' ') {
                    $title = $link->text;
                }

                switch ($node->link_style->name) {
                    case 'hyperlinks (default)':
                        if ($link->image_id != 0) {
                            $result['links'] .= '<li><a href="' . URL::base() . 'renderLabyrinth/go/' . $node->map_id . '/' . $link->node_id_2 . '"><img src="' . URL::base() . $link->image->path . '"></a></li>';
                        } else {
                            $result['links'] .= '<li><a href="' . URL::base() . 'renderLabyrinth/go/' . $node->map_id . '/' . $link->node_id_2 . '">' . $title . '</a></li>';
                        }
                        break;
                    case 'dropdown':
                        $result['links'] .= '<option value="' . $link->node_id_2 . '">' . $title . '</option>';
                        break;
                    case 'dropdown + confidence':
                        $result['links'] .= '<option value="' . $link->node_id_2 . '">' . $title . '</option>';
                        break;
                    case 'type in text':
                        if (isset($result['links']['alinkfil'])) {
                            $result['links']['alinkfil'] .= '"' . strtolower($title) . '", ';
                            $result['links']['alinknod'] .= '"' . $link->node_id_2 . '", ';
                        } else {
                            $result['links']['alinkfil'] = '"' . strtolower($title) . '", ';
                            $result['links']['alinknod'] = '"' . $link->node_id_2 . '", ';
                        }
                        break;
                    case 'buttons':
                        $result['links'] .= '<div><a href="' . URL::base() . 'renderLabyrinth/go/' . $node->map_id . '/' . $link->node_id_2 . '" class="btn">' . $title . '</a></div>';
                        break;
                }
            }

            switch ($node->link_style->name) {
                case 'hyperlinks (default)':
                    $result['links'] = '<ul class="links navigation">'.$result['links'].'</ul>';
                    break;
                case 'dropdown':
                    $result['links'] = '<select name="links" onchange=' . chr(34) . "jumpMenu('parent',this,0)" . chr(34) . ' name="linkjump"><option value="">select ...</option>' . $result['links'] . '</select>';
                    break;
                case 'dropdown + confidence':
                    $result['links'] = '<form method="post" action="' . URL::base() . 'renderLabyrinth/go/' . $node->map_id . '"><select name="id">' . $result['links'] . '</select>';
                    $result['links'] .= '<select name="conf">';
                    $result['links'] .= '<option value="">select how confident you are ...</option>';
                    $result['links'] .= '<option value="4">I am very confident</option>';
                    $result['links'] .= '<option value="3">I am quite confident</option>';
                    $result['links'] .= '<option value="2">I am quite unconfident</option>';
                    $result['links'] .= '<option value="1">I am very unconfident</option>';
                    $result['links'] .= '</select><input type="submit" name="submit" value="go" /></form>';
                    break;
                case 'type in text':
                    $result['links']['display'] = '<form action="' . URL::base() . 'renderLabyrinth/go/' . $node->map_id . '" name="form2"><input name="filler" type="text" size="25" value="" onKeyUp="javascript:Populate(this.form);"><input type="hidden" name="id" value="' . $node->id . '" /><input type="submit" name="submit" value="go" /></form>';
                    break;
            }

            if ($node->end and $node->link_style->name == 'type in text') {
                $result['links']['display'] .= $endNodeTemplate;
            } else if ($node->end) {
                $result['links'] .= $endNodeTemplate;
            }

            return $result;
        } else {
            if ($node->end and $node->link_style->name == 'type in text') {
                if(!isset($result['links']['display']))
                    $result['links']['display'] = '';
                $result['links']['display'] .= $endNodeTemplate;
                return $result;
            } else if ($node->end) {
                $result['links'] .= $endNodeTemplate;
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
            $result = '<ul class="navigation">';
            foreach ($sections as $section) {
                if ($section->map->section->name == 'visible') {
                    $result .= "<li>" . $section->name . "</li>";
                } else if ($section->map->section->name == 'navigable') {
                    $result .= '<li><a href="';
                    if (count($section->nodes) > 0) {
                        $result .= URL::base() . 'renderLabyrinth/go/' . $section->map_id . '/' . $section->nodes[0]->node->id;
                    } else {
                        $result .= URL::base() . 'renderLabyrinth/index/' . $section->map_id;
                    }
                    $result .= '">' . $section->name . '</a></li>';
                }
            }
            $result .= '</ul>';
            return $result;
        }

        return NULL;
    }

    public static function parseText($text, $mapId = NULL) {
        $result = $text;

        $codes = array('MR', 'FL', 'CHAT', 'DAM', 'AV', 'VPD', 'QU', 'INFO', 'VD', 'CR');

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
                                        } elseif (strstr($media->mime, 'audio')) {
                                            $replaceString = Controller_RenderLabyrinth::getAudioHTML($id);
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
                                    case 'INFO':
                                        $replaceString = Controller_RenderLabyrinth::getInfoHTML($id);
                                        break;
                                    case 'VD':
                                        $replaceString = Controller_RenderLabyrinth::getVisualDisplayHTML($id);
                                        break;
                                    case 'CR':
                                        $replaceString = Controller_RenderLabyrinth::getCounterHTML($mapId, $id);
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

    private static function getInfoHTML($id) {
        $info = '<a href="#" onclick="window.open(\'' . URL::base() . 'renderLabyrinth/info/' . $id . '\', \'info\', \'toolbar=no, directories=no, location=no, status=no, menubat=no, resizable=no, scrollbars=yes, width=500, height=400\'); return false;"><img src="' . URL::base() . 'images/info_lblu.gif" border="0" alt="info"></a>';
        return $info;
    }

    private static function getImageHTML($id) {
        $image = DB_ORM::model('map_element', array((int) $id));
        if ($image) {
            return '<img src="' . URL::base() . $image->path . '">';
        }

        return '';
    }

    private static function getAudioHTML($id) {
        $audio = DB_ORM::model('map_element', array((int) $id));
        if ($audio) {
            return '<audio src="' . URL::base() . $audio->path . '" controls preload="auto" autoplay="autoplay" autobuffer></audio>';
        }

        return '';
    }

    private static function getSwfHTML($id) {
        $swf = DB_ORM::model('map_element', array((int) $id));
        if ($swf) {
            $userBrowser = Controller_RenderLabyrinth::getUserBroswer();
            if (substr($userBrowser, 0, 2) == "ie") {
                return "<object classid='clsid:D27CDB6E-AE6D-11cf-96B8-444553540000' codebase='http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0'>
                <param name='movie' value='" . URL::base() . $swf->path . "' />
                <param name='allowScriptAccess' value='sameDomain' />
                <param name='quality' value='high' />
                </object>";
            } else {
                return "<object type='application/x-shockwave-flash' data='" . URL::base() . $swf->path . "'>
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
        if ($avatar->image != null) {
            $image = '<img src="' . URL::base() . 'avatars/' . $avatar->image . '" />';
        } else {
            $image = '<img src="' . URL::base() . 'avatars/default.png" />';
        }
        return $image;
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
                $result = '<input autocomplete="off" class="clearQuestionPrompt" type="text" size="' . $question->width . '" name="qresponse_' . $question->id . '" value="' . $question->feedback . '" id="qresponse_' . $question->id . '" ' ;
                $submitText = 'Submit';
                if ($question->show_submit == 1) {
                    if ($question->submit_text != null) {
                        $submitText = $question->submit_text;
                    }
                    $result .= '/><span id="questionSubmit' . $question->id . '" style="display:none;font-size:12px">Answer has been sent.</span><button onclick="ajaxFunction(' . $question->id . ');$(this).hide();$(\'#questionSubmit' . $question->id . '\').show();$(\'#qresponse_' . $question->id . '\').attr(\'disabled\', \'disabled\');">' . $submitText. '</button>';
                }
                else {
                    $result .= 'onKeyUp="if (event.keyCode == 13) {ajaxFunction(' . $question->id . ');$(\'#questionSubmit' . $question->id . '\').show();$(\'#qresponse_' . $question->id . '\').attr(\'disabled\', \'disabled\');}"/><span id="questionSubmit' . $question->id . '" style="display:none;font-size:12px">Answer has been sent.</span>';
                }
                $result .= '<div id="AJAXresponse' . $question->id . '"></div>';
            } else if ($question->type->value == 'area') {
                $result = '<textarea autocomplete="off" class="clearQuestionPrompt" cols="' . $question->width . '" rows="' . $question->height . '" name="qresponse_' . $question->id . '" id="qresponse_' . $question->id . '">' . $question->feedback . '</textarea><p><span id="questionSubmit' . $question->id . '" style="display:none;font-size:12px">Answer has been sent.</span><button onclick="ajaxFunction(' . $question->id . ');$(this).hide();$(\'#questionSubmit' . $question->id . '\').show();$(\'#qresponse_' . $question->id . '\').attr(\'readonly\', \'readonly\');">Submit</button></p>';
                $result .= '<div id="AJAXresponse' . $question->id . '"></div>';
            } else if($question->type->value == 'mcq') {
                if (count($question->responses) > 0) {
                    $result = '<div class="questionResponces ';
                    $result .= ($question->type_display == 1) ? 'horizontal' : '';
                    $result .= '"><ul class="navigation">';
                    $i = 1;
                    foreach ($question->responses as $responce) {
                        $result .= '<li>';
                        $result .= '<span id="click' . $responce->id . '"><input type="checkbox" name="option-'.$id.'" onclick="ajaxQU(this, ' . $question->id . ',' . $responce->id . ',' . $question->num_tries . ');" /></span>';
                        $result .= '<span class="text">' . $responce->response . '</span>';
                        $result .= '<span id="AJAXresponse' . $responce->id . '"></span>';
                        $result .= '</li>';
                        $i++;
                    }
                    
                    $result .= '</ul></div>';
                    if($question->show_submit == 1 && $question->redirect_node_id != null && $question->redirect_node_id > 0) {
                        $result .= '<div class="questionSubmitButton"><a href="' . URL::base() . 'renderLabyrinth/go/' . $question->map_id . '/' . $question->redirect_node_id . '"><input type="button" value="' . $question->submit_text . '" /></a></div>';
                    }
                }
            } else if($question->type->value == 'pcq') {
                if (count($question->responses) > 0) {
                    $result = '<div class="questionResponces questionForm_'.$question->id.' ';
                    $result .= ($question->type_display == 1) ? 'horizontal' : '';
                    $result .= '"><ul class="navigation">';
                    $i = 1;
                    foreach ($question->responses as $responce) {
                        $result .= '<li>';
                        $result .= '<span class="click" id="click' . $responce->id . '"><input type="radio" name="option-'.$id.'" onclick="ajaxQU(this, ' . $question->id . ',' . $responce->id . ',' . $question->num_tries . ');" /></span>';
                        $result .= '<span>' . $responce->response . '</span>';
                        $result .= '<span id="AJAXresponse' . $responce->id . '"></span>';
                        $result .= '</li>';
                        $i++;
                    }
                    if($question->show_submit == 1 && $question->redirect_node_id != null && $question->redirect_node_id > 0) {
                        $result .= '<div class="questionSubmitButton"><a href="' . URL::base() . 'renderLabyrinth/go/' . $question->map_id . '/' . $question->redirect_node_id . '"><input type="button" value="' . $question->submit_text . '" /></a></div>';
                    }
                }
            } else if($question->type->value == 'slr') {
                if($question->settings != null) {
                    $settings = json_decode($question->settings);
                    $sliderValue = $settings->minValue;

                    if($question->counter_id > 0) {
                        $sliderValue = Controller_RenderLabyrinth::getCurrentCounterValue($question->map_id, $question->counter_id);
                    } else if(property_exists($settings, 'defaultValue')) {
                        $sliderValue = $settings->defaultValue;
                    }

                    if($sliderValue > $settings->maxValue) {
                        $sliderValue = $settings->maxValue;
                    } else if($sliderValue < $settings->minValue) {
                        $sliderValue = $settings->minValue;
                    }

                        if($settings->showValue == 1) {
                            $result .= '<div style="margin-bottom: 22px;position:relative">
                                            <input autocomplete="off" type="text" id="sliderQuestionR_' . $question->id . '" value="' . $settings->minValue . '" style="float: left;height: 20px;padding: 0;margin: 0;font-size: 11px;width: 40px;" ' . ($settings->abilityValue == 0 ? 'disabled' : '') . '/>
                                            <div style="font-size: 12px;position: absolute;' . ($settings->orientation == 'hor' ? "top: 21px;left: 51px;" : "top: 2px;left: 74px;") . '">' . $settings->minValue . '</div>
                                            <script>
                                                var slider' . $question->id . ' = new dhtmlxSlider({
                                                    size: 300,
                                                    value: '    . $sliderValue                       . ',
                                                    min: '      . $settings->minValue                       . ',
                                                    max: '      . $settings->maxValue                       . ',
                                                    skin: "'    . $settings->sliderSkin                     . '",
                                                    step: '     . $settings->stepValue                      . ',
                                                    vertical: ' . ($settings->orientation == 'hor' ? 0 : 1) . ',
                                                    onChange: function(n) { $("#sliderQuestionR_' . $question->id . '").val(n); },
                                                    onSlideEnd: function(value) { sendSliderValue(' . $question->id . ', value); }
                                                });
                                                slider' . $question->id . '.init();
                                                $("#sliderQuestionR_' . $question->id . '").val(' . $sliderValue . ');
                                                $("#sliderQuestionR_' . $question->id . '").change(function() {
                                                    var value = $(this).val();
                                                    if(value > ' . $settings->maxValue . ') {
                                                        value = ' . $settings->maxValue . ';
                                                        $(this).val(value);
                                                    } else if(value < ' . $settings->minValue . ') {
                                                        value = ' . $settings->minValue . ';
                                                        $(this).val(value);
                                                    }

                                                    slider' . $question->id . '.setValue(value);
                                                    sendSliderValue(' . $question->id . ', value);
                                                });
                                            </script>
                                            <div style="font-size: 12px;position: absolute;' . ($settings->orientation == 'hor' ? "top: 21px;left: 330px;" : "top: 284px;left: 74px;") . '">' . $settings->maxValue . '</div>
                                        </div>';
                        } else {
                            $result .= '<div style="margin-bottom: 22px;position:relative">
                                            <div style="font-size: 12px;position: absolute;' . ($settings->orientation == 'hor' ? "top: 21px;left: 5px;" : "top: 2px;left: 34px;") . '">' . $settings->minValue . '</div>
                                            <script>
                                                var slider = new dhtmlxSlider({
                                                    size: 300,
                                                    value: '    . $sliderValue                       . ',
                                                    min: '      . $settings->minValue                       . ',
                                                    max: '      . $settings->maxValue                       . ',
                                                    skin: "'    . $settings->sliderSkin                     . '",
                                                    step: '     . $settings->stepValue                      . ',
                                                    vertical: ' . ($settings->orientation == 'hor' ? 0 : 1) . ',
                                                    onSlideEnd: function(value) { sendSliderValue(' . $question->id . ', value); }
                                                });
                                                slider.init();
                                            </script>
                                            <div style="font-size: 12px;position: absolute;' . ($settings->orientation == 'hor' ? "top: 21px;left: 290px;" : "top: 284px;left: 34px;") . '">' . $settings->maxValue . '</div>
                                        </div>';
                        }
                }
            }

            $result = '<table bgcolor="#eeeeee" width="100%"><tr><td><p>' . $question->stem . '</p>' . $result . '</td></tr></table>';

            return $result;
        }

        return '';
    }

    private static function getCurrentCounterValue($mapId, $counterId) {
        $rootNode = DB_ORM::model('map_node')->getRootNodeByMap($mapId);
        $counter  = DB_ORM::model('map_counter', array((int)$counterId));

        $sessionId = Session::instance()->get('session_id', NULL);
        if ($sessionId == NULL && isset($_COOKIE['OL'])) {
            $sessionId = $_COOKIE['OL'];
        } else {
            if ($sessionId == NULL){
                $sessionId = 'notExist';
            }
        }

        $currentCountersState = '';
        if ($rootNode != NULL) {
            $currentCountersState = DB_ORM::model('user_sessionTrace')->getCounterByIDs($sessionId, $rootNode->map_id, $rootNode->id);
        }

        $thisCounter = null;
        if ($currentCountersState != '') {
            $s = strpos($currentCountersState, '[CID=' . $counter->id . ',') + 1;
            $tmp = substr($currentCountersState, $s, strlen($currentCountersState));
            $e = strpos($tmp, ']') + 1;
            $tmp = substr($tmp, 0, $e - 1);
            $tmp = str_replace('CID=' . $counter->id . ',V=', '', $tmp);
            $thisCounter = $tmp;
        }

        return $thisCounter != null ? $thisCounter : 0;
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
                            $result .= $vpdText;
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
                            $result .= self::getImageHTML($mId);
                        }
                    }
                    $result .= '</p></td></tr></table>';
                    break;
                case 'PhysicalExam':
                    $result .= "<table width='100%' border='1' cellspacing='0' cellpadding='6' RULES='NONE' FRAME='BOX'><tr><td align='left' valign='top' width='30%'><p><strong>Physical Examination</strong></p></td><td align='left' valign='top'><p>";
                    $result .= "Examination: " . Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'ExamName') . "<br />";
                    if (Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'ExamDesc') != '') {
                        $result .= "Description: " . Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'ExamDesc') . "<br />";
                    }
                    $result .= "Body part: " . Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'BodyPart') . "<br />";
                    $result .= "Orientation: - " . Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'ProxDist')
                            . ' - ' . Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'ProxDist') .
                            ' - ' . Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'RightLeft') .
                            ' - ' . Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'FrontBack') .
                            ' - ' . Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'InfSup') .
                            "</p></td><td valign='top'><p>";

                    if (Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'FindMedia') != '') {
                        $mId = (int) Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'FindMedia');
                        $mediaElement = DB_ORM::model('map_element', array((int) $mId));
                        if ($mediaElement->mime == 'application/x-shockwave-flash') {
                            $result .= Controller_RenderLabyrinth::getSwfHTML($id);
                        } else {
                            $result .= self::getImageHTML($mId);
                        }
                    }

                    $result .= '</p></td></tr></table>';
                    break;
                case 'DiagnosticTest':
                    $result .= "<table width='100%' border='1' cellspacing='0' cellpadding='6' RULES='NONE' FRAME='BOX'><tr><td align='left' valign='top'><p><strong>Diagnostic Test</strong></p></td><td align='left' valign='top'><p>";
                    $result .= "Test: " . Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'TestName') . "<br />";
                    $result .= "Description: " . Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'TestDesc') . "</p></td><td valign='top'><p>";
                    if (Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'TestResult') != '') {
                        $result .= "Result: " . Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'TestResult') . "<br />";
                    }
                    $result .= "Units: " . Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'TestUnits') . "</p></td>";
                    $result .= "<td valign='top'><p>Normal values: " . Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'TestNorm') . "<br />";

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
                    $result .= "Diagnosis: " . Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'DiagTitle') . "<br />Description: " . Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'DiagDesc') . "<br />";
                    $result .= "Likelihood: " . Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'Likelihood') . "</p></td>";
                    $result .= "</tr></table>";
                    break;
                case 'Intervention':
                    $result .= "<table width='100%' border='1' cellspacing='0' cellpadding='6' RULES='NONE' FRAME='BOX'><tr><td align='left' valign='top'><p><strong>Intervention</strong></p></td><td align='left' valign='top'><p>";

                    $result .= "Intervention: " . Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'IntervTitle') . "<br />";
                    if (Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'IntervDesc') != '') {
                        $result .= "Description: " . Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'IntervDesc') . "</p></td><td valign='top'><p>";
                    }
                    if (Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'iMedicTitle') != '') {
                        $result .= "Medication: " . Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'iMedicTitle') . "<br />";
                    }
                    if (Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'iMedicDose') != '') {
                        $result .= "Dose: " . Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'iMedicDose') . "<br />";
                    }
                    if (Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'iMedicRoute') != '') {
                        $result .= "Route: " . Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'iMedicRoute') . "<br />";
                    }
                    if (Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'iMedicFreq') != '') {
                        $result .= "Frequency: " . Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'iMedicFreq') . "<br />";
                    }
                    if (Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'iMedicSource') != '') {
                        $result .= "Source: " . Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'iMedicSource') . "<br />";
                    }
                    if (Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'iMedicSourceID') != '') {
                        $result .= "Source ID: " . Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'iMedicSourceID') . "</p></td><td valign='top'><p>";
                    }
                    if (Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'Appropriateness') != '') {
                        $result .= "Appropriateness: " . Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'Appropriateness') . "<br />";
                    }
                    if (Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'ResultTitle') != '') {
                        $result .= "Results: " . Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'ResultTitle') . " - " . Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'ResultDesc') . "<br />";
                    }

                    if (Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'iTestMedia') != '') {
                        $mId = (int) Controller_RenderLabyrinth::getValueByElementKey($vpd->elements, 'iTestMedia');
                        $mediaElement = DB_ORM::model('map_element', array((int) $mId));
                        if ($mediaElement->mime == 'application/x-shockwave-flash') {
                            $result .= Controller_RenderLabyrinth::getSwfHTML($id);
                        } else {
                            $result .= self::getImageHTML($mId);
                        }
                    }

                    $result .= "</p></td></tr></table>";
                    break;
            }
        }

        return $result;
    }

    public static function getDamHTML($id) {
        $dam = DB_ORM::model('map_dam', array((int) $id));
        $result = '';

        if ($dam != NULL) {
            if (count($dam->elements) > 0) {
                foreach ($dam->elements as $damElement) {
                    switch ($damElement->element_type) {
                        case 'vpd':
                            $result .= '[[VPD:' . $damElement->element_id . ']]';
                            break;
                        case 'dam':
                            $result .= '[[DAM:' . $damElement->element_id . ']]';
                            break;
                        case 'mr':
                            $result .= '[[MR:' . $damElement->element_id . ']]';
                            break;
                    }

                    $result = Controller_RenderLabyrinth::parseText($result);
                }
            }
        }

        return $result;
    }

    private static function getVisualDisplayHTML($visualDisplayId) {
        $visualDisplay = DB_ORM::model('map_visualdisplay', array((int) $visualDisplayId));
        $result = '';

        $traceId = Session::instance()->get('trace_id');
        $traceCountersValues = Session::instance()->get('traceCountersValues');

        if($visualDisplay != null) {
            $result .= '<div class="visual-display-container" style="position:relative; display:block; height: 100%; width: 100%;">';

            if($visualDisplay->panels != null && count($visualDisplay->panels) > 0) {
                foreach($visualDisplay->panels as $panel) {
                    $result .= '<div style="        position: absolute;
                                                         top: ' . $panel->y . 'px;
                                                        left: ' . $panel->x . 'px;
                                                     z-index: ' . $panel->z_index . ';
                                            background-color: ' . $panel->background_color . ';
                                                       width: ' . $panel->width . ';
                                                      height: ' . $panel->height . ';
                                                border-width: ' . $panel->border_size . 'px;
                                                border-style: solid;
                                                border-color: ' . $panel->border_color . ';
                                               border-radius: ' . $panel->border_radius . 'px;
                                       -webkit-border-radius: ' . $panel->border_radius . 'px;
                                          -moz-border-radius: ' . $panel->border_radius . 'px;
                                              -moz-transform: rotate(' . $panel->angle . 'deg);
                                           -webkit-transform: rotate(' . $panel->angle . 'deg);
                                                -o-transform: rotate(' . $panel->angle . 'deg);
                                               -ms-transform: rotate(' . $panel->angle . 'deg);
                                                   transform: rotate(' . $panel->angle . 'deg);">
                                </div>';
                }
            }

            if($visualDisplay->images != null && count($visualDisplay->images) > 0) {
                foreach($visualDisplay->images as $image) {
                    if(!file_exists(DOCROOT . '/files/' . $visualDisplay->map_id . '/vdImages/' . $image->name)) continue;

                    $result .= '<div style="position: absolute;
                                                 top: ' . $image->y . 'px;
                                                left: ' . $image->x . 'px;
                                               width: ' . $image->width . 'px;
                                              height: ' . $image->height . 'px;
                                             z-index: ' . $image->z_index . ';
                                      -moz-transform: rotate(' . $image->angle . 'deg);
                                   -webkit-transform: rotate(' . $image->angle . 'deg);
                                        -o-transform: rotate(' . $image->angle . 'deg);
                                       -ms-transform: rotate(' . $image->angle . 'deg);
                                           transform: rotate(' . $image->angle . 'deg);
                                ">
                                    <img style="width: 100%" src="' . URL::base() . 'files/' . $visualDisplay->map_id . '/vdImages/' . $image->name . '" />
                                </div>';
                }
            }

            if($visualDisplay->counters != null && count($visualDisplay->counters) > 0) {
                foreach($visualDisplay->counters as $counter) {
                    $thisCounter = $counter->counter->start_value;

                    if($traceCountersValues != null) {
                        $s = strpos($traceCountersValues, '[CID=' . $counter->counter_id . ',') + 1;
                        $tmp = substr($traceCountersValues, $s, strlen($traceCountersValues));
                        $e = strpos($tmp, ']') + 1;
                        $tmp = substr($tmp, 0, $e - 1);
                        $tmp = str_replace('CID=' . $counter->counter_id . ',V=', '', $tmp);
                        $thisCounter = $tmp;
                    }

                    $labelFont = explode('%#%', $counter->label_font_style);
                    $valueFont = explode('%#%', $counter->value_font_style);

                    $result .= '<div style="position: absolute;
                                                 top: ' . $counter->label_y . ';
                                                left: ' . $counter->label_x . ';
                                             z-index: ' . $counter->label_z_index . ';
                                      -moz-transform: rotate(' . $counter->label_angle . 'deg);
                                   -webkit-transform: rotate(' . $counter->label_angle . 'deg);
                                        -o-transform: rotate(' . $counter->label_angle . 'deg);
                                       -ms-transform: rotate(' . $counter->label_angle . 'deg);
                                           transform: rotate(' . $counter->label_angle . 'deg);
                                         font-family: \'' . $labelFont[0] . '\';
                                           font-size: ' . $labelFont[1] . 'px;
                                         font-weight: \'' . $labelFont[2] . '\';
                                               color: ' . $labelFont[3] . ';
                                          font-style: \'' . $labelFont[4] . '\';
                                     text-decoration: \'' . $labelFont[5] . '\';
                                ">' . $counter->label_text . '</div>
                                <div style="position: absolute;
                                                 top: ' . $counter->value_y . ';
                                                left: ' . $counter->value_x . ';
                                             z-index: ' . $counter->value_z_index . ';
                                      -moz-transform: rotate(' . $counter->value_angle . 'deg);
                                   -webkit-transform: rotate(' . $counter->value_angle . 'deg);
                                        -o-transform: rotate(' . $counter->value_angle . 'deg);
                                       -ms-transform: rotate(' . $counter->value_angle . 'deg);
                                           transform: rotate(' . $counter->value_angle . 'deg);
                                         font-family: \'' .  $valueFont[0] . '\';
                                           font-size: ' . $valueFont[1] . 'px;
                                         font-weight: \'' . $valueFont[2] . '\';
                                               color: ' . $valueFont[3] . ';
                                          font-style: \'' . $valueFont[4] . '\';
                                     text-decoration: \'' . $valueFont[5] . '\';
                                ">' . $thisCounter . '</div>';
                }
            }

            $result .= '</div>';
        }

        return $result;
    }

    private static function getCounterHTML($mapId, $id){
        $counterValue = Model::factory('labyrinth')->getCounterValueByID($mapId, $id);
        return $counterValue;
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
        if (stristr($_SERVER['HTTP_USER_AGENT'], 'Firefox'))
            return 'firefox';
        elseif (stristr($_SERVER['HTTP_USER_AGENT'], 'Chrome'))
            return 'chrome';
        elseif (stristr($_SERVER['HTTP_USER_AGENT'], 'Safari'))
            return 'safari';
        elseif (stristr($_SERVER['HTTP_USER_AGENT'], 'Opera'))
            return 'opera';
        elseif (stristr($_SERVER['HTTP_USER_AGENT'], 'MSIE 6.0'))
            return 'ie6';
        elseif (stristr($_SERVER['HTTP_USER_AGENT'], 'MSIE 7.0'))
            return 'ie7';
        elseif (stristr($_SERVER['HTTP_USER_AGENT'], 'MSIE 8.0'))
            return 'ie8';
    }

    private function generateReviewLinks($traces) {
        /*
         *                             {
                                "startDate":"2011,12,10",
                                "endDate":"2011,12,11",
                                "headline":"Headline Goes Here",
                            }
         *
         */
        if ($traces != NULL and count($traces) > 0) {
            //$result = array();
            $result = '<ul class="links navigation">';
            $i = 0;
            foreach ($traces as $trace) {
                //$result[] = array("startDate"=>date("Y,m,d,H,i,s", $trace->date_stamp),"endDate"=>date("Y,m,d,H,i,s"),"headline"=>"<a href='".URL::base() . 'renderLabyrinth/review/' . $trace->node->map_id . '/' . $trace->node->id."'>".$trace->node->title."</a>",);
//                if($i>0)
//                    $result[$i-1]["endDate"] = date("Y,m,d,H,i,s", $trace->date_stamp);
//                $i++;

                $result .= '<li><a href=' . URL::base() . 'renderLabyrinth/review/' . $trace->node->map_id . '/' . $trace->node->id . '>' . $trace->node->title . '</a></li>';
            }

            //$result =json_encode($result);
            $result .= '</ul>';
            return $result;
        }

        return '';
    }


    private function prepareUndoLinks($sessionId,$mapId) {
        $traces = DB_ORM::model('user_sessiontrace')->getUniqueTraceBySessions(array($sessionId));

        //Delete root node and current node
        array_shift($traces);
       // array_pop($traces);

        $html = '<ul class="links navigation">';

        if (count($traces) > 0) {
            foreach($traces as &$trace){
                $trace['node_name'] = DB_ORM::model('map_node')->getNodeName($trace['node_id']);
                $html .= '<li><i><font color="#777799">' .$trace['node_name'] . '</font></i><a href=' . URL::base() . 'renderLabyrinth/undo/' . $mapId . '/' .$trace['node_id'] .'>'  . ' [undo]' . '</a></li>';
            }
        }
        $html .= '</ul>';

        return $html;
    }
}

