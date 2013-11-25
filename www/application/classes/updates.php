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

class Updates
{
    public static function update(){
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
                    usort($files, array('Updates', 'sortVersionInOrder'));
                    foreach($files as $f){
                        $ext = pathinfo($f, PATHINFO_EXTENSION);
                        if ($ext == 'sql'){
                            $pathToFile = $dir.$f;
                            if (!isset($skipFiles[$f])){
                                Updates::populateDatabase($pathToFile);
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
        $queries = Updates::_splitQueries($buffer);
        if (count($queries) > 0){
            $db = Database::instance('default');
            $db->connect();

            foreach ($queries as $query)
            {
                // Trim any whitespace.
                $query = trim($query);

                // If the query isn't empty and is not a MySQL or PostgreSQL comment, execute it.
                if (!empty($query) && ($query{0} != '#') && ($query{0} != '-'))
                {
                    // Execute the query.

                    $result = @mysql_query($query);

                    if (!$result){
                        $return = false;
                    }
                }
            }
            $db->disconnect();
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
}