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
                <h4><?php echo __('Add Counter'); ?></h4>
                <table width="100%" cellpadding="6">
                    <tr bgcolor="#ffffff"><td align="left">
                            <form id="form1" name="form1" method="post" action="<?php echo URL::base().'counterManager/saveNewCounter/'.$templateData['map']->id; ?>">
                                <table bgcolor="#ffffff" cellpadding="6" width="80%">
                                    <tr><td><p><?php echo __('counter name'); ?></p></td><td colspan="2"><input type="text" name="cName" size="40" value=""></td></tr>
                                    <tr><td><p><?php echo __('counter description (optional)'); ?></p></td><td colspan="2"><textarea name="cDesc" rows="6" cols="40"></textarea></td></tr>
                                    <tr><td><p><?php echo __('counter image (optional)'); ?></p></td><td colspan="2">
                                            <select name="cIconId">
                                                <option value="" selected="">no image</option>
                                                <?php if(isset($templateData['images']) and count($templateData['images']) > 0) { ?>
                                                    <?php foreach($templateData['images'] as $image) { ?>
                                                        <option value="<?php echo $image->id; ?>"><?php echo $image->name; ?> (ID:<?php echo $image->id; ?>)</option>
                                                    <?php } ?>
                                                <?php } ?>
                                            </select>
                                        </td></tr>
                                    <tr><td><p><?php echo __('starting value (optional)'); ?></p></td><td><input type="text" name="cStartV" size="4" value=""></td><td></td></tr>
                                    <tr><td><p><?php echo __('visible'); ?></p></td><td><select name="cVisible"><option value="1" selected=""><?php echo __('show'); ?></option><option value="0"><?php echo __('don\'t show'); ?></option></select></td><td></td></tr>
                                    <tr><td colspan="3"><input type="submit" name="Submit" value="<?php echo __('submit'); ?>"></td></tr>
                                </table>
                            </form>
                            <br>
                            <br>
                        </td></tr>
                </table>
            </td>
        </tr>
    </table>
<?php } ?>