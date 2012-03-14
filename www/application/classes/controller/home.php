<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Home extends Controller_Base {
    
    public function action_index() {
        
    }
    
    public function action_login() {
        if ($_POST) {
            $status = Auth::instance()->login($_POST['username'], $_POST['password']);
            if ($status) {
                Request::initial()->redirect(URL::base());
            } else {
                $this->templateData['error'] = 'Invalid username or password';
                $this->template->set('templateData', $this->templateData);
            }
        }
    }
    
    public function action_logout() {
        if (Auth::instance()->logged_in()) {
            Auth::instance()->logout();
        }

        Request::initial()->redirect(URL::base());
    }
}

?>
