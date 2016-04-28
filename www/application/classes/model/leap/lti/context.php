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
 * Model for lti_contexts in database
 */
class Model_Leap_Lti_Context extends DB_ORM_Model {
    public function __construct() {
        parent::__construct();

        $this->fields = array(
            'consumer_key' => new DB_ORM_Field_String($this, array(
                    'max_length' => 255,
                    'nullable' => FALSE,
                    'savable' => TRUE,
                )),
            'context_id' => new DB_ORM_Field_String($this, array(
                    'max_length' => 255,
                    'nullable' => FALSE,
                    'savable' => TRUE,
                )),
            'lti_context_id' => new DB_ORM_Field_String($this, array(
                    'max_length' => 255,
                    'nullable' => TRUE,
                    'savable' => TRUE,
                    'default' => NULL
                )),
            'lti_resource_id' => new DB_ORM_Field_String($this, array(
                    'max_length' => 255,
                    'nullable' => TRUE,
                    'savable' => TRUE,
                    'default' => NULL
                )),
            'title' => new DB_ORM_Field_String($this, array(
                    'max_length' => 255,
                    'nullable' => FALSE,
                    'savable' => TRUE,
                )),
            'settings' => new DB_ORM_Field_Text($this, array(
                    'nullable' => TRUE,
                    'savable' => TRUE,
                    'default' => NULL
                )),
            'primary_consumer_key' => new DB_ORM_Field_String($this, array(
                    'max_length' => 255,
                    'nullable' => TRUE,
                    'savable' => TRUE,
                    'default' => NULL
                )),
            'primary_context_id' => new DB_ORM_Field_String($this, array(
                    'max_length' => 255,
                    'nullable' => TRUE,
                    'savable' => TRUE,
                    'default' => NULL
                )),
            'share_approved' => new DB_ORM_Field_Integer($this, array(
                    'max_length' => 1,
                    'nullable' => TRUE,
                    'unsigned' => TRUE,
                    'default' => NULL
                )),
            'created' => new DB_ORM_Field_DateTime($this, array(
                    'savable' => TRUE,
                    'nullable' => FALSE,
                )),
            'updated' => new DB_ORM_Field_DateTime($this, array(
                    'savable' => TRUE,
                    'nullable' => FALSE,
                ))
        );
        $this->relations = array(
            'context' => new DB_ORM_Relation_BelongsTo($this, array(
                    'child_key' => array('consumer_key'),
                    'parent_key' => array('consumer_key'),
                    'parent_model' => 'lti_consumer',
                )),
        );
    }
    public static function data_source() {
        return 'default';
    }

    public static function table() {
        return 'lti_contexts';
    }

    public static function primary_key() {
        return array('context_id');
    }
    public function addContext($consumerKey, $values) {
        DB_ORM::delete('Lti_Context')->where('consumer_key' , '=', $consumerKey)->execute();
        $builder =  DB_ORM::insert('Lti_Context')
            ->column('consumer_key',        $consumerKey)
            ->column('context_id',          Arr::get($values, 'context_id',''))
            ->column('lti_context_id',      Arr::get($values, 'lti_context_id', NULL))
            ->column('lti_resource_id',     Arr::get($values, 'lti_resource_id', NULL))
            ->column('title',               Arr::get($values, 'title', ''))
            ->column('settings',            Arr::get($values, 'settings', NULL))
            ->column('primary_consumer_key',Arr::get($values, 'primary_consumer_key', NULL))
            ->column('primary_context_id',  Arr::get($values, 'primary_context_id', NULL))
            ->column('share_approved',      Arr::get($values, 'share_approved', NULL))
            ->column('created',             Arr::get($values, 'created', ''))
            ->column('updated',             Arr::get($values, 'updated', ''));
        $result = $builder->execute();

        return $result;
    }

    public function updateContext($consumerKey, $values){
        $id = Arr::get($values,   'context_id');
        $query = DB_ORM::update('Lti_Context')
            ->set('lti_context_id',         Arr::get($values,   'lti_context_id',       $this->lti_context_id))
            ->set('lti_resource_id',        Arr::get($values,   'lti_resource_id',      $this->lti_resource_id))
            ->set('title',                  Arr::get($values,   'title',                $this->title))
            ->set('settings',               Arr::get($values,   'settings',             $this->settings))
            ->set('primary_consumer_key',   Arr::get($values,   'primary_consumer_key', $this->primary_consumer_key))
            ->set('primary_context_id',     Arr::get($values,   'primary_context_id',   $this->primary_context_id))
            ->set('share_approved',         Arr::get($values,   'share_approved',       $this->share_approved))
            ->set('created',                Arr::get($values,   'created',              $this->created))
            ->set('updated',                Arr::get($values,   'updated',              $this->updated))
            ->where('consumer_key', '=', $consumerKey, 'AND')->where('context_id', '=', $id);
        return $query->execute();
    }

    public function getAllRecords() {
        $builder = DB_SQL::select('default')->from($this->table());
        $result = $builder->query();
        if ($result->is_loaded()){
            $elements = array();
            foreach ($result as $record) $elements[] = DB_ORM::model('lti_context', array($record['context_id']));
            return $elements;
        }
        return NULL;
    }

    public function getByKeyId($key, $id){
        $builder = DB_SQL::select('default')->from($this->table())->where('consumer_key', '=', $key, 'AND')->where('context_id', '=', $id);
        $result = $builder->query();
        if($result->is_loaded()) {
            $result = $result[0];
            return $result;
        }
        return NULL;
    }
}