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
 * Model for map_avatars table in database 
 */
class Model_Leap_Map_Avatar extends DB_ORM_Model {

    public function __construct() {
        parent::__construct();

        $this->fields = array(
            'id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
                'unsigned' => TRUE,
            )),
            
            'map_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            
            'skin_1' => new DB_ORM_Field_String($this, array(
                'max_length' => 6,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            
            'skin_2' => new DB_ORM_Field_String($this, array(
                'max_length' => 6,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            
            'cloth' => new DB_ORM_Field_String($this, array(
                'max_length' => 6,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            
            'nose' => new DB_ORM_Field_String($this, array(
                'max_length' => 20,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            
            'hair' => new DB_ORM_Field_String($this, array(
                'max_length' => 20,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            
            'environment' => new DB_ORM_Field_String($this, array(
                'max_length' => 20,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            
            'accessory_1' => new DB_ORM_Field_String($this, array(
                'max_length' => 20,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            
            'accessory_2' => new DB_ORM_Field_String($this, array(
                'max_length' => 20,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            
            'accessory_3' => new DB_ORM_Field_String($this, array(
                'max_length' => 20,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            
            'bkd' => new DB_ORM_Field_String($this, array(
                'max_length' => 6,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            
            'sex' => new DB_ORM_Field_String($this, array(
                'max_length' => 20,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            
            'mouth' => new DB_ORM_Field_String($this, array(
                'max_length' => 20,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            
            'outfit' => new DB_ORM_Field_String($this, array(
                'max_length' => 20,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            
            'bubble' => new DB_ORM_Field_String($this, array(
                'max_length' => 20,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            
            'bubble_text' => new DB_ORM_Field_String($this, array(
                'max_length' => 100,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            
            'age' => new DB_ORM_Field_String($this, array(
                'max_length' => 2,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            
            'eyes' => new DB_ORM_Field_String($this, array(
                'max_length' => 20,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            
            'hair_color' => new DB_ORM_Field_String($this, array(
                'max_length' => 6,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),

            'image' => new DB_ORM_Field_String($this, array(
                'max_length' => 100,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            'is_private' => new DB_ORM_Field_Boolean($this, array(
                'savable' => TRUE,
                'nullable' => FALSE,
                'default' => FALSE
            ))
        );
        
        $this->relations = array(
            'map' => new DB_ORM_Relation_BelongsTo($this, array(
                'child_key' => array('map_id'),
                'parent_key' => array('id'),
                'parent_model' => 'map',
            )),
        );
    }

    public static function data_source() {
        return 'default';
    }

    public static function table() {
        return 'map_avatars';
    }

    public static function primary_key() {
        return array('id');
    }
    
    public function addAvatar($mapId) {
        $builder = DB_ORM::insert('map_avatar')
                ->column('map_id', $mapId);
        return $builder->execute();
    }

    public function getLastAddedAvatar($mapId) {
        $builder = DB_SQL::select('default')->from($this->table())->where('map_id', '=', $mapId)->order_by('id', 'DESC')->limit(1);
        $result = $builder->query();

        if ($result->is_loaded()) {
            return DB_ORM::model('map_avatar', array($result[0]['id']));
        }

        return NULL;
    }

    public function updateAvatar($avatarId, $values) {
        $this->id = $avatarId;
        $this->load();
        
        if ($this->is_loaded())
        {
            $this->skin_1       = Arr::get($values, 'avskin1', $this->skin_1);
            $this->skin_2       = Arr::get($values, 'avskin2', $this->skin_2);
            $this->cloth        = Arr::get($values, 'avcloth', $this->cloth);
            $this->nose         = Arr::get($values, 'avnose', $this->nose);
            $this->hair         = Arr::get($values, 'avhair', $this->hair);
            $this->environment  = Arr::get($values, 'avenvironment', $this->environment);
            $this->accessory_1  = Arr::get($values, 'avaccessory1', $this->accessory_1);
            $this->bkd          = Arr::get($values, 'avbkd', $this->bkd);
            $this->sex          = Arr::get($values, 'avsex', $this->sex);
            $this->mouth        = Arr::get($values, 'avmouth', $this->mouth);
            $this->outfit       = Arr::get($values, 'avoutfit', $this->outfit);
            $this->bubble       = Arr::get($values, 'avbubble', $this->bubble);
            $this->bubble_text  = Arr::get($values, 'avbubbletext', $this->bubble_text);
            $this->accessory_2  = Arr::get($values, 'avaccessory2', $this->accessory_2);
            $this->accessory_3  = Arr::get($values, 'avaccessory3', $this->accessory_3);
            $this->age          = Arr::get($values, 'avage', $this->age);
            $this->eyes         = Arr::get($values, 'aveyes', $this->eyes);
            $this->hair_color   = Arr::get($values, 'avhaircolor', $this->hair_color);
            $this->image        = Arr::get($values, 'image_data', $this->image);

            $this->save();
        }
    }
    
    public function getAvatarsByMap ($mapId)
    {
        $builder = DB_SQL::select('default')->from($this->table())->where('map_id', '=', $mapId);
        $result = $builder->query();
        
        if ($result->is_loaded())
        {
            $avatars = array();
            foreach($result as $record) $avatars[] = DB_ORM::model('map_avatar', array((int)$record['id']));
            return $avatars;
        }
        return array();
    }

    public function getAvatarImage ($avatarId)
    {
        $builder = DB_SQL::select('default', array('image'))->from($this->table())->where('id', '=', $avatarId);
        $result = $builder->query();

        if($result->is_loaded()) return $result[0]['image'];
    }

    public function duplicateAvatar($avatarId, $file) {
        $this->id = $avatarId;
        $this->load();
        
        if($this->is_loaded()) {
            $duplicateAvatar = DB_ORM::model('map_avatar');
            $duplicateAvatar->map_id = $this->map_id;
            $duplicateAvatar->skin_1 = $this->skin_1;
            $duplicateAvatar->skin_2 = $this->skin_2;
            $duplicateAvatar->cloth = $this->cloth;
            $duplicateAvatar->nose = $this->nose;
            $duplicateAvatar->hair = $this->hair;
            $duplicateAvatar->environment = $this->environment;
            $duplicateAvatar->accessory_1 = $this->accessory_1;
            $duplicateAvatar->accessory_2 = $this->accessory_2;
            $duplicateAvatar->accessory_3 = $this->accessory_3;
            $duplicateAvatar->bkd = $this->bkd;
            $duplicateAvatar->sex = $this->sex;
            $duplicateAvatar->mouth = $this->mouth;
            $duplicateAvatar->outfit = $this->outfit;
            $duplicateAvatar->bubble = $this->bubble;
            $duplicateAvatar->bubble_text = $this->bubble_text;
            $duplicateAvatar->age = $this->age;
            $duplicateAvatar->eyes = $this->eyes;
            $duplicateAvatar->hair_color = $this->hair_color;
            $duplicateAvatar->image = $file;
            $duplicateAvatar->is_private = $this->is_private;

            $duplicateAvatar->save();
        }
    }

    public function duplicateAvatars($fromMapId, $toMapId)
    {
        $avatarMap = array();

        if ( ! $toMapId) return $avatarMap;

        foreach ($this->getAvatarsByMap($fromMapId) as $avatar)
        {
            $avatarImage = $this->getAvatarImage($avatar->id);
            $file = NULL;

            if ( ! empty($avatarImage)) {
                $upload_dir = DOCROOT.'avatars\\';
                $avatarPath = $upload_dir.$avatarImage;
				if (is_dir($upload_dir) AND file_exists($avatarPath)) {
                    copy($avatarPath, $upload_dir.uniqid().'.png');
                }
            }

            $avatarMap[$avatar->id] = DB_ORM::insert('map_avatar')
                    ->column('map_id', $toMapId)
                    ->column('skin_1', $avatar->skin_1)
                    ->column('skin_2', $avatar->skin_2)
                    ->column('cloth', $avatar->cloth)
                    ->column('nose', $avatar->nose)
                    ->column('hair', $avatar->hair)
                    ->column('environment', $avatar->environment)
                    ->column('accessory_1', $avatar->accessory_1)
                    ->column('accessory_2', $avatar->accessory_2)
                    ->column('accessory_3', $avatar->accessory_3)
                    ->column('bkd', $avatar->bkd)
                    ->column('sex', $avatar->sex)
                    ->column('mouth', $avatar->mouth)
                    ->column('outfit', $avatar->outfit)
                    ->column('bubble', $avatar->bubble)
                    ->column('bubble_text', $avatar->bubble_text)
                    ->column('age', $avatar->age)
                    ->column('eyes', $avatar->eyes)
                    ->column('hair_color', $avatar->hair_color)
                    ->column('image', $file)
                    ->column('is_private', $avatar->is_private)
                    ->execute();
        }
        return $avatarMap;
    }

    public function getAvatarById($id) {
        $builder = DB_SQL::select('default')->from($this->table())->where('id', '=', $id);
        $result = $builder->query();

        if($result->is_loaded()) {
            $avatars = array();

            foreach($result as $key => $value){
                $avatars[$key] = $value;
            }

            return $avatars;
        }

        return NULL;
    }
}