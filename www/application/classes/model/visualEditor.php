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

class Model_VisualEditor extends Model {
    public function generateXML($mapId) {
        $xmlBuffer = '<?xml version='.chr(34).'1.0'.chr(34).'?>'.chr(13).chr(10);
        $xmlBuffer .= '<nodes>'.chr(13).chr(10);
        $xmlBuffer .= '<mapid>'.$mapId.'</mapid>'.chr(13).chr(10);
        
        $mnX = 60;
        $mnY = 40;
   
        $nodes = DB_ORM::model('map_node')->getNodesByMap($mapId);
        
        if($nodes != NULL and count($nodes) > 0) {
            foreach($nodes as $node) {
                if($node->x < 1 or $node->x == 0) {
                    $mnX += 110;
                    if($mnX > 900) {
                        $mnX = 60;
                        $mnY += 90;
                    }
                } else {
                    $mnX = $node->x;
                    $mnY = $node->y;
                }

                $xmlBuffer .= '<node>'.chr(13).chr(10);
                $xmlBuffer .= '<ID>'.$node->id.'</ID>'.chr(13).chr(10);
                $xmlBuffer .= '<title><![CDATA['.$node->title.']]></title>'.chr(13).chr(10);
                $xmlBuffer .= '<body><![CDATA['.$node->text.']]></body>'.chr(13).chr(10);
                $xmlBuffer .= '<x>'.$mnX.'</x>'.chr(13).chr(10);
                $xmlBuffer .= '<y>'.$mnY.'</y>'.chr(13).chr(10);
                $xmlBuffer .= '<rgb>'.$node->rgb.'</rgb>'.chr(13).chr(10);
                $xmlBuffer .= '</node>'.chr(13).chr(10);
            }
        }
        
        $links = DB_ORM::model('map_node_link')->getLinksByMap($mapId);
        
        if($links != NULL and count($links) > 0) {
            foreach($links as $link) {
                $xmlBuffer .=  '<link>'.chr(13).chr(10);
                $xmlBuffer .=  '<nodeA>'.$link->node_id_1.'</nodeA>'.chr(13).chr(10);
                $xmlBuffer .=  '<nodeB>'.$link->node_id_2.'</nodeB>'.chr(13).chr(10);
                $xmlBuffer .=  '</link>'.chr(13).chr(10);
            }
        }
        
        $xmlBuffer .= '</nodes>'.chr(13).chr(10);
        
        $fileName = $_SERVER['DOCUMENT_ROOT'].'/export/visual_editor/mapview_'.$mapId.'.xml';
        $f = fopen($fileName, 'w') or die("can't create file");
        fwrite($f, $xmlBuffer);
        fclose($f);
    }
    
