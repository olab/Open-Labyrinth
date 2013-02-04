<?php
/**
 * Created by JetBrains PhpStorm.
 * User: larjohns
 * Date: 28/9/2012
 * Time: 9:46 πμ
 * To change this template use File | Settings | File Templates.
 */

defined('SYSPATH') or die('No direct script access.');

class Model_Leap_Metadata_TextRecord extends Model_Leap_Metadata_LiteralRecord
{
    public function __construct() {
        parent::__construct();
       $this->fields['value'] = new DB_ORM_Field_Text($this, array(
            'nullable' => TRUE,
            'savable' => TRUE,
        ));


    }
    protected static function prepareViews()
    {

        parent::prepareViews();




    }


    public static function table() {

        return 'metadata_text_fields';
    }


    public  function getEditorUI($name){
        $metadata = Model_Leap_Metadata::getMetadataByName($name);
        $this->prepareViews();
        if($this->is_loaded()){
            $value = $this->value;
        }
        else {
            $value = "";
        }

        $formFieldName = $name .
            ($metadata->cardinality===Model_Leap_Metadata::Cardinality_Many?"[]":"");
        $html ="<textarea class='textarea' name='".$formFieldName."'/>$value</textarea>";

        return $html;
    }

}

