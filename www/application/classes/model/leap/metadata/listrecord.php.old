<?php
/**
 * Created by JetBrains PhpStorm.
 * User: larjohns
 * Date: 28/9/2012
 * Time: 9:46 πμ
 * To change this template use File | Settings | File Templates.
 */

defined('SYSPATH') or die('No direct script access.');

class Model_Leap_Metadata_ListRecord extends Model_Leap_Metadata_Record
{
    public function __construct() {
        parent::__construct();

        $this->fields = array(
            'id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
                'unsigned' => TRUE,
            )),
            'field_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            'object_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            'term_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),

        );

     $this->relations = array(
         'field' => new DB_ORM_Relation_BelongsTo($this, array(
             'child_key' => array('field_id'),
             'parent_model' => 'metadata',
             'parent_key' => array('id'),

         )),

         'term' => new DB_ORM_Relation_BelongsTo($this, array(
             'child_key' => array('term_id'),
             'parent_model' => 'metadata_listrecord_term',
             'parent_key' => array('id'),

         )),


         );

    }

    public function getPossibleTerms($metadata){

        $builder = DB_SQL::select('default')
            ->from(Model_Leap_Metadata_ListRecord_Term::table())
            ->where('field_id', '=', $metadata->id);



        $result = $builder->query();

        if($result->is_loaded()) {
            $terms = array();

            foreach($result as $record) {
                $terms[] = DB_ORM::model('metadata_listrecord_term', array((int)$record['id']));
            }

            return $terms;
        }

        return NULL;
    }


    public static function data_source() {
        return 'default';
    }

    public static function table() {
        return 'metadata_list_fields';
    }

    public static function primary_key() {
        return array('id');
    }


    public function getAllValues(){

    }


    public function updateRecord($recId, $value){
        $this->id = $recId;
        $this->load();

                if($value != NULL) {
                    $this->term_id = $value;
                }

                $this->save();
    }


    public function newRecord($field_id,$objectId, $value=""){
        $this->object_id = $objectId;
        $this->field_id = $field_id;
        $this->term_id = $value;
        $this->save();

        return $this;

    }


    public function getRecord($recId) {
        $builder = DB_SQL::select('default')->from($this->table())->where('id', '=', $recId);
        $result = $builder->query();

        if($result->is_loaded()) {
            $records = array();
            foreach($result as $record) {
                $records[] = DB_ORM::model('metadata_listrecord', array($record['id']));
            }

            return $records;
        }

        return NULL;
    }


    public  function getEditorUI($name){

        if($this->is_loaded()){
            $value = $this->term->id;
        }
        else {
            $value = "";
        }


        $metadata = Model_Leap_Metadata::getMetadataByName($name);

        $terms = $this->getPossibleTerms($metadata);

        $formFieldName = $name . ($metadata->cardinality===Model_Leap_Metadata::Cardinality_Many?"[]":"");
        $html ="<select id='".$name."_' name='".$formFieldName."' id='$name'>";

        foreach($terms as $term){
            $termId = $term->id;
            $termValue = $term->value;
            $selected = $term->id ==$value?"selected":"";
            $html .= "<option $selected value='$termId'>$termValue</option>";
        }


        $html .="</select>";

        return $html;

    }


    public  function getViewerUI(){

        if($this->is_loaded()){
            $value = $this->term->value;
        }
        else {
            $value = "";
        }
        $mappings_count = count($this->field->mappings);
        $uri = Model_Leap_Vocabulary::getObjectUri($this->field->model, $this->object_id);

        if($mappings_count>0){
            $rdfa = "property='".$this->field->getMappingsString()."' about='".$uri."'";
        }
        else $rdfa ="";

        $html ="<div $rdfa>$value</div>";

        return $html;

    }
    public function dataType()
    {
        return "string";
    }
    public function toString(){
        return $this->term->value;
    }

}
