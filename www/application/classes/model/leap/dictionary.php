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

class Model_Leap_Dictionary extends DB_ORM_Model {

    public function __construct() {
        parent::__construct();

        $this->fields = array(
            'id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
                'unsigned' => TRUE,
            )),

            'word' => new DB_ORM_Field_String($this, array(
                'max_length' => 50,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
        );
    }

    public static function data_source() {
        return 'default';
    }

    public static function table() {
        return 'dictionary';
    }

    public static function primary_key() {
        return array('id');
    }

    public function addWord($term){
        $db = Database::instance('default');
        $db->connect();
        $query = "INSERT IGNORE INTO `dictionary` (`word`) VALUE ('".$term."');";
        mysql_query($query) or die(mysql_error());
        $db->disconnect();
    }

    public function updateWord($term){
        if ($term['delete'] == 1){
            DB::delete('dictionary')->where('id', '=', $term['id'])->execute();
        }else{
            DB::update('dictionary')->set(array('word'=>$term['word']))->where('id', '=', $term['id'])->execute();
        }
    }

    public function getWordsName($term) {
        $builder = DB_SQL::select('default')->from($this->table())->where('word', 'like', $term.'%');
        $result = $builder->query();

        if($result->is_loaded()) {
            $array = array();
            foreach($result as $record) {
                $array[] = $record['word'];
            }
            return $array;
        }
        return NULL;
    }

    public function getWords($term) {
        $builder = DB_SQL::select('default')->from($this->table())->where('word', 'like', $term.'%');
        $result = $builder->query();

        if($result->is_loaded()) {
            $array = array();
            foreach($result as $record) {
                $array[] = $record;
            }
            return $array;
        }
        return NULL;
    }

    public function uploadFile($file) {
        if(is_uploaded_file($file['filename']['tmp_name'])) {
            $file = $file['filename']['tmp_name'];

            $handle = fopen($file, "r");
            $buffer = fgets($handle);
            $encoding = mb_detect_encoding($buffer);
            rewind($handle);

            $insert = array();
            while (($buffer = fgets($handle)) !== false) {
                $buffer = mb_convert_encoding($buffer, "UTF-8", $encoding);
                $buffer = preg_replace('/[^\w\s.\'-]|[\\r\\n?|\\n]/u', '', $buffer);
                if(!empty($buffer)){
                    $insert[] = "('".str_replace("'", "''", $buffer)."')";
                }
            }
            $db = Database::instance('default');
            $db->connect();
            $partsOfInsert = array_chunk($insert, 50000);
            foreach($partsOfInsert as $array){
                $query = 'INSERT IGNORE INTO `dictionary` (`word`) VALUES '.implode(', ', $array).';';
                mysql_query($query) or die(mysql_error());
            }
            $db->disconnect();
            fclose($handle);
            unlink($file);
            return true;
        }else{
            return false;
        }
    }
}