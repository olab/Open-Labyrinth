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
        <link rel="stylesheet" type="text/css" href="<?php echo URL::base(); ?>css/basic.css" />
    </head>
    <body>
        <table cellspacing="6" cellpadding="6">
            <tr>
                <td bgcolor="#FFFFFF" valign="top">
                    <h4><font color="#000000"><?php echo __('counter'); ?>: <?php if(isset($name)) echo $name; ?></font></h4>
                    <p><?php if(isset($description)) echo $description; ?></p><p><?php if(isset($icon)) echo $icon; ?></p>
                </td>
                <td bgcolor="#FFFFFF" align="center" valign="top"><p><?php echo __('current value'); ?></p>
                    <p><font size="30"><strong><?php if(isset($currentValue)) echo $currentValue; ?></strong></font></p>
                </td>
            </tr>
            <tr>
                <td colspan="3" bgcolor="#FFFFFF" align="center" valign="top">
                    <p><a href="javascript:window.close();"><?php echo __('close window'); ?></a></p>
                </td>
            </tr>
        </table>
    </body>
</html>