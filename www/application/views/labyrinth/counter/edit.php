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
if (isset($templateData['map']) and isset($templateData['counter'])) { ?>
    <table width="100%" height="100%" cellpadding="6">
        <tr>
            <td valign="top" bgcolor="#bbbbcb">
                <h4><?php echo __('Edit Counter') . ' ' . $templateData['counter']->id . ' "' . $templateData['counter']->name . '"'; ?></h4>
                <table width="100%" cellpadding="6">
                    <tr bgcolor="#ffffff"><td align="left">
                            <form id="form1" name="form1" method="post" action="<?php echo URL::base().'counterManager/updateCounter/'.$templateData['map']->id.'/'.$templateData['counter']->id; ?>">
                                <table bgcolor="#ffffff" cellpadding="6" width="80%">
                                    <tr><td><p><?php echo __('counter name'); ?></p></td><td colspan="2"><input type="text" name="cName" size="40" value="<?php echo $templateData['counter']->name; ?>"></td></tr>
                                    <tr><td><p><?php echo __('counter description (optional)'); ?></p></td><td colspan="2"><textarea name="cDesc" rows="6" cols="40"><?php echo $templateData['counter']->description; ?></textarea></td></tr>
                                    <tr><td><p><?php echo __('counter image (optional)'); ?></p></td><td colspan="2">
                                            <select name="cIconId">
                                                <?php if($templateData['counter']->icon_id == 0) echo '<option value="0" selected="">no image</option>'; ?>
                                                <?php if(isset($templateData['images']) and count($templateData['images']) > 0) { ?>
                                                    <?php foreach($templateData['images'] as $image) { ?>
                                                        <option value="<?php echo $image->id; ?>" <?php if($templateData['counter']->icon_id == $image->id) echo 'selected=""'; ?>><?php echo $image->name; ?> (ID:<?php echo $image->id; ?>)</option>
                                                    <?php } ?>
                                                <?php } ?>
                                            </select></td></tr>
                                    <tr><td><p><?php echo __('starting value (optional)'); ?></p></td><td><input type="text" name="cStartV" size="4" value="<?php echo $templateData['counter']->start_value; ?>"></td><td></td></tr>
                                    <tr><td><p>visible</p></td><td><select name="cVisible"><option value="1" <?php if($templateData['counter']->visible) echo 'selected=""'; ?>>show</option><option value="0" <?php if(!$templateData['counter']->visible) echo 'selected=""'; ?>>don't show</option></select></td><td></td></tr>
                                    <tr><td colspan="3"><input type="submit" name="Submit" value="submit"></td></tr>
                                </table>
                            </form>
                            <hr>
                            <p><strong><?php echo __('counter rules'); ?></strong></p>
                            <?php if(isset($templateData['rules']) and count($templateData['rules']) > 0) { ?>
                                <?php foreach($templateData['rules'] as $rule) { ?>
                                    <p><img src="<?php echo URL::base(); ?>images/rule.gif" align="absmiddle" alt="rule"> rule: if '<?php echo $templateData['counter']->name; ?>' is <?php echo $rule->relation->title; ?> <?php echo $rule->value; ?> then go to node <?php echo $rule->redirect_node_id; ?> ('<?php echo $rule->redirect_node->title; ?>') and reset counter '<?php echo $rule->counter_value; ?>' - <a href="<?php echo URL::base().'counterManager/deleteRule/'.$templateData['map']->id.'/'.$templateData['counter']->id.'/'.$rule->id.'/'.$rule->redirect_node_id; ?>">delete</a></p>
                                <?php } ?>
                            <?php } ?>
                            <hr>
                            <form id="form2" name="form1" method="post" action="<?php echo URL::base().'counterManager/addRule/'.$templateData['map']->id.'/'.$templateData['counter']->id; ?>">
                                <p><strong><?php echo __('add counter rule'); ?></strong></p>
                                <p><?php echo __('if value of counter'); ?>
                                    <?php if(isset($templateData['relations']) and count($templateData['relations']) > 0) { ?>
                                    <select name="relation">
                                        <?php foreach($templateData['relations'] as $relation) { ?>
                                            <option value="<?php echo $relation->id; ?>"><?php echo $relation->title ?></option>
                                        <?php } ?>
                                    </select>
                                    <?php } ?>
                                     <input type="text" name="rulevalue"></p>

                                <p><?php echo __('then go to node'); ?>&nbsp;
                                    <?php if(isset($templateData['nodes']) and count($templateData['nodes']) > 0) { ?>
                                    <select name="node">
                                        <?php foreach($templateData['nodes'] as $node) { ?>
                                            <option value="<?php echo $node->id; ?>"><?php echo $node->title; ?></option>
                                        <?php } ?>
                                    </select>
                                    <?php } ?>
                                    </p>
                                <p><?php echo __('reset counter'); ?>&nbsp;<input type="text" name="ctrval" value="" size="4">&nbsp;<?php echo __('type +, - or = an integer - e.g. +1 or =32'); ?>&nbsp;<?php echo __('you need to change the value of the counter or it will loop'); ?></p>
                                <input type="submit" name="Submit" value="<?php echo __('submit'); ?>">
                                <br>
                            </form>
                            <br>
                        </td></tr>
                </table>
            </td>
        </tr>
    </table>
<?php } ?>