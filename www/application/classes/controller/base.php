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

class Controller_Base extends Controller_Template {

    public $template = 'home';
    protected $templateData = array();
    private $unauthorizedRules = array(
        array('controller' => 'home', 'action' => 'login'),
        array('controller' => 'home', 'action' => 'loginOAuth'),
        array('controller' => 'home', 'action' => 'loginOAuthCallback'),
        array('controller' => 'home', 'action' => 'resetPassword'),
        array('controller' => 'home', 'action' => 'passwordMessage'),
        array('controller' => 'home', 'action' => 'confirmLink'),
        array('controller' => 'home', 'action' => 'updateResetPassword'),
        array('controller' => 'reportManager', 'action' => 'showReport'),
        array('controller' => 'renderLabyrinth', 'action' => 'questionResponce'),
        array('controller' => 'updateDatabase', 'action' => 'index'),
    );
    private $authorizedRules = array(
        array('controller' => 'home', 'action' => 'login'),
        array('controller' => 'home', 'action' => 'logout'),
    );
    private $learnerRules = array(
        array('controller' => 'authoredLabyrinth', 'action' => 'index', 'isFullController' => true),
        array('controller' => 'collectionManager', 'action' => 'editCollection'),
        array('controller' => 'collectionManager', 'action' => 'addCollection'),
        array('controller' => 'labyrinthManager', 'action' => 'index', 'isFullController' => true),
        array('controller' => 'exportImportManager', 'action' => 'index', 'isFullController' => true),
        array('controller' => 'presentationManager', 'action' => 'index', 'isFullController' => true),
        array('controller' => 'remoteServiceManager', 'action' => 'index', 'isFullController' => true),
        array('controller' => 'userManager', 'action' => 'index'),
        array('controller' => 'userManager', 'action' => 'addUser'),
        array('controller' => 'userManager', 'action' => 'saveNewUser'),
        array('controller' => 'userManager', 'action' => 'deleteUser'),
        array('controller' => 'userManager', 'action' => 'addGroup'),
        array('controller' => 'userManager', 'action' => 'saveNewGroup'),
        array('controller' => 'userManager', 'action' => 'editGroup'),
        array('controller' => 'userManager', 'action' => 'deleteGroup'),
        array('controller' => 'userManager', 'action' => 'addMemberToGroup'),
        array('controller' => 'userManager', 'action' => 'updateGroup'),
        array('controller' => 'userManager', 'action' => 'removeMember'),
        array('controller' => 'webinarManager', 'action' => 'add'),
        array('controller' => 'webinarManager', 'action' => 'edit'),
        array('controller' => 'webinarManager', 'action' => 'statistic'),
        array('controller' => 'webinarManager', 'action' => 'delete'),
        array('controller' => 'webinarManager', 'action' => 'save'),
        array('controller' => 'webinarManager', 'action' => 'changeStep'),
        array('controller' => 'dForumManager', 'action' => 'addForum'),
        array('controller' => 'dForumManager', 'action' => 'editForum'),
        array('controller' => 'dForumManager', 'action' => 'updateForum'),
        array('controller' => 'dForumManager', 'action' => 'deleteForum'),
        array('controller' => 'dForumManager', 'action' => 'saveNewForum')
    );
    private $authorRules = array(
        array('controller' => 'presentationManager', 'action' => 'index', 'isFullController' => true),
        array('controller' => 'remoteServiceManager', 'action' => 'index', 'isFullController' => true),
        array('controller' => 'userManager', 'action' => 'index'),
        array('controller' => 'userManager', 'action' => 'addUser'),
        array('controller' => 'userManager', 'action' => 'saveNewUser'),

        array('controller' => 'userManager', 'action' => 'deleteUser'),
        array('controller' => 'userManager', 'action' => 'addGroup'),
        array('controller' => 'userManager', 'action' => 'saveNewGroup'),
        array('controller' => 'userManager', 'action' => 'editGroup'),
        array('controller' => 'userManager', 'action' => 'deleteGroup'),
        array('controller' => 'userManager', 'action' => 'addMemberToGroup'),
        array('controller' => 'userManager', 'action' => 'updateGroup'),
        array('controller' => 'userManager', 'action' => 'removeMember')
    );

