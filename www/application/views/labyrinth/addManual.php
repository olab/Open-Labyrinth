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
            <h4><?php echo __('add Labyrinth'); ?></h4>
            <table bgcolor="#ffffff"><tr><td align="left">
                        <form id="addManualForm" name="addManualForm" method="post" action=<?php echo URL::base().'labyrinthManager/addNewMap' ?>>
                            <table width="100%" border="0" cellspacing="0" cellpadding="4">
                                <tr>
                                    <td width="33%" align="right"><p><?php echo __('title'); ?></p></td>
                                    <td width="50%"><p><input name="title" type="text" id="mtitle" size="40" value=""></p></td>
                                </tr>
                                <tr>
                                    <td align="right"><p><?php echo __('description'); ?></p></td>
                                    <td align="left"><p><textarea name="description" cols="40" rows="5" id="mdesc"></textarea></p></td>
                                </tr>
                                <tr>
                                    <td align="right"><p><?php echo __('keywords'); ?></p></td>
                                    <td align="left"><p>
                                            <input name="keywords" type="text" id="keywords" size="40" value="">
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="right"><p><?php echo __('Labyrinth type'); ?></p></td>
                                    <td align="left"><p>
                                            <label>
                                                <select name="type">
                                                    <option value="1" selected="selected"><?php echo __('select'); ?></option>
                                                    <?php if(isset($templateData['types'])) { ?>
                                                        <?php foreach($templateData['types'] as $type) { ?>
                                                            <option value=<?php echo $type->id; ?>><?php echo $type->name; ?></option>
                                                        <?php } ?>
                                                    <?php } ?>
                                                </select>
                                            </label>
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="right"><p><?php echo __('Labyrinth Skin'); ?></p></td>
                                    <td align="left"><p>
                                            <label>
                                                <select name="skin">
                                                    <option value="1" selected="selected"><?php echo __('select'); ?></option>
                                                    <?php if(isset($templateData['skins'])) { ?>
                                                        <?php foreach($templateData['skins'] as $skin) { ?>
                                                            <option value=<?php echo $skin->id; ?>><?php echo $skin->name; ?></option>
                                                        <?php } ?>
                                                    <?php } ?>
                                                </select>
                                            </label>
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="right"><p><?php echo __('timing'); ?></p></td>
                                    <td align="left"><p>
                                            <?php echo __('timing off'); ?><input type="radio" name="timing" value=0 checked=""> : <?php echo __('timing on'); ?><input type="radio" name="timing" value=1>
                                            <br><br>
                                            <?php echo __('time delta (seconds)'); ?><input name="delta_time" type="text" id="delta_time" value="" size="6">
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="right"><p><?php echo __('security'); ?></p></td>
                                    <td align="left">
                                        <p>
                                            <?php if(isset($templateData['securities'])) { ?>
                                                <?php foreach($templateData['securities'] as $security) { ?>
                                                    <input type="radio" name="security" value=<?php echo $security->id; ?>><?php echo __($security->name); ?><br>
                                                <?php } ?>
                                            <?php } ?>
                                            <br><br></p>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="right"><p><?php echo __('section browsing'); ?></p></td>
                                    <td align="left"><p>
                                        </p><p>
                                            <?php if(isset($templateData['sections'])) { ?>
                                                <?php foreach($templateData['sections'] as $section) { ?>
                                                    <?php echo __($section->name); ?><input type="radio" name="section" value=<?php echo $section->id; ?>> |
                                                <?php } ?>
                                            <?php } ?>
                                            <br><br></p>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="left"><p>&nbsp;</p></td>
                                    <td align="left"><p>
                                            <label>
                                                <input type="submit" name="AddManualMapSubmit" value="<?php echo __('submit'); ?>">
                                            </label>
                                        </p></td>
                                    <td align="left"><p>&nbsp;</p></td>
                                </tr>
                            </table>
                        </form>
                    </td></tr></table>
        </td>
    </tr>
</table>

