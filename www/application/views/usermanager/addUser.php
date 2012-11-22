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
            <h4><?php echo __('Create user account'); ?></h4>
            <table width="100%" cellpadding="6">
                <tr bgcolor="#ffffff"><td>
                        <form action="<?php echo URL::base().'usermanager/newUserSummary'; ?>" method="post">
                            <table>
                                <tr><td align="left"><p><?php echo __('username'); ?></p></td><td align="left"><input class="not-autocomplete" type="text" name="uid" size="20" value=""></td></tr>
                                <tr><td align="left"><p><?php echo __('password'); ?></p></td><td align="left"><input type="password" name="upw" size="20" value=""></td></tr>
                                <tr><td align="left"><p><?php echo __('name'); ?></p></td><td align="left"><input class="not-autocomplete" type="text" name="uname" size="50" value=""></td></tr>
                                <tr><td align="left"><p><?php echo __('e-mail'); ?></p></td><td align="left"><input class="not-autocomplete" type="text" name="uemail" size="50" value=""></td></tr>

                                <tr><td align="left"><p>
                                            <?php echo __('user type'); ?>
                                        </p></td><td align="left">
                                        <select name="usertype">
                                            <?php if(isset($templateData['types'])) { ?>
                                                <?php foreach($templateData['types'] as $type) { ?>
                                                    <option value="<?php echo $type->id; ?>"><?php echo $type->name; ?></option>
                                                <?php } ?>
                                            <?php } ?>
                                        </select>
                                    </td></tr>

                                <tr><td align="left"><p><?php echo __('language'); ?></p></td><td align="left"><p>[english <input type="radio" name="langID" value="1">] [francais <input type="radio" name="langID" value="2">] </p></td></tr>

                                <tr><td align="left"><p>&nbsp;</p></td><td align="left"><input type="submit" name="SaveNewUserSubmit" value="<?php echo __('submit'); ?>"></td></tr>
                            </table>
                        </form>
                        <p><a href=<?php echo URL::base().'usermanager'; ?>><?php echo __('users'); ?></a></p>
                    </td></tr>
            </table>
        </td>
    </tr>
</table>