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
if (isset($templateData['service'])) { ?>
    <table width="100%" bgcolor="#ffffff"><tr><td>
                <table width="100%" bgcolor="#ffffff"><tr><td>
                            <h4><?php echo __('Labyrinths'); ?></h4>
                            <p><?php echo __('service name'); ?>: <?php echo $templateData['service']->name; ?></p>
                            <p>service IP mask: <?php echo $templateData['service']->ip; ?></p>
                            <p><?php echo __('Labyrinths'); ?>:</p>
                            <?php if(count($templateData['service']->maps) > 0) { ?>
                            <?php foreach($templateData['service']->maps as $map) { ?>
                            <p><?php echo $map->map->name; ?> (<?php echo $map->map->id; ?>) [<a href="<?php echo URL::base(); ?>remoteServiceManager/deleteMap/<?php echo $templateData['service']->id; ?>/<?php echo $map->id; ?>"><?php echo __('delete'); ?></a>]</p>
                            <?php } ?>
                            <?php } ?>
                            <hr>
                            <form id="form1" name="form1" method="post" action="<?php echo URL::base(); ?>remoteServiceManager/addMap/<?php echo $templateData['service']->id; ?>">
                                <table width="100%" border="0" cellspacing="0" cellpadding="4">
                                    <tr>
                                        <td><p><?php echo __('select a Labyrinth to add to this service'); ?></p></td>
                                        <td><select name="mapid" size="1">
                                                <?php if(isset($templateData['maps']) and count($templateData['maps']) > 0) { ?>
                                                <?php foreach($templateData['maps'] as $map) { ?>
                                                <option value="<?php echo $map->id; ?>"><?php echo $map->name; ?></option>
                                                <?php } ?>
                                                <?php } ?> 
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><p>&nbsp;</p></td>
                                        <td><input type="submit" name="Submit" value="<?php echo __('submit'); ?>"></td>
                                    </tr>
                                </table>
                            </form>
                            <hr>
                            <p><a href="#"><?php echo __('remote services'); ?></a></p>
                        </td></tr></table>
            </td></tr></table>
<?php } ?>