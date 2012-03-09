<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Main controller for open labyrinth 
 */
class Controller_Main extends Controller_MainDesign {
    /**
     * Main index action 
     */
    public function action_index() {
        //$this->template->content = "for test";
    }
    
    public function action_logout() {
        if(Auth::instance()->logged_in()) {
            Auth::instance()->logout();
        }
        
        Request::initial()->redirect('index.php');
    }
    
    public function action_login() {
        if ($_POST) {
            $user = ORM::factory('user');
            $status = Auth::instance()->login($_POST['username'], $_POST['password']);
            $this->response->body($status);
            if ($status) {
                Request::initial()->redirect('index.php');
            }
        }
    }
}
