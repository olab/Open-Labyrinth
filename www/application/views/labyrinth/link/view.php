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
if (isset($templateData['map'])) { ?>
    <table width="100%" height="100%" cellpadding="6">
        <tr>
            <td valign="top" bgcolor="#bbbbcb">
                <h4><?php echo __('edit links for Labyrinth "') . $templateData['map']->name . '"'; ?></h4>
                <p><?php __('links represent the options available to the user') ?></p>
                <table width="100%" cellpadding="6">
                    <tr bgcolor="#ffffff"><td>
                            <table border="0" width="100%" cellpadding="2" bgcolor="#eeeeee">
                                <tr><td><p>linked from</p></td><td><p>linked to</p></td></tr>
                                <?php if(isset($templateData['nodes']) and count($templateData['nodes']) > 0) { ?>
                                    <?php foreach($templateData['nodes'] as $node) { ?>
                                        <tr bgcolor="#ffffff">
                                            <td align="left">
                                                <a name="1"></a>
                                                <p>
                                                    <a name="1">linked from '<?php echo $node->title; ?>' (<?php echo $node->id; ?>) [</a>
                                                    <a href="<?php echo URL::base().'linkManager/editLinks/'.$templateData['map']->id.'/'.$node->id; ?>">add/edit links</a>],  [
                                                    <a href="<?php echo URL::base(); ?>renderLabyrinth/go/<?php echo $templateData['map']->id; ?>/<?php echo $node->id; ?>">preview</a>]
                                                </p>
                                            </td>
                                            <td align="left">
                                                <?php if(count($node->links) > 0) { ?>
                                                    <?php foreach($node->links as $link) { ?>
                                                        <p>
                                                            linked to <?php echo $link->node_2->id; ?> ("<?php echo $link->node_2->title; ?>")
                                                            [<a href="<?php echo URL::base(); ?>renderLabyrinth/go/<?php echo $templateData['map']->id; ?>/<?php echo $link->node_2->id; ?>">preview</a>],
                                                            [<a href="<?php echo URL::base(); ?>linkManager/editLinks/<?php echo $templateData['map']->id; ?>/<?php echo $link->node_2->id; ?>">links</a>]
                                                        </p>
                                                    <?php  } ?>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                <?php } ?>
                            </table>
                            <br>
                        </td></tr></table>
            </td>
        </tr>
    </table>
<?php } ?>


