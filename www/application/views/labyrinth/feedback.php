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
                <h4><?php echo __('feedback editor for Labyrinth: "') . $templateData['map']->name . '"'; ?></h4>
                <table width="100%" border="0" cellspacing="0" cellpadding="4" bgcolor="#ffffff">
                    <tr>
                        <td align="left">
                            <table width="100%" border="0" cellpadding="4">
                                <tr align="left">
                                    <td width="25%" align="left">
                                        <p><?php echo __('general feedback irrespective of how user performs'); ?></p>
                                    </td>
                                    <td>
                                        <form action="<?php echo URL::base().'feedbackManager/updateGeneral/'.$templateData['map']->id; ?>" method="POST">
                                            <textarea name="fb" rows="6" cols="50"><?php echo $templateData['map']->feedback; ?></textarea>
                                            <input type="submit" name="Submit" value="<?php echo __('update'); ?>">
                                        </form></td>
                                </tr>
                                <tr><td colspan="2"><hr></td></tr>
                                <tr align="left">
                                    <td width="25%" align="left">
                                        <p><?php echo __('feedback for time taken'); ?></p>
                                    </td>
                                    <td>
                                        <?php if(isset($templateData['time_feedback_rules']) and count($templateData['time_feedback_rules']) > 0) { ?>
                                        <?php foreach($templateData['time_feedback_rules'] as $rule) { ?>
                                            <p><img src="<?php echo URL::base(); ?>images/rule.gif" align="absmiddle" alt="rule"> <?php echo __('rule'); ?>:&nbsp;<?php echo __('if time taken is'); ?>&nbsp;<?php echo $rule->operator->title; ?>&nbsp;<?php echo $rule->value; ?>&nbsp;<?php echo __('then give feedback'); ?>&nbsp;[<?php echo $rule->message; ?>] - <a href="<?php echo URL::base().'feedbackManager/deleteRule/'.$templateData['map']->id.'/'.$rule->id; ?>"><?php echo __('delete'); ?></a></p>
                                        <?php } ?>
                                        <?php } ?>
                                        <hr>
                                        <form action="<?php echo URL::base().'feedbackManager/addRule/'.$templateData['map']->id.'/time'; ?>" method="POST">
                                            <p><?php echo __('if time taken in this session is'); ?><br>
                                                <select name="cop">
                                                    <?php if(isset($templateData['operators'])) { ?>
                                                    <option value="">select ...</option>
                                                    <?php if(count($templateData['operators']) > 0) { ?>
                                                        <?php foreach($templateData['operators'] as $operator) { ?>
                                                            <option value="<?php echo $operator->id; ?>"><?php echo $operator->title; ?></option>
                                                        <?php } ?>
                                                    <?php } ?>
                                                    <?php } ?>
                                                </select> <input type="text" name="cval" size="4"> <?php echo __('seconds'); ?><br>
                                                then feedback<br><textarea name="cMess" rows="3" cols="30"></textarea>
                                                <input type="submit" name="Submit" value="<?php echo __('create rule'); ?>"></p>
                                        </form>
                                    </td>
                                </tr>
                                <tr><td colspan="2"><hr></td></tr>
                                <tr align="left">
                                    <td width="25%" align="left">
                                        <p><?php echo __('feedback for nodes visited'); ?></p>
                                    </td>
                                    <td>
                                        <?php if(isset($templateData['visit_feedback_rules']) and count($templateData['visit_feedback_rules']) > 0) { ?>
                                        <?php foreach($templateData['visit_feedback_rules'] as $rule) { ?>
                                            <p><img src="<?php echo URL::base(); ?>images/rule.gif" align="absmiddle" alt="rule"> <?php echo __('rule'); ?>:&nbsp;<?php echo __('if visited node'); ?>&nbsp;<?php echo $rule->value; ?>&nbsp;<?php echo __('then give feedback'); ?>&nbsp;[<?php echo $rule->message; ?>] - <a href="<?php echo URL::base().'feedbackManager/deleteRule/'.$templateData['map']->id.'/'.$rule->id; ?>"><?php echo __('delete'); ?></a></p>
                                        <?php } ?>
                                        <?php } ?>
                                        <hr>
                                        <form action="<?php echo URL::base().'feedbackManager/addRule/'.$templateData['map']->id.'/visit'; ?>" method="POST">
                                            <p><?php echo __('if visited node'); ?><br>
                                                <select name="cval">
                                                    <option value="">select ...</option>
                                                    <?php if(isset($templateData['nodes']) and count($templateData['nodes']) > 0) { ?>
                                                    <?php foreach($templateData['nodes'] as $node) { ?>
                                                        <option value="<?php echo $node->id; ?>"><?php echo $node->id; ?>: <?php echo $node->title; ?></option>
                                                    <?php } ?>
                                                    <?php } ?>
                                                </select><br>
                                                <?php echo __('then feedback'); ?><br><textarea name="cMess" rows="3" cols="30"></textarea>
                                                <input type="submit" name="Submit" value="<?php echo __('create rule'); ?>"></p>
                                        </form>
                                    </td>
                                </tr>
                                <tr><td colspan="2"><hr></td></tr>
                                <tr align="left">
                                    <td width="25%" align="left">
                                        <p><?php echo __('feedback for must visit and must avoid nodes'); ?></p>
                                    </td>
                                    <td>
                                        <?php if(isset($templateData['must_visit_feedback_rules']) and count($templateData['must_visit_feedback_rules']) > 0) { ?>
                                            <?php foreach($templateData['must_visit_feedback_rules'] as $rule) { ?>
                                                <p><img src="<?php echo URL::base(); ?>images/rule.gif" align="absmiddle" alt="rule"> <?php echo __('rule'); ?>:&nbsp;<?php echo __('if visited must visit node'); ?>&nbsp;<?php echo $rule->operator->title; ?>&nbsp;<?php echo $rule->value; ?>&nbsp;<?php echo __('then give feedback'); ?>&nbsp;[<?php echo $rule->message; ?>] - <a href="<?php echo URL::base().'feedbackManager/deleteRule/'.$templateData['map']->id.'/'.$rule->id; ?>"><?php echo __('delete'); ?></a></p>
                                            <?php } ?>
                                        <?php } ?>
                                        <?php if(isset($templateData['must_avoid_feedback_rules']) and count($templateData['must_avoid_feedback_rules']) > 0) { ?>
                                            <?php foreach($templateData['must_avoid_feedback_rules'] as $rule) { ?>
                                                <p><img src="<?php echo URL::base(); ?>images/rule.gif" align="absmiddle" alt="rule"> <?php echo __('rule'); ?>:&nbsp;<?php echo __('if visited must avoid node'); ?>&nbsp;<?php echo $rule->operator->title; ?>&nbsp;<?php echo $rule->value; ?>&nbsp;<?php echo __('then give feedback'); ?>&nbsp;[<?php echo $rule->message; ?>] - <a href="<?php echo URL::base().'feedbackManager/deleteRule/'.$templateData['map']->id.'/'.$rule->id; ?>"><?php echo __('delete'); ?></a></p>
                                            <?php } ?>
                                        <?php } ?>
                                        <hr>
                                        <form action="<?php echo URL::base().'feedbackManager/addRule/'.$templateData['map']->id.'/must'; ?>" method="POST">
                                            <p><?php echo __('if the number of nodes of type'); ?> 
                                                <select name="crtype">
                                                    <option value=""><?php echo __('select'); ?> ...</option>
                                                    <option value="mustvisit"><?php echo __('must visit'); ?></option>
                                                    <option value="mustavoid"><?php echo __('must avoid'); ?></option>
                                                </select><br>
                                                <?php echo __('is'); ?>&nbsp;
                                                <select name="cop">
                                                    <?php if(isset($templateData['operators'])) { ?>
                                                    <option value="">select ...</option>
                                                    <?php if(count($templateData['operators']) > 0) { ?>
                                                        <?php foreach($templateData['operators'] as $operator) { ?>
                                                            <option value="<?php echo $operator->id; ?>"><?php echo $operator->title; ?></option>
                                                        <?php } ?>
                                                    <?php } ?>
                                                    <?php } ?>
                                                </select> 
                                                <input type="text" name="cval" size="4"><br><?php echo __('then feedback'); ?><br>
                                                <textarea name="cMess" rows="3" cols="30"></textarea>
                                                <input type="submit" name="Submit" value="<?php echo __('create rule'); ?>"></p>
                                        </form>
                                    </td>
                                </tr>
                                <tr><td colspan="2"><hr></td></tr>
                                <tr align="left">
                                    <td width="25%" align="left">
                                        <p><?php echo __('Counter Feedback Rules'); ?></p>
                                    </td>
                                    <td>
                                        <?php if(isset($templateData['counter_feedback_rules']) and count($templateData['counter_feedback_rules']) > 0) { ?>
                                            <?php foreach($templateData['counter_feedback_rules'] as $rule) { ?>
                                                <p><img src="<?php echo URL::base(); ?>images/rule.gif" align="absmiddle" alt="rule"> rule:&nbsp;if counter&nbsp;<?php echo $rule->counter_id; ?>&nbsp;is&nbsp;<?php echo $rule->operator->title; ?>&nbsp;<?php echo $rule->value; ?>&nbsp;then give feedback&nbsp;[<?php echo $rule->message; ?>] - <a href="<?php echo URL::base().'feedbackManager/deleteRule/'.$templateData['map']->id.'/'.$rule->id; ?>">delete</a></p>
                                            <?php } ?>
                                        <?php } ?>
                                        <hr>
                                        <form action="<?php echo URL::base().'feedbackManager/addRule/'.$templateData['map']->id.'/counter'; ?>" method="POST">
                                            <p><?php echo __('if counter'); ?> 
                                                <select name="cid">
                                                    <option value=""><?php echo __('select'); ?> ...</option>
                                                    <?php if(isset($templateData['counters']) and count($templateData['counters']) > 0) { ?>
                                                    <?php foreach($templateData['counters'] as $counter) { ?>
                                                        <option value="<?php echo $counter->id; ?>"><?php echo $counter->name; ?></option>
                                                    <?php } ?>
                                                    <?php } ?>
                                                </select><br>
                                                <?php echo __('is'); ?>&nbsp;
                                                <select name="cop">
                                                    <?php if(isset($templateData['operators'])) { ?>
                                                    <option value=""><?php echo __('select'); ?> ...</option>
                                                    <?php if(count($templateData['operators']) > 0) { ?>
                                                        <?php foreach($templateData['operators'] as $operator) { ?>
                                                            <option value="<?php echo $operator->id; ?>"><?php echo $operator->title; ?></option>
                                                        <?php } ?>
                                                    <?php } ?>
                                                    <?php } ?>
                                                </select>
                                                <input type="text" name="cval" size="4"><br><?php echo __('then feedback'); ?><br>
                                                <textarea name="cMess" rows="3" cols="30"></textarea>
                                                <input type="submit" name="Submit" value="<?php echo __('create rule'); ?>"></p>
                                        </form>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
<?php } ?>


