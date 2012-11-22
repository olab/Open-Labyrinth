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
            <h4><?php echo __('Automated password recovery'); ?></h4>
            <table width="100%" cellpadding="6">
                <tr bgcolor="#ffffff"><td>
                        <form action="<?php echo URL::base(); ?>home/updateResetPassword" method="post">
                            <table cellspacing="10" cellpadding="0" border="0">
                                <tr>
                                    <td colspan="2"><p>Enter your new password</p></td>
                                </tr>
                                <tr>
                                    <td colspan="2"><hr /></td>
                                </tr>
                                <tr>
                                    <td width="140" align="right"><p>New password:</p></td>
                                    <td><input style="float:left;" type="password" name="newpswd" size="30" /></td>
                                </tr>
                                <tr>
                                    <td width="140" align="right"><p>Confirm new password:</p></td>
                                    <td><input style="float:left;" type="password" name="pswd_confirm" size="30" /></td>
                                </tr>
                                <tr><td colspan="2"><hr /></td></tr>
                                <?php if (!empty($templateData['passError'])){ ?>
                                <tr>
                                    <td colspan="2"><font style="color:red"><?php echo $templateData['passError']; ?></font></td>
                                </tr>
                                <?php } ?>
                                <tr><td colspan="2"><input type="submit" value="Submit"></td></tr>
                            </table>
                            <input type="hidden" name="token" value="<?php echo Security::token(); ?>" />
                            <input type="hidden" name="hashKey" value="<?php echo $templateData['hashKey']; ?>" />
                        </form>
                    </td></tr>
            </table>
        </td>
    </tr>
</table>