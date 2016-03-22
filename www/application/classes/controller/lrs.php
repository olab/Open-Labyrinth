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

class Controller_LRS extends Controller_Base
{

    public function before()
    {
        parent::before();
        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('LRS manager'))->set_url(URL::base() . 'lrs/index'));
    }

    public function action_index()
    {
        $this->templateData['lrs_list'] = DB_ORM::select('LRS')->order_by('name')->query();
        $this->templateData['center'] = View::factory('lrs/index')->set('templateData', $this->templateData);
        $this->template->set('templateData', $this->templateData);
    }

    public function action_create()
    {
        $this->showView();
        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Add LRS')));
    }

    public function action_update()
    {
        $id = $this->request->param('id');
        $this->showView($id);
        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Edit LRS')));
    }

    private function showView($id = null)
    {
        $this->templateData['model'] = $id ? DB_ORM::model('LRS', array($id)) : null;
        $this->templateData['center'] = View::factory('lrs/view')->set('templateData', $this->templateData);
        $this->template->set('templateData', $this->templateData);
    }

    public function action_save()
    {
        $post = $this->request->post();
        $id = Arr::get($post, 'id', null);

        if (!empty($id)) {
            $model = DB_ORM::model('LRS', array($id));
        } else {
            $model = new Model_Leap_LRS();
        }

        $model->load($post);
        $model->save();

        Session::instance()->set('success_message', 'Saved.');
        Request::initial()->redirect(URL::base() . 'lrs');
    }

    public function action_delete()
    {
        $id = $this->request->param('id');
        $model = DB_ORM::model('LRS', array($id));

        $model->delete();

        Session::instance()->set('success_message', 'Deleted.');
        Request::initial()->redirect(URL::base() . 'lrs');
    }

    public function action_deleteLRSStatement()
    {
        $id = $this->request->param('id');
        $model = DB_ORM::model('LRSStatement', array($id));

        $model->delete();

        Session::instance()->set('success_message', 'Deleted.');
        Request::initial()->redirect(URL::base() . 'lrs/failedStatements');
    }

    public function action_sendFailedLRSStatements()
    {
        /** @var Model_Leap_LRSStatement[] $lrs_statements */
        $lrs_statements = DB_ORM::select('LRSStatement')
            ->where('status', '=', Model_Leap_LRSStatement::STATUS_FAIL)
            ->order_by('id', 'DESC')
            ->query();

        foreach ($lrs_statements as $lrs_statement) {
            $lrs_statement->sendAndSave();
        }

        Session::instance()->set('info_message', 'Statements sent to LRS');
        Request::initial()->redirect(URL::base() . 'lrs/failedStatements');
    }

    public function action_failedStatements()
    {
        $lrs_statements = DB_ORM::select('LRSStatement')
            ->where('status', '=', Model_Leap_LRSStatement::STATUS_FAIL)
            ->order_by('id', 'DESC')
            ->query();

        $this->templateData['lrs_statements'] = $lrs_statements;
        $this->templateData['center'] = View::factory('lrs/failedStatements')->set('templateData', $this->templateData);
        $this->template->set('templateData', $this->templateData);
    }

//    public function action_sendReportScenarioBasedSubmit()
//    {
//        $webinar_id = $this->request->param('id');
//
//        if (empty($webinar_id)) {
//            die('webinar_id cannot be blank');
//        }
//
//        /** @var Model_Leap_User_Session[]|DB_ResultSet $sessions */
//        $sessions = DB_ORM::select('User_Session')
//            ->where('webinar_id', '=', $webinar_id)
//            ->query();
//
//        $this->sendSessions($sessions);
//
//        Request::initial()->redirect(URL::base() . 'webinarManager/progress/' . $webinar_id);
//    }

    public function action_sendReportSubmit()
    {
        $post = $this->request->post();
        $date_from = Arr::get($post, 'date_from');
        $date_to = Arr::get($post, 'date_to');
        $referrer_url = Arr::get($post, 'referrer');
        $redirect_url = $referrer_url;
        if (empty($redirect_url)) {
            $redirect_url = URL::base() . 'lrs';
        }

        if (empty($date_from) || empty($date_to)) {
            Session::instance()->set('error_message', 'Dates cannot be blank');
            Request::initial()->redirect($redirect_url);
        }

        $date_from_obj = DateTime::createFromFormat('m/d/Y H:i:s', $date_from . ' 00:00:00');
        $date_to_obj = DateTime::createFromFormat('m/d/Y H:i:s', $date_to . ' 23:59:59');

        $per_iteration = 300;
        $i = 0;
        $offset = 0;
        $limit = $per_iteration;
        do {
            /** @var Model_Leap_User_Session[]|DB_ResultSet $sessions */
            $sessions = DB_ORM::select('User_Session')
                ->where('start_time', '>=', $date_from_obj->getTimestamp())
                ->where('start_time', '<=', $date_to_obj->getTimestamp())
                ->offset($offset)
                ->limit($limit)
                ->query();

            if ($sessions->count() > 0) {
                Model_Leap_User_Session::sendSessionsToLRS($sessions);
            }

            $offset += $per_iteration;
            $limit += $per_iteration;
            $i++;

        } while ($sessions->count() > 0);

        if ($i < 2) {
            Session::instance()
                ->set('error_message', 'Sessions not found for date range: ' . $date_from . ' - ' . $date_to);
        } else {
            Session::instance()->set('info_message', 'Statements sent to LRS');
        }

        Request::initial()->redirect($redirect_url);
    }
}