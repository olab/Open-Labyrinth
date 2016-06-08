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

class Controller_FeedbackManager extends Controller_Base
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

        if ($mapId == null) {
            Request::initial()->redirect(URL::base());
        }

        DB_ORM::model('User')->can('edit', array('mapId' => $mapId));
        $map = DB_ORM::model('map', array((int)$mapId));
        $rules = Model_Leap_Map_Feedback_Rule::getRulesByMap($map);
        $this->templateData['map'] = $map;
        $this->templateData['operators'] = DB_ORM::model('map_feedback_operator')->getAllOperators();
        $this->templateData['time_feedback_rules'] = DB_ORM::model('map_feedback_rule')
            ->getRulesByTypeName('time taken', $rules);
        $this->templateData['visit_feedback_rules'] = DB_ORM::model('map_feedback_rule')
            ->getRulesByTypeName('node visit', $rules);
        $this->templateData['must_visit_feedback_rules'] = DB_ORM::model('map_feedback_rule')
            ->getRulesByTypeName('must visit', $rules);
        $this->templateData['must_avoid_feedback_rules'] = DB_ORM::model('map_feedback_rule')
            ->getRulesByTypeName('must avoid', $rules);
        $this->templateData['counter_feedback_rules'] = DB_ORM::model('map_feedback_rule')
            ->getRulesByTypeName('counter value', $rules);
        $this->templateData['counters'] = DB_ORM::model('map_counter')->getCountersByMap((int)$mapId);
        $this->templateData['nodes'] = DB_ORM::model('map_node')->getNodesByMap((int)$mapId);

        Breadcrumbs::add(Breadcrumb::factory()->set_title($this->templateData['map']->name)->set_url(URL::base() . 'labyrinthManager/global/' . $mapId));
        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Feedback'))->set_url(URL::base() . 'feedbackManager/index/' . $mapId));

        $feedbackView = View::factory('labyrinth/feedback');
        $feedbackView->set('templateData', $this->templateData);

        $leftView = View::factory('labyrinth/labyrinthEditorMenu');
        $leftView->set('templateData', $this->templateData);

        $this->templateData['left'] = $leftView;
        $this->templateData['center'] = $feedbackView;
        unset($this->templateData['right']);
        $this->template->set('templateData', $this->templateData);
    }

    public function action_updateGeneral()
    {
        $mapId = $this->request->param('id', null);
        if ($_POST and $mapId != null) {
            DB_ORM::model('User')->can('edit', array('mapId' => $mapId));
            DB_ORM::model('map')->updateFeedback($mapId, Arr::get($_POST, 'fb', null));
            Request::initial()->redirect(URL::base() . 'feedbackManager/index/' . $mapId);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

    public function action_addRule()
    {
        $mapId = $this->request->param('id', null);
        $typeName = $this->request->param('id2', null);
        if ($_POST and $mapId != null and $typeName != null) {
            DB_ORM::model('User')->can('edit', array('mapId' => $mapId));
            DB_ORM::model('map_feedback_rule')->addRule($mapId, $typeName, $_POST);
            Request::initial()->redirect(URL::base() . 'feedbackManager/index/' . $mapId);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

    public function action_deleteRule()
    {
        $mapId = $this->request->param('id', null);
        $ruleId = $this->request->param('id2', null);
        if ($mapId != null and $ruleId != null) {
            DB_ORM::model('User')->can('edit', array('mapId' => $mapId));
            DB_ORM::model('map_feedback_rule', array((int)$ruleId))->delete();
            Request::initial()->redirect(URL::base() . 'feedbackManager/index/' . $mapId);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }
}