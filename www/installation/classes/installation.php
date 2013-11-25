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

class Installation {

    public static function action_configuration(){
        $token = Arr::get($_POST, 'token', NULL);
        if (Security::check($token)) {
            $olab = Arr::get($_POST, 'olab', NULL);
            $errorFound = false;
            if ($olab != NULL){
                if (isset($olab['admin_email']) && ($olab['admin_email'] != '')){
                    if(!filter_var($olab['admin_email'], FILTER_VALIDATE_EMAIL)){
                        Notice::add('Invalid field: Admin Email');
                        $errorFound = true;
                    }
                } else {
                    Notice::add('Field required: Admin Email');
                    $errorFound = true;
                }

                if (!isset($olab['admin_user']) || ($olab['admin_user'] == '') ){
                    Notice::add('Field required: Admin Username');
                    $errorFound = true;
                }

                if (isset($olab['admin_password']) && ($olab['admin_password'] != '')) {
                    if (!isset($olab['admin_password2']) || ($olab['admin_password2'] == '')){
                        Notice::add('Field required: Confirm Admin Password');
                        $errorFound = true;
                    } else {
                        if ($olab['admin_password'] != $olab['admin_password2']){
                            $olab['admin_password2'] = '';
                            Notice::add('Invalid field: Confirm Admin Password');
                            $errorFound = true;
                        }
                    }
                }else {
                    Notice::add('Field required: Admin Password');
                    $errorFound = true;
                }

                Session::set('installationConfiguration', json_encode($olab));
                if (!$errorFound){
                    Session::set('installationStep', '3');
                }
            }
        }
        Installation::redirect(URL::base() . 'installation/index.php');
    }

    public static function action_database(){
        $token = Arr::get($_POST, 'token', NULL);
        if (Security::check($token)) {
            $olab = Arr::get($_POST, 'olab', NULL);
            $errorFound = false;
            if ($olab != NULL){
                if (!isset($olab['db_host']) || ($olab['db_host'] == '')){
                    Notice::add('Field required: Host Name');
                    $errorFound = true;
                }

                if (!isset($olab['db_user']) || ($olab['db_user'] == '')){
                    Notice::add('Field required: Username');
                    $errorFound = true;
                }

                if (!isset($olab['db_name']) || ($olab['db_name'] == '')){
                    Notice::add('Field required: Database Name');
                    $errorFound = true;
                }

                if (!$errorFound){
                    if (isset($olab['db_port']) && ($olab['db_port'] != '')){
                        $host = $olab['db_host'] . ':' . $olab['db_port'];
                    } else {
                        $host = $olab['db_host'];
                    }

                    $link = @mysql_connect($host, $olab['db_user'], $olab['db_pass']);

                    if($link == false){
                        Notice::add('An error occurred while trying connect to the database.');
                        $errorFound = true;
                    }
                }

                Session::set('installationDatabase', json_encode($olab));
                if (!$errorFound){
                    Session::set('installationStep', '4');
                }
            }
        }
        Installation::redirect(URL::base() . 'installation/index.php');
    }

    public static function action_systemOverview(){
        $token = Arr::get($_POST, 'token', NULL);
        if (Security::check($token)) {
            $errorFound = false;
            if (!Installation::getPreCheckResult(true)){
                Notice::add('Pre-installation check is not passed.');
                $errorFound = true;
            }

            if (!Installation::getFileObjectsResult(true)){
                Notice::add('Access to file system objects is not passed.');
                $errorFound = true;
            }

            if (!$errorFound){
                $baseUrl = URL::base();
                if ($baseUrl != '/'){
                    $content = '';
                    $handle = fopen(DOCROOT . 'application/bootstrap.php', 'r');
                    while (($buffer = fgets($handle)) !== false) {
                        $content .= $buffer;
                    }

                    $content = str_replace("'base_url' => '/',", "'base_url' => '".$baseUrl."',", $content);
                    file_put_contents(DOCROOT . 'application/bootstrap.php', $content);
                }
                Session::set('installationStep', '2');
            }
        }
        Installation::redirect(URL::base() . 'installation/index.php');
    }

    public static function action_overview(){
        $token = Arr::get($_POST, 'token', NULL);
        if (Security::check($token)) {
            $errorFound = false;
            if (!Installation::getPreCheckResult(true)){
                Notice::add('Pre-installation check is not passed.');
                $errorFound = true;
            }

            if (!Installation::getFileObjectsResult(true)){
                Notice::add('Access to file system objects is not passed.');
                $errorFound = true;
            }

            if (!$errorFound){
                Session::set('installationStep', '5');
            }
        }
        Installation::redirect(URL::base() . 'installation/index.php');
    }

