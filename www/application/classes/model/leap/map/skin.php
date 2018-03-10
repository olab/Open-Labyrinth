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

/**
 * Model for map_skins table in database 
 */
class Model_Leap_Map_Skin extends DB_ORM_Model {

    public function __construct() {
        parent::__construct();

        $this->fields = array(
            'id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
                'unsigned' => TRUE,
            )),
            
            'name' => new DB_ORM_Field_String($this, array(
                'max_length' => 100,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            
            'path' => new DB_ORM_Field_String($this, array(
                'max_length' => 200,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),

            'user_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => TRUE,
            )),

            'enabled' => new DB_ORM_Field_Boolean($this, array(
                'default' => TRUE,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),

            'data' => new DB_ORM_Field_Text($this, array(
                'nullable' => TRUE,
                'savable' => TRUE
            ))
        );
    }

    public static function data_source() {
        return 'default';
    }

    public static function table() {
        return 'map_skins';
    }

    public static function primary_key() {
        return array('id');
    }

    public function getSkinById($id){
        return DB_ORM::select('Map_Skin')->where('id', '=', $id)->where('enabled', '=', '1')->query()->fetch(0);
    }

    public function getAllSkins()
    {
        return DB_ORM::select('Map_Skin')->where('enabled', '=', '1')->query()->as_array();
    }

    public function getSkinsByUserId($user_id){
        $builder = DB_SQL::select('default')->from($this->table())->where('user_id', '=', $user_id)->where('enabled', '=', '1');
        $result = $builder->query();

        $skins = array();
        if ($result->is_loaded()) {
            foreach ($result as $record) {
                $skins[] = DB_ORM::model('map_skin', array((int)$record['id']));
            }
        }

        return $skins;
    }

    public function updateSkinName($id, $name, $mapId){
        $error = false;
        $nameChanged = true;
        $skin = $this->getMapByName($name);
        if ($skin != NULL){
            if ($skin->id != $id){
                $error = true;
            }else{
                $nameChanged = false;
            }
        }

        if (!$error){
            if ($nameChanged){
                $this->id = $id;
                $this->load();

                if ($this->is_loaded()){
                    $newPath = $mapId.'_'.$name;
                    rename(DOCROOT.'css/skin/'.$this->path, DOCROOT.'css/skin/'.$newPath);

                    $this->name = $name;
                    $this->path = $newPath;
                    $this->save();

                    return true;
                }
            }
        }else{
            Session::instance()->set('skinError', __('That name is already taken.'));
            return false;
        }
    }

    public function deleteSkin($id){
        $this->id = $id;
        $this->load();

        if ($this->is_loaded()){
            if ($this->user_id == Auth::instance()->get_user()->id){
                $this->enabled = 0;
                $this->save();
                return true;
            } else {
                return false;
            }
        }
        return false;
    }

    public function addSkin ($skinName, $skinPath)
    {
        $this->name     = $skinName;
        $this->path     = $skinPath;
        $this->user_id  = Auth::instance()->get_user()->id;
        $this->enabled  = 1;
        $this->save();

        return $this->getMapByName($this->name);
    }

    public function getMapByName($name){
        return DB_ORM::select('Map_Skin')->where('name', '=', $name)->where('enabled', '=', '1')->query()->fetch(0);
    }

    public function updateSkinData($skinId, $data, $html)
    {
        DB_ORM::update('Map_Skin')->set('data', $data)->where('id', '=', $skinId)->execute();
        $skinDir = DOCROOT.'/application/views/labyrinth/skin/'.$skinId.'/';
        if( ! is_dir($skinDir)) mkdir($skinDir);

        file_put_contents($skinDir.'skin.source', $html);
        $skinBuilder         = new Skin_Basic_Builder($html);
        $skinBuilderDirector = new Skin_Basic_Director();

        $skinBuilderDirector->construct($skinBuilder);
        file_put_contents($skinDir.'skin.php', $skinBuilder->getSkin());
    }
}