<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for map_counters table in database 
 */
class Model_Leap_Map_Counter extends DB_ORM_Model {

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
            
            'name' => new DB_ORM_Field_String($this, array(
                'max_length' => 200,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            
            'description' => new DB_ORM_Field_Text($this, array(
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            
            'start_value' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            
            'icon_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            
            'prefix' => new DB_ORM_Field_String($this, array(
                'max_length' => 20,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            
            'suffix' => new DB_ORM_Field_String($this, array(
                'max_length' => 20,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            
            'visible' => new DB_ORM_Field_Boolean($this, array(
                'default' => FALSE,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            
            'out_of' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
        );
        
        $this->relations = array(
            'map' => new DB_ORM_Relation_BelongsTo($this, array(
                'child_key' => array('map_id'),
                'parent_key' => array('id'),
                'parent_model' => 'map',
            )),
            
            'icon' => new DB_ORM_Relation_BelongsTo($this, array(
                'child_key' => array('icon_id'),
                'parent_key' => array('id'),
                'parent_model' => 'map_element',
            )),
        );
    }

    public static function data_source() {
        return 'default';
    }

    public static function table() {
        return 'map_counters';
    }

    public static function primary_key() {
        return array('id');
    }
    
    public function getCountersByMap($mapId) {
        $builder = DB_SQL::select('default')->from($this->table())->where('map_id', '=', $mapId);
        $result = $builder->query();
        
        if($result->is_loaded()) {
            $counters = array();
            foreach($result as $record) {
                $counters[] = DB_ORM::model('map_counter', array((int)$record['id']));
            }
            
            return $counters;
        }
        
        return NULL;
    }
    
    public function addCounter($mapId, $values) {
        $this->map_id = $mapId;
        $this->name = Arr::get($values, 'cName', '');
        $this->description = Arr::get($values, 'cDesc', '');
        $this->icon_id = Arr::get($values, 'cIconId', NULL);
        $this->start_value = Arr::get($values, 'cStartV', 0);
        $this->visible = Arr::get($values, 'cVisible', FALSE);
        
        $this->save();
    }
    
    public function updateCounter($counterId, $values) {
        $this->id = $counterId;
        $this->load();
        
        if($this) {
            $this->name = Arr::get($values, 'cName', $this->name);
            $this->description = Arr::get($values, 'cDesc', $this->description);
            $this->icon_id = Arr::get($values, 'cIconId', $this->icon_id);
            $this->start_value = Arr::get($values, 'cStartV', $this->start_value);
            $this->visible = Arr::get($values, 'cVisible', $this->visible);

            $this->save();
        }
    }

}

?>