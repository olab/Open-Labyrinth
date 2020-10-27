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

class Controller_MapUserManager extends Controller_Base {

    public function before() {
        $this->templateData['labyrinthSearch'] = 1;

        parent::before();

        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('My Labyrinths'))->set_url(URL::base() . 'authoredLabyrinth'));
    }

    public function action_index()
    {
        $mapId          = $this->request->param('id', NULL);
        $authorOrder    = $this->request->param('id2', 0);
        $learnerOrder   = $this->request->param('id3', 0);
        $reviewerOrder  = $this->request->param('id4', 0);
        $groupOrder  = $this->request->param('id5', 0);
        $tiedUsers      = array();

        if ($mapId == NULL) Controller::redirect(URL::base());

        DB_ORM::model('User')->can('edit', array('mapId' => $mapId));
        $this->templateData['map'] = DB_ORM::model('map', array((int) $mapId));

        $userIds = DB_ORM::model('map_user')->getAllUsersIds((int) $mapId);

        if($userIds != null)
        {
            foreach ($userIds as $userId) $tiedUsers[] = $userId;
        }

        $existGroups = array();
        if($this->templateData['map'] != null && count($this->templateData['map']->groups) > 0) {
            foreach($this->templateData['map']->groups as $mapGroup) {
                $existGroups[] = $mapGroup->group_id;
            }
        }

        $authorOrder = $authorOrder == 0 ? 'ASC' : 'DESC';
        $learnerOrder = $learnerOrder == 0 ? 'ASC' : 'DESC';
        $reviewerOrder = $reviewerOrder == 0 ? 'ASC' : 'DESC';
        $groupOrder = $groupOrder == 0 ? 'ASC' : 'DESC';

        $this->templateData['existGroupsIds'] = $existGroups;
        $this->templateData['groups'] = DB_ORM::model('group')->getAllGroups($groupOrder);

        $this->templateData['existAuthors']  = DB_ORM::model('map_user')->getAllAuthors($mapId, $authorOrder);
        $this->templateData['existLearners'] = DB_ORM::model('map_user')->getAllLearners((int) $mapId, $learnerOrder);
        $this->templateData['tiedUsers']     = $tiedUsers;

        $this->templateData['admins']        = DB_ORM::model('user')->getUsersByTypeName('superuser', $userIds, $authorOrder);
        $this->templateData['authors']       = DB_ORM::model('user')->getUsersByTypeName('author', $userIds, $authorOrder);
        $this->templateData['learners']      = DB_ORM::model('user')->getUsersByTypeName('learner', $userIds, $learnerOrder);
        $this->templateData['reviewers']     = DB_ORM::model('user')->getAllReviewers($reviewerOrder);
        $this->templateData['allAdmins']     = array_merge((array)$this->templateData['admins'], (array)$this->templateData['authors']);

        $this->templateData['authorOrder']   = $authorOrder   == 'ASC' ? 0 : 1;
        $this->templateData['learnerOrder']  = $learnerOrder  == 'ASC' ? 0 : 1;
        $this->templateData['reviewerOrder'] = $reviewerOrder == 'ASC' ? 0 : 1;
        $this->templateData['groupOrder']    = $groupOrder    == 'ASC' ? 0 : 1;

        $this->templateData['left']          = View::factory('labyrinth/labyrinthEditorMenu')->set('templateData', $this->templateData);
        $this->templateData['center']        = View::factory('labyrinth/user/view')->set('templateData', $this->templateData);
        $this->template->set('templateData', $this->templateData);

        Breadcrumbs::add(Breadcrumb::factory()->set_title($this->templateData['map']->name)->set_url(URL::base().'labyrinthManager/global/'.$mapId));
        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Users'))->set_url(URL::base().'mapUserManager/index/'.$mapId));
    }

    public function action_addUser()
    {
        $mapId          = $this->request->param('id', NULL);
        $authorOrder    = $this->request->param('id2', 0);
        $learnerOrder   = $this->request->param('id3', 0);
        $post           = $this->request->post();

        if (! ($post AND $mapId)) Controller::redirect(URL::base());

        DB_ORM::model('User')->can('edit', array('mapId' => $mapId));
        // ---- reviewer ---- //
        $allReviewers   = array();
        $map_reviewers  = array();
        $reviewers_post = Arr::get($post, 'reviewer', array());

        foreach (DB_ORM::model('user')->getAllReviewers() as $reviewer) $allReviewers[] = $reviewer->id;

        $map_users = DB_ORM::model('map_user')->getAllUsersIds($mapId);
        if ($map_users != null) $map_reviewers = array_intersect($allReviewers, $map_users);

        foreach (array_diff($reviewers_post, $map_reviewers) as $id_reviewer)
        {
            DB_ORM::insert('Map_User')->column('map_id', $mapId)->column('user_id', $id_reviewer)->execute();
        }

        foreach (array_diff($map_reviewers, $reviewers_post) as $id_reviewer)
        {
            DB_ORM::delete('Map_User')->where('map_id', '=', $mapId)->where('user_id', '=', $id_reviewer)->execute();
        }
        // ---- end reviewer ---- //

        $existAuthors   = DB_ORM::model('map_user')->getAllAuthors((int) $mapId);
        $existLearners  = DB_ORM::model('map_user')->getAllLearners((int) $mapId);
        $existUserMap   = array();

        foreach($existAuthors as $author) {
            $existUserMap[$author->id] = $author;
        }

        foreach($existLearners as $learner) {
            $existUserMap[$learner->id] = $learner;
        }

        $admins        = DB_ORM::model('user')->getUsersByTypeName('superuser');
        $authors       = DB_ORM::model('user')->getUsersByTypeName('author');
        $learners      = DB_ORM::model('user')->getUsersByTypeName('learner');
        $allUsers      = array_merge($admins, $authors, $learners);

        foreach ($allUsers as $user)
        {
            $isExist = Arr::get($_POST, 'user'.$user->id, null);
            if($isExist != null)
            {
                if ( ! isset($existUserMap[$user->id])) DB_ORM::model('map_user')->addUser($mapId, $user->id);
            }
            else DB_ORM::model('map_user')->deleteByUserId($mapId, $user->id);
        }

        // ---- groups ---- //
        $groupIds = Arr::get($post, 'groups', array());
        if(count($groupIds) > 0) {
            DB_ORM::model('map_group')->removeGroups($mapId, $groupIds, 'NOT IN');
            DB_ORM::model('map_group')->addNewGroups($mapId, $groupIds);
        }else{
            DB_ORM::model('map_group')->removeAllGroups($mapId);
        }
        // ---- end groups ---- //

        Controller::redirect(URL::base().'mapUserManager/index/'.$mapId.'/'.$authorOrder.'/'.$learnerOrder);
    }

    public function action_removeAllGroups(){
        $mapId = $this->request->param('id', NULL);
        $authorOrder = $this->request->param('id2', 0);
        $learnerOrder = $this->request->param('id3', 0);
        if(!empty($mapId)) {
            DB_ORM::model('map_group')->removeAllGroups($mapId);
            Controller::redirect(URL::base() . 'mapUserManager/index/' . $mapId . '/' . $authorOrder . '/' . $learnerOrder);
        }else{
            Controller::redirect(URL::base());
        }
    }

    public function action_addAllGroups(){
        $mapId = $this->request->param('id', NULL);
        $authorOrder = $this->request->param('id2', 0);
        $learnerOrder = $this->request->param('id3', 0);
        if(!empty($mapId)) {
            $groupIds = DB_ORM::model('group')->getAllGroupsId();
            DB_ORM::model('map_group')->addNewGroups($mapId, $groupIds);
            Controller::redirect(URL::base() . 'mapUserManager/index/' . $mapId . '/' . $authorOrder . '/' . $learnerOrder);
        }else{
            Controller::redirect(URL::base());
        }
    }

    public function action_deleteUser() {
        $mapId = $this->request->param('id', NULL);
        $userId = $this->request->param('id2', NULL);
        if ($mapId != NULL)
        {
            DB_ORM::model('User')->can('edit', array('mapId' => $mapId));
            DB_ORM::model('map_user')->deleteByUserId($mapId, $userId);
            Controller::redirect(URL::base() . 'mapUserManager/index/' . $mapId);
        }
        else Controller::redirect(URL::base());
    }

    public function action_addAllLearners() {
        $mapId = $this->request->param('id', NULL);
        $authorOrder = $this->request->param('id2', 0);
        $learnerOrder = $this->request->param('id3', 0);
        if ($mapId != NULL)
        {
            DB_ORM::model('User')->can('edit', array('mapId' => $mapId));
            DB_ORM::model('map_user')->addAllLearners($mapId);
            Controller::redirect(URL::base() . 'mapUserManager/index/' . $mapId . '/' . $authorOrder . '/' . $learnerOrder);
        }
        else Controller::redirect(URL::base());
    }

    public function action_removeAllLearners()
    {
        $mapId = $this->request->param('id', NULL);
        $authorOrder = $this->request->param('id2', 0);
        $learnerOrder = $this->request->param('id3', 0);
        if ($mapId != NULL)
        {
            DB_ORM::model('User')->can('edit', array('mapId' => $mapId));
            DB_ORM::model('map_user')->removeAllLearners($mapId);
            Controller::redirect(URL::base() . 'mapUserManager/index/' . $mapId . '/' . $authorOrder . '/' . $learnerOrder);
        }
        else Controller::redirect(URL::base());
    }

    public function action_addAllAuthors() {
        $mapId = $this->request->param('id', NULL);
        if ($mapId != NULL)
        {
            DB_ORM::model('User')->can('edit', array('mapId' => $mapId));
            DB_ORM::model('map_user')->addAllAuthors($mapId);
            Controller::redirect(URL::base() . 'mapUserManager/index/' . $mapId);
        }
        else Controller::redirect(URL::base());
    }

    public function action_removeAllAuthors()
    {
        $mapId = $this->request->param('id', NULL);
        if ($mapId != NULL)
        {
            DB_ORM::model('User')->can('edit', array('mapId' => $mapId));
            DB_ORM::model('map_user')->removeAllAuthors($mapId);
            Controller::redirect(URL::base() . 'mapUserManager/index/' . $mapId);
        }
        else Controller::redirect(URL::base());
    }

    public function action_addAllReviewers()
    {
        $mapId = $this->request->param('id');
        if ($mapId != NULL)
        {
            DB_ORM::model('User')->can('edit', array('mapId' => $mapId));
            DB_ORM::model('map_user')->addAllReviewers($mapId);
            Controller::redirect(URL::base().'mapUserManager/index/'.$mapId);
        }
        else Controller::redirect(URL::base());
    }

    public function action_removeAllReviewers()
    {
        $mapId = $this->request->param('id');
        if ($mapId != NULL)
        {
            DB_ORM::model('User')->can('edit', array('mapId' => $mapId));
            DB_ORM::model('map_user')->removeAllReviewers($mapId);
            Controller::redirect(URL::base().'mapUserManager/index/'.$mapId);
        }
        else Controller::redirect(URL::base());
    }
}

