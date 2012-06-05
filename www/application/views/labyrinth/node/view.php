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
                <h4><?php echo __('edit nodes of Labyrinth') . ' "' . $templateData['map']->name . '"'; ?></h4>
                <table width="100%" cellpadding="3">
                    <tr bgcolor="#ffffff"><td align="left">
                            <p><br>[<a href="<?php echo URL::base().'nodeManager/addNode/'.$templateData['map']->id; ?>"><?php echo __('add a node'); ?></a>]&nbsp;[<a href="<?php echo URL::base().'nodeManager/sections/'.$templateData['map']->id; ?>">sections</a>]&nbsp;[<a href="<?php echo URL::base().'nodeManager/grid/'.$templateData['map']->id; ?>">nodegrid</a>]</p>
                            <table border="0" width="50%" cellpadding="3">
                                <tr><td colspan="3"><hr></td></tr>
                                <?php if(isset($templateData['nodes'])) { ?>
                                <?php foreach($templateData['nodes'] as $node) { ?>
                                    <tr>
                                        <td align="left">
                                            <p><?php echo $node->title; ?> <?php if($node->type->name == 'root') echo '[root]'; ?> (<?php echo $node->id; ?>)</p>
                                        </td>
                                        <td><p><a href="<?php echo URL::base().'nodeManager/editNode/'.$node->id; ?>">edit</a></p></td>
                                        <td><p><a href="<?php echo URL::base().'linkManager/index/'.$templateData['map']->id; ?>">links</a></p></td>
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


