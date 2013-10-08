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
 * Model for user_sessions table in database
 */
class Model_Leap_Statistics_User_Session extends DB_ORM_Model {

    public function __construct() {
        parent::__construct();

        $this->fields = array(
            'id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 10,
                'nullable' => FALSE,
            )),

            'user_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 10,
                'nullable' => FALSE,
                'unsigned' => TRUE,
            )),

            'map_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 10,
                'nullable' => FALSE,
                'unsigned' => TRUE,
            )),

            'start_time' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),

            'user_ip' => new DB_ORM_Field_String($this, array(
                'max_length' => 50,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),

            'webinar_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => TRUE,
                'savable' => TRUE
            )),

            'webinar_step' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => TRUE,
                'savable' => TRUE
            )),
            'date_save_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => TRUE,
                'savable' => TRUE
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
                'options' => array(array('order_by', array('date_stamp', 'ASC')), ),
            )),

            'map' => new DB_ORM_Relation_BelongsTo($this, array(
                'child_key' => array('map_id'),
                'parent_key' => array('id'),
                'parent_model' => 'map',
            )),
        );
    }

    public static function data_source() {
        return 'default';
    }

    public static function table() {
        return 'statistics_user_sessions';
    }

    public static function primary_key() {
        return array('id');
    }

    public function saveWebInarSession(array $sessionData) {

        $id = DB_ORM::model('statistics_user_datesave')->saveDate();

        foreach ($sessionData as $data) {
            $builder = DB_ORM::insert('statistics_user_session')
                ->column('id', $data['id'])
                ->column('user_id', $data['user_id'])
                ->column('map_id', $data['map_id'])
                ->column('start_time', $data['start_time'])
                ->column('user_ip', $data['user_ip'])
                ->column('webinar_id', $data['webinar_id'])
                ->column('webinar_step', $data['webinar_step'])
                ->column('date_save_id', $id);

             $builder->execute();
        }
    }

    public function getSessionByWebinarId($webinarId) {
        $builder = DB_SQL::select('default')
            ->from($this->table())
            ->where('webinar_id', '=', $webinarId);
        $result = $builder->query();
        $ids = array();
        if($result->is_loaded()) {
            foreach ($result as $record) {
                $ids[] = $record['id'];
            }
        }
        return  $ids;
    }

    public function getDateSaveByWebinarId($webinarId){

        $connection = DB_Connection_Pool::instance()->get_connection('default');

        $results = $connection->query ("
            SELECT
              DISTINCT ( ud.id ),
                ud.date_save, MAX(us.webinar_step) as webinar_step
            FROM
              statistics_user_sessions AS us
            LEFT JOIN
              statistics_user_datesave AS ud ON ud.id = us.date_save_id
            WHERE
              us.webinar_id =$webinarId
            GROUP BY
              ud.id
            ORDER BY
              ud.date_save
        ");

        $res = array();

        if ($results->is_loaded()) {
            foreach($results as $record) {
                $res[] = $record;
            }
        }

        return $res;
    }

    public function getSessionByUserMapIDs($userId, $mapId, $webinarId = null, $currentStep = null, $date=null) {
        $builder = DB_SQL::select('default')
            ->from($this->table())
            ->where('user_id', '=', $userId, 'AND')
            ->where('map_id', '=', $mapId)
            ->order_by('start_time', 'DESC');

        if($webinarId != null) {
            $builder = $builder->where('webinar_id', '=', $webinarId);
        }

        if($currentStep != null) {
            $builder = $builder->where('webinar_step', '<=', $currentStep);
        }

        if($date != null) {
            $builder = $builder->where('date_save_id', '=', $date);
        }

        $result = $builder->query();

        if($result->is_loaded()) {
            $sessions = array();
            foreach($result as $record) {
                $sessions[] = DB_ORM::model('statistics_user_session', array((int)$record['id']));
            }

            return $sessions;
        }

        return NULL;
    }

    const USER_NOT_PLAY_MAP   = 0;
    const USER_NOT_FINISH_MAP = 1;
    const USER_FINISH_MAP     = 2;

    /**
     * Check user finish labyrinth or play it now
     *
     * @param integer $mapId - map Id
     * @param integer $userId - User Id
     * @param integer $webinarId - Webinar Id
     * @returns integer - 0 - not play, 1 - now playing, 2 - finish
     */
    public function isUserFinishMap($mapId, $userId, $webinarId = null, $currentStep = null) {
        $result = Model_Leap_User_Session::USER_NOT_PLAY_MAP;
        $sessions = $this->getSessionByUserMapIDs($userId, $mapId, $webinarId, $currentStep);
        if($sessions == null || count($sessions) <= 0) return $result;
        $result = Model_Leap_User_Session::USER_NOT_FINISH_MAP;

        $endNodes = DB_ORM::model('map_node')->getEndNodesForMap($mapId);
        if($endNodes != null) {
            $endNodeIDs = array();
            if(count($endNodes) > 0) {
                foreach($endNodes as $endNode) {
                    $endNodeIDs[] = $endNode->id;
                }
            } else {
                $endNodeIDs[] = $endNodes->id;
            }

            $sessionIDs = array();
            foreach($sessions as $session) {
                $sessionIDs[] = $session->id;
            }

            if(DB_ORM::model('statistics_user_sessiontrace')->isExistTrace($userId, $mapId, $sessionIDs, $endNodeIDs)) {
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
     * @param array|null $notInUsers - not include sessions of susers
     * @param integer|null $dateStatistics - if this sessions for Statistics
     * @return array|null - session ids or null
     */
    public function getSessions($mapId, $webinarId = null, $webinarStep = null, $notInUsers = null, $dateStatistics = null) {
        $builder = DB_SQL::select('default')
            ->from($this->table())
            ->where('map_id', '=', $mapId, 'AND')
            ->column('id');
        if($webinarId != null && $webinarId > 0) {
            $builder = $builder->where('webinar_id', '=', $webinarId, 'AND');
        }

        if($webinarStep != null && $webinarStep > 0) {
            $builder = $builder->where('webinar_step', '=', $webinarStep, 'AND');
        }

        if($notInUsers != null && count($notInUsers) > 0) {
            $builder = $builder->where('user_id', 'NOT IN', $notInUsers);
        }

        if($dateStatistics != null && $dateStatistics > 0) {
            $builder = $builder->where('date_save_id', '=', $dateStatistics);
        }

        $records = $builder->query();
        if($records->is_loaded()) {
            $sessions = array();
            foreach($records as $record) {
                $sessions[] = $record['id'];
            }

            return $sessions;
        }

        return null;
    }
}

?>