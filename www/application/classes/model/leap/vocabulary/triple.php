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
            'data_type' => new DB_ORM_Field_String($this, array(
                    'max_length' => 200,
                    'nullable' => True,
                    'savable' => FALSE,
                )),

        );
    }

    public function toString()
    {
        $s = $this->s;
        $p = $this->p;
        $o = $this->o;

        $arc_triple = array('s'=>$s,'p'=>$p,'o'=>$o);

        if($this->data_type!=NULL){
            $arc_triple['o_datatype']="http://www.w3.org/2001/XMLSchema#". $this->data_type;
        }

        if ($this->type == Model_Leap_Vocabulary_Term::Property)
            $arc_triple['o_type'] = "uri";
        else{//var_dump($this->type);
            $arc_triple['o_type'] = "uri";
            if($this->type == Model_Leap_Vocabulary_Term::Reverse){
                $arc_triple['s'] = $this->o;
                $arc_triple['o'] = $this->s;


            }
        }



        return $arc_triple;
    }

}
