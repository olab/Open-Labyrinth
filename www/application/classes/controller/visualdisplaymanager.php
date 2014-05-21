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

class Controller_VisualDisplayManager extends Controller_Base {

    private $fonts = array(
        'Open Sans',
        'Andale Mono',
        'Arial',
        'Arial Black',
        'Book Antiqua',
        'Comic Sans MS',
        'Courier New',
        'Georgia',
        'Helvetica',
        'Impact',
        'Symbol',
        'Tahoma',
        'Terminal',
        'Times New Roman',
        'Trebuchet MS',
        'Verdana',
        'Webdings'
    );

    public function before() {
        $this->templateData['labyrinthSearch'] = 1;

        parent::before();
    }

    public function action_index() {
        $mapId = $this->request->param('id', null);
        
        if($mapId != null) {
            $this->templateData['map'] = DB_ORM::model('map', array((int)$mapId));
            $this->templateData['displays'] = DB_ORM::model('map_visualdisplay')->getMapDisplays($mapId);

            Breadcrumbs::add(Breadcrumb::factory()->set_title($this->templateData['map']->name)->set_url(URL::base() . 'labyrinthManager/global/' . $mapId));
            Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Visual Display')));

            $view = View::factory('labyrinth/display/view');
            $view->set('templateData', $this->templateData);

            $leftView = View::factory('labyrinth/labyrinthEditorMenu');
            $leftView->set('templateData', $this->templateData);

            $this->templateData['center'] = $view;
            $this->templateData['left'] = $leftView;
            unset($this->templateData['right']);
            $this->template->set('templateData', $this->templateData);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }
    
    public function action_display()
    {
        $mapId      = $this->request->param('id', false);
        $displayId  = $this->request->param('id2', null);
        
        if ( ! $mapId) Request::initial()->redirect(URL::base());

        $this->getAllFonts();

        $this->templateData['fonts']         = $this->fonts;
        $this->templateData['map']           = DB_ORM::model('map', array($mapId));
        $this->templateData['counters']      = DB_ORM::model('map_counter')->getCountersByMap($mapId);
        $this->templateData['displayImages'] = $this->getVisualDisplayImages($mapId);

        if ($displayId)
        {
            $this->templateData['display']     = DB_ORM::model('map_visualdisplay', array($displayId));
            $this->templateData['displayJSON'] = DB_ORM::model('map_visualdisplay')->toJSON($displayId);
        }

        $this->templateData['center'] = View::factory('labyrinth/display/display')->set('templateData', $this->templateData);
        $this->templateData['left']   = View::factory('labyrinth/labyrinthEditorMenu')->set('templateData', $this->templateData);
        $this->template->set('templateData', $this->templateData);
    }

    private function getAllFonts ()
    {
        $fileContent     = file('css/font.css');
        $patterns[0]     = '/@import url\(http:\/\/fonts\.googleapis\.com\/css\?family=/';
        $patterns[1]     = '/\+/';
        $patterns[2]     = '/\);/';
        $replacements[0] = '';
        $replacements[1] = ' ';
        $replacements[2] = '';

        foreach ($fileContent as $font) $this->fonts[] = trim(preg_replace($patterns, $replacements, $font));

        sort($this->fonts);
    }

    public function action_ajaxAddFont ()
    {
        $newFontName = $this->request->param('id');

        $this->getAllFonts();

        if (in_array($newFontName, $this->fonts)) exit;

        $newFontName = str_replace(' ', '+', $newFontName);
        $newFontName = '@import url(http://fonts.googleapis.com/css?family='.$newFontName.');';

        $file = 'css/font.css';
        $current = file_get_contents($file);
        $current .= $newFontName."\n";
        file_put_contents($file, $current);
        exit;
    }

    public function action_ajaxDeleteFont ()
    {
        $fontName = $this->request->param('id');
        $fontName = str_replace(' ', '+', $fontName);
        $fontName = '@import url(http://fonts.googleapis.com/css?family='.$fontName.');'."\n";

        $fileName    = 'css/font.css';
        $fileContent = file_get_contents($fileName);
        $fileContent = str_replace($fontName, '', $fileContent);
        file_put_contents($fileName, $fileContent);
        exit;
    }
    
    public function action_save()
    {
        $post = $this->request->post();
        $this->auto_render = false;
        
        $mapId          = Arr::get($post, 'mapId', null);
        $json           = Arr::get($post, 'data', null);
        $showOnAllPages = Arr::get($post, 'allPages', 'false') == 'true' ? 1 : 0;

        $status = 'fail';

        if($mapId != null)
        {
            $status = DB_ORM::model('map_visualdisplay')->updateFromJSON($mapId, $json);
            DB_ORM::model('map_visualdisplay')->updateShowOnAllPages($status, $showOnAllPages);
        }
        
        echo $status;
    }
    
    public function action_replaceDisplayFile() {
        $this->auto_render = false;
        
        $mapId = Arr::get($_POST, 'mapId', null);
        $fileName = Arr::get($_POST, 'fileName', null);

        $result = '';
        if($mapId != null && $fileName != null) {
            $dir = DOCROOT . '/files/' . $mapId . '/vdImages';
            if(!is_dir($dir)) {
                mkdir(DOCROOT . '/files/' . $mapId . '/vdImages');
                
            }
            if(!is_dir($dir . '/thumbs')) {
                mkdir(DOCROOT . '/files/' . $mapId . '/vdImages/thumbs');
            }
            
            $dest = DOCROOT . '/files/' . $mapId . '/vdImages/' . $fileName;
            $dest2 = DOCROOT . '/files/' . $mapId . '/vdImages/thumbs/' . $fileName;
            $src  = DOCROOT . '/scripts/fileupload/php/files/' . $fileName;
            $src2 = DOCROOT . '/scripts/fileupload/php/thumbnails/' . $fileName;

            copy($src, $dest);
            copy($src2, $dest2);
            unlink($src);
            unlink($src2);
            
            $result = $fileName;
        }
        
        echo $result;
    }
    
    public function action_deleteImage() {
        $mapId = $this->request->param('id', null);
        $displayId = $this->request->param('id2', null);
        $imageName = Arr::get($_POST, 'imageName', null);
        
        if($imageName != null) {
            DB_ORM::model('map_visualdisplay')->deleteImage($displayId, $mapId, $imageName);
        }
        
        Request::initial()->redirect(URL::base().'visualdisplaymanager/display/'.$mapId.'/'.$displayId.'#imagesTab');
    }
    
    public function action_deleteDisplay() {
        $mapId = $this->request->param('id', null);
        $displayId = $this->request->param('id2', null);
        
        if($displayId != null) {
            DB_ORM::delete('map_visualdisplay')
                    ->where('id', '=', $displayId)
                    ->execute();
        }
        
        Request::initial()->redirect(URL::base(). 'visualdisplaymanager/index/' . $mapId);
    }
    
    private function getVisualDisplayImages($mapId) {
        // define all directory
        $dir_main   = DOCROOT.'/files/'.$mapId;
        $dir        = $dir_main.'/vdImages';
        $dir_sub    = $dir.'/thumbs';

        // define directory doesn't exsist, create it
        if (! is_dir($dir_main)) mkdir($dir_main);
        if (! is_dir($dir))      mkdir($dir);
        if (! is_dir($dir_sub))  mkdir($dir_sub);

        $images = array();
        $handle = opendir($dir);

        while(($file = readdir($handle)) !== false) { 
            if($file == '.' || $file == '..') { 
                continue; 
            } 
            
            $filepath = $dir == '.' ? $file : $dir . '/' . $file; 
            if(is_link($filepath)) { 
                continue; 
            }
            
            if(is_file($filepath)) {
                $images[] = $file; 
            }
        } 
        
        closedir($handle); 
        
        return $images;
    }
}