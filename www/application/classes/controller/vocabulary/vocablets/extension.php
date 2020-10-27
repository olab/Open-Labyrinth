<?php
/**
 * Created by JetBrains PhpStorm.
 * User: larjohns
 * Date: 8/12/2012
 * Time: 8:48 μμ
 * To change this template use File | Settings | File Templates.
 */
class Controller_Vocabulary_Vocablets_Extension extends Controller_Base
{

    public function before()
    {

        parent::before();
        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Manage RDF Vocabularies'))->set_url(URL::base() . 'vocabulary/manager'));
        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Mappings'))->set_url(URL::base() . 'vocabulary/mappings/manager'));
    }

    public function action_index()
    {
        $leftView = View::factory('vocabulary/semanticExtensionsMenu');
        $leftView->set('templateData', $this->templateData);

        $this->templateData['title'] = "Semantic Extensions";
        $this->templateData['left'] = $leftView;
        $vocablets = Model_Leap_Vocabulary_Vocablet::getList();

        $path = 'extensions/vocablets/mesh';
        set_include_path(get_include_path() . PATH_SEPARATOR . $path);

        $mods = Kohana_Core::modules();

        $mods["vocablet_mesh"] = $path;

        Kohana_Core::modules($mods);

        var_dump(Kohana::include_paths());

        $this->templateData['vocablets'] = $vocablets;
        $view = View::factory('vocabulary/vocablets/extension');
        $view->set('templateData', $this->templateData);

        $this->templateData['center'] = $view;
        unset($this->templateData['right']);
        $this->template->set('templateData', $this->templateData);

    }

    public function action_install()
    {

        $values = $this->request->query();

        Model_Leap_Vocabulary_Vocablet::install($values["vocablet"]);
        Controller::redirect(URL::base() . 'vocabulary/vocablets/manager/');

    }

    public function action_uninstall(){

        $values = $this->request->post();
        $vocablet = Model_Leap_Vocabulary_Vocablet::getVocabletByGuid($values['guid']);

        $vocablet->delete();
        Controller::redirect(URL::base() . 'vocabulary/vocablets/manager/');
    }




}
