<?php
/**
 * Created by JetBrains PhpStorm.
 * User: larjohns
 * Date: 8/12/2012
 * Time: 8:48 μμ
 * To change this template use File | Settings | File Templates.
 */
class Controller_Vocabulary_Mappings_Manager extends Controller_Base
{

    public function before()
    {

        parent::before();
        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Manage RDF Vocabularies'))->set_url(URL::base() . 'vocabulary/manager'));
        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Mappings'))->set_url(URL::base() . 'vocabulary/mappings/manager'));
    }

    public function action_index()
    {
        $metadataMappings = Model_Leap_Vocabulary_Mapping::getAll();
        $classMappings = Model_Leap_Vocabulary_ClassMapping::getAllClassMappings();
        $legacyPropertyMappings = Model_Leap_Vocabulary_LegacyPropertyMapping::getAllMappings();

        $inlines = Model_Leap_Metadata::getMetadataByType("inlineobjectrecord");

        $models = Model_Leap_Metadata::$Models;

        $properties = array();

        foreach ($models as $model => $label) {

            if (isset(Model_Leap_Vocabulary_LegacyPropertyMapping::$PrivateProperties[$model])) {
                $properties[$model] = array_values(array_diff(Model_Leap_Vocabulary_LegacyPropertyMapping::get_properties_by_class($model), Model_Leap_Vocabulary_LegacyPropertyMapping::$PrivateProperties[$model]));

            } else
                $properties[$model] = Model_Leap_Vocabulary_LegacyPropertyMapping::get_properties_by_class($model);

        }

        foreach ($inlines as $inline) {
            $models[$inline->name] = 'inlineobjectrecord.' . $inline->name;
        }

        $terms_properties = array();
        foreach (Model_Leap_Vocabulary_Term::getAll(array(Model_Leap_Vocabulary_Term::RDFPropertyType)) as $vocab => $terms) {
            if (!isset($terms_properties[$vocab]))
                $terms_properties[$vocab] = array();
            foreach ($terms as $term) {
                $terms_properties[$vocab][$term->id] = array("label" => $term->term_label, "uri" => $term->getFullRepresentation());
            }
        }
        $terms_classes = array();
        foreach (Model_Leap_Vocabulary_Term::getAll(array(Model_Leap_Vocabulary_Term::RDFClassType, Model_Leap_Vocabulary_Term::OWLClassType)) as $vocab => $terms) {
            if (!isset($terms_classes[$vocab]))
                $terms_classes[$vocab] = array();
            foreach ($terms as $term) {
                $terms_classes[$vocab][$term->id] = array("label" => $term->term_label, "uri" => $term->getFullRepresentation());
            }
        }
        $leftView = View::factory('vocabulary/semanticsMenu');
        $leftView->set('templateData', $this->templateData);

        $this->templateData['title'] = "Manage Metadata Mappings";
        $this->templateData['left'] = $leftView;

        $this->templateData['metadata'] = Model_Leap_Metadata::getMetadataByModelName();
        $this->templateData['terms_properties'] = $terms_properties;
        $this->templateData['terms_classes'] = $terms_classes;
        $this->templateData['metadataMappings'] = $metadataMappings;
        $this->templateData['classMappings'] = $classMappings;
        $this->templateData['legacyPropertyMappings'] = $legacyPropertyMappings;
        $this->templateData['models'] = $models;
        $this->templateData['properties'] = $properties;
        $view = View::factory('vocabulary/mappings/manage');
        $view->set('templateData', $this->templateData);

        $this->templateData['center'] = $view;
        unset($this->templateData['right']);
        $this->template->set('templateData', $this->templateData);

    }

    public function action_addlegacy()
    {
        $values = $this->request->post();
        if (isset(Model_Leap_Vocabulary_LegacyPropertyMapping::$PrivateProperties[$values["class"]]))
            if (in_array($values["property"], Model_Leap_Vocabulary_LegacyPropertyMapping::$PrivateProperties[$values["class"]]))
                return;
        $metadata = DB_ORM::model('vocabulary_legacypropertymapping');
        $metadata->load($values);
        $metadata->save();
        Request::initial()->redirect(URL::base() . 'vocabulary/mappings/manager/');
    }

    public function action_addclass()
    {
        $values = $this->request->post();
        $metadata = DB_ORM::model('vocabulary_classmapping');
        $metadata->load($values);
        $metadata->save();
        Request::initial()->redirect(URL::base() . 'vocabulary/mappings/manager/');
    }

    public function action_addmetadata()
    {
        $values = $this->request->post();
        $metadata = DB_ORM::model('vocabulary_mapping');
        $metadata->load($values);
        $metadata->save();
        Request::initial()->redirect(URL::base() . 'vocabulary/mappings/manager/');
    }

    public function action_deletemetadata()
    {

        $values = $this->request->post();
        $mapping = DB_ORM_Model::factory("vocabulary_mapping");
        $mapping->load(array("id" => $values['id']));
        $mapping->delete();
        Request::initial()->redirect(URL::base() . 'vocabulary/mappings/manager/');

    }

    public function action_deleteclass()
    {

        $values = $this->request->post();
        $mapping = DB_ORM_Model::factory("vocabulary_classmapping");
        $mapping->load(array("id" => $values['id']));
        $mapping->delete();
        Request::initial()->redirect(URL::base() . 'vocabulary/mappings/manager/');

    }

    public function action_deletelegacy()
    {

        $values = $this->request->post();
        $mapping = DB_ORM_Model::factory("vocabulary_legacypropertymapping");
        $mapping->load(array("id" => $values['id']));
        $mapping->delete();
        Request::initial()->redirect(URL::base() . 'vocabulary/mappings/manager/');

    }

}
