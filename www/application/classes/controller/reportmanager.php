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

    public function action_index()
    {
        $mapId = $this->request->param('id', NULL);

        if ($mapId != NULL AND $this->checkUser())
        {
            $this->templateData['map']      = DB_ORM::model('map', array((int)$mapId));
            $this->templateData['sessions'] = DB_ORM::model('user_session')->getAllSessionByMap((int)$mapId);
            $this->templateData['left']     = View::factory('labyrinth/labyrinthEditorMenu')->set('templateData', $this->templateData);
            $this->templateData['center']   = View::factory('labyrinth/report/allView')->set('templateData', $this->templateData);
            unset($this->templateData['right']);
            $this->template->set('templateData', $this->templateData);

            Breadcrumbs::add(Breadcrumb::factory()->set_title($this->templateData['map']->name)->set_url(URL::base().'labyrinthManager/global/'.$mapId));
            Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Sessions'))->set_url(URL::base().'reportManager/index/'.$mapId));
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

    public function action_finishAndShowReport()
    {
        Session::instance()->delete('session_id'); // set in renderLabyrinth, checkTypeCompatibility method
        $sessionId      = $this->request->param('id', NULL);
        $mapId          = $this->request->param('id2', NULL);
        $previewNodeId  = DB_ORM::model('user_sessionTrace')->getTopTraceBySessionId($sessionId);

        if ($sessionId == NULL) {
            $sessionId = isset($_COOKIE['OL']) ? $_COOKIE['OL'] : 'notExist';
        }

        Model::factory('Labyrinth')->addQuestionResponsesAndChangeCounterValues($mapId, $sessionId, $previewNodeId);
        DB_ORM::model('user_sessionTrace')->setElapsedTime((int)$sessionId);

        Request::initial()->redirect(URL::base().'reportManager/showReport/'.$sessionId);
    }

    public function action_exportToExcel() {
        $reportId = $this->request->param('id', null);

        if($reportId != null) {
            $report = new Report_Session(new Report_Impl_PHPExcel(), $reportId);
            $report->generate();

            $report->get();
        }
    }

    public function action_showReport()
    {
        $reportId = $this->request->param('id', NULL);

        if ($reportId == NULL) {
            Request::initial()->redirect(URL::base());
        }

        $session    = DB_ORM::model('user_session', array((int)$reportId));
        $questions  = DB_ORM::select('map_question')->where('map_id', '=', $session->map_id)->query()->as_array();

        $this->templateData['session']      = $session;
        $this->templateData['map']          = $session->map;
        $this->templateData['counters']     = DB_ORM::model('user_sessionTrace')->getCountersValues($this->templateData['session']->id);
        $this->templateData['questions']    = $questions;
        $this->templateData['nodes']        = DB_ORM::model('map_node')->getNodesByMap($session->map_id);

        if ($session->webinar_id AND $session->webinar_step) {
            $scenario = DB_ORM::model('webinar', array($session->webinar_id));

            $this->templateData['webinarID']        = $scenario->id;
            $this->templateData['webinarForum']     = $scenario->forum_id;

            if (count($scenario->steps)) {
                foreach ($scenario->steps as $step_order => $scenarioStep) {
                    if ($scenarioStep->id != $session->webinar_step) {
                        continue;
                    }
                    if (count($scenarioStep->maps)) {
                        foreach($scenarioStep->maps as $map_order => $scenarioStepMap) {
                            if ($scenario->changeSteps == 'automatic' AND $map_order + 1 == count($scenarioStep->maps) AND isset($scenario->steps[$step_order+1])) {
                                DB_ORM::model('Webinar')->changeWebinarStep($scenario->id, $scenario->steps[$step_order+1]->id);
                            }

                            $isFinished = DB_ORM::model('user_session')->isUserFinishMap($scenarioStepMap->reference_id, $session->user_id, $scenarioStepMap->which, $scenario->id, $session->webinar_step);
                            if ($isFinished == Model_Leap_User_Session::USER_NOT_PLAY_MAP) {
                                $this->templateData['nextCase'] = array(
                                    'webinarId'     => $scenario->id,
                                    'webinarStep'   => $scenarioStep->id,
                                    'webinarMap'    => $scenarioStepMap->reference_id
                                );
                                break;
                            }
                        }
                        break;
                    }
                }
            }
        }

        foreach ($questions as $question) {
            $response = DB_ORM::model('user_response')->getResponse($session->id, $question->id);
            $responseObj = end($response);
            if ($responseObj) {
                if ($question->entry_type_id == 8) {
                    $responseObj->response = DB_ORM::model('User_Response')->sjtConvertResponse($responseObj->response);
                }
                $this->templateData['responses'][$question->id][] = $responseObj;
            }
        }

        $allCounters = DB_ORM::model('map_counter')->getCountersByMap($this->templateData['session']->map_id);
        foreach ($allCounters as $counter) {
            $this->templateData['startValueCounters'][$counter->id] = $counter->start_value;
        }

        $this->templateData['feedbacks']    = Model::factory('labyrinth')->getMainFeedback($session, $this->templateData['counters'], $session->map_id);
        $this->templateData['center']       = View::factory('labyrinth/report/report')->set('templateData', $this->templateData);
        $this->templateData['left']         = View::factory('labyrinth/labyrinthEditorMenu')->set('templateData', $this->templateData);
        $this->template->set('templateData', $this->templateData);

        Breadcrumbs::add(Breadcrumb::factory()->set_title($this->templateData['session']->map->name)->set_url(URL::base().'labyrinthManager/global/'.$this->templateData['session']->map->id));
        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Sessions'))->set_url(URL::base().'reportManager/index/'.$this->templateData['session']->map->id));
        Breadcrumbs::add(Breadcrumb::factory()->set_title($reportId)->set_url(URL::base().'reportManager/showReport/'.$reportId));
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

    private function checkUser()
    {
        $user_type = Auth::instance()->get_user()->type->name;
        return (bool) ($user_type == 'author' OR $user_type == 'superuser');
    }

    public function action_pathVisualisation ()
    {
        $id_map     = $this->request->param('id', NULL);
        $id_session = $this->request->param('id2', NULL);

        if ($id_map == NULL AND ! $this->checkUser()) Request::initial()->redirect(URL::base());

        // selected_session needed for build path by javascript
        if ($id_session)
        {
            $sessions_for_encode = array();
            foreach (DB_ORM::select('user_sessiontrace')->where('session_id', '=', $id_session)->query() as $session)
            {
                $sessions_for_encode[] = array(
                    'id_node'   =>$session->node_id,
                    'start'     =>$session->date_stamp,
                    'end'       =>$session->end_date_stamp,
                );
            }
            $this->templateData['selected_session'] = json_encode($sessions_for_encode);
        }

        $this->templateData['map']      = DB_ORM::model('map', array((int)$id_map));
        $this->templateData['mapJSON']  = Model::factory('visualEditor')->generateJSON($id_map);
        $this->templateData['sessions'] = DB_ORM::model('user_session')->getAllSessionByMap((int)$id_map);
        $this->templateData['left']     = View::factory('labyrinth/labyrinthEditorMenu')->set('templateData', $this->templateData);
        $this->templateData['current_s']= $id_session;
        $this->templateData['center']   = View::factory('labyrinth/report/pathVisualisation')->set('templateData', $this->templateData);
        $this->template->set('templateData', $this->templateData);

        Breadcrumbs::add(Breadcrumb::factory()->set_title($this->templateData['map']->name)->set_url(URL::base().'labyrinthManager/global/'.$id_map));
        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Sessions'))->set_url(URL::base().'reportManager/index/'.$id_map));
    }
}