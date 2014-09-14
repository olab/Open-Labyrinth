<?php
/**
 * Created by JetBrains PhpStorm.
 * User: larjohns
 * Date: 8/12/2012
 * Time: 8:48 μμ
 * To change this template use File | Settings | File Templates.
 */
class Controller_Vocabulary_Inline_Pages extends Controller_Template
{
    public $template = 'simple';
    protected $templateData = array();

    public function action_schema()
    {



        $view = View::factory('vocabulary/inline/schema');
        $view->set('templateData', $this->templateData);

        $this->templateData['center'] = $view;

        $this->template->set('templateData', $this->templateData);

    }

    public function action_annotate()
    {
        $view = View::factory('vocabulary/inline/annotate');
        $view->set('templateData', $this->templateData);

        $this->templateData['center'] = $view;

        $this->template->set('templateData', $this->templateData);

    }

    public function after(){

        if(!in_array($this->request->action(),array(
            "annotator"
        ))){
            parent::after();
        }
    }

    public function action_annotator(){
        //var_dump($this->request->query());die;
        $text = $this->request->query('text');


        //$text = "Melanoma is a malignant tumor of melanocytes which are found predominantly in skin but also in the bowel and the eye.";


        $annotator = new Helper_Model_AnnotatedEntity();
        $annotator->load($text);
        $annotator->parse();

        $out = $annotator->output();
        $this->template = null;
        $this->response->headers('Content-Type','application/json');
        $this->response->body(json_encode($out));

    }







}
