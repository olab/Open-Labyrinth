<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Base design class for application 
 */
class Controller_MainDesign extends Controller_Template {

    public $template = 'mainTemplate'; // Main view template name
    
    protected $languageTranslate = array();

    public function before() {
        parent::before();
        
        $this->template->title = 'OpenLabyrinth';
        $this->template->left = '';
        
        if (Auth::instance()->logged_in()) {
            $this->languageTranslate = $this->GenerateLanguageArray(Auth::instance()->get_user()->language_id);
            
            $this->template->langTrans = $this->languageTranslate;
            $this->template->left = View::factory('loginned');
            
            if(Auth::instance()->logged_in('admin') || Auth::instance()->logged_in('author')) {
                $this->template->center = View::factory('labyrinthManager');
            }
            
        } else {
            $this->template->left = View::factory('loginUser');
        }
    }
    
    private function GenerateLanguageArray($id) {
        $languageTranslate = ORM::factory('languageTranslate')->where('langid', '=', $id)->find_all();
        
        $result = array();
        foreach($languageTranslate as $value) {
            $result[$value->key] = $value->translate;
        }
        
        return $result;
    }
}