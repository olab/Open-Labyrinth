<?php defined('SYSPATH') or die('No direct script access.');

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
                'max_length' => 2,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            
            'hair' => new DB_ORM_Field_String($this, array(
                'max_length' => 2,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            
            'environment' => new DB_ORM_Field_String($this, array(
                'max_length' => 2,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            
            'accessory_1' => new DB_ORM_Field_String($this, array(
                'max_length' => 2,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            
            'accessory_2' => new DB_ORM_Field_String($this, array(
                'max_length' => 2,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            
            'accessory_3' => new DB_ORM_Field_String($this, array(
                'max_length' => 2,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            
            'bkd' => new DB_ORM_Field_String($this, array(
                'max_length' => 6,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            
            'sex' => new DB_ORM_Field_String($this, array(
                'max_length' => 2,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            
            'mouth' => new DB_ORM_Field_String($this, array(
                'max_length' => 2,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            
            'outfit' => new DB_ORM_Field_String($this, array(
                'max_length' => 2,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            
            'bubble' => new DB_ORM_Field_String($this, array(
                'max_length' => 2,
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
                'max_length' => 2,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            
            'weather' => new DB_ORM_Field_String($this, array(
                'max_length' => 2,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            
            'hair_color' => new DB_ORM_Field_String($this, array(
                'max_length' => 6,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
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
    
    public function updateAvatar($avatarId, $values) {
        $this->id = $avatarId;
        $this->load();
        
        if($this->is_loaded()) {
            $this->sex = Arr::get($values, 'avsex', $this->sex);
            $this->mouth = Arr::get($values, 'avmouth', $this->mouth);
            $this->age = Arr::get($values, 'avage', $this->age);
            $this->eyes = Arr::get($values, 'aveyes', $this->eyes);
            $this->outfit = Arr::get($values, 'avoutfit', $this->outfit);
            $this->cloth = Arr::get($values, 'avcloth', $this->cloth);
            $this->nose = Arr::get($values, 'avnose', $this->nose);
            $this->hair = Arr::get($values, 'avhair', $this->hair);
            $this->hair_color = Arr::get($values, 'avhaircolor', $this->hair_color);
            $this->accessory_1 = Arr::get($values, 'avaccessory1', $this->accessory_1);
            $this->accessory_2 = Arr::get($values, 'avaccessory2', $this->accessory_2);
            $this->accessory_3 = Arr::get($values, 'avaccessory3', $this->accessory_3);
            $this->skin_1 = Arr::get($values, 'avskin1', $this->skin_1);
            $this->skin_2 = Arr::get($values, 'avskin2', $this->skin_2);
            $this->bkd = Arr::get($values, 'avbkd', $this->bkd);
            $this->environment = Arr::get($values, 'avenvironment', $this->environment);
            $this->weather = Arr::get($values, 'avweather', $this->weather);
            $this->bubble = Arr::get($values, 'avbubble', $this->bubble);
            $this->bubble_text = Arr::get($values, 'avbubbletext', $this->bubble_text);
            
            $this->save();
        }
    }
    
    public function getAvatarsByMap($mapId) {
        $builder = DB_SQL::select('default')->from($this->table())->where('map_id', '=', $mapId);
        $result = $builder->query();
        
        if($result->is_loaded()) {
            $avatars = array();
            foreach($result as $record) {
                $avatars[] = DB_ORM::model('map_avatar', array((int)$record['id']));
            }
            
            return $avatars;
        }
        
        return NULL;
    }
    
    public function duplicateAvatar($avatarId) {
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
            $duplicateAvatar->weather = $this->weather;
            $duplicateAvatar->hair_color = $this->hair_color;
            
            $duplicateAvatar->save();
        }
    }
}

?>