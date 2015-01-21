<?php
/**
 * Created by JetBrains PhpStorm.
 * User: larjohns
 * Date: 14/9/2012
 * Time: 10:00 πμ
 * To change this template use File | Settings | File Templates.
 */
require_once(Kohana::find_file('vendor', 'arc2/ARC2'));
require_once(Kohana::find_file('vendor', 'Graphite'));

class Controller_Mesh extends Controller_Hierarchicalpie
{
    public $title = "MeSH Classification Report";
    public $view = "mesh/report";
}

