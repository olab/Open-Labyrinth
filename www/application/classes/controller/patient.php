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

class Controller_Patient extends Controller_Base {

    public function before()
    {
        parent::before();
    }

    public function action_index()
    {
        $patients = DB_SQL::select('default')->from('patient')->query()->as_array();
        $this->templateData['patients']  = $patients;
        $this->templateData['scenarios'] = array();
        foreach ($patients as $patient)
        {
            $idPatient = $patient['id'];
            foreach (DB_ORM::model('Patient_Scenario')->getScenarioByPatient($idPatient) as $obj)
            {
                $this->templateData['scenarios'][$patient['id']][] = $obj->id_scenario.': '.DB_ORM::model('Webinar', $obj->id_scenario)->title;
            }
        }
        $this->templateData['center']    = View::factory('patient/grid')->set('templateData', $this->templateData);
        $this->template->set('templateData', $this->templateData);
        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('My Labyrinths'))->set_url(URL::base().'patient/index/'));
    }

    public function action_management ()
    {
        $id_patient = $this->request->param('id');
        $patient    = DB_ORM::model('Patient', array($id_patient));

        $this->templateData['patient']              = $patient;
        $this->templateData['all_patients']         = DB_ORM::select('Patient')->query()->as_array();
        $this->templateData['patient_conditions']   = DB_ORM::model('Patient_ConditionRelation')->get_conditions($id_patient);
        $this->templateData['scenario']             = DB_ORM::model('Webinar')->getAllWebinars();
        $this->templateData['patient_scenarios']    = DB_ORM::model('Patient_Scenario')->getPatientScenario($id_patient);
        $this->templateData['patient_type']         = DB_ORM::model('Patient')->allType;
        $this->templateData['center']               = View::factory('patient/management')->set('templateData', $this->templateData);
        $this->template->set('templateData', $this->templateData);
    }

    public function action_update()
    {
        $post               = $this->request->post();
        $patient_name       = Arr::get($post, 'name');
        $patient_scenarios  = Arr::get($post, 'scenarios', array());
        $scenario_delete    = Arr::get($post, 'scenario_delete', array());
        $patientType        = Arr::get($post, 'patientType', array());
        $conditions         = Arr::get($post, 'conditions', array());
        $conditions_names   = Arr::get($conditions, 'name', array());
        $conditions_value   = Arr::get($conditions, 'value', array());
        $conditions_ids     = Arr::get($conditions, 'id', array());
        $id_patient         = DB_ORM::model('Patient')->update($patient_name, $patientType, $this->request->param('id'));

        // Condition, and condition relation save
        foreach ($conditions_names as $id=>$name)
        {
            $id_condition = ($conditions_ids[$id] == 'new') ? FALSE : $conditions_ids[$id];
            $id_condition = DB_ORM::model('Patient_Condition')->update($name, $conditions_value[$id], $id_condition);
            DB_ORM::model('Patient_ConditionRelation')->check_and_create($id_patient, $id_condition);
        }

        // Patient scenario save
        foreach ($patient_scenarios as $k=>$idScenario)
        {
            if (substr_count($k, 'id')) DB_ORM::model('Patient_Scenario')->update(substr($k, 2), $id_patient, $idScenario);
            else DB_ORM::model('Patient_Scenario')->create($id_patient, $idScenario);
        }

        // Patient scenario delete
        foreach ($scenario_delete as $id)
        {
            DB_ORM::model('Patient_Scenario')->deleteRecord($id);
        }

        Request::initial()->redirect(URL::base().'patient/index');
    }

    public function action_labyrinth()
    {
        $id_map                 = $this->request->param('id');
        $id_patient_selected    = $this->request->param('id2');

        $nodes                  = DB_ORM::model('map_node')->getNodesByMap((int) $id_map);

        $this->templateData['map']      = DB_ORM::model('map', array($id_map));
        $this->templateData['left']     = View::factory('labyrinth/labyrinthEditorMenu')->set('templateData', $this->templateData);
        $this->templateData['nodes']    = $nodes;
        $this->templateData['patients'] = DB_ORM::model('Patient_Scenario')->getPatientsByMap($id_map);

        foreach ($nodes as $node)
        {
            $id_node = $node->id;
            $records = DB_ORM::select('Patient_ConditionChange')->where('id_node', '=', $id_node)->query();
            foreach ($records as $record)
            {
                $this->templateData['existing_data'][$node->id][$record->id_condition]['value'] = $record->value;
                $this->templateData['existing_data'][$node->id][$record->id_condition]['appear'] = $record->appear;
            }
        }

        if ($id_patient_selected)
        {
            $this->templateData['selected_patient'] = DB_ORM::model('Patient', array($id_patient_selected));
            $this->templateData['patient_condition'] = DB_ORM::model('Patient_ConditionRelation')->get_conditions($id_patient_selected);
        }

        $this->templateData['center'] = View::factory('patient/labyrinth')->set('templateData', $this->templateData);
        $this->template->set('templateData', $this->templateData);

        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('My Labyrinths'))->set_url(URL::base().'authoredLabyrinth'));
        Breadcrumbs::add(Breadcrumb::factory()->set_title($this->templateData['map']->name)->set_url(URL::base().'labyrinthManager/global/'.$id_map));
        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Users'))->set_url(URL::base().'patient/labyrinth/'.$id_map));
    }

    public function action_delete_patient()
    {
        $id_patient = $this->request->param('id');
        DB_ORM::model('Patient_ConditionRelation')->deletePatientConditions($id_patient);
        DB_ORM::delete('Patient')->where('id', '=', $id_patient)->execute();
        Request::$initial->redirect(URL::base().'patient/index');
    }

    public function action_condition()
    {
        $id_patient = $this->request->param('id');
        foreach ($this->request->post('dbn') as $id_node=>$condition_data)
        {
            foreach ($condition_data as $id_condition=>$data)
            {
                DB_ORM::model('Patient_ConditionChange')->create_or_update($id_node, $id_condition, Arr::get($data, 'value', 0), Arr::get($data, 'appear', 0), $id_patient);
            }
        }
        Request::$initial->redirect($this->request->referrer());
    }

    public function action_connection ()
    {
        $this->templateData['connection']    = DB_ORM::select('Patient_Rule')->query()->as_array();
        $this->templateData['center']   = View::factory('patient/connection/grid')->set('templateData', $this->templateData);
        $this->template->set('templateData', $this->templateData);
    }

    public function action_connectionDelete ()
    {
        $connectionId = $this->request->param('id');
        $result = DB_ORM::model('Patient_Rule', array($connectionId));
        $result->delete();
        Request::$initial->redirect($this->request->referrer());
    }

    public function action_connectionManage ()
    {
        $connectionId = $this->request->param('id');

        $sameA      = DB_ORM::select('Patient')->where('type', '=', 'Parallel same set')->query()->as_array();
        $sameB      = DB_ORM::select('Patient')->where('type', '=', 'Longitudinal same set')->query()->as_array();
        $differentA = DB_ORM::select('Patient')->where('type', '=', 'Parallel different set')->query()->as_array();
        $differentB = DB_ORM::select('Patient')->where('type', '=', 'Longitudinal different set')->query()->as_array();

        $this->templateData['patientSame']      = array_merge($sameA, $sameB);
        $this->templateData['patientDifferent'] = array_merge($differentA, $differentB);
        $this->templateData['connection']       = DB_ORM::model('Patient_Rule', array($connectionId));
        $this->templateData['center']           = View::factory('patient/connection/management')->set('templateData', $this->templateData);
        $this->template->set('templateData', $this->templateData);
    }

    public function action_ajaxGetCondition ()
    {
        $patientId          = $this->request->param('id');
        $result             = array();
        $conditionsRelation = DB_ORM::select('Patient_ConditionRelation')->where('id_patient', '=', $patientId)->query()->as_array();

        foreach ($conditionsRelation as $conditionRelation)
        {
            $condition = DB_ORM::model('Patient_Condition', array($conditionRelation->id_condition));
            $result[$condition->id] = $condition->name;
        }

        exit(json_encode($result));
    }

    public function action_updateRule()
    {
        $connectionId = $this->request->param('id');
        $rule = $this->request->post('rule');
        $correct = 1;
        DB_ORM::model('Patient_Rule')->update($rule, $correct, $connectionId);
        Request::$initial->redirect(URL::base().'patient/connection');
    }

    public function action_deleteCondition()
    {
        DB_ORM::delete('Patient_Condition')->where('id', '=', $this->request->param('id'))->execute();
        Request::initial()->redirect($this->request->referrer());
    }
}
