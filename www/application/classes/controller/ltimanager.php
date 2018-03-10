<?php

/**
 * Open Labyrinth [ http://www.openlabyrinth.ca ]
 *
 * Open Labyrinth is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Open Labyrinth is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Open Labyrinth.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @copyright Copyright 2012 Open Labyrinth. All Rights Reserved.
 *
 */
defined('SYSPATH') or die('No direct script access.');

class Controller_LtiManager extends Controller_Base {

    public function before() {
        parent::before();
        Breadcrumbs::add(Breadcrumb::factory()->set_title(__("Lti manager"))->set_url(URL::base().'ltimanager/index'));
    }

    public function action_providers()
    {
        $this->templateData['providers'] = DB_ORM::select('Lti_Provider')->query();

        $this->templateData['center'] = View::factory('lti/providers')->set('templateData', $this->templateData);
        $this->template->set('templateData', $this->templateData);

        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Providers')));
    }

    public function action_providerView()
    {
        $key = $this->request->param('id');
        $this->templateData['provider'] = $key ? DB_ORM::model('Lti_Provider', array($key)) : false;
        $this->templateData['center'] = View::factory('lti/provider')->set('templateData', $this->templateData);
        $this->template->set('templateData', $this->templateData);
        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Provider Manager')));
    }

    public function action_saveProvider()
    {
        $post = $this->request->post();
        $id = Arr::get($post, 'id', null);

        $provider = DB_ORM::model('Lti_Provider', array($id));
        $provider->name = Arr::get($post, 'name', $provider->name);
        $provider->consumer_key = Arr::get($post, 'consumer_key', $provider->consumer_key);
        $provider->secret = Arr::get($post, 'secret', $provider->secret);
        $provider->launch_url = Arr::get($post, 'launch_url', $provider->launch_url);
        $provider->save();

        Request::initial()->redirect(URL::base().'ltimanager/providers');
    }

    public function action_deleteProvider() {
        $id = $this->request->param('id', null);
        $provider = DB_ORM::model('Lti_Provider', $id);
        $provider->delete();

        Request::initial()->redirect(URL::base().'ltimanager/providers');
    }

    public function action_index()
    {
        $this->templateData['users'] = DB_ORM::model('Lti_Consumer')->getAll();
        $this->templateData['center'] = View::factory('lti/index')->set('templateData', $this->templateData);
        $this->template->set('templateData', $this->templateData);

        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Consumers')));
    }

    public function action_userView()
    {
        $key = $this->request->param('id');
        $this->templateData['user'] = $key ? DB_ORM::model('Lti_Consumer', array($key)) : false;
        $this->templateData['center'] = View::factory('lti/user')->set('templateData', $this->templateData);
        $this->template->set('templateData', $this->templateData);
        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('User Manager')));
    }

    public function action_saveConsumer()
    {
        $post   = $this->request->post();
        $id     = Arr::get($post, 'id', null);
        $post['without_end_date'] = Arr::get($post, 'without_end_date', 0);

        DB_ORM::model('Lti_Consumer')->saveConsumer($id, $post);

        Request::initial()->redirect(URL::base().'ltimanager');
    }

    public function action_deleteUser() {
        $id = $this->request->param('id', null);
        $user = DB_ORM::model('Lti_Consumer', $id);
        $user->delete();

        Request::initial()->redirect(URL::base().'ltimanager');
    }

    public function action_getXML(){ //TODO: change xml credentials
        $key = $this->request->param('id', null);
        $secret = $this->request->param('id2', null);
        $url = URL::site(NULL, TRUE);
        $xml = <<< EOD
        <?xml version="1.0" encoding="UTF-8"?>
        <basic_lti_link
            xmlns="http://www.imsglobal.org/xsd/imsbasiclti_v1p0"
            xmlns:lticm ="http://www.imsglobal.org/xsd/imslticm_v1p0"
            xmlns:lticp ="http://www.imsglobal.org/xsd/imslticp_v1p0"
            xmlns:xsi = "http://www.w3.org/2001/XMLSchema-instance"
            xsi:schemaLocation = "http://www.imsglobal.org/xsd/imsbasiclti_v1p0 http://www.imsglobal.org/xsd/lti/ltiv1p0/imsbasiclti_v1p0.xsd
                                  http://www.imsglobal.org/xsd/imslticm_v1p0 http://www.imsglobal.org/xsd/lti/ltiv1p0/imslticm_v1p0.xsd
                                  http://www.imsglobal.org/xsd/imslticp_v1p0 http://www.imsglobal.org/xsd/lti/ltiv1p0/imslticp_v1p0.xsd">
          <title>OpenLabyrinth</title>
          <description>Access to OpenLabyrinth using LTI</description>
          <launch_url>{'$url'}</launch_url>
          <secure_launch_url />
          <icon>{""}</icon>
          <secure_icon />
          <custom />
          <extensions platform="learn">
            <lticm:property name="guid">{$key}</lticm:property>
            <lticm:property name="secret">{$secret}</lticm:property>
          </extensions>
          <vendor>
            <lticp:code>spvsp</lticp:code>
            <lticp:name>SPV Software Products</lticp:name>
            <lticp:description>Provider of open source educational tools.</lticp:description>
            <lticp:url>http://www.SW.com/</lticp:url>
            <lticp:contact>
              <lticp:email>SW@SW.com</lticp:email>
            </lticp:contact>
          </vendor>
        </basic_lti_link>
EOD;
        header("Cache-Control: public");
        header("Content-Description: File Transfer");
        header("Content-Length: ". strlen($xml).";");
        header("Content-Disposition: attachment; filename=$key.xml");
        header("Content-Type: application/octet-stream; ");
        header("Content-Transfer-Encoding: binary");
        echo $xml;
        exit;
    }
}