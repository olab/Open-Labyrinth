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
                <h4><?php echo __('NodeGrid "') . $templateData['map']->name . '"'; ?></h4>
                <table width="100%" cellpadding="3">
                    <tr bgcolor="#ffffff"><td align="left">
                            <form action="<?php echo URL::base().'nodeManager/saveGrid/'.$templateData['map']->id; ?>" method="POST">
                                <p><input type="submit" name="Submit" value="<?php echo __('submit'); ?>"></p>
                                <?php if(isset($templateData['nodes']) and count($templateData['nodes']) > 0) { ?>
                                <table border="0" width="50%" cellpadding="3">
                                    <tr><td colspan="3"><hr></td></tr>
                                    <?php foreach($templateData['nodes'] as $node) { ?>
                                    <tr>
                                        <td valign="top"><p>ID:<?php echo $node->id; ?> <?php if($node->type->name == 'root') echo __('[root]'); ?></p></td>
                                        <td valign="top"><p><input type="text" size="50" name="title_<?php echo $node->id; ?>" value="<?php echo $node->title; ?>"></p></td>
                                        <td valign="top"><p><textarea cols="40" rows="4" name="text_<?php echo $node->id; ?>"><?php echo $node->text; ?></textarea></p></td>
                                    </tr>
                                    <?php } ?>
                                </table>
                                <?php } ?>
                                <p><input type="submit" name="Submit" value="<?php echo __('submit'); ?>"></p>
                            </form>
                        </td></tr>
                </table>
            </td>
        </tr>
    </table>
<?php } ?>