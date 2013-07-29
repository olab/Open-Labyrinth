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

class Controller_Error extends Controller_Template {
    public $template = 'error/basic';

    private $error = null;

    public function before() {
        parent::before();

        if (Request::$initial !== Request::$current) {
            if ($message = rawurldecode($this->request->param('message'))) {
                $this->template->message = $message;
                $this->parseError($message);
            }
        } else {
            $this->request->action(404);
        }
        $this->response->status((int) $this->request->action());
    }

    public function action_404() {
        $this->writeToLog();
        $this->sendMail();

        $this->template->set('center', View::factory('error/404'));

        $this->template->set('type', $this->error['type']);
        $this->template->set('code', $this->error['code']);
        $this->template->set('message', $this->error['message']);
        $this->template->set('file', $this->error['file']);
        $this->template->set('line', $this->error['line']);
        $this->template->set('trace', $this->error['trace']);

    }

    private function sendMail() {
        $supportConfig = Kohana::$config->load('support');

        if($supportConfig == null) return;

        $to = $supportConfig['support']['email'];
        $subject = $supportConfig['support']['mail_settings']['subject'];
        $message = $supportConfig['support']['mail_settings']['message'];
        $headers = 'From: ' . $supportConfig['support']['main_support_email'];

        if($subject != null) {
            $subject = str_replace('#error_type#', $this->toString($this->error['type']), $subject);
        }

        if($message != null) {
            $message = str_replace('#error_type#', $this->toString($this->error['type']), $message);
            $message = str_replace('#error_code#', $this->toString($this->error['code']), $message);
            $message = str_replace('#error_message#', $this->toString($this->error['message']), $message);
            $message = str_replace('#error_file#', $this->toString($this->error['file']), $message);
            $message = str_replace('#error_line#', $this->toString($this->error['line']), $message);
            $message = str_replace('#browser_info#', $this->toString($this->error['browser'] != null && isset($this->error['browser']['browser']) ? $this->error['browser']['browser'] : null), $message);
            $message = str_replace('#browser_version#', $this->toString($this->error['browser'] != null && isset($this->error['browser']['version']) ? $this->error['browser']['version'] : null), $message);
            $message = str_replace('#client_resolution#', $this->toString($this->error['resolution'] != null ? ($this->error['resolution']['width'] . 'x' . $this->error['resolution']['height']) : null), $message);
            $message = str_replace('#username#', $this->toString($this->error['user'] != null ? $this->error['user']->username : null), $message);
            $message = str_replace('#post#', $this->toString($this->error['post']), $message);
            $message = str_replace('#get#', $this->toString($this->error['get']), $message);
            $message = str_replace('#url#', $this->toString($this->error['url']), $message);
        }

        mail($to, $subject, $message, $headers);
    }

    private function writeToLog() {
        Kohana::$log->add(Log::ERROR, 'type: :type; code: :code; message: :message; file: :file; line: :line; browser: :br; browser version: :brv; resolution: :res; user: :user', array(
            ':type'    => $this->toString($this->error['type']),
            ':code'    => $this->toString($this->error['code']),
            ':message' => $this->toString($this->error['message']),
            ':file'    => $this->toString($this->error['file']),
            ':line'    => $this->toString($this->error['line']),
            ':br'      => $this->error['browser'] != null && isset($this->error['browser']['browser']) ? $this->error['browser']['browser'] : '-',
            ':brv'     => $this->error['browser'] != null && isset($this->error['browser']['version']) ? $this->error['browser']['version'] : '-',
            ':res'     => $this->error['resolution'] != null ? ($this->error['resolution']['width'] . 'x' . $this->error['resolution']['height']) : '-',
            ':user'    => $this->error['user'] != null ? $this->error['user']->username : '-'
        ));
    }

    private function parseError($message) {
        if($message == null || strlen($message) <= 0) return;

        $e = explode('#$#', $message);
        if($e == null || count($e) <= 0) return;

        $this->error = array();

        $this->error['type']       = isset($e[0]) ? $e[0] : null;
        $this->error['code']       = isset($e[1]) ? isset(Kohana_Exception::$php_errors[$e[1]]) ? Kohana_Exception::$php_errors[$e[1]]
                                                                                                : $e[1]
                                                  : null;
        $this->error['message']    = isset($e[2]) ? $e[2] : null;
        $this->error['file']       = isset($e[3]) ? $e[3] : null;
        $this->error['line']       = isset($e[4]) ? $e[4] : null;
        $this->error['browser']    = null;
        $browser = Arr::get($_COOKIE, 'browser', null);
        if($browser != null) {
            $t = explode(', ', $browser);
            if($t != null && count($t) == 2) {
                $this->error['browser'] = array('browser' => $t[0], 'version' => $t[1]);
            }
        }
        $this->error['resolution'] = null;
        $resolution = Arr::get($_COOKIE, 'resolution', null);
        if($resolution != null && strlen($resolution) > 0) {
            $t = explode(',', $resolution);
            if($t != null && count($t) == 3) {
                $this->error['resolution']['width']  = $t[0];
                $this->error['resolution']['ratio']  = $t[1];
                $this->error['resolution']['height'] = $t[2];
            }
        }
        $this->error['user'] = null;
        if(Auth::instance()->logged_in()) {
            $this->error['user'] = Auth::instance()->get_user();
        }

        $this->error['post'] = isset($e[5]) && strlen($e[5]) > 2 ? $e[5] : null;
        $this->error['get']  = isset($e[6]) && strlen($e[6]) > 2 ? $e[6] : null;
        $this->error['url']  = isset($e[7]) ? $e[7] : null;
        $this->error['trace'] = isset($e[7]) ? json_decode($e[7],true) : null;

    }

    private function toString($input) {
        if($input == null || empty($input)) return '-';

        return $input;
    }
}