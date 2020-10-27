<?php
/**
 * Created by PhpStorm.
 * User: larjohns
 * Date: 5/5/2014
 * Time: 6:49 μμ
 */
defined('SYSPATH') or die('No direct script access.');


class Model_Leap_Vocabulary_Vocablet extends DB_ORM_Model
{
    public function __construct()
    {
        parent::__construct();

        $this->fields = array(
            'id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
                'unsigned' => TRUE,
            )),
            'guid' => new DB_ORM_Field_String($this, array(
                'max_length' => 16,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            'version' => new DB_ORM_Field_String($this, array(
                'max_length' => 5,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            'name' => new DB_ORM_Field_String($this, array(
                'max_length' => 64,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            'path' => new DB_ORM_Field_String($this, array(
                'max_length' => 128,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            'state' => new DB_ORM_Field_String($this, array(
                'max_length' => 10,
                'nullable' => TRUE,
                'savable' => TRUE,
                'default' => true,
            )),



        );

    }

    public static function getList()
    {


        $path = DOCROOT .  "extensions/vocablets";


        $extensions = array();

        foreach (new DirectoryIterator($path) as $file) {
            if ($file->isDot()) continue;

            if ($file->isDir()) {
                $name = $file->getFilename();
                $ini = $path . "/" . $name . "/vocablet.ini";


                if (file_exists($ini)) {
                    $settings = parse_ini_file($ini, true);
                    //var_dump($settings);
                    $state = null;
                    $existing =  self::getVocabletByGuid($settings["info"]["guid"]);
                    if(!empty($existing))
                        $state  = $existing->state;

                    $extensions[$settings["info"]["guid"]] = array("settings" => $settings, "name" => $name, "state"=>$state);

                }


            }
        }


        return $extensions;

    }

    public function getSettings()
    {
        $path = DOCROOT . "extensions/vocablets";


        $dir = $path . "/" . $this->name;


        if (is_dir($dir)) {

            $ini = $dir . "/vocablet.ini";

            //echo $ini;
            if (file_exists($ini)) {
                $settings = parse_ini_file($ini, true);
                return $settings;

            }
        }
        return null;
    }

    public static function getAllPages(){
        $installed = self::getEnabledObjects();

        $pages = array();

        foreach ($installed as $vocablet) {
            $settings = $vocablet->getSettings();
            if(!empty($settings)){
                if(!empty($settings["pages"])){
                    $pages[$vocablet->name] = $settings["pages"];
                }
            }
        }

        return $pages;

    }



    public static function getAllEntities(){
        $installed = self::getEnabledObjects();

        $entities = array();

        foreach ($installed as $vocablet) {
            $settings = $vocablet->getSettings();
            if(!empty($settings)){
                if(!empty($settings["entities"])){
                    $entities[$vocablet->name] = $settings["entities"];
                }
            }
        }

        return $entities;

    }
    public static function getAllRenders(){
        $installed = self::getEnabledObjects();

        $renders = array();

        foreach ($installed as $vocablet) {
            $settings = $vocablet->getSettings();
            if(!empty($settings)){
                if(!empty($settings["renders"])){
                    $renders[$vocablet->name] = $settings["renders"];
                }
            }
        }

        return $renders;

    }

    public static function install($vocablet)
    {

        $path = DOCROOT .  "extensions/vocablets";


        $dir = $path . "/" . $vocablet;


        if (is_dir($dir)) {

            $ini = $dir . "/vocablet.ini";


            if (file_exists($ini)) {
                $settings = parse_ini_file($ini, true);
                //var_dump($settings);

                $existing = self::getVocabletByGuid($settings["info"]["guid"]);
                if(!empty($existing))return true;


                //dependencies
                if (isset($settings["dependencies"])) {

                    foreach($settings["dependencies"] as $dependency=>$info){
                        if(!self::install($info["name"]))return false;

                    }


                }


                //metadata
                if (isset($settings["metadata"])) {
                    foreach ($settings["metadata"] as $metadata => $field_settings) {
                        $byGuid = Model_Leap_Metadata::getMetadataByGuid($field_settings["guid"]);
                        if (empty($byGuid)) {
                            $metadata = DB_ORM::model('metadata');
                            $metadata->load($field_settings);
                            $metadata->save();
                        }

                    }
                    // var_dump($settings["metadata"]);
                }
                //vocabularies
                if (isset($settings["vocabularies"])) {
                    foreach ($settings["vocabularies"] as $vocabulary => $vocabulary_settings) {
                        if(isset($vocabulary_settings["file"])){
                            $vpath = "extensions/vocablets". "/" . $vocablet ."/vocabularies/".$vocabulary_settings["file"];

                        }
                        else $vpath = $vocabulary_settings["url"];

                        if(!empty($vpath)){
                            $terms = Model_Leap_Vocabulary::import($vpath);
                            //var_dump($vpath);
                           // var_dump($terms);
                        }

                    }
                }


                //mappings
                if (isset($settings["mappings"])) {
                    foreach ($settings["mappings"] as $mapping => $mapping_settings) {

                        switch ($mapping_settings["mapping_type"]) {
                            case "metadata":
                                $term = $mapping_settings["term"];
                                $vocabulary = $mapping_settings["vocabulary"];
                                $vocab_term = Model_Leap_Vocabulary_Term::getTerm($term, $vocabulary);
                                $byGuid = Model_Leap_Metadata::getMetadataByGuid($mapping_settings["field_guid"]);
                                // var_dump($vocab_term->name);die;
                                if (!empty($vocab_term) && !empty($byGuid)) {

                                    $values = array(
                                        "metadata_id" => $byGuid->id,
                                        "term_id" => $vocab_term->id,
                                        "type" => $mapping_settings["type"],

                                    );
                                    $metadata = DB_ORM::model('vocabulary_mapping');
                                    $metadata->load($values);
                                    $metadata->save();
                                }


                                break;
                            case "property":

                                $term = $mapping_settings["term"];
                                $vocabulary = $mapping_settings["vocabulary"];
                                $model = $mapping_settings["model"];
                                $property = $mapping_settings["property"];
                                $vocab_term = Model_Leap_Vocabulary_Term::getTerm($term, $vocabulary);

                                if (!empty($vocab_term) && !empty($model)&& !empty($property)) {

                                    $values = array(
                                        "class" => $model,
                                        "property" => $property,
                                        "term_id" => $vocab_term->id,
                                        "type" => $mapping_settings["type"],

                                    );
                                    $metadata = DB_ORM::model('vocabulary_legacypropertymapping');
                                    $metadata->load($values);
                                    $metadata->save();
                                }

                                break;
                            case "class":
                                $term = $mapping_settings["term"];
                                $vocabulary = $mapping_settings["vocabulary"];
                                $model = $mapping_settings["model"];
                                $vocab_term = Model_Leap_Vocabulary_Term::getTerm($term, $vocabulary);

                                if (!empty($vocab_term) && !empty($model) ) {

                                    $values = array(
                                        "class" => $model,
                                        "term_id" => $vocab_term->id,


                                    );
                                    $metadata = DB_ORM::model('vocabulary_classmapping');
                                    $metadata->load($values);
                                    $metadata->save();
                                }

                                break;
                            default: continue;
                        }


                    }
                }
                //var_dump( $settings);
                $vocabletInstance = DB_ORM::model('vocabulary_vocablet');
                $vocabletInstance->guid = $settings["info"]["guid"];
                $vocabletInstance->version = $settings["info"]["version"];
                $vocabletInstance->name = $vocablet;
                $vocabletInstance->path = $vocablet;

                $vocabletInstance->save();

             }

            return true;
        }
        else {
            return false;
        }
    }

    public static function getEnabled(){
        error_reporting(E_ALL ^ E_DEPRECATED);
        $checktable  = DB::query(null,'SHOW TABLES LIKE "vocablets"')->execute();
         //$table_exists = mysql_num_rows($checktable) > 0;

        if($checktable==false) return array();

        $builder = DB_SQL::select('default')->from(self::table())->where('state', '=', 1);
        $result = $builder->query();

        if ($result->is_loaded()) {
            $vocablets = array();

            foreach ($result as $record) {
                $vocablet = DB_ORM::model('vocabulary_vocablet', array((int)$record['id']));
                $vocablets[$vocablet->name] =
                    DOCROOT . "extensions/vocablets/". $vocablet->name;
            }

            return $vocablets;
        }
        return array();
    }

    private static function getEnabledObjects(){
        $builder = DB_SQL::select('default')->from(self::table())->where('state', '=', 1);
        $result = $builder->query();

        if ($result->is_loaded()) {
            $vocablets = array();

            foreach ($result as $record) {
                $vocablet = DB_ORM::model('vocabulary_vocablet', array((int)$record['id']));
                $vocablets[$vocablet->name] =
                    $vocablet;
            }

            return $vocablets;
        }
        return array();
    }

    public static function getRoutes(){

    }


    public function uninstall(){


    }

    public static function getVocabletByGuid($guid)
    {
        $builder = DB_SQL::select('default')->from(self::table())->where('guid', '=', $guid);
        $result = $builder->query();

        if ($result->is_loaded()) {
            $vocablets = array();

            foreach ($result as $record) {
                $vocablets[] = DB_ORM::model('vocabulary_vocablet', array((int)$record['id']));
            }

            return $vocablets[0];
        }
        return NULL;
    }

    public static function data_source()
    {
        return 'default';
    }

    public static function table()
    {
        return 'vocablets';
    }

    public static function primary_key()
    {
        return array('id');
    }

} 