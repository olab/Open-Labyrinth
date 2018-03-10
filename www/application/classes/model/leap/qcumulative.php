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

/**
 * Model for users table in database
 */
class Model_Leap_qCumulative extends DB_ORM_Model {

    public function __construct() {
        parent::__construct();

        $this->fields = array(
            'id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11
            )),

            'question_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 10,
                'unsigned' => TRUE
            )),

            'map_id' => new DB_ORM_Field_String($this, array(
                'max_length' => 10,
                'unsigned' => TRUE
            )),

            'reset' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11
            ))
        );
    }

    public static function data_source() {
        return 'default';
    }

    public static function table() {
        return 'q_cumulative';
    }

    public static function primary_key() {
        return array('id');
    }

    public function setResetByScenario($scenarioId)
    {
        $mapsId = DB_ORM::model('webinar_map')->getMapsId($scenarioId);
        foreach ($mapsId as $mapId) {
            $this->setResetByMap($mapId);
        }
    }

    public function setResetByMap($mapId)
    {
        $questions = DB_ORM::select('Map_Question')->where('map_id', '=', $mapId)->where('entry_type_id', '=', 9)->query()->as_array();
        foreach ($questions as $question) {
            $reset          = time();
            $questionId     = $question->id;
            $alreadyReset   = DB_ORM::select('qCumulative')->where('question_id', '=', $questionId)->where('map_id', '=', $mapId)->query()->fetch();

            if ($alreadyReset) {
                $alreadyReset->reset = $reset;
                $alreadyReset->save();
            } else {
                $newRecord = new $this;
                $newRecord->question_id = $questionId;
                $newRecord->map_id = $mapId;
                $newRecord->reset = $reset;
                $newRecord->save();
            }
        }
    }

    public function getAnswers($mapId, $questionId, $sessionId)
    {
        $responsesSQL = DB_SQL::select('default')
            ->from('user_sessions', 's')
            ->join('LEFT', 'user_responses', 'r')
            ->on('s.id', '=', 'r.session_id')
            ->join('LEFT', 'q_cumulative', 'c')
            ->on('s.map_id', '=', 'c.map_id')
            ->on('s.start_time', '>=', 'c.reset')
            ->where('s.map_id', '=', $mapId)
            ->where('s.id', '<', $sessionId)
            ->where('r.question_id', '=', $questionId)
            ->query()
            ->as_array();

        $responses = array();
        foreach ($responsesSQL as $response) {
            if (isset($response['response'])) {
                $responses[$response['question_id']][] = $response['response'];
            }
        }

        return $responses;
    }
}