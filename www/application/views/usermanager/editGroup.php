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
<table width="100%" height="100%" cellpadding='6'>
    <tr>
        <td valign="top" bgcolor="#bbbbcb">
            <h4>Edit group</h4>
            <?php if (isset($templateData['group'])) { ?>
                <table width="100%" cellpadding="6">
                    <tr bgcolor="#ffffff">
                        <td>
                            <p><a href=<?php echo URL::base() . 'usermanager/deleteGroup/' . $templateData['group']->id; ?>>[delete]</a></p>
                            <form action=<?php echo URL::base() . 'usermanager/updateGroup/' . $templateData['group']->id; ?> method="post">
                                <table>
                                    <tr><td align="left"><p>group name</p></td>
                                        <td align="left"><input type="text" name="groupname" size="50" value="<?php echo $templateData['group']->name; ?>"></td></tr>

                                    <tr><td align="left"><p>&nbsp;</p></td><td align="left">
                                            <input type="submit" name="UpdateGroupSubmit" value="<?php echo __('submit'); ?>"></td></tr>
                                </table>
                            </form>
                            <p><strong>Members</strong></p>
                            <form action=<?php echo URL::base().'usermanager/addMemberToGroup/'.$templateData['group']->id; ?> method="post">
                                <?php if(isset($templateData['users'])) { ?>
                                <select name="userid">
                                    <?php foreach($templateData['users'] as $user) { ?>
                                    <option value="<?php echo $user->id; ?>"><?php echo $user->nickname; ?> (<?php echo $user->username; ?>)</option>
                                    <?php } ?>
                                </select>
                                <input type="submit" name="AddUserToGroupSubmit" value="<?php echo __('submit'); ?>">
                                <?php } ?>
                            </form>
                            
                            <?php if(isset($templateData['members'])) { ?>
                                <?php foreach($templateData['members'] as $member) { ?>
                                    <p><?php echo $member->nickname.'('.$member->username.')';?>[
                                        <a href=<?php echo URL::base().'usermanager/removeMember/'.$member->id.'/'.$templateData['group']->id; ?>>remove</a>]</p>
                                <?php } ?>
                            <?php } ?>
                        </td>
                    </tr>
                </table>
            <?php } ?>
        </td>
    </tr>
</table>
