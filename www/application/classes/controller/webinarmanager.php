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

class Controller_WebinarManager extends Controller_Base {
    public static $chat_id_template = 'chat-';

    public function before()
    {
        parent::before();
        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Scenario Management'))->set_url(URL::base().'webinarManager'));
    }

    public function action_index()
    {
        Breadcrumbs::clear();

        $webinar_id = (int)$this->request->param('id', 0);
        $webinar_id = !empty($webinar_id) ? $webinar_id : Session::instance()->get('webinar_id', 0);
        $webinar = $this->getWebinar($webinar_id);

        if(!empty($webinar)){
            Session::instance()->set('webinar_id', $webinar->id);
            Request::initial()->redirect(URL::base().'webinarmanager/progress/'.$webinar->id);
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

        if(!empty($settings['webinars'][$webinar_id]['chats'])){
            $chats = $settings['webinars'][$webinar_id]['chats'];
            uasort($chats, function($a, $b){
                if ($a['order'] == $b['order']) {
                    return 0;
                }
                return ($a['order'] < $b['order']) ? -1 : 1;
            });
        }else{
            $chats = array();
            for($i = 1; $i < 9; ++$i){
                $chat_id = self::$chat_id_template.$i;
                $chats[$chat_id]['order'] = $i;
            }
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

        $this->templateData['users']    = DB_ORM::model('user')->getAllUsersAndAuth('ASC');
        $this->templateData['groups']   = DB_ORM::model('group')->getAllGroups();
        $this->templateData['maps']     = (Auth::instance()->get_user()->type->name == 'superuser')
            ? DB_ORM::model('map')->getAllEnabledMap()
            : DB_ORM::model('map')->getAllEnabledAndAuthoredMap(Auth::instance()->get_user()->id, 0, true);
        // ------ Add sections ------- //
        foreach ($this->templateData['maps'] as $map)
        {
            foreach (DB_ORM::select('Map_Node_Section')->where('map_id', '=', $map->id)->query()->as_array() as $section)
            {
                $section->name = $map->name.'. Section: '.$section->name;
                $this->templateData['maps'][] = $section;
            }
        }
        // ------ End add sections ------- //
        $this->templateData['forums']   = DB_ORM::model('dforum')->getAllForums(1,0); // Type of Sort, 1 = Name , 0 - ASC
        $this->templateData['center']   = View::factory('webinar/webinar')->set('templateData', $this->templateData);
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
        foreach ($MapsObj as $map)
        {
            foreach (DB_ORM::select('Map_Node_Section')->where('map_id', '=', $map->id)->query()->as_array() as $section)
            {
                $this->templateData['sections'][$section->id] = $section->map_id;
                $section->name = $map->name;
                $this->templateData['maps'][] = $section;
            }
        }
        // ------ End add sections ------- //

        // ------ Add poll node, end nodes of map ------- //
        foreach (DB_ORM::select('Webinar_PollNode')->where('webinar_id', '=', $webinarId)->query()->as_array() as $obj)
        {
            $nodeObj = DB_ORM::model('Map_Node', array($obj->node_id));
            $mapId   = $nodeObj->map->id;
            $this->templateData[$mapId]['pollNodes'][$obj->node_id] = $obj->time;

            if (empty($this->templateData[$mapId]['mapNodes']))
            {
                foreach (DB_ORM::model('Map_Node')->getAllNode($mapId) as $nodeObj)
                {
                    $this->templateData[$mapId]['mapNodes'][$nodeObj->id] = $nodeObj->title;
                }
            }
        }
        // ------ End add poll node ------- //

        $webinar = $this->getWebinar($webinarId);
        $this->templateData['macros_list'] = $webinar->macros;
        $this->templateData['webinar'] = $webinar;
        $this->templateData['webinars'] = $this->getWebinars();

        $this->templateData['experts']  = array();
        foreach (DB_ORM::select('Webinar_User')->where('webinar_id', '=', $webinarId)->query()->as_array() as $wUserObj)
        {
            if ($wUserObj->expert == 1) $this->templateData['experts'][] = $wUserObj->user_id;
        }

        $existUsers = array();
        if(count($this->templateData['webinar']->users) > 0)
        {
            foreach($this->templateData['webinar']->users as $webinarUser) {
                $existUsers[] = $webinarUser->user_id;
            }
        }

        $existGroups = array();
        if($this->templateData['webinar'] != null && count($this->templateData['webinar']->groups) > 0) {
            foreach($this->templateData['webinar']->groups as $webinarGroup) {
                $existGroups[] = $webinarGroup->group_id;
            }
        }

        $this->templateData['users']  = DB_ORM::model('user')->getAllUsersAndAuth('ASC', $existUsers);
        $this->templateData['groups'] = DB_ORM::model('group')->getAllGroups('ASC', $existGroups);

        $allUsers = DB_ORM::model('user')->getAllUsersAndAuth('ASC');
        if($allUsers != null && count($allUsers) > 0) {
            foreach($allUsers as $user) {
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
        $step       = $this->request->param('id2', null);
        $dateId     = $this->request->param('id3', null);

        if ($scenarioId == null || $dateId == null) Request::initial()->redirect(URL::base().'webinarmanager/index');

        $scenarioStepMap = array();
        $scenarioData    = array();
        $usersMap        = array();
        $scenario        = $this->getWebinar($scenarioId);

        if (count($scenario->users) AND count($scenario->maps))
        {
            foreach ($scenario->users as $scenarioUser)
            {
                $userId = $scenarioUser->user_id;
                if ( ! isset($usersMap[$userId])) $usersMap[$userId] = $scenarioUser->user;

                foreach($scenario->maps as $scenarioMap)
                {
                    $scenarioStep     = $scenarioMap->step;
                    $scenarioWhichId  = $scenarioMap->reference_id;
                    $scenarioWhich    = $scenarioMap->which;

                    if ($scenarioWhich == 'labyrinth') $scenarioData[$userId][$scenarioStep][$scenarioWhichId]['map'] = DB_ORM::model('Map', array((int)$scenarioWhichId));
                    elseif ($scenarioWhich == 'section') $scenarioData[$userId][$scenarioStep][$scenarioWhichId]['section'] = DB_ORM::model('Map_Section', array((int)$scenarioWhichId));
                    $scenarioData[$userId][$scenarioStep][$scenarioWhichId]['user']   = $scenarioUser->user;
                    $scenarioData[$userId][$scenarioStep][$scenarioWhichId]['status'] = $scenarioStep <= $step
                        ? DB_ORM::model('statistics_user_session')->isUserFinishMap($scenarioWhichId, $userId, $scenarioWhich, $scenario->id, $step, $dateId)
                        : 0;
                }
            }

            if(count($scenario->steps))
            {
                foreach($scenario->steps as $scenarioStep)
                {
                    $scenarioStepMap[$scenarioStep->id] = $scenarioStep;
                }
            }
        }

        $this->templateData['webinar']  = $scenario;
        $this->templateData['webinars'] = $this->getWebinars();
        $this->templateData['webinarStepMap'] = $scenarioStepMap;

        foreach ($this->templateData['webinar']->users as $user)
        {
            DB_ORM::model('webinar_user')->updateInclude4R($user->id, 1);

            $this->templateData['includeUsersData'][$user->user_id] = $user->id;
            $this->templateData['includeUsers'][$user->user_id]     = $user->include_4R;
        }

        $this->templateData['usersMap']     = $usersMap;
        $this->templateData['webinarData']  = $scenarioData;
        $this->templateData['step']         = $step;
        $this->templateData['dateId']       = $dateId;

        foreach(DB_ORM::model('user')->getAllUsersAndAuth('ASC') as $user)
        {
            $this->templateData['usersAuthMap'][$user['id']] = $user;
        }

        $this->templateData['center'] = View::factory('webinar/showStats')->set('templateData', $this->templateData);
        $this->template->set('templateData', $this->templateData);

        $date  = date('Y-m-d H:i:s', DB_ORM::model('statistics_user_datesave', array($dateId))->date_save);
        Breadcrumbs::add(Breadcrumb::factory()->set_title('Statistics for '.$scenario->title)->set_url(URL::base().'webinarManager/statistics/'.$scenarioId));
        Breadcrumbs::add(Breadcrumb::factory()->set_title($date)->set_url(URL::base().'webinarManager/progress'));
    }

    public function action_progress()
    {
        Breadcrumbs::clear();
        $webinarId = $this->request->param('id', null);

        if ($webinarId == null) Request::initial()->redirect(URL::base().'webinarmanager/index');

        $wData          = array();
        $usersMap       = array();
        $webinar        = $this->getWebinar($webinarId);
        $webinarStepMap = array();

        if($webinar != null && count($webinar->users) && count($webinar->maps) > 0)
        {
            foreach($webinar->users as $wUser)
            {
                $wUserId = $wUser->user_id;
                $userType =DB_ORM::model('User', array($wUserId))->type_id;
                if( ! isset($usersMap[$wUserId])) $usersMap[$wUserId] = $wUser->user;

                $this->templateData['includeUsersData'][$wUserId] = $wUser->id;
                $this->templateData['includeUsers'][$wUserId] = $wUser->include_4R;
                if ($userType != 1) $this->templateData['experts'][$wUserId] = $wUser->expert;

                foreach ($webinar->maps as $wMapObj)
                {
                    $wStep          = $wMapObj->step;
                    $id             = $wMapObj->reference_id;
                    $wCurrentStep   = $webinar->current_step;
                    $prefix         = '';

                    if ($wMapObj->which == 'labyrinth') $wData[$wUserId][$wStep][$id]['map'] = DB_ORM::model('map', array((int)$id));
                    else
                    {
                        $prefix = 's';
                        $sectionObj = DB_ORM::model('Map_Node_Section', array($id));
                        $sectionObj->id     = $sectionObj->map_id;
                        $sectionObj->name   = 'Section: '.$sectionObj->name;
                        $wData[$wUserId][$wStep][$prefix.$id]['map'] = $sectionObj;
                    }

                    $wData[$wUserId][$wStep][$prefix.$id]['status'] = ($wStep <= $wCurrentStep)
                        ? DB_ORM::model('user_session')->isUserFinishMap($id, $wUserId, $wMapObj->which, $webinar->id, $wCurrentStep)
                        : 0;
                    $last_trace = DB_ORM::model('User')->getLastSessionTrace($wUserId, $webinar->id, $id);
                    if(!empty($last_trace)){
                        $last_node_id = $last_trace->node_id;
                        $last_node_title = $last_trace->node->title;
                    }else{
                        $last_node_id = null;
                        $last_node_title = null;
                    }
                    $wData[$wUserId][$wStep][$prefix.$id]['node_id'] = $last_node_id;
                    $wData[$wUserId][$wStep][$prefix.$id]['node_title'] = $last_node_title ;
                    $wData[$wUserId][$wStep][$prefix.$id]['user']   = $wUser->user;
                }
            }
            if(count($webinar->steps))
            {
                foreach($webinar->steps as $webinarStep)
                {
                    $webinarStepMap[$webinarStep->id] = $webinarStep;
                }
            }
        }

        $this->templateData['scenario']       = DB_ORM::select('Webinar')->query()->as_array();
        $this->templateData['webinarStepMap'] = $webinarStepMap;
        $this->templateData['webinar']        = $webinar;
        $this->templateData['webinars']       = $this->getWebinars();
        $this->templateData['usersMap']       = $usersMap;
        $this->templateData['webinarData']    = $wData;

        $allUsers = DB_ORM::model('user')->getAllUsersAndAuth('ASC');
        if(count($allUsers))
        {
            foreach($allUsers as $user)
            {
                $this->templateData['usersAuthMap'][$user['id']] = $user;
            }
        }

        $this->templateData['center'] = View::factory('webinar/statistic')->set('templateData', $this->templateData);
        $this->template->set('templateData', $this->templateData);
    }

    public function action_statistic() {
        Request::initial()->redirect(URL::base() . 'webinarmanager/progress/' . $this->request->param('id', null));
    }

    public function action_statistics() {
        $webinarId   = $this->request->param('id', null);

        if($webinarId == null) {
            Request::initial()->redirect(URL::base() . 'webinarmanager/index');
        } else {
            $this->templateData['history'] = DB_ORM::model('statistics_user_session')->getDateSaveByWebinarId($webinarId);

            $webinar = $this->getWebinar($webinarId);

            $this->templateData['webinar']     = $webinar;
            $this->templateData['webinars']    = $this->getWebinars();
            Breadcrumbs::add(Breadcrumb::factory()->set_title('Statistics for ' . $webinar->title)->set_url(URL::base() . 'webinarManager/statistics'));

            $this->templateData['center'] = View::factory('webinar/all');
            $this->templateData['center']->set('templateData', $this->templateData);
            $this->template->set('templateData', $this->templateData);
        }
    }

    public function action_publishStep()
    {
        $webinarId   = $this->request->param('id', null);
        $webinarStep = $this->request->param('id2', null);
        $dateId      = $this->request->param('id3', null);
        $webinar     = DB_ORM::model('webinar', array((int)$webinarId));

        if($webinar != null && $webinarStep != null && $webinarStep > 0)
        {
            $jsonObject = ($webinar->publish == null) ? array() : json_decode($webinar->publish);

            if(!in_array($webinarId.'-'.$webinarStep, $jsonObject))
            {
                $jsonObject[] = $webinarId.'-'.$webinarStep;

                $webinar->publish = json_encode($jsonObject);
                $webinar->save();

                if ($webinar->forum_id > 0) DB_ORM::model('dforum_messages')->createMessage($webinar->forum_id, '<a href="' . URL::base() . 'webinarManager/stepReport4R/' . $webinarId . '/' . $webinarStep .'/'. $dateId. '">Step ' . $webinarStep . ' 4R Report</a>');
            }

            Request::initial()->redirect(URL::base() . 'webinarmanager/showStats/' . $webinarId . '/' . $webinarStep . '/' . $dateId);
        }
        else Request::initial()->redirect(URL::base() . 'webinarmanager/index');
    }

    public function action_render()
    {
        $scenarioId = $this->request->param('id', null);
        $scenario   = DB_ORM::model('webinar', array($scenarioId));

        if(count($scenario->steps)) {
            foreach($scenario->maps as $scenarioMap) {
                $this->templateData['mapsMap'][$scenarioMap->step][$scenarioMap->reference_id] = ($scenarioMap->step <= $scenario->current_step)
                    ? DB_ORM::model('user_session')->isUserFinishMap($scenarioMap->reference_id, Auth::instance()->get_user()->id, $scenarioMap->which, $scenarioId, $scenario->current_step)
                    : 0;
            }
        }

        $this->templateData['scenario']  = $scenario;
        $this->templateData['center']   = View::factory('webinar/render')->set('templateData', $this->templateData);
        $this->template->set('templateData', $this->templateData);
    }

    public function action_delete()
    {
        DB_ORM::model('webinar')->deleteWebinar($this->request->param('id', null));
        Request::initial()->redirect(URL::base().'webinarmanager/index');
    }

    public function action_save()
    {
        DB_ORM::model('webinar')->saveWebinar($this->request->post());
        Request::initial()->redirect(URL::base().'webinarmanager/index');
    }

    public function action_changeStep()
    {
        $scenarioId = $this->request->param('id' , null);
        $step       = $this->request->param('id2', null);
        $redirect   = $this->request->param('id3', null);

        DB_ORM::model('webinar')->changeWebinarStep($scenarioId, $step);

        if ($redirect == null){
            Request::initial()->redirect(URL::base().'webinarmanager/index/'.$scenarioId);
        } else {
            Request::initial()->redirect(URL::base().'webinarmanager/progress/'.$scenarioId);
        }
    }

    public function action_stepReport4R()
    {
        $webinarId = $this->request->param('id', null);
        $stepKey   = $this->request->param('id2', null);
        $dateId    = $this->request->param('id3', null);

        if ($webinarId != null && $webinarId > 0 && $stepKey != null && $stepKey > 0) {
            $webinar = DB_ORM::model('webinar', array((int)$webinarId));
            $isExistAccess = false;

            if (Auth::instance()->get_user()->id == $webinar->author_id || Auth::instance()->get_user()->type->name == 'superuser') {
                $isExistAccess = true;
            }

            if ( ! $isExistAccess && $webinar->publish != null) {
                $jsonObject = json_decode($webinar->publish);

                $isExistAccess = in_array($webinarId . '-' . $stepKey, $jsonObject);
            }

            if ($isExistAccess) {
                $report  = new Report_4R(new Report_Impl_PHPExcel(), $webinar->title);
                $notIncludUsers = DB_ORM::model('webinar_user')->getNotIncludedUsers($webinar->id);
                if($webinar != null && count($webinar->maps) > 0) {
                    foreach($webinar->maps as $webinarMap) {
                        if($webinarMap->step == $stepKey) {
                            $mapId = ($webinarMap->which == 'labyrinth')
                                ? $webinarMap->reference_id
                                : DB_ORM::model('Map_Node_Section', array($webinarMap->reference_id))->map_id;
                            $report->add($mapId, $webinar->id, $stepKey, $notIncludUsers,$dateId);
                        }
                    }
                }
                $report->generate();

                $report->get();
            } else {
                Request::initial()->redirect(URL::base() . 'home/index');
            }
        } else {
            Request::initial()->redirect(URL::base() . 'webinarmanager/index');
        }
    }

    public function action_stepReportSCT()
    {
        $webinarId          = $this->request->param('id', null);
        $stepKey            = $this->request->param('id2', null);
        $expertWebinarId    = $this->request->param('id3', null);

        if ($webinarId == null AND $stepKey) Request::initial()->redirect(URL::base().'webinarmanager/index');

        $webinar = DB_ORM::model('webinar', array((int)$webinarId));
        $isExistAccess = false;

        if (Auth::instance()->get_user()->id == $webinar->author_id || Auth::instance()->get_user()->type->name == 'superuser') $isExistAccess = true;

        if ( ! $isExistAccess AND $webinar->publish != null)
        {
            $jsonObject = json_decode($webinar->publish);
            $isExistAccess = in_array($webinarId . '-' . $stepKey, $jsonObject);
        }

        if($isExistAccess)
        {
            $report  = new Report_SCT(new Report_Impl_PHPExcel(), $webinar->title);
            if($webinar != null && count($webinar->maps) > 0)
            {
                foreach($webinar->maps as $webinarMap)
                {
                    if($webinarMap->step == $stepKey)
                    {
                        // if labyrinth, else section
                        if ($webinarMap->which == 'labyrinth')
                        {
                            $mapId = $webinarMap->reference_id;
                            $sectionId = false;
                        }
                        else
                        {
                            $mapId = DB_ORM::model('Map_Node_Section', array($webinarMap->reference_id))->map_id;
                            $sectionId = $webinarMap->reference_id;
                        }
                        $report->add($mapId, $webinarId, $expertWebinarId, $sectionId);
                    }
                }
            }
            $report->generate();
            $report->get();
        }
        else Request::initial()->redirect(URL::base().'home/index');
    }

    public function action_stepReportSJT()
    {
        $scenarioId          = $this->request->param('id', null);
        $stepKey             = $this->request->param('id2', null);
        $expertScenarioId    = $this->request->param('id3', null);

        if ($scenarioId == null AND $stepKey) Request::initial()->redirect(URL::base().'webinarmanager/index');

        $scenario = DB_ORM::model('webinar', array($scenarioId));
        $isExistAccess = ((Auth::instance()->get_user()->id == $scenario->author_id) OR (Auth::instance()->get_user()->type->name == 'superuser'));

        if ( ! $isExistAccess AND $scenario->publish) {
            $jsonObject = json_decode($scenario->publish);
            $isExistAccess = in_array($scenarioId . '-' . $stepKey, $jsonObject);
        }

        if ($isExistAccess) {
            $report  = new Report_SJT(new Report_Impl_PHPExcel(), $scenario->title);
            if(count($scenario->maps)) {
                foreach($scenario->maps as $scenarioMap) {
                    if($scenarioMap->step == $stepKey) {
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
            $report->generate();
            $report->get();
        } else {
            Request::initial()->redirect(URL::base().'home/index');
        }
    }

    public function action_stepReportPoll()
    {
        $webinarId          = $this->request->param('id', null);
        $stepKey            = $this->request->param('id2', null);

        if ($webinarId == null AND $stepKey != null) Request::initial()->redirect(URL::base().'webinarmanager/index');

        $webinar = DB_ORM::model('webinar', array((int)$webinarId));
        $isExistAccess = false;

        if (Auth::instance()->get_user()->id == $webinar->author_id OR Auth::instance()->get_user()->type->name == 'superuser') $isExistAccess = true;

        if ( ! $isExistAccess AND $webinar->publish != null)
        {
            $jsonObject = json_decode($webinar->publish);
            $isExistAccess = in_array($webinarId . '-' . $stepKey, $jsonObject);
        }

        if($isExistAccess)
        {
            $report         = new Report_Poll(new Report_Impl_PHPExcel(), $webinar->title);
            if(count($webinar->maps) > 0)
            {
                foreach($webinar->maps as $webinarMap)
                {
                    if($webinarMap->step == $stepKey)
                    {
                        $mapId = ($webinarMap->which == 'labyrinth')
                            ? $webinarMap->reference_id
                            : DB_ORM::model('Map_Node_Section', array($webinarMap->reference_id))->map_id;
                        $report->add($mapId, $webinarId);
                    }
                }
            }
            $report->generate();
            $report->get();
        }
        else Request::initial()->redirect(URL::base().'home/index');
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

    private function createReport($type)
    {
        $scenarioId         = $this->request->param('id', null);
        $mapId              = $this->request->param('id2', null);
        $sectionId          = $this->request->param('id3', null);
        $expertScenarioId   = $this->request->param('id4', null);

        if ($scenarioId == null AND $mapId == null) Request::initial()->redirect(URL::base().'webinarmanager/index');

        switch ($type)
        {
            case 'SCT':
                $report = new Report_SCT(new Report_Impl_PHPExcel(), 'SCT Report '.DB_ORM::model('map', array((int)$mapId))->name);
                $report->add($mapId, $scenarioId, $expertScenarioId, $sectionId);
                $report->generate();
                $report->get();
            break;
            case 'Poll':
                $report = new Report_Poll(new Report_Impl_PHPExcel(), 'Poll '.DB_ORM::model('map', array((int)$mapId))->name);
                $report->add($mapId, $scenarioId, '');
                $report->generate();
                $report->get();
            break;
            case 'SJT':
                $report = new Report_SJT(new Report_Impl_PHPExcel(), 'SJT '.DB_ORM::model('map', array((int)$mapId))->name);
                $report->add($mapId, $scenarioId, $expertScenarioId, '');
                $report->generate();
                $report->get();
                break;
            case '4R':
                $notIncludeUsers = DB_ORM::model('webinar_user')->getNotIncludedUsers($scenarioId);

                $report  = new Report_4R(new Report_Impl_PHPExcel(), DB_ORM::model('map', array((int)$mapId))->name);
                $report->add($mapId, $scenarioId, '' , $notIncludeUsers);
                $report->generate();
                $report->get();
            break;
            default: Request::initial()->redirect(URL::base().'webinarmanager/index');
        }
    }

    public function action_play()
    {
        $scenarioId = $this->request->param('id', null);
        $step       = $this->request->param('id2', null);
        $id         = $this->request->param('id3', null);
        $type       = $this->request->param('id4', null);

        if ($scenarioId AND $step) {
            Session::instance()->set('webinarId', $scenarioId);
            Session::instance()->set('step', $step);

            //------ redirect to scenario section ------//
            if ($type == 'section') {
                $section     = DB_ORM::model('Map_Node_Section', array($id));
                $sectionNode = DB_ORM::select('Map_Node_Section_Node')->where('section_id', '=', $id)->where('node_type', '=', 'in')->query()->fetch(0);

                if ( ! $sectionNode) {
                    Notice::add('Section must have start node', 'error');
                    Request::initial()->redirect($this->request->referrer());
                }

                Session::instance()->set('webinarSection', $type);
                Session::instance()->set('webinarSectionId', $id);
                Request::initial()->redirect(URL::base().'renderLabyrinth/go/'.$section->map->id.'/'.$sectionNode->node_id);
            }
            //------ end redirect to scenario section ------//
        }

        Request::initial()->redirect(URL::base().'renderLabyrinth/index/'.$id);
    }

    public function action_reset() {
        $scenarioId = $this->request->param('id', null);

        $dataStatisticsIds = DB_ORM::model('statistics_user_session')->getSessionByWebinarId($scenarioId);
        list($data, $ids) = DB_ORM::model('user_session')->getSessionByWebinarId($scenarioId,$dataStatisticsIds);

        if (count($data)) {
            // Save Statistics
            DB_ORM::model('statistics_user_session')->saveWebInarSession( $data );
            DB_ORM::model('statistics_user_sessiontrace')->saveWebInarSessionTraces( $ids );
            DB_ORM::model('statistics_user_response')->saveScenarioResponse( $ids );
        }

        DB_ORM::model('qCumulative')->setResetByScenario($scenarioId);
        DB_ORM::model('webinar')->resetWebinar($scenarioId);

        Request::initial()->redirect(URL::base().'webinarmanager/index');
    }

    public function action_updateInclude4R() {
        $id      = $this->request->param('id', null);
        $include = $this->request->param('id2', null);

        DB_ORM::model('webinar_user')->updateInclude4R($id, $include);

        return true;

    }

    public function action_updateExpert()
    {
        $id     = $this->request->param('id', null);
        $expert = $this->request->param('id2', null);

        DB_ORM::model('Webinar_User')->updateExpert($id, $expert);

        return true;
    }

    public function action_getMapByWebinar()
    {
        $wId = $this->request->param('id', null);
        $mapsId = array();
        foreach (DB_ORM::select('Webinar_Map')->where('webinar_id', '=', $wId)->query()->as_array() as $wObj){
            $mapsId[] = $wObj->reference_id;
        }
        echo(json_encode($mapsId));
        exit;
    }

    public function action_getSectionAJAX()
    {
        $response   = array();
        $mapId      = $this->request->param('id');
        $sections   = DB_ORM::model('Map_Node_Section')->getSectionsByMapId($mapId);

        foreach ($sections as $sectionObj){
            $response[$sectionObj->name] = $sectionObj->id;
        }
        echo json_encode($response);
        exit;
    }

    public function action_getNodesAjax ()
    {
        $mapId  = $this->request->param('id');
        $result = array();

        foreach(DB_ORM::select('Map_Node')->where('map_id', '=', $mapId)->query()->as_array() as $nodeObj){
            $result[$nodeObj->title] = $nodeObj->id;
        }
        ksort($result);
        exit(json_encode($result));
    }

    public function action_deleteNodeAjax ()
    {
        DB_ORM::model('Webinar_PollNode')->deleteNode($this->request->param('id'));
    }

    public function action_visualEditor ()
    {
        $scenarioId = $this->request->param('id');

        $this->templateData['enabledMaps']  = DB_ORM::model('map')->getAllEnabledMap(0, 'name', 'ASC');
        $this->templateData['steps']        = DB_ORM::model('Webinar_Step')->getScenarioSteps($scenarioId);
        $this->templateData['scenario']     = DB_ORM::model('Webinar', array($scenarioId));
        $this->templateData['scenarioJSON'] = DB_ORM::model('Webinar')->generateJSON($scenarioId);
        $this->templateData['center']       = View::factory('webinar/canvas')->set('templateData', $this->templateData);
        $this->template->set('templateData', $this->templateData);

        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Visual Editor')));
    }

    public function action_ajaxStepUpdate()
    {
        $scenarioId     = $this->request->post('scenarioId');
        $data           = $this->request->post('data');
        $data           = json_decode($data, true);
        $steps          = Arr::get($data, 'steps', array());
        $elements       = Arr::get($data, 'elements', array());

        $dbSteps = DB_ORM::model('Webinar_Step')->getOnlyId($scenarioId);

        foreach ($elements as $idStep => $elementsData){
            $labyrinths = Arr::get($elementsData, 'labyrinth', array());
            $section    = Arr::get($elementsData, 'section', array());

            if ( ! ($labyrinths OR $section)) continue;

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
            foreach ($labyrinths as $idElement){
                if(isset($dbElements[$idElement])) unset($dbElements[$idElement]);
                else DB_ORM::model('Webinar_Map')->addMap($scenarioId, $idElement, $idStep, 'labyrinth');
            }

            // update section
            foreach ($section as $idElement){
                if(isset($dbElements[$idElement])) unset($dbElements[$idElement]);
                else DB_ORM::model('Webinar_Map')->addMap($scenarioId, $idElement, $idStep, 'section');
            }

            // delete remains
            foreach ($dbElements as $recordId){
                DB_ORM::delete('Webinar_Map')->where('id', '=', $recordId)->execute();
            }
            // ----- end elements ----- //
        }

        // ----- delete steps ----- //
        foreach ($dbSteps as $id=>$trash){
            DB_ORM::model('Webinar_Step')->removeStep($id);
        }

        exit(DB_ORM::model('Webinar')->generateJSON($scenarioId));
    }

    public function action_resetCumulative()
    {
        $scenarioId = $this->request->param('id');
        $mapId      = $this->request->param('id2');
        DB_ORM::update('User_Session')->set('notCumulative', 1)->where('webinar_id', '=', $scenarioId)->where('map_id', '=', $mapId)->execute();
        exit;
    }

    public function action_allConditions()
    {
        $conditions                         = DB_ORM::select('Conditions')->query()->as_array();
        $this->templateData['scenarios']    = DB_ORM::select('Webinar')->order_by('title')->query()->as_array();
        $this->templateData['conditions']   = $conditions;
        $this->templateData['assign']       = array();

        foreach ($conditions as $condition) {
            $assigns = DB_ORM::select('Conditions_Assign')->where('condition_id', '=', $condition->id)->query()->as_array();
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
        $post                    = $this->request->post();
        $newConditionsName       = Arr::get($post, 'newConditionsName', array());
        $newConditionsName       = array_filter($newConditionsName);
        $newConditionsValue      = Arr::get($post, 'newConditionsValue', array());
        $changedConditionsName   = Arr::get($post, 'changedConditionsName', array());
        $changedConditionsValue  = Arr::get($post, 'changedConditionsValue', array());
        $deletedConditions       = Arr::get($post, 'deletedConditions', array());
        $deletedConditions       = array_filter($deletedConditions);
        $conditionsModel         = DB_ORM::model('Conditions');

        foreach ($newConditionsName as $key => $conditionsName){
            $conditionsModel->add($conditionsName, $newConditionsValue[$key]);
        }

        foreach ($changedConditionsName as $id => $conditionsName){
            $conditionsModel->update($conditionsName, $changedConditionsValue[$id], $id);
        }

        foreach ($deletedConditions as $id){
            $conditionsModel->deleteRecord($id);
        }

        Request::initial()->redirect(URL::base().'webinarManager/allConditions');
    }

    public function action_editCondition()
    {
        $conditionId = $this->request->param('id');

        $this->templateData['condition'] = DB_ORM::model('Conditions', array($conditionId));
        $this->templateData['assign']    = DB_ORM::select('Conditions_Assign')->where('condition_id', '=', $conditionId)->query()->as_array();
        $this->templateData['scenarios'] = DB_ORM::select('Webinar')->order_by('title')->query()->as_array();
        $this->templateData['center']    = View::factory('conditions/edit')->set('templateData', $this->templateData);
        $this->template->set('templateData', $this->templateData);
    }

    public function action_editConditionSave()
    {
        $conditionId    = $this->request->param('id');
        $post           = $this->request->post();
        $newAssign      = Arr::get($post, 'newConditionAssign', array());
        $newAssign      = array_unique($newAssign);
        $changedAssign  = Arr::get($post, 'changedConditionAssign', array());
        $deleteAssign   = Arr::get($post, 'deleteAssign', array());
        $assignModel    = DB_ORM::model('Conditions_Assign');
        $startValue     = DB_ORM::model('Conditions', array($conditionId))->startValue;

        foreach ($newAssign as $scenarioId){
            if ($scenarioId != 'None') {
                $assignModel->add($conditionId, $scenarioId, $startValue);
            }
        }

        foreach ($changedAssign as $id => $scenarioId){
            $assignModel->update($id, $scenarioId);
        }

        foreach ($deleteAssign as $id){
            $assignModel->deleteRecord($id);
        }

        Request::initial()->redirect($this->request->referrer());
    }

    public function action_mapsGrid()
    {
        $scenarioId         = $this->request->param('id');
        $conditionId        = $this->request->param('id2');
        $scenarioElements   = DB_ORM::select('Webinar_Map')->where('webinar_id', '=', $scenarioId)->query()->as_array();

        $this->templateData['scenario'] = DB_ORM::model('Webinar', array($scenarioId));
        $this->templateData['condition'] = DB_ORM::model('Conditions', array($conditionId));

        foreach ($scenarioElements as $element) {
            $elementObj = ($element->which == 'labyrinth')
                ? DB_ORM::model('Map', array($element->reference_id))
                : DB_ORM::model('Map_Node_Section', array($element->reference_id));

            $nodesTitle = 'Nodes of '.$element->which.'\'s \''.$elementObj->name.'\'';

            if ($element->which == 'labyrinth') {
                $this->templateData['nodes'][$nodesTitle] = DB_ORM::select('Map_Node')->where('map_id', '=', $element->reference_id)->query()->as_array();
            } else {
                $noseSection = DB_ORM::select('Map_Node_Section_Node')->where('section_id', '=', $element->reference_id)->query()->as_array();
                foreach ($noseSection as $obj) {
                    $this->templateData['nodes'][$nodesTitle][] = $obj->node;
                }
            }
        }

        $conditionsChange = DB_ORM::select('Conditions_Change')->where('scenario_id', '=', $scenarioId)->where('condition_id', '=', $conditionId)->query()->as_array();
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
        $conditionId  = $this->request->param('id');
        $scenarioId   = $this->request->param('id2');
        $post         = $this->request->post();
        $newNodes     = Arr::get($post, 'newNodes', array());
        $existNodes   = Arr::get($post, 'existNodes', array());

        foreach ($newNodes as $nodeId => $data) {
            DB_ORM::model('Conditions_Change')->add($conditionId, $scenarioId, $nodeId, Arr::get($data, 'value', 0), Arr::get($data, 'appears', 0));
        }

        foreach ($existNodes as $id => $data) {
            DB_ORM::model('Conditions_Change')->update($id, Arr::get($data, 'value', 0), Arr::get($data, 'appears', 0));
        }

        Request::initial()->redirect($this->request->referrer());
    }

    public function action_resetCondition()
    {
        $assignId       = $this->request->param('id');
        $conditionId    = $this->request->param('id2');
        $value          = DB_ORM::model('Conditions', array($conditionId))->startValue;

        DB_ORM::model('Conditions_Assign')->resetValue($assignId, $value);

        Request::initial()->redirect($this->request->referrer());
    }

    //save settings\\
    //ajax
    public function action_saveChatsOrder()
    {
        $user = Auth::instance()->get_user();
        $webinar_id = (int)$this->request->param('id', null);

        if(!empty($_POST['chat']) && count($_POST['chat']) > 0 && !empty($webinar_id)) {
            $settings = $user->getSettings();
            $chats_order = $_POST['chat'];
            foreach($chats_order as $order => $chat_id) {
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

        if(!empty($webinar_id) && !empty($chat_id)) {
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

        $responses = DB_ORM::model('User_Response')->getTurkTalkResponse($question_id, $session_id, $chat_session_id, $node_id);
        $result = '';
        if(!empty($responses)){
            $result['response_type'] = 'text';
            $result['response_text'] = '';
            $result['waiting_for_response'] = false;
            foreach($responses as $response){
                $isLearner = $response['role'] == 'learner' ? true : false;

                if($isLearner){
                    $result['waiting_for_response'] = true;
                }else{
                    $result['waiting_for_response'] = false;
                }

                if($from_labyrinth){
                    $name = $isLearner  ? 'You' : 'Turker';
                }else{
                    $name = !$isLearner  ? 'You' : 'User';
                }

                if($from_labyrinth == 1){
                    if($response['type'] == 'redirect'){
                        $result['response_type'] = 'redirect';
                        $url = URL::base(true).'renderLabyrinth/go/'.$response['text']['map_id'].'/'.$response['text']['node_id'];
                        $result['response_text'] = $url;
                        die(json_encode($result));
                    }
                }
                ob_start();
                ?>
                <div class="message" style="padding:10px;border-bottom:1px solid #eee;">
                    <div class="name"><b><?php echo $name ?>:</b></div>
                    <div class="text"><?php echo is_array($response['text']) ? json_encode($response['text']) : $response['text'] ?></div>
                </div>
                <?php
                $result['response_text'] .= ob_get_clean();
            }

            $result = json_encode($result);
        }
        die($result);
    }

    //ajax
    public function action_getCurrentNode()
    {
        $result = '';
        $user_id = (int)$this->request->param('id', null);
        $webinar_id = (int)$this->request->param('id2', null);

        if(empty($user_id) || empty($webinar_id)) return false;

        $last_trace = DB_ORM::model('user')->getLastSessionTrace($user_id, $webinar_id);

        if(!empty($last_trace)) {
            $session_id = (int)$last_trace->session_id;
            $node_id = (int)$last_trace->node_id;
            $node = DB_ORM::model('Map_Node', array($node_id));
            $node_title = $node->getNodeTitle();

            //get $question_id
            $question_id = null;
            $responses = DB_ORM::model('User_Response')->getResponsesBySessionAndNode($session_id, $node_id);
            if(!empty($responses) && count($responses) > 0){
                foreach($responses as $response){
                    $question = DB_ORM::model('Map_Question', array((int)$response->question_id));
                    if($question->entry_type_id == 11){
                        $question_id = $question->id;
                        break;
                    }
                }
            }
            //end get $question_id

            $result = array('session_id' => $session_id, 'node_id' => $node_id, 'node_title' => $node_title, 'question_id' => $question_id);
            $result = json_encode($result);
        }
        die($result);
    }

    //ajax
    public function action_getNodeLinks()
    {
        $result = '';
        $node_id = (int)$this->request->param('id', null);
        if(empty($node_id)) die($result);
        $node = DB_ORM::model('Map_Node', array($node_id));
        if(!empty($node)){
            $links = $node->links;
            if(!empty($links) && count($links) > 0) {
                $result = '<option>- Redirect to... -</option>';
                foreach ($links as $link) {
                    $value = array('map_id' => $link->map_id, 'node_id' => $link->node_id_2);
                    $value = json_encode($value);
                    $node_title = trim($link->node_2->title);
                    $result .= '<option value=\''.$value.'\'>'.$node_title.'</option>';
                }
            }

        }
        die($result);
    }

    private function getWebinars()
    {
        $user       = Auth::instance()->get_user();
        $userType   = $user->type->name;

        return ($userType == 'superuser' || $userType == 'Director')
            ? DB_ORM::model('webinar')->getAllWebinars()
            : DB_ORM::model('webinar')->getAllWebinars($user->id);
    }

    private function getWebinar($webinar_id)
    {
        if(!empty($webinar_id)) {
            $scenario = DB_ORM::model('Webinar', array($webinar_id));
            $scenario_id = $scenario->id;
        }

        return !empty($scenario_id) ? $scenario : null;
    }
}