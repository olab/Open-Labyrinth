<?php defined('SYSPATH') or die('No direct script access.');

class Controller_FileManager extends Controller_Base {
    
    public function action_index() {
        $mapId = $this->request->param('id', NULL);
        if($mapId != NULL) {
            $this->templateData['map'] = DB_ORM::model('map', array((int)$mapId));
            
            $fileInfo = DB_ORM::model('map_element')->getFilesSize();
            
            $this->templateData['files_size'] = DB_ORM::model('map_element')->sizeFormat($fileInfo['size']);
            $this->templateData['files_count'] = $fileInfo['count'];
            
            $this->templateData['files'] = DB_ORM::model('map_element')->getAllFilesByMap((int)$mapId);
            
            $fileView = View::factory('labyrinth/file/view');
            $fileView->set('templateData', $this->templateData);
        
            $leftView = View::factory('labyrinth/labyrinthEditorMenu');
            $leftView->set('templateData', $this->templateData);
            
            $this->templateData['left'] = $leftView;
            $this->templateData['center'] = $fileView;
            unset($this->templateData['right']);
            $this->template->set('templateData', $this->templateData);
        } else {
             Request::initial()->redirect(URL::base());
        }
    }
    
    public function action_uploadFile() {
        $mapId = $this->request->param('id', NULL);
        if($_FILES and $mapId != NULL) {
            DB_ORM::model('map_element')->uploadFile($mapId, $_FILES);
            Request::initial()->redirect(URL::base().'fileManager/index/'.$mapId);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }
    
    public function action_deleteFile() {
        $mapId = $this->request->param('id', NULL);
        $fileId = $this->request->param('id2', NULL);
        if($mapId != NULL and $fileId != NULL) {
            DB_ORM::model('map_element')->deleteFile($fileId);
            Request::initial()->redirect(URL::base().'fileManager/index/'.$mapId);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }
    
    public function action_editFile() {
        $mapId = $this->request->param('id', NULL);
        $fileId = $this->request->param('id2', NULL);
        if($mapId != NULL and $fileId != NULL) {
            $this->templateData['map'] = DB_ORM::model('map', array((int)$mapId));
            $this->templateData['file'] = DB_ORM::model('map_element', array((int)$fileId));
            
            $fileView = View::factory('labyrinth/file/edit');
            $fileView->set('templateData', $this->templateData);
        
            $leftView = View::factory('labyrinth/labyrinthEditorMenu');
            $leftView->set('templateData', $this->templateData);
            
            $this->templateData['left'] = $leftView;
            $this->templateData['center'] = $fileView;
            unset($this->templateData['right']);
            $this->template->set('templateData', $this->templateData);
        } else {
             Request::initial()->redirect(URL::base());
        }
    }
    
    public function action_updateFile() {
        $mapId = $this->request->param('id', NULL);
        $fileId = $this->request->param('id2', NULL);
        if($_POST and $mapId != NULL and $fileId != NULL) {
            DB_ORM::model('map_element')->updateFile($fileId, $_POST);
            Request::initial()->redirect(URL::base().'fileManager/index/'.$mapId);
        } else {
             Request::initial()->redirect(URL::base());
        }
    }
}
    
?>