    private $reviewerRules = array(
        array('controller' => 'collectionManager', 'action' => 'editCollection'),
        array('controller' => 'collectionManager', 'action' => 'addCollection')
    );

    private $mapActions = array(
        array('controller' => 'labyrinthManager', 'action' => 'global'),
        array('controller' => 'labyrinthManager', 'action' => 'info'),
        array('controller' => 'labyrinthManager', 'action' => 'showDevNotes'),

        array('controller' => 'visualManager', 'action' => 'index'),
        array('controller' => 'nodeManager', 'action' => 'index'),
        array('controller' => 'nodeManager', 'action' => 'grid'),
        array('controller' => 'linkManager', 'action' => 'index'),

        array('controller' => 'nodeManager', 'action' => 'sections'),
        array('controller' => 'chatManager', 'action' => 'index'),
        array('controller' => 'questionManager', 'action' => 'index'),
        array('controller' => 'avatarManager', 'action' => 'index'),
        array('controller' => 'counterManager', 'action' => 'index'),
        array('controller' => 'counterManager', 'action' => 'grid'),
        array('controller' => 'visualdisplaymanager', 'action' => 'index'),
        array('controller' => 'counterManager', 'action' => 'rules'),
        array('controller' => 'elementManager', 'action' => 'index'),
        array('controller' => 'clusterManager', 'action' => 'index'),

        array('controller' => 'feedbackManager', 'action' => 'index'),
        array('controller' => 'skinManager', 'action' => 'index'),
        array('controller' => 'fileManager', 'action' => 'index'),

        array('controller' => 'mapUserManager', 'action' => 'index'),
        array('controller' => 'reportManager', 'action' => 'index')
    );

    private $forumActions = array(
        array('controller' => 'dforumManager', 'action' => 'viewForum'),
        array('controller' => 'dforumManager', 'action' => 'editForum'),
        array('controller' => 'dforumManager', 'action' => 'deleteForum')
    );

    private $topicActions = array(
        array('controller' => 'dtopicManager', 'action' => 'editTopic'),
        array('controller' => 'dtopicManager', 'action' => 'deleteTopic')
    );

    private $webinarsActions = array(
        array('controller' => 'webinarManager', 'action' => 'edit'),
        array('controller' => 'webinarManager', 'action' => 'delete'),
        array('controller' => 'webinarManager', 'action' => 'statistics')
    );

    private $blockedAccess = array(
        array('controller' => 'labyrinthManager', 'action' => 'global'),
        array('controller' => 'visualManager', 'action' => 'index'),
        array('controller' => 'nodeManager', 'action' => 'editNode'),
        array('controller' => 'nodeManager', 'action' => 'grid'),
        array('controller' => 'linkManager', 'action' => 'editLinks'),
        array('controller' => 'nodeManager', 'action' => 'editSection'),
        array('controller' => 'chatManager', 'action' => 'editChat'),
        array('controller' => 'questionManager', 'action' => 'question'),
        array('controller' => 'avatarManager', 'action' => 'editAvatar'),
        array('controller' => 'counterManager', 'action' => 'editCounter'),
        array('controller' => 'counterManager', 'action' => 'grid'),
        array('controller' => 'visualdisplaymanager', 'action' => 'display'),
        array('controller' => 'counterManager', 'action' => 'editCommonRule'),
        array('controller' => 'popupManager', 'action' => 'editPopup'),
        array('controller' => 'elementManager', 'action' => 'editVpd'),
        array('controller' => 'clusterManager', 'action' => 'editCluster'),
        array('controller' => 'feedbackManager', 'action' => 'index'),
        array('controller' => 'fileManager', 'action' => 'editFile'),
        array('controller' => 'fileManager', 'action' => 'imageEditor'),
        array('controller' => 'mapUserManager', 'action' => 'index'),
    );


