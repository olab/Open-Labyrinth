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

class Controller_TodayTipManager extends Controller_Base {

    public function before() {
        parent::before();

        if (Auth::instance()->get_user()->type->name != 'superuser') {
            Request::initial()->redirect(URL::base());
        }

        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Today\'s tip manager'))->set_url(URL::base() . 'TodayTipManager/index'));

        unset($this->templateData['right']);
        $this->template->set('templateData', $this->templateData);
    }

    public function after() {
        $this->templateData['left']->set('templateData', $this->templateData);
        $this->templateData['center']->set('templateData', $this->templateData);

        $this->template->set('templateData', $this->templateData);

        parent::after();
    }

    public function action_index() {
        $view = View::factory('todaytip/view');
        $menuView = View::factory('todaytip/leftMenu');

        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Current Tips')));

        $this->templateData['activeTips'] = DB_ORM::model('TodayTip')->getActiveTips();

        $this->templateData['left'] = $menuView;
        $this->templateData['center'] = $view;
    }

    public function action_archived() {
        $view = View::factory('todaytip/archived');
        $menuView = View::factory('todaytip/leftMenu');

        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Archived Tips')));

        $this->templateData['archivedTips'] = DB_ORM::model('TodayTip')->getArchivedTips();

        $this->templateData['left'] = $menuView;
        $this->templateData['center'] = $view;
    }

    public function action_addTip() {
        $view = View::factory('todaytip/tip');
        $menuView = View::factory('todaytip/leftMenu');

        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Create Tip')));

        $this->templateData['left'] = $menuView;
        $this->templateData['center'] = $view;
    }

    public function action_editTip() {
        $tipId = $this->request->param('id', null);

        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Edit Tip')));

        $view = View::factory('todaytip/tip');
        $menuView = View::factory('todaytip/leftMenu');

        if($tipId != null) {
            $this->templateData['tip'] = DB_ORM::model('TodayTip', array((int)$tipId));
        }

        $this->templateData['left'] = $menuView;
        $this->templateData['center'] = $view;
    }

    public function action_saveTip() {
        $tipId  = Arr::get($_POST, 'tipId', null);
        $result = DB_ORM::model('TodayTip')->saveTip($tipId, $_POST);

        if($result != null) {
            $tipId = $result;
        }

        Request::initial()->redirect(URL::base() . 'TodayTipManager/editTip/' . $tipId);
    }

    public function action_deleteTip() {
        $tipId = $this->request->param('id', null);

        if($tipId != null && $tipId > 0) {
            DB_ORM::delete('TodayTip')->where('id', '=', $tipId)->execute();
        }

        Request::initial()->redirect(URL::base() . 'TodayTipManager/index');
    }

    public function action_deleteArchiveTip() {
        $tipId = $this->request->param('id', null);

        if($tipId != null && $tipId > 0) {
            DB_ORM::delete('TodayTip')->where('id', '=', $tipId)->execute();
        }

        Request::initial()->redirect(URL::base() . 'TodayTipManager/archived');
    }

    public function action_archive() {
        $tipId = $this->request->param('id', null);

        if($tipId != null && $tipId > 0) {
            DB_ORM::update('TodayTip')->set('is_archived', 1)->where('id', '=', $tipId)->execute();
        }

        Request::initial()->redirect(URL::base() . 'TodayTipManager/index');
    }

    public function action_unarchive() {
        $tipId = $this->request->param('id', null);

        if($tipId != null && $tipId > 0) {
            DB_ORM::update('TodayTip')->set('is_archived', 0)->where('id', '=', $tipId)->execute();
        }

        Request::initial()->redirect(URL::base() . 'TodayTipManager/archived');
    }
}

?>