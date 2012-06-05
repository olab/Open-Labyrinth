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
if (isset($templateData['sessions'])) { ?>
    <table width="100%" height="100%" cellpadding='6'>
        <tr>
            <td valign="top" bgcolor="#bbbbcb">
                <h4><?php echo __('my Labyrinth '); ?></h4>
                <table width="100%" cellpadding="6">
                    <tr bgcolor="#ffffff"><td>
                            <p><?php echo __('the following are your Labyrinth sessions organised by Labyrinth - click to view your performance within a session'); ?> ...</p>
                            <p><?php echo __('note that these are sessions with more than three nodes browsed within them'); ?></p>
                            <table width="100%">
                                <?php if(count($templateData['sessions']) > 0) { ?>
                                <?php foreach($templateData['sessions'] as $session) { ?>
                                <tr>
                                    <td><p><a href="<?php echo URL::base(); ?>reportManager/showReport/<?php echo $session->id; ?>">session at '<?php echo date('Y:m:d H:i:s', $session->start_time); ?> (<?php echo __('clicks'); ?> <?php echo count($session->traces); ?>)</a></p></td>
									<td><p><?php if(isset($templateData['bookmarks']) and isset($templateData['bookmarks'][$session->id])) { ?>
										<a href='<?php echo URL::base(); ?>renderLabyrinth/go/<?php echo $session->map_id; ?>/<?php echo $templateData['bookmarks'][$session->id]->node_id; ?>/b/<?php echo $templateData['bookmarks'][$session->id]->id; ?>'>bookmark at <?php echo $templateData['bookmarks'][$session->id]->node->title; ?></a>
									<?php } ?>
									</p></td>
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

