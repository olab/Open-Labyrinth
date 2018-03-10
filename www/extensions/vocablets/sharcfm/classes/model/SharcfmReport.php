<?php
/**
 * Created by PhpStorm.
 * User: larjohns
 * Date: 13/5/2014
 * Time: 5:15 μμ
 */

require_once(Kohana::find_file('vendor', 'arc2/ARC2'));
require_once(Kohana::find_file('vendor', 'sparqllib'));

class Model_SharcfmReport extends Model_HierarchicalReport
{


    protected static  $broader = "http://www.w3.org/2004/02/skos/core#broader";
    protected  static $label = "http://www.w3.org/2004/02/skos/core#prefLabel";
    protected static $predicate = "http://purl.org/meducator/ns/Subject";

} 