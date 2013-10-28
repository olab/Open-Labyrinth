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
 * Model for map_elements_metadata table in database
 */
class Model_Leap_Map_Element_Metadata extends DB_ORM_Model {
    public function __construct() {
        parent::__construct();

        $this->fields = array(
            'id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
                'unsigned' => TRUE,
            )),

            'element_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),

            'originURL' => new DB_ORM_Field_String($this, array(
                'max_length' => 300,
                'nullable' => TRUE,
                'savable' => TRUE,
            )),

            'description' => new DB_ORM_Field_Text($this, array(
                'nullable' => TRUE,
                'savable' => TRUE
            )),

            'copyright' => new DB_ORM_Field_Text($this, array(
                'nullable' => TRUE,
                'savable' => TRUE
            ))
        );
    }

    public static function data_source() {
        return 'default';
    }

    public static function table() {
        return 'map_elements_metadata';
    }

    public static function primary_key() {
        return array('id');
    }

    /**
     * Get metadata by element ID
     *
     * @param {integer} $id - element ID
     * @return mixed|null - null or metadata object
     */
    public function getMetadataByElementId($id) {
        $builder = DB_SQL::select('default')->from($this->table())->column('id')->where('element_id', '=', $id)->limit(1);
        $record  = $builder->query();

        if($record->is_loaded()) {
            return DB_ORM::model('map_element_metadata', array($record[0]['id']));
        }

        return null;
    }

    /**
     * Create new metadata object
     *
     * @param {integer} $elementId - element ID
     * @return mixed - created metadata object
     */
    public function createMetadata($elementId) {
        $metadataId = DB_ORM::insert('map_element_metadata')
                              ->column('element_id', $elementId)
                              ->execute();

        return DB_ORM::model('map_element_metadata', array($metadataId));
    }

    /**
     * Save metadata object
     *
     * @param {integer} $elementId - element ID
     * @param mixed $values - values
     */
    public function saveMetadata($elementId, $values) {
        $metadata = $this->getMetadataByElementId($elementId);
        if($metadata == null) {
            $metadata = $this->createMetadata($elementId);
        }

        $metadata->description = Arr::get($values, 'description', '');
        $metadata->originURL   = Arr::get($values, 'originURL', '');
        $metadata->copyright   = Arr::get($values, 'copyright', '');

        $metadata->save();
    }
}

?>