<?php defined('SYSPATH') or die('No direct script access.');

class Model_Labyrinth extends Model {
    public function execute($nodeId) {
        $result = array();
        
        $result['userId'] = 0;
        if(Auth::instance()->logged_in()) {
            $result['userId'] = Auth::instance()->get_user()->id;
        }
        $node = DB_ORM::model('map_node', array((int)$nodeId));
        
        if($node) {
            $result['node'] = $node;
            $result['map'] = DB_ORM::model('map', array((int)$node->map_id));
            if($node->kfp) {
                $matches = $this->getMatch($nodeId);
            }
            
            $result['editor'] = FALSE;
            if($this->checkUser($node->map_id)) {
                $result['editor'] = TRUE;
            }
            
            $result['node_title'] = $node->title;
            $result['node_text'] = $node->text;
            
            $sessionId = NULL;
            if($node->type->name == 'root') {
                $sessionId = DB_ORM::model('user_session')->createSession($result['userId'], $node->map_id, time(), getenv('REMOTE_ADDR'));
                Session::instance()->set('session_id', $sessionId);
                setcookie('OL', $sessionId);
            } else {
                $sessionId = Session::instance()->get('session_id', NULL);
                if($sessionId == NULL) {
                    $sessionId = $_COOKIE['OL'];
                }
            }
            
            $result['previewNodeId'] = DB_ORM::model('user_sessionTrace')->getTopTraceBySessionId($sessionId);
            
            DB_ORM::model('user_sessionTrace')->createTrace($sessionId, $result['userId'], $node->map_id, $node->id);
            $result['node_links'] = $this->generateLinks($result['node']);
            $result['sections'] = DB_ORM::model('map_node_section')->getSectionsByMapId($node->map_id);
            
            $conditional = $this->conditional($sessionId, $node);
            if($conditional != NULL) {
                $result['node_text'] = $conditional['message'];
                $result['node_links'] = $conditional['linker']; 
            }
            
            if(substr($result['node_text'], 0, 3) != '<p>') {
                $result['node_text'] = '<p>'.$result['node_text'].'</p>';
            }
            
            $c = $this->counters($sessionId, $node);
            if($c != NULL) {
                $result['counters'] = $c['counterString'];
                $result['redirect'] = $c['redirect'];
            } else {
                $result['counters'] = '';
                $result['redirect'] = NULL;
            }
        }
        
        return $result;
    }
    
    private function checkUser($mapId) {
        if(Auth::instance()->logged_in()) {
            if(DB_ORM::model('map_user')->checkUserById($mapId, Auth::instance()->get_user()->id)) {
                return TRUE;
            }
            
            return FALSE;
        }
        
        return FALSE;
    }
    
    private function getMatch($nodeId) {
        return NULL;
    }
    
    private function generateLinks($node) {
        if(count($node->links) > 0) {
            $result = array();
            foreach($node->links as $link) {
                switch($node->link_type->name) {
                    case 'ordered':
                        if(isset($result[$link->order])) {
                            $result[$link->order + 1] = $link;
                        } else {
                            $result[$link->order] = $link;
                        }
                        break;
                    case 'random order':
                        $randomIndex = rand();
                        if(isset($result[$randomIndex])) {
                            $result[$randomIndex + 1] = $link;
                        } else {
                            $result[$randomIndex] = $link;
                        }
                        break;
                    case 'random select one *':
                        $randomIndex = rand()*($link->probability == 0 ? 1 : $link->probability);
                        if(isset($result[$randomIndex])) {
                            $result[$randomIndex + 1] = $link;
                        } else {
                            $result[$randomIndex] = $link;
                        }
                        break;
                    default:
                        $result[] = $link;
                        break;
                }
            }
            
            return $this->clearArray($result);
        }
        
        return NULL;
    }
    
    private function clearArray($array) {
        if(count($array) > 0) {
            $result = array();
            for($i = 0, $j = 0; $i < count($array);$j++) {
                if(isset($array[$j])) {
                    $result[] = $array[$j];
                    $i++;
                }
            }
            
            return $result;
        }
            
        return NULL;
    }
    
    private function conditional($sessionId, $node) { 
        if($node != NULL and $node->conditional != '') {
            $mode = 'o';
            if(strstr($node->conditional, 'and')) {
                $mode = 'a';
            }
            
            $nodes = array();
            $conditional = $node->conditional;
            while(strlen($conditional) > 0) {
                if($conditional[0] == '[') {
                    for($i = 1; $i < strlen($conditional); $i++) {
                        if($conditional[$i] == ']') {
                            $id = substr($conditional, 1, $i-1);
                            if(is_numeric($id)) {
                                $nodes[] = (int)$id;
                            }
                            break;
                        }
                    }
                }
                
                $conditional = substr($conditional, 1, strlen($conditional));
            }
            
            $count = DB_ORM::model('user_sessionTrace')->getCountTracks($sessionId, $nodes);
            
            $message = '<p>Sorry but you haven\'t yet explored all the required options ...</p>';
            if($node->conditional_message != '') {
                $message = $node->conditional_message;
            }
            
            if($mode == 'a') {
                if($count >= count($nodes)) {
                    return array('message' => $message, 'linker' => '<p><a href="javascript:history.back()">back</a></p>');
                }
            } else if($mode == 'o') {
                if($count >= 1) {
                    return array('message' => $message, 'linker' => '<p><a href="javascript:history.back()">back</a></p>');
                }
            }
        }
        
        return NULL;
    }
    
