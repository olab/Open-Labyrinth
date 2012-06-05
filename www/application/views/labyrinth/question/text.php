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
                <h4><?php echo __('questions "') . $templateData['map']->name . '"'; ?></h4>

                <table width="100%" cellpadding="6">
                    <tr bgcolor="#ffffff"><td>
                            <?php if(isset($templateData['question'])) { ?>
                            <form method="POST" action="<?php echo URL::base().'questionManager/updateQuestion/'.$templateData['map']->id.'/'.$templateData['questionType'].'/'.$templateData['question']->id; ?>">
                            <?php } else { ?>
                            <form method="POST" action="<?php echo URL::base().'questionManager/saveNewQuestion/'.$templateData['map']->id.'/'.$templateData['questionType']; ?>">
                            <?php } ?>
                                <table border="0" width="100%" cellpadding="1">
                                    <tr><td><p>stem:</p></td><td><p><textarea cols="50" rows="3" name="qstem"><?php if(isset($templateData['question'])) echo $templateData['question']->stem; ?></textarea></p></td></tr>
                                    <tr><td><p><?php echo __('width'); ?>:</p></td><td><p>
                                                <select name="qwidth">
                                                    <?php for($i = 10; $i <= 60; $i += 10) { ?>
                                                        <option value="<?php echo $i; ?>" <?php if(isset($templateData['question']) and $templateData['question']->width == $i) echo 'selected=""'; ?>><?php echo $i; ?></option>
                                                    <?php } ?>
                                                </select></p></td></tr>
                                    <tr><td><p><?php echo __('feedback'); ?>:</p></td><td><p><textarea cols="60" rows="3" name="fback"><?php if(isset($templateData['question'])) echo $templateData['question']->feedback; ?></textarea></p></td></tr>
                                    <tr><td colspan="2"><input type="submit" name="Submit" value="submit"></td></tr>
                                </table>
                            </form>
                        </td></tr>
                </table>
            </td>
        </tr>
    </table>
<?php } ?>


