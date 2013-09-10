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

    public function before() {
        parent::before();

        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('My Labyrinths'))->set_url(URL::base() . 'authoredLabyrinth'));
    }

    public function action_index() {
        Request::initial()->redirect(URL::base());
    }

    public function action_createLabyrinth() {
        $this->templateData['center'] = View::factory('labyrinth/createLabyrinth');
        unset($this->templateData['right']);
        $this->template->set('templateData', $this->templateData);
    }

    public function action_addManual() {
        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Create Manually'))->set_url(URL::base() . 'labyrinthManager/addManual'));

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

    public function action_caseWizard() {
        $stepId = $this->request->param('id', '1');

        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Create Step-by-Step'))->set_url(URL::base() . 'labyrinthManager/caseWizard'));
        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Step :step', array(':step' => $stepId))));

        $action = $this->request->param('id2', 'none');
        $receivedMapId = $this->request->param('id3', 'none');
        $typeStep3 = null;
        $createSkin = false;
        switch ($action) {
            case 'editNode':
                $typeStep3 = 'editNode';
                $nodeId = $this->request->param('id4', NULL);
                if ($nodeId != NULL) {
                    $this->templateData['nodeId'] = $nodeId;
                    $editMode = $this->request->param('id5', NULL);
                    if ($editMode != NULL) {
                        $this->templateData['editMode'] = $editMode;
                    } else {
                        $this->templateData['editMode'] = 'w';
                    }

                    $this->templateData['node'] = DB_ORM::model('map_node', array((int) $nodeId));
                    $this->templateData['map'] = DB_ORM::model('map', array((int) $this->templateData['node']->map_id));
                    $this->templateData['linkStyles'] = DB_ORM::model('map_node_link_style')->getAllLinkStyles();
                    $this->templateData['priorities'] = DB_ORM::model('map_node_priority')->getAllPriorities();
                    $this->templateData['counters'] = DB_ORM::model('map_counter')->getCountersByMap((int) $this->templateData['node']->map_id);

                    $editNodeView = View::factory('labyrinth/casewizard/editNode');
                    $editNodeView->set('templateData', $this->templateData);
                    $this->templateData['nodeData'] = $editNodeView;
                }

                $this->templateData['nodes'] = DB_ORM::model('map_node')->getNodesByMap($receivedMapId);
                break;

            case 'labyrinthType':
                $labyrinthType = $_POST['labyrinthType'];
                $session = Session::instance();
                DB_ORM::model('map')->updateType($receivedMapId, $labyrinthType);
                $skipTypes = array(7, 8, 10);
                if(!in_array($labyrinthType, $skipTypes))
                    Request::initial()->redirect(URL::base() . 'labyrinthManager/caseWizard/3/' . $receivedMapId);
                else
                    Request::initial()->redirect(URL::base() . 'labyrinthManager/caseWizard/4/' . $receivedMapId);
                exit;
                break;

            case 'addNewLabyrinth':
                if (isset($_POST) && !empty($_POST)) {
                    $session = Session::instance();
                    $id = 0;
                    $_POST['type'] = $session->get('labyrinthType');
                    $_POST['author'] = Auth::instance()->get_user()->id;
                    if($receivedMapId != null && $receivedMapId > 0) {
                        DB_ORM::model('map')->updateMap($receivedMapId, $_POST);
                        $id = $receivedMapId;
                    } else {
                        $map = DB_ORM::model('map')->createMap($_POST, false);
                        $id = $map->id;
                    }
                    Request::initial()->redirect(URL::base() . 'labyrinthManager/caseWizard/2/' . $id);
                } else {
                    Request::initial()->redirect(URL::base());
                }
                exit;
                break;
            case 'updateVisualEditor':
                $mapId = $this->request->param('id3', NULL);
                $this->auto_render = false;
                if ($mapId) {
                    $mapId = Arr::get($_POST, 'id', NULL);
                    $data = Arr::get($_POST, 'data', NULL);

                    Model::factory('visualEditor')->updateFromJSON($mapId, $data);
                    $this->templateData['map'] = DB_ORM::model('map', array((int) $mapId));
                }
                echo Model::factory('visualEditor')->generateJSON($mapId);
                exit;
                break;
            case 'createLinear':
                $mapId = $this->request->param('id3', NULL);
                DB_ORM::model('map')->createLinearMap($mapId, $_POST);
                Request::initial()->redirect(URL::base() . 'labyrinthManager/caseWizard/4/' . $mapId);
                break;
            case 'createBranched':
                $mapId = $this->request->param('id3', NULL);
                DB_ORM::model('map')->createBranchedMap($mapId, $_POST);
                Request::initial()->redirect(URL::base() . 'labyrinthManager/caseWizard/4/' . $mapId);
                break;
            case 'updateNode':
                $nodeId = $this->request->param('id3', NULL);
                if ($_POST and $nodeId != NULL) {
                    $node = DB_ORM::model('map_node')->updateNode($nodeId, $_POST);
                    if ($node != NULL) {
                        DB_ORM::model('map_node_counter')->updateNodeCounterByNode($node->id, $node->map_id, $_POST);
                        Request::initial()->redirect(URL::base() . 'labyrinthManager/caseWizard/4/editNode/' . $node->map_id . '/' . $node->id);
                    } else {
                        Request::initial()->redirect(URL::base());
                    }
                } else {
                    Request::initial()->redirect(URL::base());
                }
                exit;
                break;

            case 'addFile':
                $typeStep3 = 'addFile';
                if ($receivedMapId != NULL) {
                    $this->templateData['map'] = DB_ORM::model('map', array((int) $receivedMapId));

                    $this->templateData['files'] = DB_ORM::model('map_element')->getAllFilesByMap((int) $receivedMapId);
                    $fileInfo = DB_ORM::model('map_element')->getFilesSize($this->templateData['files']);

                    $this->templateData['files_size'] = DB_ORM::model('map_element')->sizeFormat($fileInfo['size']);
                    $this->templateData['files_count'] = $fileInfo['count'];

                    $fileView = View::factory('labyrinth/casewizard/file/view');
                    $fileView->set('templateData', $this->templateData);

                    $this->templateData['content'] = $fileView;
                }
                break;

            case 'uploadFile':
                $mapId = $receivedMapId;
                if ($_FILES and $mapId != NULL) {
                    DB_ORM::model('map_element')->uploadFile($mapId, $_FILES);
                    Request::initial()->redirect(URL::base() . 'labyrinthManager/caseWizard/4/addFile/' . $mapId);
                } else {
                    Request::initial()->redirect(URL::base());
                }
                break;

            case 'deleteFile':
                $mapId = $receivedMapId;
                $fileId = $this->request->param('id4', NULL);
                if ($mapId != NULL and $fileId != NULL) {
                    DB_ORM::model('map_element')->deleteFile($fileId);
                    Request::initial()->redirect(URL::base() . 'labyrinthManager/caseWizard/4/addFile/' . $mapId);
                } else {
                    Request::initial()->redirect(URL::base());
                }
                break;

            case 'editFile':
                $mapId = $receivedMapId;
                $fileId = $this->request->param('id4', NULL);
                if ($mapId != NULL and $fileId != NULL) {
                    $this->templateData['map'] = DB_ORM::model('map', array((int) $mapId));
                    $this->templateData['file'] = DB_ORM::model('map_element', array((int) $fileId));

                    $fileView = View::factory('labyrinth/casewizard/file/edit');
                    $fileView->set('templateData', $this->templateData);

                    $this->templateData['content'] = $fileView;
                } else {
                    Request::initial()->redirect(URL::base());
                }
                break;

            case 'updateFile':
                $mapId = $receivedMapId;
                $fileId = $this->request->param('id4', NULL);
                if ($_POST and $mapId != NULL and $fileId != NULL) {
                    DB_ORM::model('map_element')->updateFile($fileId, $_POST);
                    Request::initial()->redirect(URL::base() . 'labyrinthManager/caseWizard/4/addFile/' . $mapId);
                } else {
                    Request::initial()->redirect(URL::base());
                }
                break;

            case 'addQuestion':
                $mapId = $receivedMapId;
                if ($mapId) {
                    $this->templateData['map'] = DB_ORM::model('map', array((int) $mapId));
                    $this->templateData['questions'] = DB_ORM::model('map_question')->getQuestionsByMap((int) $mapId);
                    $this->templateData['question_types'] = DB_ORM::model('map_question_type')->getAllTypes();

                    $questionView = View::factory('labyrinth/casewizard/question/view');
                    $questionView->set('templateData', $this->templateData);

                    $this->templateData['content'] = $questionView;
                } else {
                    Request::initial()->redirect(URL::base());
                }
                break;

            case 'addNewQuestion':
                $mapId = $receivedMapId;
                $templateType = $this->request->param('id4', NULL);

                if ($mapId != NULL and $templateType != NULL) {
                    $type = DB_ORM::model('map_question_type', array((int) $templateType));

                    if ($type) {
                        $this->templateData['map'] = DB_ORM::model('map', array((int) $mapId));
                        $this->templateData['questionType'] = $templateType;
                        $this->templateData['args'] = $type->template_args;
                        $this->templateData['counters'] = DB_ORM::model('map_counter')->getCountersByMap((int) $mapId);


                        $editView = View::factory('labyrinth/casewizard/question/' . $type->template_name);
                        $editView->set('templateData', $this->templateData);

                        $this->templateData['content'] = $editView;
                    }
                } else {
                    Request::initial()->redirect(URL::base());
                }
                break;

            case 'editQuestion':
                $mapId = $receivedMapId;
                $templateType = $this->request->param('id4', NULL);
                $questionId = $this->request->param('id5', NULL);

                if ($mapId != NULL and $templateType != NULL and $questionId != NULL) {
                    $type = DB_ORM::model('map_question_type', array((int) $templateType));

                    if ($type) {
                        $this->templateData['map'] = DB_ORM::model('map', array((int) $mapId));
                        $this->templateData['questionType'] = $templateType;
                        $this->templateData['args'] = $type->template_args;
                        $this->templateData['counters'] = DB_ORM::model('map_counter')->getCountersByMap((int) $mapId);
                        $this->templateData['question'] = DB_ORM::model('map_question', array((int) $questionId));


                        $editView = View::factory('labyrinth/casewizard/question/' . $type->template_name);
                        $editView->set('templateData', $this->templateData);

                        $this->templateData['content'] = $editView;
                    }
                } else {
                    Request::initial()->redirect(URL::base());
                }
                break;

            case 'updateQuestion':
                $mapId = $receivedMapId;
                $templateType = $this->request->param('id4', NULL);
                $questionId = $this->request->param('id5', NULL);

                if ($_POST and $mapId != NULL and $templateType != NULL and $questionId != NULL) {
                    $type = DB_ORM::model('map_question_type', array((int) $templateType));

                    if ($type) {
                        DB_ORM::model('map_question')->updateQuestion($questionId, $type, $_POST);
                    }

                    Request::initial()->redirect(URL::base() . 'labyrinthManager/caseWizard/4/addQuestion/' . $mapId);
                } else {
                    Request::initial()->redirect(URL::base());
                }
                break;

            case 'saveNewQuestion':
                $mapId = $receivedMapId;
                $templateType = $this->request->param('id4', NULL);

                if ($_POST and $mapId != NULL and $templateType != NULL) {
                    $type = DB_ORM::model('map_question_type', array((int) $templateType));

                    if ($type) {
                        var_dump($_POST);
                        DB_ORM::model('map_question')->addQuestion($mapId, $type, $_POST);
                    }

                    Request::initial()->redirect(URL::base() . 'labyrinthManager/caseWizard/4/addQuestion/' . $mapId);
                } else {
                    Request::initial()->redirect(URL::base());
                }
                break;

            case 'deleteQuestion':
                $mapId = $receivedMapId;
                $questionId = $this->request->param('id4', NULL);

                if ($mapId != NULL and $questionId != NULL) {
                    DB_ORM::model('map_question', array((int) $questionId))->delete();
                    DB_ORM::model('map_question_response')->deleteByQuestion($questionId);

                    Request::initial()->redirect(URL::base() . 'labyrinthManager/caseWizard/4/addQuestion/' . $mapId);
                } else {
                    Request::initial()->redirect(URL::base());
                }
                break;

            case 'addAvatar':
                $mapId = $receivedMapId;
                if ($mapId) {
                    $this->templateData['map'] = DB_ORM::model('map', array((int) $mapId));
                    $this->templateData['avatars'] = DB_ORM::model('map_avatar')->getAvatarsByMap((int) $mapId);

                    $avatarView = View::factory('labyrinth/casewizard/avatar/view');
                    $avatarView->set('templateData', $this->templateData);

                    $this->templateData['content'] = $avatarView;
                } else {
                    Request::initial()->redirect(URL::base());
                }
                break;

            case 'addNewAvatar':
                $mapId = $receivedMapId;
                if ($mapId) {
                    $avatarId = DB_ORM::model('map_avatar')->addAvatar($mapId);
                    Request::initial()->redirect(URL::base() . 'labyrinthManager/caseWizard/4/editAvatar/' . $mapId . '/' . $avatarId);
                } else {
                    Request::initial()->redirect(URL::base());
                }
                break;

            case 'editAvatar':
                $mapId = $receivedMapId;
                $avatarId = $this->request->param('id4', NULL);
                if ($mapId != NULL and $avatarId != NULL) {
                    $this->templateData['map'] = DB_ORM::model('map', array((int) $mapId));
                    $this->templateData['avatar'] = DB_ORM::model('map_avatar', array((int) $avatarId));

                    $editAvatarView = View::factory('labyrinth/casewizard/avatar/edit');
                    $editAvatarView->set('templateData', $this->templateData);

                    $this->templateData['content'] = $editAvatarView;
                } else {
                    Request::initial()->redirect(URL::base());
                }
                break;

            case 'deleteAvatar':
                $mapId = $receivedMapId;
                $avatarId = $this->request->param('id4', NULL);
                if ($mapId != NULL and $avatarId != NULL) {
                    $upload_dir = DOCROOT . '/avatars/';
                    $avatarImage = DB_ORM::model('map_avatar')->getAvatarImage($avatarId);
                    if (!empty($avatarImage)) {
                        @unlink($upload_dir . $avatarImage);
                    }
                    DB_ORM::model('map_avatar', array((int) $avatarId))->delete();
                    Request::initial()->redirect(URL::base() . 'labyrinthManager/caseWizard/4/addAvatar/' . $mapId);
                } else {
                    Request::initial()->redirect(URL::base());
                }
                break;

            case 'updateAvatar':
                $mapId = $receivedMapId;
                $avatarId = $this->request->param('id4', NULL);
                if ($_POST and $mapId != NULL and $avatarId != NULL) {
                    $upload_dir = DOCROOT . '/avatars/';
                    $avatarImage = DB_ORM::model('map_avatar')->getAvatarImage($avatarId);
                    if (!empty($avatarImage)) {
                        @unlink($upload_dir . $avatarImage);
                    }
                    $img = $_POST['image_data'];
                    $img = str_replace('data:image/png;base64,', '', $img);
                    $img = str_replace(' ', '+', $img);
                    $data = base64_decode($img);
                    $file = uniqid() . '.png';
                    file_put_contents($upload_dir . $file, $data);
                    $_POST['image_data'] = $file;
                    DB_ORM::model('map_avatar')->updateAvatar($avatarId, $_POST);
                    if ($_POST['save_exit_value'] == 0) {
                        Request::initial()->redirect(URL::base() . 'labyrinthManager/caseWizard/4/editAvatar/' . $mapId . '/' . $avatarId);
                    } else {
                        Request::initial()->redirect(URL::base() . 'labyrinthManager/caseWizard/4/addAvatar/' . $mapId);
                    }
                } else {
                    Request::initial()->redirect(URL::base());
                }
                break;

            case 'duplicateAvatar':
                $mapId = $receivedMapId;
                $avatarId = $this->request->param('id4', NULL);
                if ($mapId != NULL and $avatarId != NULL) {
                    $avatarImage = DB_ORM::model('map_avatar')->getAvatarImage($avatarId);
                    if (!empty($avatarImage)) {
                        $upload_dir = DOCROOT . '/avatars/';
                        $file = uniqid() . '.png';
                        copy($upload_dir . $avatarImage, $upload_dir . $file);
                    } else {
                        $file = NULL;
                    }
                    DB_ORM::model('map_avatar')->duplicateAvatar($avatarId, $file);
                    Request::initial()->redirect(URL::base() . 'labyrinthManager/caseWizard/4/addAvatar/' . $mapId);
                } else {
                    Request::initial()->redirect(URL::base());
                }
                break;

            case 'addCounter':
                $mapId = $receivedMapId;
                if ($mapId) {
                    $this->templateData['map'] = DB_ORM::model('map', array((int) $mapId));
                    $this->templateData['counters'] = DB_ORM::model('map_counter')->getCountersByMap($mapId);

                    $countersView = View::factory('labyrinth/casewizard/counter/view');
                    $countersView->set('templateData', $this->templateData);

                    $this->templateData['content'] = $countersView;
                } else {
                    Request::initial()->redirect(URL::base());
                }
                break;

            case 'addNewCounter':
                $mapId = $receivedMapId;
                if ($mapId) {
                    $this->templateData['map'] = DB_ORM::model('map', array((int) $mapId));
                    $this->templateData['images'] = DB_ORM::model('map_element')->getImagesByMap($mapId);

                    $addCounterView = View::factory('labyrinth/casewizard/counter/add');
                    $addCounterView->set('templateData', $this->templateData);

                    $this->templateData['content'] = $addCounterView;
                } else {
                    Request::initial()->redirect(URL::base());
                }
                break;

            case 'saveNewCounter':
                $mapId = $receivedMapId;
                if ($_POST and $mapId != NULL) {
                    DB_ORM::model('map_counter')->addCounter($mapId, $_POST);
                    Request::initial()->redirect(URL::base() . 'labyrinthManager/caseWizard/4/addCounter/' . $mapId);
                } else {
                    Request::initial()->redirect(URL::base());
                }
                break;

            case 'editCounter':
                $mapId = $receivedMapId;
                $counterId = $this->request->param('id4', NULL);
                if ($mapId != NULL and $counterId != NULL) {
                    $this->templateData['map'] = DB_ORM::model('map', array((int) $mapId));
                    $this->templateData['counter'] = DB_ORM::model('map_counter', array((int) $counterId));
                    $this->templateData['images'] = DB_ORM::model('map_element')->getImagesByMap($mapId);
                    $this->templateData['rules'] = DB_ORM::model('map_counter_rule')->getRulesByCounterId($counterId);
                    $this->templateData['relations'] = DB_ORM::model('map_counter_relation')->getAllRealtions();
                    $this->templateData['nodes'] = DB_ORM::model('map_node')->getNodesByMap($mapId);

                    $editCounterView = View::factory('labyrinth/casewizard/counter/edit');
                    $editCounterView->set('templateData', $this->templateData);

                    $this->templateData['content'] = $editCounterView;
                } else {
                    Request::initial()->redirect(URL::base());
                }
                break;

            case 'updateCounter':
                $mapId = $receivedMapId;
                $counterId = $this->request->param('id4', NULL);
                if ($_POST and $mapId != NULL and $counterId != NULL) {
                    DB_ORM::model('map_counter')->updateCounter($counterId, $_POST);
                    Request::initial()->redirect(URL::base() . 'labyrinthManager/caseWizard/4/editCounter/' . $mapId . '/' . $counterId);
                } else {
                    Request::initial()->redirect(URL::base());
                }
                break;

            case 'deleteRule':
                $mapId = $receivedMapId;
                $counterId = $this->request->param('id4', NULL);
                $ruleId = $this->request->param('id5', NULL);
                $nodeId = $this->request->param('id6', NULL);
                if ($mapId != NULL and $counterId != NULL and $ruleId != NULL and $nodeId != NULL) {
                    DB_ORM::model('map_counter_rule', array((int) $ruleId))->delete();
                    DB_ORM::model('map_node_counter')->deleteNodeCounter($nodeId, $counterId);
                    Request::initial()->redirect(URL::base() . 'labyrinthManager/caseWizard/4/editCounter/' . $mapId . '/' . $counterId);
                } else {
                    Request::initial()->redirect(URL::base());
                }
                break;

            case 'addRule':
                $mapId = $receivedMapId;
                $counterId = $this->request->param('id4', NULL);
                if ($_POST and $mapId != NULL and $counterId != NULL) {
                    DB_ORM::model('map_counter_rule')->addRule($counterId, $_POST);
                    Request::initial()->redirect(URL::base() . 'labyrinthManager/caseWizard/4/editCounter/' . $mapId . '/' . $counterId);
                } else {
                    Request::initial()->redirect(URL::base());
                }
                break;

            case 'deleteCounter':
                $mapId = $receivedMapId;
                $counterId = $this->request->param('id4', NULL);
                if ($mapId != NULL and $counterId != NULL) {
                    DB_ORM::model('map_node_counter')->deleteAllNodeCounterByCounter((int) $counterId);
                    DB_ORM::model('map_counter', array((int) $counterId))->delete();
                    Request::initial()->redirect(URL::base() . 'labyrinthManager/caseWizard/4/addCounter/' . $mapId);
                } else {
                    Request::initial()->redirect(URL::base());
                }
                break;

            case 'grid':
                $mapId = $receivedMapId;
                $counterId = $this->request->param('id4', NULL);
                if ($mapId) {
                    $this->templateData['map'] = DB_ORM::model('map', array((int) $mapId));
                    $this->templateData['nodes'] = DB_ORM::model('map_node')->getNodesByMap((int) $mapId);
                    if ($counterId != NULL) {
                        $this->templateData['counters'][] = DB_ORM::model('map_counter', array((int) $counterId));
                        $this->templateData['oneCounter'] = true;
                    } else {
                        $this->templateData['counters'] = DB_ORM::model('map_counter')->getCountersByMap((int) $mapId);
                    }

                    $gridCounterView = View::factory('labyrinth/casewizard/counter/grid');
                    $gridCounterView->set('templateData', $this->templateData);

                    $this->templateData['content'] = $gridCounterView;
                } else {
                    Request::initial()->redirect(URL::base());
                }
                break;

            case 'updateGrid':
                $mapId = $receivedMapId;
                $counterId = $this->request->param('id4', NULL);
                if ($_POST and $mapId != NULL) {
                    if ($counterId != NULL) {
                        DB_ORM::model('map_node_counter')->updateNodeCounters($_POST, (int) $counterId, (int) $mapId);
                        Request::initial()->redirect(URL::base() . 'labyrinthManager/caseWizard/4/grid/' . $mapId . '/' . $counterId);
                    } else {
                        DB_ORM::model('map_node_counter')->updateNodeCounters($_POST, NULL, (int) $mapId);
                        Request::initial()->redirect(URL::base() . 'labyrinthManager/caseWizard/4/grid/' . $mapId);
                    }
                } else {
                    Request::initial()->redirect(URL::base());
                }
                break;

            case 'previewCounter':
                $mapId = $receivedMapId;
                $counterId = $this->request->param('id4', NULL);
                if ($counterId != NULL) {
                    $this->templateData['map'] = DB_ORM::model('map', array((int) $mapId));
                    $this->templateData['counter'] = DB_ORM::model('map_counter', array((int) $counterId));

                    $previewCounter = View::factory('labyrinth/casewizard/counter/preview');
                    $previewCounter->set('templateData', $this->templateData);

                    $this->templateData['content'] = $previewCounter;
                } else {
                    Request::initial()->redirect(URL::base());
                }
                break;

            case 'uploadSkin':
                $this->templateData['map'] = DB_ORM::model('map', array((int) $receivedMapId));
                $previewUpload = View::factory('labyrinth/casewizard/skineditor/upload');
                $previewUpload->set('templateData', $this->templateData);
                $this->templateData['content'] = $previewUpload;
                break;

            case 'uploadNewSkin':
                $mapId = $receivedMapId;
                if (is_uploaded_file($_FILES['zipSkin']['tmp_name'])) {
                    $ext = substr(($_FILES['zipSkin']['name']), -3);
                    $filename = substr(($_FILES['zipSkin']['name']), 0, strlen($_FILES['zipSkin']['name']) - 4);
                    if ($ext == 'zip') {
                        $zip = new ZipArchive();
                        $result = $zip->open($_FILES['zipSkin']['tmp_name']);
                        if ($result === true) {
                            $zip->extractTo(DOCROOT . '/css/skin/');
                            $zip->close();
                        }

                        $skin = DB_ORM::model('map_skin')->addSkin($filename, $filename);
                        DB_ORM::model('map')->updateMapSkin($mapId, $skin->id);
                    }
                }
                Request::initial()->redirect(URL::base() . 'labyrinthManager/caseWizard/5/listSkins/' . $mapId . '/' . $skin->id);
                break;

            case 'listSkins':
                $this->templateData['map'] = DB_ORM::model('map', array((int) $receivedMapId));
                $this->templateData['skinList'] = DB_ORM::model('map_skin')->getAllSkins();
                $this->templateData['skinId'] = $this->request->param('id4', NULL);
                $previewList = View::factory('labyrinth/casewizard/skineditor/list');
                $previewList->set('templateData', $this->templateData);

                $this->templateData['content'] = $previewList;
                break;

            case 'saveSelectedSkin':
                $mapId = $receivedMapId;
                if ($_POST['skinId'] != 0 and $mapId != NULL) {
                    DB_ORM::model('map')->updateMapSkin($mapId, $_POST['skinId']);
                }
                Request::initial()->redirect(URL::base() . 'labyrinthManager/caseWizard/5/listSkins/' . $mapId . '/' . $_POST['skinId']);
                break;

            case 'createNewSkin':
                $createSkin = true;
                break;

            case 'saveSkin':
                if (isset($_POST['save'])) {
                    $skin_name = $_POST['skin_name'];
                    $centre = $_POST['centre'];
                    $outside = $_POST['outside'];
                    $folder = DOCROOT . 'css/skin/' . $receivedMapId . '_' . $skin_name . '/';
                    $skinName = $skin_name;
                    $skinPath = $receivedMapId . '_' . $skin_name;
                    @mkdir($folder, 0777);

                    $outside_image = $_POST['outside_image'];
                    $centre_image = $_POST['centre_image'];

                    if ($outside_image != null) {
                        @rename(DOCROOT . "scripts/fileupload/php/files/" . $outside_image, $folder . $outside_image);
                    }

                    if ($centre_image != null) {
                        @rename(DOCROOT . "scripts/fileupload/php/files/" . $centre_image, $folder . $centre_image);
                    }

                    $file = @fopen($folder . 'default.css', 'w+');

                    $css = 'body {background-image: url("' . $outside_image . '"); background-color: ' . $outside['b-color'] . '; background-size: ' . $outside['b-size'] . '; background-repeat: ' . $outside['b-repeat'] . '; background-position: ' . $outside['b-position'] . ';} #centre_table {background-image: url("' . $centre_image . '"); background-size: ' . $centre['b-size'] . '; background-repeat: ' . $centre['b-repeat'] . '; background-position: ' . $centre['b-position'] . ';} .centre_td {background-color: ' . $centre['b-color'] . ';}';
                    @fwrite($file, $css);

                    $skin_id = DB_ORM::model('map_skin')->addSkin($skinName, $skinPath);
                    DB_ORM::model('map')->updateMapSkin($receivedMapId, $skin_id->id);
                    Request::initial()->redirect(URL::base() . 'labyrinthManager/caseWizard/5/createSkin/' . $receivedMapId . '/done');
                }
                break;
        }
        switch ($stepId) {
            case '1':
                if($action != NULL){
                    $this->templateData['securities'] = DB_ORM::model('map_security')->getAllSecurities();
                    $this->templateData['sections'] = DB_ORM::model('map_section')->getAllSections();
                }
                if($action > 0) {
                    $this->templateData['map'] = DB_ORM::model('map', array((int) $action));
                }
                break;
            case '2':
                $this->templateData['map'] = DB_ORM::model('map', array((int) $action));
                break;
            case '3':
                $this->templateData['map'] = DB_ORM::model('map', array((int) $action));
                break;
            case '4':
                if ($action != NULL) {
                    $this->templateData['map'] = $action;
                    $this->templateData['mapJSON'] = Model::factory('visualEditor')->generateJSON($action);

                    $this->templateData['counters'] = DB_ORM::model('map_counter')->getCountersByMap((int)$action);
                    $this->templateData['mapModel'] = DB_ORM::model('map', array((int)$action));
                    $this->templateData['linkStyles'] = DB_ORM::model('map_node_link_style')->getAllLinkStyles();
                    $this->templateData['priorities'] = DB_ORM::model('map_node_priority')->getAllPriorities();
                }
                break;
            case '5':
                $this->templateData['type'] = $typeStep3;
                $this->templateData['map'] = $receivedMapId;
                $this->templateData['action'] = $action;
                break;

            case '6':
                $this->templateData['map'] = DB_ORM::model('map', array((int) $receivedMapId));
                $this->templateData['action'] = $action;
                $this->templateData['result'] = $this->request->param('id4', NULL);
                break;
        }
        if (!$createSkin) {
            $caseWizard = View::factory('labyrinth/casewizard/step' . $stepId);
            $caseWizard->set('templateData', $this->templateData);

            $this->templateData['center'] = $caseWizard;
            unset($this->templateData['right']);
            $this->template->set('templateData', $this->templateData);
        } else {
            $this->templateData['action_url'] = URL::base() . 'labyrinthManager/caseWizard/5/saveSkin/' . $receivedMapId;
            $this->template = View::factory('labyrinth/skin/create');
            $this->template->set('templateData', $this->templateData);
        }
    }

    public function action_addNewMap() {
        if (isset($_POST) && !empty($_POST)) {
            $_POST['author'] = Auth::instance()->get_user()->id;
            $map = DB_ORM::model('map')->createMap($_POST);
            Request::initial()->redirect(URL::base() . 'labyrinthManager/editMap/' . $map->id);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

    public function action_editMap() {
        $mapId = (int) $this->request->param('id', 0);
        if ($mapId) {
            Request::initial()->redirect(URL::base() . 'labyrinthManager/global/' . $mapId);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

    public function action_disableMap() {
        $mapId = (int) $this->request->param('id', 0);
        if ($mapId) {
            DB_ORM::model('map')->disableMap($mapId);
        }

        Request::initial()->redirect(URL::base() . 'authoredLabyrinth');
    }

    public function action_global() {
        $mapId = (int) $this->request->param('id', 0);
        if ($mapId) {

            $this->templateData['map'] = DB_ORM::model('map', array($mapId));
            $this->templateData['types'] = DB_ORM::model('map_type')->getAllTypes();
            $this->templateData['skins'] = DB_ORM::model('map_skin')->getAllSkins();
            $this->templateData['securities'] = DB_ORM::model('map_security')->getAllSecurities();
            $this->templateData['sections'] = DB_ORM::model('map_section')->getAllSections();
            $this->templateData['contributors'] = DB_ORM::model('map_contributor')->getAllContributors($mapId);
            $this->templateData['contributor_roles'] = DB_ORM::model('map_contributor_role')->getAllRoles();
            $this->templateData['linkStyles'] = DB_ORM::model('map_node_link_style')->getAllLinkStyles();

            Breadcrumbs::add(Breadcrumb::factory()->set_title($this->templateData['map']->name)->set_url(URL::base() . 'labyrinthManager/global/' . $mapId));
            Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Details'))->set_url(URL::base() . 'labyrinthManager/global/id/' . $mapId));

            $regAuthors = DB_ORM::model('map_user')->getAllAuthors($mapId);
            if ($regAuthors != NULL) {
                $this->templateData['regAuthors'] = $regAuthors;
            }

            $regLearners = DB_ORM::model('map_user')->getAllLearners($mapId);
            if ($regLearners != NULL) {
                $this->templateData['regLearners'] = $regLearners;
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
        $mapId = (int) $this->request->param('id', 0);
        if ($mapId) {
            DB_ORM::model('map_contributor')->createContributor($mapId);
            Request::initial()->redirect(URL::base() . 'labyrinthManager/global/' . $mapId);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

    public function action_deleteContributor() {
        $mapId = (int) $this->request->param('id', 0);
        $contId = (int) $this->request->param('id2', 0);
        if ($mapId && $contId) {
            DB_ORM::model('map_contributor', array($contId))->delete();
            Request::initial()->redirect(URL::base() . 'labyrinthManager/global/' . $mapId);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

    public function action_saveGlobal() {
        $mapId = (int) $this->request->param('id', 0);
        if (isset($_POST) && !empty($_POST)) {
            if ($mapId) {
                if ( isset($_POST['delta_time_seconds']) && isset($_POST['delta_time_minutes']) ) {
                    $delta_time_seconds = $_POST['delta_time_seconds'];
                    $delta_time_minutes = $_POST['delta_time_minutes'];

                    $delta_timing = $delta_time_minutes * 60 + $delta_time_seconds;

                    unset($_POST['delta_time_seconds']);
                    unset($_POST['delta_time_minutes']);
                    $_POST['delta_time'] = $delta_timing;
                }

                if ( isset($_POST['reminder_seconds']) && isset($_POST['reminder_minutes']) ) {
                    $reminder_seconds = $_POST['reminder_seconds'];
                    $reminder_minutes = $_POST['reminder_minutes'];

                    $reminder_time = $reminder_minutes * 60 + $reminder_seconds;

                    unset($_POST['reminder_seconds']);
                    unset($_POST['reminder_minutes']);
                    $_POST['reminder_time'] = $reminder_time;
                }
                DB_ORM::model('map')->updateMap($mapId, $_POST);
                DB_ORM::model('map_contributor')->updateContributors($mapId, $_POST);

                $linkStyleId = Arr::get($_POST, 'linkStyle', null);
                if($linkStyleId != null) {
                    DB_ORM::model('map_node')->setLinkStyle($linkStyleId);
                }

                $map = DB_ORM::model('map', array($mapId));
                if ($map) {
                    $map->dev_notes = Arr::get($_POST, 'devnotes', $map->dev_notes);
                    $map->save();
                }

                Model_Leap_Metadata_Record::updateMetadata("map",$mapId,$_POST);
                Request::initial()->redirect(URL::base().'labyrinthManager/global/'.$mapId);

            } else {
                Request::initial()->redirect(URL::base() . 'labyrinthManager/editMap/' . $mapId);
            }
        } else {
            Request::initial()->redirect(URL::base() . 'labyrinthManager/editMap/' . $mapId);
        }
    }

    public function action_editKeys() {
        $mapId = (int) $this->request->param('id', 0);
        $countOfKeys = (int) $this->request->param('id2', 0);
        if ($mapId) {
            $this->templateData['map'] = DB_ORM::model('map', array($mapId));
            if ($countOfKeys) {
                $this->templateData['keyCount'] = (int) $countOfKeys + 1;
            } else {
                $this->templateData['keyCount'] = 1;
            }

            $currentKeys = DB_ORM::model('map_key')->getKeysByMap($mapId);
            if ($currentKeys != NULL) {
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
            Request::initial()->redirect(URL::base() . 'labyrinthManager');
        }
    }

    public function action_addKey() {
        $mapId = (int) $this->request->param('id', 0);
        $countOfKeys = (int) $this->request->param('id2', 0);
        if ($mapId) {
            if ($countOfKeys) {
                Request::initial()->redirect(URL::base() . 'labyrinthManager/editKeys/' . $mapId . '/' . $countOfKeys);
            } else {
                Request::initial()->redirect(URL::base() . 'labyrinthManager/editKeys/' . $mapId . '/1');
            }
        } else {
            Request::initial()->redirect(URL::base() . 'labyrinthManager');
        }
    }

    public function action_saveKeys() {
        $mapId = (int) $this->request->param('id', 0);
        $countOfAddKeys = (int) $this->request->param('id2', 0);
        if (isset($_POST) && !empty($_POST) && $mapId) {
            DB_ORM::model('map_key')->updateKeys($mapId, $_POST);
            if ($countOfAddKeys) {
                DB_ORM::model('map_key')->createKeys($mapId, $_POST, ($countOfAddKeys - 1));
            }
            Request::initial()->redirect(URL::base() . 'labyrinthManager/editKeys/' . $mapId);
        } else {
            Request::initial()->redirect(URL::base() . 'labyrinthManager/editMap/' . $mapId);
        }
    }

    public function action_deleteKey() {
        $mapId = (int) $this->request->param('id', 0);
        $keyId = (int) $this->request->param('id2', 0);
        if ($keyId) {
            DB_ORM::model('map_key', array($keyId))->delete();
            Request::initial()->redirect(URL::base() . 'labyrinthManager/editKeys/' . $mapId);
        } else {
            Request::initial()->redirect(URL::base() . 'labyrinthManager/editKeys/' . $mapId);
        }
    }
}