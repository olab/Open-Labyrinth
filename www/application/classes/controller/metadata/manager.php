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
        $models = array("user"=>"user","map"=>"map");

        foreach($inlines as $inline){
            $models[$inline->name]='inlineobjectrecord.'.$inline->name;
        }

        $this->templateData['metadata'] = $metadata;
        $this->templateData['models'] = $models;
        $view = View::factory('metadata/manage');
        $view->set('templateData', $this->templateData);

        $this->templateData['center'] = $view;
        unset($this->templateData['right']);
        $this->template->set('templateData', $this->templateData);

    }

    public function before() {
        parent::before();

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
