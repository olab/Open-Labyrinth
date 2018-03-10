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

class Controller_WebinarManager extends Controller_Base
{
    public static $chat_id_template = 'chat-';
    public static $chat_quantity = 8;

    public function before()
    {
        parent::before();
        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Scenario Management'))->set_url(URL::base() . 'webinarManager'));
    }

    public function action_index()
    {
        Breadcrumbs::clear();

        $webinar_id = (int)$this->request->param('id', 0);
        $webinar_id = !empty($webinar_id) ? $webinar_id : Session::instance()->get('webinar_id', 0);
        $webinar = $this->getWebinar($webinar_id);

        if (!empty($webinar)) {
            Session::instance()->set('webinar_id', $webinar->id);
            Request::initial()->redirect(URL::base() . 'webinarmanager/progress/' . $webinar->id);
        }
        $this->templateData['scenario'] = $webinar;

        $this->templateData['webinars'] = $this->getWebinars();
        $this->templateData['center'] = View::factory('webinar/view')->set('templateData', $this->templateData);

        $this->template->set('templateData', $this->templateData);
    }

    public function action_chats()
    {
        Breadcrumbs::clear();
        $webinar_id = (int)$this->request->param('id', null);
        $webinar = $this->getWebinar($webinar_id);
        $users = $webinar->users;

        $this->templateData['users'] = $users;
        $this->templateData['webinar_id'] = $webinar_id;

        $settings = Auth::instance()->get_user()->getSettings();

        if (!empty($settings['webinars'][$webinar_id]['chats'])) {
            $chats = $settings['webinars'][$webinar_id]['chats'];
            if (count($chats) < self::$chat_quantity) {
                $chats = $this->addChats($chats);
            }
            uasort($chats, function ($a, $b) {
                if (!isset($a['order'], $b['order']) || ($a['order'] == $b['order'])) {
                    return 0;
                }

                return ($a['order'] < $b['order']) ? -1 : 1;
            });
        } else {
            $chats = $this->addChats();
        }

        $this->templateData['chats'] = $chats;
        $this->templateData['scenario'] = $webinar;
        $this->templateData['webinars'] = $this->getWebinars();
        $this->templateData['macros_list'] = $webinar->macros;
        $this->templateData['center'] = View::factory('webinar/chats')->set('templateData', $this->templateData);
        $this->templateData['left'] = View::factory('webinar/chat_macros')->set('templateData', $this->templateData);
        $this->template->set('templateData', $this->templateData);
    }

    public function action_my()
    {
        $this->templateData['scenarios'] = DB_ORM::model('webinar')->getScenariosByUser(Auth::instance()->get_user()->id);
        $this->templateData['center'] = View::factory('webinar/my')->set('templateData', $this->templateData);
        $this->template->set('templateData', $this->templateData);
    }

    public function action_add()
    {
        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Create Scenario'))->set_url(URL::base() . 'webinarManager/add'));

        $this->templateData['users'] = DB_ORM::model('user')->getAllUsersAndAuth('ASC');
        $this->templateData['groups'] = DB_ORM::model('group')->getAllGroups();
        $this->templateData['maps'] = (Auth::instance()->get_user()->type->name == 'superuser')
            ? DB_ORM::model('map')->getAllEnabledMap()
            : DB_ORM::model('map')->getAllEnabledAndAuthoredMap(Auth::instance()->get_user()->id, 0, true);
        // ------ Add sections ------- //
        foreach ($this->templateData['maps'] as $map) {
            foreach (DB_ORM::select('Map_Node_Section')->where('map_id', '=',
                $map->id)->query()->as_array() as $section) {
                $section->name = $map->name . '. Section: ' . $section->name;
                $this->templateData['maps'][] = $section;
            }
        }
        // ------ End add sections ------- //
        $this->templateData['forums'] = DB_ORM::model('dforum')->getAllForums(1, 0); // Type of Sort, 1 = Name , 0 - ASC
        $this->templateData['center'] = View::factory('webinar/webinar')->set('templateData', $this->templateData);
        $this->template->set('templateData', $this->templateData);
    }

    public function action_edit()
    {
        Breadcrumbs::clear();
        $webinarId = $this->request->param('id', null);

        $MapsObj = (Auth::instance()->get_user()->type->name == 'superuser')
            ? DB_ORM::model('map')->getAllEnabledMap(0, 'name', 'ASC')
            : DB_ORM::model('map')->getAllEnabledAndAuthoredMap(Auth::instance()->get_user()->id, 0, true);

        $this->templateData['maps'] = $MapsObj;

        // ------ Add sections ------- //
        foreach ($MapsObj as $map) {
            foreach (DB_ORM::select('Map_Node_Section')->where('map_id', '=',
                $map->id)->query()->as_array() as $section) {
                $this->templateData['sections'][$section->id] = $section->map_id;
                $section->name = $map->name;
                $this->templateData['maps'][] = $section;
            }
        }
        // ------ End add sections ------- //

        // ------ Add poll node, end nodes of map ------- //
        foreach (DB_ORM::select('Webinar_PollNode')->where('webinar_id', '=',
            $webinarId)->query()->as_array() as $obj) {
            $nodeObj = DB_ORM::model('Map_Node', array($obj->node_id));
            $mapId = $nodeObj->map->id;
            $this->templateData[$mapId]['pollNodes'][$obj->node_id] = $obj->time;

            if (empty($this->templateData[$mapId]['mapNodes'])) {
                foreach (DB_ORM::model('Map_Node')->getAllNode($mapId) as $nodeObj) {
                    $this->templateData[$mapId]['mapNodes'][$nodeObj->id] = $nodeObj->title;
                }
            }
        }
        // ------ End add poll node ------- //

        $webinar = $this->getWebinar($webinarId);
        $this->templateData['macros_list'] = $webinar->macros;
        $this->templateData['webinar'] = $webinar;
        $this->templateData['webinars'] = $this->getWebinars();

        $this->templateData['experts'] = array();
        foreach (DB_ORM::select('Webinar_User')->where('webinar_id', '=',
            $webinarId)->query()->as_array() as $wUserObj) {
            if ($wUserObj->expert == 1) {
                $this->templateData['experts'][] = $wUserObj->user_id;
            }
        }

        $existUsers = array();
        if (count($this->templateData['webinar']->users) > 0) {
            foreach ($this->templateData['webinar']->users as $webinarUser) {
                $existUsers[] = $webinarUser->user_id;
            }
        }

        $existGroups = array();
        if ($this->templateData['webinar'] != null && count($this->templateData['webinar']->groups) > 0) {
            foreach ($this->templateData['webinar']->groups as $webinarGroup) {
                $existGroups[] = $webinarGroup->group_id;
            }
        }

        $this->templateData['users'] = DB_ORM::model('user')->getAllUsersAndAuth('ASC', $existUsers);
        $this->templateData['groups'] = DB_ORM::model('group')->getAllGroups('ASC', $existGroups);

        $allUsers = DB_ORM::model('user')->getAllUsersAndAuth('ASC');
        if ($allUsers != null && count($allUsers) > 0) {
            foreach ($allUsers as $user) {
                $this->templateData['usersMap'][$user['id']] = $user;
            }
        }
        $this->templateData['center'] = View::factory('webinar/webinar')->set('templateData', $this->templateData);
        $this->template->set('templateData', $this->templateData);
    }

