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
 * @property string $name
 * @property string $key
 */
class Model_Leap_Language extends DB_ORM_Model
{

    public function __construct()
    {
        parent::__construct();

        $this->fields = array(
            'id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => false,
                'unsigned' => true,
            )),
            'name' => new DB_ORM_Field_String($this, array(
                'max_length' => 20,
                'nullable' => false,
                'savable' => true,
            )),
            'key' => new DB_ORM_Field_String($this, array(
                'max_length' => 20,
                'nullable' => false,
                'savable' => true,
            )),
        );
    }

    public static function data_source()
    {
        return 'default';
    }

    public static function table()
    {
        return 'languages';
    }

    public static function primary_key()
    {
        return array('id');
    }

    /**
     * @return DB_ResultSet|self|static
     */
    public static function all()
    {
        return DB_ORM::select('Language')->order_by('name')->query();
    }

    public function getLanguageByName($name)
    {
        $record = DB_SQL::select('default')
            ->from(static::table())
            ->column('id')
            ->where('name', '=', $name)
            ->limit(1)
            ->query();

        if ($record->is_loaded()) {
            return DB_ORM::model('language', array((int)$record[0]['id']));
        }

        return null;
    }
}
