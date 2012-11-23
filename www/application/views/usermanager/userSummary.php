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
            <h4><?php echo __('User account'); ?></h4>
            <table width="100%" cellpadding="6">
                <tr bgcolor="#ffffff"><td>
                        <p><?php echo __('username'); ?>: <strong><?php if(isset($templateData['newUser']['uid'])) echo $templateData['newUser']['uid']; ?></strong><br>
                           <?php echo __('password'); ?>: <strong><?php if(isset($templateData['newUser']['upw'])) echo $templateData['newUser']['upw']; ?></strong><br>
                           <?php echo __('name'); ?>: <strong><?php if(isset($templateData['newUser']['uname'])) echo $templateData['newUser']['uname']; ?></strong><br>
                           <?php echo __('e-mail'); ?>: <strong><?php if(isset($templateData['newUser']['uemail'])) echo $templateData['newUser']['uemail']; ?></strong><br>
                           <?php echo __('user type'); ?>: <strong><?php if(isset($templateData['newUser']['usertype'])) echo $templateData['newUser']['usertype']; ?></strong><br>
                           <?php echo __('language'); ?>: <strong><?php if(isset($templateData['newUser']['langID'])) echo $templateData['newUser']['langID']; ?></strong></p>
                        <p style="color:green;"><?php echo __('New user added successfully.'); ?></p>
                        <p><a href=<?php echo URL::base().'usermanager'; ?>><?php echo __('users'); ?></a></p>
                    </td></tr>
            </table>
        </td>
    </tr>
</table>