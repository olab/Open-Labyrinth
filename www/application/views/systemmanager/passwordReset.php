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
<form action="<?php echo URL::base().'systemManager/updatePasswordResetSettings/'; ?>" method="post">
    <table>
        <tr>
            <td align="left">
                <p><?php echo __('Mail from: '); ?></p>
            </td>
            <td align="left">
                <p><input size="50" type="text" name="mailfrom" value="<?php echo $templateData['email_config']['mailfrom']; ?>" /></p>
            </td>
        </tr>
        <tr>
            <td align="left">
                <p><?php echo __('From name: '); ?></p>
            </td>
            <td align="left">
                <p><input size="50" type="text" name="fromname" value="<?php echo $templateData['email_config']['fromname']; ?>" /></p>
            </td>
        </tr>
        <tr>
            <td align="left">
                <p><?php echo __('Request email subject: '); ?></p>
            </td>
            <td align="left">
                <p><input size="50" type="text" name="email_password_reset_subject" value="<?php echo $templateData['email_config']['email_password_reset_subject']; ?>" /></p>
            </td>
        </tr>
        <tr>
            <td align="left">
                <p><?php echo __('Request email body: '); ?></p>
            </td>
            <td align="left">
                <p><?php echo '<%name%> - tag that inserts name into email body'; ?></p>
                <p><?php echo '<%username%> - tag that inserts user name into email body'; ?></p>
                <p><?php echo '<%link%> - tag that inserts link to new password page into email body'; ?></p>
                <p>
                    <textarea style="width:320px; height:130px;" name="email_password_reset_body"><?php echo $templateData['email_config']['email_password_reset_body']; ?></textarea>
                </p>
            </td>
        </tr>
        <tr>
            <td align="left">
                <p><?php echo __('Complete email subject: '); ?></p>
            </td>
            <td align="left">
                <p><input size="50" type="text" name="email_password_complete_subject" value="<?php echo $templateData['email_config']['email_password_complete_subject']; ?>" /></p>
            </td>
        </tr>
        <tr>
            <td align="left">
                <p><?php echo __('Complete email body: '); ?></p>
            </td>
            <td align="left">
                <p>
                    <textarea style="width:320px; height:130px;" name="email_password_complete_body"><?php echo $templateData['email_config']['email_password_complete_body']; ?></textarea>
                </p>
            </td>
        </tr>
        <tr>
            <td colspan="2" align="left">
                <input type="Submit" value="<?php echo __('Submit'); ?>">
            </td>
        </tr>
    </table>
    <input type="hidden" name="token" value="<?php echo $templateData['token']; ?>" />
</form>
