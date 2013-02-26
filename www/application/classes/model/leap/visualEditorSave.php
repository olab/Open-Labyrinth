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

class Model_Leap_VisualEditorSave extends DB_ORM_Model {

    public function __construct() {
        parent::__construct();
        $this->fields = array(
            'id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
                'unsigned' => TRUE,
            )),
            'map_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
                'unsigned' => TRUE,
            )),
            'user_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
                'unsigned' => TRUE,
            )),
            'json' => new DB_ORM_Field_Text($this, array(
                'savable' => TRUE,
            ))
        );
        
        $this->relations = array(
            'map' => new DB_ORM_Relation_BelongsTo($this, array(
                'child_key' => array('map_id'),
                'parent_key' => array('id'),
                'parent_model' => 'map',
            )),
            'user' => new DB_ORM_Relation_BelongsTo($this, array(
                'child_key' => array('user_id'),
                'parent_key' => array('id'),
                'parent_model' => 'user',
            )),
        );
    }
    
    public static function data_source() {
        return 'default';
    }

    public static function table() {
        return 'visual_editor_autosaves';
    }

    public static function primary_key() {
        return array('id');
    }
    
    public function saveJSON($mapId, $userId, $json) {
        $record = $this->getSave($mapId, $userId);
        
        if($record == NULL) {
            $builder = DB_ORM::insert('visualeditorsave')
                    ->column('map_id', $mapId)
                    ->column('user_id', $userId)
                    ->column('json', str_replace(array('\\r\\n', '\\r', '\\n'), array('', '', ''), $json));
            
            $builder->execute();
        } else {
            $record->json = str_replace(array('\\r\\n', '\\r', '\\n'), array('', '', ''), $json);
            $record->save();
        }
    }
    
    public function getSave($mapId, $userId) {
        $builder = DB_SQL::select('default')
                ->from($this->table())
                ->where('map_id', '=', $mapId)
                ->where('user_id', '=', $userId)
                ->limit(1);
        
        $data = $builder->query();
        $result = NULL;
        if($data->is_loaded()) {
            $result = DB_ORM::model('visualeditorsave', array((int)$data[0]['id']));
        }
        
        return $result;
    }
    
    public function deleteSave($mapId, $userId) {
        DB_SQL::delete('default')->from($this->table())
                ->where('map_id', '=', $mapId, 'AND')
                ->where('user_id', '=', $userId)
                ->execute();
    }
}

;
?>
