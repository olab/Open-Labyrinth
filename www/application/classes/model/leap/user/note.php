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
 * @property int $id
 * @property int $user_id
 * @property int|null $session_id
 * @property int|null $webinar_id
 * @property string $text
 * @property string $created_at
 * @property string $updated_at
 * @property string|null $deleted_at
 * @property Model_Leap_User $user
 * @property Model_Leap_User_Session $session
 */
class Model_Leap_User_Note extends Model_Leap_Base
{

    public function __construct()
    {
        parent::__construct();

        $this->fields = array(
            'id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 10,
                'nullable' => false,
            )),
            'user_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 10,
                'nullable' => false,
                'unsigned' => true,
            )),
            'session_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 10,
                'nullable' => true,
                'unsigned' => true,
            )),
            'webinar_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => true,
                'savable' => true,
                'unsigned' => true,
            )),
            'text' => new DB_ORM_Field_Text($this, array(
                'max_length' => 65535,
                'nullable' => false,
            )),
            'created_at' => new DB_ORM_Field_String($this, array(
                'max_length' => 255,
                'nullable' => false,
                'savable' => true,
            )),
            'updated_at' => new DB_ORM_Field_String($this, array(
                'max_length' => 255,
                'nullable' => false,
                'savable' => true,
            )),
            'deleted_at' => new DB_ORM_Field_String($this, array(
                'max_length' => 255,
                'nullable' => true,
                'savable' => true,
            )),
        );

        $this->relations = array(
            'user' => new DB_ORM_Relation_BelongsTo($this, array(
                'child_key' => array('user_id'),
                'parent_key' => array('id'),
                'parent_model' => 'User',
            )),
            'session' => new DB_ORM_Relation_BelongsTo($this, array(
                'child_key' => array('session_id'),
                'parent_key' => array('id'),
                'parent_model' => 'User_Session',
            )),
        );
    }

    public static function data_source()
    {
        return 'default';
    }

    public static function table()
    {
        return 'user_notes';
    }

    public static function primary_key()
    {
        return array('id');
    }


    //-----------------------------------------------------
    // Additional helper methods
    //-----------------------------------------------------

    /**
     * @param $webinar_id
     * @throws Exception
     */
    public static function deleteByWebinarId($webinar_id)
    {
        if (empty($webinar_id) || !is_numeric($webinar_id)) {
            throw new Exception('webinar_id must be an integer > 0');
        }

        DB_ORM::update('User_Note')
            ->where('webinar_id', '=', $webinar_id)
            ->set('deleted_at', microtime(true))
            ->execute();
    }

    public function save($reload = false)
    {
        $id = $this->id;

        if ($id <= 0) {
            $this->created_at = microtime(true);
        }

        $this->updated_at = microtime(true);

        parent::save($reload);
    }
}