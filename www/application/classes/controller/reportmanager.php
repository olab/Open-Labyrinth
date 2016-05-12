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

    public function before()
    {
        $this->templateData['labyrinthSearch'] = 1;

        parent::before();

        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('My Labyrinths'))->set_url(URL::base() . 'authoredLabyrinth'));
    }

    public function action_index()
    {
        $mapId = $this->request->param('id', null);

        if ($mapId != null AND $this->checkUser()) {
            $this->templateData['map'] = DB_ORM::model('map', array((int)$mapId));
            $this->templateData['sessions'] = $this->templateData['map']->sessions;
            $this->templateData['sessionsComplete'] = $this->templateData['map']->getCompleteSessions($this->templateData['sessions']);
            $this->templateData['left'] = View::factory('labyrinth/labyrinthEditorMenu')->set('templateData',
                $this->templateData);
            $this->templateData['center'] = View::factory('labyrinth/report/allView')->set('templateData',
                $this->templateData);
            unset($this->templateData['right']);
            $this->template->set('templateData', $this->templateData);

            Breadcrumbs::add(Breadcrumb::factory()->set_title($this->templateData['map']->name)->set_url(URL::base() . 'labyrinthManager/global/' . $mapId));
            Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Sessions'))->set_url(URL::base() . 'reportManager/index/' . $mapId));
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

    public function action_finishAndShowReport()
    {
        $sessionId = Session::instance()->get('session_id', null);
        $mapId = $this->request->param('id2', null);
        $previousNodeId = DB_ORM::model('user_sessionTrace')->getTopTraceBySessionId($sessionId, true);
        $previousNodeId = $previousNodeId['node_id'];
        $user = Auth::instance()->get_user();
        $userId = (!empty($user)) ? $user->id : null;

        if ($sessionId == null) {
            $sessionId = isset($_COOKIE['OL']) ? $_COOKIE['OL'] : 'notExist';
        }

        Model::factory('Labyrinth')->addQuestionResponsesAndChangeCounterValues($mapId, $sessionId, $previousNodeId);
        if (!empty($sessionId) && is_numeric($sessionId)) {
            /** @var Model_Leap_User_Session $session */
            $session = DB_ORM::model('user_session', (int)$sessionId);
            $session->end_time = microtime(true);
            $session->save();
            DB_ORM::model('user_sessionTrace')->setElapsedTime((int)$sessionId);

            //send xAPI statement
            if (Model_Leap_Map::isXAPIStatementsEnabled($mapId)) {
                $session_trace = $session->getLatestTrace();
                /** @var Model_Leap_Statement $statement */
                $statement = $session_trace->createXAPIStatementCompleted();
                Model_Leap_LRSStatement::sendStatementsToLRS($statement->lrs_statements);
            }
            //end send xAPI statement
        }
        Session::instance()->delete('session_id'); // set in renderLabyrinth, checkTypeCompatibility method
        Session::instance()->set('finalSubmit', 'Map has been finished, you can not change your answers');
        if (!empty($mapId) && !empty($userId)) {
            DB_ORM::model('User_Bookmark')->deleteBookmarksByMapAndUser($mapId, $userId);
        }

        //send score to LTI Consumer
        $toolProvider = Session::instance()->get('lti_tool_provider');
        if (!empty($toolProvider)) {
            $last_trace = DB_ORM::model('User_SessionTrace')->getLastTraceBySessionId($sessionId);
            if (!empty($last_trace)) {
                $score = DB_ORM::model('Map_Counter')->getMainCounterFromSessionTrace($last_trace);
                $score = isset($score['value']) ? $score['value'] : 0;
                $this->setScore($score);
            }
        }
        //end send score to LTI Consumer

        if (empty($sessionId) || $sessionId == 'notExist') {
            $sessionIdUrl = $this->request->param('id', null);
            if (empty($sessionIdUrl)) {
                $sessionIdUrl = 'notExist';
            }
        } else {
            $sessionIdUrl = $sessionId;
        }
        Request::initial()->redirect(URL::base() . 'reportManager/showReport/' . $sessionIdUrl);
    }

    public function action_exportToExcel()
    {
        $reportId = $this->request->param('id', null);

        if ($reportId != null) {
            $report = new Report_Session(new Report_Impl_PHPExcel(), $reportId);
            $report->generate();

            $report->get();
        }
    }

    public function action_showReport()
    {
        $reportId = $this->request->param('id', null);

        if ($reportId == null) {
            Request::initial()->redirect(URL::base());
        }

        $session = DB_ORM::model('user_session', array((int)$reportId));
        $mapId = $session->map_id;

        $this->templateData['session'] = $session;
        $this->templateData['map'] = $session->map;
        $this->templateData['counters'] = DB_ORM::model('user_sessionTrace')->getCountersValues($this->templateData['session']->id);
        $this->templateData['nodes'] = DB_ORM::model('map_node')->getNodesByMap($mapId);

        if ($session->webinar_id AND $session->webinar_step) {
            $scenario = DB_ORM::model('webinar', array($session->webinar_id));

            $this->templateData['webinarID'] = $scenario->id;
            $this->templateData['webinarForum'] = $scenario->forum_id;

            if (count($scenario->steps)) {
                foreach ($scenario->steps as $step_order => $scenarioStep) {
                    if ($scenarioStep->id != $session->webinar_step) {
                        continue;
                    }
                    if (count($scenarioStep->maps)) {
                        foreach ($scenarioStep->maps as $map_order => $scenarioStepMap) {
                            if ($scenario->changeSteps == 'automatic' AND $map_order + 1 == count($scenarioStep->maps) AND isset($scenario->steps[$step_order + 1])) {
                                DB_ORM::model('Webinar')->changeWebinarStep($scenario->id,
                                    $scenario->steps[$step_order + 1]->id);
                            }

                            $isFinished = DB_ORM::model('user_session')->isUserFinishMap($scenarioStepMap->reference_id,
                                $session->user_id, $scenarioStepMap->which, $scenario->id, $session->webinar_step);
                            if ($isFinished == Model_Leap_User_Session::USER_NOT_PLAY_MAP) {
                                $this->templateData['nextCase'] = array(
                                    'webinarId' => $scenario->id,
                                    'webinarStep' => $scenarioStep->id,
                                    'webinarMap' => $scenarioStepMap->reference_id
                                );
                                break;
                            }
                        }
                        break;
                    }
                }
            }
        }

        $questions = array();
        foreach (DB_ORM::select('map_question')->where('map_id', '=', $mapId)->query()->as_array() as $question) {
            $questions[$question->id] = $question;
        }

        $this->templateData['questions'] = $questions;
        $this->templateData['responses'] = array();

        if ($this->templateData['map']->revisable_answers) {
            $orderBy = 'DESC';
        } else {
            $orderBy = 'ASC';
        }

        $userResponses = DB_ORM::select('user_response')->where('session_id', '=', $session->id)->order_by('id',
            $orderBy)->query()->as_array();
        $multipleResponses = $this->mcqConvertResponses($userResponses, $questions, $orderBy);

        $answeredQuestions = array();
        foreach ($userResponses as $userResponse) {
            $questionId = $userResponse->question_id;
            $nodeId = $userResponse->node_id;
            if ($questions[$questionId]->entry_type_id == Model_Leap_Map_Question::ENTRY_TYPE_SJT) {
                $userResponse->response = DB_ORM::model('User_Response')->sjtConvertResponse($userResponse->response);
            }

            if (!isset($answeredQuestions[$nodeId]) || !in_array($questionId, $answeredQuestions[$nodeId])) {
                if (in_array($questions[$questionId]->entry_type_id, [
                    Model_Leap_Map_Question::ENTRY_TYPE_SINGLE_LINE,
                    Model_Leap_Map_Question::ENTRY_TYPE_MULTI_LNE,
                    Model_Leap_Map_Question::ENTRY_TYPE_PCQ,
                    Model_Leap_Map_Question::ENTRY_TYPE_SLIDER,
                    Model_Leap_Map_Question::ENTRY_TYPE_DRAG_AND_DROP,
                    Model_Leap_Map_Question::ENTRY_TYPE_SCT,
                    Model_Leap_Map_Question::ENTRY_TYPE_SJT,
                    Model_Leap_Map_Question::ENTRY_TYPE_CUMULATIVE,
                    Model_Leap_Map_Question::ENTRY_TYPE_RICH_TEXT,
                    Model_Leap_Map_Question::ENTRY_TYPE_DROP_DOWN,
                    Model_Leap_Map_Question::ENTRY_TYPE_MCQ_GRID,
                    Model_Leap_Map_Question::ENTRY_TYPE_PCQ_GRID,
                ])) {
                    $this->templateData['responses'][] = $userResponse;
                    $answeredQuestions[$nodeId][] = $questionId;
                } elseif (in_array($questions[$questionId]->entry_type_id, array(Model_Leap_Map_Question::ENTRY_TYPE_MCQ))) {
                    if (isset($multipleResponses[$questionId], $multipleResponses[$questionId][$nodeId])) {
                        foreach ($multipleResponses[$questionId][$nodeId] as $mcqUserResponse) {
                            $this->templateData['responses'][] = $mcqUserResponse;
                        }
                        $answeredQuestions[$nodeId][] = $questionId;
                    }
                } elseif (in_array($questions[$questionId]->entry_type_id, array(Model_Leap_Map_Question::ENTRY_TYPE_TURK_TALK))) {

                    $responseArray = json_decode($userResponse->response, true);
                    if (!empty($responseArray)) {
                        $responseArray = end($responseArray);

                        if ($responseArray['type'] == 'init' || $responseArray['type'] == 'bell') {
                            continue;
                        }

                        if ($responseArray['type'] == 'text') {
                            $userResponse->response = $responseArray['role'] . ': ' . $responseArray['text'];
                        } else {
                            $userResponse->response = 'Redirect to the Node ID: ' . $responseArray['text']['node_id'];
                        }
                        $this->templateData['responses'][] = $userResponse;
                    }

                } else {
                    $this->templateData['responses'][] = $userResponse;
                }
            }
        }

        if (!empty($this->templateData['responses'])) {
            usort($this->templateData['responses'], function ($a, $b) {
                return ($a->id < $b->id) ? -1 : 1;
            });
        }

        $allCounters = DB_ORM::model('map_counter')->getCountersByMap($this->templateData['session']->map_id);
        foreach ($allCounters as $counter) {
            $this->templateData['startValueCounters'][$counter->id] = $counter->start_value;
        }

        $this->templateData['feedbacks'] = Model::factory('labyrinth')->getMainFeedback($session,
            $this->templateData['counters'], $session->map_id);
        $this->templateData['center'] = View::factory('labyrinth/report/report')->set('templateData',
            $this->templateData);
        $editorAccess = $this->checkUser();
        if ($editorAccess) {
            $this->templateData['left'] = View::factory('labyrinth/labyrinthEditorMenu')->set('templateData',
                $this->templateData);
        }
        $this->template->set('templateData', $this->templateData);

        Breadcrumbs::add(Breadcrumb::factory()->set_title($this->templateData['session']->map->name)->set_url(URL::base() . 'labyrinthManager/global/' . $this->templateData['session']->map->id));
        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Sessions'))->set_url(URL::base() . 'reportManager/index/' . $this->templateData['session']->map->id));
        Breadcrumbs::add(Breadcrumb::factory()->set_title($reportId)->set_url(URL::base() . 'reportManager/showReport/' . $reportId));
    }

    public function action_summaryReport()
    {
        $mapId = $this->request->param('id', null);
        if ($mapId != null) {
            $this->templateData['map'] = DB_ORM::model('map', array((int)$mapId));
            $this->templateData['sessions'] = DB_ORM::model('user_session')->getAllSessionByMap((int)$mapId);

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
            if ($allCounters != null and count($allCounters) > 0) {
                foreach ($allCounters as $counter) {
                    $this->templateData['startValueCounters'][$counter->id] = $counter->start_value;
                }
            }

            $this->templateData['allCounters'] = DB_ORM::model('map_counter')->getCountersByMap((int)$mapId);
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

    public function mcqConvertResponses($userResponses, $questions, $orderBy)
    {
        $multipleResponses = array();
        $result = array();

        foreach ($userResponses as $userResponse) {
            $questionId = $userResponse->question_id;
            $responseNodeId = $userResponse->node_id;
            if (in_array($questions[$questionId]->entry_type_id, array(3))) {
                $multipleResponses[$questionId][$responseNodeId][] = $userResponse->created_at;
            }
        }

        foreach ($userResponses as $userResponse) {
            $questionId = $userResponse->question_id;
            $responseNodeId = $userResponse->node_id;
            if (!isset($multipleResponses[$questionId]) || !isset($multipleResponses[$questionId][$responseNodeId])) {
                continue;
            }

            if ($orderBy == 'DESC') {
                //get last response
                $created_at = max($multipleResponses[$questionId][$responseNodeId]);
            } else {
                //get first response
                $created_at = min($multipleResponses[$questionId][$responseNodeId]);
            }

            if ($userResponse->created_at == $created_at) {
                $result[$questionId][$responseNodeId][] = $userResponse;
            }
        }

        return $result;
    }

    /**
     * @return bool
     */
    private function checkUser()
    {
        $user = Auth::instance()->get_user();
        if (empty($user)) {
            return false;
        }

        $type = $user->type;

        if (empty($type)) {
            return false;
        }

        $user_type = $type->name;

        return (bool)($user_type === 'author' || $user_type === 'superuser');
    }

    public function action_pathVisualisation()
    {
        $id_map = $this->request->param('id', null);
        $id_session = $this->request->param('id2', null);

        if ($id_map == null AND !$this->checkUser()) {
            Request::initial()->redirect(URL::base());
        }

        // selected_session needed for build path by javascript
        if ($id_session) {
            $sessions_for_encode = array();
            foreach (DB_ORM::select('user_sessiontrace')->where('session_id', '=', $id_session)->query() as $session) {
                $sessions_for_encode[] = array(
                    'id_node' => $session->node_id,
                    'start' => $session->date_stamp,
                    'end' => $session->end_date_stamp,
                );
            }
            $this->templateData['selected_session'] = json_encode($sessions_for_encode);
        }

        $this->templateData['map'] = DB_ORM::model('map', array((int)$id_map));
        $this->templateData['mapJSON'] = Model::factory('visualEditor')->generateJSON($id_map);
        $this->templateData['sessions'] = DB_ORM::model('user_session')->getAllSessionByMap((int)$id_map);
        $this->templateData['left'] = View::factory('labyrinth/labyrinthEditorMenu')->set('templateData',
            $this->templateData);
        $this->templateData['current_s'] = $id_session;
        $this->templateData['center'] = View::factory('labyrinth/report/pathVisualisation')->set('templateData',
            $this->templateData);
        $this->template->set('templateData', $this->templateData);

        Breadcrumbs::add(Breadcrumb::factory()->set_title($this->templateData['map']->name)->set_url(URL::base() . 'labyrinthManager/global/' . $id_map));
        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Sessions'))->set_url(URL::base() . 'reportManager/index/' . $id_map));
    }

    private function setScore($score)
    {
        $score = (float)$score;
        if (!empty($score)) {
            $score = $score / 100;
            $score = round((float)$score, 2);
        }

        $returnUrl = Lti_ToolProvider::sendScore($score);
        Auth::instance()->logout();
        Request::initial()->redirect(!empty($returnUrl) ? $returnUrl : URL::base());
    }
}