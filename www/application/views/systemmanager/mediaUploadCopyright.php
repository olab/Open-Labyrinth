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
<form action="<?php echo URL::base().'systemManager/updateMediaUploadCopyright/'; ?>" method="post">
    <table>
        <tr>
            <td align="left">
                <p><?php echo __('Title: '); ?></p>
            </td>
            <td align="left">
                <p><input size="50" type="text" name="title" value="<?php echo $templateData['media_copyright']['title']; ?>" /></p>
            </td>
        </tr>
        <tr>
            <td align="left">
                <p><?php echo __('Copyright message: '); ?></p>
            </td>
            <td align="left">
                <p>
                    <textarea style="width:320px; height:130px;" name="copyright_message"><?php echo $templateData['media_copyright']['copyright_message']; ?></textarea>
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