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
 * Model for user_bookmarks table in database 
 */
class Model_Leap_User_Bookmark extends DB_ORM_Model {

    public function __construct() {
        parent::__construct();

        $this->fields = array(
            'id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 10,
                'nullable' => FALSE,
                'unsigned' => TRUE,
            )),
			'session_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 10,
                'nullable' => FALSE,
                'unsigned' => TRUE,
            )),
			'node_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 10,
                'nullable' => FALSE,
                'unsigned' => TRUE,
            )),
            'user_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 10,
                'nullable' => FALSE,
                'unsigned' => TRUE,
            )),
        );
    }
    
    public static function data_source() {
        return 'default';
    }

    public static function table() {
        return 'user_bookmarks';
    }

    public static function primary_key() {
        return array('id');
    }
	
	public function addBookmark($nodeId, $sessionId, $userId)
    {
        $current_time = time();
        // check for existing bookmark
        $nodeObj = DB_ORM::model('Map_Node', array($nodeId));
        if ($nodeObj) {
            $this->deleteBookmarksByMapAndUser($nodeObj->map_id, $userId);
        }

        $this->user_id = $userId;
		$this->node_id = $nodeId;
		$this->session_id = $sessionId;
		$this->save();

        /** @var Model_Leap_User_SessionTrace $session_trace */
        $session_trace = Model_Leap_User_SessionTrace::getLatestBySession($sessionId);
        $session_trace->bookmark_made = $current_time;
        $session_trace->save();

        //send xAPI statement
        if (Model_Leap_Map::isXAPIStatementsEnabled($nodeObj->map_id)) {
            $statement = $session_trace->createXAPIStatementSuspended();
            Model_Leap_LRSStatement::sendStatementsToLRS($statement->lrs_statements);
        }
        //end send xAPI statement
	}

    public function deleteBookmarksByMapAndUser($mapId, $userId)
    {
        $result = $this->getBookmarkByMapAndUser($mapId, $userId);
        if ($result) {
            DB_ORM::delete('User_Bookmark')->where('id', '=', $result['id'])->execute();
        }
    }
	
	public function getBookmark($sessionId)
    {
        $result = DB_SQL::select('default')->from($this->table())->where('session_id', '=', $sessionId)->order_by('time_stamp', 'DESC')->limit(1)->query();

		if($result->is_loaded()) {
			return DB_ORM::model('user_bookmark', array((int)$result[0]['id']));
		}

		return NULL;
	}

    public function  getBookmarkByMapAndUser($mapId, $userId)
    {
        return DB_SQL::select('default')
            ->from('map_nodes', 'n')
            ->join('LEFT', 'user_bookmarks','b')
            ->on('n.id','=','b.node_id')
            ->where('b.user_id', '=', $userId)
            ->where('n.map_id', '=', $mapId)
            ->query()
            ->fetch(0);
    }
}