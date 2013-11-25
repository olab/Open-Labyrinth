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

class Controller_FileManager extends Controller_Base {

    private $metadataJPEGFields = array(array('key' => 'Artist', 'title' => 'Artist'),
                                        array('key' => 'Company', 'title' => 'Company'),
                                        array('key' => 'Make', 'title' => 'Make'),
                                        array('key' => 'Model', 'title' => 'Model'),
                                        array('key' => 'DateTimeOriginal', 'title' => 'Original date time'),
                                        array('key' => 'DateTimeDigitized', 'title' => 'Digitized date time'),
                                        array('key' => 'Software', 'title' => 'Software'),
                                        array('key' => 'DateTime', 'title' => 'Date time'));

    public function before() {
        $this->templateData['labyrinthSearch'] = 1;

        parent::before();

        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('My Labyrinths'))->set_url(URL::base() . 'authoredLabyrinth'));
    }

    public function action_index() {
        $mapId = $this->request->param('id', NULL);
        if ($mapId != NULL) {
            $this->templateData['map'] = DB_ORM::model('map', array((int) $mapId));

            $this->templateData['files'] = DB_ORM::model('map_element')->getAllFilesByMap((int) $mapId);
            $fileInfo = DB_ORM::model('map_element')->getFilesSize($this->templateData['files']);

            $this->templateData['files_size'] = DB_ORM::model('map_element')->sizeFormat($fileInfo['size']);
            $this->templateData['files_count'] = $fileInfo['count'];
            $this->templateData['media_copyright'] = Kohana::$config->load('media_upload_copyright');

            Breadcrumbs::add(Breadcrumb::factory()->set_title($this->templateData['map']->name)->set_url(URL::base() . 'labyrinthManager/global/' . $mapId));
            Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Files'))->set_url(URL::base() . 'fileManager/index/' . $mapId));

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
        if ($_FILES and $mapId != NULL) {
            DB_ORM::model('map_element')->uploadFile($mapId, $_FILES);
            Request::initial()->redirect(URL::base() . 'fileManager/index/' . $mapId);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

    public function action_replaceFiles() {
        set_time_limit(0);
        $this->auto_render = false;

        $mapId = Arr::get($_POST, 'mapId', null);
        $fileName = Arr::get($_POST, 'fileName', null);

        $result = '';
        if($mapId != null && $fileName != null) {
            $dir = DOCROOT . '/files/' . $mapId ;
            if(!is_dir($dir)) {
                mkdir(DOCROOT . '/files/' . $mapId);
            }

            $dest = DOCROOT . '/files/' . $mapId . '/' . $fileName;
            $src  = DOCROOT . '/scripts/fileupload/php/files/' . $fileName;
            if (getimagesize($src)) {
                $src2 = DOCROOT . '/scripts/fileupload/php/thumbnails/' . $fileName;
                unlink($src2);
            }

            $path = 'files/' . $mapId . '/' . $fileName;

            $dataSave = array(
                'name' => $fileName,
                'path' => $path,
            );

            copy($src, $dest);
            unlink($src);

            DB_ORM::model('map_element')->saveElement($mapId,$dataSave);

            $result = $fileName;
        }

        echo $result;
    }

    public function action_deleteFile() {
        $mapId = $this->request->param('id', NULL);
        $fileId = $this->request->param('id2', NULL);
        if ($mapId != NULL and $fileId != NULL) {
            DB_ORM::model('map_element')->deleteFile($fileId);
            Request::initial()->redirect(URL::base() . 'fileManager/index/' . $mapId);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

    public function action_saveByUrl(){
        $mapId = $this->request->param('id', NULL);
        $url = Arr::get($this->request->post(), 'url', NULL);
        $meta_values['originURL'] = $url;

        if (($url != NULL) && ($url != '')){
            $url_info = pathinfo ($url);
            if(isset($url_info['basename'])){
                $name = $url_info['basename'];

                $path = DOCROOT . '/files/'.$mapId.'/';
                if (file_exists($path.$name)){
                    $name = uniqid().'_'.$name;
                }

                $img = file_get_contents($url, FILE_USE_INCLUDE_PATH);
                file_put_contents($path.$name, $img);

                $values['path'] = 'files/'.$mapId.'/'.$name;
                $values['name'] = $name;
                $obj = DB_ORM::model('map_element')->saveElement($mapId, $values);
                DB_ORM::model('map_element_metadata')->saveMetadata($obj->id, $meta_values);
            }
        }

        Request::initial()->redirect(URL::base().'fileManager/index/'.$mapId);
    }

    private function getArrayValueByKey($needle, $haystack) {
        if(array_key_exists($needle, $haystack)) { return array(true, $haystack[$needle]); }

        foreach($haystack as $v) {
            $k = false;
            $r = null;
            if(is_array($v)) {
                list($k, $r) = $this->getArrayValueByKey($needle, $v);
            }

            if($k) { return array(true, $r); }
        }

        return array(false, null);
    }

    public function action_editFile() {
        $mapId = $this->request->param('id', NULL);
        $fileId = $this->request->param('id2', NULL);
        if ($mapId != NULL and $fileId != NULL) {
            $this->templateData['map'] = DB_ORM::model('map', array((int) $mapId));
            $this->templateData['file'] = DB_ORM::model('map_element', array((int) $fileId));

            $extensionExist = extension_loaded('exif');
            if($extensionExist && isset($this->templateData['file']) && $this->templateData['file']->mime == 'image/jpeg') {
                $jpegInfo     = exif_read_data(DOCROOT . $this->templateData['file']->path, 0, true);
                $jpegMetadata = array();
                foreach($this->metadataJPEGFields as $metadataField) {
                    list($exist, $value) = $this->getArrayValueByKey($metadataField['key'], $jpegInfo);
                    if($exist) {
                        $jpegMetadata[] = array('title' => $metadataField['title'], 'value' => $value);
                    }
                }

                $this->templateData['fileMetadata'] = $jpegMetadata;
            } else if(!$extensionExist) {
                $this->templateData['enableModule'] = true;
            }

            Breadcrumbs::add(Breadcrumb::factory()->set_title($this->templateData['map']->name)->set_url(URL::base() . 'labyrinthManager/global/' . $mapId));
            Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Files'))->set_url(URL::base() . 'fileManager/index/' . $mapId));
            Breadcrumbs::add(Breadcrumb::factory()->set_title($this->templateData["file"]->name)->set_url(URL::base() . 'fileManager/editFile/' . $mapId.'/'.$fileId));
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
        if ($_POST and $mapId != NULL and $fileId != NULL) {
            DB_ORM::model('map_element')->updateFile($fileId, $_POST);
            Request::initial()->redirect(URL::base() . 'fileManager/index/' . $mapId);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

    public function action_imageEditor() {
        $mapId = $this->request->param('id', NULL);
        $fileId = $this->request->param('id2', NULL);
        if ($mapId != NULL and $fileId != NULL) {
            $this->templateData['map'] = DB_ORM::model('map', array((int) $mapId));
            $this->templateData['file'] = DB_ORM::model('map_element', array((int) $fileId));

            $fileView = View::factory('labyrinth/file/imageEditor');
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

    public function action_imageEditorPost() {
        $_POST["imageSource"] = DOCROOT . $_POST["filesrc"];
        $filesrc = DOCROOT . $_POST["filesrc"];
        list($width, $height) = getimagesize($_POST["imageSource"]);
        $viewPortW = $_POST["viewPortW"];
        $viewPortH = $_POST["viewPortH"];
        $pWidth = $_POST["imageW"];
        $pHeight = $_POST["imageH"];
        $ext = $this->endc(explode(".", $_POST["imageSource"]));
        $function = $this->returnCorrectFunction($ext);
        $image = $function($_POST["imageSource"]);
        $width = imagesx($image);
        $height = imagesy($image);
        // Resample
        $image_p = imagecreatetruecolor($pWidth, $pHeight);
        $this->setTransparency($image, $image_p, $ext);
        imagecopyresampled($image_p, $image, 0, 0, 0, 0, $pWidth, $pHeight, $width, $height);
        imagedestroy($image);
        $widthR = imagesx($image_p);
        $hegihtR = imagesy($image_p);

        $selectorX = $_POST["selectorX"];
        $selectorY = $_POST["selectorY"];

        if ($_POST["imageRotate"]) {
            $angle = 360 - $_POST["imageRotate"];
            $image_p = imagerotate($image_p, $angle, 0);

            $pWidth = imagesx($image_p);
            $pHeight = imagesy($image_p);

            $diffW = abs($pWidth - $widthR) / 2;
            $diffH = abs($pHeight - $hegihtR) / 2;

            $_POST["imageX"] = ($pWidth > $widthR ? $_POST["imageX"] - $diffW : $_POST["imageX"] + $diffW);
            $_POST["imageY"] = ($pHeight > $hegihtR ? $_POST["imageY"] - $diffH : $_POST["imageY"] + $diffH);
        }

        $dst_x = $src_x = $dst_y = $src_y = 0;

        if ($_POST["imageX"] > 0) {
            $dst_x = abs($_POST["imageX"]);
        } else {
            $src_x = abs($_POST["imageX"]);
        }
        if ($_POST["imageY"] > 0) {
            $dst_y = abs($_POST["imageY"]);
        } else {
            $src_y = abs($_POST["imageY"]);
        }


        $viewport = imagecreatetruecolor($_POST["viewPortW"], $_POST["viewPortH"]);
        $this->setTransparency($image_p, $viewport, $ext);

        imagecopy($viewport, $image_p, $dst_x, $dst_y, $src_x, $src_y, $pWidth, $pHeight);
        imagedestroy($image_p);


        $selector = imagecreatetruecolor($_POST["selectorW"], $_POST["selectorH"]);
        $this->setTransparency($viewport, $selector, $ext);
        imagecopy($selector, $viewport, 0, 0, $selectorX, $selectorY, $_POST["viewPortW"], $_POST["viewPortH"]);

        $this->parseImage($ext, $selector, $filesrc);
        imagedestroy($viewport);
        //Return value
        echo true;
        die();
        /* Functions */
    }

    private function endc($array) {
        return end($array);
    }

    private function determineImageScale($sourceWidth, $sourceHeight, $targetWidth, $targetHeight) {
        $scalex = $targetWidth / $sourceWidth;
        $scaley = $targetHeight / $sourceHeight;
        return min($scalex, $scaley);
    }

    private function returnCorrectFunction($ext) {
        $function = "";
        switch ($ext) {
            case "png":
                $function = "imagecreatefrompng";
                break;
            case "jpeg":
                $function = "imagecreatefromjpeg";
                break;
            case "jpg":
                $function = "imagecreatefromjpeg";
                break;
            case "gif":
                $function = "imagecreatefromgif";
                break;
        }
        return $function;
    }

    private function parseImage($ext, $img, $file = null) {
        switch ($ext) {
            case "png":
                imagepng($img, ($file != null ? $file : ''));
                break;
            case "jpeg":
                imagejpeg($img, ($file ? $file : ''), 90);
                break;
            case "jpg":
                imagejpeg($img, ($file ? $file : ''), 90);
                break;
            case "gif":
                imagegif($img, ($file ? $file : ''));
                break;
        }
    }

    private function setTransparency($imgSrc, $imgDest, $ext) {
        if ($ext == "png" || $ext == "gif") {
            $trnprt_indx = imagecolortransparent($imgSrc);
            // If we have a specific transparent color
            if ($trnprt_indx >= 0) {
                // Get the original image's transparent color's RGB values
                $trnprt_color = imagecolorsforindex($imgSrc, $trnprt_indx);
                // Allocate the same color in the new image resource
                $trnprt_indx = imagecolorallocate($imgDest, $trnprt_color['red'], $trnprt_color['green'], $trnprt_color['blue']);
                // Completely fill the background of the new image with allocated color.
                imagefill($imgDest, 0, 0, $trnprt_indx);
                // Set the background color for new image to transparent
                imagecolortransparent($imgDest, $trnprt_indx);
            }
            // Always make a transparent background color for PNGs that don't have one allocated already
            elseif ($ext == "png") {
                // Turn off transparency blending (temporarily)
                imagealphablending($imgDest, true);
                // Create a new transparent color for image
                $color = imagecolorallocatealpha($imgDest, 0, 0, 0, 127);
                // Completely fill the background of the new image with allocated color.
                imagefill($imgDest, 0, 0, $color);
                // Restore transparency blending
                imagesavealpha($imgDest, true);
            }
        }
    }

}

