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

class Controller_FileManager extends Controller_Base
{

    private $metadataJPEGFields = array(
        array('key' => 'Artist', 'title' => 'Artist'),
        array('key' => 'Company', 'title' => 'Company'),
        array('key' => 'Make', 'title' => 'Make'),
        array('key' => 'Model', 'title' => 'Model'),
        array('key' => 'DateTimeOriginal', 'title' => 'Original date time'),
        array('key' => 'DateTimeDigitized', 'title' => 'Digitized date time'),
        array('key' => 'Software', 'title' => 'Software'),
        array('key' => 'DateTime', 'title' => 'Date time')
    );

    public function before()
    {
        $this->templateData['labyrinthSearch'] = 1;

        parent::before();

        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('My Labyrinths'))->set_url(URL::base() . 'authoredLabyrinth'));
    }

    public function action_fileupload()
    {
        error_reporting(E_ALL | E_STRICT);
        set_time_limit(0);
        require_once DOCROOT . 'scripts/fileupload/php/customupload.class.php';

        $upload_handler = new CustomUploadHandler();

        header('Pragma: no-cache');
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Content-Disposition: inline; filename="files.json"');
        header('X-Content-Type-Options: nosniff');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: OPTIONS, HEAD, GET, POST, PUT, DELETE');
        header('Access-Control-Allow-Headers: X-File-Name, X-File-Type, X-File-Size');

        switch ($_SERVER['REQUEST_METHOD']) {
            case 'OPTIONS':
                break;
            case 'HEAD':
            case 'GET':
                $upload_handler->get();
                break;
            case 'POST':
                if (isset($_REQUEST['_method']) && $_REQUEST['_method'] === 'DELETE') {
                    $upload_handler->delete();
                } else {
                    $upload_handler->post();
                }
                break;
            case 'DELETE':
                $upload_handler->delete();
                break;
            default:
                header('HTTP/1.1 405 Method Not Allowed');
        }

        die;
    }

    public function action_index()
    {
        $mapId = $this->request->param('id', null);

        if (!$mapId) {
            Request::initial()->redirect(URL::base());
        }

        DB_ORM::model('User')->can('edit', array('mapId' => $mapId));
        $files = DB_ORM::model('map_element')->getAllFilesByMap((int)$mapId);
        $fileInfo = DB_ORM::model('map_element')->getFilesSize($files);

        $this->templateData['map'] = DB_ORM::model('map', array((int)$mapId));
        $this->templateData['files'] = $files;
        $this->templateData['files_size'] = DB_ORM::model('map_element')->sizeFormat($fileInfo['size']);
        $this->templateData['files_count'] = $fileInfo['count'];
        $this->templateData['media_copyright'] = Kohana::$config->load('media_upload_copyright');

        $ses = Session::instance();
        if ($ses->get('warningMessage')) {
            $this->templateData['warningMessage'] = $ses->get('warningMessage');
            $this->templateData['listOfUsedReferences'] = $ses->get('listOfUsedReferences');
            $ses->delete('listOfUsedReferences');
            $ses->delete('warningMessage');
        }

        $this->templateData['left'] = View::factory('labyrinth/labyrinthEditorMenu')->set('templateData',
            $this->templateData);
        $this->templateData['center'] = View::factory('labyrinth/file/view')->set('templateData', $this->templateData);
        $this->template->set('templateData', $this->templateData);

        Breadcrumbs::add(Breadcrumb::factory()->set_title($this->templateData['map']->name)->set_url(URL::base() . 'labyrinthManager/global/' . $mapId));
        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Files'))->set_url(URL::base() . 'fileManager/index/' . $mapId));
    }

    public function action_uploadFile()
    {
        $mapId = $this->request->param('id', null);
        if ($_FILES AND $mapId) {
            DB_ORM::model('map_element')->uploadFile($mapId, $_FILES);
            Request::initial()->redirect(URL::base() . 'fileManager/index/' . $mapId);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

    public function action_replaceFiles()
    {
        set_time_limit(0);
        $this->auto_render = false;

        $mapId = Arr::get($_POST, 'mapId', null);
        $fileName = Arr::get($_POST, 'fileName', null);
        $result = '';

        if ($mapId != null && $fileName != null) {
            $dir = DOCROOT . '/files/' . $mapId;
            if (!is_dir($dir)) {
                mkdir(DOCROOT . '/files/' . $mapId);
            }

            $dest = $dir . '/' . $fileName;
            $src = DOCROOT . '/scripts/fileupload/php/files/' . $fileName;

            if (getimagesize($src)) {
                $src2 = DOCROOT . '/scripts/fileupload/php/thumbnails/' . $fileName;
                if (file_exists($src2)) {
                    unlink($src2);
                }
            }

            $path = 'files/' . $mapId . '/' . $fileName;

            $dataSave = array(
                'name' => $fileName,
                'path' => $path,
            );

            copy($src, $dest);
            unlink($src);

            DB_ORM::model('map_element')->saveElement($mapId, $dataSave);

            $result = $fileName;
        }
        echo $result;
    }

    public function action_deleteFile()
    {
        $mapId = $this->request->param('id', null);
        $fileId = $this->request->param('id2', null);
        if ($mapId != null and $fileId != null) {
            DB_ORM::model('User')->can('edit', array('mapId' => $mapId));
            $references = DB_ORM::model('map_node_reference')->getByElementType($fileId, 'MR');
            if ($references != null) {
                $ses = Session::instance();
                $ses->set('listOfUsedReferences', CrossReferences::getListReferenceForView($references));
                $ses->set('warningMessage',
                    'The file wasn\'t deleted. The selected file is used in the following labyrinths:');
            } else {
                DB_ORM::model('map_element')->deleteFile($fileId);
            }
            Request::initial()->redirect(URL::base() . 'fileManager/index/' . $mapId);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

    public function action_saveByUrl()
    {
        $mapId = $this->request->param('id', null);
        $url = Arr::get($this->request->post(), 'url', null);
        $meta_values['originURL'] = $url;

        if (($url != null) && ($url != '')) {
            $url_info = pathinfo($url);
            if (isset($url_info['basename'])) {
                $name = $url_info['basename'];

                $path = DOCROOT . '/files/' . $mapId . '/';
                if (!is_dir($path)) {
                    mkdir($path);
                }
                if (file_exists($path . $name)) {
                    $name = uniqid() . '_' . $name;
                }

                $img = file_get_contents($url, FILE_USE_INCLUDE_PATH);
                file_put_contents($path . $name, $img);

                $values['path'] = 'files/' . $mapId . '/' . $name;
                $values['name'] = $name;
                $obj = DB_ORM::model('map_element')->saveElement($mapId, $values);
                DB_ORM::model('map_element_metadata')->saveMetadata($obj->id, $meta_values);
            }
        }

        Request::initial()->redirect(URL::base() . 'fileManager/index/' . $mapId);
    }

    private function getArrayValueByKey($needle, $haystack)
    {
        if (array_key_exists($needle, $haystack)) {
            return array(true, $haystack[$needle]);
        }

        foreach ($haystack as $v) {
            $k = false;
            $r = null;
            if (is_array($v)) {
                list($k, $r) = $this->getArrayValueByKey($needle, $v);
            }

            if ($k) {
                return array(true, $r);
            }
        }

        return array(false, null);
    }

    public function action_editFile()
    {
        $mapId = $this->request->param('id', null);
        $fileId = $this->request->param('id2', null);
        if ($mapId != null and $fileId != null) {

            DB_ORM::model('User')->can('edit', array('mapId' => $mapId));
            $this->templateData['map'] = DB_ORM::model('map', array((int)$mapId));
            $this->templateData['file'] = DB_ORM::model('map_element', array((int)$fileId));

            $usedElements = DB_ORM::model('map_node_reference')->getByElementType($this->templateData['file']->id,
                'MR');
            $this->templateData['used'] = count($usedElements);
            $extensionExist = extension_loaded('exif');
            if ($extensionExist && isset($this->templateData['file']) && $this->templateData['file']->mime == 'image/jpeg') {
                $jpegInfo = exif_read_data(DOCROOT . $this->templateData['file']->path, 0, true);
                $jpegMetadata = array();
                foreach ($this->metadataJPEGFields as $metadataField) {
                    list($exist, $value) = $this->getArrayValueByKey($metadataField['key'], $jpegInfo);
                    if ($exist) {
                        $jpegMetadata[] = array('title' => $metadataField['title'], 'value' => $value);
                    }
                }

                $this->templateData['fileMetadata'] = $jpegMetadata;
            } else {
                if (!$extensionExist) {
                    $this->templateData['enableModule'] = true;
                }
            }

            Breadcrumbs::add(Breadcrumb::factory()->set_title($this->templateData['map']->name)->set_url(URL::base() . 'labyrinthManager/global/' . $mapId));
            Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Files'))->set_url(URL::base() . 'fileManager/index/' . $mapId));
            Breadcrumbs::add(Breadcrumb::factory()->set_title($this->templateData["file"]->name)->set_url(URL::base() . 'fileManager/editFile/' . $mapId . '/' . $fileId));
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

    public function action_updateFile()
    {
        $mapId = $this->request->param('id', null);
        $fileId = $this->request->param('id2', null);
        if ($_POST and $mapId != null and $fileId != null) {
            $references = DB_ORM::model('map_node_reference')->getNotParent($mapId, $fileId, 'MR');
            $privete = Arr::get($_POST, 'is_private');
            if ($references != null && $privete) {
                $ses = Session::instance();
                $ses->set('listOfUsedReferences', CrossReferences::getListReferenceForView($references));
                $ses->set('warningMessage',
                    'The file wasn\'t set to private. The selected file is used in the following labyrinths:');
                $_POST['is_private'] = false;
            }
            DB_ORM::model('map_element')->updateFile($fileId, $_POST);
            Request::initial()->redirect(URL::base() . 'fileManager/index/' . $mapId);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

    public function action_imageEditor()
    {
        $mapId = $this->request->param('id', null);
        $fileId = $this->request->param('id2', null);

        if (!($mapId AND $fileId)) {
            Request::initial()->redirect(URL::base());
        }

        DB_ORM::model('User')->can('edit', array('mapId' => $mapId));
        $this->templateData['map'] = DB_ORM::model('map', array((int)$mapId));
        $this->templateData['file'] = DB_ORM::model('map_element', array((int)$fileId));
        $this->templateData['left'] = View::factory('labyrinth/labyrinthEditorMenu')->set('templateData',
            $this->templateData);
        $this->templateData['center'] = View::factory('labyrinth/file/imageEditor')->set('templateData',
            $this->templateData);
        $this->template->set('templateData', $this->templateData);
    }

    public function action_imageEditorPost()
    {
        $post = $this->request->post();
        $file_src = Arr::get($post, 'filesrc', false);
        if (!$file_src) {
            exit;
        }
        $imageSource = DOCROOT . $file_src;
        if (!file_exists($imageSource)) {
            exit;
        }
        $viewPortW = Arr::get($post, 'viewPortW');
        $viewPortH = Arr::get($post, 'viewPortH');
        $pWidth = Arr::get($post, 'imageW');
        $pHeight = Arr::get($post, 'imageH');
        $imageRotate = Arr::get($post, 'imageRotate');
        $ext = strtolower($this->endc(explode(".", $imageSource)));
        $getImg = getimagesize($file_src);
        $mime = Arr::get($getImg, 'mime', false);
        $function = $this->returnFunctionByMime($mime);
        $image = $function($imageSource);
        $imageX = Arr::get($post, '$imageX');
        $imageY = Arr::get($post, '$imageY');

        // Resample
        $image_p = imagecreatetruecolor($pWidth, $pHeight);
        $this->setTransparency($image, $image_p, $ext);
        imagecopyresampled($image_p, $image, 0, 0, 0, 0, $pWidth, $pHeight, imagesx($image), imagesy($image));
        imagedestroy($image);
        $widthR = imagesx($image_p);
        $heightR = imagesy($image_p);

        if ($imageRotate) {
            $angle = 360 - $imageRotate;
            $image_p = imagerotate($image_p, $angle, 0);

            $pWidth = imagesx($image_p);
            $pHeight = imagesy($image_p);

            $diffW = abs($pWidth - $widthR) / 2;
            $diffH = abs($pHeight - $heightR) / 2;

            $imageX = $pWidth > $widthR ? $imageX - $diffW : $imageX + $diffW;
            $imageY = ($pHeight > $heightR ? $imageY - $diffH : $imageY + $diffH);
        }

        $dst_x = $src_x = $dst_y = $src_y = 0;

        if ($imageX > 0) {
            $dst_x = abs($imageX);
        } else {
            $src_x = abs($imageX);
        }

        if ($imageY > 0) {
            $dst_y = abs($imageY);
        } else {
            $src_y = abs($imageY);
        }

        $viewPort = imagecreatetruecolor($viewPortW, $viewPortH);
        $this->setTransparency($image_p, $viewPort, $ext);

        imagecopy($viewPort, $image_p, $dst_x, $dst_y, $src_x, $src_y, $pWidth, $pHeight);
        imagedestroy($image_p);

        $selector = imagecreatetruecolor(Arr::get($post, 'selectorW'), Arr::get($post, 'selectorH'));
        $this->setTransparency($viewPort, $selector, $ext);
        imagecopy($selector, $viewPort, 0, 0, Arr::get($post, 'selectorX'), Arr::get($post, 'selectorY'), $viewPortW,
            $viewPortH);

        $this->parseImage($ext, $selector, $imageSource);
        imagedestroy($viewPort);
        //Return value
        echo true;
        exit();
    }

    private function endc($array)
    {
        return end($array);
    }

    private function determineImageScale($sourceWidth, $sourceHeight, $targetWidth, $targetHeight)
    {
        $scalex = $targetWidth / $sourceWidth;
        $scaley = $targetHeight / $sourceHeight;

        return min($scalex, $scaley);
    }

    private function returnCorrectFunction($ext)
    {
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

    private function returnFunctionByMime($mime)
    {
        $function = "";
        switch ($mime) {
            case "image/png":
                $function = "imagecreatefrompng";
                break;
            case "image/jpeg":
                $function = "imagecreatefromjpeg";
                break;
            case "image/gif":
                $function = "imagecreatefromgif";
                break;
        }

        return $function;
    }

    private function parseImage($ext, $img, $file = null)
    {
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

    private function setTransparency($imgSrc, $imgDest, $ext)
    {
        if ($ext == "png" || $ext == "gif") {
            $trnprt_indx = imagecolortransparent($imgSrc);
            // If we have a specific transparent color
            if ($trnprt_indx >= 0) {
                // Get the original image's transparent color's RGB values
                $trnprt_color = imagecolorsforindex($imgSrc, $trnprt_indx);
                // Allocate the same color in the new image resource
                $trnprt_indx = imagecolorallocate($imgDest, $trnprt_color['red'], $trnprt_color['green'],
                    $trnprt_color['blue']);
                // Completely fill the background of the new image with allocated color.
                imagefill($imgDest, 0, 0, $trnprt_indx);
                // Set the background color for new image to transparent
                imagecolortransparent($imgDest, $trnprt_indx);
            } // Always make a transparent background color for PNGs that don't have one allocated already
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

    public function action_arhiveUnZip()
    {
        $mapId = $this->request->param('id', null);
        $fileId = $this->request->param('id2', null);
        if ($mapId != null and $fileId != null) {
            $this->templateData['file'] = DB_ORM::model('map_element', array((int)$fileId));
            $arhfile = DOCROOT . $this->templateData['file']->path;
            $pathToArhFile = str_replace($this->templateData['file']->name, '', $arhfile);
            $tempFolder = 'tmp/' . md5($this->templateData['file']->name . rand()) . '/';

            $zip = new ZipArchive();
            if ($zip->open($arhfile) === true) {
                $zip->extractTo($tempFolder);
                $zip->close();
            }
            $listOfFile = $this->getListFiles($tempFolder);
            foreach ($listOfFile as $file) {
                if (is_dir($tempFolder . $file)) {
                    $this->removeDirectory($tempFolder . $file);
                }
            }
            DB_ORM::model('map_element')->deleteFile($fileId);
            try {
                if (file_exists($tempFolder . 'order.xml')) {
                    $simplexml = new SimpleXMLElement($tempFolder . 'order.xml', null, true);
                    foreach ($simplexml->order->item as $item) {
                        if (is_file($tempFolder . (string)$item)) {
                            $orderListByXML[] = (string)$item;
                        }
                    }
                    unlink($tempFolder . 'order.xml');

                    $listOfFile = $this->getListFiles($tempFolder);
                    $orderedList = $this->sortingList($listOfFile, $orderListByXML);
                } else {
                    $listOfFile = $this->getListFiles($tempFolder);
                    sort($listOfFile);
                    $orderedList = $listOfFile;
                    unset($listOfFile);
                }

                $this->moveUnpackFile($tempFolder, $pathToArhFile, $orderedList);
                foreach ($orderedList as $fileName) {
                    $path = 'files/' . $mapId . '/' . $fileName;
                    $dataSave = array(
                        'name' => $fileName,
                        'path' => $path,
                    );
                    DB_ORM::model('map_element')->saveElement($mapId, $dataSave);

                }
            } catch (Exception $e) {
                $this->removeDirectory($tempFolder);
                $ses = Session::instance();
                $ses->set('warningMessage', 'Please, validate "order.xml".');
            }
            Request::initial()->redirect(URL::base() . 'fileManager/index/' . $mapId);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

    private function getListFiles($dir)
    {
        $result = array();
        if (is_dir($dir)) {
            $result = array();
            $listOfFile = scandir($dir);
            foreach ($listOfFile as $file) {
                if ($file != '.' && $file != '..' && $file != 'empty.folder') {
                    $result[] = $file;
                }
            }
        }

        return $result;
    }

    private function sortingList($notorderlist, $orderListXML)
    {
        $orderedList = $notorderlist;
        for ($i_xml = 0; $i_xml < count($orderListXML); $i_xml++) {
            for ($i_file = 0; $i_file < count($notorderlist); $i_file++) {
                if ($orderListXML[$i_xml] === $notorderlist[$i_file]) {
                    unset($orderedList[$i_file]);
                }
            }
        }
        sort($orderedList);

        return array_merge($orderListXML, $orderedList);
    }

    private function moveUnpackFile($from, $to, $list)
    {
        foreach ($list as $nameFile) {
            if (is_file($from . $nameFile)) {
                rename($from . $nameFile, $to . $nameFile);
            }
        }
        $this->removeDirectory($from);

    }

    private function removeDirectory($dir)
    {
        if ($objs = glob($dir . "/*")) {
            foreach ($objs as $obj) {
                is_dir($obj) ? removeDirectory($obj) : unlink($obj);
            }
        }
        rmdir($dir);
    }

    public function action_delCheked()
    {
        $mapId = $this->request->param('id', null);
        $files = Arr::get($_POST, 'namecb', null);
        $notDeleted = null;
        if ($mapId != null) {
            if (!empty($files)) {
                foreach ($files as $file) {
                    $reference = DB_ORM::model('map_node_reference')->getByElementType($file, 'MR');
                    if ($reference != null) {
                        $notDeleted = $notDeleted .
                            '<a href="' . URL::base() . 'fileManager/deleteFile/' . $mapId . '/' . $file . '">' . '[[MR:' . $file . ']]' . '</a>' . ' ';
                    } else {
                        DB_ORM::model('map_element')->deleteFile($file);
                    }
                }
                if ($notDeleted != null) {
                    $ses = Session::instance();
                    $ses->set('warningMessage',
                        'The following files were not removed: ' . $notDeleted . '. Please, check other labyrinths on reference on this files.');
                }
            }
            Request::initial()->redirect(URL::base() . 'fileManager/index/' . $mapId);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

    public function action_globalFiles()
    {
        $mapId = $this->request->param('id', null);
        if ($mapId) {
            DB_ORM::model('User')->can('edit', array('mapId' => $mapId));

            $this->templateData['map'] = DB_ORM::model('map', array((int)$mapId));
            $this->templateData['files'] = array();
            $path = 'global/files/';
            $filesArray = $this->getListFiles($path);
            $elements = DB_ORM::model('map_element')->getAllFilesByMap($mapId);
            $list = array();
            $totalSize = 0;

            foreach ($filesArray as $file) {
                $filePath = DOCROOT . $path . $file;
                if (file_exists($filePath)) {
                    $totalSize += filesize($filePath);
                }

                $list['there'] = false;
                foreach ($elements as $element) {
                    if ($element->name == $file) {
                        $list['there'] = true;
                    }
                }
                $list['name'] = $file;
                $this->templateData['files'][] = $list;
            }

            $this->templateData['files_size'] = DB_ORM::model('map_element')->sizeFormat($totalSize);
            $this->templateData['files_count'] = count($this->templateData['files']);
            $this->templateData['left'] = View::factory('labyrinth/labyrinthEditorMenu')->set('templateData',
                $this->templateData);
            $this->templateData['center'] = View::factory('labyrinth/file/global')->set('templateData',
                $this->templateData);
            $this->template->set('templateData', $this->templateData);

            Breadcrumbs::add(Breadcrumb::factory()->set_title($this->templateData['map']->name)->set_url(URL::base() . 'labyrinthManager/global/' . $mapId));
            Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Global files'))->set_url(URL::base() . 'fileManager/index/' . $mapId));
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

    public function action_addToLabyrinth()
    {
        $mapId = $this->request->param('id', null);
        $fileName = base64_decode($this->request->param('id2', null));

        if ($mapId != null && $fileName != null) {
            $dir = 'files/' . $mapId;
            if (!is_dir($dir)) {
                mkdir('files/' . $mapId);
            }
            $dest = 'files/' . $mapId . '/' . $fileName;
            $src = 'global/files/' . $fileName;
            $path = 'files/' . $mapId . '/' . $fileName;
            $dataSave = array(
                'name' => $fileName,
                'path' => $path,
            );
            copy($src, $dest);

            DB_ORM::model('map_element')->saveElement($mapId, $dataSave);

            Request::initial()->redirect(URL::base() . 'fileManager/index/' . $mapId);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }
}

