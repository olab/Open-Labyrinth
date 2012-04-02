<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Model for map_presentation_users table in database 
 */
class Model_Leap_Map_Presentation_User extends DB_ORM_Model {

    public function __construct() {
        parent::__construct();

        $this->fields = array(
            'id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
                'unsigned' => TRUE,
            )),
            'presentation_id' => new DB_ORM_Field_Integer($this, array(
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

        $this->relations = array(
            'user' => new DB_ORM_Relation_BelongsTo($this, array(
                'child_key' => array('user_id'),
                'parent_key' => array('id'),
                'parent_model' => 'user',
            )),
            
            'presentation' => new DB_ORM_Relation_BelongsTo($this, array(
                'child_key' => array('presentation_id'),
                'parent_key' => array('id'),
                'parent_model' => 'map_presentation',
            )),
        );
    }

    public static function data_source() {
        return 'default';
    }

    public static function table() {
        return 'map_presentation_users';
    }

    public static function primary_key() {
        return array('id');
    }

    public function add($presentationId, $userId) {
        if ($presentationId != NULL and $userId != NULL) {
            $this->presentation_id = $presentationId;
            $this->user_id = $userId;

            $this->save();
        }
    }

    public function addUsersFromGroup($presentationId, $groupId, $userTypeName = NULL) {
        $builder = DB_SQL::select('default')->from($this->table())->where('presentation_id', '=', $presentationId);
        $result = $builder->query();

        if ($result->is_loaded()) {
            $userIDs = array();
            foreach ($result as $record) {
                $userIDs[] = $record['user_id'];
            }

            $group = DB_ORM::model('group', array((int) $groupId));
            if ($group) {
                $groupUsersIDs = array();
                foreach ($group->users as $gUser) {
                    if ($userTypeName != NULL) {
                        if ($gUser->user->type->name == $userTypeName) {
                            $userIDs[] = $gUser->user_id;
                            $groupUsersIDs[] = $gUser->user_id;
                        }
                    } else {
                        $userIDs[] = $gUser->user_id;
                        $groupUsersIDs[] = $gUser->user_id;
                    }
                }

                $userIDs = array_unique($userIDs);
                $addUserIDs = array_diff($groupUsersIDs, $userIDs);
                foreach ($addUserIDs as $addUserId) {
                    $newUser = DB_ORM::model('map_presentation_user');
                    $newUser->presentation_id = $presentationId;
                    $newUser->user_id = $addUserId;

                    $newUser->save();
                }
            }
        } else {
            $userIDs = array();
            $group = DB_ORM::model('group', array((int) $groupId));
            if ($group) {
                
                $groupUsersIDs = array();
                foreach ($group->users as $gUser) {
                    if ($userTypeName != NULL) {
                        if ($gUser->user->type->name == $userTypeName) {
                            $userIDs[] = $gUser->user_id;
                            $groupUsersIDs[] = $gUser->user_id;
                        }
                    } else {
                        $userIDs[] = $gUser->user_id;
                        $groupUsersIDs[] = $gUser->user_id;
                    }
                }
                
                $userIDs = array_unique($userIDs);
                $addUserIDs = $groupUsersIDs;

                foreach ($addUserIDs as $addUserId) {
                    $newUser = DB_ORM::model('map_presentation_user');
                    $newUser->presentation_id = $presentationId;
                    $newUser->user_id = $addUserId;

                    $newUser->save();
                }
            }
        }
    }

    public function getAllByUserId($userId) {
        $builder = DB_SQL::select('default')
                ->from($this->table())
                ->where('user_id', '=', $userId);
        $result = $builder->query();
        
        if($result->is_loaded()) {
            $presentations = array();
            foreach($result as $record) {
                $r = DB_ORM::model('map_presentation_user', array((int)$record['id']));
                $presentations[] = $r->presentation;
            }
            
            return $presentations;
        }
        
        return NULL;
    }
}

?>