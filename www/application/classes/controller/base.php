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
        array('controller' => 'collectionManager', 'action' => 'index', 'isFullController' => true),
        array('controller' => 'labyrinthManager', 'action' => 'index', 'isFullController' => true),
        array('controller' => 'exportImportManager', 'action' => 'index', 'isFullController' => true),
        array('controller' => 'presentationManager', 'action' => 'index', 'isFullController' => true),
        array('controller' => 'remoteServiceManager', 'action' => 'index', 'isFullController' => true),
        array('controller' => 'userManager', 'action' => 'index'),
        array('controller' => 'userManager', 'action' => 'addUser'),
        array('controller' => 'userManager', 'action' => 'saveNewUser'),
        array('controller' => 'userManager', 'action' => 'editUser'),
        array('controller' => 'userManager', 'action' => 'saveOldUser'),
        array('controller' => 'userManager', 'action' => 'deleteUser'),
        array('controller' => 'userManager', 'action' => 'addGroup'),
        array('controller' => 'userManager', 'action' => 'saveNewGroup'),
        array('controller' => 'userManager', 'action' => 'editGroup'),
        array('controller' => 'userManager', 'action' => 'deleteGroup'),
        array('controller' => 'userManager', 'action' => 'addMemberToGroup'),
        array('controller' => 'userManager', 'action' => 'updateGroup'),
        array('controller' => 'userManager', 'action' => 'removeMember')
    );
    private $authorRules = array(
        array('controller' => 'collectionManager', 'action' => 'index', 'isFullController' => true),
        array('controller' => 'presentationManager', 'action' => 'index', 'isFullController' => true),
        array('controller' => 'remoteServiceManager', 'action' => 'index', 'isFullController' => true),
        array('controller' => 'userManager', 'action' => 'index'),
        array('controller' => 'userManager', 'action' => 'addUser'),
        array('controller' => 'userManager', 'action' => 'saveNewUser'),
        array('controller' => 'userManager', 'action' => 'editUser'),
        array('controller' => 'userManager', 'action' => 'saveOldUser'),
        array('controller' => 'userManager', 'action' => 'deleteUser'),
        array('controller' => 'userManager', 'action' => 'addGroup'),
        array('controller' => 'userManager', 'action' => 'saveNewGroup'),
        array('controller' => 'userManager', 'action' => 'editGroup'),
        array('controller' => 'userManager', 'action' => 'deleteGroup'),
        array('controller' => 'userManager', 'action' => 'addMemberToGroup'),
        array('controller' => 'userManager', 'action' => 'updateGroup'),
        array('controller' => 'userManager', 'action' => 'removeMember')
    );

    public function before() {
        parent::before();

        if (Auth::instance()->logged_in()) {
            if($this->checkUserRoleRules()) {
                Request::initial()->redirect(URL::base());   
            }
            
            I18n::lang(Auth::instance()->get_user()->language->key);
            $this->templateData['username'] = Auth::instance()->get_user()->nickname;

            Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Home'))->set_url(URL::base()));

            if (Auth::instance()->get_user()->type->name == 'superuser' or Auth::instance()->get_user()->type->name == 'author') {
                $centerView = View::factory('adminMenu');
                $this->templateData['todayTip'] = Kohana::$config->load('today_tip');
                /*
                 * Fetch the latest authored labyrinths.
                 */
                if (Auth::instance()->get_user()->type->name == 'superuser') {
                    $maps = DB_ORM::model('map')->getAllEnabledMap(7);
                } else {
                    $maps = DB_ORM::model('map')->getAllEnabledAndAuthoredMap(Auth::instance()->get_user()->id, 7);
                }

                $this->templateData['latestAuthoredLabyrinths'] = $maps;

                /*
                 * Fetch the latest played labyrinths.
                 */
                $mapIDs = array();
                $sessions = DB_ORM::model('user_session')->getAllSessionByUser(Auth::instance()->get_user()->id, 7);
                if (count($sessions) > 0) {
                    foreach ($sessions as $s) {
                        $mapIDs[] = $s->map_id;
                    }
                }

                if (count($mapIDs) > 0) {
                    $this->templateData['latestPlayedLabyrinths'] = DB_ORM::model('map')->getMapsIn($mapIDs);
                }

                $centerView->set('templateData', $this->templateData);
                $this->templateData['center'] = $centerView;
            } else {
                $centerView = View::factory('userMenu');
                $centerView->set('openLabyrinths', DB_ORM::model('map')->getAllMapsForRegisteredUser(Auth::instance()->get_user()->id));
                $centerView->set('presentations', DB_ORM::model('map_presentation')->getPresentationsByUserId(Auth::instance()->get_user()->id));

                $centerView->set('templateData', $this->templateData);
                $this->templateData['center'] = $centerView;
            }
        } else {
            if ($this->request->controller() == 'home' && $this->request->action() == 'index') {
                $this->templateData['redirectURL'] = Session::instance()->get('redirectURL');
                $this->templateData['oauthProviders'] = DB_ORM::model('oauthprovider')->getAll();
                Session::instance()->delete('redirectURL');
                $this->templateData['left'] = View::factory('login');
                $this->templateData['left']->set('templateData', $this->templateData);
                
                $centerView = View::factory('userMenu');
                $centerView->set('openLabyrinths', DB_ORM::model('map')->getAllEnabledOpenVisibleMap());
                $centerView->set('templateData', $this->templateData);
                
                $this->templateData['center'] = $centerView;
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
    
    private function checkUserRoleRules() {
        if(!Auth::instance()->logged_in()) return false;
        
        $controller = strtolower($this->request->controller());
        $action = strtolower($this->request->action());
        
        $rules = array();
        if(Auth::instance()->get_user()->type->name == 'learner') {
            $rules = $this->learnerRules;
        } else if(Auth::instance()->get_user()->type->name == 'author') {
            $rules = $this->authorRules;
        }

        foreach($rules as $rule) {
            if(isset($rule['isFullController']) && $rule['isFullController'] && strtolower($rule['controller']) == $controller) {
                return true;
            } else if(strtolower($rule['controller']) == $controller && strtolower($rule['action']) == $action) {
                return true;
            }
        }

        return false;
    }

}

