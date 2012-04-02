<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for map_presentations table in database 
 */
class Model_Leap_Map_Presentation extends DB_ORM_Model {

    public function __construct() {
        parent::__construct();

        $this->fields = array(
            'id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
                'unsigned' => TRUE,
            )),
            
            'title' => new DB_ORM_Field_String($this, array(
                'max_length' => 1000,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            
            'header' => new DB_ORM_Field_String($this, array(
                'max_length' => 3000,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            
            'footer' => new DB_ORM_Field_String($this, array(
                'max_length' => 3000,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            
            'skin_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 10,
                'nullable' => FALSE,
                'unsigned' => TRUE,
            )),
            
            'access' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            
            'login' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            
            'order' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            
            'user_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            
            'start_date' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 20,
                'nullable' => FALSE,
            )),
            
            'end_date' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 20,
                'nullable' => FALSE,
            )),
            
            'tries' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
        );
        
        $this->relations = array(
            'maps' => new DB_ORM_Relation_HasMany($this, array(
                'child_key' => array('presentation_id'),
                'child_model' => 'map_presentation_map',
                'parent_key' => array('id'),
                'options' => array(array('order_by', array('order', 'DESC')),),
            )),
            
            'users' => new DB_ORM_Relation_HasMany($this, array(
                'child_key' => array('presentation_id'),
                'child_model' => 'map_presentation_user',
                'parent_key' => array('id'),
            )),
            
            'author' => new DB_ORM_Relation_BelongsTo($this, array(
                'child_key' => array('user_id'),
                'parent_key' => array('id'),
                'parent_model' => 'user',
            )),
        );
    }

    public static function data_source() {
        return 'default';
    }

    public static function table() {
        return 'map_presentations';
    }

    public static function primary_key() {
        return array('id');
    }
    
    public function getAllPresentations($userId) {
        $builder = DB_SQL::select('default')->from($this->table())->where('user_id', '=', $userId);
        $result = $builder->query();
        
        if($result->is_loaded()) {
            $presentations = array();
            foreach($result as $record) {
                $presentations[] = DB_ORM::model('map_presentation', array((int)$record['id']));
            }
            
            return $presentations;
        }
        
        return NULL;
    }
    
    public function addPresentation($userId, $values) {
        if($userId != NULL) {
            $this->user_id = $userId;
            $this->title = Arr::get($values, 'title', 'anonimus presentation');
            $this->header = Arr::get($values, 'header', '');
            $this->footer = Arr::get($values, 'footer', '');
            $this->access = Arr::get($values, 'access', 1);
            $this->skin_id = Arr::get($values, 'skin', 1);
            $this->tries = Arr::get($values, 'tries', 0);
            
            if(($startDate = mktime(0, 0, 0,Arr::get($values, 'startmonth', ''), Arr::get($values, 'startday', ''), Arr::get($values, 'startyear', ''))) != FALSE) {
                $this->start_date = $startDate;
            }
            
            if(($endDate = mktime(0, 0, 0,Arr::get($values, 'endmonth', ''), Arr::get($values, 'endday', ''), Arr::get($values, 'endyear', ''))) != FALSE) {
                $this->end_date = $endDate;
            }
            
            $this->save();
        }
    }
    
    public function updatePresentation($id, $values) {
        $this->id = $id;
        $this->load();
        
        if($this->is_loaded()) {
            $this->title = Arr::get($values, 'title', $this->title);
            $this->header = Arr::get($values, 'header', $this->header);
            $this->footer = Arr::get($values, 'footer', $this->footer);
            $this->access = Arr::get($values, 'access', $this->access);
            $this->skin_id = Arr::get($values, 'skin', $this->skin_id);
            $this->tries = Arr::get($values, 'tries', $this->tries);
            
            if(($startDate = mktime(0, 0, 0,Arr::get($values, 'startmonth', ''), Arr::get($values, 'startday', ''), Arr::get($values, 'startyear', ''))) != FALSE) {
                $this->start_date = $startDate;
            }

            if(($endDate = mktime(0, 0, 0,Arr::get($values, 'endmonth', ''), Arr::get($values, 'endday', ''), Arr::get($values, 'endyear', ''))) != FALSE) {
                $this->end_date = $endDate;
            }
            
            $this->save();
        }
    }
    
    public function getPresentationsByUserId($userId) {
        if($userId != NULL) {
            $presentations = array();
            $builder = DB_SQL::select('default')
                    ->from($this->table())
                    ->where('user_id', '=', $userId);
            $result = $builder->query();

            if($result->is_loaded()) {
                foreach($result as $record) {
                    $presentations[] = DB_ORM::model('map_presentation', array((int)$record['id']));
                }
            }

            $resultArray = array();
            $resultArray = DB_ORM::model('map_presentation_user')->getAllByUserId($userId);
            
            if(count($resultArray) > 0) {
                foreach($resultArray as $r) {
                    $presentations[] = $r;
                }
            }
            
            $now = time();
            $returnPresentations = array();
            foreach($presentations as $presentation) {
                if($now >= $presentation->start_date and $now <= $presentation->end_date) {
                    $returnPresentations[] = $presentation;
                }
            }
            
            return $returnPresentations;
        }
        
        return NULL;
    }
    
}

?>