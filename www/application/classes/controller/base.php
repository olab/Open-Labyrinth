<?php defined('SYSPATH') or die('No direct script access.');

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
            }
        } else {
            $this->templateData['left'] = View::factory('login');
        }
        
        $this->templateData['title'] = 'OpenLabyrinth';
        $this->template->set('templateData', $this->templateData);
    }
}

?>