    public function action_showStats()
    {
        Breadcrumbs::clear();
        $scenarioId = $this->request->param('id', null);
        $step = $this->request->param('id2', null);
        $dateId = $this->request->param('id3', null);

        if ($scenarioId == null || $dateId == null) {
            Request::initial()->redirect(URL::base() . 'webinarmanager/index');
        }

        $scenarioStepMap = array();
        $scenarioData = array();
        $usersMap = array();
        $scenario = $this->getWebinar($scenarioId);

        if (count($scenario->users) AND count($scenario->maps)) {
            foreach ($scenario->users as $scenarioUser) {
                $userId = $scenarioUser->user_id;
                if (!isset($usersMap[$userId])) {
                    $usersMap[$userId] = $scenarioUser->user;
                }

                foreach ($scenario->maps as $scenarioMap) {
                    $scenarioStep = $scenarioMap->step;
                    $scenarioWhichId = $scenarioMap->reference_id;
                    $scenarioWhich = $scenarioMap->which;

                    if ($scenarioWhich == 'labyrinth') {
                        $scenarioData[$userId][$scenarioStep][$scenarioWhichId]['map'] = DB_ORM::model('Map',
                            array((int)$scenarioWhichId));
                    } elseif ($scenarioWhich == 'section') {
                        $scenarioData[$userId][$scenarioStep][$scenarioWhichId]['section'] = DB_ORM::model('Map_Section',
                            array((int)$scenarioWhichId));
                    }
                    $scenarioData[$userId][$scenarioStep][$scenarioWhichId]['user'] = $scenarioUser->user;
                    $scenarioData[$userId][$scenarioStep][$scenarioWhichId]['status'] = $scenarioStep <= $step
                        ? DB_ORM::model('statistics_user_session')->isUserFinishMap($scenarioWhichId, $userId,
                            $scenarioWhich, $scenario->id, $step, $dateId)
                        : 0;
                }
            }

            if (count($scenario->steps)) {
                foreach ($scenario->steps as $scenarioStep) {
                    $scenarioStepMap[$scenarioStep->id] = $scenarioStep;
                }
            }
        }

        $this->templateData['webinar'] = $scenario;
        $this->templateData['webinars'] = $this->getWebinars();
        $this->templateData['webinarStepMap'] = $scenarioStepMap;

        foreach ($this->templateData['webinar']->users as $user) {
            DB_ORM::model('webinar_user')->updateInclude4R($user->id, 1);

            $this->templateData['includeUsersData'][$user->user_id] = $user->id;
            $this->templateData['includeUsers'][$user->user_id] = $user->include_4R;
        }

        $this->templateData['usersMap'] = $usersMap;
        $this->templateData['webinarData'] = $scenarioData;
        $this->templateData['step'] = $step;
        $this->templateData['dateId'] = $dateId;

        foreach (DB_ORM::model('user')->getAllUsersAndAuth('ASC') as $user) {
            $this->templateData['usersAuthMap'][$user['id']] = $user;
        }

        $this->templateData['center'] = View::factory('webinar/showStats')->set('templateData', $this->templateData);
        $this->template->set('templateData', $this->templateData);

        $date = date('Y-m-d H:i:s', DB_ORM::model('statistics_user_datesave', array($dateId))->date_save);
        Breadcrumbs::add(Breadcrumb::factory()->set_title('Statistics for ' . $scenario->title)->set_url(URL::base() . 'webinarManager/statistics/' . $scenarioId));
        Breadcrumbs::add(Breadcrumb::factory()->set_title($date)->set_url(URL::base() . 'webinarManager/progress'));
    }

    public function action_progress()
    {
        Breadcrumbs::clear();
        $webinarId = $this->request->param('id', null);

        if ($webinarId == null) {
            Request::initial()->redirect(URL::base() . 'webinarmanager/index');
        }

        $wData = array();
        $usersMap = array();
        $webinar = $this->getWebinar($webinarId);
        $webinarStepMap = array();

        if ($webinar != null && count($webinar->users) && count($webinar->maps) > 0) {
            foreach ($webinar->users as $wUser) {
                $wUserId = $wUser->user_id;
                $userType = DB_ORM::model('User', array($wUserId))->type_id;
                if (!isset($usersMap[$wUserId])) {
                    $usersMap[$wUserId] = $wUser->user;
                }

                $this->templateData['includeUsersData'][$wUserId] = $wUser->id;
                $this->templateData['includeUsers'][$wUserId] = $wUser->include_4R;
                if ($userType != 1) {
                    $this->templateData['experts'][$wUserId] = $wUser->expert;
                }

                foreach ($webinar->maps as $wMapObj) {
                    $wStep = $wMapObj->step;
                    $id = $wMapObj->reference_id;
                    $wCurrentStep = $webinar->current_step;
                    $prefix = '';

                    if ($wMapObj->which == 'labyrinth') {
                        $wData[$wUserId][$wStep][$id]['map'] = DB_ORM::model('map', array((int)$id));
                    } else {
                        $prefix = 's';
                        $sectionObj = DB_ORM::model('Map_Node_Section', array($id));
                        $sectionObj->id = $sectionObj->map_id;
                        $sectionObj->name = 'Section: ' . $sectionObj->name;
                        $wData[$wUserId][$wStep][$prefix . $id]['map'] = $sectionObj;
                    }

                    $wData[$wUserId][$wStep][$prefix . $id]['status'] = ($wStep <= $wCurrentStep)
                        ? DB_ORM::model('user_session')->isUserFinishMap($id, $wUserId, $wMapObj->which, $webinar->id,
                            $wCurrentStep)
                        : 0;
                    $last_trace = DB_ORM::model('User')->getLastSessionTrace($wUserId, $webinar->id, $id);
                    if (!empty($last_trace)) {
                        $last_node_id = $last_trace->node_id;
                        $last_node_title = $last_trace->node->title;
                    } else {
                        $last_node_id = null;
                        $last_node_title = null;
                    }
                    $wData[$wUserId][$wStep][$prefix . $id]['node_id'] = $last_node_id;
                    $wData[$wUserId][$wStep][$prefix . $id]['node_title'] = $last_node_title;
                    $wData[$wUserId][$wStep][$prefix . $id]['user'] = $wUser->user;
                }
            }
            if (count($webinar->steps)) {
                foreach ($webinar->steps as $webinarStep) {
                    $webinarStepMap[$webinarStep->id] = $webinarStep;
                }
            }
        }

        $this->templateData['scenario'] = DB_ORM::select('Webinar')->query()->as_array();
        $this->templateData['webinarStepMap'] = $webinarStepMap;
        $this->templateData['webinar'] = $webinar;
        $this->templateData['webinars'] = $this->getWebinars();
        $this->templateData['usersMap'] = $usersMap;
        $this->templateData['webinarData'] = $wData;

        $allUsers = DB_ORM::model('user')->getAllUsersAndAuth('ASC');
        if (count($allUsers)) {
            foreach ($allUsers as $user) {
                $this->templateData['usersAuthMap'][$user['id']] = $user;
            }
        }

        $this->templateData['center'] = View::factory('webinar/statistic')->set('templateData', $this->templateData);
        $this->template->set('templateData', $this->templateData);
    }

    public function action_statistic()
    {
        Request::initial()->redirect(URL::base() . 'webinarmanager/progress/' . $this->request->param('id', null));
    }

    /*public function action_timeBasedReports()
    {
        $this->templateData['center'] = View::factory('webinar/timeBasedReports')->set('templateData',
            $this->templateData);
        $this->template->set('templateData', $this->templateData);
    }*/

    public function action_statistics()
    {
        $webinarId = $this->request->param('id', null);

        if ($webinarId == null) {
            Request::initial()->redirect(URL::base() . 'webinarmanager/index');
        } else {
            $this->templateData['history'] = DB_ORM::model('statistics_user_session')->getDateSaveByWebinarId($webinarId);

            $webinar = $this->getWebinar($webinarId);

            $this->templateData['webinar'] = $webinar;
            $this->templateData['webinars'] = $this->getWebinars();
            Breadcrumbs::add(Breadcrumb::factory()->set_title('Statistics for ' . $webinar->title)->set_url(URL::base() . 'webinarManager/statistics'));

            $this->templateData['center'] = View::factory('webinar/all');
            $this->templateData['center']->set('templateData', $this->templateData);
            $this->template->set('templateData', $this->templateData);
        }
    }

