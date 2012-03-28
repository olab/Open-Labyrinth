<?php

defined('SYSPATH') or die('No direct script access.');

class Controller_RenderLabyrinth extends Controller_Template {

    public $template = 'home'; // Default

    public function action_index() {
        $mapId = $this->request->param('id', NULL);
        if ($mapId != NULL) {
            $rootNode = DB_ORM::model('map_node')->getRootNodeByMap((int) $mapId);

            if ($rootNode != NULL) {
                $data = Model::factory('labyrinth')->execute($rootNode->id);
                if($data['redirect'] != NULL) {
                    Request::initial()->redirect(URL::base().'renderLabyrinth/go/'.$mapId.'/'.$data['redirect']);
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
                    
                    $data['node_text'] = $this->parseText($data['node_text']);

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
                    if($data['redirect'] != NULL) {
                        Request::initial()->redirect(URL::base().'renderLabyrinth/go/'.$mapId.'/'.$data['redirect']);
                    }
                    if ($data['node']->link_style->name == 'type in text') {
                        $result = $this->generateLinks($data['node'], $data['node_links']);
                        $data['links'] = $result['display'];
                        $data['alinkfil'] = substr($result['alinkfil'], 0, strlen($result['alinkfil']) - 2);
                        $data['alinknod'] = substr($result['alinknod'], 0, strlen($result['alinknod']) - 2);
                    } else {
                        $data['links'] = $this->generateLinks($data['node'], $data['node_links']);
                    }

                    $data['node_text'] = $this->parseText($data['node_text']);

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
    
    public function action_info() {
        $nodeId = $this->request->param('id', NULL);
        if ($nodeId != NULL) {
            $node = DB_ORM::model('map_node', array((int)$nodeId));
            
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
        
        if($counterName != NULL) {
            $popupView = View::factory('labyrinth/counter/popup');
            $popupView->set('name', $counterName);
            $popupView->set('currentValue', $counterValue);
            $popupView->set('description', $counterDesc);
            $popupView->set('icon', $counterLabel);
            
            $this->template = $popupView;
        }
    }

    private function generateLinks($node, $links) {
        if (is_array($links) and count($links) > 0) {
            $result = '';
            foreach ($links as $link) {
                $title = $link->node_2->title;
                if($link->text != '') {
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
                        if(isset($result['alinkfil'])) {
                            $result['alinkfil'] .= '"'.strtolower($title).'", ';
                            $result['alinknod'] .= $link->node_id_2.', ';
                        } else {
                            $result['alinkfil'] = '"'.strtolower($title).'", ';
                            $result['alinknod'] = $link->node_id_2.', ';
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
                    $result['display'] = '<form action="'.URL::base().'renderLabyrinth/go/'.$node->map_id.'" name="form2"><input name="filler" type="text" size="25" value="" onKeyUp="javascript:Populate(this.form);"><input type="hidden" name="id" value="'.$node->id.'" /><input type="submit" name="submit" value="go" /></form>';
                    break;
            }

            if ($node->end and $node->link_style->name == 'type in text') {
                $result['display'] .= '<p><a href="#">end session and view report</a></p>';
            } else if($node->end) {
                $result .= '<p><a href="#">end session and view report</a></p>';
            }

            return $result;
        } else {
            if($links != '') {
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

    private function parseText($text) {
        $result = $text;

        $codes = array('MR', 'FL', 'CHAT', 'DAM', 'AV', 'VPD');

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
                                        $replaceString = $this->getImageHTML($id);
                                        break;
                                    case 'AV':
                                        $replaceString = $this->getAvatarHTML($id);
                                        break;
                                }

                                $repRegExp = '/[\[' . $code . ':' . $id . '\]]+/';
                                $result = str_replace('[[' . $code . ':' . $id . ']]', $replaceString, $result);
                            }
                        }
                    }
                }
            }
        }

        return $result;
    }

    private function getImageHTML($id) {
        $image = DB_ORM::model('map_element', array((int) $id));
        if ($image) {
            return '<img src="' . URL::base() . $image->path . '">';
        }

        return '';
    }

    private function getAvatarHTML($id) {
        $avatar = DB_ORM::model('map_avatar', array((int) $id));
        if ($avatar) {
            return '<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0" width="300" height="300" id="avatar_'.$avatar->id.'" align="middle">
                        <param name="allowScriptAccess" value="sameDomain">
                        <param name="movie" value="'.URL::base().'documents/avatar.swf">
                        <param name="quality" value="high">
                        <param name="flashVars" value="fSkin='.$avatar->skin_1.'&fSkinOut='.$avatar->skin_2.'&fBkd='.$avatar->bkd.'&fCloth='.$avatar->cloth.'&fNose='.$avatar->nose.'&fHair='.$avatar->hair.'&fAccessory1='.$avatar->accessory_1.'&fAccessory2='.$avatar->accessory_2.'&fAccessory3='.$avatar->accessory_3.'&fEnvironment='.$avatar->environment.'&fOutfit='.$avatar->outfit.'&fMouth='.$avatar->mouth.'&fSex='.$avatar->sex.'&fBubble='.$avatar->bubble.'&fBubbleText='.$avatar->bubble_text.'&fAge='.$avatar->age.'&fEyes='.$avatar->eyes.'&fWeather='.$avatar->weather.'&fHairColor='.$avatar->hair_color.'">
                        <embed src="'.URL::base().'documents/avatar.swf" flashvars="fSkin='.$avatar->skin_1.'&fSkinOut='.$avatar->skin_2.'&fBkd='.$avatar->bkd.'&fCloth='.$avatar->cloth.'&fNose='.$avatar->nose.'&fHair='.$avatar->hair.'&fAccessory1='.$avatar->accessory_1.'&fAccessory2='.$avatar->accessory_2.'&fAccessory3='.$avatar->accessory_3.'&fEnvironment='.$avatar->environment.'&fOutfit='.$avatar->outfit.'&fMouth='.$avatar->mouth.'&fSex='.$avatar->sex.'&fBubble='.$avatar->bubble.'&fBubbleText='.$avatar->bubble_text.'&fAge='.$avatar->age.'&fEyes='.$avatar->eyes.'&fWeather='.$avatar->weather.'&fHairColor='.$avatar->hair_color.'" quality="high" bgcolor="#ffffff" width="300" height="300" name="avatar_'.$avatar->id.'" align="middle" allowscriptaccess="sameDomain" allowfullscreen="false" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer">
                      </object>';
        }

        return '';
    }

}

?>
