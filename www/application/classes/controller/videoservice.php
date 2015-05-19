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

class Controller_VideoService extends Controller_Base {

    public function before() {
        parent::before();
    }

    public function action_index()
    {
        Request::initial()->redirect('videoservice/video');
    }

    public function action_video()
    {
        $user = Auth::instance()->get_user();

        $provider = DB_ORM::select('Lti_Provider')->where('name' , '=', 'video service')->query()->fetch();

        if(!empty($provider)) {
            $endpoint = $provider->launch_url;
            $key = $provider->consumer_key;
            $secret = $provider->secret;

            $params = array(
                "resource_link_id" => "120988f929-274612",
                "resource_link_title" => "OpenLabyrinth",
                "resource_link_description" => "OpenLabyrinth",
                "user_id" => $user->id,
                "roles" => $user->type->name,
                "lis_person_name_full" => $user->nickname,
                "lis_person_contact_email_primary" => $user->email,
                "lis_person_sourcedid" => "oLab:user",
                "context_id" => "456434513",
                "context_title" => "OpenLabyrinth",
                "context_label" => "OpenLabyrinth",
                "tool_consumer_instance_guid" => $_SERVER['HTTP_HOST'],
                "tool_consumer_instance_description" => "University",
            );

            $params = $this->signParameters($params, $endpoint, "POST", $key, $secret);
            $this->templateData['LTI'] = $params;
            $this->templateData['endpoint'] = $endpoint;
        }

        $this->templateData['leftHidden'] = true;
        $nodeView = View::factory('videoservice');
        $nodeView->set('templateData', $this->templateData);
        $this->templateData['center'] = $nodeView;

        $this->template->set('templateData', $this->templateData);
        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Video mashup')));
    }

    private function signParameters($params, $endpoint, $method, $oauth_consumer_key, $oauth_consumer_secret)
    {
        $params["lti_version"] = "LTI-1p0";
        $params["lti_message_type"] = "basic-lti-launch-request";
        $params["oauth_callback"] = "about:blank";

        $token = '';

        if(!class_exists('OAuthSignatureMethod_HMAC_SHA1')){
            require_once APPPATH.'classes/lti/oauth.php';
        }

        $hmac_method = new OAuthSignatureMethod_HMAC_SHA1();
        $consumer = new OAuthConsumer($oauth_consumer_key, $oauth_consumer_secret, NULL);

        $acc_req = OAuthRequest::from_consumer_and_token($consumer, $token, $method, $endpoint, $params);
        $acc_req->sign_request($hmac_method, $consumer, $token);

        $new_params = $acc_req->get_parameters();

        return $new_params;
    }
}