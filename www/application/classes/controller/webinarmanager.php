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

    public function before() {
        parent::before();

        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Scenario Management'))->set_url(URL::base() . 'webinarManager'));
    }

    public function action_index() {
        unset($this->templateData['right']);

        $this->templateData['webinars'] = DB_ORM::model('webinar')->getAllWebinars();

        $this->templateData['center'] = View::factory('webinar/view');
        $this->templateData['center']->set('templateData', $this->templateData);

        $this->template->set('templateData', $this->templateData);
    }

    public function action_my() {
        $this->templateData['webinars'] = DB_ORM::model('webinar')->getWebinarsForUser(Auth::instance()->get_user()->id);

        $this->templateData['center'] = View::factory('webinar/my');
        $this->templateData['center']->set('templateData', $this->templateData);

        unset($this->templateData['right']);
        $this->template->set('templateData', $this->templateData);
    }

    public function action_add() {
        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Create Scenario'))->set_url(URL::base() . 'webinarManager/add'));

        $this->templateData['users']  = DB_ORM::model('user')->getAllUsersAndAuth('ASC');
        $this->templateData['groups'] = DB_ORM::model('group')->getAllGroups();

        $this->templateData['maps'] = (Auth::instance()->get_user()->type->name == 'superuser') ? DB_ORM::model('map')->getAllEnabledMap()
                                                                                                : DB_ORM::model('map')->getAllEnabledAndAuthoredMap(Auth::instance()->get_user()->id);

        $this->templateData['center'] = View::factory('webinar/webinar');
        $this->templateData['center']->set('templateData', $this->templateData);

        unset($this->templateData['right']);
        $this->template->set('templateData', $this->templateData);
    }

    public function action_edit() {
        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Edit Scenario'))->set_url(URL::base() . 'webinarManager/edit'));

        $this->templateData['maps']    = (Auth::instance()->get_user()->type->name == 'superuser') ? DB_ORM::model('map')->getAllEnabledMap()
                                                                                                   : DB_ORM::model('map')->getAllEnabledAndAuthoredMap(Auth::instance()->get_user()->id);

        $this->templateData['webinar'] = DB_ORM::model('webinar', array((int)$this->request->param('id', null)));

        $existUsers = array();
        if($this->templateData['webinar'] != null && count($this->templateData['webinar']->users) > 0) {
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

        $this->templateData['center'] = View::factory('webinar/webinar');
        $this->templateData['center']->set('templateData', $this->templateData);

        unset($this->templateData['right']);
        $this->template->set('templateData', $this->templateData);
    }

    public function action_statistic() {
        $webinarId = $this->request->param('id', null);

        if($webinarId == null) {
            Request::initial()->redirect(URL::base() . 'webinarmanager/index');
        } else {
            Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Statistic of Scenario'))->set_url(URL::base() . 'webinarManager/statistic'));

            $webinarData = array();
            $usersMap    = array();
            $webinar     = DB_ORM::model('webinar', array((int)$webinarId));
            if($webinar != null && count($webinar->users) && count($webinar->maps) > 0) {
                foreach($webinar->users as $webinarUser) {
                    if(!isset($usersMap[$webinarUser->user_id])) {
                        $usersMap[$webinarUser->user_id] = $webinarUser->user;
                    }

                    foreach($webinar->maps as $webinarMap) {
                        $webinarData[$webinarUser->user_id][$webinarMap->step][$webinarMap->map_id]['map']    = DB_ORM::model('map', array((int)$webinarMap->map_id));

                        if($webinarMap->step <= $webinar->current_step) {
                            $webinarData[$webinarUser->user_id][$webinarMap->step][$webinarMap->map_id]['status'] = DB_ORM::model('user_session')->isUserFinishMap($webinarMap->map_id, $webinarUser->user_id, $webinar->id, $webinar->current_step);
                        } else {
                            $webinarData[$webinarUser->user_id][$webinarMap->step][$webinarMap->map_id]['status'] = 0;
                        }

                        $webinarData[$webinarUser->user_id][$webinarMap->step][$webinarMap->map_id]['user']   = $webinarUser->user;
                    }
                }
            }

            $this->templateData['webinar']     = $webinar;
            $this->templateData['usersMap']    = $usersMap;
            $this->templateData['webinarData'] = $webinarData;

            $allUsers = DB_ORM::model('user')->getAllUsersAndAuth('ASC');
            if($allUsers != null && count($allUsers) > 0) {
                foreach($allUsers as $user) {
                    $this->templateData['usersAuthMap'][$user['id']] = $user;
                }
            }

            $this->templateData['center'] = View::factory('webinar/statistic');
            $this->templateData['center']->set('templateData', $this->templateData);

            unset($this->templateData['right']);
            $this->template->set('templateData', $this->templateData);
        }
    }

    public function action_publishStep() {
        $webinarId   = $this->request->param('id', null);
        $webinarStep = $this->request->param('id2', null);
        $webinar     = DB_ORM::model('webinar', array((int)$webinarId));

        if($webinar != null && $webinarStep != null && $webinarStep > 0) {
            $jsonObject = null;
            if($webinar->publish == null) {
                $jsonObject = array();
            } else {
                $jsonObject = json_decode($webinar->publish);
            }

            if(!in_array($webinarId . '-' . $webinarStep, $jsonObject)) {
                $jsonObject[] = $webinarId . '-' . $webinarStep;

                $webinar->publish = json_encode($jsonObject);
                $webinar->save();

                if($webinar->forum_id > 0) {
                    DB_ORM::model('dforum_messages')->createMessage($webinar->forum_id, '<a href="' . URL::base() . 'webinarManager/stepReport/' . $webinarId . '/' . $webinarStep . '">Step ' . $webinarStep . ' 4R Report</a>');
                }
            }

            Request::initial()->redirect(URL::base() . 'webinarmanager/statistic/' . $webinarId);
        } else {
            Request::initial()->redirect(URL::base() . 'webinarmanager/index');
        }
    }

    public function action_render() {
        $webinarId = $this->request->param('id', null);

        $this->templateData['webinar'] = DB_ORM::model('webinar', array((int)$webinarId));
        if($this->templateData['webinar'] != null && count($this->templateData['webinar']->maps) > 0) {
            foreach($this->templateData['webinar']->maps as $webinarMap) {
                if($webinarMap->step <= $this->templateData['webinar']->current_step) {
                    $this->templateData['mapsMap'][$webinarMap->step][$webinarMap->map_id] = DB_ORM::model('user_session')->isUserFinishMap($webinarMap->map_id, Auth::instance()->get_user()->id, $webinarId, $this->templateData['webinar']->current_step);
                } else {
                    $this->templateData['mapsMap'][$webinarMap->step][$webinarMap->map_id] = 0;
                }
            }
        }

        $this->templateData['center'] = View::factory('webinar/render');
        $this->templateData['center']->set('templateData', $this->templateData);

        unset($this->templateData['right']);
        $this->template->set('templateData', $this->templateData);
    }

    public function action_delete() {
        DB_ORM::model('webinar')->deleteWebinar($this->request->param('id', null));

        Request::initial()->redirect(URL::base() . 'webinarmanager/index');
    }

    public function action_save() {
        DB_ORM::model('webinar')->saveWebinar($_POST);

        Request::initial()->redirect(URL::base() . 'webinarmanager/index');
    }

    public function action_changeStep() {
        $webinarId = $this->request->param('id' , null);
        $step      = $this->request->param('id2', null);

        if($step < 1) { $step = 1; }
        if($step > 3) { $step = 3; }

        DB_ORM::model('webinar')->changeWebinarStep($webinarId, $step);

        Request::initial()->redirect(URL::base() . 'webinarmanager/index');
    }

    public function action_stepReport() {
        $webinarId = $this->request->param('id', null);
        $stepKey   = $this->request->param('id2', null);

        if($webinarId != null && $webinarId > 0 && $stepKey != null && $stepKey > 0) {
            $webinar = DB_ORM::model('webinar', array((int)$webinarId));
            $isExistAccess = false;

            if(Auth::instance()->get_user()->id == $webinar->author_id || Auth::instance()->get_user()->type->name == 'superuser') {
                $isExistAccess = true;
            }

            if(!$isExistAccess && $webinar->publish != null) {
                $jsonObject = json_decode($webinar->publish);

                $isExistAccess = in_array($webinarId . '-' . $stepKey, $jsonObject);
            }

            if($isExistAccess) {
                $report  = new Report_4R(new Report_Impl_PHPExcel(), $webinar->title);
                if($webinar != null && count($webinar->maps) > 0) {
                    foreach($webinar->maps as $webinarMap) {
                        if($webinarMap->step == $stepKey) {
                            $report->add($webinarMap->map_id, $webinar->id, $stepKey);
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

    public function action_mapReport() {
        $webinarId = $this->request->param('id', null);
        $mapKey    = $this->request->param('id2', null);

        if($webinarId != null && $webinarId > 0 && $mapKey != null && $mapKey > 0) {
            $webinar = DB_ORM::model('webinar', array((int)$webinarId));
            $map = DB_ORM::model('map', array((int)$mapKey));

            $report  = new Report_4R(new Report_Impl_PHPExcel(), $map->name);
            $report->add($mapKey, $webinar->id);
            $report->generate();

            $report->get();
        } else {
            Request::initial()->redirect(URL::base() . 'webinarmanager/index');
        }
    }

    public function action_play() {
        $webinarId = $this->request->param('id', null);
        $step      = $this->request->param('id2', null);
        $mapId     = $this->request->param('id3', null);

        if($webinarId != null && $webinarId > 0 && $step != null && $step > 0) {
            Session::instance()->set('webinarId', $webinarId);
            Session::instance()->set('step', $step);
        }

        Request::initial()->redirect(URL::base() . 'renderLabyrinth/index/' . $mapId);
    }

    public function action_reset() {
        DB_ORM::model('webinar')->resetWebinar($this->request->param('id', null));

        Request::initial()->redirect(URL::base() . 'webinarmanager/index');
    }
}