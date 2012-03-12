<?php defined('SYSPATH') or die('No direct script access.');

class Controller_UserManager extends Controller_Base {

    public function action_index() {
        $view = View::factory('usermanager/view');
        $this->templateData['users'] = ORM::factory('user')->find_all();
        $view->set('templateData', $this->templateData);
        
        $this->templateData['center'] = $view;
        $this->template->set('templateData', $this->templateData);
    }
}