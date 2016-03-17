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
 * @property int $session_id
 * @property int $node_id
 * @property int $map_id
 * @property int $date_stamp
 * @property int $end_date_stamp
 * @property int $confidence
 * @property int $bookmark_made
 * @property int $bookmark_used
 * @property bool $is_redirected
 * @property string $counters
 * @property string $dams
 * @property Model_Leap_Map_Node $node
 * @property Model_Leap_Map $map
 * @property Model_Leap_User_Session $session
 */
class Model_Leap_User_SessionTrace extends DB_ORM_Model
{

    public function __construct()
    {
        parent::__construct();

        $this->fields = array(
            'id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => false,
            )),
            'session_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 10,
                'nullable' => false,
                'unsigned' => true,
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
            'node_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 10,
                'nullable' => false,
                'unsigned' => true,
            )),
            'is_redirected' => new DB_ORM_Field_Boolean($this, array(
                'default' => false,
                'nullable' => false,
                'savable' => true,
            )),
            'counters' => new DB_ORM_Field_String($this, array(
                'max_length' => 700,
                'nullable' => false,
                'savable' => true,
            )),
            'date_stamp' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 20,
                'nullable' => false,
            )),
            'end_date_stamp' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 20,
                'nullable' => true,
            )),
            'confidence' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 6,
                'nullable' => true,
                'default' => null,
            )),
            'dams' => new DB_ORM_Field_String($this, array(
                'max_length' => 700,
                'nullable' => true,
                'default' => null,
                'savable' => true,
            )),
            'bookmark_made' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => true,
                'default' => null,
            )),
            'bookmark_used' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => true,
                'default' => null,
            )),
        );

        $this->relations = array(
            'node' => new DB_ORM_Relation_BelongsTo($this, array(
                'child_key' => array('node_id'),
                'parent_key' => array('id'),
                'parent_model' => 'map_node',
            )),
            'map' => new DB_ORM_Relation_BelongsTo($this, array(
                'child_key' => array('map_id'),
                'parent_key' => array('id'),
                'parent_model' => 'map',
            )),
            'session' => new DB_ORM_Relation_BelongsTo($this, array(
                'child_key' => array('session_id'),
                'parent_key' => array('id'),
                'parent_model' => 'User_Session',
            )),
        );
    }

    public static function data_source()
    {
        return 'default';
    }

    public static function table()
    {
        return 'user_sessiontraces';
    }

    public static function primary_key()
    {
        return array('id');
    }

    public function getCountersAsArray()
    {
        $result = array();

        $counters = $this->counters;
        if (empty($counters)) {
            return $result;
        }

        //test string
        //$counters = '[CID=1,V=0.6][CID=8,V=1321][MCID=8,V=0][CID=9,V=-9][CID=10,V=][CID=,V=12][CID=2,V=reset]';

        $float_pattern = '(\+|\-)?[0-9]+(\.[0-9]+)?';
        $string_pattern = '[a-zA-Z]+';
        $float_or_string = '(' . $float_pattern . '|' . $string_pattern . ')';
        preg_match_all('#(\[CID=(?<id>\d+)+)+(,V=)+(?<value>' . $float_or_string . '?)?(\])+#', $counters, $matches);

        if (!empty($matches['id'])) {
            $result = array_combine($matches['id'], $matches['value']);
        }

        return $result;
    }

    public function createXAPIStatementResumed()
    {

        if (!($this->bookmark_used > 0)) {
            return false;
        }

        $timestamp = $this->bookmark_used;

        //verb
        $verb = array(
            'id' => 'http://adlnet.gov/expapi/verbs/resumed',
            'display' => array(
                'en-US' => 'resumed'
            ),
        );
        //end verb

        //object
        $node = $this->node;
        $url = URL::base(true) . 'nodeManager/editNode/' . $node->id;
        $object = array(
            'id' => $url,
            'definition' => array(
                'name' => array(
                    'en-US' => 'node "' . $node->title . '" (#' . $node->id . ')'
                ),
                'description' => array(
                    'en-US' => 'Node content: ' . $node->text
                ),
                'type' => 'http://activitystrea.ms/schema/1.0/node',
                'moreInfo' => $url,
            ),

        );
        //end object

        //result
        $result = array(
            'completion' => true,
        );
        //end result

        //context
        $context = array();
        $session = $this->session;
        $context['contextActivities']['parent']['id'] = $url;

        $map_url = URL::base(true) . 'labyrinthManager/global/' . $session->map_id;
        $context['contextActivities']['grouping']['id'] = $map_url;

        //end context

        return Model_Leap_Statement::create($session, $verb, $object, $result, $context, $timestamp);
    }

    public function createXAPIStatementSuspended()
    {

        if (!($this->bookmark_made > 0)) {
            return false;
        }

        $timestamp = $this->bookmark_made;

        //verb
        $verb = array(
            'id' => 'http://adlnet.gov/expapi/verbs/suspended',
            'display' => array(
                'en-US' => 'suspended'
            ),
        );
        //end verb

        //object
        $node = $this->node;
        $url = URL::base(true) . 'nodeManager/editNode/' . $node->id;
        $object = array(
            'id' => $url,
            'definition' => array(
                'name' => array(
                    'en-US' => 'node "' . $node->title . '" (#' . $node->id . ')'
                ),
                'description' => array(
                    'en-US' => 'Node content: ' . $node->text
                ),
                'type' => 'http://activitystrea.ms/schema/1.0/node',
                'moreInfo' => $url,
            ),

        );
        //end object

        //result
        $result = array(
            'completion' => true,
        );
        //end result

        //context
        $context = array();
        $session = $this->session;
        $context['contextActivities']['parent']['id'] = $url;

        $map_url = URL::base(true) . 'labyrinthManager/global/' . $session->map_id;
        $context['contextActivities']['grouping']['id'] = $map_url;

        //end context

        return Model_Leap_Statement::create($session, $verb, $object, $result, $context, $timestamp);
    }

    public function createXAPIStatementUpdated(Model_Leap_User_SessionTrace $previous_session_trace)
    {
        if ($previous_session_trace instanceof Model_Leap_User_SessionTrace) {
            if ($previous_session_trace->counters === $this->counters) {
                return;
            }
        }

        $current_counters = $this->getCountersAsArray();
        $previous_counters = $previous_session_trace->getCountersAsArray();

        $changed_counters = array();
        foreach ($current_counters as $id => $current_counter) {
            if (isset($previous_counters[$id])) {
                if ($previous_counters[$id] !== $current_counter) {
                    $changed_counters[$id] = $current_counter;
                }
            } else {
                $changed_counters[$id] = $current_counter;
            }
        }

        if (empty($changed_counters)) {
            return;
        }

        $timestamp = $this->date_stamp;

        //verb
        $verb = array(
            'id' => 'http://w3id.org/xapi/medbiq/verbs/updated',
            'display' => array(
                'en-US' => 'updated'
            ),
        );
        //end verb

        //result
        $result = null;
        //end result

        //context
        $context = array();

        $node = $this->node;
        $node_url = URL::base(true) . 'nodeManager/editNode/' . $node->id;
        $context['contextActivities']['parent']['id'] = $node_url;

        $session = $this->session;
        $map_url = URL::base(true) . 'labyrinthManager/global/' . $session->map_id;
        $context['contextActivities']['grouping']['id'] = $map_url;
        //end context

        foreach ($changed_counters as $counter_id => $changed_counter) {

            /** @var Model_Leap_Map_Counter $counter */
            $counter = DB_ORM::model('Map_Counter', array($counter_id));

            $counter_exists = $counter->is_loaded();

            //object
            $url = URL::base(true) . 'counterManager/editCounter/' . $this->map_id . '/' . $counter_id;
            $object = array(
                'id' => $url,
                'definition' => array(
                    'name' => array(
                        'en-US' => $counter_exists ? 'counter "' . $counter->name . '" (#' . $counter_id . ')' : 'counter'
                    ),
                    'description' => array(
                        'en-US' => $counter_exists ? 'Counter description: ' . $counter->description : ''
                    ),
                    //'type' => 'http://activitystrea.ms/schema/1.0/node',
                    'moreInfo' => $url,
                ),

            );
            //end object

            //result
            $result = array(
                'score' => array(
                    'raw' => $changed_counter,
                ),
            );
            //end result

            Model_Leap_Statement::create($session, $verb, $object, $result, $context, $timestamp);
        }
    }

    public function createXAPIStatementLaunched()
    {
        if (!$this->is_redirected) {
            return false;
        }

        $node = $this->node;

        $timestamp = $this->date_stamp;

        //verb
        $verb = array(
            'id' => 'http://adlnet.gov/expapi/verbs/launched',
            'display' => array(
                'en-US' => 'launched'
            ),
        );
        //end verb

        //object
        $url = URL::base(true) . 'nodeManager/editNode/' . $node->id;
        $object = array(
            'id' => $url,
            'definition' => array(
                'name' => array(
                    'en-US' => 'node "' . $node->title . '" (#' . $node->id . ')'
                ),
                'description' => array(
                    'en-US' => 'Node content: ' . $node->text
                ),
                'type' => 'http://activitystrea.ms/schema/1.0/node',
                'moreInfo' => $url,
            ),

        );
        //end object

        //result
        $result = array(
            'completion' => true,
        );
        //end result

        //context
        $context = array();
        $session = $this->session;
        $context['contextActivities']['parent']['id'] = $url;

        $map_url = URL::base(true) . 'labyrinthManager/global/' . $session->map_id;
        $context['contextActivities']['grouping']['id'] = $map_url;

        //end context

        return Model_Leap_Statement::create($session, $verb, $object, $result, $context, $timestamp);
    }

    public function createXAPIStatementInitialized($node = null)
    {
        $node = ($node === null) ? $this->node : $node;

        $timestamp = $this->date_stamp;

        //verb
        $verb = array(
            'id' => 'http://adlnet.gov/expapi/verbs/initialized',
            'display' => array(
                'en-US' => 'initialized'
            ),
        );
        //end verb

        //object
        $url = URL::base(true) . 'nodeManager/editNode/' . $node->id;
        $object = array(
            'id' => $url,
            'definition' => array(
                'name' => array(
                    'en-US' => 'node "' . $node->title . '" (#' . $node->id . ')'
                ),
                'description' => array(
                    'en-US' => 'Node content: ' . $node->text
                ),
                'type' => 'http://activitystrea.ms/schema/1.0/node',
                'moreInfo' => $url,
            ),

        );
        //end object

        //result
        $result = array(
            'completion' => true,
        );
        //end result

        //context
        $context = array();
        $session = $this->session;
        $context['contextActivities']['parent']['id'] = $url;

        $map_url = URL::base(true) . 'labyrinthManager/global/' . $session->map_id;
        $context['contextActivities']['grouping']['id'] = $map_url;

        //end context

        return Model_Leap_Statement::create($session, $verb, $object, $result, $context, $timestamp);
    }

    public function createXAPIStatementArrived()
    {
        $node = $this->node;

        $timestamp = $this->date_stamp;

        //verb
        $verb = array(
            'id' => 'http://w3id.org/xapi/medbiq/verbs/arrived',
            'display' => array(
                'en-US' => 'arrived'
            ),
        );
        //end verb

        //object
        $url = URL::base(true) . 'nodeManager/editNode/' . $node->id;
        $object = array(
            'id' => $url,
            'definition' => array(
                'name' => array(
                    'en-US' => 'node "' . $node->title . '" (#' . $node->id . ')'
                ),
                'description' => array(
                    'en-US' => 'Node content: ' . $node->text
                ),
                'type' => 'http://activitystrea.ms/schema/1.0/node',
                'moreInfo' => $url,
            ),

        );
        //end object

        //result
        $result = array(
            'completion' => true,
        );
        //end result

        //context
        $context = array();
        $session = $this->session;
        $context['contextActivities']['parent']['id'] = $url;

        $map_url = URL::base(true) . 'labyrinthManager/global/' . $session->map_id;
        $context['contextActivities']['grouping']['id'] = $map_url;

        //end context

        return Model_Leap_Statement::create($session, $verb, $object, $result, $context, $timestamp);
    }

    public function createXAPIStatementCompleted($node = null)
    {
        $node = ($node === null) ? $this->node : $node;
        if (!$node->end) {
            return false;
        }

        $timestamp = $this->date_stamp;

        $verb = array(
            'id' => 'http://adlnet.gov/expapi/verbs/completed',
            'display' => array(
                'en-US' => 'completed'
            ),
        );

        //object
        $url = URL::base(true) . 'labyrinthManager/global/' . $this->map_id;
        $map = $this->map;
        $object = array(
            'id' => $url,
            'definition' => array(
                'name' => array(
                    'en-US' => 'map "' . $map->name . '" (#' . $map->id . ')'
                ),
                'description' => array(
                    'en-US' => 'Map description: ' . $map->abstract
                ),
                //'type' => 'http://activitystrea.ms/schema/1.0/node',
                'moreInfo' => $url,
            ),

        );
        //end object

        //result
        $result = array(
            'completion' => true,
            //'score' => array(
            //
            //),
        );
        //end result

        //context
        $context = null; //default
        //end context
        $session = $this->session;

        return Model_Leap_Statement::create($session, $verb, $object, $result, $context, $timestamp);
    }

    public function getUniqueTraceByMapId($mapId)
    {
        $records = DB_SQL::select('default')
            ->from($this->table())
            ->where('map_id', '=', $mapId)
            ->group_by('node_id')
            ->group_by('session_id')
            ->order_by('id')
            ->column('id')
            ->column('session_id')
            ->column('user_id')
            ->column('node_id')
            ->query();

        $result = array();
        if ($records->is_loaded()) {
            foreach ($records as $record) {
                $result[] = array(
                    'id' => $record['id'],
                    'session_id' => $record['session_id'],
                    'user_id' => $record['user_id'],
                    'node_id' => $record['node_id']
                );
            }
        }

        return $result;
    }

    public function getUniqueTraceBySessions($sessions, $idNode = false, $undo = false)
    {
        $records = DB_SQL::select('default')
            ->from($this->table())
            ->where('session_id', 'IN', $sessions)
            ->group_by('node_id')
            ->group_by('session_id')
            ->order_by('id')
            ->column('id')
            ->column('session_id')
            ->column('user_id')
            ->column('node_id')
            ->query();

        $result = array();

        foreach ($records as $record) {
            $undoNode = $undo ? DB_ORM::model('Map_Node', array($record['node_id']))->undo : true;

            if ($undoNode AND $idNode != $record['node_id']) {
                $result[] = array(
                    'id' => $record['id'],
                    'session_id' => $record['session_id'],
                    'user_id' => $record['user_id'],
                    'node_id' => $record['node_id']
                );
            }
        }

        return $result;
    }

    public function undoTrace($sessionId, $mapId, $nodeId)
    {
        $records = DB_SQL::select('default')
            ->from($this->table())
            ->where('session_id', '=', $sessionId, 'AND')
            ->where('map_id', '=', $mapId)
            ->order_by('id', 'DESC')
            ->column('id')
            ->column('node_id')
            ->query();

        if ($records->is_loaded()) {
            $removeTraceIDs = array();
            $undoTraceId = null;
            foreach ($records as $record) {
                if ($record['node_id'] == $nodeId) {
                    $undoTraceId = $record['id'];
                    break;
                } else {
                    $removeTraceIDs[] = $record['id'];
                }
            }

            if ($undoTraceId != null && $undoTraceId > 0) {
                $trace = DB_ORM::model('user_sessionTrace', array((int)$undoTraceId));
                $rootNode = DB_ORM::model('map_node')->getRootNodeByMap($mapId);
                $this->updateCounter($sessionId, $mapId, $rootNode->id, $trace->counters);

                $removeTraceIDs[] = $trace->id;
                $this->deleteTracesByIDs($removeTraceIDs);
            }
        }
    }

    public function deleteTracesByIDs($tracesIDs)
    {
        if ($tracesIDs == null && count($tracesIDs) <= 0) {
            return;
        }

        DB_SQL::delete('default')
            ->from($this->table())
            ->where('id', 'IN', $tracesIDs)
            ->execute();
    }

    public function createTrace($sessionId, $userId, $mapId, $nodeId, $is_redirected = false)
    {
        $time = $this->setElapsedTime($sessionId);

        return DB_ORM::insert('user_sessionTrace')
            ->column('session_id', $sessionId)
            ->column('user_id', $userId)
            ->column('map_id', $mapId)
            ->column('node_id', $nodeId)
            ->column('date_stamp', $time)
            ->column('is_redirected', $is_redirected)
            ->execute();
    }

    public function setElapsedTime($sessionId)
    {
        $traceId = $this->getTopTraceBySessionId($sessionId);
        $time = time();
        if ($traceId != null) {
            DB_ORM::update('user_sessionTrace')
                ->set('end_date_stamp', $time)
                ->where('id', '=', $traceId)
                ->execute();
        }

        return $time;
    }

    public function getTopTraceBySessionId($sessionId, $getObject = false)
    {
        $builder = DB_SQL::select('default')
            ->from($this->table())
            ->where('session_id', '=', $sessionId)
            ->order_by('id', 'DESC')
            ->limit(1);
        $result = $builder->query();

        if ($result->is_loaded()) {
            if ($getObject) {
                return $result[0];
            } else {
                return $result[0]['id'];
            }
        }

        return null;
    }

    public function getLastTraceBySessionId($sessionId)
    {
        $builder = DB_SQL::select('default')
            ->from($this->table())
            ->where('session_id', '=', $sessionId)
            ->order_by('date_stamp', 'DESC')
            ->limit(1);
        $result = $builder->query();

        if ($result->is_loaded()) {
            return $result[0];
        }

        return null;
    }

    /**
     * @param int $sessionId
     * @return Model_Leap_User_SessionTrace
     */
    public static function getLatestBySession($sessionId)
    {
        return DB_ORM::select('user_sessionTrace')->where('session_id', '=',
            $sessionId)->order_by('id', 'DESC')->query()->fetch(0);
    }

    public function isExistBySessionAndNodeIDs($sessionId, $nodeId)
    {
        $builder = DB_SQL::select('default')
            ->from($this->table())
            ->where('session_id', '=', $sessionId, 'AND')
            ->where('node_id', '=', $nodeId);
        $result = $builder->query();

        if ($result->is_loaded()) {
            return true;
        }

        return false;
    }

    public function getCountTracks($sessionId, $nodeIDs)
    {
        if (count($nodeIDs) > 0) {
            $builder = DB_SQL::select('default')
                ->from($this->table())
                ->where('session_id', '=', $sessionId, 'AND')
                ->where('node_id', 'IN', $nodeIDs);
            $result = $builder->query();

            if ($result->is_loaded()) {
                return count($result);
            }
        }

        return null;
    }

    public function getCounterByIDs($sessionId, $mapId, $nodeId)
    {
        $builder = DB_SQL::select('default')
            ->from($this->table())
            ->where('session_id', '=', $sessionId, 'AND')
            ->where('map_id', '=', $mapId, 'AND')
            ->where('node_id', '=', $nodeId)
            ->limit(1);
        $result = $builder->query();

        if ($result->is_loaded()) {
            return $result[0]['counters'];
        }

        return null;
    }

    public function updateCounter($sessionId, $mapId, $nodeId, $newCounters, $traceId = null)
    {
        $builder = DB_ORM::update('user_sessionTrace')
            ->set('counters', $newCounters)
            ->where('session_id', '=', $sessionId, 'AND')
            ->where('map_id', '=', $mapId, 'AND');

        if ($traceId != null) {
            $builder = $builder->where('id', '=', $traceId, 'AND');
        }

        $builder
            ->where('node_id', '=', $nodeId)
            ->order_by('id', 'ASC')
            ->limit(1)
            ->execute();
    }

    public function getTraceBySessionID($sessionId)
    {
        return DB_ORM::select('user_sessiontrace')->where('session_id', '=', $sessionId)->order_by('id',
            'ASC')->query()->as_array();
    }

    public function getCountersValues($sessionId)
    {
        $traces = $this->getTraceBySessionID($sessionId);
        if (count($traces)) {
            $result = array();
            $i = 0;
            foreach ($traces as $trace) {
                if ($trace->counters != '') {
                    $counters = DB_ORM::model('map_counter')->getCountersByMap($trace->map_id);
                    if ($counters != null and count($counters) > 0) {
                        $currentCountersState = $trace->counters;
                        $j = 0;
                        foreach ($counters as $counter) {
                            $s = strpos($currentCountersState, '[CID=' . $counter->id . ',') + 1;
                            $tmp = substr($currentCountersState, $s, strlen($currentCountersState));
                            $e = strpos($tmp, ']') + 1;
                            $tmp = substr($tmp, 0, $e - 1);
                            $tmp = str_replace('CID=' . $counter->id . ',V=', '', $tmp);
                            $result[$counter->name][0] = $counter->name;
                            $result[$counter->name][2] = $counter->id;
                            if (is_numeric($tmp)) {
                                $thisCounter = $tmp;
                                $result[$counter->name][1][] = $thisCounter;
                            }
                            $j++;
                        }
                    }
                }
                $i++;
            }

            return $result;
        }

        return null;
    }

    public function isExistTrace($userId, $mapId, $sessionIDs, $nodeIDs)
    {
        $records = DB_SQL::select('default')
            ->from($this->table())
            ->column('id')
            ->where('user_id', '=', $userId)
            ->where('map_id', '=', $mapId)
            ->where('session_id', 'IN', $sessionIDs)
            ->where('node_id', 'IN', $nodeIDs)
            ->query();

        return $records->is_loaded();
    }

    public function getDateStampBySessionAndNodeId($sessionId, $nodeId)
    {
        $res = array();
        $result = DB_SQL::select('default')
            ->from($this->table())
            ->column('date_stamp')
            ->where('session_id', '=', $sessionId, 'AND')
            ->where('node_id', '=', $nodeId)
            ->limit(1)
            ->query();

        if ($result->is_loaded()) {
            foreach ($result as $record) {
                $res[] = $record['date_stamp'];
            }
        }

        return $res;
    }

    public function updateSession($sessionId, $nodeId, $mapId, $dateStamp)
    {
        $builder = DB_ORM::delete('user_sessiontrace')
            ->where('session_id', '=', $sessionId)
            ->where('node_id', '=', $nodeId)
            ->where('map_id', '=', $mapId)
            ->where('date_stamp', '>=', $dateStamp);
        $builder->execute();
    }

    public function getPreviousTrace($sessionId)
    {
        $sessionTraces = DB_ORM::select('User_SessionTrace')->where('session_id', '=', $sessionId)->query()->as_array();

        return (count($sessionTraces) == 1) ? $sessionTraces[0] : $sessionTraces[count($sessionTraces) - 2];
    }

    public function getEndSessionTime($sessionId)
    {
        $result = 0;
        $record = DB_ORM::select('User_SessionTrace')->where('session_id', '=', $sessionId)->order_by('end_date_stamp',
            'DESC')->query()->fetch(0);
        if ($record !== false) {
            $result = $record->end_date_stamp;
        }

        return $result;
    }
}