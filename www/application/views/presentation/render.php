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
if(isset($templateData['presentation'])) { ?>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>OpenLabyrinth</title>
        <link rel="stylesheet" type="text/css" href="<?php echo URL::base(); ?>css/basic.css">
    </head>
    <body>
        <center>
            <table bgcolor="#ffffff" cellpadding="6" width="100%">
                <tr>
                    <td valign="top" width="20%">
                        <table width="100%" cellpadding="6">
                            <tr bgcolor="#ffffff">
                                <td align="left">
                                    <table border="0" width="100%" cellpadding="1">
                                        <tr>
                                            <td valign="top">
                                                <h4><?php echo $templateData['presentation']->title; ?></h4>
                                                <p><?php echo $templateData['presentation']->header; ?></p>
                                                <hr>
                                                    <?php foreach($templateData['presentation']->maps as $map) { ?>
                                                    <p><a href="<?php echo URL::base(); ?>renderLabyrinth/index/<?php echo $map->map->id; ?>"><?php echo $map->map->name; ?></a></p>
                                                    <?php } ?>
                                                    <hr />
                                                    <p><?php echo $templateData['presentation']->footer; ?></p>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </center>
    </body>
</html>
<?php } ?>
