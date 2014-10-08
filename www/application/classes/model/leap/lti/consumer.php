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
* Model for lti_consumers in database
*/
class Model_Leap_Lti_Consumer extends DB_ORM_Model {
    public function __construct() {
        parent::__construct();

        $this->fields = array(
            'id' => new DB_ORM_Field_Integer($this, array(
                    'max_length' => 11,
                    'nullable' => FALSE,
                    'savable' => TRUE,
                )),
            'consumer_key' => new DB_ORM_Field_String($this, array(
                    'max_length' => 255,
                    'nullable' => FALSE,
                    'savable' => TRUE,
                )),
            'name' => new DB_ORM_Field_String($this, array(
                    'max_length' => 45,
                    'nullable' => FALSE,
                    'savable' => TRUE,
                )),
            'secret' => new DB_ORM_Field_String($this, array(
                    'max_length' => 32,
                    'nullable' => FALSE,
                    'savable' => TRUE,
                )),
            'lti_version' => new DB_ORM_Field_String($this, array(
                    'max_length' => 12,
                    'nullable' => TRUE,
                    'savable' => TRUE,
                    'default' => NULL
                )),
            'consumer_name' => new DB_ORM_Field_String($this, array(
                    'max_length' => 255,
                    'nullable' => TRUE,
                    'savable' => TRUE,
                    'default' => NULL
                )),
            'consumer_version' => new DB_ORM_Field_String($this, array(
                    'max_length' => 255,
                    'nullable' => TRUE,
                    'savable' => TRUE,
                    'default' => NULL
                )),
            'consumer_guid' => new DB_ORM_Field_String($this, array(
                    'max_length' => 255,
                    'nullable' => TRUE,
                    'savable' => TRUE,
                    'default' => NULL
                )),
            'css_path' => new DB_ORM_Field_String($this, array(
                    'max_length' => 255,
                    'nullable' => TRUE,
                    'savable' => TRUE,
                    'default' => NULL
                )),
            'protected' => new DB_ORM_Field_Integer($this, array(
                    'max_length' => 1,
                    'nullable' => FALSE,
                    'unsigned' => TRUE,
                )),
            'enabled' => new DB_ORM_Field_Integer($this, array(
                    'max_length' => 1,
                    'nullable' => FALSE,
                    'unsigned' => TRUE,
                )),
            'enable_from' => new DB_ORM_Field_DateTime($this, array(
                    'savable' => TRUE,
                    'nullable' => TRUE,
                    'default' => NULL
                )),
            'enable_until' => new DB_ORM_Field_DateTime($this, array(
                    'savable' => TRUE,
                    'nullable' => TRUE,
                    'default' => NULL
                )),
            'without_end_date' => new DB_ORM_Field_Integer($this, array(
                    'max_length' => 1,
                    'nullable' => TRUE,
                    'unsigned' => TRUE,
                    'default' => false
                )),
            'last_access' => new DB_ORM_Field_Date($this, array(
                    'savable' => TRUE,
                    'nullable' => TRUE,
                    'default' => NULL
                )),
            'created' => new DB_ORM_Field_DateTime($this, array(
                    'savable' => TRUE,
                    'nullable' => FALSE,
                )),
            'updated' => new DB_ORM_Field_DateTime($this, array(
                    'savable' => TRUE,
                    'nullable' => FALSE,
                )),
            'role' => new DB_ORM_Field_Integer($this, array(
                    'max_length' => 11,
                    'nullable' => FALSE,
                    'default' => 1,
                ))
        );
    }
    public static function data_source() {
        return 'default';
    }

    public static function table() {
        return 'lti_consumers';
    }

    public static function primary_key() {
        return array('id');
    }

