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
                <h4><?php echo __('Edit skin of Labyrinth') . ' "' . $templateData['map']->name . '"'; ?></h4>
                <table width="100%" cellpadding="3">
                    <tr bgcolor="#ffffff"><td align="left">
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
                            <table border="0" width="50%" cellpadding="3">
                                <tr>
                                    <td style="width:100px;"><p>Current skin:</p></td>
                                    <td colspan="2"><p>
                                    <?php if ($templateData['skin']->name != NULL){
                                        echo $templateData['skin']->name;
                                    }else{
                                        echo 'not selected';
                                    }?></p>
                                    </td>
                                </tr>
                            </table>
                        </td></tr>
                </table>
            </td>
        </tr>
    </table>
<?php } ?>


