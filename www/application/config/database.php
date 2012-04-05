<?php defined('SYSPATH') or die('No direct access allowed.');

$config = array();

$config['default'] = array(
    'type'          => 'mssql',     // string (e.g. db2, drizzle, firebird, mariadb, mssql, mysql, oracle, postgresql, or sqlite)
    'driver'        => 'standard',  // string (e.g. standard, improved, or pdo)
    'connection'    => array(
        'persistent'    => FALSE,       // boolean
        'hostname'      => 'ACER-PC\SQLEXPRESS', // string
        'port'          => '',          // string
        'database'      => 'openlabyrinth',          // string
        'username'      => 'sa',      // string
        'password'      => '#passfor_sqlsa123',      // string
    ),
    'caching'       => FALSE,       // boolean
    'charset'       => 'utf8',      // string
    'profiling'     => FALSE,       // boolean
    'table_prefix'  => '',          // string
);

return $config;
?>