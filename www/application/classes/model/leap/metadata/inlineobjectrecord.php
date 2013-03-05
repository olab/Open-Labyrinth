<?php
/**
 * Created by JetBrains PhpStorm.
 * User: larjohns
 * Date: 28/9/2012
 * Time: 9:46 πμ
 * To change this template use File | Settings | File Templates.
 */

defined('SYSPATH') or die('No direct script access.');

class Model_Leap_Metadata_InlineObjectRecord extends Model_Leap_Metadata_Record
{
    public function __construct()
    {
        parent::__construct();

        $this->fields = array(
            'id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => TRUE,
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
        );


        $this->relations = array(
            'field' => new DB_ORM_Relation_BelongsTo($this, array(
                'child_key' => array('field_id'),
                'parent_model' => 'metadata',
                'parent_key' => array('id'),

            )),

        );


    }

    public static function data_source()
    {
        return 'default';
    }

    public static function primary_key()
    {
        return array('id');
    }

    public function updateRecord($recId, $value)
    {

        $this->id = $recId;
        $this->load();
        $valueObject = is_array($value) ? $value : json_decode($value, true);

        if ($value != NULL) {
            foreach ($valueObject as $key => $subValue) {
                Model_Leap_Metadata_Record::mergeRecords($this->id, $key, $subValue);
            }
        }


        $this->save();
    }

    public function load(Array $columns = array())
    {

        parent::load($columns);

        $this->relations = array_merge($this->relations,
            Model_Leap_Metadata::getMetadataRelations($this->field->type . "." . $this->field->name, $this));

    }

    public function newRecord($field_id, $objectId, $value = "")
    {

        $this->object_id = $objectId;
        $this->field_id = $field_id;
        $valueObject = is_array($value) ? $value : json_decode($value, true);
        //var_dump($valueObject); die;

        $this->save(TRUE);

        if ($value != NULL) {
            foreach ($valueObject as $key => $subValue) {
                Model_Leap_Metadata_Record::mergeRecords($this->id, $key, $subValue);
            }
        }

        return $this;

    }

    public function getRecord($recId)
    {
        $builder = DB_SQL::select('default')->from($this->table())->where('id', '=', $recId);
        $result = $builder->query();

        if ($result->is_loaded()) {
            $records = array();
            foreach ($result as $record) {
                $records[] = DB_ORM::model('metadata_inlineobjectrecord', array($record['id']));
            }

            return $records;
        }

        return NULL;
    }

    public static function table()
    {
        return 'metadata_inlineobject_fields';
    }

    public function getEditorUI($name)
    {

        $this->prepareViews();
        $html = '      ';


        $metadata = Model_Leap_Metadata::getMetadataByName($name);
        if ($metadata->cardinality == Model_Leap_Metadata::Cardinality_Many) {
            $cardinalityClass = "multi";
        } else {
            $cardinalityClass = "single";
        }

        $inlineMetadata = Model_Leap_Metadata::getMetadataByModelName("inlineobjectrecord." . $name);

        $formFieldName = $name . ($metadata->cardinality === Model_Leap_Metadata::Cardinality_Many ? "[]" : "");

        $html .= "<div class='inline-object $cardinalityClass' id='$formFieldName'>";
        //var_dump($metadata);
        foreach ($inlineMetadata as $inline) {
            //Model_Leap_Metadata_Record::getEditor($inline->name);
            $html .=  '          <div class="control-group" >
                                   <label class="control-label" >'.$inline->label.'</label >
                                     <div class="controls" >
                                     '.Helper_Controller_Metadata::metadataEdit($inline, $this).'
                                        </div >
                                 </div >';



        }


        $html .= "</div>";

        return $html;

    }

    protected static function prepareViews()
    {

        parent::prepareViews();

        Helper_Html_Javascript::add('scripts/jquery/jquery.jstree.js');
        Helper_Html_Javascript::add('scripts/jquery/jquery.hotkeys.js');
        Helper_Html_Javascript::add('scripts/jquery/jquery.cookie.js');

    }

    public function getViewerUI()
    {
        $mappings_count = count($this->field->mappings);

        $uri = Model_Leap_Vocabulary::getObjectUri($this->field->model, $this->object_id);
        $thisUri = Model_Leap_Vocabulary::getObjectUri($this->field->name, $this->id);
        if ($mappings_count > 0) {
            $rdfa = "property='" . $this->field->getMappingsString() . "' about='" . $uri . "' resource='" . $thisUri . "'";
        } else $rdfa = "";


        $html = "<div $rdfa>";
        $name = $this->field->name;

        $inlineMetadata = Model_Leap_Metadata::getMetadataByModelName("inlineobjectrecord." . $name);




        foreach ($inlineMetadata as $inline) {
            //Model_Leap_Metadata_Record::getEditor($inline->name);

            $name = $inline->name;
            if (!$this->is_relation($name)) break;
            $rendered_obj = Helper_Controller_Metadata::metadataView($this->$name);

            $html .= $rendered_obj["label"].": " . $rendered_obj["body"];

        }


        $html .= "</div>";

        return $html;

    }

    public function toString()
    {
        return Model_Leap_Vocabulary::getObjectUri($this->field->name, $this->id);
    }


}
