<?php
/**
 * Created by JetBrains PhpStorm.
 * User: larjohns
 * Date: 11/9/2012
 * Time: 10:09 πμ
 * To change this template use File | Settings | File Templates.
 */

require_once(Kohana::find_file('vendor', 'arc2/ARC2'));
require_once(Kohana::find_file('vendor', 'Graphite'));
class Model_Leap_Vocabulary extends DB_ORM_Model
{


    public function __construct() {
        parent::__construct();

        $this->fields = array(
            'id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => TRUE,
                'unsigned' => TRUE,
            )),
            'namespace' => new DB_ORM_Field_String($this, array(
                'max_length' => 500,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),

            'prefix' => new DB_ORM_Field_String($this, array(
                'max_length' => 500,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),

            'alternative_source_uri' => new DB_ORM_Field_String($this, array(
                'max_length' => 500,
                'nullable' => TRUE,
                'savable' => TRUE,
            )),
         );


        $this->relations = array(

            'terms' => new DB_ORM_Relation_HasMany($this, array(
                'child_key' => array('vocab_id'),
                'child_model' => 'vocabulary_term',
                'parent_key' => array('id'),
            )),

        );
    }

    public static function data_source() {
        return 'default';
    }

    public static function table() {
        return 'rdf_vocabularies';
    }

    public static function primary_key() {
        return array('id');
    }


    public static function getAllVocabulary() {
        $builder = DB_SQL::select('default')->from(self::table());
        $result = $builder->query();

        $vocabs = array();
        foreach($result as $record) {
            $vocabs[$record['namespace']] = DB_ORM::model('vocabulary', array((int)$record['id']));
        }

        return $vocabs;


    }

    public static  function getVocabularyByNamespace($namespace) {
        $builder = DB_SQL::select('default')->from(self::table())->where('namespace', '=', $namespace);
        $result = $builder->query();

        if ($result->is_loaded()) {
            return DB_ORM::model('vocabulary', array($result[0]['id']));
        }

        return NULL;
    }

    public static function getGraphUri(){
        $url_base =  URL::base();
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        return $protocol. $_SERVER['SERVER_NAME'].$url_base;

    }

    public static function getObjectUri($type, $id){
         return self::getGraphUri() . "resource/".$type."/".$id;
    }

    public function delete($reset = FALSE) {
        foreach($this->terms as $term){
            foreach($term->mappings as $mapping){
                $mapping->delete();
            }
            $term->delete();
        }



        parent::delete($reset);
    }
    public function newVocabulary($namespace,  $alt_source = NULL, $prefix = NULL){

        $this->namespace = $namespace;
        $this->prefix = $prefix;
        $this->alternative_source_uri = $alt_source;


        $this->save(TRUE);

        return $this;
    }

    public static function import($uri){
        $uri_abs = $uri;

        if (!parse_url($uri, PHP_URL_SCHEME) != '') $uri_abs  = self::getGraphUri().$uri;

        $graph = new Graphite();
        $graph->load( $uri_abs );

        $terms =  $graph->allSubjects();

        $termLabels = array();

        $vocabularies = array();

        foreach($terms as $term){
            $termUri  =  $term->toString();
            $vocab_term = DB_ORM_Model::factory("vocabulary_term");
            $vocab_term->newTerm($termUri, $term->label(),$term->type());

            $termLabels[] = $term->toString();
            if($vocab_term->vocabulary->namespace!=="")
                $vocabularies[$vocab_term->vocabulary->namespace]=$vocab_term->vocabulary;

        }



        foreach($vocabularies as $vocab){

            $vocab->load();
            $vocab->alternative_source_uri = $uri;
            $vocab->save();

        }

        return $termLabels;

    }



}
