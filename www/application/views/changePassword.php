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
if(isset($templateData['user'])) { ?>
<table width="100%" height="100%" cellpadding='6'>
    <tr>
        <td valign="top" bgcolor="#bbbbcb">
            <h4><?php echo __('Change Labyrinth User Password'); ?></h4>
            <table width="100%" cellpadding="6">
                <tr bgcolor="#ffffff"><td>
                        <form action="<?php echo URL::base(); ?>home/updatePassword" method="post" id="form1" name="form1">
                            <table cellspacing="10" cellpadding="0" border="0">
                                <tr>
                                    <td><p>User ID</p></td>
                                    <td>
                                        <input type="text" name="uid_display" size="30" disabled="disabled" value="<?php echo $templateData['user']->nickname; ?>">
                                    </td>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr><td><p>Current Password</p></td><td><input type="password" name="upw" size="30"></td><td><div id="divMsg1" style="color:#FF0000;"></div></td></tr>
                                <tr><td colspan="3"><hr></td></tr>
                                <tr><td><p>New password</p></td><td><input type="password" name="newpswd" size="30"></td><td><div id="divMsg2" style="color:#FF0000;"></div></td></tr>
                                <tr><td><p>Confirm new password</p></td><td><input type="password" name="pswd_confirm" size="30"></td><td valign="top"><div id="divMsg3" style="color:#FF0000;"></div></td></tr>
                                <tr><td colspan="3"><hr></td></tr>
                                <tr><td colspan="3"><input type="submit" value="Submit"></td></tr>
                            </table>
                        </form>
                    </td></tr>
            </table>
        </td>
    </tr>
</table>
<?php } ?>