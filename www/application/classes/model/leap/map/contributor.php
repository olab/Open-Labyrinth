<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for map_contributors table in database 
 */
class Model_Leap_Map_Contributor extends DB_ORM_Model {

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
            )),
            
            'role_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            
            'name' => new DB_ORM_Field_String($this, array(
                'max_length' => 200,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            
            'organization' => new DB_ORM_Field_String($this, array(
                'max_length' => 200,
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
        );
        
        $this->relations = array(
            'role' => new DB_ORM_Relation_BelongsTo($this, array(
                'child_key' => array('role_id'),
                'parent_key' => array('id'),
                'parent_model' => 'map_contributor_role',
            )),
        );
    }

    public static function data_source() {
        return 'default';
    }

    public static function table() {
        return 'map_contributors';
    }

    public static function primary_key() {
        return array('id');
    }
    
    public function getAllContributors($mapId) {
        $builder = DB_SQL::select('default')->from($this->table())->where('map_id', '=', $mapId);
        $result = $builder->query();
        
        if($result->is_loaded()) {
            $contributors = array();
            foreach($result as $record) {
                $contributors[] = DB_ORM::model('map_contributor', array($record['id']));
            }
            
            return $contributors;
        }
        
        return NULL;
    }
    
    public function createContributor($mapId) {
        $this->map_id = $mapId;
        $this->role_id = 13; // 'Select' role default
        $this->save();
    }
    
    public function updateContributors($mapId, $values) {
        $contibutors = $this->getAllContributors($mapId);
        if(count($contibutors) > 0) {
            foreach($contibutors as $contributor) {
                $role = Arr::get($values, 'role_'.$contributor->id, NULL);
                $name = Arr::get($values, 'cname_'.$contributor->id, '');
                $organization = Arr::get($values, 'cnorg_'.$contributor->id, '');

                if($role != NULL) {
                    $contributor->role_id = $role;
                }

                if($name != NULL) {
                    $contributor->name = $name;
                }

                if($organization != NULL) {
                    $contributor->organization = $organization;
                }

                $contributor->save();
            }
        }
    }
}

?>
