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
        $this->templateData['patients']  = DB_SQL::select('default')->from('patient')->query()->as_array();
        $this->templateData['r_patient'] =array();

        foreach (DB_ORM::select('Patient_Relation')->query()->as_array() as $record)
        {
            $id_first_patient  = $record->id_first_patient;
            $id_second_patient = $record->id_second_patient;
            $this->templateData['r_patient'][$id_first_patient][$id_second_patient] = DB_ORM::model('Patient', array($id_second_patient))->name;
            $this->templateData['r_patient'][$id_second_patient][$id_first_patient] = DB_ORM::model('Patient', array($id_first_patient))->name;
        }

        $this->templateData['center'] = View::factory('patient/grid')->set('templateData', $this->templateData);
        $this->template->set('templateData', $this->templateData);
        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('My Labyrinths'))->set_url(URL::base().'patient/index/'));
    }

    public function action_management ()
    {
        $id_patient = $this->request->param('id');

        $this->templateData['patient']              = DB_ORM::model('Patient', array($id_patient));
        $this->templateData['all_patients']         = DB_ORM::select('Patient')->query()->as_array();
        $this->templateData['patient_conditions']   = DB_ORM::model('Patient_ConditionRelation')->get_conditions($id_patient);
        $this->templateData['r_patient']            = DB_ORM::select('Patient_Relation')->where('id_first_patient', '=', $id_patient)->query()->fetch(0);
        $this->templateData['maps']                 = DB_ORM::model('Map')->getAllEnabledMap();
        $this->templateData['patient_id_maps']      = DB_ORM::model('Patient_Map')->get_maps_id($id_patient);
        $this->templateData['center']               = View::factory('patient/management')->set('templateData', $this->templateData);
        $this->template->set('templateData', $this->templateData);
    }

    public function action_update()
    {
        $id_patient         = $this->request->param('id');
        $post               = $this->request->post();
        $conditions         = Arr::get($post, 'conditions', array());
        $patient_name       = Arr::get($post, 'name');
        $patient_maps       = Arr::get($post, 'maps');
        $related_patient    = Arr::get($post, 'r_patient');
        $conditions_names   = Arr::get($conditions, 'name', array());
        $conditions_value   = Arr::get($conditions, 'value', array());
        $conditions_ids     = Arr::get($conditions, 'id', array());
        $id_patient = DB_ORM::model('Patient')->update($patient_name, $id_patient);

        // Condition, and condition relation save
        foreach ($conditions_names as $id=>$name)
        {
            $id_condition = ($conditions_ids[$id] == 'new') ? FALSE : $conditions_ids[$id];
            $id_condition = DB_ORM::model('Patient_Condition')->update($name, $conditions_value[$id], $id_condition);
            DB_ORM::model('Patient_ConditionRelation')->check_and_create($id_patient, $id_condition);
        }

        // Patient map save
        $queue = 0;
        foreach ($patient_maps as $k=>$id_map)
        {
            if (substr_count($k, 'id'))
            {
                $id_record = substr($k, 2);
                DB_ORM::update('Patient_Map')
                    ->set('id_map', $id_map)
                    ->where('id', '=', $id_record)
                    ->execute();
                // count position in queue for new record
                $queue = DB_ORM::model('Patient_Map', array($id_record))->queue+1;
            }
            else
            {
                DB_ORM::insert('Patient_Map')
                    ->column('id_patient', $id_patient)
                    ->column('id_map', $id_map)
                    ->column('queue', $queue)
                    ->execute();
            }
        }
        // Related patient save
        if ($related_patient) DB_ORM::model('Patient_Relation')->update($id_patient, $related_patient);

        Request::initial()->redirect(URL::base().'patient/index');
    }

    /**
     * @param array $maps of map id
     * @param $id_patient
     */
    public function update_patient_map(array $maps, $id_patient)
    {
        $maps_from_db = array();
        foreach (DB_ORM::select('Patient_Map')->where('id_patient', '=', $id_patient)->query()->as_array() as $map)
        {
            $maps_from_db[] = $map->id_map;
        }
        foreach (array_diff($maps_from_db, $maps) as $id_map_for_delete)
        {
            DB_ORM::delete('Patient_Map')->where('id_map', '=', $id_map_for_delete)->execute();
        }
        foreach (array_diff($maps, $maps_from_db) as $new_map_id)
        {
            DB_ORM::insert('Patient_Map')->column('id_map', $new_map_id)->column('id_patient', $id_patient)->execute();
        }
    }

    public function action_labyrinth()
    {
        $id_map                 = $this->request->param('id');
        $id_patient_selected    = $this->request->param('id2');
        $id_patients_by_map     = DB_ORM::model('Patient_Map')->get_patients_id($id_map);
        $nodes                  = DB_ORM::model('map_node')->getNodesByMap((int) $id_map);

        $this->templateData['map']      = DB_ORM::model('map', array($id_map));
        $this->templateData['left']     = View::factory('labyrinth/labyrinthEditorMenu')->set('templateData', $this->templateData);
        $this->templateData['nodes']    = $nodes;
        $this->templateData['patients'] = array();

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

        foreach ($id_patients_by_map as $id_patient)
        {
            $this->templateData['patients'][] = DB_ORM::model('Patient', array($id_patient));
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
        DB_ORM::model('Patient_ConditionRelation')->delete_condition($id_patient);
        DB_ORM::delete('Patient')->where('id', '=', $id_patient)->execute();
        Request::$initial->redirect(URL::base().'patient/index');
    }

    public function action_delete_from_edit()
    {
        $id_patient = $this->request->param('id');
        $id         = $this->request->param('id2');
        $type       = $this->request->param('id3');

        switch ($type)
        {
            case 'condition':
                DB_ORM::delete('Patient_Condition')->where('id', '=', $id)->execute();
            break;
            case 'labyrinth':
                DB_ORM::delete('Patient_Map')->where('id_patient', '=', $id_patient)->where('id_map', '=', $id)->execute();;
            break;
            case 'relation':
                DB_ORM::delete('Patient_Relation')->where('id', '=', $id)->execute();;
            break;
        }
        Request::$initial->redirect(URL::base().'patient/management/'.$id_patient);
    }

    public function action_condition()
    {
        $id_patient = $this->request->param('id');
        foreach ($this->request->post('dbn') as $id_node=>$condition_data)
        {
            foreach ($condition_data as $id_condition=>$data)
            {
                DB_ORM::model('Patient_ConditionChange')
                    ->create_or_update($id_node, $id_condition, Arr::get($data, 'value', 0), Arr::get($data, 'appear', 0), $id_patient);
            }
        }
        Request::$initial->redirect($this->request->referrer());
    }

    public function action_assign_grid()
    {
        $this->templateData['assign']   = array();
        $sessions                       = DB_ORM::select('Patient_Sessions')->query()->as_array();
        $this->templateData['session']  = $sessions;

        if ($sessions)
        {
            foreach ($sessions as $session)
            {
                $id_assign = $session->id_assign;
                $assign = DB_ORM::select('Patient_Assign')->where('id_assign', '=', $id_assign)->query()->as_array();
                foreach($assign as $record)
                {
                    $u_or_g = $record->user_or_group;
                    $name = $u_or_g == 'user'
                            ? DB_ORM::model('User', array($record->id_user))->username
                            : DB_ORM::model('Group', array($record->id_group))->name;

                    $this->templateData['assign'][$id_assign]['who'][] = $record->user_or_group.': '.$name;
                }
                $this->templateData['assign'][$id_assign]['patient'] = DB_ORM::model('Patient', array($session->id_patient))->name;
                $this->templateData['assign'][$id_assign]['type']    = DB_ORM::model('Patient_Type', array($session->id_type))->type;
            }
        }
        $this->templateData['center'] = View::factory('patient/assign/grid')->set('templateData', $this->templateData);
        $this->template->set('templateData', $this->templateData);
    }

    public function action_assign_management()
    {
        $id_assign  = $this->request->param('id');

        if ($id_assign)
        {
            $session = DB_ORM::select('Patient_Sessions')->where('id_assign', '=', $id_assign)->query()->fetch(0);

            if ($session)
            {
                $id_patient = $session->id_patient;
                $this->templateData['s_patient'] = $id_patient;
                $this->templateData['s_type']    = $session->id_type;
            }
            $this->templateData['s_assign']  = DB_ORM::select('Patient_Assign')->where('id_assign', '=', $id_assign)->query()->as_array();
            $this->templateData['id_assign'] = $id_assign;
        }

        $this->templateData['patients']         = DB_ORM::select('Patient')->query()->as_array();
        $this->templateData['patient_type']     = DB_ORM::select('Patient_Type')->query()->as_array();
        $this->templateData['users']            = DB_ORM::model('User')->getAllUsers();
        $this->templateData['groups']           = DB_ORM::model('Group')->getAllGroups();
        $this->templateData['center']           = View::factory('patient/assign/management')->set('templateData', $this->templateData);
        $this->template->set('templateData', $this->templateData);
    }

    public function action_assign_save()
    {
        $id_assign   = $this->request->param('id', false);
        $post        = $this->request->post();
        $assign_type = Arr::get($post, 'type', 0);
        $assign      = Arr::get($post, 'assign', array());
        $id_patient  = Arr::get($post, 'patient', 0);
        $maps        = Arr::get($post, 'maps', array());

        $maps = array_unique($maps);
        $this->update_patient_map($maps, $id_patient);

        $id_assign = DB_ORM::model('Patient_Assign')->update($id_assign, $assign);
        DB_ORM::model('Patient_Sessions')->create($id_assign, $id_patient, $assign_type);

        Request::$initial->redirect(URL::base().'patient/assign_grid');
    }

    public function action_assign_delete()
    {
        $id_assign = $this->request->param('id');
        DB_ORM::delete('Patient_Assign')->where('id_assign', '=', $id_assign)->execute();
        Request::$initial->redirect(URL::base().'patient/assign_grid');
    }

    public function action_delete_assign_record()
    {
        $id_assign      = $this->request->param('id');
        $id             = $this->request->param('id2');
        $user_or_group  = $this->request->param('id3') ? 'user' : 'group';

        DB_ORM::delete('Patient_assign')->where('id_assign', '=', $id_assign)->where('id_'.$user_or_group, '=', $id)->execute();

        Request::$initial->redirect(URL::base().'patient/assign_management/'.$id_assign);
    }

    public function action_rule ()
    {
        $this->templateData['rules']    = DB_ORM::select('Patient_Rule')->query()->as_array();
        $this->templateData['center']   = View::factory('patient/rule/grid')->set('templateData', $this->templateData);
        $this->template->set('templateData', $this->templateData);
    }

    public function action_rule_add ()
    {
        $nodesArray = DB_ORM::select('Map_Node')->query()->as_array();
        $nodes = array();
        $ids = array();

        foreach($nodesArray as $node){
            $word = html_entity_decode($node->title, ENT_QUOTES);
            $word = preg_replace('/[^a-zA-Z0-9_.,; ]/', '', $word);
            $nodes[] = $word;
            $ids[] = '[[NODE:'.$node->id.']]';
        }

        $this->templateData['nodes']['text'] = json_encode($nodes);
        $this->templateData['nodes']['id'] = json_encode($ids);

        $conditionArray = DB_ORM::select('Patient_Condition')->query()->as_array();
        $conditions = array();
        $ids = array();

        foreach($conditionArray as $condition){
            $word = html_entity_decode($condition->name, ENT_QUOTES);
            $word = preg_replace('/[^a-zA-Z0-9_.,; ]/', '', $word);
            $conditions[] = $word;
            $ids[] = '[[C:'.$condition->id.']]';
        }

        $this->templateData['condition']['text'] = json_encode($conditions);
        $this->templateData['condition']['id'] = json_encode($ids);

        $this->templateData['center'] = View::factory('patient/rule/management')->set('templateData', $this->templateData);
        $this->template->set('templateData', $this->templateData);
    }

    public function action_check_rule ($mapId = '', $ruleText = '', $isAjax = true)
    {
        $status = 1;
        $this->auto_render = false;

        if ($isAjax) $ruleText = Arr::get($this->request->post(),'ruleText',NULL);

        $conditions = DB_ORM::select('Patient_Condition')->query()->as_array();
        $values = array();

        foreach($conditions as $condition) $values[$condition->id] = $condition->value;

        $runtimelogic = new RunTimeLogic();
        $runtimelogic->values = $values;

        $array = $runtimelogic->parsingPatientRule($ruleText);

        if (count($array['errors']) > 0)$status = 0;

        if ($isAjax) echo $status;

        return $status;
    }
}
