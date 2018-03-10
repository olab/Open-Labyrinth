<?php
/**
 * Created by JetBrains PhpStorm.
 * User: larjohns
 * Date: 14/9/2012
 * Time: 10:00 πμ
 * To change this template use File | Settings | File Templates.
 */
require_once(Kohana::find_file('vendor', 'arc2/ARC2'));
require_once(Kohana::find_file('vendor', 'Graphite'));

class Controller_Vocabulary_Manager extends Controller_Base
{
    public function action_index() {
        $this->templateData['vocabularies'] = DB_ORM::model('vocabulary')->getAllVocabulary();

        $view = View::factory('vocabulary/info');

        $leftView = View::factory('vocabulary/semanticsMenu');
        $leftView->set('templateData', $this->templateData);


        $this->templateData['left'] = $leftView;

        $view->set('templateData', $this->templateData);
        $this->templateData['title']= "Manage Vocabularies";
            $this->templateData['center'] = $view;
            unset($this->templateData['right']);
            $this->template->set('templateData', $this->templateData);

    }

    public function before() {
        parent::before();
        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Manage RDF Vocabularies'))->set_url(URL::base() . 'vocabulary/manager'));
    
        if(Auth::instance()->get_user()==NULL || Auth::instance()->get_user()->type->name != 'superuser') {
            Request::initial()->redirect(URL::base());
        }

        unset($this->templateData['right']);
        $this->template->set('templateData', $this->templateData);
    }

    public function action_delete(){

        $namespace = $this->request->post('uri');

        $vocab = Model_Leap_Vocabulary::getVocabularyByNamespace($namespace);
        $vocab->delete();
        Request::initial()->redirect(URL::base() . 'vocabulary/manager');
    }

    public function action_import(){

        $uri = $this->request->query('uri');

        $termLabels = Model_Leap_Vocabulary::import($uri);


        $this->templateData['uri'] = $uri;

        $this->templateData['terms'] = $termLabels;

        $this->templateData['title']= "Manage Vocabularies";

        $leftView = View::factory('vocabulary/semanticsMenu');
        $leftView->set('templateData', $this->templateData);


        $this->templateData['left'] = $leftView;
        $view = View::factory('vocabulary/importOK');
        $view->set('templateData', $this->templateData);
        $this->templateData['center'] = $view;
        unset($this->templateData['right']);
        $this->template->set('templateData', $this->templateData);

    }
}