    public static function action_previousStep(){
        $token = Arr::get($_POST, 'token', NULL);
        if (Security::check($token)) {
            $stepIndex = Session::get('installationStep', '1');
            if ($stepIndex > 1){
                $stepIndex--;
                Session::set('installationStep', $stepIndex);
            }
        }
        Installation::redirect(URL::base() . 'installation/index.php');
    }

    public static function getPreCheckResult($returnStatus = false){
        $array = array();
        $status = true;
        if (version_compare(PHP_VERSION, '5.3', '<')){
            // Clear out the cache to prevent errors. This typically happens on Windows/FastCGI.
            clearstatcache();
        } else {
            // Clearing the realpath() cache is only possible PHP 5.3+
            clearstatcache(TRUE);
        }

        $temp['item'] = 'PHP 5.3.3 or newer';
        if (version_compare(PHP_VERSION, '5.3.3', '>=')){
            $temp['label'] = 'success';
            $temp['status'] = 'Yes';
        } else {
            $temp['label'] = 'important';
            $temp['status'] = 'No';
            $status = false;
        }
        $array[] = $temp;

        $temp['item'] = 'Mod-rewrite';
        if (in_array('mod_rewrite', apache_get_modules())){
            $temp['label'] = 'success';
            $temp['status'] = 'Yes';
        } else {
            $temp['label'] = 'important';
            $temp['status'] = 'No';
            $status = false;
        }
        $array[] = $temp;


        $temp['item'] = 'System Directory';
        if (is_dir(SYSPATH) AND is_file(SYSPATH.'classes/kohana'.EXT)){
            $temp['label'] = 'success';
            $temp['status'] = 'Yes';
        } else {
            $temp['label'] = 'important';
            $temp['status'] = 'No';
            $status = false;
        }
        $array[] = $temp;

        $temp['item'] = 'Application Directory';
        if (is_dir(APPPATH) AND is_file(APPPATH.'bootstrap'.EXT)){
            $temp['label'] = 'success';
            $temp['status'] = 'Yes';
        } else {
            $temp['label'] = 'important';
            $temp['status'] = 'No';
            $status = false;
        }
        $array[] = $temp;

        $temp['item'] = 'PCRE UTF-8';
        if ( ! @preg_match('/^.$/u', 'ñ')){
            $temp['label'] = 'important';
            $temp['status'] = 'No';
            $status = false;
        } elseif ( ! @preg_match('/^\pL$/u', 'ñ')){
            $temp['label'] = 'important';
            $temp['status'] = 'No';
            $status = false;
        } else {
            $temp['label'] = 'success';
            $temp['status'] = 'Yes';
        }
        $array[] = $temp;

        $temp['item'] = 'SPL Enabled';
        if (function_exists('spl_autoload_register')){
            $temp['label'] = 'success';
            $temp['status'] = 'Yes';
        } else {
            $temp['label'] = 'important';
            $temp['status'] = 'No';
            $status = false;
        }
        $array[] = $temp;

        $temp['item'] = 'Reflection Enabled';
        if (class_exists('ReflectionClass')){
            $temp['label'] = 'success';
            $temp['status'] = 'Yes';
        } else {
            $temp['label'] = 'important';
            $temp['status'] = 'No';
            $status = false;
        }
        $array[] = $temp;

        $temp['item'] = 'Filters Enabled';
        if (function_exists('filter_list')){
            $temp['label'] = 'success';
            $temp['status'] = 'Yes';
        } else {
            $temp['label'] = 'important';
            $temp['status'] = 'No';
            $status = false;
        }
        $array[] = $temp;

        $temp['item'] = 'Iconv Extension Loaded';
        if (extension_loaded('iconv')){
            $temp['label'] = 'success';
            $temp['status'] = 'Yes';
        } else {
            $temp['label'] = 'important';
            $temp['status'] = 'No';
            $status = false;
        }
        $array[] = $temp;

        if (extension_loaded('mbstring')){
            $temp['item'] = 'Mbstring Not Overloaded';
            if (ini_get('mbstring.func_overload') & MB_OVERLOAD_STRING){
                $temp['label'] = 'important';
                $temp['status'] = 'No';
                $status = false;
            } else {
                $temp['label'] = 'success';
                $temp['status'] = 'Yes';
            }
            $array[] = $temp;
        }

        $temp['item'] = 'Character Type (CTYPE) Extension';
        if ( ! function_exists('ctype_digit')){
            $temp['label'] = 'important';
            $temp['status'] = 'No';
            $status = false;
        } else {
            $temp['label'] = 'success';
            $temp['status'] = 'Yes';
        }
        $array[] = $temp;

        $temp['item'] = 'URI Determination';
        if (isset($_SERVER['REQUEST_URI']) OR isset($_SERVER['PHP_SELF']) OR isset($_SERVER['PATH_INFO'])){
            $temp['label'] = 'success';
            $temp['status'] = 'Yes';
        } else {
            $temp['label'] = 'important';
            $temp['status'] = 'No';
            $status = false;
        }
        $array[] = $temp;

        return ($returnStatus) ? $status : $array;
    }

