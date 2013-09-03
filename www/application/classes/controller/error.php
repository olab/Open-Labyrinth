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

        require_once(DOCROOT.'application/classes/class.phpmailer.php');
        $mail = new PHPMailer;

        $mail->From = 'support@'.$_SERVER['HTTP_HOST'];
        $mail->FromName = 'OpenLabyrinth Support';

        $toArray = explode(',', $supportConfig['support']['email']);
        if (count($toArray) > 0){
            foreach($toArray as $to){
                $mail->AddAddress($to);
            }
        }

        $subject = $supportConfig['support']['mail_settings']['subject'];
        $message = $supportConfig['support']['mail_settings']['message'];

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

        $errorDetails = View::factory('error/error');
        $errorDetails->set('type', $this->error['type']);
        $errorDetails->set('code', $this->error['code']);
        $errorDetails->set('message', $this->error['message']);
        $errorDetails->set('file', $this->error['file']);
        $errorDetails->set('line', $this->error['line']);
        $errorDetails->set('trace', $this->error['trace']);
        $errorDetails->set('browser_info', $this->toString($this->error['browser'] != null && isset($this->error['browser']['browser']) ? $this->error['browser']['browser'] : null));
        $errorDetails->set('browser_version', $this->toString($this->error['browser'] != null && isset($this->error['browser']['version']) ? $this->error['browser']['version'] : null));
        $errorDetails->set('client_resolution', $this->toString($this->error['resolution'] != null ? ($this->error['resolution']['width'] . 'x' . $this->error['resolution']['height']) : null));
        $errorDetails->set('username', $this->toString($this->error['user'] != null ? $this->error['user']->username : null));

        $filename = DOCROOT.'tmp/errorDetails_'.uniqid('error').'.html';
        file_put_contents($filename, $errorDetails);

        $mail->AddAttachment($filename);

        $mail->Subject = $subject;
        $mail->Body = $message;

        $mail->Send();
        @unlink($filename);
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
        $this->error['line']       = isset($e[4]) ? (int)$e[4] : null;
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
        $this->error['url']  = isset($e[8]) ? $e[8] : null;
        $this->error['trace'] = isset($e[7]) ? json_decode($e[7],true) : null;

    }

    private function toString($input) {
        if($input == null || empty($input)) return '-';

        return $input;
    }
}