    public function saveConsumer($key, $values)
    {
        $hours      = Arr::get($values, 'hours', '00');
        $minute     = Arr::get($values, 'minute', '00');
        $dateString = Arr::get($values, 'date', '').' '.$hours.':'.$minute.':00';
        $date = null;
        if(strlen($dateString) > 5) {
            $date = new DateTime($dateString);
            if($date != null) {
                $dateString = $date->format('Y-m-d H:i:s');
            }
        }

        $hoursEnd = Arr::get($values, 'hoursEnd', '00');
        $minuteEnd = Arr::get($values, 'minuteEnd', '00');
        $endDateString = Arr::get($values, 'dateEnd', '').' '.$hoursEnd.':'.$minuteEnd.':00';
        $endDate = null;
        if(strlen($endDateString) > 5) {
            $endDate = new DateTime($endDateString);
            if($endDate != null) {
                $endDateString = $endDate->format('Y-m-d H:i:s');
            } else if($date != null) {
                $endDate = $date->add(new DateInterval('P1D'));
                $endDateString = $endDate->format('Y-m-d H:i:s');
            }
        } else if($date != null) {
            $endDate = $date->add(new DateInterval('P1D'));
            $endDateString = $endDate->format('Y-m-d H:i:s');
        }

        // if $key numeric - id of record, else - consumer key
        $user = is_numeric($key)
            ? DB_ORM::model('Lti_Consumer', array($key))
            : DB_ORM::select('Lti_Consumer')->where('consumer_key' , '=', $key)->query()->fetch();
        if(isset($values['enable_from'])){
            $enableFrom     = Arr::get($values, 'enable_from', $user->enable_from);
            $enableUntil    = Arr::get($values, 'enable_until', $user->enable_until);
        } else {
            $enableFrom     = ($date != null) ? $dateString : $user->enable_from;
            $enableUntil    = ($endDate != null) ? $endDateString : $user->enable_until;
        }

        if ($user) {
            $user->name             = Arr::get($values, 'name', $user->name);
            $user->secret           = Arr::get($values, 'secret', $user->secret);
            $user->lti_version      = Arr::get($values, 'lti_version', $user->lti_version);
            $user->consumer_name    = Arr::get($values, 'consumer_name', $user->consumer_name);
            $user->consumer_version = Arr::get($values, 'consumer_version', $user->consumer_version);
            $user->consumer_guid    = Arr::get($values, 'consumer_guid', $user->consumer_guid);
            $user->css_path         = Arr::get($values, 'css_path', $user->css_path);
            $user->protected        = Arr::get($values, 'protected', $user->protected);
            $user->enabled          = Arr::get($values, 'active', $user->enabled);
            $user->enable_from      = $enableFrom;
            $user->enable_until     = $enableUntil;
            $user->without_end_date = Arr::get($values, 'without_end_date');
            $user->last_access      = Arr::get($values, 'last_access', $user->last_access);
            $user->updated          = Arr::get($values, 'updated', $user->updated);
            $user->role             = Arr::get($values, 'permission', $user->role);
            $user->save();
        } else {
            $statusKey = false;
            $usersList = $this->getAll();
            do{
                $newKey = 'SW-'.$this->getRandomString(6);
                foreach($usersList as $user) {
                    if($user->consumer_key == $newKey) $statusKey = true;
                }
            }
            while ($statusKey);

            $key = DB_ORM::insert('Lti_Consumer')
                ->column('consumer_key',        $newKey)
                ->column('name',            Arr::get($values, 'name', ''))
                ->column('secret',          $this->getRandomString(32))
                ->column('lti_version',     Arr::get($values, 'lti_version', NULL))
                ->column('consumer_name',   Arr::get($values, 'consumer_name', NULL))
                ->column('consumer_version',Arr::get($values, 'consumer_version', NULL))
                ->column('consumer_guid',   Arr::get($values, 'consumer_guid', NULL))
                ->column('css_path',        Arr::get($values, 'css_path', NULL))
                ->column('protected',       Arr::get($values, 'protected', ''))
                ->column('enabled',         Arr::get($values, 'active', ''))
                ->column('enable_from',     $enableFrom)
                ->column('enable_until',    $enableUntil)
                ->column('without_end_date',Arr::get($values,  'without_end_date', false) ? 1 : 0)
                ->column('last_access',     NULL)
                ->column('created',         date("Y-m-d H:i:s"))
                ->column('updated',         date("Y-m-d H:i:s"))
                ->column('role',            Arr::get($values,  'permission',  1))
                ->execute();
        }
        return $key;
    }

    public function getAll() {
        return DB_ORM::select('Lti_Consumer')->query()->as_array();
    }

    private function getRandomString($length = 8) {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        $value = '';
        $charsLength = strlen($chars) - 1;
        for ($i = 1 ; $i <= $length; $i++) {
            $value .= $chars[rand(0, $charsLength)];
        }
        return $value;
    }
}