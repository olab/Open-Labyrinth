<?php
/**
 * Created by JetBrains PhpStorm.
 * User: larjohns
 * Date: 8/12/2012
 * Time: 8:48 μμ
 * To change this template use File | Settings | File Templates.
 */ 
class Controller_Metadata_Manager extends Controller_Base {


    public function action_index(){
        $metadata = Model_Leap_Metadata::getMetadataByModelName();
        $inlines = Model_Leap_Metadata::getMetadataByType("inlineobjectrecord");
        $models = Model_Leap_Metadata::$Models;

        foreach($inlines as $inline){
            $models['inlineobjectrecord.'.$inline->name]=$inline->name;
        }
        $this->templateData['title']= "Manage Metadata";
        $this->templateData['metadata'] = $metadata;
        $this->templateData['models'] = $models;
        $this->templateData["extras"] = Model_Leap_Metadata::$MetadataExtras;
        $view = View::factory('metadata/manage');
        $view->set('templateData', $this->templateData);

        $this->templateData['center'] = $view;
        unset($this->templateData['right']);
        $this->template->set('templateData', $this->templateData);
    }

    private function getSubclassesOf($parent) {
        $result = array();
        foreach (get_declared_classes() as $class) {
           // if (is_subclass_of($class, $parent))
                $result[] = $class;
        }
        return $result;
    }
    public function before() {
        parent::before();
        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Metadata'))->set_url(URL::base() . 'vocabulary/metadata/manager'));

        if(Auth::instance()->get_user()==NULL || Auth::instance()->get_user()->type->name != 'superuser') {
            Request::initial()->redirect(URL::base());
        }

        unset($this->templateData['right']);
        $this->template->set('templateData', $this->templateData);
    }

    public function action_add(){
        $values = $this->request->post();
        if(!empty($values)){
            $metadata = DB_ORM::model('metadata');
            $metadata->load($values);
            $metadata->save();
        }
        Request::initial()->redirect(URL::base().'metadata/manager/');
    }

    public function action_delete(){
        $values = $this->request->post();
        $metadata = Model_Leap_Metadata::getMetadataByName($values['name']);
        $metadata->delete();
        Request::initial()->redirect(URL::base().'metadata/manager/');

    }

}
