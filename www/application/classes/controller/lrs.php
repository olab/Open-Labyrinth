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

        Request::initial()->redirect(URL::base() . 'lrs');
    }

    public function action_delete()
    {
        $id = $this->request->param('id');
        $model = DB_ORM::model('LRS', array($id));

        $model->delete();

        Request::initial()->redirect(URL::base() . 'lrs');
    }

    public function action_sendReportSubmit()
    {
        $post = $this->request->post();
        $date_from = Arr::get($post, 'date_from');
        $date_to = Arr::get($post, 'date_to');

        if (empty($date_from) || empty($date_to)) {
            die('Dates cannot be blank');
        }

        $date_from_obj = DateTime::createFromFormat('Y-m-d', $date_from);
        $date_to_obj = DateTime::createFromFormat('Y-m-d', $date_to);

        /** @var Model_Leap_User_Session[]|DB_ResultSet $sessions */
        $sessions = DB_ORM::select('User_Session')
            ->where('start_time', '>=', $date_from_obj->getTimestamp())
            ->where('start_time', '<=', $date_to_obj->getTimestamp())
            ->query();

        foreach ($sessions as $session) {
            $this->createSessionStatements($session);
        }

        foreach ($sessions as $session) {
            $this->sendSessionStatements($session);
        }

        die;

    }

    private function createSessionStatements(Model_Leap_User_Session $session)
    {
        //create responses statements
        $responses = $session->responses;
        foreach($responses as $response){
            $response->createXAPIStatement();
        }
        //end create responses statements
    }

    private function sendSessionStatements(Model_Leap_User_Session $session)
    {
        $session->sendXAPIStatements();
    }
}