    public function action_publishStep()
    {
        $webinarId = $this->request->param('id', null);
        $webinarStep = $this->request->param('id2', null);
        $dateId = $this->request->param('id3', null);
        $webinar = DB_ORM::model('webinar', array((int)$webinarId));

        if ($webinar != null && $webinarStep != null && $webinarStep > 0) {
            $jsonObject = ($webinar->publish == null) ? array() : json_decode($webinar->publish);

            if (!in_array($webinarId . '-' . $webinarStep, $jsonObject)) {
                $jsonObject[] = $webinarId . '-' . $webinarStep;

                $webinar->publish = json_encode($jsonObject);
                $webinar->save();

                if ($webinar->forum_id > 0) {
                    DB_ORM::model('dforum_messages')->createMessage($webinar->forum_id,
                        '<a href="' . URL::base() . 'webinarManager/stepReport4R/' . $webinarId . '/' . $webinarStep . '/' . $dateId . '">Step ' . $webinarStep . ' 4R Report</a>');
                }
            }

            Request::initial()->redirect(URL::base() . 'webinarmanager/showStats/' . $webinarId . '/' . $webinarStep . '/' . $dateId);
        } else {
            Request::initial()->redirect(URL::base() . 'webinarmanager/index');
        }
    }

    public function action_render()
    {
        $scenarioId = $this->request->param('id', null);
        $scenario = DB_ORM::model('webinar', array($scenarioId));

        if (count($scenario->steps)) {
            foreach ($scenario->maps as $scenarioMap) {
                $this->templateData['mapsMap'][$scenarioMap->step][$scenarioMap->reference_id] = ($scenarioMap->step <= $scenario->current_step)
                    ? DB_ORM::model('user_session')->isUserFinishMap($scenarioMap->reference_id,
                        Auth::instance()->get_user()->id, $scenarioMap->which, $scenarioId, $scenario->current_step)
                    : 0;
            }
        }

        $this->templateData['scenario'] = $scenario;
        $this->templateData['center'] = View::factory('webinar/render')->set('templateData', $this->templateData);
        $this->template->set('templateData', $this->templateData);
    }

    public function action_delete()
    {
        DB_ORM::model('webinar')->deleteWebinar($this->request->param('id', null));
        Session::instance()->delete('webinar_id');
        Request::initial()->redirect(URL::base() . 'webinarmanager/index');
    }

    public function action_save()
    {
        DB_ORM::model('webinar')->saveWebinar($this->request->post());
        Request::initial()->redirect(URL::base() . 'webinarmanager/index');
    }

    public function action_changeStep()
    {
        $scenarioId = $this->request->param('id', null);
        $step = $this->request->param('id2', null);
        $redirect = $this->request->param('id3', null);

        DB_ORM::model('webinar')->changeWebinarStep($scenarioId, $step);

        if ($redirect == null) {
            Request::initial()->redirect(URL::base() . 'webinarmanager/index/' . $scenarioId);
        } else {
            Request::initial()->redirect(URL::base() . 'webinarmanager/progress/' . $scenarioId);
        }
    }

    public function action_stepReport4R()
    {
        $post = $this->request->post();
        $is_ajax = Arr::get($post, 'is_ajax', '0') === '0' ? false : true;
        $filename = Arr::get($post, 'filename', 'report');
        $webinarId = $this->request->param('id', null);
        $stepKey = $this->request->param('id2', null);
        $dateId = $this->request->param('id3', null);
        $redirect_url = URL::base() . 'webinarManager/progress/' . $webinarId;

        if (!($webinarId != null && $webinarId > 0 && $stepKey != null && $stepKey > 0)) {
            if ($is_ajax) {
                $this->jsonResponse(array('reload' => true));
            }
            Request::initial()->redirect(URL::base() . 'webinarmanager/index');
        }

        $webinar = DB_ORM::model('webinar', array((int)$webinarId));
        $isExistAccess = false;

        if (Auth::instance()->get_user()->id == $webinar->author_id || Auth::instance()->get_user()->type->name == 'superuser') {
            $isExistAccess = true;
        }

        if (!$isExistAccess && $webinar->publish != null) {
            $jsonObject = json_decode($webinar->publish);

            $isExistAccess = in_array($webinarId . '-' . $stepKey, $jsonObject);
        }

        if (!$isExistAccess) {
            Session::instance()->set('error_message', 'Access denied.');
            if ($is_ajax) {
                $this->jsonResponse(array('reload' => true));
            }
            Request::initial()->redirect($redirect_url);
        }

        if ($is_ajax) {
            Session::instance()->write();
        }

        $report = new Report_4R(new Report_Impl_PHPExcel(), $filename);
        $notIncludUsers = DB_ORM::model('webinar_user')->getNotIncludedUsers($webinar->id);
        if ($webinar != null && count($webinar->maps) > 0) {
            foreach ($webinar->maps as $webinarMap) {
                if ($webinarMap->step == $stepKey) {
                    $mapId = ($webinarMap->which == 'labyrinth')
                        ? $webinarMap->reference_id
                        : DB_ORM::model('Map_Node_Section', array($webinarMap->reference_id))->map_id;
                    $report->add($mapId, $webinar->id, $stepKey, $notIncludUsers, $dateId);
                }
            }
        }
        $report->generate();
        $report->get($is_ajax);
        die;
    }

    public function action_stepReportSCT()
    {
        $post = $this->request->post();
        $is_ajax = Arr::get($post, 'is_ajax', '0') === '0' ? false : true;
        $filename = Arr::get($post, 'filename', 'report');
        $webinarId = $this->request->param('id', null);
        $stepKey = $this->request->param('id2', null);
        $expertWebinarId = $this->request->param('id3', null);
        $redirect_url = URL::base() . 'webinarManager/progress/' . $webinarId;
        $latest = Session::instance()->get('report_by_latest_session', true);

        if ($webinarId == null AND $stepKey) {
            if ($is_ajax) {
                $this->jsonResponse(array('reload' => true));
            }
            Request::initial()->redirect(URL::base() . 'webinarmanager/index');
        }

        $webinar = DB_ORM::model('webinar', array((int)$webinarId));
        $isExistAccess = false;

        if (Auth::instance()->get_user()->id == $webinar->author_id || Auth::instance()->get_user()->type->name == 'superuser') {
            $isExistAccess = true;
        }

        if (!$isExistAccess AND $webinar->publish != null) {
            $jsonObject = json_decode($webinar->publish);
            $isExistAccess = in_array($webinarId . '-' . $stepKey, $jsonObject);
        }

        if (!$isExistAccess) {
            Session::instance()->set('error_message', 'Access denied.');
            if ($is_ajax) {
                $this->jsonResponse(array('reload' => true));
            }
            Request::initial()->redirect($redirect_url);
        }

        if ($is_ajax) {
            Session::instance()->write();
        }

        $report = new Report_SCT(new Report_Impl_PHPExcel(), $filename);
        if ($webinar != null && count($webinar->maps) > 0) {
            foreach ($webinar->maps as $webinarMap) {
                if ($webinarMap->step == $stepKey) {
                    // if labyrinth, else section
                    if ($webinarMap->which == 'labyrinth') {
                        $mapId = $webinarMap->reference_id;
                        $sectionId = false;
                    } else {
                        $mapId = DB_ORM::model('Map_Node_Section', array($webinarMap->reference_id))->map_id;
                        $sectionId = $webinarMap->reference_id;
                    }
                    $report->add($mapId, $webinarId, $expertWebinarId, $sectionId);
                }
            }
        }
        $report->generate($latest);
        $report->get($is_ajax);
        die;
    }

