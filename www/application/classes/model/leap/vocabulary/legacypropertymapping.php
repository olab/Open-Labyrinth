<?php
/**
 * Created by JetBrains PhpStorm.
 * User: larjohns
 * Date: 14/10/2012
 * Time: 12:35 πμ
 * To change this template use File | Settings | File Templates.
 */
class Model_Leap_Vocabulary_LegacyPropertyMapping extends DB_ORM_Model
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
            'class' => new DB_ORM_Field_String($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            'property' => new DB_ORM_Field_String($this, array(
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

        );
    }

    public static function data_source() {
        return 'default';
    }

    public static function table() {
        return 'rdf_mappings_legacy_properties';
    }

    public static function primary_key() {
        return array('id');
    }

    public static function getAllMappings(){
        $builder = DB_SQL::select('default')->from(self::table());

        $result = $builder->query();

        if ($result->is_loaded()) {
            $mappings = array();

            foreach ($result as $record) {
                $mappings[] = DB_ORM::model('vocabulary_legacypropertymapping', array((int)$record['id']));
            }


            return $mappings;
        }

        return array();


    }

    public static function get_properties_by_class($className){
        return array_keys(DB_ORM_Model::factory($className)->as_array());
    }

    public static function get_mappings_by_class($className){
        $builder = DB_SQL::select('default')->from(self::table())->where('class', '=', $className);
        $result = $builder->query();

        if ($result->is_loaded()) {
            $mappings = array();

            foreach ($result as $record) {
                $mappings[] = DB_ORM::model('vocabulary_legacypropertymapping', array((int)$record['id']));
            }

            return $mappings;
        }

        return array();

    }



    public function getTriples($offset=0, $limit=0){
        $tableName = DB_ORM::model($this->class)->table();
        $primary = DB_ORM::model($this->class)->primary_key();
        $builder = DB_SQL::select('default')
            ->from($tableName)->offset($offset);
        if($limit>0) $builder->limit($limit);
        $result = $builder->query();



        $triples = array();
        foreach ($result as $record) {

            $uri = Model_Leap_Vocabulary::getObjectUri($this->class,$record[$primary[0]]);
            $object = DB_ORM::model($this->class, array($record[$primary[0]]));

            if($object->is_field($this->property))
                $value = $object->{$this->property};
            else {


                $property = $object->{$this->property};

               if(is_a($property, "DB_ResultSet"))
                if($property->count()>0)
                   $property = $property[0];
                else {break;}

                $className = get_class($property);
                $modelName = strtolower(str_replace("Model_Leap_","",$className));

                    $rel_pkeys = $property->primary_key();


                $value = Model_Leap_Vocabulary::getObjectUri($modelName,$property->{$rel_pkeys[0]});

            }

            $triple_data =
                array('s'=>$uri, 'p'=>$this->term->getFullRepresentation(), 'o'=>$value,'type'=>$this->type);

            $triple = DB_ORM::model('vocabulary_triple');

            $triple->load($triple_data);

            $triples[] = $triple;
        }
        return $triples;

    }
    public static $PrivateProperties = array(
        'user'=>array('password', 'type', 'type_id', 'resetHashKey', 'resetHashKeyTime', 'resetAttempt', 'resetTimestamp'),
        'user_session'=>array('user_ip'),
    );

}
