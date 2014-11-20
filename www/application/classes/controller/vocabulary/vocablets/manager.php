<?php
/**
 * Created by JetBrains PhpStorm.
 * User: larjohns
 * Date: 8/12/2012
 * Time: 8:48 μμ
 * To change this template use File | Settings | File Templates.
 */
class Controller_Vocabulary_Vocablets_Manager extends Controller_Base
{

    public function before()
    {

        parent::before();
        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Manage RDF Vocabularies'))->set_url(URL::base() . 'vocabulary/manager'));
        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Semantic Extensions'))->set_url(URL::base() . 'vocabulary/mappings/manager'));
    }

    public function action_index()
    {
        $leftView = View::factory('vocabulary/semanticExtensionsMenu');
        $leftView->set('templateData', $this->templateData);

        $this->templateData['title'] = "Manage Semantic Extensions";
        $this->templateData['left'] = $leftView;
        $vocablets = Model_Leap_Vocabulary_Vocablet::getList();



        $this->templateData['vocablets'] = $vocablets;
        $view = View::factory('vocabulary/vocablets/index');
        $view->set('templateData', $this->templateData);

        $this->templateData['center'] = $view;
        unset($this->templateData['right']);
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
