<?php defined('SYSPATH') or die('No direct script access.');

class Model_User extends Model_Auth_User {
    protected $_belongs_to = array(
        'language' => array(
            'model' => 'language',
            'foreign_key' => 'language_id'),
    );
}
