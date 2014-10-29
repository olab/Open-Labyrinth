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
 * Model for map_question_responces table in database
 */
class Model_Leap_Map_Question_Validation extends DB_ORM_Model {

    public $one_parameter = array(
        'isEmail'           => 'str',
        'isAlpha'           => 'str',
        'isNumeric'         => 'str',
        'isAlphanumeric'    => 'str',
        'isHexadecimal'     => 'str',
        'isHexColor'        => 'str',
        'isLowercase'       => 'str',
        'isUppercase'       => 'str',
        'isInt'             => 'str',
        'isFloat'           => 'str',
        'isNull'            => 'str',
        'isDate'            => 'str',
        'isCreditCard'      => 'str',
        'isJSON'            => 'str',
        'isMultibyte'       => 'str',
        'isAscii'           => 'str',
        'isFullWidth'       => 'str',
        'isHalfWidth'       => 'str',
        'isVariableWidth'   => 'str',
        'isSurrogatePair'   => 'str'
    );

    public $two_parameter = array(
        'equals'        => 'comparison',
        'contains'      => 'seed',
        'matches'       => 'pattern',
        'isURL'         => 'options',
        'isIP'          => 'version',
        'isDivisibleBy' => 'number',
        'isUUID'        => 'version',
        'isAfter'       => 'date',
        'isBefore'      => 'date',
        'isIn'          => 'values',
        'isISBN'        => 'version'
    );

    public $three_parameter = array(
        'isLength'      => 'range',
        'isByteLength'  => 'range'
    );

    public function __construct() {
        parent::__construct();

        $this->fields = array(
            'id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
                'unsigned' => TRUE,
            )),
            'question_id' => new DB_ORM_Field_Integer($this, array(
                'max_length' => 11,
                'nullable' => FALSE,
            )),
            'validator' => new DB_ORM_Field_Text($this, array(
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            'second_parameter' => new DB_ORM_Field_Text($this, array(
                'nullable' => FALSE,
                'savable' => TRUE,
            )),
            'error_message' => new DB_ORM_Field_Text($this, array(
                'nullable' => FALSE,
                'savable' => TRUE,
            ))
        );
    }

    public static function data_source() {
        return 'default';
    }

    public static function table() {
        return 'map_question_validation';
    }

    public static function primary_key() {
        return array('id');
    }

    public function update($questionId, $validator, $secondParameter, $errorMessage)
    {
        $questionObj = $this->getRecord($questionId);
        if ($questionObj)
        {
            $this->id = $questionObj->id;
            $this->load();
        }

        $this->question_id = $questionId;
        $this->validator = $validator;
        $this->second_parameter = $secondParameter;
        $this->error_message = $errorMessage;
        $this->save();
    }

    public function getRecord($questionId)
    {
        if ( ! $questionId) return false;

        return DB_ORM::select('Map_Question_Validation')->where('question_id', '=', $questionId)->query()->fetch(0);
    }

    public function deleteByQuestionId($questionId)
    {
        DB_ORM::delete('Map_Question_Validation')->where('question_id', '=', $questionId)->execute();
    }

    public function exportMVP($questionId)
    {
        return DB_SQL::select('default')->from('map_question_validation')->where('question_id', '=', $questionId)->query()->as_array();
    }
}