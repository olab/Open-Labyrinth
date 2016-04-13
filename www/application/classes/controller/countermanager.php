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

class Controller_CounterManager extends Controller_Base
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

        $this->templateData['map'] = DB_ORM::model('map', array((int)$mapId));
        $this->templateData['counters'] = DB_ORM::model('map_counter')->getCountersByMap($mapId);
        $this->templateData['center'] = View::factory('labyrinth/counter/view')->set('templateData',
            $this->templateData);;
        $this->templateData['left'] = View::factory('labyrinth/labyrinthEditorMenu')->set('templateData',
            $this->templateData);;
        $this->template->set('templateData', $this->templateData);

        Breadcrumbs::add(Breadcrumb::factory()->set_title($this->templateData['map']->name)->set_url(URL::base() . 'labyrinthManager/global/' . $mapId));
        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Counters'))->set_url(URL::base() . 'counterManager/index/' . $mapId));
    }

    public function action_addCounter()
    {
        $mapId = $this->request->param('id', null);

        if ($mapId == null) {
            Request::initial()->redirect(URL::base());
        }

        DB_ORM::model('User')->can('edit', array('mapId' => $mapId));
        $this->templateData['map'] = DB_ORM::model('map', array((int)$mapId));
        $this->templateData['images'] = DB_ORM::model('map_element')->getImagesByMap($mapId);
        $this->templateData['center'] = View::factory('labyrinth/counter/add')->set('templateData',
            $this->templateData);
        $this->templateData['left'] = View::factory('labyrinth/labyrinthEditorMenu')->set('templateData',
            $this->templateData);
        $this->template->set('templateData', $this->templateData);

        Breadcrumbs::add(Breadcrumb::factory()->set_title($this->templateData['map']->name)->set_url(URL::base() . 'labyrinthManager/global/' . $mapId));
        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Counters'))->set_url(URL::base() . 'counterManager/index/' . $mapId));
        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Add Counter'))->set_url(URL::base() . 'counterManager/addCounter/' . $mapId));
    }

    public function action_saveNewCounter()
    {
        $mapId = $this->request->param('id', null);
        if ($_POST and $mapId != null) {
            DB_ORM::model('map_counter')->addCounter($mapId, $_POST);
            Request::initial()->redirect(URL::base() . 'counterManager/index/' . $mapId);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

    public function action_editCounter()
    {
        $mapId = $this->request->param('id', null);
        $counterId = $this->request->param('id2', null);
        if ($mapId AND $counterId) {
            DB_ORM::model('User')->can('edit', array('mapId' => $mapId));
            $this->templateData['map'] = DB_ORM::model('map', array((int)$mapId));
            $this->templateData['counter'] = DB_ORM::model('map_counter', array((int)$counterId));
            $this->templateData['images'] = DB_ORM::model('map_element')->getImagesByMap($mapId);
            $this->templateData['rules'] = DB_ORM::model('map_counter_rule')->getRulesByCounterId($counterId);
            $this->templateData['relations'] = DB_ORM::model('map_counter_relation')->getAllRealtions();
            $this->templateData['nodes'] = DB_ORM::model('map_node')->getNodesByMap($mapId);
            $this->templateData['center'] = View::factory('labyrinth/counter/edit')->set('templateData',
                $this->templateData);
            $this->templateData['left'] = View::factory('labyrinth/labyrinthEditorMenu')->set('templateData',
                $this->templateData);
            $this->template->set('templateData', $this->templateData);
            Breadcrumbs::add(Breadcrumb::factory()->set_title($this->templateData['map']->name)->set_url(URL::base() . 'labyrinthManager/global/' . $mapId));
            Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Counters'))->set_url(URL::base() . 'counterManager/index/' . $mapId));
            Breadcrumbs::add(Breadcrumb::factory()->set_title($this->templateData['counter']->name)->set_url(URL::base() . 'counterManager/editCounter/' . $mapId . '/' . $counterId));
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

    public function action_updateCounter()
    {
        $mapId = $this->request->param('id', null);
        $counterId = $this->request->param('id2', null);
        if ($_POST and $mapId != null and $counterId != null) {
            DB_ORM::model('map_counter')->updateCounter($counterId, $_POST);
            Request::initial()->redirect(URL::base() . 'counterManager/editCounter/' . $mapId . '/' . $counterId);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

    public function action_deleteRule()
    {
        $mapId = $this->request->param('id', null);
        $counterId = $this->request->param('id2', null);
        $ruleId = $this->request->param('id3', null);
        $nodeId = $this->request->param('id4', null);

        if ($mapId == null OR $counterId == null OR $ruleId == null OR $nodeId == null) {
            Request::initial()->redirect(URL::base());
        }

        DB_ORM::model('map_counter_rule', array((int)$ruleId))->delete();
        Request::initial()->redirect(URL::base() . 'counterManager/editCounter/' . $mapId . '/' . $counterId);
    }

    public function action_addRule()
    {
        $mapId = $this->request->param('id', null);
        $counterId = $this->request->param('id2', null);

        if ($mapId == null OR $counterId == null) {
            Request::initial()->redirect(URL::base());
        }

        DB_ORM::model('map_counter_rule')->addRule($counterId, $this->request->post());
        Request::initial()->redirect(URL::base() . 'counterManager/editCounter/' . $mapId . '/' . $counterId);
    }

    public function action_deleteCounter()
    {
        $mapId = $this->request->param('id', null);
        $counterId = $this->request->param('id2', null);

        if ($mapId != null and $counterId != null) {
            DB_ORM::model('User')->can('edit', array('mapId' => $mapId));
            DB_ORM::model('map_node_counter')->deleteAllNodeCounterByCounter((int)$counterId);
            DB_ORM::model('map_popup_counter')->deleteCounters((int)$counterId, 'counter_id');
            DB_ORM::model('map_counter', array((int)$counterId))->delete();
            Request::initial()->redirect(URL::base() . 'counterManager/index/' . $mapId);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

    public function action_grid()
    {
        $mapId = $this->request->param('id', null);
        $counterId = $this->request->param('id2', null);

        if ($mapId == null) {
            Request::initial()->redirect(URL::base());
        }

        DB_ORM::model('User')->can('edit', array('mapId' => $mapId));
        $this->templateData['map'] = DB_ORM::model('map', array((int)$mapId));
        $this->templateData['nodes'] = DB_ORM::model('map_node')->getNodesByMap((int)$mapId);
        $this->templateData['popups'] = DB_ORM::model('map_popup')->getAllMapPopups((int)$mapId);

        if ($counterId == null) {
            $this->templateData['counters'] = DB_ORM::model('map_counter')->getCountersByMap((int)$mapId);
        } else {
            $this->templateData['counters'][] = DB_ORM::model('map_counter', array((int)$counterId));
            $this->templateData['oneCounter'] = true;
        }

        $this->templateData['center'] = View::factory('labyrinth/counter/grid')->set('templateData',
            $this->templateData);
        $this->templateData['left'] = View::factory('labyrinth/labyrinthEditorMenu')->set('templateData',
            $this->templateData);
        $this->template->set('templateData', $this->templateData);

        Breadcrumbs::add(Breadcrumb::factory()->set_title($this->templateData['map']->name)->set_url(URL::base() . 'labyrinthManager/global/' . $mapId));
        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Counter Grid'))->set_url(URL::base() . 'counterManager/grid/' . $mapId));
    }

    public function action_updateGrid()
    {
        $mapId = $this->request->param('id', null);
        $counterId = $this->request->param('id2', null);
        $post = $this->request->post();

        if (!$post OR $mapId == null) {
            Request::initial()->redirect(URL::base());
        }

        if ($counterId != null) {
            DB_ORM::model('map_node_counter')->updateNodeCounters($post, (int)$counterId, (int)$mapId);
            Request::initial()->redirect(URL::base() . 'counterManager/grid/' . $mapId . '/' . $counterId);
        } else {
            DB_ORM::model('map_node_counter')->updateNodeCounters($post, null, (int)$mapId);
            foreach (Arr::get($post, 'pc', array()) as $popupId => $counters) {
                DB_ORM::model('map_popup_counter')->updatePopupCounters($counters, $popupId);
            }
            Request::initial()->redirect(URL::base() . 'counterManager/grid/' . $mapId);
        }
    }

    public function action_previewCounter()
    {
        $mapId = $this->request->param('id', null);
        $counterId = $this->request->param('id2', null);
        if ($counterId != null) {
            $this->templateData['map'] = DB_ORM::model('map', array((int)$mapId));
            $this->templateData['counter'] = DB_ORM::model('map_counter', array((int)$counterId));

            $this->template = View::factory('labyrinth/counter/preview');
            $this->template->set('templateData', $this->templateData);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

    public function action_rules()
    {
        $mapId = $this->request->param('id', null);

        if ($mapId == null) {
            Request::initial()->redirect(URL::base());
        }

        DB_ORM::model('User')->can('edit', array('mapId' => $mapId));
        $this->templateData['map'] = DB_ORM::model('map', array((int)$mapId));
        $this->templateData['rules'] = DB_ORM::model('map_counter_commonrules')->getRulesByMapId($mapId, 'all');

        $this->ruleDisplay($mapId);

        $this->templateData['center'] = View::factory('labyrinth/counter/rules')->set('templateData',
            $this->templateData);
        $this->templateData['left'] = View::factory('labyrinth/labyrinthEditorMenu')->set('templateData',
            $this->templateData);
        $this->template->set('templateData', $this->templateData);

        Breadcrumbs::add(Breadcrumb::factory()->set_title($this->templateData['map']->name)->set_url(URL::base() . 'labyrinthManager/global/' . $mapId));
        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Rules'))->set_url(URL::base() . 'counterManager/rules/' . $mapId));
    }

    public function action_addCommonRule()
    {
        $this->commonRule();
    }

    public function action_editCommonRule()
    {
        $this->commonRule();
    }

    function commonRule()
    {
        $mapId = $this->request->param('id', null);
        $ruleId = $this->request->param('id2', null);
        if ($mapId) {
            DB_ORM::model('User')->can('edit', array('mapId' => $mapId));

            $this->templateData['map'] = DB_ORM::model('map', array((int)$mapId));
            if ($ruleId) {
                $this->templateData['commonRule'] = DB_ORM::model('map_counter_commonrules', array((int)$ruleId));
            }

            $this->ruleDisplay($mapId);

            $this->templateData['center'] = View::factory('labyrinth/counter/actionCommonRule')->set('templateData',
                $this->templateData);
            $this->templateData['left'] = View::factory('labyrinth/labyrinthEditorMenu')->set('templateData',
                $this->templateData);
            $this->template->set('templateData', $this->templateData);

            Breadcrumbs::add(Breadcrumb::factory()->set_title($this->templateData['map']->name)->set_url(URL::base() . 'labyrinthManager/global/' . $mapId));
            Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Rules'))->set_url(URL::base() . 'counterManager/rules/' . $mapId));
            $ruleId
                ? Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Edit Rule'))->set_url(URL::base() . 'counterManager/editCommonRule/' . $mapId))
                : Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Add Rule'))->set_url(URL::base() . 'counterManager/addCommonRule/' . $mapId));
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

    function ruleDisplay($mapId)
    {
        // ----- node display ----- //
        $nodesArray = DB_ORM::model('map_node')->getNodesByMap($mapId, null, null, true);
        $nodes = array();
        $ids = array();
        foreach ($nodesArray as $node) {
            $word = html_entity_decode($node->title, ENT_QUOTES);
            $word = mb_ereg_replace(__('[^a-zA-Z0-9_.,; ]'), '', $word);
            $nodes[] = $word;
            $ids[] = '[[NODE:' . $node->id . ']]';

        }
        $this->templateData['nodes']['text'] = json_encode($nodes);
        $this->templateData['nodes']['id'] = json_encode($ids);
        // ----- end node display ----- //

        // ----- counter display ----- //
        $countersArray = DB_ORM::model('map_counter')->getCountersByMap($mapId, true);
        $counters = array();
        $ids = array();
        foreach ($countersArray as $counter) {
            $word = html_entity_decode($counter->name, ENT_QUOTES);
            $word = mb_ereg_replace(__('[^a-zA-Z0-9_.,; ]'), '', $word);
            $counters[] = $word;
            $ids[] = '[[CR:' . $counter->id . ']]';
        }
        $this->templateData['counters']['text'] = json_encode($counters);
        $this->templateData['counters']['id'] = json_encode($ids);
        // ----- end counter display ----- //

        // ----- step display ----- //
        $scenariosText = array();
        $scenariosId = array();
        $scenario = array();
        $conditionsAssign = array();
        $mapSections = DB_ORM::select('Map_Node_Section')->where('map_id', '=', $mapId)->query()->as_array();
        $scenarioByReference = DB_ORM::select('Webinar_Map')->where('reference_id', '=', $mapId)->where('which', '=',
            'labyrinth')->group_by('webinar_id')->query()->as_array();
        foreach ($scenarioByReference as $scenarioObj) {
            $scenario[$scenarioObj->webinar_id] = $scenarioObj;
        }
        foreach ($mapSections as $mapSection) {
            $scenarioSections = DB_ORM::select('Webinar_Map')->where('reference_id', '=',
                $mapSection->id)->where('which', '=', 'section')->query()->as_array();
            foreach ($scenarioSections as $scenarioSection) {
                $scenario[$scenarioSection->webinar_id] = $scenarioSection;
            }
        }
        foreach ($scenario as $scenarioId => $scenarioObj) {
            $conditionsAssign = DB_ORM::select('Conditions_Assign')->where('scenario_id', '=',
                $scenarioId)->group_by('condition_id')->query()->as_array();
            $steps = DB_ORM::model('Webinar', $scenarioId)->steps;
            foreach ($steps as $step) {
                $word = html_entity_decode($step->name, ENT_QUOTES);
                $word = mb_ereg_replace(__('[^a-zA-Z0-9_.,; ]'), '', $word);
                $scenariosText[] = $word;
                $scenariosId[] = '[[STEP:' . $step->id . ']]';
            }
        }
        $this->templateData['steps']['text'] = json_encode($scenariosText);
        $this->templateData['steps']['id'] = json_encode($scenariosId);
        // ----- end step display ----- //

        // ----- conditions display ----- //
        $conditionsText = array();
        $conditionsId = array();
        foreach ($conditionsAssign as $conditionAssign) {
            $condition = DB_ORM::model('Conditions', array($conditionAssign->condition_id));
            $word = html_entity_decode($condition->name, ENT_QUOTES);
            $word = mb_ereg_replace(__('[^a-zA-Z0-9_.,; ]'), '', $word);
            $conditionsText[] = $word;
            $conditionsId[] = '[[COND:' . $condition->id . ']]';
        }
        $this->templateData['conditions']['text'] = json_encode($conditionsText);
        $this->templateData['conditions']['id'] = json_encode($conditionsId);
        // ----- end conditions display ----- //
    }

    public function action_updateCommonRule()
    {
        $mapId = $this->request->param('id', false);
        $ruleId = $this->request->param('id2', false);
        $post = $this->request->post();
        $ruleText = Arr::get($post, 'commonRule', '');
        $ruleCorrect = Arr::get($post, 'isCorrect', 0);
        $lightning = Arr::get($post, 'lightning');
        $lightning = $lightning == 'on' ? 1 : 0;

        if (!$mapId) {
            Request::initial()->redirect(URL::base());
        }

        if ($ruleId) {
            DB_ORM::model('map_counter_commonrules')->editRule($ruleId, $ruleText, $ruleCorrect, $lightning);
        } else {
            $ruleId = DB_ORM::model('map_counter_commonrules')->addRule($mapId, $ruleText, $ruleCorrect, $lightning);
        }

        $cron = DB_ORM::select('Cron')->where('rule_id', '=', $ruleId)->query()->fetch(0);
        $pattern = '/ALERT|ACTIVATE/';
        preg_match($pattern, $ruleText, $matches);
        if (isset($matches[0]) AND $ruleCorrect) {
            if (!$cron) {
                DB_ORM::model('cron')->add($ruleId);
            }
        } elseif ($cron) {
            $cron->delete();
        }

        Request::initial()->redirect(URL::base() . 'counterManager/rules/' . $mapId);
    }

    public function action_deleteCommonRule()
    {
        $mapId = $this->request->param('id', null);
        $ruleId = $this->request->param('id2', null);
        if ($mapId AND $ruleId) {
            DB_ORM::model('User')->can('edit', array('mapId' => $mapId));
            DB_ORM::model('map_counter_commonrules', array((int)$ruleId))->delete();
            Request::initial()->redirect(URL::base() . 'counterManager/rules/' . $mapId);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

    /** ajax request */
    public function action_checkCommonRule()
    {
        $this->auto_render = false;
        $mapId = Arr::get($this->request->post(), 'mapId', null);
        $ruleText = Arr::get($this->request->post(), 'ruleText', null);
        $status = true;

        if ($mapId) {
            $counters = DB_ORM::model('map_counter')->getCountersByMap($mapId);
            $values = array();

            foreach ($counters as $counter) {
                $values[$counter->id] = $counter->start_value;
            }

            $runtimeLogic = new RunTimeLogic();
            $runtimeLogic->values = $values;

            $array = $runtimeLogic->parsingString($ruleText);

            if (count($array['errors'])) {
                $status = false;
            }
        } else {
            $status = false;
        }

        exit ($status);
    }
}