    public function before()
    {
        parent::before();

        if (Auth::instance()->logged_in())
        {
            if ($this->checkUserRoleRules() OR
                $this->checkAllowedMaps() OR
                $this->checkAllowedForums() OR
                $this->checkAllowedWebinars() OR
                $this->checkAllowedTopics()) Request::initial()->redirect(URL::base());

            $user_type_name             = Auth::instance()->get_user()->type->name;
            $user_id                    = Auth::instance()->get_user()->id;
            $usersHistory               = DB_ORM::model('user')->getUsersHistory($user_id);
            $uri                        = $this->request->detect_uri();
            $historyShowWarningPopup    = 0;
            $readonly                   = NULL;

            foreach ($usersHistory as $value)
            {
                if ((strcmp($value['href'], $uri) == 0) AND ($user_id != $value['id']) AND ($value['readonly'] == 0))
                {
                    $readonly = 1;
                    $historyShowWarningPopup = 1;
                    break;
                }

                if (((boolean) preg_match('#(grid|visualManager)#i', $uri)) AND ((boolean) preg_match('#(grid|visualManager)#i', $value['href'])) AND ($user_id != $value['id']) AND ($value['readonly'] == 0))
                {
                    $readonly = 1;
                    $historyShowWarningPopup = 1;
                    break;
                }
            }

            $this->templateData['user_id']                  = $user_id;
            $this->templateData['userHasBlockedAccess']     = ( ! $this->request->is_ajax()) ? $this->addUserHistory($user_id, $readonly) : 0;
            $this->templateData['historyShowWarningPopup']  = $historyShowWarningPopup;
            $this->templateData['currentUserReadOnly']      = $readonly;
            $this->templateData['historyOfAllUsers']        = json_encode($usersHistory);

            I18n::lang(Auth::instance()->get_user()->language->key);
            $this->templateData['username']                 = Auth::instance()->get_user()->nickname;

            Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Home'))->set_url(URL::base()));

            if ($user_type_name == 'superuser' OR $user_type_name == 'author' OR $user_type_name == 'Director')
            {
                $this->templateData['todayTip'] = DB_ORM::model('todaytip')->getTodayTips();

                /* Fetch the latest authored labyrinths. */
                $maps = ($user_type_name == 'superuser')
                        ? $maps = DB_ORM::model('map')->getAllEnabledMap(7)
                        : $maps = DB_ORM::model('map')->getAllMapsForAuthorAndReviewer($user_id, 7);

                $this->templateData['latestAuthoredLabyrinths'] = $maps;

                $rooNodesMap = array();

                $rootNodeMaps = ($user_type_name == 'superuser')
                                ? DB_ORM::model('map')->getAllEnabledMap()
                                : DB_ORM::model('map')->getAllEnabledAndAuthoredMap($user_id);

                if($rootNodeMaps != NULL AND count($rootNodeMaps) > 0)
                {
                    foreach($rootNodeMaps as $map)
                    {
                        $rooNodesMap[$map->id] = DB_ORM::model('map_node')->getRootNodeByMap($map->id);
                    }
                }

                $this->templateData['rootNodeMap'] = $rooNodesMap;

                /* Fetch the latest played labyrinths. */
                $mapIDs = array();
                $sessions = DB_ORM::model('user_session')->getAllSessionByUser($user_id, 7);
                if (count($sessions) > 0) {
                    foreach ($sessions as $s) {
                        $mapIDs[] = $s->map_id;
                    }
                }

                if (count($mapIDs) > 0)  $this->templateData['latestPlayedLabyrinths'] = DB_ORM::model('map')->getMapsIn($mapIDs);

                $centerView = View::factory('adminMenu');
                $this->templateData['center'] = $centerView;
                $centerView->set('templateData', $this->templateData);
            }
            else
            {
                $maps = DB_ORM::model('map')->getAllEnabledOpenVisibleMap($user_type_name);
                $rooNodesMap = array();

                foreach($maps as $map)
                {
                    $rooNodesMap[$map->id] = DB_ORM::model('map_node')->getRootNodeByMap($map->id);
                }

                $this->templateData['rootNodeMap'] = $rooNodesMap;

                $centerView = View::factory('userMenu');

                if ($user_type_name == 'learner')
                {
                    $centerView->set('openLabyrinths', DB_ORM::model('map')->getAllMapsForLearner($user_id));
                }
                else
                {
                    $centerView->set('openLabyrinths', DB_ORM::model('map')->getAllMapsForRegisteredUser($user_id));
                }

                $centerView->set('presentations', DB_ORM::model('map_presentation')->getPresentationsByUserId($user_id));

                $centerView->set('templateData', $this->templateData);
                $this->templateData['center'] = $centerView;
            }
        }
        else
        {
            if ($this->request->controller() == 'home' && $this->request->action() == 'index') {
                $this->templateData['redirectURL'] = Session::instance()->get('redirectURL');
                $this->templateData['oauthProviders'] = DB_ORM::model('oauthprovider')->getAll();
                Session::instance()->delete('redirectURL');
                $this->templateData['left'] = View::factory('login');
                $this->templateData['left']->set('templateData', $this->templateData);

                $maps = DB_ORM::model('map')->getAllEnabledOpenVisibleMap();
                $rooNodesMap = array();
                if($maps != null && count($maps) > 0) {
                    foreach($maps as $map) {
                        $rooNodesMap[$map->id] = DB_ORM::model('map_node')->getRootNodeByMap($map->id);
                    }
                }

                $this->templateData['rootNodeMap'] = $rooNodesMap;

                $this->templateData['center'] = View::factory('userMenu')
                    ->set('openLabyrinths', DB_ORM::model('map')->getAllEnabledOpenVisibleMap())
                    ->set('templateData', $this->templateData)
                    ->set('openLabyrinths', $maps)
                    ->set('rootNodesMap', $rooNodesMap)
                    ->set('templateData', $this->templateData);
            } else {
                $controller = $this->request->controller();
                $action = $this->request->action();

                $isRedirect = true;
                foreach ($this->unauthorizedRules as $rule) {
                    if ($controller == $rule['controller'] && $action == $rule['action']) {
                        $isRedirect = false;
                    }
                }
                
                if ($isRedirect) {
                    Session::instance()->set('redirectURL', $this->request->uri());
                    Notice::add('Please login first.');
                    Request::initial()->redirect(URL::base());
                }
            }
        }
        $this->templateData['title'] = 'OpenLabyrinth';
        $this->template->set('templateData', $this->templateData);
    }
    
