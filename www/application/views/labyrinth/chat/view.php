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
                <h4><?php echo __('Chats "') . $templateData['map']->name . '"'; ?></h4>
                <table width="100%" cellpadding="6">
                    <tr bgcolor="#ffffff">
                        <td>
                            <table border="0" width="100%" cellpadding="1">
                                <tr>
                                    <td align="left">
                                        <?php if(isset($templateData['chats']) and count($templateData['chats']) > 0) { ?>
                                        <?php foreach($templateData['chats'] as $chat) { ?>
                                            <p><input type="text" value="[[CHAT:<?php echo $chat->id; ?>]]"> <?php echo $chat->stem; ?> 
                                                [<a href="<?php echo URL::base().'chatManager/editChat/'.$templateData['map']->id.'/'.$chat->id.'/'.count($chat->elements); ?>"><?php echo __('edit'); ?></a> 
                                                - <a href="<?php echo URL::base().'chatManager/deleteChat/'.$templateData['map']->id.'/'.$chat->id; ?>"><?php echo __('delete'); ?></a>]</p>
                                        <?php } ?>
                                        <?php } ?>
                                    </td>
                                </tr>
                                <tr><td align="left"><p><a href="<?php echo URL::base().'chatManager/addChat/'.$templateData['map']->id.'/2'; ?>"><?php echo __('Add Chat'); ?></a></p></td></tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
<?php } ?>