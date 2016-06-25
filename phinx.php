<?php

define('SYSPATH', realpath($system) . DIRECTORY_SEPARATOR);
require './www/application/config/database.php';

$config = $config["default"];

return array(
    'environments' => [
        'default_database' => 'production',
        'production' => [
            'adapter' => $config['type'],
            'host' => $config['connection']['hostname'],
            'name' => $config['connection']['database'],
            'user' => $config['connection']['username'],
            'pass' => $config['connection']['password'],
            'port' => $config['connection']['port'],
            'charset' => $config['charset'],
        ]
    ],
    
    'paths' => [
        'migrations' => './database/migrations',
        'seeds' => './database/seeds',
    ],
);