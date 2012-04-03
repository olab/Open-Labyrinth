<?php defined('SYSPATH') or die('No direct access allowed.');

$config = array();

$config['default'] = array(
    'type'          => 'sqlite',     // string (e.g. db2, drizzle, firebird, mariadb, mssql, mysql, oracle, postgresql, or sqlite)
    'driver'        => 'pdo',  // string (e.g. standard, improved, or pdo)
    'connection'    => array(
        'persistent'    => FALSE,       // boolean
        'hostname'      => '', // string
        'port'          => '',          // string
        'database'      => 'openlabyrinth.db',          // string
        'username'      => '',      // string
        'password'      => '',      // string
    ),
    'caching'       => FALSE,       // boolean
    'charset'       => 'utf8',      // string
    'profiling'     => FALSE,       // boolean
    'table_prefix'  => '',          // string
);

return $config;
?>