<?php
/**
 * Created by JetBrains PhpStorm.
 * User: larjohns
 * Date: 17/10/2012
 * Time: 9:23 πμ
 * To change this template use File | Settings | File Templates.
 */
class Model_Leap_Metadata_ListRecord_Term extends DB_ORM_Model
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
            'value' => new DB_ORM_Field_String($this, array(
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


    }






    public static function data_source() {
        return 'default';
    }

    public static function table() {
        return 'metadata_list_fields_terms';
    }

    public static function primary_key() {
        return array('id');
    }



}