    public function action_stepReportSJT()
    {
        $post = $this->request->post();
        $is_ajax = Arr::get($post, 'is_ajax', '0') === '0' ? false : true;
        $filename = Arr::get($post, 'filename', 'report');
        $scenarioId = $this->request->param('id', null);
        $stepKey = $this->request->param('id2', null);
        $expertScenarioId = $this->request->param('id3', null);
        $redirect_url = URL::base() . 'webinarManager/progress/' . $scenarioId;
        $latest = Session::instance()->get('report_by_latest_session', true);

        if ($scenarioId == null AND $stepKey) {
            if ($is_ajax) {
                $this->jsonResponse(array('reload' => true));
            }
            Request::initial()->redirect(URL::base() . 'webinarmanager/index');
        }

        $scenario = DB_ORM::model('webinar', array($scenarioId));
        $isExistAccess = ((Auth::instance()->get_user()->id == $scenario->author_id) OR (Auth::instance()->get_user()->type->name == 'superuser'));

        if (!$isExistAccess AND $scenario->publish) {
            $jsonObject = json_decode($scenario->publish);
            $isExistAccess = in_array($scenarioId . '-' . $stepKey, $jsonObject);
        }

        if (!$isExistAccess) {
            Session::instance()->set('error_message', 'Access denied.');
            if ($is_ajax) {
                $this->jsonResponse(array('reload' => true));
            }
            Request::initial()->redirect($redirect_url);
        }

        if ($is_ajax) {
            Session::instance()->write();
        }

        $report = new Report_SJT(new Report_Impl_PHPExcel(), $filename);
        if (count($scenario->maps)) {
            foreach ($scenario->maps as $scenarioMap) {
                if ($scenarioMap->step == $stepKey) {
                    // if labyrinth, else section
                    if ($scenarioMap->which == 'labyrinth') {
                        $mapId = $scenarioMap->reference_id;
                        $sectionId = false;
                    } else {
                        $mapId = DB_ORM::model('Map_Node_Section', array($scenarioMap->reference_id))->map_id;
                        $sectionId = $scenarioMap->reference_id;
                    }
                    $report->add($mapId, $scenarioId, $expertScenarioId, $sectionId);
                }
            }
        }
        $report->generate($latest);
        $report->get($is_ajax);
        die;
    }

    public function action_stepReportPoll()
    {
        $post = $this->request->post();
        $is_ajax = Arr::get($post, 'is_ajax', '0') === '0' ? false : true;
        $filename = Arr::get($post, 'filename', 'report');
        $webinarId = $this->request->param('id', null);
        $stepKey = $this->request->param('id2', null);
        $redirect_url = URL::base() . 'webinarManager/progress/' . $webinarId;
        $latest = Session::instance()->get('report_by_latest_session', true);

        if ($webinarId == null AND $stepKey != null) {
            if ($is_ajax) {
                $this->jsonResponse(array('reload' => true));
            }
            Request::initial()->redirect(URL::base() . 'webinarmanager/index');
        }

        $webinar = DB_ORM::model('webinar', array((int)$webinarId));
        $isExistAccess = false;

        if (Auth::instance()->get_user()->id == $webinar->author_id OR Auth::instance()->get_user()->type->name == 'superuser') {
            $isExistAccess = true;
        }

        if (!$isExistAccess AND $webinar->publish != null) {
            $jsonObject = json_decode($webinar->publish);
            $isExistAccess = in_array($webinarId . '-' . $stepKey, $jsonObject);
        }

        if (!$isExistAccess) {
            Session::instance()->set('error_message', 'Access denied.');
            if ($is_ajax) {
                $this->jsonResponse(array('reload' => true));
            }
            Request::initial()->redirect($redirect_url);
        }

        if ($is_ajax) {
            Session::instance()->write();
        }

        $report = new Report_Poll(new Report_Impl_PHPExcel(), $filename);
        if (count($webinar->maps) > 0) {
            foreach ($webinar->maps as $webinarMap) {
                if ($webinarMap->step == $stepKey) {
                    $mapId = ($webinarMap->which == 'labyrinth')
                        ? $webinarMap->reference_id
                        : DB_ORM::model('Map_Node_Section', array($webinarMap->reference_id))->map_id;
                    $report->add($mapId, $webinarId);
                }
            }
        }
        $report->generate($latest);
        $report->get($is_ajax);
        die;
    }

    public static function saveReportProgress($filename, $is_done_new = null, $counter_new = null)
    {
        $data = static::getReportProgressData($filename);
        $counter = $data['counter'];
        $is_done = $data['is_done'];
        $progress_filename = $data['progress_filename'];

        file_put_contents($progress_filename, json_encode(array(
            'is_done' => isset($is_done_new) ? $is_done_new : $is_done,
            'counter' => isset($counter_new) ? $counter_new : $counter,
        )));
    }

    public static function getReportProgressData($filename)
    {
        $progress_filename = $_SERVER['DOCUMENT_ROOT'] . '/tmp/report_progress_' . $filename;
        if (file_exists($progress_filename)) {
            $data = json_decode(file_get_contents($progress_filename), true);
            $counter = (int)$data['counter'];
            $is_done = $data['is_done'];
        } else {
            $counter = 0;
            $is_done = false;
        }

        return array(
            'is_done' => $is_done,
            'counter' => $counter,
            'progress_filename' => $progress_filename,
        );
    }

    public function action_getReportProgress()
    {
        $filename = $this->request->post('filename');

        $data = static::getReportProgressData($filename);

        $is_done = $data['is_done'];
        $counter = $data['counter'];
        $progress_filename = $data['progress_filename'];

        if ($is_done && file_exists($progress_filename)) {
            unlink($progress_filename);
        }

        $this->jsonResponse(array(
            'session_counter' => $counter,
            'is_done' => $is_done,
        ));
    }

    public function action_downloadReport()
    {
        $filename = $this->request->param('id');
        $file_path = $_SERVER['DOCUMENT_ROOT'] . '/tmp/' . $filename . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx');
        header('Cache-Control: max-age=0');
        readfile($file_path);
        unlink($file_path);
        die;
    }

    public function action_stepReportxAPI()
    {
        set_time_limit(60 * 5);
        $post = $this->request->post();
        $is_initial_request = Arr::get($post, 'is_initial_request', 1) === '0' ? false : true;
        $webinarId = $this->request->param('id', null);
        $stepKey = $this->request->param('id2', null);

        $webinar = DB_ORM::model('webinar', array((int)$webinarId));
        $isExistAccess = false;

        if (Auth::instance()->get_user()->id == $webinar->author_id || Auth::instance()->get_user()->type->name == 'superuser') {
            $isExistAccess = true;
        }

        if (!$isExistAccess && $webinar->publish != null) {
            $jsonObject = json_decode($webinar->publish);
            $isExistAccess = in_array($webinarId . '-' . $stepKey, $jsonObject);
        }

        if (!$isExistAccess) {
            Session::instance()->set('error_message', 'Access denied.');
            $this->jsonResponse(array('completed' => true));
        }

        $not_included_user_ids = DB_ORM::model('webinar_user')->getNotIncludedUsers($webinar->id);

        $per_iteration = 1;
        $limit = $per_iteration;
        if ($is_initial_request) {
            $i = 0;
            $offset = 0;
            
            $query_count = DB_SQL::select()
                ->from(Model_Leap_User_Session::table())
                ->where('webinar_id', '=', $webinarId)
                ->where('webinar_step', '=', $stepKey);

            if (!empty($not_included_user_ids)) {
                $query_count->where('user_id', 'NOT IN', $not_included_user_ids);
            }
            Session::instance()->set('xAPI_report_total_sessions', $query_count
                ->column(DB_SQL::expr("COUNT(*)"), 'counter')
                ->query()[0]['counter']
            );
        } else {
            $i = Session::instance()->get('xAPI_report_i');
            $offset = Session::instance()->get('xAPI_report_offset');
        }

        $query = DB_ORM::select('User_Session')
            ->where('webinar_id', '=', $webinarId)
            ->where('webinar_step', '=', $stepKey);

        if (!empty($not_included_user_ids)) {
            $query->where('user_id', 'NOT IN', $not_included_user_ids);
        }

        /** @var Model_Leap_User_Session[]|DB_ResultSet $sessions */
        $sessions = $query
            ->order_by('id', 'ASC')
            ->offset($offset)
            ->limit($limit)
            ->query();

        if ($sessions->count() > 0) {
            Model_Leap_User_Session::sendSessionsToLRS($sessions);
        }

        $offset += $per_iteration;
        $i++;

        Session::instance()->set('xAPI_report_i', $i);
        Session::instance()->set('xAPI_report_offset', $offset);

        $total_sessions = Session::instance()->get('xAPI_report_total_sessions', 0);

        if ($is_initial_request && $sessions->count() == 0) {
            Session::instance()->set('error_message', 'Sessions not found');
            $this->jsonResponse(array('completed' => true, 'total' => $total_sessions, 'sent' => $offset));
        } elseif ($sessions->count() == 0) {
            Session::instance()->set('info_message', 'Statements sent to LRS');
            $this->jsonResponse(array('completed' => true, 'total' => $total_sessions, 'sent' => $offset));
        } else {
            $this->jsonResponse(array('completed' => false, 'total' => $total_sessions, 'sent' => $offset));
        }
    }