    public static function getFileObjectsResult($returnStatus = false){
        $array = array();
        $status = true;

        $temp['item'] = URL::base().'application/bootstrap.php';
        if (is_dir(APPPATH) AND is_writable(APPPATH.'bootstrap.php')){
            $temp['label'] = 'success';
            $temp['status'] = 'Writable';
        } else {
            $temp['label'] = 'important';
            $temp['status'] = 'Not writable';
            $status = false;
        }
        $array[] = $temp;

        $temp['item'] = URL::base().'install.php';
        if (is_writable(DOCROOT.'install.php')){
            $temp['label'] = 'success';
            $temp['status'] = 'Writable';
        } else {
            $temp['label'] = 'important';
            $temp['status'] = 'Not writable';
            $status = false;
        }
        $array[] = $temp;

        $temp['item'] = URL::base().'installation';
        if (is_dir(DOCROOT.'installation') AND is_writable(DOCROOT.'installation')){
            $temp['label'] = 'success';
            $temp['status'] = 'Writable';
        } else {
            $temp['label'] = 'important';
            $temp['status'] = 'Not writable';
            $status = false;
        }
        $array[] = $temp;

        $temp['item'] = URL::base().'application/cache';
        if (is_dir(APPPATH) AND is_dir(APPPATH.'cache') AND is_writable(APPPATH.'cache')){
            $temp['label'] = 'success';
            $temp['status'] = 'Writable';
        } else {
            $temp['label'] = 'important';
            $temp['status'] = 'Not writable';
            $status = false;
        }
        $array[] = $temp;

        $temp['item'] = URL::base().'application/logs';
        if (is_dir(APPPATH) AND is_dir(APPPATH.'logs') AND is_writable(APPPATH.'logs')){
            $temp['label'] = 'success';
            $temp['status'] = 'Writable';
        } else {
            $temp['label'] = 'important';
            $temp['status'] = 'Not writable';
            $status = false;
        }
        $array[] = $temp;

        $temp['item'] = URL::base().'application/config';
        if (is_dir(APPPATH) AND is_dir(APPPATH.'config') AND is_writable(APPPATH.'config')){
            $temp['label'] = 'success';
            $temp['status'] = 'Writable';
        } else {
            $temp['label'] = 'important';
            $temp['status'] = 'Not writable';
            $status = false;
        }
        $array[] = $temp;

        $temp['item'] = URL::base().'avatars';
        if (is_dir(DOCROOT.'avatars') AND is_writable(DOCROOT.'avatars')){
            $temp['label'] = 'success';
            $temp['status'] = 'Writable';
        } else {
            $temp['label'] = 'important';
            $temp['status'] = 'Not writable';
            $status = false;
        }
        $array[] = $temp;

        $temp['item'] = URL::base().'css/skin';
        if (is_dir(DOCROOT.'css/skin') AND is_writable(DOCROOT.'css/skin')){
            $temp['label'] = 'success';
            $temp['status'] = 'Writable';
        } else {
            $temp['label'] = 'important';
            $temp['status'] = 'Not writable';
            $status = false;
        }
        $array[] = $temp;

        $temp['item'] = URL::base().'files';
        if (is_dir(DOCROOT.'files') AND is_writable(DOCROOT.'files')){
            $temp['label'] = 'success';
            $temp['status'] = 'Writable';
        } else {
            $temp['label'] = 'important';
            $temp['status'] = 'Not writable';
            $status = false;
        }
        $array[] = $temp;

        $temp['item'] = URL::base().'scripts/fileupload';
        if (is_dir(DOCROOT.'scripts/fileupload') AND is_writable(DOCROOT.'scripts/fileupload')){
            $temp['label'] = 'success';
            $temp['status'] = 'Writable';
        } else {
            $temp['label'] = 'important';
            $temp['status'] = 'Not writable';
            $status = false;
        }
        $array[] = $temp;

        $temp['item'] = URL::base().'tmp';
        if (is_dir(DOCROOT.'tmp') AND is_writable(DOCROOT.'tmp')){
            $temp['label'] = 'success';
            $temp['status'] = 'Writable';
        } else {
            $temp['label'] = 'important';
            $temp['status'] = 'Not writable';
            $status = false;
        }
        $array[] = $temp;

        $temp['item'] = URL::base().'updates';
        if (is_dir(DOCROOT.'updates') AND is_writable(DOCROOT.'updates')){
            $temp['label'] = 'success';
            $temp['status'] = 'Writable';
        } else {
            $temp['label'] = 'important';
            $temp['status'] = 'Not writable';
            $status = false;
        }
        $array[] = $temp;

        return ($returnStatus) ? $status : $array;
    }