    private function counters($sessionId, $node) {
        if($node != NULL) {
            $counters = DB_ORM::model('map_counter')->getCountersByMap($node->map_id);
            if(count($counters) > 0) {
                $updateCounter = '';
                $counterString = '';
                $rootNode = DB_ORM::model('map_node')->getRootNodeByMap($node->map_id);
                $redirect = NULL;
                foreach($counters as $counter) {
                    $currentCountersState = '';
                    if($rootNode != NULL) {
                        $currentCountersState = DB_ORM::model('user_sessionTrace')->getCounterByIDs($sessionId, $rootNode->map_id, $rootNode->id);
                    }
                    
                    $label = $counter->name;
                    if($counter->icon_id != 0) {
                        $label = '<img src="'.URL::base().$counter->icon->path.'">';
                    }
                    
                    $thisCounter = 0;
                    if($node->type->name == 'root') {
                        $thisCounter = $counter->start_value;
                    } else if($currentCountersState != '') {
                        $s = strpos($currentCountersState, '[CID='.$counter->id.',')+1;
                        $tmp = substr($currentCountersState, $s, strlen($currentCountersState));
                        $e = strpos($tmp, ']')+1;
                        $tmp = substr($tmp, 0, $e-1);
                        $tmp = str_replace('CID='.$counter->id.',V=', '', $tmp);
                        if(is_numeric($tmp)) {
                            $thisCounter = (int)$tmp;
                        }
                        
                        $n = DB_ORM::model('map_node_counter')->getNodeCounter($node->id, $counter->id);
                        if($n != NULL and $n->function != '') {
                            if($n->function[0] == '=') {
                                $thisCounter = (int)substr($n->function, 1, strlen($n->function));
                            } else if($n->function[0] == '-') {
                                $thisCounter -= (int)substr($n->function, 1, strlen($n->function));
                            } else if($n->function[0] == '+') {
                                $thisCounter += (int)substr($n->function, 1, strlen($n->function));
                            }
                        } 
                    }   
                    
                    $counterFunction = '';
                    if(count($node->counters) > 0) {
                        foreach($node->counters as $nodeCounter) {
                            if($counter->id == $nodeCounter->counter->id) {
                                $counterFunction = $nodeCounter->function;
                                break;
                            }
                        }
                    }
                    
                    if($counterFunction != '') {
                        if($counterFunction[0] == '=') {
                            $thisCounter = (int)substr($counterFunction, 1, strlen($counterFunction));
                        } else if($counterFunction[0] == '-') {
                            $thisCounter -= (int)substr($counterFunction, 1, strlen($counterFunction));
                        } else if($counterFunction[0] == '+') {
                            $thisCounter += (int)substr($counterFunction, 1, strlen($counterFunction));
                        }
                    }
                    
                    if($counterFunction != '') {
                        $func = '<sup>['.$counterFunction.']</sup>';
                    } else {
                        $func = '<sup>[no]</sup>';
                    }
                    
                    if($counter->visible) {
                        $popup = '<a href="#" onclick="window.open("'.URL::base().'renderLabyrinth/", "Counter", '."'toolbar=no, directories=no, location=no, status=no, menubar=no, resizable=yes, scrollbars=yes, width=400, height=350);".' return false;">';
                        $counterString .= '<p>'.$popup.$label.'</a>('.$thisCounter.') '.$func.'</p>';
                    }
                    
                    $rules = DB_ORM::model('map_counter_rule')->getRulesByCounterId($counter->id);
                    
                    if($rules != NULL and count($rules) > 0) {
                        foreach($rules as $rule) {
                            $resultExp = FALSE;
                            
                            switch($rule->relation->value) {
                                case 'eq':
                                    if($thisCounter == $rule->value)
                                        $resultExp = TRUE;
                                    break;
                                case 'neq':
                                    
                                    if($thisCounter != $rule->value)
                                        $resultExp = TRUE;
                                    break;
                                case 'leq':
                                    if($thisCounter <= $rule->value)
                                        $resultExp = TRUE;
                                    break;
                                case 'lt':
                                    if($thisCounter < $rule->value)
                                        $resultExp = TRUE;
                                    break;
                                case 'geq':
                                    if($thisCounter >= $rule->value)
                                        $resultExp = TRUE;
                                    break;
                                case 'gt':
                                    if($thisCounter > $rule->value)
                                        $resultExp = TRUE;
                                    break;
                            }
                            
                            if($resultExp == TRUE) {
                                if($rule->function == 'redir') {
                                    $redirect = $rule->redirect_node_id;
                                }
                            }
                        }
                    }
                    
                    $updateCounter .= '[CID='.$counter->id.',V='.$thisCounter.']';
                }
                
                DB_ORM::model('user_sessionTrace')->updateCounter($sessionId, $rootNode->map_id, $rootNode->id, $updateCounter);
                
                return array('counterString' => $counterString, 'redirect' => $redirect);
            }
            
            return '';
        }
        
        return '';
    }
}

?>