    public function action_report4RTimeBased()
    {
        $post = $this->request->post();
        $is_ajax = Arr::get($post, 'is_ajax', '0') === '0' ? false : true;
        $filename = Arr::get($post, 'filename', 'report');
        $dateId = $this->request->param('id3', null);

        $webinars = $this->getWebinarsForTimeBasedReport();
        $date_from = Arr::get($post, 'date_from');
        $date_to = Arr::get($post, 'date_to');
        $date_from_obj = DateTime::createFromFormat('m/d/Y H:i:s', $date_from . ' 00:00:00');
        $date_to_obj = DateTime::createFromFormat('m/d/Y H:i:s', $date_to . ' 23:59:59');

        if ($is_ajax) {
            Session::instance()->write();
        }

        $report = new Report_4R(new Report_Impl_PHPExcel(), $filename);
        //$notIncludUsers = null;
        foreach ($webinars as $webinar) {
            if ($webinar != null && count($webinar->maps) > 0) {
                $notIncludUsers = DB_ORM::model('webinar_user')->getNotIncludedUsers($webinar->id);
                foreach ($webinar->maps as $webinarMap) {
                    $mapId = ($webinarMap->which == 'labyrinth')
                        ? $webinarMap->reference_id
                        : DB_ORM::model('Map_Node_Section', array($webinarMap->reference_id))->map_id;
                    $report->add($mapId, $webinar->id, null, $notIncludUsers, $dateId);
                }
            }
        }
        $report->generate($date_from_obj->getTimestamp(), $date_to_obj->getTimestamp());
        $report->get($is_ajax);
        die;
    }

    public function action_reportSCTTimeBased()
    {
        $post = $this->request->post();
        $is_ajax = Arr::get($post, 'is_ajax', '0') === '0' ? false : true;
        $filename = Arr::get($post, 'filename', 'report');
        $expertWebinarId = $this->request->param('id3', null);
        $latest = Session::instance()->get('report_by_latest_session', true);

        $webinars = $this->getWebinarsForTimeBasedReport();
        $date_from = Arr::get($post, 'date_from');
        $date_to = Arr::get($post, 'date_to');
        $date_from_obj = DateTime::createFromFormat('m/d/Y H:i:s', $date_from . ' 00:00:00');
        $date_to_obj = DateTime::createFromFormat('m/d/Y H:i:s', $date_to . ' 23:59:59');

        if ($is_ajax) {
            Session::instance()->write();
        }

        $report = new Report_SCT(new Report_Impl_PHPExcel(), $filename);
        foreach ($webinars as $webinar) {
            if ($webinar != null && count($webinar->maps) > 0) {
                foreach ($webinar->maps as $webinarMap) {
                    // if labyrinth, else section
                    if ($webinarMap->which == 'labyrinth') {
                        $mapId = $webinarMap->reference_id;
                        $sectionId = false;
                    } else {
                        $mapId = DB_ORM::model('Map_Node_Section', array($webinarMap->reference_id))->map_id;
                        $sectionId = $webinarMap->reference_id;
                    }
                    $report->add($mapId, $webinar->id, $expertWebinarId, $sectionId);
                }
            }
        }
        $report->generate($latest, $date_from_obj->getTimestamp(), $date_to_obj->getTimestamp());
        $report->get($is_ajax);
        die;
    }

    public function action_reportSJTTimeBased()
    {
        $post = $this->request->post();
        $is_ajax = Arr::get($post, 'is_ajax', '0') === '0' ? false : true;
        $filename = Arr::get($post, 'filename', 'report');
        $expertScenarioId = $this->request->param('id3', null);
        $latest = Session::instance()->get('report_by_latest_session', true);

        $webinars = $this->getWebinarsForTimeBasedReport();
        $date_from = Arr::get($post, 'date_from');
        $date_to = Arr::get($post, 'date_to');
        $date_from_obj = DateTime::createFromFormat('m/d/Y H:i:s', $date_from . ' 00:00:00');
        $date_to_obj = DateTime::createFromFormat('m/d/Y H:i:s', $date_to . ' 23:59:59');

        if ($is_ajax) {
            Session::instance()->write();
        }

        $report = new Report_SJT(new Report_Impl_PHPExcel(), $filename);
        foreach ($webinars as $scenario) {
            if (count($scenario->maps)) {
                foreach ($scenario->maps as $scenarioMap) {
                    // if labyrinth, else section
                    if ($scenarioMap->which == 'labyrinth') {
                        $mapId = $scenarioMap->reference_id;
                        $sectionId = false;
                    } else {
                        $mapId = DB_ORM::model('Map_Node_Section', array($scenarioMap->reference_id))->map_id;
                        $sectionId = $scenarioMap->reference_id;
                    }
                    $report->add($mapId, $scenario->id, $expertScenarioId, $sectionId);
                }
            }
        }
        $report->generate($latest, $date_from_obj->getTimestamp(), $date_to_obj->getTimestamp());
        $report->get($is_ajax);
        die;
    }

    public function action_reportPollTimeBased()
    {
        $post = $this->request->post();
        $is_ajax = Arr::get($post, 'is_ajax', '0') === '0' ? false : true;
        $filename = Arr::get($post, 'filename', 'report');
        $latest = Session::instance()->get('report_by_latest_session', true);

        $webinars = $this->getWebinarsForTimeBasedReport();
        $date_from = Arr::get($post, 'date_from');
        $date_to = Arr::get($post, 'date_to');
        $date_from_obj = DateTime::createFromFormat('m/d/Y H:i:s', $date_from . ' 00:00:00');
        $date_to_obj = DateTime::createFromFormat('m/d/Y H:i:s', $date_to . ' 23:59:59');

        if ($is_ajax) {
            Session::instance()->write();
        }

        $report = new Report_Poll(new Report_Impl_PHPExcel(), $filename);
        foreach ($webinars as $webinar) {
            if (count($webinar->maps) > 0) {
                foreach ($webinar->maps as $webinarMap) {
                    $mapId = ($webinarMap->which == 'labyrinth')
                        ? $webinarMap->reference_id
                        : DB_ORM::model('Map_Node_Section', array($webinarMap->reference_id))->map_id;
                    $report->add($mapId, $webinar->id);
                }
            }
        }

        $report->generate($latest, $date_from_obj->getTimestamp(), $date_to_obj->getTimestamp());
        $report->get($is_ajax);
        die;
    }

    private function getWebinarsForTimeBasedReport()
    {
        set_time_limit(60 * 5);
        $post = $this->request->post();
        $date_from = Arr::get($post, 'date_from');
        $date_to = Arr::get($post, 'date_to');
        $webinar_id = Arr::get($post, 'webinar_id');
        $redirect_url = URL::base() . 'webinarmanager/progress';

        if (empty($date_from) || empty($date_to)) {
            Session::instance()->set('error_message', 'Dates cannot be blank');
            $this->jsonResponse(array('reload' => true));
        }

        $date_from_obj = DateTime::createFromFormat('m/d/Y H:i:s', $date_from . ' 00:00:00');
        $date_to_obj = DateTime::createFromFormat('m/d/Y H:i:s', $date_to . ' 23:59:59');

        $webinar_ids = DB_SQL::select()
            ->from(Model_Leap_User_Session::table())
            ->where('start_time', '>=', $date_from_obj->getTimestamp())
            ->where('start_time', '<=', $date_to_obj->getTimestamp())
            ->where('webinar_id', '=', $webinar_id)
            ->column('webinar_id')
            ->query()
            ->as_array();

        if (empty($webinar_ids)) {
            Session::instance()
                ->set('error_message', 'Scenarios sessions not found for date range: ' . $date_from . ' - ' . $date_to);
            $this->jsonResponse(array('reload' => true));
        }

        $webinars = DB_ORM::select('webinar')
            ->where('id', '=', $webinar_id)
            ->query();

        return $webinars;
    }

    public function action_mapReport4R()
    {
        $this->createReport('4R');
    }

    public function action_mapReportSCT()
    {
        $this->createReport('SCT');
    }