    public static function getRecommendedResult(){
        $array = array();

        $temp['item'] = 'PECL HTTP';
        $temp['label'] = 'success';
        $temp['status'] = 'On';
        if (extension_loaded('http')){
            $temp['ac-label'] = 'success';
            $temp['ac-status'] = 'On';
        } else {
            $temp['ac-label'] = 'warning';
            $temp['ac-status'] = 'Off';
        }
        $array[] = $temp;

        $temp['item'] = 'cURL';
        $temp['label'] = 'success';
        $temp['status'] = 'On';
        if (extension_loaded('curl')){
            $temp['ac-label'] = 'success';
            $temp['ac-status'] = 'On';
        } else {
            $temp['ac-label'] = 'warning';
            $temp['ac-status'] = 'Off';
        }
        $array[] = $temp;

        $temp['item'] = 'mcrypt';
        $temp['label'] = 'success';
        $temp['status'] = 'On';
        if (extension_loaded('mcrypt')){
            $temp['ac-label'] = 'success';
            $temp['ac-status'] = 'On';
        } else {
            $temp['ac-label'] = 'warning';
            $temp['ac-status'] = 'Off';
        }
        $array[] = $temp;

        $temp['item'] = 'GD';
        $temp['label'] = 'success';
        $temp['status'] = 'On';
        if (function_exists('gd_info')){
            $temp['ac-label'] = 'success';
            $temp['ac-status'] = 'On';
        } else {
            $temp['ac-label'] = 'warning';
            $temp['ac-status'] = 'Off';
        }
        $array[] = $temp;

        $temp['item'] = 'MySQL';
        $temp['label'] = 'success';
        $temp['status'] = 'On';
        if (function_exists('mysql_connect')){
            $temp['ac-label'] = 'success';
            $temp['ac-status'] = 'On';
        } else {
            $temp['ac-label'] = 'warning';
            $temp['ac-status'] = 'Off';
        }
        $array[] = $temp;

        $temp['item'] = 'PDO';
        $temp['label'] = 'success';
        $temp['status'] = 'On';
        if (class_exists('PDO')){
            $temp['ac-label'] = 'success';
            $temp['ac-status'] = 'On';
        } else {
            $temp['ac-label'] = 'warning';
            $temp['ac-status'] = 'Off';
        }
        $array[] = $temp;

        $temp['item'] = 'Memory limit';
        $temp['label'] = 'success';
        $temp['status'] = '1024M';
        $limit = ini_get('memory_limit');
        if ($limit >= 1024){
            $temp['ac-label'] = 'success';
            $temp['ac-status'] = $limit;
        } else {
            $temp['ac-label'] = 'warning';
            $temp['ac-status'] = $limit;
        }
        $array[] = $temp;

        $temp['item'] = 'Max execution time (in seconds)';
        $temp['label'] = 'success';
        $temp['status'] = '300';
        $limit = ini_get('max_execution_time');
        if ($limit >= 300){
            $temp['ac-label'] = 'success';
            $temp['ac-status'] = $limit;
        } else {
            $temp['ac-label'] = 'warning';
            $temp['ac-status'] = $limit;
        }
        $array[] = $temp;

        $temp['item'] = 'Upload max filesize';
        $temp['label'] = 'success';
        $temp['status'] = '10M';
        $limit = ini_get('upload_max_filesize');
        if ($limit >= 10){
            $temp['ac-label'] = 'success';
            $temp['ac-status'] = $limit;
        } else {
            $temp['ac-label'] = 'warning';
            $temp['ac-status'] = $limit;
        }
        $array[] = $temp;

        return $array;
    }

