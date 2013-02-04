<?php
/**
 * Created by JetBrains PhpStorm.
 * User: larjohns
 * Date: 8/12/2012
 * Time: 8:48 μμ
 * To change this template use File | Settings | File Templates.
 */ 
class Controller_Vocabulary_Mappings_Manager extends Controller_Base {


    public function action_index(){
        $metadataMappings = Model_Leap_Vocabulary_Mapping::getAll();
        $classMappings = Model_Leap_Vocabulary_ClassMapping::getAllClassMappings();
        $legacyPropertyMappings = Model_Leap_Vocabulary_LegacyPropertyMapping::getAllMappings();

        $inlines = Model_Leap_Metadata::getMetadataByType("inlineobjectrecord");

        $models = array("user"=>"user","map"=>"map");

        foreach($inlines as $inline){
            $models[$inline->name]='inlineobjectrecord.'.$inline->name;
        }

        $terms = array();

        foreach(Model_Leap_Vocabulary_Term::getAll() as $term){
            $terms[$term->id]= $term->term_label;
        }

        $this->templateData['metadata'] = Model_Leap_Metadata::getMetadataByModelName();
        $this->templateData['terms'] = $terms;
        $this->templateData['metadataMappings'] = $metadataMappings;
        $this->templateData['classMappings'] = $classMappings;
        $this->templateData['legacyPropertyMappings'] = $legacyPropertyMappings;
        $this->templateData['models'] = $models;
        $view = View::factory('vocabulary/mappings/manage');
        $view->set('templateData', $this->templateData);

        $this->templateData['center'] = $view;
        unset($this->templateData['right']);
        $this->template->set('templateData', $this->templateData);

    }

    public function action_addlegacy(){
        $values = $this->request->post();
        $metadata = DB_ORM::model('vocabulary_legacypropertymapping');
        $metadata->load($values);
        $metadata->save();
        Request::initial()->redirect(URL::base().'vocabulary/mappings/manager/');
    }

    public function action_addclass(){
        $values = $this->request->post();
        $metadata = DB_ORM::model('vocabulary_classmapping');
        $metadata->load($values);
        $metadata->save();
        Request::initial()->redirect(URL::base().'vocabulary/mappings/manager/');
    }

    public function action_addmetadata(){
        $values = $this->request->post();
        $metadata = DB_ORM::model('vocabulary_mapping');
        $metadata->load($values);
        $metadata->save();
        Request::initial()->redirect(URL::base().'vocabulary/mappings/manager/');
    }

    public function action_deletemetadata(){

        $values = $this->request->post();
        $mapping = DB_ORM_Model::factory("vocabulary_mapping");
        $mapping->load(array("id" => $values['id']));
        $mapping->delete();
        Request::initial()->redirect(URL::base().'vocabulary/mappings/manager/');

    }

    public function action_deleteclass(){

        $values = $this->request->post();
        $mapping = DB_ORM_Model::factory("vocabulary_classmapping");
        $mapping->load(array("id" => $values['id']));
        $mapping->delete();
        Request::initial()->redirect(URL::base().'vocabulary/mappings/manager/');

    }

    public function action_deletelegacy(){

        $values = $this->request->post();
        $mapping = DB_ORM_Model::factory("vocabulary_legacypropertymapping");
        $mapping->load(array("id" => $values['id']));
        $mapping->delete();
        Request::initial()->redirect(URL::base().'vocabulary/mappings/manager/');

    }

}
