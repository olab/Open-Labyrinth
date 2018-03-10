<?php
/**
 * Created by PhpStorm.
 * User: larjohns
 * Date: 16/5/2014
 * Time: 1:02 πμ
 */

class Model_Leap_Vocabulary_EntityType  {

    public static function getDefinition(){return array();}
    public static function getSuggestedUris($term){return array();}

    public static function getSuggestion ($term, $type){
        $className = "Model_Leap_Vocabulary_EntityType_".$type;
        $suggestion  = $className::getSuggestedUris($term);
        return $suggestion;
    }

    public static function getConfig(){
        $type_names = Model_Leap_Vocabulary_Vocablet::getAllEntities();

        $dataTypes = self::getDataTypes();

        $valid = date("Y-m-d");
        $config = array(

            "valid"=>$valid,
        );
       // var_dump($type_names);die;
        foreach($type_names as $extension){
            foreach($extension as $type){
                $className = "Model_Leap_Vocabulary_EntityType_".$type["name"];

                $classDef = $className::getDefinition();
                $config = array_merge_recursive($config, $classDef);
            }



        }

        $config["datatypes"] = $dataTypes;
        return $config;
    }

    private static function getDataTypes()
    {
        return
            array (
            'Boolean' =>
                array (
                    'ancestors' =>
                        array (
                            0 => 'DataType',
                        ),
                    'comment' => '',
                    'comment_plain' => '',
                    'id' => 'Boolean',
                    'instances' =>
                        array (
                            0 => 'False',
                            1 => 'True',
                        ),
                    'label' => 'Boolean',
                    'properties' =>
                        array (
                        ),
                    'specific_properties' =>
                        array (
                        ),
                    'subtypes' =>
                        array (
                        ),
                    'supertypes' =>
                        array (
                            0 => 'DataType',
                        ),
                    'url' => 'http://schema.org/Boolean',
                ),
            'DataType' =>
                array (
                    'ancestors' =>
                        array (
                        ),
                    'comment' => '',
                    'comment_plain' => '',
                    'id' => 'DataType',
                    'label' => 'Data Type',
                    'properties' =>
                        array (
                        ),
                    'specific_properties' =>
                        array (
                        ),
                    'subtypes' =>
                        array (
                            0 => 'Boolean',
                            1 => 'Date',
                            2 => 'DateTime',
                            3 => 'Number',
                            4 => 'Text',
                            5 => 'Time',
                        ),
                    'supertypes' =>
                        array (
                        ),
                    'url' => 'http://schema.org/DataType',
                ),
            'Date' =>
                array (
                    'ancestors' =>
                        array (
                            0 => 'DataType',
                        ),
                    'comment' => '',
                    'comment_plain' => '',
                    'id' => 'Date',
                    'label' => 'Date',
                    'properties' =>
                        array (
                        ),
                    'specific_properties' =>
                        array (
                        ),
                    'subtypes' =>
                        array (
                        ),
                    'supertypes' =>
                        array (
                            0 => 'DataType',
                        ),
                    'url' => 'http://schema.org/Date',
                ),
            'DateTime' =>
                array (
                    'ancestors' =>
                        array (
                            0 => 'DataType',
                        ),
                    'comment' => '',
                    'comment_plain' => '',
                    'id' => 'DateTime',
                    'label' => 'Date Time',
                    'properties' =>
                        array (
                        ),
                    'specific_properties' =>
                        array (
                        ),
                    'subtypes' =>
                        array (
                        ),
                    'supertypes' =>
                        array (
                            0 => 'DataType',
                        ),
                    'url' => 'http://schema.org/DateTime',
                ),
            'Float' =>
                array (
                    'ancestors' =>
                        array (
                            0 => 'DataType',
                            1 => 'Number',
                        ),
                    'comment' => '',
                    'comment_plain' => '',
                    'id' => 'Float',
                    'label' => 'Float',
                    'properties' =>
                        array (
                        ),
                    'specific_properties' =>
                        array (
                        ),
                    'subtypes' =>
                        array (
                        ),
                    'supertypes' =>
                        array (
                            0 => 'Number',
                        ),
                    'url' => 'http://schema.org/Float',
                ),
            'Integer' =>
                array (
                    'ancestors' =>
                        array (
                            0 => 'DataType',
                            1 => 'Number',
                        ),
                    'comment' => '',
                    'comment_plain' => '',
                    'id' => 'Integer',
                    'label' => 'Integer',
                    'properties' =>
                        array (
                        ),
                    'specific_properties' =>
                        array (
                        ),
                    'subtypes' =>
                        array (
                        ),
                    'supertypes' =>
                        array (
                            0 => 'Number',
                        ),
                    'url' => 'http://schema.org/Integer',
                ),
            'Number' =>
                array (
                    'ancestors' =>
                        array (
                            0 => 'DataType',
                        ),
                    'comment' => '',
                    'comment_plain' => '',
                    'id' => 'Number',
                    'label' => 'Number',
                    'properties' =>
                        array (
                        ),
                    'specific_properties' =>
                        array (
                        ),
                    'subtypes' =>
                        array (
                            0 => 'Float',
                            1 => 'Integer',
                        ),
                    'supertypes' =>
                        array (
                            0 => 'DataType',
                        ),
                    'url' => 'http://schema.org/Number',
                ),
            'Text' =>
                array (
                    'ancestors' =>
                        array (
                            0 => 'DataType',
                        ),
                    'comment' => '',
                    'comment_plain' => '',
                    'id' => 'Text',
                    'label' => 'Text',
                    'properties' =>
                        array (
                        ),
                    'specific_properties' =>
                        array (
                        ),
                    'subtypes' =>
                        array (
                            0 => 'URL',
                        ),
                    'supertypes' =>
                        array (
                            0 => 'DataType',
                        ),
                    'url' => 'http://schema.org/Text',
                ),
            'Time' =>
                array (
                    'ancestors' =>
                        array (
                            0 => 'DataType',
                        ),
                    'comment' => '',
                    'comment_plain' => '',
                    'id' => 'Time',
                    'label' => 'Time',
                    'properties' =>
                        array (
                        ),
                    'specific_properties' =>
                        array (
                        ),
                    'subtypes' =>
                        array (
                        ),
                    'supertypes' =>
                        array (
                            0 => 'DataType',
                        ),
                    'url' => 'http://schema.org/Time',
                ),
            'URL' =>
                array (
                    'ancestors' =>
                        array (
                            0 => 'DataType',
                            1 => 'Text',
                        ),
                    'comment' => '',
                    'comment_plain' => '',
                    'id' => 'URL',
                    'label' => 'URL',
                    'properties' =>
                        array (
                        ),
                    'specific_properties' =>
                        array (
                        ),
                    'subtypes' =>
                        array (
                        ),
                    'supertypes' =>
                        array (
                            0 => 'Text',
                        ),
                    'url' => 'http://schema.org/URL',
                ),
        );
    }

} 