    private function checkUserRoleRules()
    {
        if ( ! Auth::instance()->logged_in()) return false;
        
        $controller = strtolower($this->request->controller());
        $action     = strtolower($this->request->action());
        $userType   = Auth::instance()->get_user()->type->name;

        switch ($userType)
        {
            case 'learner':
                $rules = $this->learnerRules;
                break;
            case 'author':
            case 'Director':
                $rules = $this->authorRules;
                break;
            case 'reviewer':
                $rules = $this->reviewerRules;
                break;
            default:
                return false;
        }

        foreach ($rules as $rule)
        {
            if(isset($rule['isFullController']) && $rule['isFullController'] && strtolower($rule['controller']) == $controller)
            {
                return true;
            }
            else if(strtolower($rule['controller']) == $controller && strtolower($rule['action']) == $action)
            {
                return true;
            }
        }

        return false;
    }
    private function checkAllowedWebinars() {

        $rules = $this->webinarsActions;
        $controller = strtolower($this->request->controller());
        $action = strtolower($this->request->action());
        $webinarId = (int) $this->request->param('id', 0);
        $allowedWebinars = DB_ORM::model('webinar')->getAllowedWebinars(Auth::instance()->get_user()->id);

        foreach($rules as $rule) {
            if(strtolower($rule['controller']) == $controller && strtolower($rule['action']) == $action && !in_array($webinarId,$allowedWebinars) &&
                Auth::instance()->get_user()->type->name != 'superuser' ) {
                return true;
            }
        }

        return false;
    }

