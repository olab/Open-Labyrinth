<?php if (isset($templateData['map'])) { ?>
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
                                <p><?php if(isset($templateData['question']->responses)) var_dump($templateData['question']->responses[0]->response); ?></P>
                                <table border="0" width="100%" cellpadding="1">
                                    <tr><td><p>stem:</p></td><td><p><textarea cols="50" rows="3" name="qstem"><?php if(isset($templateData['question'])) echo $templateData['question']->stem; ?></textarea></p></td></tr>
                                    <tr><td colspan="2"><hr></td></tr>
                                    <?php if(isset($templateData['args'])) { ?>
                                        <?php for($i = 1; $i <= (int)$templateData['args']; $i++) { ?>
                                            <tr>
                                                <td>
                                                    <p><?php echo __('response').' '.$i; ?>:</p>
                                                </td>
                                                <td>
                                                    <p>response: <input type="text" name="qresp<?php echo $i; ?>t" size="50" 
                                                                        value="<?php if(isset($templateData['question']) and count($templateData['question']->responses) > 0) echo $templateData['question']->responses[$i-1]->response; ?>"></p>
                                                    <p>feedback: <input type="text" name="qfeed<?php echo $i; ?>" size="50" 
                                                                        value="<?php if(isset($templateData['question']) and count($templateData['question']->responses) > 0) echo $templateData['question']->responses[$i-1]->feedback; ?>"></p>
                                                    <p>[<input type="radio" name="qresp<?php echo $i ?>y" value="1" <?php if(isset($templateData['question']) and count($templateData['question']->responses) > 0 and $templateData['question']->responses[$i-1]->is_correct == 1) echo 'checked=""'; ?>> correct] 
                                                        [<input type="radio" name="qresp<?php echo $i ?>y" value="0" <?php if(isset($templateData['question']) and count($templateData['question']->responses) > 0 and $templateData['question']->responses[$i-1]->is_correct == 0) echo 'checked=""'; ?>> incorrect] 
                                                        <select name="qresp<?php echo $i; ?>s">
                                                            <?php for($j = -10; $j <= 10; $j++) { ?>
                                                                <option value="<?php echo $j; ?>" 
                                                                    <?php if(isset($templateData['question']) and count($templateData['question']->responses) > 0 and $templateData['question']->responses[$i-1]->score == $j) { echo 'selected=""'; } ?>
                                                                    <?php if(!isset($templateData['question']) and $j == 0) echo 'selected=""'; ?>><?php echo $j; ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </p>
                                                </td>
                                            </tr>
                                            <tr><td colspan="2"><hr></td></tr>
                                        <?php } ?>
                                    <?php } ?>
                                    <tr><td><p><?php echo __('show answer to user'); ?>:</p></td><td><p>[<input type="radio" name="qshow" value="1" <?php if(isset($templateData['question']) and $templateData['question']->show_answer == 1) echo 'checked=""'; ?>> show] [<input type="radio" name="qshow" value="0" <?php if(isset($templateData['question']) and $templateData['question']->show_answer == 0) echo 'checked=""'; ?>> do not show]</p></td></tr>
                                    <tr><td><p><?php echo __('number of tries allowed'); ?>:</p></td><td><p><select name="numtries"><option value="-1" <?php if(isset($templateData['question']) and $templateData['question']->num_tries == -1) echo 'selected=""'; ?>>no limit</option><option value="1" <?php if(isset($templateData['question']) and $templateData['question']->num_tries == 1) echo 'selected=""'; ?>>1 try</select></p></td></tr>
                                    <tr><td><p><?php echo __('track score with existing counter'); ?>:</p></td>
                                        <td><p>
                                                <select name="scount">
                                                    <option value="0">no counter</option>
                                                    <?php if(isset($templateData['counters']) and count($templateData['counters']) > 0) { ?>
                                                        <?php foreach($templateData['counters'] as $counter) { ?>
                                                            <option value="<?php echo $counter->id; ?>" <?php if(isset($templateData['question']) and $counter->id == $templateData['question']->counter_id) echo 'selected=""'; ?>><?php echo $counter->name; ?></option>
                                                        <?php } ?>
                                                    <?php } ?>
                                                </select>
                                            </p></td></tr>
                                    <tr><td><p>feedback:</p></td><td><p><textarea cols="60" rows="3" name="fback"><?php if(isset($templateData['question'])) echo $templateData['question']->feedback; ?></textarea></p></td></tr>
                                    <tr><td colspan="2"><input type="submit" name="Submit" value="submit"></td></tr>
                                </table>
                            </form>
                        </td></tr>
                </table>
            </td>
        </tr>
    </table>
<?php } ?>