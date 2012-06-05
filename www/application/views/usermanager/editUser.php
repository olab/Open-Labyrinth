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
            <h4><?php echo __('Edit')." ".__('user account'); ?></h4>
            <?php if(isset($templateData['user'])) { ?>
            <table width="100%" cellpadding="6">
                <tr bgcolor="#ffffff"><td>
                        <form action=<?php echo URL::base().'usermanager/saveOldUser/'.$templateData['user']->id; ?> method="post">
                            <table>
                                <tr><td align="left"><p><?php echo __('username'); ?></p></td><td align="left"><p><?php echo $templateData['user']->username; ?>&nbsp;<a href=<?php echo URL::base().'usermanager/deleteUser/'.$templateData['user']->id; ?>>[<?php echo __('delete'); ?>]</a></p></td></tr>

                                <tr><td align="left"><p><?php echo __('new password'); ?></p></td><td align="left"><p><input type="password" name="upw" size="20" value=""> <font color="red">*</font> password will be changed if value is not empty</p></td></tr>
                                <tr><td align="left"><p><?php echo __('name'); ?></p></td><td align="left"><input type="text" name="uname" size="50" value="<?php echo $templateData['user']->nickname; ?>"></td></tr>
                                <tr><td align="left"><p><?php echo __('e-mail'); ?></p></td><td align="left"><input type="text" name="uemail" size="50" value="<?php echo $templateData['user']->email; ?>"></td></tr>

                                <tr><td align="left"><p>
                                            <?php echo __('user type'); ?>
                                        </p></td><td align="left">
                                        <select name="usertype">
                                            <?php if(isset($templateData['types'])) { ?>
                                                <?php foreach($templateData['types'] as $type) { ?>
                                                    <option value="<?php echo $type->id; ?>" <?php if($type->id == $templateData['user']->type_id) echo 'selected'; ?>><?php echo $type->name; ?></option>
                                                <?php } ?>
                                            <?php } ?>
                                        </select>
                                    </td></tr>

                                <tr><td align="left"><p><?php echo __('language'); ?></p></td><td align="left"><p>[english <input type="radio" name="langID" value="1" <?php if($templateData['user']->language_id == 1) echo 'checked=""'; ?>>] [francais <input type="radio" name="langID" value="2" <?php if($templateData['user']->language_id == 2) echo 'checked=""'; ?>>] </p></td></tr>

                                <tr><td align="left"><p>&nbsp;</p></td><td align="left"><input type="submit" name="EditUserSubmit" value="<?php echo __('submit'); ?>"></td></tr>
                            </table>
                        </form>
                        <p><a href=<?php echo URL::base().'usermanager'; ?>><?php echo __('users'); ?></a></p>
                    </td></tr>
            </table>
            <?php } ?>
        </td>
    </tr>
</table>
