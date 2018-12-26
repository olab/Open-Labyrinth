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
defined('SYSPATH') or die('No direct access allowed.');

$config = array();

$config['support'] = array(
    'main_support_email' => '',
    'email' => '',
    'mail_settings' => array(
        'subject' => 'OpenLabyrinth: Support: #error_type#',
        'message' => 'Hi,

    DETAILS:
        Error type: #error_type#
        Error code: #error_code#
        Error message: #error_message#
        Error file: #error_file#
        Error line: #error_line#
        Client browser: #browser_info#
        Client browser version: #browser_version#
        Client resolution: #client_resolution#
        User: #username#
        POST(json): #post#
        GET(json): #get#
        URL: #url#
        IP: #ip#'
    )
);

return $config;
?>