    public function update($mapId, $emap, $enode, $elink) {
        $newNodeId = NULL;
        if($emap != '') {    
            $emap = str_replace('<m', 'm', $emap);
            $emap = str_replace('}>', '}---', $emap);

            $enStart = strpos($emap, '--nodes');
            $enEnd = strpos($emap, '--links');
            $emapNodes = substr($emap, 0, $enEnd);
            $emapNodes = substr($emapNodes, $enStart, strlen($emapNodes));
            $emapNodes = str_replace('-', '', $emapNodes);
            $emapNodes = str_replace('nodes', '', $emapNodes);
            
            
            while(strpos($emapNodes, '|||)')) {
                $fp = strpos($emapNodes, '|||)')+1;
                $fpHold = substr($emapNodes, 0, $fp);
                $emapNodes = str_replace($fpHold, '', $emapNodes);
                
                $fpHold = str_replace('(|||', '', $fpHold);
                $fpHold = str_replace('|||)', '', $fpHold);
                
                $fp2 = strpos($fpHold, ',')+1;
                $nID = substr($fpHold, 0, $fp2);
                $fpHold = str_replace($nID, '', $fpHold);
                
                $fp3 = strpos($fpHold, ',')+1;
                $nX = substr($fpHold, 0, $fp3);
                $fpHold = str_replace($nX, '', $fpHold);
                
                $fp4 = strpos($fpHold, ',')+1;
                $nY = substr($fpHold, 0, $fp4);
                $fpHold = str_replace($nY, '', $fpHold);
                
                $fp5 = strpos($fpHold, ',')+1;
                $rgb = substr($fpHold, 0, $fp5);
                $fpHold = str_replace($rgb, '', $fpHold);
                
                $fp6 = strpos($fpHold, '}}}},{{{{{')+5;
                $nTi = substr($fpHold, 0, $fp6);
                $fpHold = str_replace($nTi, '', $fpHold);
                $nTx = $fpHold;
                
                $nID = str_replace(',', '', $nID);
                $nID = str_replace('||)', '', $nID);
                
                $nX = str_replace(',', '', $nX);
                $nY = str_replace(',', '', $nY);
                
                $rgb = str_replace(',', '', $rgb);
                
                $nTi = str_replace('{{{{', '', $nTi);
                $nTi = str_replace('}}}},', '', $nTi);
                
                $nTx = str_replace('{{{{{', '', $nTx);
                $nTx = str_replace('}}}}}|', '', $nTx);
                
                $nTi = $this->cleaner($nTi);
                $nTx = $this->cleaner($nTx);
                
                if(is_numeric($nID)) {
                    $n = DB_ORM::model('map_node')->getNodeById((int)$nID);
                    if($n == NULL and $mapId != NULL) {
                        $builder = DB_ORM::insert('map_node')
                                ->column('map_id', $mapId)
                                ->column('title', $nTi)
                                ->column('text', $nTx)
                                ->column('x', $nX)
                                ->column('y', $nY)
                                ->column('rgb', $rgb)
                                ->column('type_id', 2)
                                ->column('link_style_id', 1)
                                ->column('priority_id', 1)
                                ->column('undo', FALSE)
                                ->column('end', FALSE);
                        $newNodeId = $builder->execute();
                    } else {
                        if(is_numeric($nX) and is_numeric($nY)) {
                            $n->x = (int)$nX;
                            $n->y = (int)$nY;
                            $n->title = $nTi;
                            $n->text = $nTx;
                            $n->rgb = $rgb;
                            $n->save();
                        }
                    }
                }
            }
            
            $elStart = strpos($emap, '--links')+1;
            $emapLinks = substr($emap, $elStart, strlen($emap));
            $emapLinks = str_replace('-', '', $emapLinks);
            $emapLinks = str_replace('links', '', $emapLinks);
            
            while(strpos($emapLinks, '}')) {
                $fp = strpos($emapLinks, '}')+1;
                $fpHold = substr($emapLinks, 0, $fp);
                $emapLinks = str_replace($fpHold, '', $emapLinks);
                
                $fpHold = str_replace('{', '', $fpHold);
                $fpHold = str_replace('}', '', $fpHold);
                
                $fp2 = strpos($fpHold, ',')+1;
                $idA = substr($fpHold, 0, $fp2);
                $fpHold = str_replace($idA, '', $fpHold);
                $idB = $fpHold;
                
                if($newNodeId != NULL and $idA == $nID) {
                    $idA = $newNodeId;
                }
                
                if($newNodeId != NULL and $idB == $nID) {
                    $idB = $newNodeId;
                }
                
                $idA = str_replace(',', '', $idA);
                $idB = str_replace(',', '', $idB);
                
                if(is_numeric($idA) and is_numeric($idB)) {
                    $link = DB_ORM::model('map_node_link')->getLinkByNodeIDs((int)$idA, (int)$idB);
                    if($link == NULL) {
                        $newLink = DB_ORM::model('map_node_link');
                        $newLink->map_id = $mapId;
                        $newLink->node_id_1 = (int)$idA;
                        $newLink->node_id_2 = (int)$idB;
                        
                        $newLink->save();
                    }
                }
            }
        }
        
        if($enode != '') {
            $enode = str_replace('<editnode[', '', $enode);
            $enode = str_replace(']>', '', $enode);
            
            $enAction = substr($enode, 0, 1);
            $enID = str_replace($enAction.'-', '', $enode);
            
            switch($enAction) {
                case 'r':
                    $rootNode = DB_ORM::model('map_node')->getRootNodeByMap($mapId);
                    if($rootNode != NULL) {
                        DB_ORM::model('map_node')->setRootNode($mapId, $enID);
                    }
                    break;
                case 'd':
                    DB_ORM::model('map_node_link')->deleteLinks((int)$enID);
                    DB_ORM::model('map_node', array((int)$enID))->delete();
                    break;    
            }
        }
        
        if($elink != '') {
            $elink = str_replace('<editlink', '', $elink);
            $elink = str_replace('>', '', $elink);
            
            $i1 = strpos($elink, ']')+1;
            $node1 = substr($elink, 0, $i1);
            $elink = str_replace($node1, '', $elink);
            
            $i2 = strpos($elink, '}')+1;
            $node2 = substr($elink, 0, $i2);
            $laction = str_replace($node2, '', $elink);
            
            $node1 = str_replace('[', '', $node1);
            $node1 = str_replace(']', '', $node1);
            $node2 = str_replace('{', '', $node2);
            $node2 = str_replace('}', '', $node2);
            $laction = str_replace('(', '', $laction);
            $laction = str_replace(')', '', $laction);
            
            DB_ORM::model('map_node_link')->deleteLinkByNodeIds((int)$node1, (int)$node2);
            
            switch($laction) {
                case 'r':
                    $newLink = DB_ORM::model('map_node_link');
                    $newLink->map_id = $mapId;
                    $newLink->node_id_1 = (int)$node1;
                    $newLink->node_id_2 = (int)$node2;
                    $newLink->save();
                    break;
                case 'l':
                    $newLink = DB_ORM::model('map_node_link');
                    $newLink->map_id = $mapId;
                    $newLink->node_id_1 = (int)$node2;
                    $newLink->node_id_2 = (int)$node1;
                    $newLink->save();
                    break;
                case 'b':
                    $newLink = DB_ORM::model('map_node_link');
                    $newLink->map_id = $mapId;
                    $newLink->node_id_1 = (int)$node1;
                    $newLink->node_id_2 = (int)$node2;
                    $newLink->save();
                    
                    $newLink2 = DB_ORM::model('map_node_link');
                    $newLink2->map_id = $mapId;
                    $newLink2->node_id_1 = (int)$node2;
                    $newLink2->node_id_2 = (int)$node1;
                    $newLink2->save();
                    
                    break;
            }
        }
    }
    
    private function cleaner($str) {
        $str = ltrim(rtrim($str));
        if($str != '') {
            $str = str_replace("'", '&#039;', $str);
            $str = str_replace(chr(34), '&#034;', $str);
        }
        
        return $str;
    }
}

?>
