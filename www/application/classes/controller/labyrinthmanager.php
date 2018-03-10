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

class Controller_LabyrinthManager extends Controller_Base
{

    public function before()
    {
        $this->templateData['labyrinthSearch'] = 1;

        parent::before();

        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('My Labyrinths'))->set_url(URL::base() . 'authoredLabyrinth'));
    }

    public function action_index()
    {
        Request::initial()->redirect(URL::base());
    }

    public function action_createLabyrinth()
    {
        $this->templateData['center'] = View::factory('labyrinth/createLabyrinth');
        unset($this->templateData['right']);
        $this->template->set('templateData', $this->templateData);
    }

    public function action_addManual()
    {
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

    public function action_caseWizard()
    {
        $stepId = $this->request->param('id', '1');
        $action = $this->request->param('id2', 'none');
        $receivedMapId = $this->request->param('id3', 0);
        $typeStep3 = null;
        $createSkin = false;
        $post = $this->request->post();

        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Create Step-by-Step'))->set_url(URL::base() . 'labyrinthManager/caseWizard'));
        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Step :step', array(':step' => $stepId))));

        switch ($action) {
            case 'editNode':
                $typeStep3 = 'editNode';
                $nodeId = $this->request->param('id4', null);
                if ($nodeId != null) {
                    $this->templateData['nodeId'] = $nodeId;
                    $editMode = $this->request->param('id5', null);
                    if ($editMode != null) {
                        $this->templateData['editMode'] = $editMode;
                    } else {
                        $this->templateData['editMode'] = 'w';
                    }

                    $this->templateData['node'] = DB_ORM::model('map_node', array((int)$nodeId));
                    $this->templateData['map'] = DB_ORM::model('map', array((int)$this->templateData['node']->map_id));
                    $this->templateData['linkStyles'] = DB_ORM::model('map_node_link_style')->getAllLinkStyles();
                    $this->templateData['priorities'] = DB_ORM::model('map_node_priority')->getAllPriorities();
                    $this->templateData['counters'] = DB_ORM::model('map_counter')->getCountersByMap((int)$this->templateData['node']->map_id);

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
                if (!in_array($labyrinthType, $skipTypes)) {
                    Request::initial()->redirect(URL::base() . 'labyrinthManager/caseWizard/3/' . $receivedMapId);
                } else {
                    Request::initial()->redirect(URL::base() . 'labyrinthManager/caseWizard/4/' . $receivedMapId);
                }
                exit;
                break;
            case 'addNewLabyrinth':
                if (isset($_POST) && !empty($_POST)) {
                    $_POST['type'] = Session::instance()->get('labyrinthType');
                    $_POST['author'] = Auth::instance()->get_user()->id;
                    if ($receivedMapId > 0) {
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
                $mapId = $this->request->param('id3', null);
                $this->auto_render = false;
                if ($mapId) {
                    $mapId = Arr::get($_POST, 'id', null);
                    $data = Arr::get($_POST, 'data', null);

                    Model::factory('visualEditor')->updateFromJSON($mapId, $data);
                    $this->templateData['map'] = DB_ORM::model('map', array((int)$mapId));
                }
                echo Model::factory('visualEditor')->generateJSON($mapId);
                exit;
                break;
            case 'createLinear':
                $mapId = $this->request->param('id3', null);
                DB_ORM::model('map')->createLinearMap($mapId, $_POST);
                Request::initial()->redirect(URL::base() . 'labyrinthManager/caseWizard/4/' . $mapId);
                break;
            case 'createBranched':
                $mapId = $this->request->param('id3', null);
                DB_ORM::model('map')->createBranchedMap($mapId, $_POST);
                Request::initial()->redirect(URL::base() . 'labyrinthManager/caseWizard/4/' . $mapId);
                break;
            case 'updateNode':
                $nodeId = $this->request->param('id3', null);
                if ($post AND $nodeId != null) {
                    $node = DB_ORM::model('map_node')->updateNode($nodeId, $post);
                    if ($node != null) {
                        DB_ORM::model('map_node_counter')->updateNodeCounterByNode($node->id, $node->map_id, $post);
                        Request::initial()->redirect(URL::base() . 'labyrinthManager/caseWizard/' . $stepId . '/editNode/' . $node->map_id . '/' . $node->id);
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
                if ($receivedMapId != null) {
                    $this->templateData['map'] = DB_ORM::model('map', array((int)$receivedMapId));
                    $this->templateData['files'] = DB_ORM::model('map_element')->getAllFilesByMap((int)$receivedMapId);
                    $fileInfo = DB_ORM::model('map_element')->getFilesSize($this->templateData['files']);
                    $this->templateData['files_size'] = DB_ORM::model('map_element')->sizeFormat($fileInfo['size']);
                    $this->templateData['files_count'] = $fileInfo['count'];
                    $this->templateData['content'] = View::factory('labyrinth/casewizard/file/view')->set('templateData',
                        $this->templateData);
                }
                break;
            case 'uploadFile':
                $mapId = $receivedMapId;
                if ($_FILES and $mapId != null) {
                    DB_ORM::model('map_element')->uploadFile($mapId, $_FILES);
                    Request::initial()->redirect(URL::base() . 'labyrinthManager/caseWizard/' . $stepId . '/addFile/' . $mapId);
                } else {
                    Request::initial()->redirect(URL::base());
                }
                break;
            case 'deleteFile':
                $mapId = $receivedMapId;
                $fileId = $this->request->param('id4', null);
                if ($mapId != null and $fileId != null) {
                    DB_ORM::model('map_element')->deleteFile($fileId);
                    Request::initial()->redirect(URL::base() . 'labyrinthManager/caseWizard/' . $stepId . '/addFile/' . $mapId);
                } else {
                    Request::initial()->redirect(URL::base());
                }
                break;
            case 'editFile':
                $mapId = $receivedMapId;
                $fileId = $this->request->param('id4', null);
                if ($mapId != null and $fileId != null) {
                    $this->templateData['map'] = DB_ORM::model('map', array((int)$mapId));
                    $this->templateData['file'] = DB_ORM::model('map_element', array((int)$fileId));

                    $fileView = View::factory('labyrinth/casewizard/file/edit');
                    $fileView->set('templateData', $this->templateData);

                    $this->templateData['content'] = $fileView;
                } else {
                    Request::initial()->redirect(URL::base());
                }
                break;
            case 'updateFile':
                $mapId = $receivedMapId;
                $fileId = $this->request->param('id4', null);
                if ($post AND $mapId != null AND $fileId != null) {
                    DB_ORM::model('map_element')->updateFile($fileId, $_POST);
                    Request::initial()->redirect(URL::base() . 'labyrinthManager/caseWizard/' . $stepId . '/addFile/' . $mapId);
                } else {
                    Request::initial()->redirect(URL::base());
                }
                break;
            case 'addQuestion':
                $mapId = $receivedMapId;
                if ($mapId) {
                    $this->templateData['map'] = DB_ORM::model('map', array((int)$mapId));
                    $this->templateData['questions'] = DB_ORM::model('map_question')->getQuestionsByMap((int)$mapId);
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
                $templateType = $this->request->param('id4', null);

                if ($mapId != null AND $templateType != null) {
                    $type = DB_ORM::model('map_question_type', array((int)$templateType));

                    if ($type) {
                        $this->templateData['map'] = DB_ORM::model('map', array((int)$mapId));
                        $this->templateData['questionType'] = $templateType;
                        $this->templateData['args'] = $type->template_args;
                        $this->templateData['counters'] = DB_ORM::model('map_counter')->getCountersByMap((int)$mapId);
                        $this->templateData['content'] = View::factory('labyrinth/casewizard/question/' . $type->template_name)->set('templateData',
                            $this->templateData);
                    }
                } else {
                    Request::initial()->redirect(URL::base());
                }
                break;
            case 'editQuestion':
                $mapId = $receivedMapId;
                $templateType = $this->request->param('id4', null);
                $questionId = $this->request->param('id5', null);

                if ($mapId != null AND $templateType != null AND $questionId != null) {
                    $type = DB_ORM::model('map_question_type', array((int)$templateType));

                    if ($type) {
                        $this->templateData['map'] = DB_ORM::model('map', array((int)$mapId));
                        $this->templateData['questionType'] = $templateType;
                        $this->templateData['args'] = $type->template_args;
                        $this->templateData['counters'] = DB_ORM::model('map_counter')->getCountersByMap((int)$mapId);
                        $this->templateData['question'] = DB_ORM::model('map_question', array((int)$questionId));
                        $this->templateData['content'] = View::factory('labyrinth/casewizard/question/' . $type->template_name)->set('templateData',
                            $this->templateData);
                    }
                } else {
                    Request::initial()->redirect(URL::base());
                }
                break;
            case 'updateQuestion':
                $mapId = $receivedMapId;
                $templateType = $this->request->param('id4', null);
                $questionId = $this->request->param('id5', null);

                if ($_POST and $mapId != null and $templateType != null and $questionId != null) {
                    $type = DB_ORM::model('map_question_type', array((int)$templateType));

                    if ($type) {
                        DB_ORM::model('map_question')->updateQuestion($questionId, $type, $_POST);
                    }

                    Request::initial()->redirect(URL::base() . 'labyrinthManager/caseWizard/' . $stepId . '/addQuestion/' . $mapId);
                } else {
                    Request::initial()->redirect(URL::base());
                }
                break;
            case 'saveNewQuestion':
                $mapId = $receivedMapId;
                $templateType = $this->request->param('id4', null);

                if ($post AND $mapId != null and $templateType != null) {
                    $type = DB_ORM::model('map_question_type', array((int)$templateType));
                    if ($type) {
                        DB_ORM::model('map_question')->addQuestion($mapId, $type, $post);
                    }
                    Request::initial()->redirect(URL::base() . 'labyrinthManager/caseWizard/' . $stepId . '/addQuestion/' . $mapId);
                } else {
                    Request::initial()->redirect(URL::base());
                }
                break;
            case 'deleteQuestion':
                $mapId = $receivedMapId;
                $questionId = $this->request->param('id4', null);

                if ($mapId != null AND $questionId != null) {
                    DB_ORM::model('map_question', array((int)$questionId))->delete();
                    DB_ORM::model('map_question_response')->deleteByQuestion($questionId);
                    Request::initial()->redirect(URL::base() . 'labyrinthManager/caseWizard/' . $stepId . '/addQuestion/' . $mapId);
                } else {
                    Request::initial()->redirect(URL::base());
                }
                break;
            case 'addAvatar':
                $mapId = $receivedMapId;
                if ($mapId) {
                    $this->templateData['map'] = DB_ORM::model('map', array((int)$mapId));
                    $this->templateData['avatars'] = DB_ORM::model('map_avatar')->getAvatarsByMap((int)$mapId);
                    $this->templateData['content'] = View::factory('labyrinth/casewizard/avatar/view')->set('templateData',
                        $this->templateData);
                } else {
                    Request::initial()->redirect(URL::base());
                }
                break;
            case 'addNewAvatar':
                $mapId = $receivedMapId;
                if ($mapId) {
                    $avatarId = DB_ORM::model('map_avatar')->addAvatar($mapId);
                    Request::initial()->redirect(URL::base() . 'labyrinthManager/caseWizard/' . $stepId . '/editAvatar/' . $mapId . '/' . $avatarId);
                } else {
                    Request::initial()->redirect(URL::base());
                }
                break;
            case 'editAvatar':
                $mapId = $receivedMapId;
                $avatarId = $this->request->param('id4', null);
                if ($mapId != null AND $avatarId != null) {
                    $this->templateData['map'] = DB_ORM::model('map', array((int)$mapId));
                    $this->templateData['avatar'] = DB_ORM::model('map_avatar', array((int)$avatarId));
                    $this->templateData['content'] = View::factory('labyrinth/casewizard/avatar/edit')->set('templateData',
                        $this->templateData);;
                } else {
                    Request::initial()->redirect(URL::base());
                }
                break;
            case 'deleteAvatar':
                $mapId = $receivedMapId;
                $avatarId = $this->request->param('id4', null);
                if ($mapId != null AND $avatarId != null) {
                    $upload_dir = DOCROOT . '/avatars/';
                    $avatarImage = DB_ORM::model('map_avatar')->getAvatarImage($avatarId);
                    if (!empty($avatarImage)) {
                        @unlink($upload_dir . $avatarImage);
                    }
                    DB_ORM::model('map_avatar', array((int)$avatarId))->delete();
                    Request::initial()->redirect(URL::base() . 'labyrinthManager/caseWizard/' . $stepId . '/addAvatar/' . $mapId);
                } else {
                    Request::initial()->redirect(URL::base());
                }
                break;
            case 'updateAvatar':
                $mapId = $receivedMapId;
                $avatarId = $this->request->param('id4', null);
                if ($_POST and $mapId != null and $avatarId != null) {
                    $upload_dir = DOCROOT . '/avatars/';
                    $avatarImage = DB_ORM::model('map_avatar')->getAvatarImage($avatarId);
                    if (!empty($avatarImage)) {
                        @unlink($upload_dir . $avatarImage);
                    }
                    $img = Arr::get($post, 'image_data');
                    $img = str_replace('data:image/png;base64,', '', $img);
                    $img = str_replace(' ', '+', $img);
                    $data = base64_decode($img);
                    $file = uniqid() . '.png';
                    file_put_contents($upload_dir . $file, $data);
                    $_POST['image_data'] = $file;
                    DB_ORM::model('map_avatar')->updateAvatar($avatarId, $_POST);
                    if ($_POST['save_exit_value'] == 0) {
                        Request::initial()->redirect(URL::base() . 'labyrinthManager/caseWizard/' . $stepId . '/editAvatar/' . $mapId . '/' . $avatarId);
                    } else {
                        Request::initial()->redirect(URL::base() . 'labyrinthManager/caseWizard/' . $stepId . '/addAvatar/' . $mapId);
                    }
                } else {
                    Request::initial()->redirect(URL::base());
                }
                break;
            case 'duplicateAvatar':
                $mapId = $receivedMapId;
                $avatarId = $this->request->param('id4', null);
                if ($mapId != null AND $avatarId != null) {
                    $avatarImage = DB_ORM::model('map_avatar')->getAvatarImage($avatarId);
                    if (!empty($avatarImage)) {
                        $upload_dir = DOCROOT . '/avatars/';
                        $file = uniqid() . '.png';
                        copy($upload_dir . $avatarImage, $upload_dir . $file);
                    } else {
                        $file = null;
                    }
                    DB_ORM::model('map_avatar')->duplicateAvatar($avatarId, $file);
                    Request::initial()->redirect(URL::base() . 'labyrinthManager/caseWizard/' . $stepId . '/addAvatar/' . $mapId);
                } else {
                    Request::initial()->redirect(URL::base());
                }
                break;
            case 'addCounter':
                $mapId = $receivedMapId;
                if ($mapId) {
                    $this->templateData['map'] = DB_ORM::model('map', array((int)$mapId));
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
                    $this->templateData['map'] = DB_ORM::model('map', array((int)$mapId));
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
                if ($post and $mapId != null) {
                    DB_ORM::model('map_counter')->addCounter($mapId, $post);
                    Request::initial()->redirect(URL::base() . 'labyrinthManager/caseWizard/' . $stepId . '/addCounter/' . $mapId);
                } else {
                    Request::initial()->redirect(URL::base());
                }
                break;
            case 'editCounter':
                $mapId = $receivedMapId;
                $counterId = $this->request->param('id4', null);
                if ($mapId != null and $counterId != null) {
                    $this->templateData['map'] = DB_ORM::model('map', array((int)$mapId));
                    $this->templateData['counter'] = DB_ORM::model('map_counter', array((int)$counterId));
                    $this->templateData['images'] = DB_ORM::model('map_element')->getImagesByMap($mapId);
                    $this->templateData['rules'] = DB_ORM::model('map_counter_rule')->getRulesByCounterId($counterId);
                    $this->templateData['relations'] = DB_ORM::model('map_counter_relation')->getAllRealtions();
                    $this->templateData['nodes'] = DB_ORM::model('map_node')->getNodesByMap($mapId);
                    $this->templateData['content'] = View::factory('labyrinth/casewizard/counter/edit')->set('templateData',
                        $this->templateData);
                } else {
                    Request::initial()->redirect(URL::base());
                }
                break;
            case 'updateCounter':
                $mapId = $receivedMapId;
                $counterId = $this->request->param('id4', null);
                if ($post AND $mapId != null AND $counterId != null) {
                    DB_ORM::model('map_counter')->updateCounter($counterId, $_POST);
                    Request::initial()->redirect(URL::base() . 'labyrinthManager/caseWizard/' . $stepId . '/editCounter/' . $mapId . '/' . $counterId);
                } else {
                    Request::initial()->redirect(URL::base());
                }
                break;
            case 'deleteRule':
                $mapId = $receivedMapId;
                $counterId = $this->request->param('id4', null);
                $ruleId = $this->request->param('id5', null);
                $nodeId = $this->request->param('id6', null);
                if ($mapId != null and $counterId != null and $ruleId != null and $nodeId != null) {
                    DB_ORM::model('map_counter_rule', array((int)$ruleId))->delete();
                    DB_ORM::model('map_node_counter')->deleteNodeCounter($nodeId, $counterId);
                    Request::initial()->redirect(URL::base() . 'labyrinthManager/caseWizard/4/editCounter/' . $mapId . '/' . $counterId);
                } else {
                    Request::initial()->redirect(URL::base());
                }
                break;
            case 'addRule':
                $mapId = $receivedMapId;
                $counterId = $this->request->param('id4', null);
                if ($post AND $mapId != null AND $counterId != null) {
                    DB_ORM::model('map_counter_rule')->addRule($counterId, $_POST);
                    Request::initial()->redirect(URL::base() . 'labyrinthManager/caseWizard/' . $stepId . '/editCounter/' . $mapId . '/' . $counterId);
                } else {
                    Request::initial()->redirect(URL::base());
                }
                break;
            case 'deleteCounter':
                $mapId = $receivedMapId;
                $counterId = $this->request->param('id4', null);
                if ($mapId != null AND $counterId != null) {
                    DB_ORM::model('map_node_counter')->deleteAllNodeCounterByCounter((int)$counterId);
                    DB_ORM::model('map_counter', array((int)$counterId))->delete();
                    Request::initial()->redirect(URL::base() . 'labyrinthManager/caseWizard/' . $stepId . '/addCounter/' . $mapId);
                } else {
                    Request::initial()->redirect(URL::base());
                }
                break;
            case 'grid':
                $mapId = $receivedMapId;
                $counterId = $this->request->param('id4', null);
                if ($mapId) {
                    $this->templateData['map'] = DB_ORM::model('map', array((int)$mapId));
                    $this->templateData['nodes'] = DB_ORM::model('map_node')->getNodesByMap((int)$mapId);
                    if ($counterId != null) {
                        $this->templateData['counters'][] = DB_ORM::model('map_counter', array((int)$counterId));
                        $this->templateData['oneCounter'] = true;
                    } else {
                        $this->templateData['counters'] = DB_ORM::model('map_counter')->getCountersByMap((int)$mapId);
                    }
                    $this->templateData['content'] = View::factory('labyrinth/casewizard/counter/grid')->set('templateData',
                        $this->templateData);
                } else {
                    Request::initial()->redirect(URL::base());
                }
                break;
            case 'updateGrid':
                $mapId = $receivedMapId;
                $counterId = $this->request->param('id4', null);
                $redirect = '';

                if ($post AND $mapId != null) {
                    if ($counterId != null) {
                        DB_ORM::model('map_node_counter')->updateNodeCounters($post, (int)$counterId, (int)$mapId);
                        $redirect = 'labyrinthManager/caseWizard/' . $stepId . '/grid/' . $mapId . '/' . $counterId;
                    } else {
                        DB_ORM::model('map_node_counter')->updateNodeCounters($post, null, (int)$mapId);
                        $redirect = 'labyrinthManager/caseWizard/' . $stepId . '/grid/' . $mapId;
                    }
                }
                Request::initial()->redirect(URL::base() . $redirect);
                break;
            case 'previewCounter':
                $mapId = $receivedMapId;
                $counterId = $this->request->param('id4', null);
                if ($counterId != null) {
                    $this->templateData['map'] = DB_ORM::model('map', array((int)$mapId));
                    $this->templateData['counter'] = DB_ORM::model('map_counter', array((int)$counterId));
                    $this->templateData['content'] = View::factory('labyrinth/casewizard/counter/preview')->set('templateData',
                        $this->templateData);
                } else {
                    Request::initial()->redirect(URL::base());
                }
                break;
            case 'uploadSkin':
                $this->templateData['map'] = DB_ORM::model('map', array((int)$receivedMapId));
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
                $this->templateData['map'] = DB_ORM::model('map', array((int)$receivedMapId));
                $this->templateData['skinList'] = DB_ORM::model('map_skin')->getAllSkins();
                $this->templateData['skinId'] = $this->request->param('id4', null);
                $previewList = View::factory('labyrinth/casewizard/skineditor/list');
                $previewList->set('templateData', $this->templateData);

                $this->templateData['content'] = $previewList;
                break;

            case 'saveSelectedSkin':
                $mapId = $receivedMapId;
                if ($_POST['skinId'] != 0 and $mapId != null) {
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
                    @mkdir($folder, DEFAULT_FOLDER_MODE);

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
                if ($action != null) {
                    // create map object, which used below, and fill it.
                    $map = DB_ORM::model('map');
                    $this->templateData['securities'] = DB_ORM::model('map_security')->getAllSecurities();
                    $this->templateData['sections'] = DB_ORM::model('map_section')->getAllSections();
                }
                if ($action > 0) {
                    $this->templateData['map'] = DB_ORM::model('map', array((int)$action));
                }
                break;
            case '2':
                $this->templateData['map'] = DB_ORM::model('map', array((int)$action));
                break;
            case '3':
                $this->templateData['map'] = DB_ORM::model('map', array((int)$action));
                break;
            case '4':
                if ($action != null) {
                    $this->templateData['action'] = $action;
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
                $this->templateData['map'] = DB_ORM::model('map', array((int)$receivedMapId));
                $this->templateData['action'] = $action;
                $this->templateData['result'] = $this->request->param('id4', null);
                break;
        }

        if (!$createSkin) {
            $this->templateData['center'] = View::factory('labyrinth/casewizard/step' . $stepId)->set('templateData',
                $this->templateData);
            unset($this->templateData['right']);
            $this->template->set('templateData', $this->templateData);
        } else {
            $this->templateData['action_url'] = URL::base() . 'labyrinthManager/caseWizard/5/saveSkin/' . $receivedMapId;
            $this->template = View::factory('labyrinth/skin/create');
            $this->template->set('templateData', $this->templateData);
        }
    }

    public function action_addNewMap()
    {
        if (isset($_POST) AND !empty($_POST)) {
            $_POST['author'] = Auth::instance()->get_user()->id;
            $map = DB_ORM::model('map')->createMap($_POST);
            Request::initial()->redirect(URL::base() . 'labyrinthManager/editMap/' . $map->id);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

    public function action_editMap()
    {
        $mapId = (int)$this->request->param('id', 0);
        if ($mapId) {
            Request::initial()->redirect(URL::base() . 'labyrinthManager/global/' . $mapId);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

    public function action_disableMap()
    {
        $mapId = (int)$this->request->param('id', 0);
        if ($mapId) {
            DB_ORM::model('map')->disableMap($mapId);
        }
        Request::initial()->redirect(URL::base() . 'authoredLabyrinth');
    }

    public function action_global()
    {
        $mapId = (int)$this->request->param('id', 0);
        if (!$mapId) {
            Request::initial()->redirect(URL::base());
        }

        $selectedLinkStyle = DB_ORM::model('Map_Node')->getMainLinkStyles($mapId);
        $map = DB_ORM::model('map', array($mapId));

        DB_ORM::model('User')->can('edit', array('mapId' => $mapId));
        $this->templateData['map'] = $map;
        $this->templateData['authorsList'] = DB_ORM::select('User')->where('type_id', '=', 2)->where('type_id', '=', 6,
            'OR')->order_by('nickname')->query()->as_array();
        $this->templateData['verification'] = ($map->verification != null) ? json_decode($map->verification,
            true) : array();
        $this->templateData['types'] = DB_ORM::model('map_type')->getAllTypes();
        $this->templateData['skins'] = DB_ORM::model('map_skin')->getAllSkins();
        $this->templateData['securities'] = DB_ORM::model('map_security')->getAllSecurities();
        $this->templateData['sections'] = DB_ORM::model('map_section')->getAllSections();
        $this->templateData['contributors'] = DB_ORM::model('map_contributor')->getAllContributors($mapId);
        $this->templateData['contributor_roles'] = DB_ORM::model('map_contributor_role')->getAllRoles();
        $this->templateData['linkStyles'] = DB_ORM::model('map_node_link_style')->getAllLinkStyles();
        $this->templateData['selectedLinkStyles'] = $selectedLinkStyle ? $selectedLinkStyle : 5;
        $this->templateData['files'] = DB_ORM::model('map_element')->getAllFilesByMap($mapId);
        $this->templateData['creators'] = DB_ORM::select('user')->where('type_id', '=', 2)->where('type_id', '=', 4,
            'OR')->order_by('nickname')->query()->as_array();
        $this->templateData['regAuthors'] = DB_ORM::model('map_user')->getAllAuthors($mapId);
        $this->templateData['groupsOfLearner'] = DB_ORM::model('User_Group')->getGroupOfLearners($mapId);
        $this->templateData['left'] = View::factory('labyrinth/labyrinthEditorMenu')->set('templateData',
            $this->templateData);
        $this->templateData['center'] = View::factory('labyrinth/global')->set('templateData', $this->templateData);
        $this->template->set('templateData', $this->templateData);

        Breadcrumbs::add(Breadcrumb::factory()->set_title($this->templateData['map']->name)->set_url(URL::base() . 'labyrinthManager/global/' . $mapId));
        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Details'))->set_url(URL::base() . 'labyrinthManager/global/id/' . $mapId));
    }

    public function action_addNewForum()
    {
        $mapId = $this->request->param('id', null);
        $redirectURL = URL::base();

        if ($mapId != null) {
            $map = DB_ORM::model('map', array((int)$mapId));
            if ($map != null) {
                $forum = DB_ORM::model('dforum')->createForum($map->name, 1, 1);
                DB_ORM::model('dforum_messages')->createMessage($forum, Arr::get($_POST, 'firstForumMessage', ''));
                DB_ORM::model('dforum_users')->updateUsers($forum, array(Auth::instance()->get_user()->id), 1);
                DB_ORM::model('map')->updateMapForumAssign($mapId, $forum);

                $redirectURL .= 'labyrinthManager/global/' . $mapId;
            }
        }
        Request::initial()->redirect($redirectURL);
    }

    public function action_unassignForum()
    {
        $mapId = $this->request->param('id', null);
        $redirectURL = URL::base();

        if ($mapId != null) {
            $map = DB_ORM::model('map', array((int)$mapId));
            if ($map != null) {
                DB_ORM::model('map')->updateMapForumAssign($mapId, null);

                $redirectURL .= 'labyrinthManager/global/' . $mapId;
            }
        }

        Request::initial()->redirect($redirectURL);
    }

    public function action_deleteContributor()
    {
        $mapId = (int)$this->request->param('id', 0);
        $contId = (int)$this->request->param('id2', 0);
        if ($mapId && $contId) {
            DB_ORM::model('map_contributor', array($contId))->delete();
            Request::initial()->redirect(URL::base() . 'labyrinthManager/global/' . $mapId);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

    public function action_saveGlobal()
    {
        $mapId = (int)$this->request->param('id', 0);
        $post = $this->request->post();

        if (!($post AND $mapId)) {
            Request::initial()->redirect(URL::base() . 'labyrinthManager/editMap/' . $mapId);
        }

        $delta_time_seconds = Arr::get($post, 'delta_time_seconds', false);
        $delta_time_minutes = Arr::get($post, 'delta_time_minutes', false);
        $reminder_seconds = Arr::get($post, 'reminder_seconds', false);
        $reminder_minutes = Arr::get($post, 'reminder_minutes', false);

        if ($delta_time_seconds AND $delta_time_minutes) {
            unset($post['delta_time_seconds']);
            unset($post['delta_time_minutes']);
            $post['delta_time'] = $delta_time_minutes * 60 + $delta_time_seconds;;
        }

        if ($reminder_seconds AND $reminder_minutes) {
            unset($post['reminder_seconds']);
            unset($post['reminder_minutes']);
            $post['reminder_time'] = $reminder_minutes * 60 + $reminder_seconds;;
        }

        if (isset($post['verification'])) {
            if (count($post['verification'])) {
                foreach ($post['verification'] as $key => $value) {
                    $verification = Arr::get($post, $key, 0);
                    $post['verification'][$key] = ($verification == 1) ? strtotime($post['verification'][$key]) : null;
                }
            }

            if (isset($post['inst_guide']) AND isset($post['inst_guide_select'])) {
                $post['verification']['inst_guide'] = ($post['inst_guide'] == 1) ? $post['inst_guide_select'] : null;
            } else {
                $post['verification']['inst_guide'] = null;
            }

            $post['verification'] = json_encode($post['verification']);
        }
        DB_ORM::model('map')->updateMap($mapId, $post);

        // ---- add new contributor and update old ---- //
        DB_ORM::model('map_contributor')->updateContributors($mapId, $post);
        $contributor = Arr::get($post, 'contributor', array());
        if ($contributor) {
            for ($i = 0; $i < count($contributor['name']); $i++) {
                $order = key($contributor['order']) + $i;
                DB_ORM::insert('Map_Contributor')
                    ->column('map_id', $mapId)
                    ->column('role_id', $contributor['role'][$i])
                    ->column('name', $contributor['name'][$i])
                    ->column('organization', $contributor['org'][$i])
                    ->column('order', $order)
                    ->execute();
            }
        }
        // ---- end add new contributor and update old ---- //

        $linkStyleId = Arr::get($post, 'linkStyle', null);
        if ($linkStyleId != null) {
            DB_ORM::model('map_node')->setLinkStyle($mapId, $linkStyleId);
        }

        $map = DB_ORM::model('map', array($mapId));
        if ($map) {
            $map->dev_notes = Arr::get($post, 'devnotes', $map->dev_notes);
            $map->save();
        }

        Model_Leap_Metadata_Record::updateMetadata("map", $mapId, $post);
        $controller = $this->request->post('edit_key') ? 'editKeys' : 'global';
        Request::initial()->redirect(URL::base() . 'labyrinthManager/' . $controller . '/' . $mapId);
    }

    public function action_editKeys()
    {
        $mapId = (int)$this->request->param('id', 0);
        $countOfKeys = (int)$this->request->param('id2', 0);
        if ($mapId) {
            $this->templateData['map'] = DB_ORM::model('map', array($mapId));
            if ($countOfKeys) {
                $this->templateData['keyCount'] = (int)$countOfKeys + 1;
            } else {
                $this->templateData['keyCount'] = 1;
            }

            $currentKeys = DB_ORM::model('map_key')->getKeysByMap($mapId);
            if ($currentKeys != null) {
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

    public function action_addKey()
    {
        $mapId = (int)$this->request->param('id', 0);
        $countOfKeys = (int)$this->request->param('id2', 0);
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

    public function action_saveKeys()
    {
        $mapId = (int)$this->request->param('id', 0);
        $countOfAddKeys = (int)$this->request->param('id2', 0);
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

    public function action_deleteKey()
    {
        $mapId = (int)$this->request->param('id', 0);
        $keyId = (int)$this->request->param('id2', 0);
        if ($keyId) {
            DB_ORM::model('map_key', array($keyId))->delete();
            Request::initial()->redirect(URL::base() . 'labyrinthManager/editKeys/' . $mapId);
        } else {
            Request::initial()->redirect(URL::base() . 'labyrinthManager/editKeys/' . $mapId);
        }
    }

    public function action_info()
    {
        $mapId = $this->request->param('id', null);

        if (!$mapId) {
            Request::initial()->redirect(URL::base() . 'openLabyrinth');
        }

        DB_ORM::model('User')->can('edit', array('mapId' => $mapId));
        $this->templateData['map'] = DB_ORM::model('map', array((int)$mapId));
        $this->templateData['center'] = View::factory('labyrinth/labyrinthInfo')->set('templateData',
            $this->templateData);
        $this->templateData['left'] = View::factory('labyrinth/labyrinthEditorMenu')->set('templateData',
            $this->templateData);
        $this->template->set('templateData', $this->templateData);
    }

    public function action_search()
    {
        $mapId = $this->request->param('id', null);
        $searchText = Arr::get($_GET, 's', null);

        $searcher = new Searcher();
        $searcher->addElement(
            new Searcher_Element_BasicMap_Dam(
                $mapId,
                array(
                    array('field' => 'id', 'label' => 'Id'),
                    array('field' => 'name', 'label' => 'Name')
                )
            )
        );
        $searcher->addElement(
            new Searcher_Element_BasicMap_Node(
                $mapId,
                array(
                    array('field' => 'id', 'label' => 'Id'),
                    array('field' => 'title', 'label' => 'Title'),
                    array('field' => 'text', 'label' => 'Content')
                )
            )
        );
        $searcher->addElement(new Searcher_Element_BasicMap_NodeSection($mapId, array(
            array('field' => 'id', 'label' => 'Id'),
            array('field' => 'name', 'label' => 'Name')
        )));
        $searcher->addElement(new Searcher_Element_BasicMap_Counter($mapId, array(
            array('field' => 'id', 'label' => 'Id'),
            array('field' => 'name', 'label' => 'Name'),
            array('field' => 'description', 'label' => 'Description'),
            array('field' => 'start_value', 'label' => 'Start value')
        )));
        $searcher->addElement(new Searcher_Element_Question($mapId, array(
            array('field' => 'id', 'label' => 'Id'),
            array('field' => 'stem', 'label' => 'Stem'),
            array('field' => 'response', 'label' => 'Response', 'type' => 'response'),
            array('field' => 'feedback', 'label' => 'Feedback', 'type' => 'response')
        )));
        $searcher->addElement(new Searcher_Element_Chat($mapId, array(
            array('field' => 'id', 'label' => 'Id'),
            array('field' => 'stem', 'label' => 'Stem'),
            array('field' => 'question', 'label' => 'Question', 'type' => 'element'),
            array('field' => 'response', 'label' => 'Response', 'type' => 'element')
        )));
        $searcher->addElement(new Searcher_Element_Vpd($mapId, array(
            array('field' => 'id', 'label' => 'Id'),
            array('field' => 'value', 'label' => 'Value', 'type' => 'element')
        )));

        $this->templateData['searchText'] = $searchText;
        $this->templateData['map'] = DB_ORM::model('map', array((int)$mapId));
        $this->templateData['center'] = View::factory('labyrinthSearchResult')->set('data',
            $searcher->search($searchText))->set('searchText', $searchText);
        $this->templateData['left'] = View::factory('labyrinth/labyrinthEditorMenu')->set('templateData',
            $this->templateData);
        $this->template->set('templateData', $this->templateData);
    }
}