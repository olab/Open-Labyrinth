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
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title><?php if(isset($templateData['title'])) echo $templateData['title']; ?></title>
        <link rel="stylesheet" type="text/css" href="<?php echo URL::base(); ?>css/basic.css" />
        <link rel="stylesheet" type="text/css" href="<?php echo URL::base(); ?>css/jquery-ui-1.9.1.custom.css" />
        <script type="text/javascript" src="<?php echo URL::base(); ?>scripts/jquery-1.7.2.min.js"></script>
        <script type="text/javascript" src="<?php echo URL::base(); ?>scripts/jquery-ui-1.9.1.custom.min.js"></script>
        <script type="text/javascript" src="<?php echo URL::base(); ?>scripts/application.js"></script>
    </head>

    <body>
        <center>
            <table bgcolor='#ffffff' cellpadding='6' width='100%'>
                <tr>
                    <td valign="top" width="20%">
                        <a href="<?php echo URL::base(); ?>"><img src="<?php echo URL::base(); ?>images/openlabyrinth-logo.jpg" alt="Labyrinth Identity" border="0" width="150" height="150"></a>
                        <h5> <font color="#000000"><?php echo __('OpenLabyrinth is an open source educational pathway authoring and delivery system'); ?></font></h5>
                        <?php if(isset($templateData['left'])) echo $templateData['left']; ?>
                        <hr />

                        <h5><font color="#000000">3.0</font></h5>
                    </td>
                    <td bgcolor='#ffffff' align='left' valign="top">
                        <?php if(isset($templateData['center'])) echo $templateData['center']; ?>
                    </td>
                    <?php if(isset($templateData['right'])) { ?>
                    <td width="20%" valign="top">
                         <?php echo $templateData['right']; ?>
                    </td>
                    <?php } ?>
                </tr>
                <tr>
                    <td style="color:red;" colspan="3"><?php if(isset($templateData['error'])) echo $templateData['error']; ?></td>
                </tr>
            </table>
        </center>
    </body>
</html>

