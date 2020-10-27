<?php
/**
 * Created by JetBrains PhpStorm.
 * User: larjohns
 * Date: 28/9/2012
 * Time: 9:46 πμ
 * To change this template use File | Settings | File Templates.
 */

defined('SYSPATH') or die('No direct script access.');

class Model_Leap_Metadata_StringRecord extends Model_Leap_Metadata_LiteralRecord
{
    public function __construct() {
        parent::__construct();


    }



    public static function table() {

        return 'metadata_string_fields';
    }
}