    private function checkAllowedMaps()
    {

        $rules      = $this->mapActions;
        $controller = strtolower($this->request->controller());
        $action     = strtolower($this->request->action());
        $mapId      = (int) $this->request->param('id', 0);
        $allowedMap = DB_ORM::model('map')->getAllowedMap(Auth::instance()->get_user()->id);

        if ( Auth::instance()->get_user()->type->name == 'author') {
            $collectionsMaps = DB_ORM::model('map_collectionmap')->getAllColMapsIds();
            $allowedMap = array_merge($allowedMap,$collectionsMaps);
            $allowedMap = array_unique($allowedMap);
        }


        foreach($rules as $rule) {
            if(strtolower($rule['controller']) == $controller && strtolower($rule['action']) == $action && !in_array($mapId,$allowedMap) &&
                Auth::instance()->get_user()->type->name != 'superuser') {
                return true;
            }
        }

        return false;
    }

    private function checkAllowedTopics() {
        $rules = $this->topicActions;
        $controller = strtolower($this->request->controller());
        $action = strtolower($this->request->action());
        $topicId = (int) $this->request->param('id2', 0);

        $allowedTopics = DB_ORM::model('dtopic')->getAllowedTopics(Auth::instance()->get_user()->id);

        foreach($rules as $rule) {
            if(strtolower($rule['controller']) == $controller && strtolower($rule['action']) == $action && !in_array($topicId,$allowedTopics) &&
                Auth::instance()->get_user()->type->name != 'superuser') {
                return true;
            }
        }

        return false;
    }

    private function checkAllowedForums() {

        $openForums = DB_ORM::model('dforum')->getAllOpenForums();
        $privateForums = DB_ORM::model('dforum')->getAllPrivateForums();
        $controller = strtolower($this->request->controller());
        $action = strtolower($this->request->action());

        if (count($openForums) <= 0) $openForums = array();
        if (count($privateForums) <= 0) $privateForums = array();

        $forumId = (int) $this->request->param('id', 0);

        $forums = array_merge($openForums, $privateForums);

        $allowedForums = array();

        if (count($forums) > 0) {
            foreach ($forums as $forum) {
                $allowedForums[] = $forum['id'];
            }
        }

        $rules = $this->forumActions;

        foreach ($rules as $rule) {
            if(strtolower($rule['controller']) == $controller && strtolower($rule['action']) == $action && !in_array($forumId,$allowedForums) &&
                Auth::instance()->get_user()->type->name != 'superuser' ) {
                return true;
            }
        }
        return false;
    }

    private function addUserHistory($user_id, $readonly) {
        $rules = $this->blockedAccess;
        $controller = strtolower($this->request->controller());
        $action = strtolower($this->request->action());

        $uri = NULL;
        foreach($rules as $rule) {
            if(strtolower($rule['controller']) == $controller && strtolower($rule['action']) == $action) {
                $uri = $this->request->detect_uri();
                break;
            }
        }
        $timestamp = time();
        DB_ORM::model('user')->updateUserHistory($user_id, $uri, $readonly, $timestamp);
        return ($uri != NULL) ? 1 : 0;
    }

    public function action_ajaxLogout()
    {
        $userId = $this->request->param('id');
        $userObj = DB_ORM::model('User')->getUserById($userId);
        $userObj->history = 'kick';
        $userObj->history_timestamp = NULL;
        $userObj->save();
        exit;
    }
}

