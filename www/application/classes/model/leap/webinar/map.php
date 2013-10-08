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
 * Model for users table in database
 */
class Model_Leap_Webinar_Map extends DB_ORM_Model {

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

            'map_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
                'unsigned' => TRUE,
            )),

            'step' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
                'unsigned' => TRUE,
            ))
        );

        $this->relations = array(
            'map' => new DB_ORM_Relation_BelongsTo($this, array(
                'child_key' => array('map_id'),
                'parent_key' => array('id'),
                'parent_model' => 'map'
            ))
        );
    }

    public static function data_source() {
        return 'default';
    }

    public static function table() {
        return 'webinar_maps';
    }

    public static function primary_key() {
        return array('id');
    }

    /**
     * Remove all webinar maps
     *
     * @param integer $webinarId - webinar ID
     */
    public function removeMaps($webinarId) {
        DB_SQL::delete('default')
                ->from($this->table())
                ->where('webinar_id', '=', $webinarId)
                ->execute();
    }

    /**
     * Remove maps for webinar step
     *
     * @param integer $webinarId - webinar ID
     * @param integer $stepId - step ID
     */
    public function removeMapsForStep($webinarId, $stepId) {
        DB_SQL::delete('default')
                ->from($this->table())
                ->where('webinar_id', '=', $webinarId, 'AND')
                ->where('step', '=', $stepId)
                ->execute();
    }

    /**
     * Remove webinar map
     *
     * @param integer $webinarId - webinar ID
     * @param integer $mapId - map ID
     */
    public function removeMap($webinarId, $mapId) {
        DB_SQL::delete('default')
            ->from($this->table())
            ->where('webinar_id', '=', $webinarId, 'AND')
            ->where('map_id', '=', $mapId)
            ->execute();
    }

    /**
     * Add map to webinar
     *
     * @param integer $webinarId - webinar ID
     * @param integer $mapId - map ID
     * @param integer $step - map step
     * @return integer - webinar map ID
     */
    public function addMap($webinarId, $mapId, $step) {
        return DB_ORM::insert('webinar_map')
                       ->column('webinar_id', $webinarId)
                       ->column('map_id', $mapId)
                       ->column('step', $step)
                       ->execute();
    }
}