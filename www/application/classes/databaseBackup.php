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

class DatabaseBackup
{
    public  $host = '',
            $username = '',
            $passwd = '',
            $dbName = '',
            $charset = 'utf8';

    /**
     * Constructor initializes database
     */
    /*function __construct($host, $username, $passwd, $dbName, $charset = 'utf8')
    {
        $this->host = $host;
        $this->username = $username;
        $this->passwd = $passwd;
        $this->dbName = $dbName;
        $this->charset = $charset;

        $this->initializeDatabase();
    }

    protected function initializeDatabase()
    {
        $conn = mysql_connect($this->host, $this->username, $this->passwd);
        mysql_select_db($this->dbName, $conn);
        if (!mysql_set_charset($this->charset, $conn)) {
            mysql_query('SET NAMES ' . $this->charset);
        }
    }*/

    /**
     * Backup the whole database or just some tables
     * Use '*' for whole database or 'table1, table2, table3'
     * @param string $tables
     */
    public function backupTables($tables = '*')
    {
        set_time_limit(0);
        try {

            if ($tables == '*') {
                $tables = array();
                $result = mysql_query('SHOW TABLES');
                while ($row = mysql_fetch_row($result)) {
                    $tables[] = $row[0];
                }
            } else {
                $tables = is_array($tables) ? $tables : explode(',', $tables);
            }

            $sql = 'SET FOREIGN_KEY_CHECKS = 0;'."\n\n";

            //$sql = 'CREATE DATABASE IF NOT EXISTS ' . $this->dbName . ";\n\n";
            //$sql .= 'USE ' . $this->dbName . ";\n\n";

            foreach ($tables as $table) {

                $result = mysql_query('SELECT * FROM ' . $table);
                $numFields = mysql_num_fields($result);

                //$sql .= 'DROP TABLE IF EXISTS ' . $table . ';';
                $row2 = mysql_fetch_row(mysql_query('SHOW CREATE TABLE ' . $table));
                $sql .= "\n" . $row2[1] . ";\n";

                for ($i = 0; $i < $numFields; $i++) {
                    while ($row = mysql_fetch_row($result)) {
                        $sql .= 'INSERT INTO ' . $table . ' VALUES(';
                        for ($j = 0; $j < $numFields; $j++) {
                            $row[$j] = addslashes($row[$j]);
                            $row[$j] = str_replace("\n", "\\n", $row[$j]);
                            if (isset($row[$j])) {
                                $sql .= '"' . $row[$j] . '"';
                            } else {
                                $sql .= '""';
                            }

                            if ($j < ($numFields - 1)) {
                                $sql .= ',';
                            }
                        }

                        $sql .= ");\n";
                    }
                }
            }
        } catch (Exception $e) {
            var_dump($e->getMessage());
            return false;
        }

        return $sql;
    }

    /**
     * Save SQL to file
     * @param string $sql
     * @param string $outputDir
     */
    public function saveFile($sql, $outputDir = '.')
    {
        if (empty($sql)) return false;

        try {
            $fileName = $outputDir . '/db-backup-' . $this->dbName . '-' . date("Ymd-His", time()) . '.sql';
            return (file_put_contents($fileName, $sql) !== false);
        } catch (Exception $e) {
            var_dump($e->getMessage());
            return false;
        }
    }
}