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
 * Model for webinar steps table in database
 */
class Model_Leap_Webinar_Step extends DB_ORM_Model {

    public function __construct() {
        parent::__construct();

        $this->fields = array(
            'id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
                'unsigned' => TRUE,
            )),

            'webinar_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
                'unsigned' => TRUE,
            )),

            'name' => new DB_ORM_Field_String($this, array(
                'max_length' => 255,
                'nullable' => FALSE,
                'savable' => TRUE
            )),
        );

        $this->relations = array(
            'maps' => new DB_ORM_Relation_HasMany($this, array(
                'child_key' => array('step', 'webinar_id'),
                'child_model' => 'webinar_map',
                'parent_key' => array('id', 'webinar_id')
            )),
        );
    }

    public static function data_source() {
        return 'default';
    }

    public static function table() {
        return 'webinar_steps';
    }

    public static function primary_key() {
        return array('id');
    }

    /**
     * Remove all steps from webinar
     *
     * @param integer $webinarId - webinar ID
     */
    public function removeSteps($webinarId) {
        DB_SQL::delete('default')
                ->from($this->table())
                ->where('webinar_id', '=', $webinarId)
                ->execute();
    }

    /**
     * Add new step
     *
     * @param integer $webinarId - webinar ID
     * @param string $stepName - step name
     * @return integer - new webinar step ID
     */
    public function addStep($webinarId, $stepName) {
        return DB_ORM::insert('webinar_step')
                       ->column('webinar_id', $webinarId)
                       ->column('name', $stepName)
                       ->execute();
    }

    /**
     * Remove webinar step
     *
     * @param integer $stepId - webinar step ID
     */
    public function removeStep($stepId) {
        DB_ORM::delete('webinar_step')
                ->where('id', '=', $stepId)
                ->execute();
    }

    /**
     * Update webinar step
     *
     * @param integer $stepId - webinar step ID
     * @param string $name - new step name
     */
    public function updateStep($stepId, $name) {
        DB_ORM::update('webinar_step')
                ->set('name', $name)
                ->where('id', '=', $stepId)
                ->execute();
    }
}