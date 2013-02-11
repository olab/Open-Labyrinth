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
			
			'time_stamp' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 20,
                'nullable' => FALSE,
            )),
        );
		
		$this->relations = array(
            'node' => new DB_ORM_Relation_BelongsTo($this, array(
                'child_key' => array('node_id'),
                'parent_key' => array('id'),
                'parent_model' => 'map_node',
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
	
	public function addBookmark($nodeId, $sessionId) {
		$this->node_id = $nodeId;
		$this->session_id = $sessionId;
		$this->time_stamp = time();
		
		$this->save();
	}
	
	public function getBookmark($sessionId) {
		$builder = DB_SQL::select('default')->from($this->table())->where('session_id', '=', $sessionId)->order_by('time_stamp', 'DESC')->limit(1);
		$result = $builder->query();
		
		if($result->is_loaded()) {
			return DB_ORM::model('user_bookmark', array((int)$result[0]['id']));
		}
		
		return NULL;
	}
}