    public function action_mapReportPoll()
    {
        $this->createReport('Poll');
    }

    public function action_mapReportSJT()
    {
        $this->createReport('SJT');
    }

    public function action_reportByLatestSession()
    {
        $value = $this->request->param('id', '0') === '0' ? false : true;
        Session::instance()->set('report_by_latest_session', $value);
    }

    private function createReport($type)
    {
        $post = $this->request->post();
        $is_ajax = Arr::get($post, 'is_ajax', '0') === '0' ? false : true;
        $filename = Arr::get($post, 'filename', 'report');
        $scenarioId = $this->request->param('id', null);
        $mapId = $this->request->param('id2', null);
        $sectionId = $this->request->param('id3', null);
        $expertScenarioId = $this->request->param('id4', null);
        $latest = Session::instance()->get('report_by_latest_session', true);

        if ($scenarioId == null AND $mapId == null) {
            Request::initial()->redirect(URL::base() . 'webinarmanager/index');
        }

        if ($is_ajax) {
            Session::instance()->write();
        }

        switch ($type) {
            case 'SCT':
                $report = new Report_SCT(new Report_Impl_PHPExcel(), $filename);
                $report->add($mapId, $scenarioId, $expertScenarioId, $sectionId);
                $report->generate($latest);
                $report->get($is_ajax);
                die;
                break;
            case 'Poll':
                $report = new Report_Poll(new Report_Impl_PHPExcel(), $filename);
                $report->add($mapId, $scenarioId, '');
                $report->generate($latest);
                $report->get($is_ajax);
                die;
                break;
            case 'SJT':
                $report = new Report_SJT(new Report_Impl_PHPExcel(), $filename);
                $report->add($mapId, $scenarioId, $expertScenarioId, '');
                $report->generate($latest);
                $report->get($is_ajax);
                die;
                break;
            case '4R':
                $notIncludeUsers = DB_ORM::model('webinar_user')->getNotIncludedUsers($scenarioId);

                $report = new Report_4R(new Report_Impl_PHPExcel(), $filename);
                $report->add($mapId, $scenarioId, '', $notIncludeUsers);
                $report->generate();
                $report->get($is_ajax);
                die;
                break;
            default:
                Request::initial()->redirect(URL::base() . 'webinarmanager/index');
        }
    }

    public function action_play()
    {
        $scenarioId = $this->request->param('id', null);
        $step = $this->request->param('id2', null);
        $id = $this->request->param('id3', null);
        $type = $this->request->param('id4', null);

        if ($scenarioId AND $step) {
            Session::instance()->set('webinarId', $scenarioId);
            Session::instance()->set('step', $step);

            //------ redirect to scenario section ------//
            if ($type == 'section') {
                $section = DB_ORM::model('Map_Node_Section', array($id));
                $sectionNode = DB_ORM::select('Map_Node_Section_Node')->where('section_id', '=',
                    $id)->where('node_type', '=', 'in')->query()->fetch(0);

                if (!$sectionNode) {
                    Notice::add('Section must have start node', 'error');
                    Request::initial()->redirect($this->request->referrer());
                }

                Session::instance()->set('webinarSection', $type);
                Session::instance()->set('webinarSectionId', $id);
                Request::initial()->redirect(URL::base() . 'renderLabyrinth/go/' . $section->map->id . '/' . $sectionNode->node_id);
            }
            //------ end redirect to scenario section ------//
        }

        Request::initial()->redirect(URL::base() . 'renderLabyrinth/index/' . $id);
    }

    public function action_reset()
    {
        $scenarioId = $this->request->param('id', null);
        $deleteSessions = (bool)$this->request->param('id2', false);

        $dataStatisticsIds = DB_ORM::model('statistics_user_session')->getSessionByWebinarId($scenarioId);
        //doesn't include user_sessions that already saved to the statistics
        list($data, $ids) = DB_ORM::model('user_session')->getSessionByWebinarId($scenarioId, $dataStatisticsIds);

        if (count($data)) {
            // Save Statistics
            DB_ORM::model('statistics_user_session')->saveWebInarSession($data);
            DB_ORM::model('statistics_user_sessiontrace')->saveWebInarSessionTraces($ids);
            DB_ORM::model('statistics_user_response')->saveScenarioResponse($ids);
        }

        DB_ORM::model('qCumulative')->setResetByScenario($scenarioId);
        DB_ORM::model('webinar')->resetWebinar($scenarioId, $deleteSessions);

        if (!empty($scenarioId) && $deleteSessions) {
            Model_Leap_User_Note::deleteByWebinarId($scenarioId);
        }

        Request::initial()->redirect(URL::base() . 'webinarmanager/index');
    }

    public function action_updateInclude4R()
    {
        $id = $this->request->param('id', null);
        $include = $this->request->param('id2', null);

        DB_ORM::model('webinar_user')->updateInclude4R($id, $include);

        return true;

    }

    public function action_updateExpert()
    {
        $id = $this->request->param('id', null);
        $expert = $this->request->param('id2', null);

        DB_ORM::model('Webinar_User')->updateExpert($id, $expert);

        return true;
    }

    public function action_getMapByWebinar()
    {
        $wId = $this->request->param('id', null);
        $mapsId = array();
        foreach (DB_ORM::select('Webinar_Map')->where('webinar_id', '=', $wId)->query()->as_array() as $wObj) {
            $mapsId[] = $wObj->reference_id;
        }
        echo(json_encode($mapsId));
        exit;
    }

    public function action_getSectionAJAX()
    {
        $response = array();
        $mapId = $this->request->param('id');
        $sections = DB_ORM::model('Map_Node_Section')->getSectionsByMapId($mapId);

        foreach ($sections as $sectionObj) {
            $response[$sectionObj->name] = $sectionObj->id;
        }
        echo json_encode($response);
        exit;
    }

    public function action_getNodesAjax()
    {
        $mapId = $this->request->param('id');
        $result = array();

        foreach (DB_ORM::select('Map_Node')->where('map_id', '=', $mapId)->query()->as_array() as $nodeObj) {
            $result[$nodeObj->title] = $nodeObj->id;
        }
        ksort($result);
        exit(json_encode($result));
    }

    public function action_deleteNodeAjax()
    {
        DB_ORM::model('Webinar_PollNode')->deleteNode($this->request->param('id'));
    }

    public function action_visualEditor()
    {
        Breadcrumbs::clear();
        $scenarioId = $this->request->param('id');

        $this->templateData['enabledMaps'] = DB_ORM::model('map')->getAllEnabledMap(0, 'name', 'ASC');
        $this->templateData['steps'] = DB_ORM::model('Webinar_Step')->getScenarioSteps($scenarioId);
        $this->templateData['scenario'] = $this->getWebinar($scenarioId);
        $this->templateData['webinars'] = $this->getWebinars();
        $this->templateData['scenarioJSON'] = DB_ORM::model('Webinar')->generateJSON($scenarioId);
        $this->templateData['center'] = View::factory('webinar/canvas')->set('templateData', $this->templateData);
        $this->template->set('templateData', $this->templateData);
    }

