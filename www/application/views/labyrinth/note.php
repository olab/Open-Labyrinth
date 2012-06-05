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
        <center>
            <?php if(isset($map)) { ?>
            <table bgcolor="#ffffff" cellpadding="6" width="80%">
                <tr>
                    <td valign="top">
                        <p>
                            <img src="<?php echo URL::base(); ?>images/notes.gif" border="0" alt="notes" align="absmiddle" />
                            <strong>author notes for Labyrinth "<?php echo $map->name; ?>"</strong>
                        </p>
                        <form action="<?php echo URL::base(); ?>labyrinthManager/updateDevNodes/<?php echo $map->id; ?>" method="POST">
                            <input type="hidden" name="mapid" value="21">
                                <textarea cols="60" rows="12" name="devnotes"><?php echo $map->dev_notes; ?></textarea>
                                <input type="submit" name="Submit" value="<?php echo __('submit'); ?>" />
                        </form>
                        <p>
                            <a href="javascript:window.close();"><?php echo __('close window'); ?></a>
                        </p>
                    </td>
                </tr>
            </table>
            <?php } ?>
        </center>
    </body>
</html>