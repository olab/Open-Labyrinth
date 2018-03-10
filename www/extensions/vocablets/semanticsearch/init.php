<?php
/**
 * Created by PhpStorm.
 * User: larjohns
 * Date: 24/5/2014
 * Time: 3:13 πμ
 */

Route::set('semanticsearch', 'semanticsearch(/<action>(/<id>)(/<id2>)(/<id3>)(/<id4>)(/<id5>)(/<id6>))')

    ->defaults(array(
        'controller' => 'semanticsearch',
        'action' => 'index',
    ));


