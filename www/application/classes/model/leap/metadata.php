<?php
/**
 * Created by JetBrains PhpStorm.
 * User: larjohns
 * Date: 27/9/2012
 * Time: 12:04 μμ
 * To change this template use File | Settings | File Templates.
 */

defined('SYSPATH') or die('No direct script access.');

class Model_Leap_Metadata extends DB_ORM_Model
{
    const Cardinality_Many = "n";
    const Cardinality_One = "1";

    static $Models = array(
        "user"=>"user",
        "map"=>"map",
        "map_node_link"=>"link",
        "map_node"=>"node",
        "user_session"=>"user session",
        "user_sessiontrace"=>"user session trace"
    );


    static $MetadataExtras = array(
        "referencerecord" => array("source", "type", "label"),
        "skosrecord" => array("source"),
        "stringrecord"=>array(),
        "daterecord" => array(),
        "textrecord" =>array(),
        "inlineobjectrecord" =>array(),
    );



    public function __construct()
    {
        parent::__construct();

        $this->fields = array(
            'id' => new DB_ORM_Field_Integer($this, array(
                    'max_length' => 11,
                    'nullable' => FALSE,
                    'unsigned' => TRUE,
                )),
            'name' => new DB_ORM_Field_String($this, array(
                    'max_length' => 200,
                    'nullable' => FALSE,
                    'savable' => TRUE,
                )),
            'model' => new DB_ORM_Field_String($this, array(
                    'max_length' => 11,
                    'nullable' => FALSE,
                )),
            'type' => new DB_ORM_Field_String($this, array(
                    'max_length' => 2000,
                    'nullable' => FALSE,
                    'savable' => TRUE,
                )),
            'options' => new DB_ORM_Field_String($this, array(
                    'max_length' => 11,
                    'nullable' => FALSE,
                )),
            'cardinality' => new DB_ORM_Field_String($this, array(
                    'max_length' => 11,
                    'nullable' => FALSE,
                )),
            'label' => new DB_ORM_Field_String($this, array(
                    'max_length' => 500,
                    'nullable' => FALSE,
                    'savable' => TRUE,
                )),
            'comment' => new DB_ORM_Field_String($this, array(
                    'max_length' => 500,
                    'nullable' => FALSE,
                    'savable' => TRUE,
                )),
        );

        $this->relations = array(
            'mappings' => new DB_ORM_Relation_HasMany($this, array(
                    'child_key' => array('metadata_id'),
                    'child_model' => 'vocabulary_mapping',
                    'parent_key' => array('id'),
                )),
        );

        $this->adaptors = array(
            'extras' => new DB_ORM_Field_Adaptor_JSON($this, array(
                    'field' => 'options',
                )),
        );
    }

    public static function data_source()
    {
        return 'default';
    }

    public static function table()
    {
        return 'metadata';
    }

    public static function primary_key()
    {
        return array('id');
    }

    public static function getMetadataRelationsMetadata($model, $object){
        $metadataFields = self::getMetadataByModelName($model);
        $relationsMetadata = array();

        foreach ($metadataFields as $metadata) {
            $relationsMetadata[$metadata->name] = $metadata->toRelationMetadata($object);
        }

        return $relationsMetadata;

    }

    public static function getMetadataRelations($type, $object)
    {
        $relationsMetadata = self::getMetadataRelationsMetadata($type,$object);

        $relations = array();
        foreach($relationsMetadata as $key=>$relMetadata){
            $relations[$key] = new DB_ORM_Relation_HasMany($object, $relMetadata);
        }
        return $relations;

    }


    public static function getMetadataByModelName($model="")
    {
        if($model==="")
            $builder = DB_SQL::select('default')->from(self::table());
        else
            $builder = DB_SQL::select('default')->from(self::table())->where('model', '=', $model);
        $result = $builder->query();

        if ($result->is_loaded()) {
            $metadataFields = array();

            foreach ($result as $record) {
                $metadataFields[] = DB_ORM::model('metadata', array((int)$record['id']));
            }

            return $metadataFields;
        }
        return array();
    }
    public static function getMetadataByName($name)
    {
        $builder = DB_SQL::select('default')->from(self::table())->where('name', '=', $name);
        $result = $builder->query();

        if ($result->is_loaded()) {
            $metadataFields = array();

            foreach ($result as $record) {
                $metadataFields[] = DB_ORM::model('metadata', array((int)$record['id']));
            }

            return $metadataFields[0];
        }
        return NULL;
    }

    public static function getMetadataByType($type=""){
        if($type==="")
            $builder = DB_SQL::select('default')->from(self::table());
        else
            $builder = DB_SQL::select('default')->from(self::table())->where('type', '=', $type);
        $result = $builder->query();

        if ($result->is_loaded()) {
            $metadataFields = array();

            foreach ($result as $record) {
                $metadataFields[] = DB_ORM::model('metadata', array((int)$record['id']));
            }

            return $metadataFields;
        }
        return array();
    }

    public function delete($reset = FALSE){

        $records = Model_Leap_Metadata_Record::getRecordsOfMetadata($this->name);

        foreach ($records as $record) {
            $record->delete();
        }


        foreach ($this->mappings as $mapping){
            $mapping->delete();
        }


        parent::delete($reset);


    }


    protected function toRelationMetadata($object)
    {
        $childModel = 'metadata_' . $this->type;

        $table = DB_ORM::model($childModel)->table();

        $childKey = $table . "." . "field_id";

        $relation = array(
            'child_key' => array('object_id'),
            'child_model' => $childModel,
            'parent_key' => $object->primary_key(),
            'options' => array(
                array('where', array($childKey, '=', $this->id)),
            ),
        );

        if ($this->cardinality === Model_Leap_Metadata::Cardinality_One)
            $relation['options'][0]['limit'] = array(1);

        return $relation;

    }

    public function getModelName(){
        return "metadata_" . $this->type;
    }


    public function getMappingsString(){
        $predicates = array();

        foreach($this->mappings as $mapping){
            $predicates[]=$mapping->term->getFullRepresentation();
        }
        return implode(" ", $predicates);
    }

    public static function associateModel($model, $object){

        $relations = Model_Leap_Metadata::getMetadataRelationsMetadata($model, $object);
        foreach($relations as $name=>$relation){
            echo 'lol';
            $object->relate($name,"has_many",$relation);
        }
    }



}
