<?php
class Database{
    private static $config;
    public static function instance(){
        $dbConfigPath = DOCROOT.'application/config/database.php';
        if(!file_exists($dbConfigPath)){
            throw new \ErrorException('oLab database config file (<b>'.$dbConfigPath.'</b>) not found. Looks like oLab not being installed, if it is - ignore this message and start installation from main page.');
        }
        $config = require $dbConfigPath;
        self::$config = $config['default'];
        return new self;
    }

    public function connect(){
        $connection = self::$config['connection'];
        mysql_connect($connection['hostname'], $connection['username'], $connection['password'], $connection['password']) || die('Could not connect to server.' );
        mysql_select_db($connection['database']) or die('Could not select database.');
        mysql_set_charset(self::$config['charset']);
    }

    public function disconnect(){
        mysql_close();
    }
}

$updatesClassPath = DOCROOT.'application/classes/updates.php';
if(file_exists($updatesClassPath)) {
    require_once $updatesClassPath;
    $result = Updates::update();
}else{
    $result = 'not_found';
}

switch ($result){
    case 0:
        $center = '<div class="alert alert-info"><span class="lead">New updates to the database was not found.</span></div>';
        break;
    case 1:
        $center = '<div class="alert alert-success"><span class="lead">Database has been successfully updated.</span></div>';
        break;
    case 2:
        $center = '<div class="alert alert-danger"><span class="lead">Update directory "updates" was not found.</span></div>';
        break;
    case 3:
        $center = '<div class="alert alert-danger"><span class="lead">Update directory "updates" is not writable. Please check permissions to folder in your server.</span></div>';
        break;
    case 4:
        $center = '<div class="alert alert-danger"><span class="lead">Roll back instructions was not found.</span></div>';
        break;
    case 5:
        $center = '<div class="alert alert-success"><span class="lead">Database has been successfully Rolling Back.</span></div>';
        break;
    case 6:
        $center = '<div class="alert alert-danger"><span class="lead">Update directory "updates/roll_back" was not found.</span></div>';
        break;
    case 'not_found':
        $center = '<div class="alert alert-danger"><span class="lead">File '.$updatesClassPath.' not found.</span></div>';
        break;
}

return $center;