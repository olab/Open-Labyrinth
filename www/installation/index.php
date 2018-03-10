<?php
/**
 * Open Labyrinth [ http://www.openlabyrinth.ca ]
 *
 * Open Labyrinth is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Open Labyrinth is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Open Labyrinth.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @copyright Copyright 2012 Open Labyrinth. All Rights Reserved.
 *
 */

$application = 'application';
$modules = 'modules';
$system = 'system';
define('EXT', '.php');

$realpath = realpath(dirname(__FILE__));
define('INST_DOCROOT', realpath(dirname(__FILE__)).DIRECTORY_SEPARATOR);
define('DOCROOT', str_replace('installation', '', $realpath));

if ( ! is_dir($application) AND is_dir(DOCROOT.$application))
    $application = DOCROOT.$application;

if ( ! is_dir($modules) AND is_dir(DOCROOT.$modules))
    $modules = DOCROOT.$modules;

if ( ! is_dir($system) AND is_dir(DOCROOT.$system))
    $system = DOCROOT.$system;

define('APPPATH', realpath($application).DIRECTORY_SEPARATOR);
define('MODPATH', realpath($modules).DIRECTORY_SEPARATOR);
define('SYSPATH', realpath($system).DIRECTORY_SEPARATOR);

include_once(INST_DOCROOT.'classes/session.php');
include_once(INST_DOCROOT.'classes/url.php');
include_once(INST_DOCROOT.'classes/installation.php');
$checkBaseUrl = URL::base();
include_once(INST_DOCROOT.'classes/notice.php');
include_once(INST_DOCROOT.'classes/security.php');
include_once(INST_DOCROOT.'classes/arr.php');
include_once(INST_DOCROOT.'classes/core.php');

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=10,9,8" />
    <title>Installation | OpenLabyrinth</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <link rel="stylesheet" href="<?php echo URL::base(); ?>css/jquery-ui-1.9.1.custom.css" />
    <link rel="stylesheet" href="<?php echo URL::base(); ?>scripts/bootstrap/css/bootstrap.css" />
    <link rel="stylesheet" href="<?php echo URL::base(); ?>scripts/bootstrap/css/bootstrap-responsive.css" />

    <link rel="stylesheet" href="<?php echo URL::base(); ?>scripts/datepicker/css/datepicker.css" />
    <link rel="stylesheet" href="<?php echo URL::base(); ?>css/basic.css" />

    <link rel="shortcut icon" href="<?php echo URL::base(); ?>images/ico/favicon.ico" />
    <link rel="apple-touch-icon-precomposed" href="<?php echo URL::base(); ?>images/ico/apple-touch-icon-57-precomposed.png" />
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="<?php echo URL::base(); ?>images/ico/apple-touch-icon-72-precomposed.png" />
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="<?php echo URL::base(); ?>images/ico/apple-touch-icon-114-precomposed.png" />
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="<?php echo URL::base(); ?>images/ico/apple-touch-icon-144-precomposed.png" />
    <script type="text/javascript" src="<?php echo URL::base(); ?>scripts/jquery-1.7.2.min.js"></script>
</head>
<body>
<div class="navbar navbar-fixed-top">
    <div class="navbar-inner">
        <div class="container-fluid">
            <a href="<?php echo URL::base(); ?>" class="brand"><img src="<?php echo URL::base(); ?>images/openlabyrinth-header.png" alt="" /> <span>Open</span>Labyrinth</a>
        </div>
    </div>
</div>
<div class="container-fluid">
    <div class="row-fluid">
        <?php
        include_once(INST_DOCROOT.'view/main.php');
        ?>
    </div>
</div>
<script type="text/javascript" src="<?php echo URL::base(); ?>scripts/jquery-ui-1.9.1.custom.min.js"></script>
<script type="text/javascript" src="<?php echo URL::base(); ?>scripts/bootstrap/js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?php echo URL::base(); ?>scripts/datepicker/js/bootstrap-datepicker.js"></script>
</body>
</html>