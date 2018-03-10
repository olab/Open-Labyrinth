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
if (isset($templateData['maps'])) { ?>
    <table width="100%" height="100%" cellpadding='6'>
        <tr>
            <td valign="top" bgcolor="#bbbbcb">
                <h4><?php echo __('my Labyrinth') . ' (' . count($templateData['maps']) . ')'; ?></h4>
                <table width="100%" cellpadding="6">
                    <tr bgcolor="#ffffff"><td>
                            <p><?php echo __('the following are your Labyrinth sessions organised by Labyrinth - click to view your performance within a session'); ?> ...</p>
                            <p><?php echo __('note that these are sessions with more than three nodes browsed within them'); ?></p>
                            <table width="100%">
                                <?php if(count($templateData['maps']) > 0) { ?>
                                <?php foreach($templateData['maps'] as $map) { ?>
                                <tr>
                                    <td><p><a href="<?php echo URL::base(); ?>playedLabyrinth/showMapInfo/<?php echo $map->id; ?>">Labyrinth: '<?php echo $map->name; ?>' (<?php echo $map->id; ?>)</a></p></td>
                                </tr>
                                <?php } ?>
                                <?php } ?>
                            </table>
                        </td></tr>
                </table>
            </td>
        </tr>
    </table>
<?php } ?>

