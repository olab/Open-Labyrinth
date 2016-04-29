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

class Controller_Options extends Controller_Base
{

    public function before()
    {
        parent::before();
        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Settings'))->set_url(URL::base() . 'options'));
    }

    public function action_index()
    {
        $this->templateData['curios_video_player_domains'] = get_option('curios_video_player_domains');

        $this->templateData['center'] = View::factory('options/index')->set('templateData', $this->templateData);
        $this->template->set('templateData', $this->templateData);
    }

    public function action_save()
    {
        $post = $this->request->post();
        $curios_video_player_domains = trim(Arr::get($post, 'curios_video_player_domains'));
        $curios_video_player_domains = trim($curios_video_player_domains, ', ');

        if (empty($curios_video_player_domains)) {
            $curios_video_player_domains = [];
        } else {
            $curios_video_player_domains = explode(',', $curios_video_player_domains);
            $curios_video_player_domains = array_map(function ($value) {
                return trim($value);
            }, $curios_video_player_domains);
        }

        update_option('curios_video_player_domains', $curios_video_player_domains);

        Session::instance()->set('success_message', 'Saved.');
        Request::initial()->redirect(URL::base() . 'options');
    }
}