    public static function redirect($url){
        header('Location: '.$url);
        exit;
    }

    public static function proceed(){
        $checkBaseUrl = URL::base();
        $olab = json_decode(Session::get('installationDatabase'), true);
        if (isset($olab['db_port']) && ($olab['db_port'] != '')){
            $host = $olab['db_host'] . ':' . $olab['db_port'];
        } else {
            $host = $olab['db_host'];
        }

        $link = mysql_connect($host, $olab['db_user'], $olab['db_pass']);

        $db_selected = mysql_select_db($olab['db_name']);
        if ($db_selected) {
            if ($olab['db_old'] == 'backup'){
                $rand = rand();
                $query = "SELECT concat('RENAME TABLE ".$olab['db_name'].".',table_name, ' TO " . $olab['db_name'] . "_" . $rand . ".',table_name, ';') as string_query FROM information_schema.TABLES WHERE table_schema='".$olab['db_name']."';";
                mysql_query('CREATE DATABASE ' . $olab['db_name'] . "_" . $rand);
                $responce = mysql_query($query) or die(mysql_error());
                while($result = mysql_fetch_array($responce)){
                    mysql_query($result['string_query']);
                }
            }
        }
        mysql_query('DROP DATABASE IF EXISTS '.$olab['db_name']);
        mysql_query('CREATE DATABASE '.$olab['db_name']);
        mysql_select_db($olab['db_name']);

        Installation::populateDatabase(INST_DOCROOT.'sql/CreateDB.sql');
        Installation::runUpdates();

        $olabUser = json_decode(Session::get('installationConfiguration'), true);

        $password = hash_hmac('sha256', $olabUser['admin_password'], '1, 3, 4, 6, 9, 13, 17, 20, 25, 30, 32, 40, 61');

        $query = "INSERT INTO `users` (`id`, `username`, `password`, `email`, `nickname`, `language_id`, `type_id`) VALUES
(1, '" . $olabUser['admin_user'] . "', '" . $password . "', '" . $olabUser['admin_email'] . "', 'administrator', 1, 4);";
        mysql_query($query);

        $databaseConfig = '$config = array();
        $config["default"] = array(
            "type"          => "mysql",
            "driver"        => "standard",
            "connection"    => array(
                "persistent"    => FALSE,
                "hostname"      => "'.$olab['db_host'].'",
                "port"          => "'.$olab['db_port'].'",
                "database"      => "'.$olab['db_name'].'",
                "username"      => "'.$olab['db_user'].'",
                "password"      => "'.$olab['db_pass'].'",
            ),
            "caching"       => FALSE,
            "charset"       => "utf8",
            "profiling"     => FALSE,
            "table_prefix"  => "",
        );

        return $config;';

        $content = '';
        $handle = fopen(DOCROOT . 'application/config/database.php', 'r');
        while (($buffer = fgets($handle)) !== false) {
            $content .= $buffer;
        }

        $position = strpos($content, '$config = array();');
        $header = substr($content, 0, $position);

        file_put_contents(DOCROOT . 'application/config/database.php', $header . $databaseConfig);

        Installation::terminate();
    }