    public function action_ajaxStepUpdate()
    {
        $scenarioId = $this->request->post('scenarioId');
        $data = $this->request->post('data');
        $data = json_decode($data, true);
        $steps = Arr::get($data, 'steps', array());
        $elements = Arr::get($data, 'elements', array());

        $dbSteps = DB_ORM::model('Webinar_Step')->getOnlyId($scenarioId);

        foreach ($elements as $idStep => $elementsData) {
            $labyrinths = Arr::get($elementsData, 'labyrinth', array());
            $section = Arr::get($elementsData, 'section', array());

            if (!($labyrinths OR $section)) {
                continue;
            }

            // ----- steps ----- //
            $newStepName = Arr::get($steps, $idStep, '');
            if (is_int($idStep)) {
                DB_ORM::model('Webinar_Step')->updateStep($idStep, $newStepName);
                unset($dbSteps[$idStep]);
            } else {
                $idStep = DB_ORM::model('Webinar_Step')->addStep($scenarioId, $newStepName);
            }
            // ----- end steps ----- //

            // ----- elements ----- //
            $dbElements = DB_ORM::model('Webinar_Map')->elementsForAjax($idStep);

            // update labyrinth
            foreach ($labyrinths as $idElement) {
                if (isset($dbElements[$idElement])) {
                    unset($dbElements[$idElement]);
                } else {
                    DB_ORM::model('Webinar_Map')->addMap($scenarioId, $idElement, $idStep, 'labyrinth');
                }
            }

            // update section
            foreach ($section as $idElement) {
                if (isset($dbElements[$idElement])) {
                    unset($dbElements[$idElement]);
                } else {
                    DB_ORM::model('Webinar_Map')->addMap($scenarioId, $idElement, $idStep, 'section');
                }
            }

            // delete remains
            foreach ($dbElements as $recordId) {
                DB_ORM::delete('Webinar_Map')->where('id', '=', $recordId)->execute();
            }
            // ----- end elements ----- //
        }

        // ----- delete steps ----- //
        foreach ($dbSteps as $id => $trash) {
            DB_ORM::model('Webinar_Step')->removeStep($id);
        }

        exit(DB_ORM::model('Webinar')->generateJSON($scenarioId));
    }

    public function action_resetCumulative()
    {
        $scenarioId = $this->request->param('id');
        $mapId = $this->request->param('id2');
        DB_ORM::update('User_Session')->set('notCumulative', 1)->where('webinar_id', '=', $scenarioId)->where('map_id',
            '=', $mapId)->execute();
        exit;
    }

    public function action_allConditions()
    {
        $conditions = DB_ORM::select('Conditions')->query()->as_array();
        $this->templateData['scenarios'] = DB_ORM::select('Webinar')->order_by('title')->query()->as_array();
        $this->templateData['conditions'] = $conditions;
        $this->templateData['assign'] = array();

        foreach ($conditions as $condition) {
            $assigns = DB_ORM::select('Conditions_Assign')->where('condition_id', '=',
                $condition->id)->query()->as_array();
            foreach ($assigns as $assign) {
                $scenario = DB_ORM::model('webinar', array($assign->scenario_id));
                $this->templateData['assign'][$condition->id][] = $scenario->title;
            }
        }
        $this->templateData['center'] = View::factory('conditions/index')->set('templateData', $this->templateData);

        $this->template->set('templateData', $this->templateData);
    }

    public function action_saveConditions()
    {
        $post = $this->request->post();
        $newConditionsName = Arr::get($post, 'newConditionsName', array());
        $newConditionsName = array_filter($newConditionsName);
        $newConditionsValue = Arr::get($post, 'newConditionsValue', array());
        $changedConditionsName = Arr::get($post, 'changedConditionsName', array());
        $changedConditionsValue = Arr::get($post, 'changedConditionsValue', array());
        $deletedConditions = Arr::get($post, 'deletedConditions', array());
        $deletedConditions = array_filter($deletedConditions);
        $conditionsModel = DB_ORM::model('Conditions');

        foreach ($newConditionsName as $key => $conditionsName) {
            $conditionsModel->add($conditionsName, $newConditionsValue[$key]);
        }

        foreach ($changedConditionsName as $id => $conditionsName) {
            $conditionsModel->update($conditionsName, $changedConditionsValue[$id], $id);
        }

        foreach ($deletedConditions as $id) {
            $conditionsModel->deleteRecord($id);
        }

        Request::initial()->redirect(URL::base() . 'webinarManager/allConditions');
    }

    public function action_editCondition()
    {
        $conditionId = $this->request->param('id');

        $this->templateData['condition'] = DB_ORM::model('Conditions', array($conditionId));
        $this->templateData['assign'] = DB_ORM::select('Conditions_Assign')->where('condition_id', '=',
            $conditionId)->query()->as_array();
        $this->templateData['scenarios'] = DB_ORM::select('Webinar')->order_by('title')->query()->as_array();
        $this->templateData['center'] = View::factory('conditions/edit')->set('templateData', $this->templateData);
        $this->template->set('templateData', $this->templateData);
    }

    public function action_editConditionSave()
    {
        $conditionId = $this->request->param('id');
        $post = $this->request->post();
        $newAssign = Arr::get($post, 'newConditionAssign', array());
        $newAssign = array_unique($newAssign);
        $changedAssign = Arr::get($post, 'changedConditionAssign', array());
        $deleteAssign = Arr::get($post, 'deleteAssign', array());
        $assignModel = DB_ORM::model('Conditions_Assign');
        $startValue = DB_ORM::model('Conditions', array($conditionId))->startValue;

        foreach ($newAssign as $scenarioId) {
            if ($scenarioId != 'None') {
                $assignModel->add($conditionId, $scenarioId, $startValue);
            }
        }

        foreach ($changedAssign as $id => $scenarioId) {
            $assignModel->update($id, $scenarioId);
        }

        foreach ($deleteAssign as $id) {
            $assignModel->deleteRecord($id);
        }

        Request::initial()->redirect($this->request->referrer());
    }

    public function action_mapsGrid()
    {
        $scenarioId = $this->request->param('id');
        $conditionId = $this->request->param('id2');
        $scenarioElements = DB_ORM::select('Webinar_Map')->where('webinar_id', '=', $scenarioId)->query()->as_array();

        $this->templateData['scenario'] = DB_ORM::model('Webinar', array($scenarioId));
        $this->templateData['condition'] = DB_ORM::model('Conditions', array($conditionId));

        foreach ($scenarioElements as $element) {
            $elementObj = ($element->which == 'labyrinth')
                ? DB_ORM::model('Map', array($element->reference_id))
                : DB_ORM::model('Map_Node_Section', array($element->reference_id));

            $nodesTitle = 'Nodes of ' . $element->which . '\'s \'' . $elementObj->name . '\'';

            if ($element->which == 'labyrinth') {
                $this->templateData['nodes'][$nodesTitle] = DB_ORM::select('Map_Node')->where('map_id', '=',
                    $element->reference_id)->query()->as_array();
            } else {
                $noseSection = DB_ORM::select('Map_Node_Section_Node')->where('section_id', '=',
                    $element->reference_id)->query()->as_array();
                foreach ($noseSection as $obj) {
                    $this->templateData['nodes'][$nodesTitle][] = $obj->node;
                }
            }
        }

        $conditionsChange = DB_ORM::select('Conditions_Change')->where('scenario_id', '=',
            $scenarioId)->where('condition_id', '=', $conditionId)->query()->as_array();
        $this->templateData['existingNode'] = array();
        foreach ($conditionsChange as $obj) {
            $this->templateData['existingNode'][$obj->node_id]['value'] = $obj->value;
            $this->templateData['existingNode'][$obj->node_id]['appears'] = $obj->appears;
            $this->templateData['existingNode'][$obj->node_id]['id'] = $obj->id;
        }

        $this->templateData['center'] = View::factory('conditions/mapsGrid')->set('templateData', $this->templateData);
        $this->template->set('templateData', $this->templateData);
    }

    public function action_saveMapsGrid()
    {
        $conditionId = $this->request->param('id');
        $scenarioId = $this->request->param('id2');
        $post = $this->request->post();
        $newNodes = Arr::get($post, 'newNodes', array());
        $existNodes = Arr::get($post, 'existNodes', array());

        foreach ($newNodes as $nodeId => $data) {
            DB_ORM::model('Conditions_Change')->add($conditionId, $scenarioId, $nodeId, Arr::get($data, 'value', 0),
                Arr::get($data, 'appears', 0));
        }

        foreach ($existNodes as $id => $data) {
            DB_ORM::model('Conditions_Change')->update($id, Arr::get($data, 'value', 0), Arr::get($data, 'appears', 0));
        }

        Request::initial()->redirect($this->request->referrer());
    }

    public function action_resetCondition()
    {
        $assignId = $this->request->param('id');
        $conditionId = $this->request->param('id2');
        $value = DB_ORM::model('Conditions', array($conditionId))->startValue;

        DB_ORM::model('Conditions_Assign')->resetValue($assignId, $value);

        Request::initial()->redirect($this->request->referrer());
    }

