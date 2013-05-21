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

class Kohana_Exception extends Kohana_Kohana_Exception {
    public static function handler(Exception $e) {
        try {
            Kohana::$log->add(Log::ERROR, parent::text($e));

            $type    = get_class($e);
            $code    = $e->getCode();
            $message = $e->getMessage();
            $file    = $e->getFile();
            $line    = $e->getLine();

            $attributes = array
            (
                'controller' => 'error',
                'action'  => 404,
                'message' => rawurlencode(
                    $type . '#$#' .
                    $code . '#$#' .
                    $message . '#$#' .
                    $file . '#$#' .
                    $line . '#$#' .
                    json_encode($_POST) . '#$#' .
                    json_encode($_GET) . '#$#' .
                    URL::site(Request::initial()->uri(), true)
                )
            );

            if ($e instanceof HTTP_Exception) {
                $attributes['action'] = $e->getCode();
            }

            echo Request::factory(Route::get('error')->uri($attributes))
                ->execute()
                ->send_headers()
                ->body();
        } catch (Exception $e) {
            ob_get_level() and ob_clean();
            echo parent::text($e);
            exit(1);
        }
    }
}