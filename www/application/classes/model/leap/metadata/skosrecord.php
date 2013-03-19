<?php
/**
 * Created by JetBrains PhpStorm.
 * User: larjohns
 * Date: 28/9/2012
 * Time: 9:46 πμ
 * To change this template use File | Settings | File Templates.
 */

defined('SYSPATH') or die('No direct script access.');

class Model_Leap_Metadata_SkosRecord extends Model_Leap_Metadata_Record
{
    public function __construct()
    {
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
            'uri' => new DB_ORM_Field_String($this, array(
                'max_length' => 2000,
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

        );

        $this->adaptors = array(
            'term' => new Helper_DB_ORM_Field_Adaptor_Skos($this, array(
                'field' => 'uri',
            )),

        );
    }

    public function getPossibleTerms($metadata)
    {
        return Helper_Model_SkosTerm::getAllTerms($metadata->extras["source"]);
    }


    public static function data_source()
    {
        return 'default';
    }

    public static function table()
    {
        return 'metadata_skos_fields';
    }

    public static function primary_key()
    {
        return array('id');
    }


    public function updateRecord($recId, $value)
    {
        $this->id = $recId;
        $this->load();

        if ($value != NULL) {
            $this->uri = $value;
        }

        $this->save();
    }


    public function newRecord($field_id, $objectId, $value = "")
    {
        $this->object_id = $objectId;
        $this->field_id = $field_id;
        $this->uri = $value;
        $this->save();

        return $this;

    }


    public function getRecord($recId)
    {
        $builder = DB_SQL::select('default')->from($this->table())->where('id', '=', $recId);
        $result = $builder->query();

        if ($result->is_loaded()) {
            $records = array();
            foreach ($result as $record) {
                $records[] = DB_ORM::model('metadata_skosrecord', array($record['id']));
            }

            return $records;
        }

        return NULL;
    }

    public static function getMultiEditorUI($name, $values = array())
    {

        $metadata = Model_Leap_Metadata::getMetadataByName($name);

        $source = $metadata->extras['source'];


        $html = self::prepareViews();

        $html .= Helper_Model_SkosTerm::getAllTermsTree($source, $metadata->name, $metadata->cardinality);


        if (isset($values[0])) $values_count = count($values);
        else $values_count = 0;
        for ($i = 0; $i < $values_count; $i++) {

            $id = $values[$i]->id;

            $html .= Model_Leap_Metadata_Record::getEditor($name, $id);
        }

        return $html;

    }

    public function getEditorUI($name)
    {

        $this->prepareViews();
        $html = '      ';

        if ($this->is_loaded()) {
            $value = $this->uri;
        } else {
            $value = "";
        }


        $metadata = Model_Leap_Metadata::getMetadataByName($name);

        $formFieldName = $name . ($metadata->cardinality === Model_Leap_Metadata::Cardinality_Many ? "[]" : "");

        $html .= "<input id='".$name."_'   class='$name' type='hidden' value='$value'/>";


        return $html;

    }

    protected static function prepareViews()
    {

        parent::prepareViews();

        Helper_Html_Javascript::add('scripts/jquery/jquery.jstree.js');
        Helper_Html_Javascript::add('scripts/jquery/jquery.hotkeys.js');
        Helper_Html_Javascript::add('scripts/jquery/jquery.cookie.js');
        Helper_Html_Javascript::add('scripts/olab/treeHandler.js');

    }


    public function getViewerUI()
    {

        if ($this->is_loaded()) {
            $value = $this->term->label();
        } else {
            $value = "";
        }
        $mappings_count = count($this->field->mappings);

        $uri = Model_Leap_Vocabulary::getObjectUri($this->field->model, $this->object_id);

        if ($mappings_count > 0) {
            $rdfa = "property='" . $this->field->getMappingsString() . "' about='" . $uri . "'";
        } else $rdfa = "";
        $termUri = $this->uri;
        $html = "<div ><a $rdfa href='$termUri'>$value</a></div>";

        return $html;

    }


    public function toString(){
        return $this->uri;
    }

    public function handlesCardinality()
    {
        return true;
    }


}
