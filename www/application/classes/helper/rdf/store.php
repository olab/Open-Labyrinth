<?php
/**
 * Created by JetBrains PhpStorm.
 * User: larjohns
 * Date: 8/12/2012
 * Time: 6:36 μμ
 * To change this template use File | Settings | File Templates.
 */

class Helper_RDF_Store {


    protected  static $endpoint;
    protected static $store;

    public static  function initialize(){


    }

    public static function getEndpoint(){

    }


    public static function getDriver(){
        $sparql_config = Kohana::$config->load('sparql');
        if(isset($sparql_config["driver"]))
            $driver = $sparql_config["driver"];
        if(!isset($driver))$driver = 'Helper_RDF_Store_Arc';
        return $driver;
    }


    public static function getStore(){

        $driver = self::getDriver();

        if(!isset($driver::$store))
            $driver::initialize();
        return $driver::$store;
    }



}
