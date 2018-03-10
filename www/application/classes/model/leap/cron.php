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
require_once(DOCROOT.'cron/mysqli.php');
require_once(DOCROOT.'cron/cronRules.php');

/**
 * Model for map_nodes table in database
 */
class Model_Leap_Cron extends DB_ORM_Model {

    public function __construct() {
        parent::__construct();

        $this->fields = array(
            'id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            'rule_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            'activate' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => TRUE,
            )),
        );
    }

    public static function data_source() {
        return 'default';
    }

    public static function table() {
        return 'cron';
    }

    public static function primary_key() {
        return array('id');
    }

    public function add($ruleId)
    {
        $record = new $this;
        $record->rule_id = $ruleId;
        $record->save();
    }

    public function deleteRecord($id)
    {
        $record = new $this;
        $record->id = $id;
        $record->delete();
    }

    /**
     * If you changed this method, you must change the similar method in 'cron' file.
     * @param $string
     * @return mixed
     */
    public function parseRule($mapId)
    {
        $scenarioId = Controller_RenderLabyrinth::$scenarioId;
        if ($scenarioId) {
            $mysqli = new mysqliConnection();
            $connection = $mysqli->connect();
            new cronRules($connection, $mapId);
            $mysqli->closeConnect($connection);
        }
    }

    /**
     * @param $string - string which need to change for method replaceConditions in class runTimeLogic
     * @return mixed
     */
    public function replaceConditions($string) {
        $pattern = '\s*ALERT\([^\)]*\),|\s*(ACTIVATE|DEACTIVATE)\s*\(\[\[STEP:\d+\]\],.*\)\s*,[^;]*';
        if (preg_match_all("/".$pattern."/is", $string, $matches) AND count($matches[0])){
            foreach($matches[0] as $match){
                $string = str_replace($match, ' [[CR:0]]', $string);
            }
        }
        return $string;
    }
}