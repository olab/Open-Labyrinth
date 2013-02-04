<?php
/**
 * Created by JetBrains PhpStorm.
 * User: larjohns
 * Date: 19/10/2012
 * Time: 9:46 πμ
 * To change this template use File | Settings | File Templates.
 */

defined('SYSPATH') or die('No direct script access.');


class Model_Leap_Vocabulary_Triple extends DB_ORM_Model
{
    public function __construct()
    {
        parent::__construct();

        $this->fields = array(
            's' => new DB_ORM_Field_String($this, array(
                'max_length' => 200,
                'nullable' => FALSE,
                'savable' => FALSE,
            )),
            'p' => new DB_ORM_Field_String($this, array(
                'max_length' => 200,
                'nullable' => FALSE,
                'savable' => FALSE,
            )),
            'o' => new DB_ORM_Field_String($this, array(
                'max_length' => 200,
                'nullable' => FALSE,
                'savable' => FALSE,
            )),
            'type' => new DB_ORM_Field_String($this, array(
                'max_length' => 200,
                'nullable' => FALSE,
                'savable' => FALSE,
            )),

        );
    }

    public function toString()
    {
        $s = $this->s;
        $p = $this->p;
        $o = $this->o;

        if ($this->type == Model_Leap_Vocabulary_Term::)
            $o = "'$o'";
        else
            $o = "<$o>";

        return "<$s> <$p> $o.";
    }

}
