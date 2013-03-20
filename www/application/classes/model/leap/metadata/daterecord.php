<?php
/**
 * Created by JetBrains PhpStorm.
 * User: larjohns
 * Date: 28/9/2012
 * Time: 9:46 πμ
 * To change this template use File | Settings | File Templates.
 */

defined('SYSPATH') or die('No direct script access.');

class Model_Leap_Metadata_DateRecord extends Model_Leap_Metadata_LiteralRecord
{
    public function __construct() {
        parent::__construct();

        $this->fields['value'] = new DB_ORM_Field_DateTime($this, array(
            'nullable' => FALSE,
            'savable' => TRUE,
        ));

    }


    public function getEditorUI($name){
        $metadata = Model_Leap_Metadata::getMetadataByName($name);

        Helper_Html_Javascript::add('scripts/jquery/jquery-ui-1.9.1.custom.min.js');
        Helper_Html_Javascript::add('scripts/olab/calendarHandler.js');
        if($this->is_loaded()){
            $value = $this->value;
        }
        else {
            $value = "";
        }

        $formFieldName = $name .
            ($metadata->cardinality===Model_Leap_Metadata::Cardinality_Many?"[]":"");
        $html = HTML::style('/scripts/jquery/ui-lightness/jquery-ui-1.9.1.custom.min.css');
        $html .="<input id='".$name."_' name='".$formFieldName."' type='text' class='date' value='$value'/>";

        return $html;
    }

    public static function table() {

        return 'metadata_date_fields';
    }

}
