<?php
/**
 * Created by JetBrains PhpStorm.
 * User: larjohns
 * Date: 8/12/2012
 * Time: 6:36 μμ
 * To change this template use File | Settings | File Templates.
 */
require_once(Kohana::find_file('vendor', 'arc2/ARC2'));
class Helper_RDF_Store {


    private static $endpoint;
    private static $store;

    public static  function initialize(){

        $db_config = Kohana::$config->load('database')->get('default');
        $sparql_config = Kohana::$config->load('sparql');

        $config = array(
            /* db */
            'db_host' => $db_config['connection']['hostname'], /* optional, default is localhost */
            'db_name' => $db_config['connection']['database'],
            'db_user' => $db_config['connection']['username'],
            'db_pwd' => $db_config['connection']['password'],

            /* store name */
            'store_name' => 'sparql',

            /* endpoint */
            'endpoint_features' => array(
                'select', 'construct', 'ask', 'describe',
                'load',
                'dump' /* dump is a special command for streaming SPOG export */
            ),
            'endpoint_timeout' => ini_get('max_execution_time'), /* not implemented in ARC2 preview */
            'time_limit'=>ini_get('max_execution_time') ,
            'endpoint_read_key' => '', /* optional */
            'endpoint_max_limit' => $sparql_config["endpoint_max_limit"], /* optional */
        );

        /* instantiation */
        self::$endpoint = ARC2::getStoreEndpoint($config);

        self::$store = ARC2::getStore($config);

        if (!self::$store->isSetUp()) {
            self::$store->setUp();
        }
        if (!self::$endpoint->isSetUp()) {
            self::$endpoint->setUp(); /* create MySQL tables */
        }

    }

    public static function getEndpoint(){

        if(!isset(self::$endpoint))
            self::initialize();
        return self::$endpoint;
    }


    public static function getStore(){

        if(!isset(self::$store))
            self::initialize();
        return self::$store;
    }



}
