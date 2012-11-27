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
 * Model for maps table in database  
 */
class Model_Leap_Map extends DB_ORM_Model {

    public function __construct() {
        parent::__construct();

        $this->fields = array(
            'id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
                'unsigned' => TRUE,
            )),
            'name' => new DB_ORM_Field_String($this, array(
                'max_length' => 200,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            'author_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            'abstract' => new DB_ORM_Field_String($this, array(
                'max_length' => 2000,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            'startScore' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            'threshold' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            'keywords' => new DB_ORM_Field_String($this, array(
                'max_length' => 500,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            'type_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            'units' => new DB_ORM_Field_String($this, array(
                'max_length' => 10,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            'security_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            'guid' => new DB_ORM_Field_String($this, array(
                'max_length' => 50,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            'timing' => new DB_ORM_Field_Boolean($this, array(
                'default' => FALSE,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            'delta_time' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            'show_bar' => new DB_ORM_Field_Boolean($this, array(
                'default' => FALSE,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            'show_score' => new DB_ORM_Field_Boolean($this, array(
                'default' => FALSE,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            'skin_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            'enabled' => new DB_ORM_Field_Boolean($this, array(
                'default' => TRUE,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            'section_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            'language_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            'feedback' => new DB_ORM_Field_String($this, array(
                'max_length' => 2000,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            'dev_notes' => new DB_ORM_Field_String($this, array(
                'max_length' => 1000,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            'source' => new DB_ORM_Field_String($this, array(
                'max_length' => 50,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            'source_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
        );

        $this->relations = array(
            'author' => new DB_ORM_Relation_BelongsTo($this, array(
                'child_key' => array('author_id'),
                'parent_key' => array('id'),
                'parent_model' => 'user',
            )),
            'type' => new DB_ORM_Relation_BelongsTo($this, array(
                'child_key' => array('type_id'),
                'parent_key' => array('id'),
                'parent_model' => 'map_type',
            )),
            'security' => new DB_ORM_Relation_BelongsTo($this, array(
                'child_key' => array('security_id'),
                'parent_key' => array('id'),
                'parent_model' => 'map_security',
            )),
            'skin' => new DB_ORM_Relation_BelongsTo($this, array(
                'child_key' => array('skin_id'),
                'parent_key' => array('id'),
                'parent_model' => 'map_skin',
            )),
            'section' => new DB_ORM_Relation_BelongsTo($this, array(
                'child_key' => array('section_id'),
                'parent_key' => array('id'),
                'parent_model' => 'map_section',
            )),
            'language' => new DB_ORM_Relation_BelongsTo($this, array(
                'child_key' => array('language_id'),
                'parent_key' => array('id'),
                'parent_model' => 'language',
            )),
            'contributors' => new DB_ORM_Relation_HasMany($this, array(
                'child_key' => array('map_id'),
                'child_model' => 'map_contributor',
                'parent_key' => array('id'),
            )),
            'authors' => new DB_ORM_Relation_HasMany($this, array(
                'child_key' => array('map_id'),
                'child_model' => 'map_user',
                'parent_key' => array('id'),
            )),
            'nodes' => new DB_ORM_Relation_HasMany($this, array(
                'child_key' => array('map_id'),
                'child_model' => 'map_node',
                'parent_key' => array('id'),
            )),
        );
    }

    public static function data_source() {
        return 'default';
    }

    public static function table() {
        return 'maps';
    }

    public static function primary_key() {
        return array('id');
    }
    
    public function getMapByName($name) {
        $builder = DB_SQL::select('default')->from($this->table())->where('name', '=', $name);
        $result = $builder->query();

        if ($result->is_loaded()) {
            return DB_ORM::model('map', array($result[0]['id']));
        }
        
        return NULL;
    }
    
    public function getAllMap() {
        $builder = DB_SQL::select('default')->from($this->table());
        $result = $builder->query();
        
        if($result->is_loaded()) {
            $maps = array();
            foreach($result as $record) {
                $maps[] = DB_ORM::model('map', array((int)$record['id']));
            }
            
            return $maps;
        }
        
        return NULL;
    }
    
    public function getAllEnabledAndOpenMap() {
        $builder = DB_SQL::select('default')
                ->from($this->table())
                ->where('enabled', '=', 1, 'AND')
                ->where('security_id', '=', 1);
        $result = $builder->query();
        
        if($result->is_loaded()) {
            $maps = array();
            foreach($result as $record) {
                $maps[] = DB_ORM::model('map', array((int)$record['id']));
            }
            
            return $maps;
        }
        
        return NULL;
    }

    public function getAllEnabledMap() {
        $builder = DB_SQL::select('default')
            ->from($this->table())
            ->where('enabled', '=', 1);
        $result = $builder->query();

        if($result->is_loaded()) {
            $maps = array();
            foreach($result as $record) {
                $maps[] = DB_ORM::model('map', array((int)$record['id']));
            }

            return $maps;
        }

        return NULL;
    }

	public function getAllEnabledOpenVisibleMap() {
		$builder = DB_SQL::select('default')
                ->from($this->table())
                ->where('enabled', '=', 1, 'AND')
                ->where('security_id', '=', 1);
        $result = $builder->query();
        
        if($result->is_loaded()) {
            $maps = array();
            foreach($result as $record) {
                $maps[] = DB_ORM::model('map', array((int)$record['id']));
            }

            return $maps;
        }
        
        return NULL;
	}

    /**
     * @param $user_id
     * @return array|null
     */
    public function getAllMapsForRegisteredUser($user_id){
        $builder = DB_SQL::select('default')
            ->all('m.*')
            ->from('maps', 'm')
            ->join('LEFT', 'map_users', 'mu')
            ->on('mu.map_id', '=', 'm.id')
            ->where('m.enabled', '=', 1)
            ->where('m.security_id', '=', 1, 'AND')
            ->where('m.security_id', '=', 2, 'OR')
            ->where('mu.user_id', '=', $user_id, 'AND');

        $result = $builder->query();

        if($result->is_loaded()) {
            $maps = array();
            foreach($result as $record) {
                $maps[] = DB_ORM::model('map', array((int)$record['id']));
            }

            return $maps;
        }

        return NULL;
    }

    public function getAllEnabledAndAuthoredMap($authorId) {
        $builder = DB_SQL::select('default')
            ->all('m.*')
            ->from('maps', 'm')
            ->join('LEFT', 'map_users', 'mu')
            ->on('mu.map_id', '=', 'm.id')
            ->where('enabled', '=', 1)
            ->where('author_id', '=', $authorId, 'AND')
            ->where('mu.user_id', '=', $authorId, 'OR');
        $result = $builder->query();
        
        if($result->is_loaded()) {
            $maps = array();
            foreach($result as $record) {
                $maps[] = DB_ORM::model('map', array((int)$record['id']));
            }
            
            return $maps;
        }
        
        return NULL;
    }
    
    public function getAllEnabledAndCloseMap() {
        $builder = DB_SQL::select('default')
                ->from($this->table())
                ->where('enabled', '=', 1, 'AND')
                ->where('security_id', '=', 2);
        $result = $builder->query();
        
        if($result->is_loaded()) {
            $maps = array();
            foreach($result as $record) {
                $maps[] = DB_ORM::model('map', array((int)$record['id']));
            }
            
            return $maps;
        }
        
        return NULL;
    }
    
    public function getAllEnabledAndKeyMap() {
        $builder = DB_SQL::select('default')
                ->from($this->table())
                ->where('enabled', '=', 1, 'AND')
                ->where('security_id', '=', 4);
        $result = $builder->query();
        
        if($result->is_loaded()) {
            $maps = array();
            foreach($result as $record) {
                $maps[] = DB_ORM::model('map', array((int)$record['id']));
            }
            
            return $maps;
        }
        
        return NULL;
    }
    
    public function createMap($values) {
        $this->name = Arr::get($values, 'title', 'empty_title');
        $this->author_id = Arr::get($values, 'author', 1);
        $this->abstract = Arr::get($values, 'description', 'empty_description');
        $this->keywords = Arr::get($values, 'keywords', 'empty_keywords');
        $this->type_id = Arr::get($values, 'type', 1);
        $this->skin_id = Arr::get($values, 'skin', 1);
        $this->timing = Arr::get($values, 'timing', FALSE);
        $this->delta_time = Arr::get($values, 'delta_time', 0);
        $this->security_id = Arr::get($values, 'security', 2);
        $this->section_id = Arr::get($values, 'section', 1);
        $this->language_id = 1;
        
        $this->save();
        
        $map = $this->getMapByName($this->name);
        DB_ORM::model('map_node')->createDefaultRootNode($map->id);
        
        return $map;
    }
    
    public function createVUEMap($title, $authorId) {
        $builder = DB_ORM::insert('map')
                ->column('name', $title)
                ->column('enabled', 1)
                ->column('abstract', 'VUE upload')
                ->column('author_id', $authorId)
                ->column('type_id', 3)
                ->column('security_id', 3)
                ->column('skin_id', 1)
                ->column('section_id', 2)
				->column('keywords', '')
				->column('timing', FALSE)
				->column('delta_time', 0)
				->column('startScore', 0)
				->column('threshold', 0)
				->column('units', '')
				->column('guid', '')
				->column('show_bar', FALSE)
				->column('show_score', FALSE)
				->column('feedback', '')
				->column('dev_notes', '')
				->column('source', '')
				->column('source_id', 0)
                ->column('language_id', 1);
        
        return $builder->execute();
    }
    
    public function disableMap($id) {
        $this->id = $id;
        $this->load();
        
        $this->enabled = FALSE;
        $this->save();
    }
    
    public function updateMap($id, $values) {
        $this->id = $id;
        $this->load();
        
        $this->name = Arr::get($values, 'title', 'empty_title');
        $this->abstract = Arr::get($values, 'description', 'empty_description');
        $this->keywords = Arr::get($values, 'keywords', 'empty_keywords');
        $this->type_id = Arr::get($values, 'type', 1);
        $this->skin_id = Arr::get($values, 'skin', 1);
        $this->timing = Arr::get($values, 'timing', FALSE);
        $this->delta_time = Arr::get($values, 'delta_time', 0);
        $this->security_id = Arr::get($values, 'security', 2);
        $this->section_id = Arr::get($values, 'section', 1);
        
        $this->save();
    }

    public function updateMapSkin($id, $value) {
        $this->id = $id;
        $this->load();

        $this->skin_id = $value;

        $this->save();
    }

    public function updateSection($mapId, $value) {
        $this->id = $mapId;
        $this->load();
        
        if($this) {
            $this->section_id = Arr::get($value, 'sectionview', $this->section_id);
            $this->save();
        }
    }
    
    public function updateFeedback($mapId, $feedback) {
        if($feedback != NULL) {
            $this->id = $mapId;
            $this->load();
            
            $this->feedback = $feedback;
            $this->save();
        }
    }
    
    public function getMaps($mapIDs) {
        $builder = DB_SQL::select('default')->from($this->table())->where('id', 'NOT IN', $mapIDs);
        $result = $builder->query();
        
        if($result->is_loaded()) {
            $maps = array();
            foreach($result as $record) {
                $maps[] = DB_ORM::model('map', array((int)$record['id']));
            }
            
            return $maps;
        }
        
        return NULL;
    }
    
    public function getMapsIn($mapIDs) {
        $builder = DB_SQL::select('default')->from($this->table())->where('id', 'IN', $mapIDs);
        $result = $builder->query();
        
        if($result->is_loaded()) {
            $maps = array();
            foreach($result as $record) {
                $maps[] = DB_ORM::model('map', array((int)$record['id']));
            }
            
            return $maps;
        }
        
        return NULL;
    }
    
    public function updateMapSecurity($mapId, $securityId) {
        $this->id = $mapId;
        $this->load();
        
        if($this->is_loaded()) {
            $this->security_id = $securityId;
            $this->save();
        }
    }
	
	public function getSearchMap($key, $onlyTitle = TRUE) {
		$builder = DB_SQL::select('default')->from($this->table())->where('enabled', '=', 1);
		
		$result = $builder->query();
        
        if($result->is_loaded()) {
            $maps = array();
            foreach($result as $record) {
				$map = DB_ORM::model('map', array((int)$record['id']));
                if($onlyTitle) {
					if(strpos($map->name, $key) === FALSE) {
					} else {
						$maps[] = $map;
					}
					
				} else {
					if(strpos($map->name, $key) === FALSE) {
						if(strpos($map->abstract, $key) === FALSE) {
						} else {
							$maps[] = $map;
						}
					} else {
						$maps[] = $map;
					}
				}
            }
            
            return $maps;
        }
        
        return NULL;
	}
}

?>
