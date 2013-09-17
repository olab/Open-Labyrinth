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

class Controller_ReportManager extends Controller_Base
{

    public function before() {
        $this->templateData['labyrinthSearch'] = 1;

        parent::before();

        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('My Labyrinths'))->set_url(URL::base() . 'authoredLabyrinth'));
    }

    public function action_index() {
        $mapId = $this->request->param('id', NULL);
        if ($mapId != NULL and $this->checkUser()) {
            $this->templateData['map'] = DB_ORM::model('map', array((int)$mapId));
            $this->templateData['sessions'] = DB_ORM::model('user_session')->getAllSessionByMap((int)$mapId);

            Breadcrumbs::add(Breadcrumb::factory()->set_title($this->templateData['map']->name)->set_url(URL::base() . 'labyrinthManager/global/' . $mapId));
            Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Sessions'))->set_url(URL::base() . 'reportManager/index/' . $mapId));

            $allReportView = View::factory('labyrinth/report/allView');
            $allReportView->set('templateData', $this->templateData);

            $leftView = View::factory('labyrinth/labyrinthEditorMenu');
            $leftView->set('templateData', $this->templateData);

            $this->templateData['left'] = $leftView;
            $this->templateData['center'] = $allReportView;
            unset($this->templateData['right']);
            $this->template->set('templateData', $this->templateData);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

    public function action_showReport() {
        $reportId = $this->request->param('id', NULL);
        if ($reportId != NULL) {
            $this->templateData['session'] = DB_ORM::model('user_session', array((int)$reportId));
            $this->templateData['map'] = $this->templateData['session']->map;
            $this->templateData['counters'] = DB_ORM::model('user_sessionTrace')->getCountersValues($this->templateData['session']->id);
            $this->templateData['questions'] = DB_ORM::model('map_question')->getQuestionsByMap($this->templateData['session']->map_id);
            $this->templateData['nodes'] = DB_ORM::model('map_node')->getNodesByMap($this->templateData['session']->map_id);

            if(isset($this->templateData['session']) && $this->templateData['session']->webinar_id != null && $this->templateData['session']->webinar_step != null) {
                $webinar = DB_ORM::model('webinar', array($this->templateData['session']->webinar_id));

                if($webinar->steps != null && count($webinar->steps) > 0) {
                    foreach($webinar->steps as $webinarStep) {
                        if($webinarStep->id != $this->templateData['session']->webinar_step) continue;

                        if(count($webinarStep->maps) > 0) {
                            foreach($webinarStep->maps as $webinarStepMap) {
                                $isFinished = DB_ORM::model('user_session')->isUserFinishMap($webinarStepMap->map_id, $this->templateData['session']->user_id, $webinar->id, $this->templateData['session']->webinar_step);
                                if($isFinished == Model_Leap_User_Session::USER_NOT_PLAY_MAP) {
                                    $this->templateData['nextCase'] = array('webinarId' => $webinar->id, 'webinarStep' => $webinarStep->id, 'webinarMap' => $webinarStepMap->map_id);
                                    break;
                                }
                            }

                            break;
                        }
                    }
                }
                $this->templateData['webinarID'] = $webinar->id;
                $this->templateData['webinarForum'] = $webinar->forum_id;
            }

            if ($this->templateData['questions'] != NULL) {
                foreach ($this->templateData['questions'] as $question) {
                    $response = DB_ORM::model('user_response')->getResponce($this->templateData['session']->id, $question->id);
                    if ($response != NULL) {
                        $this->templateData['responses'][$question->id] = $response;
                    }
                }
            }

            $allCounters = DB_ORM::model('map_counter')->getCountersByMap($this->templateData['session']->map_id);
            if ($allCounters != NULL and count($allCounters) > 0) {
                foreach ($allCounters as $counter) {
                    $this->templateData['startValueCounters'][$counter->id] = $counter->start_value;
                }
            }

            $this->templateData['feedbacks'] = Model::factory('labyrinth')->getMainFeedback($this->templateData['session'], $this->templateData['counters'], $this->templateData['session']->map_id);

                Breadcrumbs::add(Breadcrumb::factory()->set_title($this->templateData['session']->map->name)->set_url(URL::base() . 'labyrinthManager/global/' . $this->templateData['session']->map->id));
                Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Sessions'))->set_url(URL::base() . 'reportManager/index/' . $this->templateData['session']->map->id));
            Breadcrumbs::add(Breadcrumb::factory()->set_title($reportId)->set_url(URL::base() . 'reportManager/showReport/' . $reportId));

                $reportView = View::factory('labyrinth/report/report');
                $reportView->set('templateData', $this->templateData);

                $this->templateData['center'] = $reportView;

            $leftView = View::factory('labyrinth/labyrinthEditorMenu');
            $leftView->set('templateData', $this->templateData);

            $this->templateData['left'] = $leftView;

               // unset($this->templateData['right']);
                $this->template->set('templateData', $this->templateData);
            } else {
                Request::initial()->redirect(URL::base());
            }
        }

    public function action_summaryReport() {
        $mapId = $this->request->param('id', NULL);
        if ($mapId != NULL) {
            $this->templateData['map'] = DB_ORM::model('map', array((int) $mapId));
            $this->templateData['sessions'] = DB_ORM::model('user_session')->getAllSessionByMap((int) $mapId);

            $minClicks = 0;
            if (count($this->templateData['sessions']) > 0) {
                $minClicks = count($this->templateData['sessions'][0]->traces);
                foreach ($this->templateData['sessions'] as $session) {
                    if ($minClicks > count($session->traces)) {
                        $minClicks = count($session->traces);
                    }
                }
            }

            if (count($this->templateData['sessions']) > 0) {
                foreach ($this->templateData['sessions'] as $session) {
                    $this->templateData['counters'] = DB_ORM::model('user_sessionTrace')->getCountersValues($session->id);
                }
            }

            $allCounters = DB_ORM::model('map_counter')->getCountersByMap($mapId);
            if ($allCounters != NULL and count($allCounters) > 0) {
                foreach ($allCounters as $counter) {
                    $this->templateData['startValueCounters'][$counter->id] = $counter->start_value;
                }
            }

            $this->templateData['allCounters'] = DB_ORM::model('map_counter')->getCountersByMap((int) $mapId);
            $this->templateData['minClicks'] = $minClicks;
            Breadcrumbs::add(Breadcrumb::factory()->set_title($this->templateData['map']->name)->set_url(URL::base() . 'labyrinthManager/global/' . $mapId));
            Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Sessions'))->set_url(URL::base() . 'reportManager/index/' . $mapId));
            Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Summary'))->set_url(URL::base() . 'reportManager/summaryReport/' . $mapId));
            $summaryView = View::factory('labyrinth/report/summary');
            $summaryView->set('templateData', $this->templateData);

            $this->templateData['center'] = $summaryView;
            $leftView = View::factory('labyrinth/labyrinthEditorMenu');
            $leftView->set('templateData', $this->templateData);

            $this->templateData['left'] = $leftView;
            unset($this->templateData['right']);
            $this->template->set('templateData', $this->templateData);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

    private function checkUser() {
        if (Auth::instance()->get_user()->type->name == 'author' or Auth::instance()->get_user()->type->name == 'superuser') {
            return TRUE;
        }

        return FALSE;
    }

}