    //save settings\\
    //ajax
    public function action_saveChatsOrder()
    {
        $user = Auth::instance()->get_user();
        $webinar_id = (int)$this->request->param('id', null);

        if (!empty($_POST['chat']) && count($_POST['chat']) > 0 && !empty($webinar_id)) {
            $settings = $user->getSettings();
            $chats_order = $_POST['chat'];
            foreach ($chats_order as $order => $chat_id) {
                $chat_id = self::$chat_id_template . $chat_id;
                $settings['webinars'][$webinar_id]['chats'][$chat_id]['order'] = $order;
            }
            $user->saveSettings($settings);
        }
        die;
    }

    //ajax
    public function action_saveChosenUser()
    {
        $user = Auth::instance()->get_user();
        $webinar_id = (int)$this->request->param('id', null);
        $chat_id = $this->request->param('id2', null);
        $user_id = (int)$this->request->param('id3', null);

        if (!empty($webinar_id) && !empty($chat_id)) {
            $settings = $user->getSettings();
            $settings['webinars'][$webinar_id]['chats'][$chat_id]['user_id'] = $user_id;
            $user->saveSettings($settings);
        }
        die;
    }
    //end save settings\\

    //ajax
    public function action_getChatMessages()
    {
        $session_id = (int)$this->request->param('id', null);
        $question_id = (int)$this->request->param('id2', null);
        $chat_session_id = (int)$this->request->param('id3', null);
        $from_labyrinth = (int)$this->request->param('id4', null);
        $node_id = (int)$this->request->param('id5', null);

        $responses = DB_ORM::model('User_Response')->getTurkTalkResponse($question_id, $session_id, $chat_session_id,
            $node_id);
        $result = '';
        if (!empty($responses)) {
            $result['response_type'] = 'text';
            $result['response_text'] = '';
            $result['waiting_for_response'] = false;
            $bell_counter = 0;
            foreach ($responses as $response) {

                // first (init) response for history and to start new chat session
                if (in_array($response['type'], array('init'))) {
                    continue;
                }

                if ($response['type'] === 'bell') {
                    $bell_counter++;
                    continue;
                }

                if ($from_labyrinth == 1) {
                    if ($response['type'] === 'redirect') {
                        Session::instance()->set('is_redirected', true);
                        $result['response_type'] = 'redirect';
                        $url = URL::base(true) . 'renderLabyrinth/go/' . $response['text']['map_id'] . '/' . $response['text']['node_id'];
                        $result['response_text'] = $url;
                        die(json_encode($result));
                    }
                }

                $isLearner = $response['role'] === 'learner' ? true : false;
                $online = (isset($response['ping']) && $response['ping'] >= (time() - 4));

                if ($isLearner && $online) {
                    $result['waiting_for_response'] = true;
                    $result['waiting_time'] = time() - $response['created_at'];
                } else {
                    $result['waiting_for_response'] = false;
                }

                if ($from_labyrinth) {
                    $name = $isLearner ? 'You' : 'Turker';
                } else {
                    $name = !$isLearner ? 'You' : 'User';
                }

                ob_start();
                ?>
                <div class="message" style="padding:10px;border-bottom:1px solid #eee;">
                    <div>
                        <b><?php echo $name ?>:</b>
                        <?php echo is_array($response['text']) ? json_encode($response['text']) : $response['text'] ?>
                    </div>
                </div>
                <?php
                $response_text = ob_get_clean();
                $result['response_text'] .= $response_text;
            }

            $result['responses_counter'] = (string)count($responses);

            if ($from_labyrinth == 1) {
                Session::instance()->set('bell_counter', $bell_counter);
            }

            //if the last one response has role 'learner' and current user is learner, then save time of last view

            do {
                $last_response = array_pop($responses);
            } while ($last_response['type'] === 'bell' && !empty($responses));

            if ($last_response['role'] === 'learner' && $from_labyrinth) {
                $last_response_obj = DB_ORM::model('user_response', array((int)$last_response['id']));
                $response_arr = json_decode($last_response_obj->response, true);
                $key = key($response_arr);
                $response_arr[$key]['ping'] = time();
                DB_ORM::update('User_Response')
                    ->set('response', json_encode($response_arr))
                    ->where('id', '=', (int)$last_response['id'])
                    ->execute();
            }

            $result = json_encode($result);
        }
        die($result);
    }

    //ajax
    public function action_checkBell()
    {
        $result = array('need_bell' => false);
        $current_bell_counter = Session::instance()->get('current_bell_counter', 0);
        $bell_counter = Session::instance()->get('bell_counter', 0);

        if ($bell_counter > $current_bell_counter) {
            $result['need_bell'] = true;
        }

        Session::instance()->set('current_bell_counter', $bell_counter);

        die(json_encode($result));
    }

    //ajax
    public function action_getCurrentNode()
    {
        $result = '';
        $user_id = (int)$this->request->param('id', null);
        $webinar_id = (int)$this->request->param('id2', null);

        if (empty($user_id) || empty($webinar_id)) {
            return false;
        }

        $last_trace = DB_ORM::model('user')->getLastSessionTrace($user_id, $webinar_id);

        if (!empty($last_trace)) {
            $session_id = (int)$last_trace->session_id;
            $node_id = (int)$last_trace->node_id;
            $node = DB_ORM::model('Map_Node', array($node_id));
            $node_title = $node->getNodeTitle();

            //get $question_id
            $question_id = null;
            $responses = DB_ORM::model('User_Response')->getResponsesBySessionAndNode($session_id, $node_id);
            if (!empty($responses) && count($responses) > 0) {
                foreach ($responses as $response) {
                    $question = DB_ORM::model('Map_Question', array((int)$response->question_id));
                    if ($question->entry_type_id == 11) {
                        $question_id = $question->id;
                        break;
                    }
                }
            }
            //end get $question_id

            $result = array(
                'session_id' => $session_id,
                'node_id' => $node_id,
                'node_title' => $node_title,
                'question_id' => $question_id
            );
            $result = json_encode($result);
        }
        die($result);
    }

    //ajax
    public function action_getNodeLinks()
    {
        $result = '';
        $node_id = (int)$this->request->param('id', null);
        $session_id = (int)$this->request->param('id2', null);
        if (empty($node_id) || empty($session_id)) {
            die($result);
        }

        /** @var Model_Leap_Map_Node $node */
        $node = DB_ORM::model('Map_Node', array($node_id));
        if (!empty($node)) {
            $links = $node->links;
            if ($links->count() > 0) {
                $result = '<option value="">- Redirect to... -</option>';
                foreach ($links as $link) {

                    if ($link->node_2->undo && $link->node_2->isVisitedDuringSession($session_id)) {
                        continue;
                    }

                    $value = array('map_id' => $link->map_id, 'node_id' => $link->node_id_2);
                    $value = json_encode($value);
                    $node_title = trim($link->node_2->title);
                    $result .= '<option value=\'' . $value . '\'>' . $node_title . '</option>';
                }
            }

        }
        die($result);
    }

    private function getWebinars()
    {
        $user = Auth::instance()->get_user();
        $userType = $user->type->name;

        return ($userType == 'superuser' || $userType == 'Director')
            ? DB_ORM::model('webinar')->getAllWebinars()
            : DB_ORM::model('webinar')->getAllWebinars($user->id);
    }

    private function getWebinar($webinar_id)
    {
        if (!empty($webinar_id)) {
            $scenario = DB_ORM::model('Webinar', array($webinar_id));
            $scenario_id = $scenario->id;
        }

        return !empty($scenario_id) ? $scenario : null;
    }

    private function addChats($chats = array(), $chat_quantity = null)
    {
        if (empty($chat_quantity)) {
            $chat_quantity = self::$chat_quantity;
        }

        for ($i = 1; $i <= $chat_quantity; ++$i) {
            $chat_id = self::$chat_id_template . $i;
            if (!array_key_exists($chat_id, $chats)) {
                $chats[$chat_id]['order'] = $i;
                $chats[$chat_id]['user_id'] = 0;
            } else {
                if (!isset($chats[$chat_id]['order'])) {
                    $chats[$chat_id]['order'] = $i;
                }
            }
        }

        return $chats;
    }
}