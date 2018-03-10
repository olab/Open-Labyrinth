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
                $xmlBuffer .= '<body><![CDATA['.strip_tags($node->text, '<p>').']]></body>'.chr(13).chr(10);
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

        $fileName = DOCROOT.'/export/visual_editor/mapview_'.$mapId.'.xml';
        $f = fopen($fileName, 'w') or die("can't create file");
        fwrite($f, $xmlBuffer);
        fclose($f);
    }

    public function generateJSON($mapId) {
        $result   = '';
        $nodes    = DB_ORM::model('map_node')->getNodesByMap($mapId);
        $links    = DB_ORM::model('map_node_link')->getLinksByMap($mapId);
        $sections = DB_ORM::model('map_node_section')->getAllSectionsByMap($mapId);

        if (count($nodes)) {
            $nodesJSON = '';
            foreach ($nodes as $node) {
                $title = base64_encode(str_replace('&#43;', '+', $node->title));
                $text  = base64_encode(str_replace('&#43;', '+', $node->text));
                $info  = base64_encode(str_replace('&#43;', '+', $node->info));
                $annotation = base64_encode(str_replace('&#43;', '+', $node->annotation));

                $counters = '';
                $countersData = DB_ORM::model('map_node_counter')->getNodeCounters($node->id);
                if($countersData != null && isset($countersData) && count($countersData) > 0) {
                    foreach($countersData as $counter) {
                        $counters .= '{id: ' . $counter->counter_id . ', func: "' . $counter->function . '", show: ' . ($counter->display == 1 ? 'true' : 'false') . '}, ';
                    }

                    if(strlen($counters) > 2) {
                        $counters = substr($counters, 0, strlen($counters) - 2);
                        $counters = 'counters: [' . $counters . ']';
                    }
                }

                $nodesJSON .= '{id: ' . $node->id . ', title: "' . $title . '", content: "' . $text . '", support: "' . $info . '", annotation: "' . $annotation . '", isPrivate: "' . $node->is_private . '", showInfo: "' . $node->show_info . '", isExit: "' . ($node->probability ? 'true' : 'false') . '", undo: "' . ($node->undo ? 'true' : 'false') . '", isEnd: "' . ($node->end ? 'true' : 'false') . '", isRoot: "' . (($node->type_id == 1) ? 'true' : 'false') . '", linkStyle: ' . $node->link_style_id . ', nodePriority: ' . $node->priority_id . ', x: ' . ($node->x != null ? $node->x : (230 + rand(230, 300))) . ', y: ' . ($node->y != null ? $node->y : (150 + rand(150, 230))) . ',  color: "' . str_replace('0x', '#', $node->rgb) . '", isNew: "false"' . (strlen($counters) > 2 ? (', ' . $counters) : '') . '}, ';
            }

            if (strlen($nodesJSON) > 2) {
                $nodesJSON = substr($nodesJSON, 0, strlen($nodesJSON) - 2);
                $nodesJSON = 'nodes: [' . $nodesJSON . ']';
                $result = '{' . $nodesJSON . ', ';
            }
        }

        $clearLinks = $this->getClearLinks($links);
        if (count($clearLinks)) {
            $linksJSON = '';
            foreach ($clearLinks as $id => $value) {
                $linksJSON .= '{id: ' . $value['link']->id . ', nodeA: ' . $value['link']->node_id_1 . ', nodeB: ' . $value['link']->node_id_2 . ', type: "' . $value['type'] . '", label: "' . base64_encode(str_replace('&#43;', '+', $value['link']->text)) . '", imageId: ' . $value['link']->image_id . ', linkHidden: ' . (int)$value['link']->hidden . '}, ';
            }

            if (strlen($linksJSON) > 2) {
                $linksJSON = substr($linksJSON, 0, strlen($linksJSON) - 2);
                $linksJSON = 'links: [' . $linksJSON . ']';
                $result .= $linksJSON . ', ';
            }
        }

        if(count($sections)) {
            $sectionsJSON = '';
            foreach($sections as $section) {
                $sectionsJSON .= '{ id: '.$section->id.', name: "'.base64_encode(str_replace('&#43;', '+', $section->name)).'", orderBy: "'.$section->orderBy.'"';
                if(count($section->nodes)) {
                    $sectionsJSON .= ', nodes: [';
                    foreach($section->nodes as $sectionNode) {
                        $sectionsJSON .= '{ nodeId: '.$sectionNode->node_id.', order: '.$sectionNode->order.'}, ';
                    }
                    $sectionsJSON  = substr($sectionsJSON, 0, strlen($sectionsJSON) - 2);
                    $sectionsJSON .= ']';
                }
                $sectionsJSON .= '}, ';
            }

            if(strlen($sectionsJSON) > 2) {
                $sectionsJSON  = substr($sectionsJSON, 0, strlen($sectionsJSON) - 2);
                $sectionsJSON  = 'sections: [' . $sectionsJSON . ']';
                $result       .= $sectionsJSON . ', ';
            }
        }

        if (strlen($result) > 2) {
            $result = substr($result, 0, strlen($result) - 2);
            $result .= '};';
            $result = '\'' . $result . '\'';
        }

        return $result;
    }

    private function getClearLinks($links)
    {
        $linkMap = array();
        $result  = array();

        foreach ($links as $link) {
            if ( ! isset($linkMap[$link->node_id_2][$link->node_id_1])) {
                $linkMap[$link->node_id_1][$link->node_id_2]['type'] = 'direct';
                $linkMap[$link->node_id_1][$link->node_id_2]['link'] = $link;
            } else {
                $linkMap[$link->node_id_2][$link->node_id_1]['type'] = 'dual';
                $linkMap[$link->node_id_2][$link->node_id_1]['link'] = $link;
            }
        }

        foreach ($linkMap as $key1 => $l) {
            foreach ($l as $key2 => $v) {
                $result[$v['link']->id]['type'] = $v['type'];
                $result[$v['link']->id]['link'] = $v['link'];
            }
        }

        return $result;
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

    public function updateFromJSON($mapId, $jsonString)
    {
        $obj                = json_decode($jsonString, true);
        $currentNodes       = DB_ORM::model('map_node')->getNodesByMap($mapId);
        $currentLinks       = DB_ORM::model('map_node_link')->getLinksByMap($mapId);
        $currentSections    = DB_ORM::model('map_node_section')->getAllSectionsByMap($mapId);
        $clearLinks         = $this->getClearLinks($currentLinks);
        $currentNodesHash   = $this->createNodesHashTable($currentNodes);
        $currentLinksHash   = $this->createLinksHashTable($currentLinks);
        $nodesUpdate        = array();
        $linksUpdate        = array();
        $newNodesMap        = array();

        $this->createSectionHashTable($currentSections);

        if ($obj == null AND count($currentNodes)) {
            $this->deleteAllNodesWithLinks($currentNodes);
        } else {
            if (isset($obj['nodes']) AND count($obj['nodes'])) {
                $nodesMap = array();
                foreach ($obj['nodes'] as $node) {
                    $nodesMap[$node['id']] = $node['id'];
                    if (isset($node['isNew']) && $node['isNew'] == 'true') {
                        $nodesUpdate['new'][] = $node;
                    } else if (isset($node['id']) && isset($currentNodesHash[$node['id']])) {
                        $nodesUpdate['update'][] = $node;
                    } else if (isset($currentNodesHash[$node['id']])) {
                        $nodesUpdate['delete'][] = $currentNodesHash[$node['id']];
                    }
                }

                foreach ($currentNodesHash as $id => $node) {
                    if ( ! isset($nodesMap[$id])) $nodesUpdate['delete'][] = $node;
                }
            }
        }

        if (isset($nodesUpdate['new'])) {
            $newNodesMap = $this->createNewNodes($mapId, $nodesUpdate['new']);
        }
        if (isset($nodesUpdate['update'])) {
            $this->updateNodes($nodesUpdate['update']);
        }
        if (isset($nodesUpdate['delete'])) {
            $this->deleteNodesWithLinks($nodesUpdate['delete']);
        }

        if ($obj != null AND isset($obj['links']) && count($obj['links'])) {
            $linksMap = array();
            foreach ($obj['links'] as $link) {
                $linksMap[$link['id']] = $link;

                if (isset($newNodesMap[$link['nodeA']])) $link['nodeA'] = $newNodesMap[$link['nodeA']];
                if (isset($newNodesMap[$link['nodeB']])) $link['nodeB'] = $newNodesMap[$link['nodeB']];

                if (isset($currentLinksHash[$link['id']])) $linksUpdate['update'][] = $link;
                else $linksUpdate['new'][] = $link;
            }

            foreach ($clearLinks as $link)
            {
                if ( ! isset($linksMap[$link['link']->id])) {
                    $linksUpdate['delete'][] = $link['link'];
                }
            }
        } else if (count($currentLinks)) {
            foreach ($currentLinks as $link) {
                $link->delete();
            }
        }

        if (isset($linksUpdate['new'])) {
            foreach ($linksUpdate['new'] as $link) {
                $v              = array();
                $v['text']      = urldecode(str_replace('+', '&#43;', base64_decode($link['label'])));
                $v['image_id']  = $link['imageId'];
                $v['linkHidden']  = $link['linkHidden'];

                if ($link['type'] == 'direct') {
                    $v['node_id_1'] = $link['nodeA'];
                    $v['node_id_2'] = $link['nodeB'];

                    DB_ORM::model('map_node_link')->addFullLink($mapId, $v);
                } else if ($link['type'] == 'back') {
                    $v['node_id_1'] = $link['nodeB'];
                    $v['node_id_2'] = $link['nodeA'];

                    DB_ORM::model('map_node_link')->addFullLink($mapId, $v);
                } else if ($link['type'] == 'dual') {
                    $v['node_id_1'] = $link['nodeA'];
                    $v['node_id_2'] = $link['nodeB'];

                    DB_ORM::model('map_node_link')->addFullLink($mapId, $v);

                    $v['node_id_1'] = $link['nodeB'];
                    $v['node_id_2'] = $link['nodeA'];

                    DB_ORM::model('map_node_link')->addFullLink($mapId, $v);
                }
            }
        }

        if (isset($linksUpdate['update'])) {
            foreach ($linksUpdate['update'] as $link) {
                $l = DB_ORM::model('map_node_link', array((int) $link['id']));
                if ($l != null) {
                    $l->text = urldecode(str_replace('+', '&#43;', base64_decode($link['label'])));
                    $l->image_id = $link['imageId'];
                    $l->hidden = $link['linkHidden'];
                    $l->save();

                    if ($link['type'] == 'direct') {
                        DB_ORM::delete('map_node_link')->where('map_id', '=', $mapId, 'AND')->where('node_id_1', '=', $l->node_id_2, 'AND')->where('node_id_2', '=', $l->node_id_1)->execute();

                        if ($l->node_id_1 != (int) $link['nodeA']) {
                            $t = $l->node_id_1;
                            $l->node_id_1 = $l->node_id_2;
                            $l->node_id_2 = $t;
                            $l->save();
                        }
                    } else if ($link['type'] == 'back') {
                        DB_ORM::delete('map_node_link')->where('node_id_1', '=', $l->node_id_2, 'AND')->where('node_id_2', '=', $l->node_id_1)->execute();

                        if ($l->node_id_1 != (int) $link['nodeB']) {
                            $t = $l->node_id_1;
                            $l->node_id_1 = $l->node_id_2;
                            $l->node_id_2 = $t;
                            $l->save();
                        }
                    } else if ($link['type'] == 'dual') {
                        if ($l != null) {
                            $b = DB_ORM::model('map_node_link')->getLinkByNodeIDs($l->node_id_2, $l->node_id_1);
                            if ($b == null) {
                                $v['node_id_1'] = $l->node_id_2;
                                $v['node_id_2'] = $l->node_id_1;
                                $v['text'] = urldecode(str_replace('+', '&#43;', base64_decode($link['label'])));
                                $v['image_id'] = $link['imageId'];
                                $v['hidden'] = $link['linkHidden'];
                                DB_ORM::model('map_node_link')->addFullLink($mapId, $v);
                            } else {
                                $b->text = urldecode(str_replace('+', '&#43;', base64_decode($link['label'])));
                                $b->image_id = $link['imageId'];
                                $b->hidden = $link['linkHidden'];
                                $b->save();
                            }
                        }
                    }
                }
            }
        }

        if (isset($linksUpdate['delete']) AND count($linksUpdate['delete'])) {
            foreach ($linksUpdate['delete'] as $link) {
                DB_ORM::delete('map_node_link')->where('node_id_1', '=', $link->node_id_1, 'AND')->where('node_id_2', '=', $link->node_id_2)->execute();
                DB_ORM::delete('map_node_link')->where('node_id_1', '=', $link->node_id_2, 'AND')->where('node_id_2', '=', $link->node_id_1)->execute();
            }
        }

        if (isset($obj['sections']) AND count($obj['sections'])) {
            $sectionIdMap = array();
            $sectionId    = null;
            foreach ($obj['sections'] as $section) {
                if (isset($section['id']) AND strpos($section['id'], 'n') != FALSE) {
                    $sectionId = DB_ORM::model('map_node_section')->createSection($mapId, array('sectionname' => urldecode(str_replace('+', '&#43;', base64_decode($section['name'])))), $section['orderBy'])->id;
                } else {
                    $sectionId = $section['id'];
                    DB_ORM::model('map_node_section')->updateSectionRow($sectionId, array('sectiontitle' => urldecode(str_replace('+', '&#43;', base64_decode($section['name'])))), $section['orderBy']);
                }

                if (isset($section['nodes']) AND count($section['nodes'])) {
                    $sectionIdMap[$sectionId] = $sectionId;

                    DB_ORM::model('map_node_section_node')->deleteNodesBySection($sectionId);
                    foreach($section['nodes'] as $sNode) {
                        $order = $sNode['order'];
                        $nodeId = $sNode['nodeId'];
                        if (isset($newNodesMap[$nodeId])) {
                            $nodeId = $newNodesMap[$nodeId];
                        }
                        DB_ORM::model('map_node_section_node')->createNode($nodeId, $sectionId, $order);
                    }
                } else if ($sectionId > 0) {
                    DB_ORM::model('map_node_section')->deleteSection($sectionId);
                }
            }

            foreach($currentSections as $s) {
                if( ! isset($sectionIdMap[$s->id])) {
                    DB_ORM::model('map_node_section')->deleteSection($s->id);
                }
            }
        } else {
            foreach ($currentSections as $s) {
                DB_ORM::model('map_node_section')->deleteSection($s->id);
            }
        }

        return true;
    }

    private function createNewNodes($mapId, $nodes)
    {
        $nodeMap = array();
        $model   = DB_ORM::model('map_node');
        foreach ($nodes as $node) {
            $nodeMap[$node['id']] = $model->createNodeFromJSON($mapId, $node);
            $this->updateNodeCountersFromJSON($node, $nodeMap[$node['id']]);
        }
        return $nodeMap;
    }

    private function updateNodes($nodes)
    {
        if ($nodes == null || count($nodes) <= 0) return;
        $mapId = Arr::get($_POST, 'id');
        foreach ($nodes as $node)
        {
            DB_ORM::model('map_node')->updateNodeFromJSON($mapId, $node['id'], $node);
            $this->updateNodeCountersFromJSON($node, $node['id']);
        }
    }

    private function updateNodeCountersFromJSON($node, $nodeId)
    {
        if (isset($node['counters']) && count($node['counters'])) {
            foreach ($node['counters'] as $counter) {
                $nodeCounter = DB_ORM::model('map_node_counter')->getNodeCounter($nodeId, $counter['id']);
                if ($nodeCounter != null) {
                    DB_ORM::model('map_node_counter')->updateNodeCounter($nodeId, $counter['id'], $counter['func'], $counter['show'] == 'true');
                } else {
                    DB_ORM::model('map_node_counter')->addNodeCounter($nodeId, $counter['id'], $counter['func'], $counter['show'] == 'true');
                }
            }
        }
    }

    private function createNodesHashTable($nodes) {
        $result = array();
        foreach ($nodes as $node) {
            if ( ! isset($result[$node->id])) {
                $result[$node->id] = $node;
            }
        }
        return $result;
    }

    private function createLinksHashTable($links) {
        if ($links == null || count($links) <= 0)
            return array();

        $result = array();
        foreach ($links as $link) {
            if (!isset($result[$link->id]))
                $result[$link->id] = $link;
        }

        return $result;
    }

    private function createSectionHashTable($sections) {
        if ($sections == null || count($sections) <= 0)
            return array();

        $result = array();
        foreach ($sections as $section) {
            if (!isset($result[$section->id]))
                $result[$section->id] = $section;
        }

        return $result;
    }

    private function deleteNodesWithLinks($nodes) {
        $mapId = Arr::get($_POST, 'id', NULL);
        $model = DB_ORM::model('map_node');
        foreach ($nodes as $node) {
            $reference = DB_ORM::model('map_node_reference')->getByElementType($node->id, 'INFO');
            if($reference == NULL){
                DB_ORM::model('map_node_reference')->deleteByNodeId($mapId, $node->id);
                $model->deleteNode($node->id);
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

