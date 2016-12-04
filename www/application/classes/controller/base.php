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

    /**
     * @var View page template
     */
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
        array('controller' => 'H5P', 'action' => 'embed'),
        array('controller' => 'H5P', 'action' => 'saveResult'),
        array('controller' => 'H5P', 'action' => 'saveXAPIStatement'),
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

    private $exceptionsTopMenu = array(
        'webinarmanager/allConditions',
        'webinarmanager/saveConditions',
        'webinarmanager/editCondition',
        'webinarmanager/editConditionSave',
        'webinarmanager/index',
        'webinarmanager/edit',
        'webinarmanager/reset',
        'webinarmanager/resetCondition',
        'webinarmanager/save',
        'webinarmanager/mapsGrid',
        'webinarmanager/saveMapsGrid',
        'webinarmanager/play',
        'webinarmanager/progress',
        'webinarmanager/my',
        'webinarmanager/render',
        'webinarmanager/mapReport4R',
        'webinarmanager/mapReportSCT',
        'webinarmanager/mapReportPoll',
        'webinarmanager/mapReportSJT',
        'webinarmanager/stepReport4R',
        'webinarmanager/stepReportSCT',
        'webinarmanager/stepReportPoll',
        'webinarmanager/stepReportSJT',
        'webinarmanager/changeStep',
        'webinarmanager/publishStep',
        'usermanager/editUser',
        'usermanager/saveOldUser',
        'usermanager/index',
        'usermanager/addGroup',
        'usermanager/editGroup',
        'usermanager/removeMember',
        'usermanager/deleteGroup',
        'home/login',
        'home/logout',
        'reportmanager/finishAndShowReport',
        'countermanager/checkCommonRule',
        'questionmanager/questionPOST',
        'questionmanager/deleteQuestion',
        'questionmanager/duplicateQuestion',
        'questionmanager/exportQuestion',
        'avatarmanager/addAvatar',
        'avatarmanager/updateAvatar',
        'countermanager/addCounter',
        'popupmanager/deletePopup',
        'skinmanager/saveSelectedSkin',
        'skinmanager/uploadNewSkin',
        'skinmanager/exportSkins',
        'skinmanager/deleteSkin',
        'countermanager/deleteCommonRule',
        'systemmanager/index',
        'systemmanager/saveTwitterCredits',
        'systemmanager/updatePasswordResetSettings',
        'systemmanager/updateMediaUploadCopyright',
        'systemmanager/updateSupportEmails',
        'systemmanager/saveOAuth',
        'systemmanager/uploadReadMe',
        'collectionmanager/index',
        'collectionmanager/editCollection',
        'collectionmanager/viewAll',
        'labyrinthmanager/caseWizard',
        'labyrinthmanager/addManual',
        'labyrinthmanager/addNewMap',
        'exportimportmanager/import',
        'exportimportmanager/upload',
        'exportimportmanager/exportMVP',
        'exportimportmanager/exportMVPMap',
        'exportimportmanager/exportAdvanced',
        'exportimportmanager/exportAdvancedMap',
        'remoteservicemanager/index',
        'todaytipmanager/index',
        'todaytipmanager/editTip',
        'todaytipmanager/archive',
        'todaytipmanager/saveTip',
        'todaytipmanager/deleteTip',
        'todaytipmanager/addTip',
        'metadata/manager',
        'vocabulary/manager',
        'vocabulary/mappings',
        'dforummanager/index',
        'dforummanager/addForum',
        'dforummanager/editForum',
        'dforummanager/deleteForum',
        'home/about',
        'home/userGuide',
        'base/ui',
        'ltimanager/index',
        'ltimanager/userView',
        'ltimanager/saveConsumer',
        'ltimanager/deleteUser',
        'ltimanager/info',
    );

    private $exceptionLeftMenu = array(
        'labyrinthmanager/global',
        'labyrinthmanager/conditionsGrid',
        'labyrinthmanager/saveGlobal',
        'labyrinthmanager/disableMap',
        'labyrinthmanager/info',
        'labyrinthmanager/editMap',
        'labyrinthmanager/deleteMap',
        'labyrinthmanager/editKeys',
        'labyrinthmanager/saveKeys',
        'labyrinthmanager/deleteKey',
        'visualmanager/index',
        'visualmanager/updateJSON',
        'nodemanager/index',
        'nodemanager/editNode',
        'nodemanager/setRootNode',
        'nodemanager/deleteNode',
        'nodemanager/updateNode',
        'nodemanager/grid',
        'nodemanager/saveGrid',
        'nodemanager/sections',
        'nodemanager/editSection',
        'nodemanager/deleteNodeSection',
        'nodemanager/addNodeSection',
        'nodemanager/updateSection',
        'linkmanager/index',
        'linkmanager/editLinks',
        'linkmanager/editLink',
        'linkmanager/deleteLink',
        'linkmanager/updateOrder',
        'linkmanager/updateLinkType',
        'linkmanager/updateLinkStyle',
        'linkmanager/addLink',
        'questionmanager/index',
        'questionmanager/question',
        'avatarmanager/index',
        'avatarmanager/editAvatar',
        'avatarmanager/deleteAvatar',
        'avatarmanager/exportAvatar',
        'countermanager/index',
        'countermanager/addCounter',
        'countermanager/index',
        'countermanager/editCounter',
        'countermanager/addRule',
        'countermanager/rules',
        'countermanager/updateCounter',
        'countermanager/grid',
        'countermanager/updateGrid',
        'countermanager/addCommonRule',
        'countermanager/updateCommonRule',
        'countermanager/editCommonRule',
        'visualdisplaymanager/index',
        'visualdisplaymanager/display',
        'visualdisplaymanager/save',
        'visualdisplaymanager/deleteDisplay',
        'popupmanager/index',
        'popupmanager/newPopup',
        'popupmanager/savePopup',
        'popupmanager/editPopup',
        'elementmanager/index',
        'elementmanager/addNewElement',
        'skinmanager/index',
        'skinmanager/createSkin',
        'skinmanager/editSkins',
        'skinmanager/listSkins',
        'skinmanager/uploadSkin',
        'filemanager/index',
        'filemanager/editFile',
        'filemanager/deleteFile',
        'filemanager/imageEditor',
        'filemanager/delCheked',
        'mapusermanager/index',
        'mapusermanager/addUser',
        'reportmanager/index',
        'reportmanager/pathVisualisation',
        'reportmanager/summaryReport',
        'reportmanager/showReport',
    );

    public function before()
    {
        parent::before();
        Lti_DataConnector::getLtiPost();

        $post = $_POST;
        unset($post['Submit']);

        if ($post OR $_GET OR $this->request->is_ajax()) {
            return;
        }

        if (Auth::instance()->logged_in()) {
            
            if (Session::instance()->get_once('user_was_updated', false)) {
                Auth::instance()->refresh();
            }
            
            $uri                        = $this->request->detect_uri();
            $controllerAction           = strtolower($this->request->controller()).'/'.$this->request->action();
            $topMenu                    = in_array($controllerAction, $this->exceptionsTopMenu);
            $leftMenu                   = in_array($controllerAction, $this->exceptionLeftMenu);
            $user                       = Auth::instance()->get_user();
            $user_type_name             = $user->type->name;
            $user_id                    = $user->id;
            $usersHistory               = $topMenu ? array() : DB_ORM::model('user')->getUsersHistory($user_id);
            $historyShowWarningPopup    = 0;
            $readonly                   = NULL;

            // ----- check access ----- //
            $argsForAccess              = array(
                'action'     => strtolower($this->request->action()),
                'controller' => strtolower($this->request->controller()),
                'id'         => (int) $this->request->param('id', 0),
                'id2'        => (int) $this->request->param('id2', 0),
            );

            if ($user->can('access', $argsForAccess)) {
                Request::initial()->redirect(URL::base());
            }
            // ----- end check access ----- //

            if ($topMenu) return;

            foreach ($usersHistory as $value) {
                $splitHref = explode('/', $value['href']);
                $isSameLabyrinth = ($splitHref[count($splitHref) - 1] == $this->request->param('id', 0));

                if ($isSameLabyrinth AND
                    ((strcmp($value['href'], $uri) == 0) AND ($user_id != $value['id']) AND ($value['readonly'] == 0) OR
                    ((boolean) preg_match('#(grid|visualManager)#i', $uri)) AND ((boolean) preg_match('#(grid|visualManager)#i', $value['href'])) AND ($user_id != $value['id']) AND ($value['readonly'] == 0))) {
                    $readonly = 1;
                    $historyShowWarningPopup = 1;
                    break;
                }
            }

            $this->templateData['historyOfAllUsers']        = json_encode($usersHistory);
            $this->templateData['user_id']                  = $user_id;
            $this->templateData['userHasBlockedAccess']     = $this->addUserHistory($user_id, $readonly);
            $this->templateData['historyShowWarningPopup']  = $historyShowWarningPopup;
            $this->templateData['currentUserReadOnly']      = $readonly;
            $this->templateData['username']                 = Auth::instance()->get_user()->nickname;

            I18n::lang(Auth::instance()->get_user()->language->key);
            Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Home'))->set_url(URL::base()));

            if ($leftMenu) return;

            if ($user_type_name == 'superuser' OR $user_type_name == 'author' OR $user_type_name == 'Director') {
                $this->templateData['todayTip'] = DB_ORM::model('todaytip')->getTodayTips();

                $rootNodeMaps = ($user_type_name == 'superuser')
                                ? DB_ORM::model('map')->getAllEnabledMap()
                                : DB_ORM::model('map')->getAllEnabledAndAuthoredMap($user_id);
                $this->templateData['latestAuthoredLabyrinths'] = array_slice($rootNodeMaps, 0 ,7);

                $rooNodesMap = array();
                foreach($rootNodeMaps as $map) {
                    $rooNodesMap[$map->id] = DB_ORM::model('map_node')->getRootNodeByMap($map->id);
                }

                $this->templateData['rootNodeMap'] = $rooNodesMap;

                /* Fetch the latest played labyrinths. */
                $mapIDs = array();
                $sessions = DB_ORM::model('user_session')->getAllSessionByUser($user_id, 7);

                foreach ($sessions as $s) {
                    $mapIDs[] = $s->map_id;
                }

                if (count($mapIDs)) {
                    $this->templateData['latestPlayedLabyrinths'] = DB_ORM::model('map')->getMapsIn($mapIDs);
                }

                $this->templateData['center'] = View::factory('adminMenu')->set('templateData', $this->templateData);
            } else {
                $maps = DB_ORM::model('map')->getAllEnabledOpenVisibleMap($user_type_name);
                $rooNodesMap = array();

                foreach ($maps as $map) {
                    $rooNodesMap[$map->id] = DB_ORM::model('map_node')->getRootNodeByMap($map->id);
                }

                $this->templateData['rootNodeMap'] = $rooNodesMap;

                $centerView = View::factory('userMenu');

                if ($user_type_name == 'learner') {
                    $maps = DB_ORM::model('map')->getAllMapsForLearner($user_id);
                    foreach ($maps as $map) {
                        $bookmark = DB_ORM::model('User_Bookmark')->getBookmarkByMapAndUser($map->id, Auth::instance()->get_user()->id);
                        if ($bookmark) $this->templateData['bookmarks'][$map->id] = 1;
                    }
                    $centerView->set('openLabyrinths', $maps);
                }
                else {
                    $centerView->set('openLabyrinths', DB_ORM::model('map')->getAllMapsForRegisteredUser($user_id));
                }

                $this->templateData['center'] = $centerView->set('templateData', $this->templateData);;
            }
        } else {
            if ($this->request->controller() == 'home' AND $this->request->action() == 'index') {
                $this->templateData['redirectURL'] = Session::instance()->get('redirectURL');
                $this->templateData['oauthProviders'] = DB_ORM::model('oauthprovider')->getAll();
                Session::instance()->delete('redirectURL');
                $this->templateData['left'] = View::factory('login')->set('templateData', $this->templateData);

                $maps = DB_ORM::model('map')->getAllEnabledOpenVisibleMap();
                $rooNodesMap = array();
                if($maps != null AND count($maps) > 0)
                {
                    foreach($maps as $map)
                    {
                        $rooNodesMap[$map->id] = DB_ORM::model('map_node')->getRootNodeByMap($map->id);
                    }
                }

                $this->templateData['rootNodeMap'] = $rooNodesMap;

                $this->templateData['center'] = View::factory('userMenu')
                    ->set('templateData', $this->templateData)
                    ->set('openLabyrinths', $maps)
                    ->set('rootNodesMap', $rooNodesMap)
                    ->set('templateData', $this->templateData);
            } else {
                $controller = $this->request->controller();
                $action = $this->request->action();

                $isRedirect = true;
                foreach ($this->unauthorizedRules as $rule) {
                    if (strtolower($controller) === strtolower($rule['controller']) && strtolower($action) === strtolower($rule['action'])) {
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

    private function addUserHistory ($user_id, $readonly)
    {
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
        $userObj = DB_ORM::model('User')->getUserById($this->request->param('id'));
        $userObj->history = 'kick';
        $userObj->history_timestamp = NULL;
        $userObj->save();
        exit;
    }

    public function action_ui()
    {
        $mode = $this->request->param('id');
        $userId = $this->request->param('id2');
        $user = $userId
            ? DB_ORM::model('User', array($userId))
            : Auth::instance()->get_user();
        $user->changeUI($mode);
        Request::initial()->redirect($this->request->referrer());
    }

    protected function jsonResponse($data)
    {
        die(json_encode($data));
    }

}