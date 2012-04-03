<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for map_remoteServices table in database 
 */
class Model_Leap_RemoteService extends DB_ORM_Model {

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
            
            'type' => new DB_ORM_Field_String($this, array(
                'max_length' => 1,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            
            'ip' => new DB_ORM_Field_String($this, array(
                'max_length' => 50,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
        );
        
        $this->relations = array(
            'maps' => new DB_ORM_Relation_HasMany($this, array(
                'child_key' => array('service_id'),
                'child_model' => 'remoteMap',
                'parent_key' => array('id'),
            )),
        );
    }

    public static function data_source() {
        return 'default';
    }

    public static function table() {
        return 'remoteServices';
    }

    public static function primary_key() {
        return array('id');
    }
    
    
    public function checkService($ip) {
        $builder = DB_SQL::select('default')->from($this->table())->where('ip', '=', $ip);
        $result = $builder->query();
        
        if($result->is_loaded()) {
            return $result[0]['id'];
        }
        
        return FALSE;
    }
    
    public function addNewService($values) {
        $a = Arr::get($values, 'ServiceIPMaskA', NULL);
        $b = Arr::get($values, 'ServiceIPMaskB', NULL);
        $c = Arr::get($values, 'ServiceIPMaskC', NULL);
        $d = Arr::get($values, 'ServiceIPMaskD', NULL);
        
        if($a != NULL and $b != NULL and $c != NULL and $d != NULL) {
            $this->name = Arr::get($values, 'ServiceName', '');
            $this->type = Arr::get($values, 'ServiceType', 's');
            $this->ip = $a.'.'.$b.'.'.$c.'.'.$d;
            
            $this->save();
        }
    }
    
    public function updateService($id, $values) {
        $this->id = $id;
        $this->load();
        
        if($this->is_loaded()) {
            $ipArray = explode('.', $this->ip);
            $a = Arr::get($values, 'ServiceIPMaskA', $ipArray[0]);
            $b = Arr::get($values, 'ServiceIPMaskB', $ipArray[1]);
            $c = Arr::get($values, 'ServiceIPMaskC', $ipArray[2]);
            $d = Arr::get($values, 'ServiceIPMaskD', $ipArray[3]);
        
            $this->name = Arr::get($values, 'ServiceName', $this->name);
            $this->type = Arr::get($values, 'ServiceType', $this->type);
            $this->ip = $a.'.'.$b.'.'.$c.'.'.$d;
            
            $this->save();
        }
    }
    
    public function getAllServices() {
        $builder = DB_SQL::select('default')->from($this->table());
        $result = $builder->query();
        
        if($result->is_loaded()) {
            $services = array();
            foreach($result as $record) {
                $services[] = DB_ORM::model('remoteService', array((int)$record['id']));
            }
            
            return $services;
        }
        
        return NULL;
    }
}

?>