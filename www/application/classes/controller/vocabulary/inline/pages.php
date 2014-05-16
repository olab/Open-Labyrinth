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
    public function before()
    {

        parent::before();

    }

    public function action_schema()
    {



        $view = View::factory('vocabulary/inline/schema');
        $view->set('templateData', $this->templateData);

        $this->templateData['center'] = $view;

        $this->template->set('templateData', $this->templateData);

    }

    public function action_install()
    {

        $values = $this->request->query();

        Model_Leap_Vocabulary_Vocablet::install($values["vocablet"]);
        Request::initial()->redirect(URL::base() . 'vocabulary/vocablets/manager/');

    }

    public function action_uninstall(){

        $values = $this->request->post();
        $vocablet = Model_Leap_Vocabulary_Vocablet::getVocabletByGuid($values['guid']);

        $vocablet->delete();
        Request::initial()->redirect(URL::base() . 'vocabulary/vocablets/manager/');
    }





}
