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

class Controller_AvatarManager extends Controller_Base
{

    public function before()
    {
        $this->templateData['labyrinthSearch'] = 1;

        parent::before();

        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('My Labyrinths'))->set_url(URL::base() . 'authoredLabyrinth'));
    }

    public function action_index()
    {
        $mapId = $this->request->param('id', null);
        if ($mapId != null) {
            $this->templateData['map'] = DB_ORM::model('map', array((int)$mapId));
            $this->templateData['avatars'] = DB_ORM::model('map_avatar')->getAvatarsByMap((int)$mapId);

            $ses = Session::instance();
            if ($ses->get('warningMessage')) {
                $this->templateData['warningMessage'] = $ses->get('warningMessage');
                $this->templateData['listOfUsedReferences'] = $ses->get('listOfUsedReferences');
                $ses->delete('listOfUsedReferences');
                $ses->delete('warningMessage');
            }

            if (Auth::instance()->get_user()->type->name == 'superuser') {
                $this->templateData['isSuperuser'] = true;
            }

            Breadcrumbs::add(Breadcrumb::factory()->set_title($this->templateData['map']->name)->set_url(URL::base() . 'labyrinthManager/global/' . $mapId));
            Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Avatars'))->set_url(URL::base() . 'avatarManager/index/' . $mapId));

            $avatarView = View::factory('labyrinth/avatar/view');
            $avatarView->set('templateData', $this->templateData);

            $leftView = View::factory('labyrinth/labyrinthEditorMenu');
            $leftView->set('templateData', $this->templateData);

            $this->templateData['left'] = $leftView;
            $this->templateData['center'] = $avatarView;
            unset($this->templateData['right']);
            $this->template->set('templateData', $this->templateData);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

    public function action_addAvatar()
    {
        $mapId = $this->request->param('id', null);
        if ($mapId != null) {
            $avatarId = DB_ORM::model('map_avatar')->addAvatar($mapId);
            Request::initial()->redirect(URL::base() . 'avatarManager/editAvatar/' . $mapId . '/' . $avatarId);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

    public function action_editAvatar()
    {
        $mapId = $this->request->param('id', null);
        $avatarId = $this->request->param('id2', null);
        if ($mapId != null and $avatarId != null) {
            $this->templateData['map'] = DB_ORM::model('map', array((int)$mapId));
            $this->templateData['avatar'] = DB_ORM::model('map_avatar', array((int)$avatarId));
            $usedElements = DB_ORM::model('map_node_reference')->getByElementType($avatarId, 'AV');
            $this->templateData['used'] = count($usedElements);

            $ses = Session::instance();
            if ($ses->get('warningMessage')) {
                $this->templateData['warningMessage'] = $ses->get('warningMessage');
                $this->templateData['listOfUsedReferences'] = $ses->get('listOfUsedReferences');
                $ses->delete('listOfUsedReferences');
                $ses->delete('warningMessage');
            }

            Breadcrumbs::add(Breadcrumb::factory()->set_title($this->templateData['map']->name)->set_url(URL::base() . 'labyrinthManager/global/' . $mapId));
            Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Avatars'))->set_url(URL::base() . 'avatarManager/index/' . $mapId));
            Breadcrumbs::add(Breadcrumb::factory()->set_title($avatarId)->set_url(URL::base() . 'avatarManager/editAvatar/' . $mapId . '/' . $avatarId));
            $edtAvatarView = View::factory('labyrinth/avatar/edit');
            $edtAvatarView->set('templateData', $this->templateData);

            $leftView = View::factory('labyrinth/labyrinthEditorMenu');
            $leftView->set('templateData', $this->templateData);

            $this->templateData['left'] = $leftView;
            $this->templateData['center'] = $edtAvatarView;
            unset($this->templateData['right']);
            $this->template->set('templateData', $this->templateData);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

    public function action_deleteAvatar()
    {
        $mapId = $this->request->param('id', null);
        $avatarId = $this->request->param('id2', null);
        if ($mapId != null and $avatarId != null) {
            $references = DB_ORM::model('map_node_reference')->getByElementType($avatarId, 'AV');
            if ($references != null) {
                $ses = Session::instance();
                $ses->set('listOfUsedReferences', CrossReferences::getListReferenceForView($references));
                $ses->set('warningMessage',
                    'The avatar wasn\'t deleted. The selected avatar is used in the following labyrinths:');
            } else {
                $upload_dir = DOCROOT . '/avatars/';
                $avatarImage = DB_ORM::model('map_avatar')->getAvatarImage($avatarId);
                if (!empty($avatarImage)) {
                    @unlink($upload_dir . $avatarImage);
                }
                DB_ORM::model('map_avatar', array((int)$avatarId))->delete();
            }
            Request::initial()->redirect(URL::base() . 'avatarManager/index/' . $mapId);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

    public function action_updateAvatar()
    {
        $mapId = $this->request->param('id', null);
        $avatarId = $this->request->param('id2', null);
        if ($_POST and $mapId != null and $avatarId != null) {
            $upload_dir = DOCROOT . '/avatars/';
            $avatarImage = DB_ORM::model('map_avatar')->getAvatarImage($avatarId);
            if (!empty($avatarImage)) {
                @unlink($upload_dir . $avatarImage);
            }
            $img = $_POST['image_data'];
            $img = str_replace('data:image/png;base64,', '', $img);
            $img = str_replace(' ', '+', $img);
            $data = base64_decode($img);
            $file = uniqid() . '.png';
            file_put_contents($upload_dir . $file, $data);
            $_POST['image_data'] = $file;
            $references = DB_ORM::model('map_node_reference')->getNotParent($mapId, $avatarId, 'AV');
            $privete = Arr::get($_POST, 'is_private');
            if ($references != null && $privete) {
                $ses = Session::instance();
                $ses->set('listOfUsedReferences', CrossReferences::getListReferenceForView($references));
                $ses->set('warningMessage',
                    'The avatar wasn\'t set to private. The selected avatar is used in the following labyrinths:');
                $_POST['is_private'] = false;
            }
            DB_ORM::model('map_avatar')->updateAvatar($avatarId, $_POST);
            if ($_POST['save_exit_value'] == 0) {
                Request::initial()->redirect(URL::base() . 'avatarManager/editAvatar/' . $mapId . '/' . $avatarId);
            } else {
                Request::initial()->redirect(URL::base() . 'avatarManager/index/' . $mapId);
            }
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

    public function action_duplicateAvatar()
    {
        $mapId = $this->request->param('id', null);
        $avatarId = $this->request->param('id2', null);
        if ($mapId != null and $avatarId != null) {
            $avatarImage = DB_ORM::model('map_avatar')->getAvatarImage($avatarId);
            if (!empty($avatarImage)) {
                $upload_dir = DOCROOT . '/avatars/';
                $file = uniqid() . '.png';
                copy($upload_dir . $avatarImage, $upload_dir . $file);
            } else {
                $file = null;
            }
            DB_ORM::model('map_avatar')->duplicateAvatar($avatarId, $file);
            Request::initial()->redirect(URL::base() . 'avatarManager/index/' . $mapId);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

    public function action_globalAvatars()
    {
        $mapId = $this->request->param('id', null);
        if ($mapId != null) {
            $this->templateData['map'] = DB_ORM::model('map', array((int)$mapId));

            $this->templateData['avatars'] = $this->getListPNG('global/avatars/');

            Breadcrumbs::add(Breadcrumb::factory()->set_title($this->templateData['map']->name)->set_url(URL::base() . 'labyrinthManager/global/' . $mapId));
            Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Global avatars'))->set_url(URL::base() . 'avatarManager/index/' . $mapId));

            $avatarView = View::factory('labyrinth/avatar/global');
            $avatarView->set('templateData', $this->templateData);

            $leftView = View::factory('labyrinth/labyrinthEditorMenu');
            $leftView->set('templateData', $this->templateData);

            $this->templateData['left'] = $leftView;
            $this->templateData['center'] = $avatarView;
            unset($this->templateData['right']);
            $this->template->set('templateData', $this->templateData);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

    public function action_exportAvatar()
    {
        $mapId = $this->request->param('id', null);
        $avatarId = $this->request->param('id2', null);
        if ($mapId != null && $avatarId != null) {
            $rand = uniqid();
            $tmpfolder = 'tmp/' . $rand . '/';
            if (mkdir($tmpfolder)) {
                $avatar = DB_ORM::model('map_avatar')->getAvatarById($avatarId);
                $name = 'avatar_' . $rand;
                $this->createXMLFile($avatar[0], $name, $tmpfolder);
                $this->copyAvatarsImages($avatar[0], $name, $tmpfolder);

                $this->createZipArchive($tmpfolder, $rand);
                $this->removeDirectory($tmpfolder);

                $zipFile = 'tmp/' . $rand . '.zip';
                $pathInfo = pathinfo($zipFile);
                header("Cache-Control: public");
                header("Content-Description: File Transfer");
                header("Content-Disposition: attachment; filename=" . $pathInfo['basename']);
                header("Content-Type: application/zip");
                header("Content-Transfer-Encoding: binary");
                readfile('tmp/' . $rand . '.zip');
                unlink($zipFile);
            } else {
                Request::initial()->redirect(URL::base() . 'avatarManager/index/' . $mapId);
            }
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

    private function createZipArchive($folderPath, $name)
    {
        $dest = 'tmp/' . $name . '.zip';
        $zip = new ZipArchive();

        if ($h = opendir($folderPath)) {
            if ($zip->open($dest, ZIPARCHIVE::CREATE)) {
                while (false !== ($f = readdir($h))) {
                    if (strstr($f, '.') && file_exists($folderPath . '/' . $f) && strcmp($f, '.') != 0 && strcmp($f,
                            '..') != 0
                    ) {
                        $zip->addFile($folderPath . '/' . $f, $f);
                    }
                }
            }
            closedir($h);
        }
        $zip->close();

        return true;
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

    public function action_importAvatar()
    {
        $mapId = $this->request->param('id', null);
        $filePNG = base64_decode($this->request->param('id2', null));
        $filename = explode('.', $filePNG);
        $xml = $filename[0] . '.xml';
        if ($mapId != null && is_file('global/avatars/' . $xml)) {
            $xmlfile = file_get_contents('global/avatars/' . $xml);
            $ob = simplexml_load_string($xmlfile);
            $data = array();
            foreach ($ob as $tags) {
                $data = $tags;
            }
            $fields = array(
                'avskin1' => $data->skin_1,
                'avskin2' => $data->skin_2,
                'avcloth' => $data->cloth,
                'avnose' => $data->nose,
                'avhair' => $data->hair,
                'avenvironment' => $data->environment,
                'avaccessory1' => $data->accessory_1,
                'avbkd' => $data->bkd,
                'avsex' => $data->sex,
                'avmouth' => $data->mouth,
                'avoutfit' => $data->outfit,
                'avbubble' => $data->bubble,
                'avbubbletext' => $data->bubble_text,
                'avaccessory2' => $data->accessory_2,
                'avaccessory3' => $data->accessory_3,
                'avage' => $data->age,
                'aveyes' => $data->eyes,
                'avhaircolor' => $data->haircolor,
                'image_data' => $data->image,
                'is_private' => $data->noseis_private
            );
            $avatarId = DB_ORM::model('map_avatar')->addAvatar($mapId);
            DB_ORM::model('map_avatar')->updateAvatar($avatarId, $fields);
            $dest = 'avatars/' . $filePNG;
            $src = 'global/avatars/' . $filePNG;
            copy($src, $dest);
            Request::initial()->redirect(URL::base() . 'avatarManager/index/' . $mapId);
        } else {
            Request::initial()->redirect(URL::base());
        }
    }

    private function createXMLFile($array, $name, $path)
    {
        if (count($array) > 0) {
            $xml = new SimpleXMLElement('<xml />');
            $arrayXml = $xml->addChild($name);
            $this->createXMLTree($arrayXml, $array, $name);
            $filePath = $path . $name . '.xml';
            $f = fopen($filePath, 'w');
            if (function_exists('dom_import_simplexml')) {
                $dom = dom_import_simplexml($xml)->ownerDocument;
                $dom->formatOutput = true;
                $xmlObject = $dom;
            } else {
                $xmlObject = $xml;
            }
            $outputXML = str_replace('<?xml version="1.0"?>', '<?xml version="1.0" encoding="UTF-8"?>',
                $xmlObject->saveXML());
            fwrite($f, $outputXML);
            fclose($f);
        }
    }

    private function copyAvatarsImages($avatar, $name, $path)
    {
        if (count($avatar) <= 0) {
            return;
        }
        if (($avatar['image'] != 'ntr') && ($avatar['image'] != '')) {
            $avatarImagePath = 'avatars/' . $avatar['image'];
            if (file_exists($avatarImagePath) && is_dir($path)) {
                copy($avatarImagePath, $path . $name . '.png');
            }
        }
    }

    private function createXMLTree($xml, $array, $name)
    {
        if (count($array) > 0) {
            $array['image'] = $name . '.png';
            foreach ($array as $key => $value) {
                $xml->addChild($key, (string)$value);
            }
        }
    }

    private function getListPNG($dir)
    {
        $listPNG = array();
        if (is_dir($dir)) {
            $listOfFile = scandir($dir);
            foreach ($listOfFile as $file) {
                $extens = explode('.', $file);
                if ($extens[1] == 'png') {
                    $listPNG[] = $file;
                }
            }
        }

        return $listPNG;
    }
}

