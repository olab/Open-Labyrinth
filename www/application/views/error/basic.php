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
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title><?php echo __('Error') ?> > <?php echo __('OpenLabyrinth'); ?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />

        <link rel="stylesheet" href="<?php echo URL::base(); ?>css/error.css" />

        <link rel="shortcut icon" href="<?php echo URL::base(); ?>images/ico/favicon.ico" />
        <script type="text/javascript" src="<?php echo URL::base(); ?>scripts/jquery-1.7.2.min.js"></script>
        <script type="text/javascript" src="<?php echo URL::base(); ?>scripts/cookie-info.js"></script>
    </head>
    <body>
        <?php if(isset($center)) echo $center; ?>
    </body>
</html>