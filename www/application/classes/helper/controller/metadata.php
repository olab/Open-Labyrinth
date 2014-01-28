<?php
/**
 * Created by JetBrains PhpStorm.
 * User: larjohns
 * Date: 2/10/2012
 * Time: 11:47 πμ
 * To change this template use File | Settings | File Templates.
 */

defined('SYSPATH') or die('No direct script access.');
class Helper_Controller_Metadata
{

    public static function getView($metadataRecord)
    {
        return (self::metadataView($metadataRecord));
    }


    public static function isMetadataRecord($field)
    {
        if ($field instanceof Traversable && $field[0] instanceof Model_Leap_Metadata_Record) return true;
        else return false;

    }

    public static function displayEditor($object, $modelName)
    {
        $html = "<fieldset class='fieldset metadata-container'><legend>Metadata</legend>";

        $metadata = Model_Leap_Metadata::getMetadataByModelName($modelName);

        if(count($metadata)<1){
            $html = "";
            return $html;
        }
       //Helper_Html_Javascript::add('scripts/tinymce/jscripts/tiny_mce/tiny_mce.js');
       // Helper_Html_Javascript::add('scripts/jquery/jquery-ui-1.9.1.custom.min.js');
        foreach ($metadata as $property) {
            $name = $property->name;
            $comment = $property->comment;
            $metadataEditor = self::metadataEdit($property, $object);
            $html .="
            <div  class='control-group' id='$name'>
            <div class='control-label'>
            <label data-toggle='tooltip' title='".$comment."' for='".$name."_' class='control-label'>".$property->label.
                "<div class='pull-right'></div></label>".$metadataEditor["controls"]."</div>";

            $html .= $metadataEditor["form"];

            $html .="</div>";

        }

        $html .='</fieldset>';


        Helper_Html_Javascript::add('scripts/tinymce/js/tinymce/jquery.tinymce.min.js');
        Helper_Html_Javascript::add('scripts/olab/inputHandler.js');
        Helper_Html_Javascript::add('scripts/olab/calendarHandler.js');
        Helper_Html_Javascript::render(true);


        return $html;
    }


    public  static function metadataView($metadataRecord)
    {

        $result =array();


        if(!$metadataRecord[0]->is_loaded())return array("label"=>"", "body"=>"");
        $label = $metadataRecord[0]->field->name;

        $name = $metadataRecord[0]->field->name;
        $result["label"] = $label;
        $valuesCount = count($metadataRecord);
        $html = "";
        if ($valuesCount < 2) {


            $id = $metadataRecord[0]->id;

            $html .= "<div>";
            $html .= Model_Leap_Metadata_Record::getViewer($name, $id);
            $html .= "</div>";

        } else {



            for ($i = 0; $i < $valuesCount; $i++) {

                $id = $metadataRecord[$i]->id;

                $html .= "<li><div>";
                $html .= Model_Leap_Metadata_Record::getViewer($name, $id);
                $html .= "</div></li>";
            }
            $html .= "</ul></div>";

        }
        $result["body"] =$html;
        return $result;
    }

    public static function metadataEdit($metadata, $object)
    {

        $html = "";
        $controls = "";
        $cardinality = $metadata->cardinality;
        $handlesCardinality = DB_ORM::model($metadata->getModelName())->handlesCardinality();

        $label = $metadata->label;
        $name = $metadata->name;
        $type = $metadata->type;
        $comment = $metadata->comment;

        if ($cardinality === Model_Leap_Metadata::Cardinality_One) {

            $values = array();
            if( $object->is_relation($name)|| $object->is_field($name)|| $object->is_adaptor($name))
                $values = $object->$name;


            if (isset($values[0])) {
                $id = $values[0]->id;
            } else {
                $id = 0;
            }
            $html .= "<div id='$name' class=' controls $type single' >";
            if($handlesCardinality){
                $multi = Model_Leap_Metadata_Record::getMultiEditor($name, $values);
                $html .= $multi["html"] ;
                $controls = $multi["controls"];
            }
            else{
                $html .= Model_Leap_Metadata_Record::getEditor($name, $id);
            }



            $html .= "</div>";
        } else {

            $values = array();
            if( $object->is_relation($name)|| $object->is_field($name)|| $object->is_adaptor($name))
                $values = $object->$name;


            if (isset($values[0])) $values_count = count($values);
            else $values_count = 0;


            $html .= "<div class='controls $type multi' >";
            if (!$handlesCardinality) {


                for ($i = 0; $i < $values_count; $i++) {

                    $id = $values[$i]->id;

                    $html .= "<div><div class='input-append span9'>";
                    $html .= Model_Leap_Metadata_Record::getEditor($name, $id);
                    $html .= "<a class='remove btn btn-danger'><i class='icon-remove'></i>Remove</a></div></div>";

                }
                $controls .= "<a class='add btn btn-info'><i class='icon-plus'></i>Add</a>";
            } else {
                $multi = Model_Leap_Metadata_Record::getMultiEditor($name, $values);
                $html .= $multi["html"] ;
                $controls = $multi["controls"];

            }
            $html .= "</div>";
        }
        $output = array("form"=>$html, "controls" =>$controls);
        return  $output;
    }

}
