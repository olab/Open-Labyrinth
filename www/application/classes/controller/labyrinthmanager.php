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

class Controller_LabyrinthManager extends Controller_Base {
    
    public function action_index() {
        Request::initial()->redirect(URL::base());
    }
    
    public function action_createLabyrinth() {
        $this->templateData['center'] = View::factory('labyrinth/createLabyrinth');
        unset($this->templateData['right']);
        $this->template->set('templateData', $this->templateData);
    }
    
    public function action_addManual() {
        $this->templateData['types'] = DB_ORM::model('map_type')->getAllTypes();
        $this->templateData['skins'] = DB_ORM::model('map_skin')->getAllSkins();
        $this->templateData['securities'] = DB_ORM::model('map_security')->getAllSecurities();
        $this->templateData['sections'] = DB_ORM::model('map_section')->getAllSections();
        
        $addManualView = View::factory('labyrinth/addManual');
        $addManualView->set('templateData', $this->templateData);
        
        $leftView = View::factory('labyrinth/labyrinthEditorMenu');
        $leftView->set('templateData', $this->templateData);
        
        $this->templateData['center'] = $addManualView;
        $this->templateData['left'] = $leftView;
        unset($this->templateData['right']);
        $this->template->set('templateData', $this->templateData);
    }
    
    public function action_addNewMap() {
        if($_POST) {
            $_POST['author'] = Auth::instance()->get_user()->id;
            $map = DB_ORM::model('map')->createMap($_POST);
            Request::initial()->redirect(URL::base().'labyrinthManager/editMap/'.$map->id);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }
    
    public function action_editMap() {
        $mapId = $this->request->param('id', NULL);
        if($mapId) {
            $map = DB_ORM::model('map', array((int)$mapId));
            if($map != NULL) {
                if(Auth::instance()->get_user()->type->name != 'superuser') {
                    if(Auth::instance()->get_user()->id != $map->author_id) {
                        if(!DB_ORM::model('map_user')->checkUser($map->authors, Auth::instance()->get_user()->id)) {
                            Request::initial()->redirect(URL::base());
                        }
                    }
                }
            }
            $this->templateData['map'] = $map;
            
            $editorView = View::factory('labyrinth/editor');
            $editorView->set('templateData', $this->templateData);
			
			$leftView = View::factory('labyrinth/editorLeftMenu');
			$leftView->set('templateData', $this->templateData);
            
            $this->templateData['left'] = $leftView;
            $this->templateData['center'] = $editorView;
            unset($this->templateData['right']);
            $this->template->set('templateData', $this->templateData);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }
    
    public function action_disableMap() {
        $mapId = $this->request->param('id', NULL);
        if($mapId) {
            DB_ORM::model('map')->disableMap($mapId);
        }
        
        Request::initial()->redirect(URL::base());
    }
    
    public function action_global() {
        $mapId = $this->request->param('id', NULL);
        if($mapId) {
            $this->templateData['map'] = DB_ORM::model('map', array($mapId));
            $this->templateData['types'] = DB_ORM::model('map_type')->getAllTypes();
            $this->templateData['skins'] = DB_ORM::model('map_skin')->getAllSkins();
            $this->templateData['securities'] = DB_ORM::model('map_security')->getAllSecurities();
            $this->templateData['sections'] = DB_ORM::model('map_section')->getAllSections();
            $this->templateData['contributors'] = DB_ORM::model('map_contributor')->getAllContributors($mapId);
            $this->templateData['contributor_roles'] = DB_ORM::model('map_contributor_role')->getAllRoles();
            
            $regUsers = DB_ORM::model('map_user')->getAllUsers($mapId);
            if($regUsers != NULL) {
                $this->templateData['regUsers'] = $regUsers;
            }
            
            $leftView = View::factory('labyrinth/labyrinthEditorMenu');
            $leftView->set('templateData', $this->templateData);
            
            $globalView = View::factory('labyrinth/global');
            $globalView->set('templateData', $this->templateData);
            
            $this->templateData['left'] = $leftView;
            $this->templateData['center'] = $globalView;
            unset($this->templateData['right']);
            $this->template->set('templateData', $this->templateData);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }
    
    public function action_addContributor() {
        $mapId = $this->request->param('id', NULL);
        if($mapId != NULL) {
            DB_ORM::model('map_contributor')->createContributor($mapId);
            Request::initial()->redirect(URL::base().'labyrinthManager/global/'.$mapId);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }
    
    public function action_deleteContributor() {
        $mapId = $this->request->param('id', NULL);
        $contId = $this->request->param('id2', NULL);
        if($mapId != NULL and $contId != NULL) {
            DB_ORM::model('map_contributor', array((int)$contId))->delete();
            Request::initial()->redirect(URL::base().'labyrinthManager/global/'.$mapId);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }
    
    public function action_saveGlobal() {
        $mapId = $this->request->param('id', NULL);
        if($_POST) {
            if($mapId != NULL) {
                DB_ORM::model('map')->updateMap($mapId, $_POST);
                DB_ORM::model('map_contributor')->updateContributors($mapId, $_POST);
                Request::initial()->redirect(URL::base().'labyrinthManager/global/'.$mapId);
            } else {
                Request::initial()->redirect(URL::base().'labyrinthManager/editMap/'.$mapId);
            }
        } else {
            Request::initial()->redirect(URL::base().'labyrinthManager/editMap/'.$mapId);
        }
    }
    
    public function action_editKeys() {
        $mapId = $this->request->param('id', NULL);
        $countOfKeys = $this->request->param('id2', NULL);
        if($mapId != NULL) {
            $this->templateData['map'] = DB_ORM::model('map', array((int)$mapId));
            if($countOfKeys != NULL) {
                $this->templateData['keyCount'] = (int)$countOfKeys + 1;
            } else {
                $this->templateData['keyCount'] = 1;
            }
            
            $currentKeys = DB_ORM::model('map_key')->getKeysByMap($mapId);
            if($currentKeys != NULL) {
                $this->templateData['currentKeys'] = $currentKeys;
            }
            
            $leftView = View::factory('labyrinth/labyrinthEditorMenu');
            $leftView->set('templateData', $this->templateData);
            
            $keysView = View::factory('labyrinth/keys');
            $keysView->set('templateData', $this->templateData);
            
            $this->templateData['left'] = $leftView;
            $this->templateData['center'] = $keysView;
            unset($this->templateData['right']);
            $this->template->set('templateData', $this->templateData);
        } else {
            Request::initial()->redirect(URL::base().'labyrinthManager');
        }
    }
    
    public function action_addKey() {
        $mapId = $this->request->param('id', NULL);
        $countOfKeys = $this->request->param('id2', NULL);
        if($mapId != NULL) {
            if($countOfKeys != NULL) {
                Request::initial()->redirect(URL::base().'labyrinthManager/editKeys/'.$mapId.'/'.$countOfKeys);
            } else {
                Request::initial()->redirect(URL::base().'labyrinthManager/editKeys/'.$mapId.'/1');
            }
        } else {
            Request::initial()->redirect(URL::base().'labyrinthManager');
        }
    }
    
    public function action_saveKeys() {
        $mapId = $this->request->param('id', NULL);
        $countOfAddKeys = $this->request->param('id2', NULL);
        if($_POST && $mapId != NULL) {
            DB_ORM::model('map_key')->updateKeys($mapId, $_POST);
            if($countOfAddKeys != NULL) {
                DB_ORM::model('map_key')->createKeys($mapId, $_POST, (int)$countOfAddKeys-1);
            }
            Request::initial()->redirect(URL::base().'labyrinthManager/editKeys/'.$mapId);
        } else {
            Request::initial()->redirect(URL::base().'labyrinthManager/editMap/'.$mapId);
        }
    }
    
    public function action_deleteKey() {
        $mapId = $this->request->param('id', NULL);
        $keyId = $this->request->param('id2', NULL);
        if($keyId != NULL) {
            DB_ORM::model('map_key', array($keyId))->delete();
            Request::initial()->redirect(URL::base().'labyrinthManager/editKeys/'.$mapId);
        } else {
            Request::initial()->redirect(URL::base().'labyrinthManager/editKeys/'.$mapId);
        }
    }
    
    public function action_showDevNotes() {
        $mapId = $this->request->param('id', NULL);
        if($mapId != NULL) {
            $this->template = View::factory('labyrinth/note');
            $this->template->set('map', DB_ORM::model('map', array((int)$mapId)));
        } else {
            Request::initial()->redirect(URL::base().'labyrinthManager/editMap/'.$mapId);
        }
    }
    
    public function action_updateDevNodes() {
        $mapId = $this->request->param('id', NULL);
        if($_POST and $mapId != NULL) {
            $map = DB_ORM::model('map', array((int)$mapId));
            if($map != NULL) {
                $map->dev_notes = Arr::get($_POST, 'devnotes', $map->dev_notes);
                $map->save();
            }
            Request::initial()->redirect(URL::base().'labyrinthManager/showDevNotes/'.$mapId);
        } else {
            Request::initial()->redirect(URL::base().'labyrinthManager/editMap/'.$mapId);
        }
    }
}

?>
