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
 * @property int $id
 * @property int $user_id
 * @property int $map_id
 * @property int $start_time
 * @property int $end_time
 * @property int $webinar_id
 * @property int $webinar_step
 * @property bool $notCumulative
 * @property string $user_ip
 * @property Model_Leap_User $user
 * @property Model_Leap_User_SessionTrace[]|DB_ResultSet $traces
 * @property Model_Leap_Map $map
 * @property Model_Leap_User_Response[]|DB_ResultSet $responses
 * @property Model_Leap_Statement[]|DB_ResultSet $statements
 */
class Model_Leap_User_Session extends Model_Leap_Base
{

    public function __construct()
    {
        parent::__construct();

        $this->fields = array(
            'id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 10,
                'nullable' => false,
            )),
            'user_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 10,
                'nullable' => false,
                'unsigned' => true,
            )),
            'map_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 10,
                'nullable' => false,
                'unsigned' => true,
            )),
            'start_time' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => false,
            )),
            'end_time' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => true,
            )),
            'user_ip' => new DB_ORM_Field_String($this, array(
                'max_length' => 50,
                'nullable' => false,
                'savable' => true,
            )),
            'webinar_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => true,
                'savable' => true
            )),
            'webinar_step' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => true,
                'savable' => true
            )),
            'notCumulative' => new DB_ORM_Field_Boolean($this, array(
                'nullable' => false,
                'savable' => true
            ))
        );

        $this->relations = array(
            'user' => new DB_ORM_Relation_BelongsTo($this, array(
                'child_key' => array('user_id'),
                'parent_key' => array('id'),
                'parent_model' => 'user',
            )),
            'traces' => new DB_ORM_Relation_HasMany($this, array(
                'child_key' => array('session_id'),
                'child_model' => 'user_sessionTrace',
                'parent_key' => array('id'),
                'options' => array(array('order_by', array('date_stamp', 'ASC')),),
            )),
            'map' => new DB_ORM_Relation_BelongsTo($this, array(
                'child_key' => array('map_id'),
                'parent_key' => array('id'),
                'parent_model' => 'map',
            )),
            'responses' => new DB_ORM_Relation_HasMany($this, array(
                'child_key' => array('session_id'),
                'child_model' => 'user_response',
                'parent_key' => array('id'),
            )),
            'statements' => new DB_ORM_Relation_HasMany($this, array(
                'child_key' => array('session_id'),
                'child_model' => 'statement',
                'parent_key' => array('id'),
            )),
        );
    }

    public static function data_source()
    {
        return 'default';
    }

    public static function table()
    {
        return 'user_sessions';
    }

    public static function primary_key()
    {
        return array('id');
    }


    public static function getAdminBaseUrl()
    {
        return URL::base(true) . 'reportManager/showReport/';
    }


    public function toxAPIExtensionObject()
    {
        $result = $this->as_array();
        $result['id'] = static::getAdminBaseUrl() . $this->id;
        $result['internal_id'] = $this->id;
        unset($result['user_ip']);

        return $result;
    }

    /**
     * @return Model_Leap_User_SessionTrace
     */
    public function getLatestTrace()
    {
        return Model_Leap_User_SessionTrace::getLatestBySession($this->id);
    }

    /**
     * @return int
     */
    public static function countTraces($session_id)
    {
        $result = DB_SQL::select()
            ->from(Model_Leap_User_SessionTrace::table())
            ->where('session_id', '=', $session_id)
            ->column(DB_SQL::expr("COUNT(*)"), 'counter')
            ->query();

        return (int)$result[0]['counter'];
    }

    /**
     * @param Model_Leap_User_Session[]|DB_ResultSet $sessions
     */
    public static function sendSessionsToLRS($sessions)
    {
        /** @var Model_Leap_User_Session[] $sessions_array */
        $sessions_array = $sessions->as_array();
        unset($sessions);
        foreach ($sessions_array as $key => $session) {
            unset($sessions_array[$key]);
            static::createSessionStatements($session);
            $session->sendXAPIStatements();
        }
    }

    /**
     * @param Model_Leap_User_Session $session
     */
    public static function createSessionStatements(Model_Leap_User_Session $session)
    {
        //create responses statements
        $responses = $session->responses;
        foreach ($responses as $response) {
            $response->createXAPIStatement();
        }
        unset($responses, $session->responses);
        //end create responses statements

        /** @var Model_Leap_User_SessionTrace[] $session_traces_array */
        $session_traces_array = $session->traces->as_array();
        unset($session);

        if (count($session_traces_array) > 0) {

            usort($session_traces_array, function ($a, $b) {
                $al = (int)$a->id;
                $bl = (int)$b->id;
                if ($al == $bl) {
                    return 0;
                }

                return ($al > $bl) ? +1 : -1;
            });

            $session_traces_array[0]->createXAPIStatementInitialized();

            foreach ($session_traces_array as $key => $session_trace) {
                $session_trace->createXAPIStatementArrived();
                $session_trace->createXAPIStatementLaunched();
                $session_trace->createXAPIStatementSuspended();
                $session_trace->createXAPIStatementResumed();

                if (isset($session_traces[$key - 1])) {
                    $session_trace->createXAPIStatementUpdated($session_traces[$key - 1]);
                }

                if (!isset($session_traces[$key + 1])) {
                    $session_trace->createXAPIStatementCompleted();
                }

                unset($session_trace, $session_traces_array[$key]);
            }
        }
    }

    public function sendXAPIStatements()
    {
        /** @var Model_Leap_LRSStatement[] $lrs_statements */
        $lrs_statements = DB_ORM::select('LRSStatement')
            ->join('INNER', 'statements')->on('lrs_statement.statement_id', '=', 'statements.id')
            ->where('statements.session_id', '=', $this->id)
            ->where('lrs_statement.status', '=', Model_Leap_LRSStatement::STATUS_NEW)
            ->query();

        foreach ($lrs_statements as $lrs_statement) {
            $lrs_statement->sendAndSave();
        }
    }

    public function createSession($userId, $mapId, $startTime, $userIp, $webinarId = null, $webinarStep = null)
    {
        $builder = DB_ORM::insert('user_session')
            ->column('user_id', $userId)
            ->column('map_id', $mapId)
            ->column('start_time', $startTime)
            ->column('user_ip', $userIp)
            ->column('webinar_id', $webinarId)
            ->column('webinar_step', $webinarStep);

        return $builder->execute();
    }

    public function getAllSessionByMap($mapId)
    {
        $result = DB_SQL::select('default')->from($this->table())->where('map_id', '=', $mapId)->order_by('start_time',
            'DESC')->query();

        if ($result->is_loaded()) {
            $sessions = array();
            foreach ($result as $record) {
                $sessions[] = DB_ORM::model('user_session', array((int)$record['id']));
            }

            return $sessions;
        }

        return array();
    }

    public function getAllSessionByUser($userId, $limit = 0)
    {
        $result = DB_ORM::select('User_Session')->where('user_id', '=', $userId)->order_by('start_time', 'DESC');
        if ($limit) {
            $result->limit($limit);
        }

        return $result->query()->as_array();
    }

    public function getStartTimeSessionById($sessionId)
    {
        $result = DB_SQL::select('default')
            ->from($this->table())
            ->where('id', '=', $sessionId)
            ->query();

        if ($result->is_loaded()) {
            return $result[0]['start_time'];
        }

        return null;
    }

    public function getSessionByUserMapIDs($userId, $mapId, $webinarId = null, $currentStep = null)
    {
        $builder = DB_ORM::select('User_Session')
            ->where('user_id', '=', $userId)
            ->where('map_id', '=', $mapId)
            ->order_by('start_time', 'DESC');

        if ($webinarId != null) {
            $builder = $builder->where('webinar_id', '=', $webinarId);
        }

        if ($currentStep != null) {
            $builder = $builder->where('webinar_step', '<=', $currentStep);
        }

        return $builder->query()->as_array();
    }

    const USER_NOT_PLAY_MAP = 0;
    const USER_NOT_FINISH_MAP = 1;
    const USER_FINISH_MAP = 2;

    /**
     * Check user finish labyrinth or play it now
     *
     * @param integer $mapId - map Id
     * @param integer $userId - User Id
     * @param integer $webinarId - Webinar Id
     * @returns integer - 0 - not play, 1 - now playing, 2 - finish
     */
    public function isUserFinishMap($id, $userId, $type, $webinarId = null, $currentStep = null)
    {
        $result = Model_Leap_User_Session::USER_NOT_PLAY_MAP;
        $mapId = ($type == 'section') ? DB_ORM::model('Map_Node_Section', array($id))->map_id : $id;
        $sessions = $this->getSessionByUserMapIDs($userId, $mapId, $webinarId, $currentStep);

        if (count($sessions) <= 0) {
            return $result;
        }
        $result = Model_Leap_User_Session::USER_NOT_FINISH_MAP;

        $endNodes = ($type == 'labyrinth')
            ? DB_ORM::model('map_node')->getEndNodesForMap($mapId)
            : DB_ORM::model('Map_Node_Section_Node')->getEndNode($id);

        if ($endNodes) {
            $endNodeIDs = array();
            foreach ($endNodes as $endNode) {
                $endNodeIDs[] = ($type == 'labyrinth') ? $endNode->id : $endNode->node_id;
            }

            $sessionIDs = array();
            foreach ($sessions as $session) {
                $sessionIDs[] = $session->id;
            }

            if (DB_ORM::model('user_sessiontrace')->isExistTrace($userId, $mapId, $sessionIDs, $endNodeIDs)) {
                $result = Model_Leap_User_Session::USER_FINISH_MAP;
            }
        }

        return $result;
    }

    /**
     * Get session IDs by parameters
     *
     * @param integer $mapId - map ID
     * @param integer|null $webinarId - webinar ID
     * @param integer|null $webinarStep - webinar step
     * @param integer|null $notInUsers - not include sessions of susers
     * @return array|null - session ids or null
     */
    public function getSessions(
        $mapId,
        $webinarId = null,
        $webinarStep = null,
        $notInUsers = null,
        $dateStatistics = null
    )
    {
        $builder = DB_SQL::select('default')
            ->from($this->table())
            ->where('map_id', '=', $mapId, 'AND')
            ->column('id');

        if ($webinarId != null && $webinarId > 0) {
            $builder = $builder->where('webinar_id', '=', $webinarId, 'AND');
        }

        if ($webinarStep != null && $webinarStep > 0) {
            $builder = $builder->where('webinar_step', '=', $webinarStep, 'AND');
        }

        if ($notInUsers != null && count($notInUsers) > 0) {
            $builder = $builder->where('user_id', 'NOT IN', $notInUsers);
        }

        $records = $builder->query();
        if ($records->is_loaded()) {
            $sessions = array();
            foreach ($records as $record) {
                $sessions[] = $record['id'];
            }

            return $sessions;
        }

        return null;
    }

    /**
     * Delete webinar sessions
     *
     * @param integer $webinarId - webinar ID
     */
    public function deleteWebinarSessions($webinarId)
    {
        if ($webinarId == null || $webinarId <= 0) {
            return;
        }

        DB_SQL::delete('default')
            ->from($this->table())
            ->where('webinar_id', '=', $webinarId)
            ->execute();
    }

    public function getSessionByWebinarId($webinarId, $checkIds)
    {
        $builder = DB_SQL::select('default')
            ->from($this->table())
            ->where('webinar_id', '=', $webinarId, 'AND');

        if (count($checkIds) > 0) {
            $builder->where('id', 'NOT IN', $checkIds);
        }

        $result = $builder->query();

        $builder = DB_SQL::select('default')
            ->from('webinars')
            ->where('id', '=', $webinarId)
            ->column('current_step');
        $step = $builder->query();
        foreach ($step as $current) {
            $current_step = $current['current_step'];
        }

        $res = array();
        $ids = array();
        if ($result->is_loaded()) {
            foreach ($result as $record) {
                $record['webinar_step'] = $current_step;
                $res[] = $record;
                $ids[] = $record['id'];
            }
        }

        return array($res, $ids);
    }

    public function getCounterPerSession($mapId)
    {
        $result = array();

        if (is_numeric($mapId)) {
            $connection = DB_Connection_Pool::instance()->get_connection('default');
            $results = $connection->query('SELECT session_id, counters FROM `user_sessiontraces` WHERE map_id = ' . $mapId . ' GROUP by session_id');
            $tmp = array();
            if ($results->is_loaded()) {
                foreach ($results as $record) {
                    $tmp[(int)$record['session_id']] = $record['counters'];
                }
            }
            $results->free();

            $counters = DB_ORM::model('map_counter')->getCountersByMap($mapId);

            foreach ($tmp as $key => $value) {
                $j = 0;
                if (count($counters) <= 0) {
                    continue;
                }
                foreach ($counters as $counter) {
                    $s = strpos($value, '[CID=' . $counter->id . ',') + 1;
                    $tmp = substr($value, $s, strlen($value));
                    $e = strpos($tmp, ']') + 1;
                    $tmp = substr($tmp, 0, $e - 1);
                    $tmp = str_replace('CID=' . $counter->id . ',V=', '', $tmp);

                    if (is_numeric($tmp)) {
                        $thisCounter = (int)$tmp;
                        $result[$counter->id][$key] = $thisCounter;
                    }
                    $j++;
                }
            }
        }

        return $result;
    }

    public function getDashboardData($mapId)
    {
        $result = array();
        if (is_numeric($mapId)) {
            $connection = DB_Connection_Pool::instance()->get_connection('default');
            $results = $connection->query('SELECT user_id FROM `user_sessiontraces` WHERE map_id = ' . $mapId . ' and user_id <> 0 GROUP BY user_id');
            $results->free();

            $sessions = $connection->query('SELECT * FROM `user_sessions` WHERE map_id = ' . $mapId);
            $sessionMap = array();
            if ($sessions->is_loaded()) {
                $result['allAttempts'] = count($sessions);
                foreach ($sessions as $s) {
                    $sessionMap[(int)$s['id']] = (int)$s['start_time'];
                }
            }

            $des = $this->getNumberOfDecisionsPerSession((int)$mapId);
            $result['avgDes'] = 0;

            if ($des != null && count($des) > 0) {
                foreach ($des as $n => $f) {
                    $result['avgDes'] = $result['avgDes'] + $f;
                }
                $result['avgDes'] = round($result['avgDes'] / count($des), 1);
            }

            $results = $connection->query('SELECT session_id, date_stamp FROM (SELECT * FROM `user_sessiontraces` WHERE map_id = ' . $mapId . ' ORDER BY id DESC) AS T GROUP BY session_id HAVING COUNT(*) > 1');
            if ($results->is_loaded()) {
                $result['uniqueUsers'] = count($results);
                $avgTime = 0;
                $count = 0;
                foreach ($results as $s) {
                    if (isset($sessionMap[(int)$s['session_id']])) {
                        $avgTime = $avgTime + ((int)$s['date_stamp'] - $sessionMap[(int)$s['session_id']]);
                        $count++;
                    }
                }
                if ($count > 0) {
                    $result['avgTime'] = gmdate("i:s", round($avgTime / $count, 0));
                }
            }
            $results->free();
            $result['graph'] = $this->calculateNodesPerCounters($mapId);
        }

        return $result;
    }

    public function getNumberOfDecisionsPerSession($mapId)
    {
        $result = array();
        if (is_numeric($mapId)) {
            $connection = DB_Connection_Pool::instance()->get_connection('default');
            $results = $connection->query('SELECT node_id, COUNT(*) as count FROM `user_sessiontraces` WHERE map_id = ' . $mapId . ' GROUP BY node_id;');
            if ($results->is_loaded()) {
                foreach ($results as $record) {
                    $result[DB_ORM::model('map_node', array((int)$record['node_id']))->title] = (int)$record['count'];
                }
            }
            $results->free();
        }

        return $result;
    }

    private function calculateNodesPerCounters($mapId)
    {
        if ($mapId <= 0) {
            return array();
        }

        $nodes = DB_ORM::model('map_node')->getNodesByMap($mapId);
        $sessions = $this->getAllSessionByMap($mapId);
        $counters = DB_ORM::model('map_counter')->getCountersByMap($mapId);
        $result = array();

        foreach ($nodes as $node) {
            foreach ($sessions as $session) {
                if (count($session->traces) > 0) {
                    foreach ($session->traces as $trace) {
                        if ($trace->node_id != $node->id AND count($counters) <= 0) {
                            continue;
                        }
                        $j = 0;

                        foreach ($counters as $counter) {
                            $s = strpos($trace->counters, '[CID=' . $counter->id . ',') + 1;
                            $tmp = substr($trace->counters, $s, strlen($trace->counters));
                            $e = strpos($tmp, ']') + 1;
                            $tmp = substr($tmp, 0, $e - 1);
                            $tmp = str_replace('CID=' . $counter->id . ',V=', '', $tmp);

                            if (is_numeric($tmp)) {
                                $thisCounter = floatval($tmp);
                                $result[$node->id][$counter->id]['value'] = isset($result[$node->id][$counter->id]['value']) ? ($result[$node->id][$counter->id]['value'] + $thisCounter) : $thisCounter;
                                $result[$node->id][$counter->id]['count'] = isset($result[$node->id][$counter->id]['count']) ? ($result[$node->id][$counter->id]['count'] + 1) : 1;
                                $result[$node->id][$counter->id]['name'] = $node->title;
                                $result[$node->id][$counter->id]['counterName'] = $counter->name;
                            }
                            $j++;
                        }
                    }
                }
            }
        }
        foreach ($result as $nodeId => $counters) {
            foreach ($counters as $counterId => $v) {
                if (isset($v['value'])) {
                    $result[$nodeId][$counterId]['value'] = round($result[$nodeId][$counterId]['value'] / $result[$nodeId][$counterId]['count'],
                        1);
                }
            }
        }

        return $result;
    }

    public function getNodesPerUserClick($mapId)
    {
        $result = array();
        if (is_numeric($mapId)) {
            $connection = DB_Connection_Pool::instance()->get_connection('default');
            $results = $connection->query('SELECT count(user_id) as `userCount`, node_id FROM `user_sessiontraces` where map_id = ' . $mapId . ' and user_id <> 0 GROUP BY node_id');

            if ($results->is_loaded()) {
                foreach ($results as $record) {
                    $result[(int)$record['node_id']] = (int)$record['userCount'];
                }
            }
            $results->free();
        }

        return $result;
    }

    public function getAverageNodesTime($mapId)
    {
        $result = array();
        if (is_numeric($mapId)) {
            $connection = DB_Connection_Pool::instance()->get_connection('default');
            $results = $connection->query('SELECT T.node_id AS `node_id`, SUM((T.date_stamp) - (S.start_time)) / COUNT(*) AS `avg_time` FROM `user_sessiontraces` AS T INNER JOIN `user_sessions` AS S ON T.session_id = S.id WHERE T.map_id = ' . $mapId . ' and T.user_id <> 0 GROUP BY T.node_id');

            if ($results->is_loaded()) {
                foreach ($results as $record) {
                    $result[(int)$record['node_id']] = (int)$record['avg_time'];
                }
            }
            $results->free();
        }

        return $result;
    }

    public function getAverageCounterValuesForNodes($mapId)
    {
        $sessions = $this->getAllSessionByMap($mapId);
        $nodes = DB_ORM::model('map_node')->getNodesByMap($mapId);
        $counters = DB_ORM::model('map_counter')->getCountersByMap($mapId);

        if ($sessions == null || count($sessions) <= 0
            || $nodes == null || count($nodes) <= 0
            || $counters == null || count($counters) <= 0
        ) {
            return array();
        }

        $result = array();
        $tmp = array();
        foreach ($nodes as $node) {
            $tmp[$node->id] = array();
            foreach ($sessions as $session) {
                if (count($session->traces) > 0) {
                    foreach ($session->traces as $trace) {
                        if ($trace->node_id != $node->id || strlen($trace->counters) <= 0) {
                            continue;
                        }

                        $counterValues = $this->parseCountersValuesFromString($counters, $trace->counters);

                        if (count($counterValues) <= 0) {
                            continue;
                        }

                        foreach ($counterValues as $counterId => $value) {
                            $tmp[$node->id][$counterId]['summ'] = isset($tmp[$node->id][$counterId]['summ']) ? $tmp[$node->id][$counterId]['summ'] + $value : $value;
                            $tmp[$node->id][$counterId]['count'] = isset($tmp[$node->id][$counterId]['count']) ? $tmp[$node->id][$counterId]['count'] + 1 : 1;
                        }
                    }
                }
            }

            if (count($tmp[$node->id]) > 0) {
                foreach ($tmp[$node->id] as $counterId => $value) {
                    $result[$node->id][$counterId]['value'] = round($value['summ'] / $value['count'], 1);
                    $result[$node->id][$counterId]['counter'] = $this->getCounterById($counters, $counterId);
                }
            }
        }

        return $result;
    }

    private function parseCountersValuesFromString($counters, $countersValues)
    {
        if ($counters == null || count($counters) <= 0 || strlen($countersValues) <= 0) {
            return array();
        }

        $result = array();

        foreach ($counters as $counter) {
            $s = strpos($countersValues, '[CID=' . $counter->id . ',') + 1;
            $tmp = substr($countersValues, $s, strlen($countersValues));
            $e = strpos($tmp, ']') + 1;
            $tmp = substr($tmp, 0, $e - 1);
            $tmp = str_replace('CID=' . $counter->id . ',V=', '', $tmp);

            if (is_numeric($tmp)) {
                $thisCounter = (int)$tmp;
                $result[$counter->id] = $thisCounter;
            }
        }

        return $result;
    }

    private function getCounterById($couters, $id)
    {
        if (count($couters) <= 0) {
            return null;
        }

        foreach ($couters as $counter) {
            if ($counter->id == $id) {
                return $counter;
            }
        }
    }

    public function getEndNodes($mapId)
    {
        $result = array();
        if (is_numeric($mapId)) {
            $connection = DB_Connection_Pool::instance()->get_connection('default');
            $results = $connection->query('SELECT COUNT(*) AS `count`, node_id FROM (SELECT node_id FROM (SELECT * FROM `user_sessiontraces` ORDER BY id DESC) AS T WHERE user_id <> 0 and map_id = ' . $mapId . ' GROUP BY user_id) AS M GROUP BY node_id');

            if ($results->is_loaded()) {
                foreach ($results as $record) {
                    $result[(int)$record['node_id']] = (int)$record['count'];
                }
            }
            $results->free();
        }

        return $result;
    }
}