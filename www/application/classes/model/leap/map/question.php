<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for map_questions table in database 
 */
class Model_Leap_Map_Question extends DB_ORM_Model {

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
            
            'stem' => new DB_ORM_Field_String($this, array(
                'max_length' => 500,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            
            'entry_type_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            
            'width' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            
            'height' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            
            'feedback' => new DB_ORM_Field_String($this, array(
                'max_length' => 1000,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            
            'show_answer' => new DB_ORM_Field_Boolean($this, array(
                'default' => TRUE,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            
            'counter_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => TRUE,
            )),
            
            'num_tries' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
        );
        
        $this->relations = array(
            'counter' => new DB_ORM_Relation_BelongsTo($this, array(
                'child_key' => array('counter_id'),
                'parent_key' => array('id'),
                'parent_model' => 'map_counter',
            )),
            
            'map' => new DB_ORM_Relation_BelongsTo($this, array(
                'child_key' => array('map_id'),
                'parent_key' => array('id'),
                'parent_model' => 'map',
            )),
            
            'type' => new DB_ORM_Relation_BelongsTo($this, array(
                'child_key' => array('entry_type_id'),
                'parent_key' => array('id'),
                'parent_model' => 'map_question_type',
            )),
            
            'responses' => new DB_ORM_Relation_HasMany($this, array(
                'child_key' => array('question_id'),
                'child_model' => 'map_question_response',
                'parent_key' => array('id'),
            )),
        );
    }

    public static function data_source() {
        return 'default';
    }

    public static function table() {
        return 'map_questions';
    }

    public static function primary_key() {
        return array('id');
    }
    
    
    public function getQuestionsByMap($mapId) {
        $builder = DB_SQL::select('default')
                ->from($this->table())
                ->where('map_id', '=', $mapId);
        
        $result = $builder->query();
        
        if($result->is_loaded()) {
            $questions = array();
            foreach($result as $record) {
                $questions[] = DB_ORM::model('map_question', array((int)$record['id']));
            }
            
            return $questions;
        }
        
        return NULL;
    }
    
    public function addQuestion($mapId, $type, $values) {
        switch($type->value)
        {
            case "text":
                $this->saveTextQuestion($mapId, $type, $values);
                break;
            case "area":
                $this->saveAreaQuestion($mapId, $type, $values);
                break;
            default:
                $this->saveResponceQuestion($mapId, $type, $values);
                break;
        }
    }
    
    public function updateQuestion($questionId, $type, $values) {
        $this->id = $questionId;
        $this->load();
        
        switch($type->value)
        {
            case "text":
                $this->updateTextQuestion($values);
                break;
            case "area":
                $this->updateAreaQuestion($values);
                break;
            default:
                $this->updateResponseQuestion($values);
                break;
        }
    }
    
    private function updateTextQuestion($values) {
        $this->stem = Arr::get($values, 'qstem', $this->stem);
        $this->width = Arr::get($values, 'qwidth', $this->width);
        $this->feedback = Arr::get($values, 'fback', $this->feedback);
        
        $this->save();
    }
    
    private function updateAreaQuestion($values) {
        $this->stem = Arr::get($values, 'qstem', $this->stem);
        $this->width = Arr::get($values, 'qwidth', $this->width);
        $this->height = Arr::get($values, 'qheight', $this->height);
        $this->feedback = Arr::get($values, 'fback', $this->feedback);
        
        $this->save();
    }
    
    private function updateResponseQuestion($values) {
        $this->stem = Arr::get($values, 'qstem', $this->stem);
        $this->feedback = Arr::get($values, 'fback', $this->feedback);
        $this->show_answer = Arr::get($values, 'qshow', $this->show_answer);
        $this->counter_id = Arr::get($values, 'scount', $this->counter_id);
        $this->num_tries = Arr::get($values, 'numtries', $this->num_tries);
        
        $this->save();
        
        DB_ORM::model('map_question_response')->updateResponses($this->id, $values);
    }
    
    private function saveTextQuestion($mapId, $type, $values) {
        $this->map_id = $mapId;
        $this->entry_type_id = $type->id;
        $this->stem = Arr::get($values, 'qstem', '');
        $this->width = Arr::get($values, 'qwidth', 0);
        $this->feedback = Arr::get($values, 'fback', '');
        
        $this->save();
    }
    
    private function saveAreaQuestion($mapId, $type, $values) {
        $this->map_id = $mapId;
        $this->entry_type_id = $type->id;
        $this->stem = Arr::get($values, 'qstem', '');
        $this->width = Arr::get($values, 'qwidth', 0);
        $this->height = Arr::get($values, 'qheight', 0);
        $this->feedback = Arr::get($values, 'fback', '');
        
        $this->save();
    }
    
    private function saveResponceQuestion($mapId, $type, $values) {
        $builder = DB_ORM::insert('map_question')
                ->column('map_id', $mapId)
                ->column('entry_type_id', $type->id)
                ->column('stem', Arr::get($values, 'qstem', ''))
                ->column('feedback', Arr::get($values, 'fback', ''))
                ->column('show_answer', Arr::get($values, 'qshow', 1))
                ->column('counter_id', Arr::get($values, 'scount', 0))
                ->column('num_tries',  Arr::get($values, 'numtries', -1));
        $newQuestionId = $builder->execute();
        
        $respCount = (int)$type->template_args;
        for($i = 1; $i <= $respCount; $i++) {
            $responce = DB_ORM::model('map_question_response');
            $responce->question_id = $newQuestionId;
            $responce->response = Arr::get($values, 'qresp'.$i.'t', '');
            $responce->feedback = Arr::get($values, 'qfeed'.$i, '');
            $responce->is_correct = Arr::get($values, 'qresp'.$i.'y', 0);
            $responce->score = Arr::get($values, 'qresp'.$i.'s', 0);
            
            $responce->save();
        }
    }
}

?>