    public static function deleteDir($dirPath) {
        if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
            $dirPath .= '/';
        }
        $files = glob($dirPath . '*', GLOB_MARK);
        foreach ($files as $file) {
            if (is_dir($file)) {
                self::deleteDir($file);
            } else {
                unlink($file);
            }
        }
        rmdir($dirPath);
    }

    public static function runUpdates(){
        $result = 0;
        $dir = DOCROOT.'updates/';
        if(is_dir($dir)){
            $files = scandir($dir);
            array_shift($files);
            array_shift($files);
            if (count($files) > 0){
                $infoFile = $dir.'history.json';
                if (!file_exists($infoFile)){
                    if (!is_writable($dir)){
                        return 3;
                    }
                    $infoFileHandler = fopen($infoFile, 'w');
                    $skipFiles = array();
                } else {
                    $fileString = file_get_contents($infoFile);
                    $skipFiles = json_decode($fileString, true);
                }

                if (count($files) > 0){
                    usort($files, array('Installation', 'sortVersionInOrder'));
                    foreach($files as $f){
                        $ext = pathinfo($f, PATHINFO_EXTENSION);
                        if ($ext == 'sql'){
                            $pathToFile = $dir.$f;
                            if (!isset($skipFiles[$f])){
                                Installation::populateDatabase($pathToFile);
                                $skipFiles[$f] = 1;
                                $result = 1;
                            }
                            @unlink($pathToFile);
                        }
                    }
                }

                file_put_contents($infoFile, json_encode($skipFiles));
            }
        } else {
            return 2;
        }

        return $result;
    }

    public static function sortVersionInOrder($a, $b) {
        $ext = pathinfo($a, PATHINFO_EXTENSION);
        if ($ext != 'sql'){
            return -1;
        }

        $ext = pathinfo($b, PATHINFO_EXTENSION);
        if ($ext != 'sql'){
            return 1;
        }

        $regExp = '/(?<=v)(.*?)(?=\.sql)/is';
        $regExpDot = '/(\.|_)/e';
        $resultA = '';
        $resultB = '';

        if ($c=preg_match_all ($regExp, $a, $matches)) {
            if (isset($matches[0][0])) {
                $found = 0;
                $resultA = preg_replace($regExpDot, '$found++ ? \'\' : \'$1\'', $matches[0][0]);
            }
        }

        if ($c=preg_match_all ($regExp, $b, $matches)) {
            if (isset($matches[0][0])) {
                $found = 0;
                $resultB = preg_replace($regExpDot, '$found++ ? \'\' : \'$1\'', $matches[0][0]);
            }
        }

        if ($resultA == $resultB) {
            return 0;
        }

        return ($resultA-$resultB > 0) ? 1 : -1;
    }

    public static function populateDatabase($schema)
    {
        $return = true;

        // Get the contents of the schema file.
        if (!($buffer = file_get_contents($schema)))
        {
            return false;
        }

        // Get an array of queries from the schema and process them.
        $queries = Installation::_splitQueries($buffer);

        foreach ($queries as $query)
        {
            // Trim any whitespace.
            $query = trim($query);

            // If the query isn't empty and is not a MySQL or PostgreSQL comment, execute it.
            if (!empty($query) && ($query{0} != '#') && ($query{0} != '-'))
            {
                // Execute the query.
                $result = mysql_query($query) or die(mysql_error());

                if (!$result){
                    $return = false;
                }
            }
        }

        return $return;
    }

    public static function _splitQueries($sql)
    {
        $buffer    = array();
        $queries   = array();
        $in_string = false;

        // Trim any whitespace.
        $sql = trim($sql);

        // Remove comment lines.
        $sql = preg_replace("/\n\#[^\n]*/", '', "\n" . $sql);

        // Remove PostgreSQL comment lines.
        $sql = preg_replace("/\n\--[^\n]*/", '', "\n" . $sql);

        // find function
        $funct = explode('CREATE OR REPLACE FUNCTION', $sql);
        // save sql before function and parse it
        $sql = $funct[0];

        // Parse the schema file to break up queries.
        for ($i = 0; $i < strlen($sql) - 1; $i++)
        {
            if ($sql[$i] == ";" && !$in_string)
            {
                $queries[] = substr($sql, 0, $i);
                $sql = substr($sql, $i + 1);
                $i = 0;
            }

            if ($in_string && ($sql[$i] == $in_string) && $buffer[1] != "\\")
            {
                $in_string = false;
            }
            elseif (!$in_string && ($sql[$i] == '"' || $sql[$i] == "'") && (!isset ($buffer[0]) || $buffer[0] != "\\"))
            {
                $in_string = $sql[$i];
            }
            if (isset ($buffer[1]))
            {
                $buffer[0] = $buffer[1];
            }
            $buffer[1] = $sql[$i];
        }

        // If the is anything left over, add it to the queries.
        if (!empty($sql))
        {
            $queries[] = $sql;
        }

        // add function part as is
        for ($f = 1; $f < count($funct); $f++)
        {
            $queries[] = 'CREATE OR REPLACE FUNCTION ' . $funct[$f];
        }

        return $queries;
    }

    public static function terminate(){
        if ((is_writable(DOCROOT.'install.php')) AND (is_dir(DOCROOT.'installation') AND is_writable(DOCROOT.'installation'))){
            unlink(DOCROOT . 'install.php');
            Installation::deleteDir(DOCROOT . 'installation');
            $baseUrl = URL::base();
            session_destroy();
            Installation::redirect($baseUrl);
        } else {
            Notice::add('Please make <b><i>install.php</i></b> file and <b><i>installation</i></b> folder writable or delete them by yourself');
            Session::set('installationStep', '1');
            Installation::redirect(URL::base() . 'installation/index.php');
        }
    }
}