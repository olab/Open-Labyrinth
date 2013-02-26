<?php
/**
 * Created by JetBrains PhpStorm.
 * User: larjohns
 * Date: 14/10/2012
 * Time: 12:35 πμ
 * To change this template use File | Settings | File Templates.
 */
class Model_Leap_Vocabulary_Mapping extends DB_ORM_Model
{
    public function __construct() {
        parent::__construct();

        $this->fields = array(
            'id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
                'unsigned' => TRUE,
            )),
            'type' => new DB_ORM_Field_String($this, array(
                'max_length' => 500,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            'metadata_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            'term_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
        );

        $this->relations = array(
            'term' => new DB_ORM_Relation_BelongsTo($this, array(
                'child_key' => array('term_id'),
                'parent_key' => array('id'),
                'parent_model' => 'vocabulary_term',
            )),
            'metadata' => new DB_ORM_Relation_BelongsTo($this, array(
                'child_key' => array('metadata_id'),
                'parent_key' => array('id'),
                'parent_model' => 'metadata',
            )),
        );
    }

    public static function data_source() {
        return 'default';
    }

    public static function table() {
        return 'rdf_mappings';
    }

    public static function primary_key() {
        return array('id');
    }

    public static function getAll(){

         $builder = DB_SQL::select('default')->from(self::table());

         $result = $builder->query();

        if ($result->is_loaded()) {
            $mappings = array();

            foreach ($result as $record) {
                $mappings[] = DB_ORM::model('vocabulary_mapping', array((int)$record['id']));
            }

            return $mappings;
        }
        return array();
    }


}
