<?php
/**
 * Created by JetBrains PhpStorm.
 * User: larjohns
 * Date: 14/9/2012
 * Time: 10:00 πμ
 * To change this template use File | Settings | File Templates.
 */
require_once(Kohana::find_file('vendor', 'arc2/ARC2'));
require_once(Kohana::find_file('vendor', 'graphite'));

class Controller_Vocabulary_Manager extends Controller_Base
{
    public function action_index() {
        $this->templateData['vocabularies'] = DB_ORM::model('vocabulary')->getAllVocabulary();

        $view = View::factory('vocabulary/info');
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

    public function action_import(){



        $uri = $this->request->query('uri');

        $graph = new Graphite();
        $graph->load( $uri );

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



        $this->templateData['uri'] = $uri;

        $this->templateData['terms'] = $termLabels;

        $view = View::factory('vocabulary/importOK');
        $view->set('templateData', $this->templateData);
        $this->templateData['center'] = $view;
        unset($this->templateData['right']);
        $this->template->set('templateData', $this->templateData);

    }
}