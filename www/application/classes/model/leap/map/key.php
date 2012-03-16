<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for map_keys table in database 
 */
class Model_Leap_Map_Key extends DB_ORM_Model {

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
            
            'key' => new DB_ORM_Field_String($this, array(
                'max_length' => 20,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
        );
    }

    public static function data_source() {
        return 'default';
    }

    public static function table() {
        return 'map_keys';
    }

    public static function primary_key() {
        return array('id');
    }
    
    public function getKeysByMap($mapId) {
        $builder = DB_SQL::select('default')->from($this->table())->where('map_id', '=', $mapId);
        $result = $builder->query();
        
        if($result->is_loaded()) {
            $keys = array();
            foreach($result as $record) {
                $keys[] = DB_ORM::model('map_key', array((int)$record['id']));
            }
            
            return $keys;
        }
        
        return NULL;
    }
    
    public function updateKeys($mapId, $values) {
        $keys = $this->getKeysByMap($mapId);
        if($keys != NULL) {
            if(count($keys) > 0) {
                foreach($keys as $key) {
                    $data = Arr::get($values, 'key_'.$key->id, NULL);
                    if($data != NULL) {
                        $key->key = $data;
                        $key->save();
                    }
                }
            }
        }
    }
    
    public function createKeys($mapId, $values, $count) {
        for($i = 0; $i < $count; $i++) {
            $data = Arr::get($values, 'akey_'.($i+1), NULL);
            var_dump($data);
            if($data != NULL) {
                $newKey = DB_ORM::model('map_key');
                $newKey->map_id = $mapId;
                $newKey->key = $data;
                $newKey->save();
            }
        }
    }
}

?>