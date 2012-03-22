<?php if (isset($templateData['map'])) { ?>
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
                                        <p>general feedback irrespective of how user performs</p>
                                    </td>
                                    <td>
                                        <form action="<?php echo URL::base().'feedbackManager/updateGeneral/'.$templateData['map']->id; ?>" method="POST">
                                            <textarea name="fb" rows="6" cols="50"><?php echo $templateData['map']->feedback; ?></textarea>
                                            <input type="submit" name="Submit" value="update">
                                        </form></td>
                                </tr>
                                <tr><td colspan="2"><hr></td></tr>
                                <tr align="left">
                                    <td width="25%" align="left">
                                        <p>feedback for time taken</p>
                                    </td>
                                    <td>
                                        <?php if(isset($templateData['time_feedback_rules']) and count($templateData['time_feedback_rules']) > 0) { ?>
                                        <?php foreach($templateData['time_feedback_rules'] as $rule) { ?>
                                            <p><img src="<?php echo URL::base(); ?>images/rule.gif" align="absmiddle" alt="rule"> rule:&nbsp;if time taken is&nbsp;<?php echo $rule->operator->title; ?>&nbsp;<?php echo $rule->value; ?>&nbsp;then give feedback&nbsp;[<?php echo $rule->message; ?>] - <a href="<?php echo URL::base().'feedbackManager/deleteRule/'.$templateData['map']->id.'/'.$rule->id; ?>">delete</a></p>
                                        <?php } ?>
                                        <?php } ?>
                                        <hr>
                                        <form action="<?php echo URL::base().'feedbackManager/addRule/'.$templateData['map']->id.'/time'; ?>" method="POST">
                                            <p>if time taken in this session is<br>
                                                <select name="cop">
                                                    <?php if(isset($templateData['operators'])) { ?>
                                                    <option value="">select ...</option>
                                                    <?php if(count($templateData['operators']) > 0) { ?>
                                                        <?php foreach($templateData['operators'] as $operator) { ?>
                                                            <option value="<?php echo $operator->id; ?>"><?php echo $operator->title; ?></option>
                                                        <?php } ?>
                                                    <?php } ?>
                                                    <?php } ?>
                                                </select> <input type="text" name="cval" size="4"> seconds<br>
                                                then feedback<br><textarea name="cMess" rows="3" cols="30"></textarea>
                                                <input type="submit" name="Submit" value="create rule"></p>
                                        </form>
                                    </td>
                                </tr>
                                <tr><td colspan="2"><hr></td></tr>
                                <tr align="left">
                                    <td width="25%" align="left">
                                        <p>feedback for nodes visited</p>
                                    </td>
                                    <td>
                                        <?php if(isset($templateData['visit_feedback_rules']) and count($templateData['visit_feedback_rules']) > 0) { ?>
                                        <?php foreach($templateData['visit_feedback_rules'] as $rule) { ?>
                                            <p><img src="<?php echo URL::base(); ?>images/rule.gif" align="absmiddle" alt="rule"> rule:&nbsp;if visited node&nbsp;<?php echo $rule->value; ?>&nbsp;then give feedback&nbsp;[<?php echo $rule->message; ?>] - <a href="<?php echo URL::base().'feedbackManager/deleteRule/'.$templateData['map']->id.'/'.$rule->id; ?>">delete</a></p>
                                        <?php } ?>
                                        <?php } ?>
                                        <hr>
                                        <form action="<?php echo URL::base().'feedbackManager/addRule/'.$templateData['map']->id.'/visit'; ?>" method="POST">
                                            <p>if visited node<br>
                                                <select name="cval">
                                                    <option value="">select ...</option>
                                                    <?php if(isset($templateData['nodes']) and count($templateData['nodes']) > 0) { ?>
                                                    <?php foreach($templateData['nodes'] as $node) { ?>
                                                        <option value="<?php echo $node->id; ?>"><?php echo $node->id; ?>: <?php echo $node->title; ?></option>
                                                    <?php } ?>
                                                    <?php } ?>
                                                </select><br>
                                                then feedback<br><textarea name="cMess" rows="3" cols="30"></textarea>
                                                <input type="submit" name="Submit" value="create rule"></p>
                                        </form>
                                    </td>
                                </tr>
                                <tr><td colspan="2"><hr></td></tr>
                                <tr align="left">
                                    <td width="25%" align="left">
                                        <p>feedback for must visit and must avoid nodes</p>
                                    </td>
                                    <td>
                                        <?php if(isset($templateData['must_visit_feedback_rules']) and count($templateData['must_visit_feedback_rules']) > 0) { ?>
                                            <?php foreach($templateData['must_visit_feedback_rules'] as $rule) { ?>
                                                <p><img src="<?php echo URL::base(); ?>images/rule.gif" align="absmiddle" alt="rule"> rule:&nbsp;if visited must visit node&nbsp;<?php echo $rule->operator->title; ?>&nbsp;<?php echo $rule->value; ?>&nbsp;then give feedback&nbsp;[<?php echo $rule->message; ?>] - <a href="<?php echo URL::base().'feedbackManager/deleteRule/'.$templateData['map']->id.'/'.$rule->id; ?>">delete</a></p>
                                            <?php } ?>
                                        <?php } ?>
                                        <?php if(isset($templateData['must_avoid_feedback_rules']) and count($templateData['must_avoid_feedback_rules']) > 0) { ?>
                                            <?php foreach($templateData['must_avoid_feedback_rules'] as $rule) { ?>
                                                <p><img src="<?php echo URL::base(); ?>images/rule.gif" align="absmiddle" alt="rule"> rule:&nbsp;if visited must avoid node&nbsp;<?php echo $rule->operator->title; ?>&nbsp;<?php echo $rule->value; ?>&nbsp;then give feedback&nbsp;[<?php echo $rule->message; ?>] - <a href="<?php echo URL::base().'feedbackManager/deleteRule/'.$templateData['map']->id.'/'.$rule->id; ?>">delete</a></p>
                                            <?php } ?>
                                        <?php } ?>
                                        <hr>
                                        <form action="<?php echo URL::base().'feedbackManager/addRule/'.$templateData['map']->id.'/must'; ?>" method="POST">
                                            <p>if the number of nodes of type 
                                                <select name="crtype">
                                                    <option value="">select ...</option>
                                                    <option value="mustvisit">must visit</option>
                                                    <option value="mustavoid">must avoid</option>
                                                </select><br>
                                                is&nbsp;
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
                                                <input type="text" name="cval" size="4"><br>then feedback<br>
                                                <textarea name="cMess" rows="3" cols="30"></textarea>
                                                <input type="submit" name="Submit" value="create rule"></p>
                                        </form>
                                    </td>
                                </tr>
                                <tr><td colspan="2"><hr></td></tr>
                                <tr align="left">
                                    <td width="25%" align="left">
                                        <p>Counter Feedback Rules</p>
                                    </td>
                                    <td>
                                        <?php if(isset($templateData['counter_feedback_rules']) and count($templateData['counter_feedback_rules']) > 0) { ?>
                                            <?php foreach($templateData['counter_feedback_rules'] as $rule) { ?>
                                                <p><img src="<?php echo URL::base(); ?>images/rule.gif" align="absmiddle" alt="rule"> rule:&nbsp;if counter&nbsp;<?php echo $rule->counter_id; ?>&nbsp;is&nbsp;<?php echo $rule->operator->title; ?>&nbsp;<?php echo $rule->value; ?>&nbsp;then give feedback&nbsp;[<?php echo $rule->message; ?>] - <a href="<?php echo URL::base().'feedbackManager/deleteRule/'.$templateData['map']->id.'/'.$rule->id; ?>">delete</a></p>
                                            <?php } ?>
                                        <?php } ?>
                                        <hr>
                                        <form action="<?php echo URL::base().'feedbackManager/addRule/'.$templateData['map']->id.'/counter'; ?>" method="POST">
                                            <p>if counter 
                                                <select name="cid">
                                                    <option value="">select ...</option>
                                                    <?php if(isset($templateData['counters']) and count($templateData['counters']) > 0) { ?>
                                                    <?php foreach($templateData['counters'] as $counter) { ?>
                                                        <option value="<?php echo $counter->id; ?>"><?php echo $counter->name; ?></option>
                                                    <?php } ?>
                                                    <?php } ?>
                                                </select><br>
                                                is&nbsp;
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
                                                <input type="text" name="cval" size="4"><br>then feedback<br>
                                                <textarea name="cMess" rows="3" cols="30"></textarea>
                                                <input type="submit" name="Submit" value="create rule"></p>
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


