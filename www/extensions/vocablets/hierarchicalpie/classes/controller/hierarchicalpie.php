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

class Controller_Hierarchicalpie extends Controller_Base
{



    public function action_index()
    {
        $view = View::factory($this->view);

        $leftView = View::factory('vocabulary/semanticExtensionsMenu');
        $leftView->set('templateData', $this->templateData);



        $this->templateData['left'] = $leftView;

        $view->set('templateData', $this->templateData);

        $this->templateData['center'] = $view;
        unset($this->templateData['right']);
        $this->template->set('templateData', $this->templateData);


    }
    protected $title = "";
    protected $view = "";

    public function action_api(){

        $this->response->body(json_encode(Model_MeshReport::buildObject()));
    }

    public function before()
    {
        if($this->request->action()!="api"){
            parent::before();
            Breadcrumbs::add(Breadcrumb::factory()->set_title(__($this->title))->set_url(URL::base() . 'vocabulary/manager'));

            if (Auth::instance()->get_user() == NULL || Auth::instance()->get_user()->type->name != 'superuser') {
                Request::initial()->redirect(URL::base());
            }

            unset($this->templateData['right']);
            $this->template->set('templateData', $this->templateData);
        }
        else{
            $this->response->headers('Content-Type: application/json');

        }

    }

    public function after(){

        if($this->request->action()!="api"){

            parent::after();
        }
        else{
            $this->response->headers('Content-Type','application/json');

        }

    }

}

