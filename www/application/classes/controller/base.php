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
    
    public function before() {
        parent::before();
        
        if(Auth::instance()->logged_in()) {
            $loggedView = View::factory('logged');
            I18n::lang(Auth::instance()->get_user()->language->key);
            $this->templateData['username'] = Auth::instance()->get_user()->nickname;
            
            $loggedView->set('templateData', $this->templateData);
            $this->templateData['left'] = $loggedView;
            
            if(Auth::instance()->get_user()->type->name == 'superuser' or Auth::instance()->get_user()->type->name == 'author') {
                $centerView = View::factory('adminMenu');
                
                $centerView->set('templateData', $this->templateData);
                $this->templateData['center'] = $centerView;
                $this->templateData['right'] = View::factory('document');
            } else {
                $centerView = View::factory('userMenu');
                
                $centerView->set('openLabyrinths', DB_ORM::model('map')->getAllEnabledOpenVisibleMap());
                $centerView->set('presentations', DB_ORM::model('map_presentation')->getPresentationsByUserId(Auth::instance()->get_user()->id));
                
                $centerView->set('templateData', $this->templateData);
                $this->templateData['center'] = $centerView;
            }
        } else {
            $this->templateData['left'] = View::factory('login');
            
            $centerView = View::factory('userMenu');
                
            $centerView->set('openLabyrinths', DB_ORM::model('map')->getAllEnabledOpenVisibleMap());
            
            $centerView->set('templateData', $this->templateData);
            $this->templateData['center'] = $centerView;
        }
        
        $this->templateData['title'] = 'OpenLabyrinth';
        $this->template->set('templateData', $this->templateData);
    }
}

?>
