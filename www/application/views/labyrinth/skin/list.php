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
                <h4><?php echo __('Skin for "').$templateData['map']->name.'"'; ?></h4>
                <table width="100%" cellpadding="6">
                    <tr bgcolor="#ffffff"><td style="padding:3px;" align="left">
                        <p>
                            [
                            <a href="<?php echo URL::base().'skinManager/createSkin/'.$templateData['map']->id; ?>">
                                <?php echo __('Create a new skin'); ?>
                            </a>]&nbsp;[
                            <a href="<?php echo URL::base().'skinManager/listSkins/'.$templateData['map']->id.'/'.$templateData['map']->skin_id; ?>">
                                <?php echo __('Select from a list of existing skins'); ?>
                            </a>]&nbsp;[
                            <a href="<?php echo URL::base().'skinManager/uploadSkin/'.$templateData['map']->id; ?>">
                                <?php echo __('Upload a new skin'); ?>
                            </a>]
                        </p>
                        <hr/>
                            <form id="form1" name="form1" method="post" action="<?php echo URL::base().'skinManager/saveSelectedSkin/'.$templateData['map']->id; ?>">
                                <table bgcolor="#ffffff" cellpadding="6" width="80%">
                                    <tr>
                                        <td style="width:50px;">
                                            <p><?php echo __('Name:'); ?></p>
                                        </td>
                                        <td>
                                            <select name="skinId">
                                                <option value="0">----</option>
                                                <?php
                                                    if (count($templateData['skinList']) > 0){
                                                        foreach($templateData['skinList'] as $skin){
                                                            echo '<option';
                                                            if ($skin->id == $templateData['skinId']){
                                                                echo ' selected="selected"';
                                                            }
                                                            echo ' value="'.$skin->id.'">'.$skin->name.'</option>';
                                                        }
                                                    }
                                                ?>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr><td colspan="2"><input type="submit" name="Submit" value="<?php echo __('submit'); ?>"></td></tr>
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