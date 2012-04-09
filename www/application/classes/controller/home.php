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
    
    public function action_changePassword() {
        if(Auth::instance()->logged_in()) {
            $this->templateData['user'] = Auth::instance()->get_user();
            
            $view = View::factory('changePassword');
            $view->set('templateData', $this->templateData);
            
            $this->templateData['center'] = $view;
            unset($this->templateData['left']);
            unset($this->templateData['right']);
            $this->template->set('templateData', $this->templateData);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }
    
    public function action_updatePassword() {
        if($_POST and Auth::instance()->logged_in()) {
            $newPassword = Arr::get($_POST, 'newpswd', NULL);
            $confirmPassword = Arr::get($_POST, 'pswd_confirm', NULL);
            $currentPassword = Arr::get($_POST, 'upw', NULL);
            
            if($currentPassword != NULL and 
                    $newPassword != NULL and 
                    $confirmPassword != NULL and 
                    $newPassword == $confirmPassword and 
                    Auth::instance()->hash($currentPassword) == Auth::instance()->get_user()->password) {   
                $user = DB_ORM::model('user', array((int)Auth::instance()->get_user()->id));
                $user->password = Auth::instance()->hash($newPassword);
                $user->save();
                
                Request::initial()->redirect(URL::base());
            }
        } else {
            Request::initial()->redirect(URL::base());
        }
    }
	
	public function action_search() {
		if($_POST) {
			$scope = Arr::get($_POST, 'scope', NULL);
			$key = Arr::get($_POST, 'searchterm', NULL);
			$title = TRUE;
			if($scope == 'a') {
				$title = FALSE;
			}
			
			if($key != NULL) {
				$maps = DB_ORM::model('map')->getSearchMap($key, $title);
				
				$view = View::factory('search');
				$view->set('maps', $maps);
				$view->set('term', $key);
				
				$this->templateData['center'] = $view;
				unset($this->templateData['right']);
				$this->template->set('templateData', $this->templateData);
			}
		} else {
			Request::initial()->redirect(URL::base());
		}
	}
}
?>