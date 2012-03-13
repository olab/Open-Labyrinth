<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Base extends Controller_Template {

    public $template = 'home';
    protected $templateData = array();

    public function before() {
        parent::before();
        
        if (Auth::instance()->logged_in()) {
            $view = View::factory('logged');
            
            $lang = ORM::factory('user', Auth::instance()->get_user()->id)->language->name;
            I18n::lang($lang);
            $this->templateData['username'] = Auth::instance()->get_user()->nickname;
            
            $view->set('templateData', $this->templateData);
            $this->templateData['left'] = $view;
            
            if(Auth::instance()->get_user()->type->name == 'superuser' or Auth::instance()->get_user()->type->name == 'author') {
                $centerView = View::factory('adminMenu');
                
                $centerView->set('templateData', $this->templateData);
                $this->templateData['center'] = $centerView;
            }
        } else {
            $this->templateData['left'] = View::factory('login');
        }
        
        $this->templateData['title'] = 'OpenLabyrinth';
        $this->template->set('templateData', $this->templateData);
    }
}

?>
