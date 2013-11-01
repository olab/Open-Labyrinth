<?php
/**
 * Created by JetBrains PhpStorm.
 * User: larjohns
 * Date: 1/10/2012
 * Time: 11:14 πμ
 * To change this template use File | Settings | File Templates.
 */
defined('SYSPATH') or die('No direct script access.');
abstract class Model_Leap_Metadata_Record extends DB_ORM_Model
{
    public static function updateMetadata($type, $objectId, $values)
    {

        $metadata = Model_Leap_Metadata::getMetadataByModelName($type);

        foreach ($metadata as $property) {

            if (isset($values[$property->name]))
                $metadataValues = $values[$property->name];
            else
                $metadataValues = array();

            DB_ORM::model($property->getModelname())->mergeRecords($objectId, $property->name, $metadataValues);
        }
    }

    protected static function prepareViews()
    {
        Helper_Html_Javascript::add("scripts/crypto-js/rollups/md5.js");
        Helper_Html_Javascript::add("scripts/jquery/jquery-1.8.2.js");
       // Helper_Html_Javascript::add('scripts/olab/inputHandler.js');
    }

    public static function mergeRecords($objectId, $metadataName, $values)
    {
        //if (empty($values)) return;
        $metadata = Model_Leap_Metadata::getMetadataByName($metadataName);
        $builder = DB_SQL::select('default')
            ->from(DB_ORM::model($metadata->getModelname())->table())
            ->where('object_id', '=', $objectId)
            ->where('field_id', '=', $metadata->id);

        $result = $builder->query();
        $records = array();

        foreach ($result as $record) {
            $records[] = DB_ORM::model($metadata->getModelname(), array($record['id']));
        }

        $recCount = count($records);

        if ($metadata->cardinality === Model_Leap_Metadata::Cardinality_One) {
            if ($recCount > 0)
                $records[0]->updateRecord($records[0]->id, $values);
            else {
                if(count($values)>0){
                    $rec = DB_ORM_Model::factory($metadata->getModelname());
                    $rec->newRecord($metadata->id, $objectId, $values);
                }
            }
            return;
        }

        $valCount = count($values);

        if ($recCount < $valCount) {
            for ($i = 0; $i < $recCount; $i++) {

                $records[$i]->updateRecord($records[$i]->id, $values[$i]);
            }

            for ($i = $recCount; $i < $valCount; $i++) {
                $rec = DB_ORM_Model::factory($metadata->getModelname());
                $rec->newRecord($metadata->id, $objectId, $values[$i]);
                $records[] = $rec;
            }
        } else {
            for ($i = 0; $i < $valCount; $i++) {
                $records[$i]->updateRecord($records[$i]->id, $values[$i]);
            }

            for ($i = $valCount; $i < $recCount; $i++) {
                $records[$i]->delete();
            }
        }
    }

    public abstract function updateRecord($recId, $values);


    public static function createRecord($objectId, $metadataId)
    {
        $metadata = Model_Leap_Metadata::getMetadataByName($metadataId);
        $field_id = $metadata->id;
        DB_ORM::model($metadata->getModelname())->newRecord($field_id, $objectId);
    }

    public function removeRecord($recordId, $metadataId)
    {
        $metadata = Model_Leap_Metadata::getMetadataByName($metadataId);
        $rec = DB_ORM_Model::factory($metadata->getModelname());
        $rec->load(array("id" => $recordId));
        $rec->delete();
    }

    public  static function getRecordsOfMetadata($metadataName){

        $metadata = Model_Leap_Metadata::getMetadataByName($metadataName);
        $builder = DB_SQL::select('default')
            ->from(DB_ORM::model($metadata->getModelname())->table())
            ->where('field_id', '=', $metadata->id);

        $result = $builder->query();
        if ($result->is_loaded()) {
            $metadataRecords = array();

            foreach ($result as $record) {
                $metadataRecords[] = DB_ORM::model($metadata->getModelname(), array((int)$record['id']));
            }

            return $metadataRecords;
        }
        else return array();

    }


    public static function getMultiEditor($name, $values = array())
    {
        $metadata = Model_Leap_Metadata::getMetadataByName($name);


        if (DB_ORM::model($metadata->getModelname())->handlesCardinality()) {
            return DB_ORM::model($metadata->getModelname())->getMultiEditorUI($name, $values);
        }
        return false;
    }

    public static function getEditor($name, $id = 0)
    {
        $metadata = Model_Leap_Metadata::getMetadataByName($name);

        $rec = DB_ORM_Model::factory($metadata->getModelname());

        if ($id != 0) {
            $rec = DB_ORM::model($metadata->getModelname(), array($id));
        }

        return $rec->getEditorUI($name);
    }

    public static function getViewer($name, $id = 0)
    {
        $metadata = Model_Leap_Metadata::getMetadataByName($name);

        $rec = DB_ORM_Model::factory($metadata->getModelname());

        if ($id != 0) {
            $rec = DB_ORM::model($metadata->getModelname(), array($id));
        }

        return $rec->getViewerUI();
    }

    public static function getAllTriples($name, $offset=0, $limit=0)
    {
        $metadata = Model_Leap_Metadata::getMetadataByName($name);

        $id = $metadata->id;

        $builder = DB_SQL::select('default')
            ->from(DB_ORM::model($metadata->getModelname())->table())
            ->where('field_id', '=', $id);



        $result = $builder->query();

        if ($result->is_loaded()) {
            $count = $result->count();

            if ($limit == 0) {
                $limit = $count;
            }
            $triples = array();




                $builder2 = DB_SQL::select('default')
                    ->from(DB_ORM::model($metadata->getModelname())->table())
                    ->limit($limit)
                    ->offset($offset)
                    ->where('field_id', '=', $id);

                $result2 = $builder2->query();

                if ($result2->is_loaded()) {
                    $batchTriples = array();
                    foreach ($result2 as $record) {
                        $property = DB_ORM::model($metadata->getModelname(), array($record['id']));

                        $propertyTriples = $property->getTriples();
                        $batchTriples = array_merge($batchTriples, $propertyTriples);
                    }
                    $triples =  $batchTriples;
                }


            return $triples;
        } else return array();

    }

    public abstract function toString();
    public abstract function dataType();

    public function getTriples()
    {
        $mappings = $this->field->mappings;
        $uri = Model_Leap_Vocabulary::getObjectUri($this->field->model, $this->object_id);
        $value = $this->toString();
        $triples = array();

        foreach ($mappings as $mapping) {

            $triple_data = array('s' => $uri, 'p' => $mapping->term->getFullRepresentation(), 'o' => $value, 'type' => $mapping->type, 'data_type'=>$this->dataType());
            $triple = DB_ORM::model('vocabulary_triple');
            $triple->load($triple_data);
            $triples[] = $triple;
        }

        return $triples;
    }


    /**
     * True if the deriving class wants to handle its controls for
     * many values itself. For instance it may draw a tree of
     * options instead of many single checkboxes
     * @return bool
     */
    public function  handlesCardinality()
    {
        return false;
    }
}
