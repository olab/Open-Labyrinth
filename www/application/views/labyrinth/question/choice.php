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
<?php if(isset($templateData['map']) && isset($templateData['type'])) { ?>
<div class="page-header">
    <h1>
        <?php echo (!isset($templateData['question']) ? (__('New question for "') . $templateData['map']->name . '"') : (__('Edit question "') . $templateData['question']->stem . '"')); ?>
    </h1>
</div>

<form class="form-horizontal" 
      method="POST" 
      action="<?php echo URL::base(); ?>questionManager/questionPOST/<?php echo $templateData['map']->id; ?>/<?php echo $templateData['type']->id; ?><?php echo (isset($templateData['question']) ? ('/' . $templateData['question']->id) : ''); ?>">
    <fieldset class="fieldset">
        <div class="control-group">
            <label class="control-label"><?php echo __('Question type'); ?></label>
            <div class="controls">
                <div class="radio_extended btn-group">
                    <input autocomplete="off" id="question_type_mcq" type="radio" value="3" name="question_type"
                    <?php if (isset($templateData['type'])){
                        echo ($templateData['type']->id == 3) ? 'checked="checked"' : '';
                    } else {
                        echo 'checked="checked"';
                    }
                    ?> />
                    <label data-class="btn-info" class="btn" for="question_type_mcq">Multiple choice</label>
                    <input autocomplete="off" id="question_type_pcq" type="radio" value="4" name="question_type" <?php echo ((isset($templateData['type']) && $templateData['type']->id == 4) ? 'checked="checked"' : '') ?> />
                    <label data-class="btn-info" class="btn" for="question_type_pcq">Pick choice</label>
                </div>
            </div>
        </div>
        <div class="control-group">
            <label for="stem" class="control-label"><?php echo __('Stem'); ?></label>
            <div class="controls"><textarea id="stem" name="stem"><?php echo (isset($templateData['question']) ? $templateData['question']->stem : ''); ?></textarea></div>
        </div>
        
        <div class="control-group">
            <label class="control-label"><?php echo __('Show answer to user') ?></label>
            <div class="controls">
                <div class="radio_extended btn-group">
                    <input autocomplete="off" id="showAnswer1" type="radio" name="showAnswer" value="1"
                    <?php if (isset($templateData['question'])){
                        echo ($templateData['question']->show_answer == 1) ? 'checked="checked"' : '';
                    } else {
                        echo 'checked="checked"';
                    }
                    ?>
                    />
                    <label data-class="btn-info" class="btn" for="showAnswer1"><?php echo __('Show'); ?></label>
                    <input autocomplete="off" id="showAnswer0" type="radio" name="showAnswer" value="0" <?php echo ((isset($templateData['question']) && $templateData['question']->show_answer == 0) ? 'checked="checked"' : '') ?>/>
                    <label data-class="btn-info" class="btn" for="showAnswer0"><?php echo __('Do not show'); ?></label>
                </div>
            </div>
        </div>

        <div class="control-group">
            <label class="control-label"><?php echo __('Layout of answers') ?></label>
            <div class="controls">
                <div class="radio_extended btn-group">
                    <input autocomplete="off" id="typeDisplay0" type="radio" name="typeDisplay" value="0"
                        <?php if (isset($templateData['question'])){
                            echo ($templateData['question']->type_display == 0) ? 'checked="checked"' : '';
                        } else {
                            echo 'checked="checked"';
                        }
                        ?>
                        />
                    <label data-class="btn-info" class="btn" for="typeDisplay0"><?php echo __('Vertical'); ?></label>
                    <input autocomplete="off" id="typeDisplay1" type="radio" name="typeDisplay" value="1" <?php echo ((isset($templateData['question']) && $templateData['question']->type_display == 1) ? 'checked="checked"' : '') ?>/>
                    <label data-class="btn-info" class="btn" for="typeDisplay1"><?php echo __('Horizontal'); ?></label>
                </div>
            </div>
        </div>

        <div class="control-group">
            <label class="control-label"><?php echo __('Show submit button') ?></label>
            <div class="controls">
                <div class="radio_extended btn-group">
                    <input autocomplete="off" type="radio" id="showSubmit" name="showSubmit" value="1" <?php echo ((isset($templateData['question']) && $templateData['question']->show_submit == 1) ? 'checked="checked"' : '') ?>/>
                    <label data-class="btn-info" class="btn" for="showSubmit"><?php echo __('Show'); ?></label>

                    <input autocomplete="off" type="radio" id="hideSubmit" name="showSubmit" value="0"
                    <?php if (isset($templateData['question'])){
                        echo ($templateData['question']->show_submit == 0) ? 'checked="checked"' : '';
                    } else {
                        echo 'checked="checked"';
                    }
                    ?>
                    />
                    <label data-class="btn-info" class="btn" for="hideSubmit"><?php echo __('Do not show'); ?></label>
                </div>
            </div>
        </div>

        <div class="control-group submitSettingsContainer <?php echo ((isset($templateData['question']) && $templateData['question']->show_submit == 1) ? '' : 'hide') ?>">
            <label class="control-label"><?php echo __('Submit button text') ?></label>
            <div class="controls">
                <input autocomplete="off" type="text" name="submitButtonText" value="<?php echo ((isset($templateData['question']) && $templateData['question']->submit_text != null) ? $templateData['question']->submit_text : 'Submit'); ?>"/>
            </div>
        </div>
        
        <?php if(isset($templateData['nodes']) && count($templateData['nodes']) > 0) { ?>
        <div class="control-group submitSettingsContainer <?php echo ((isset($templateData['question']) && $templateData['question']->show_submit == 1) ? '' : 'hide') ?>">
            <label class="control-label"><?php echo __('Redirect Node') ?></label>
            <div class="controls">
                <select autocomplete="off" name="redirectNode">
                    <option value="">Select</option>
                    <?php foreach($templateData['nodes'] as $node) { ?>
                    <option value="<?php echo $node->id; ?>" <?php echo ((isset($templateData['question']) && $templateData['question']->redirect_node_id == $node->id) ? 'selected=""' : ''); ?>><?php echo $node->title; ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>
        <?php } ?>
        
        <div class="control-group">
            <label for="counter" class="control-label"><?php echo __('Track score with existing counter'); ?></label>
            <div class="controls">
                <select autocomplete="off" id="counter" name="counter">
                    <option value="0">no counter</option>
                    <?php if(isset($templateData['counters']) && count($templateData['counters']) > 0) { ?>
                        <?php foreach($templateData['counters'] as $counter) { ?>
                            <option value="<?php echo $counter->id; ?>" <?php echo ((isset($templateData['question']) && $templateData['question']->counter_id == $counter->id) ? 'selected="selected"' : ''); ?>>
                                <?php echo $counter->name; ?>
                            </option>
                        <?php } ?>
                    <?php } ?>
                </select>
            </div>
        </div>
        
        <div class="control-group">
            <label class="control-label"><?php echo __('Number of tries allowed'); ?></label>
            <div class="controls">
                <div class="radio_extended btn-group">
                    <input autocomplete="off" id="tries1" type="radio" name="tries" value="1"
                    <?php if (isset($templateData['question'])){
                        echo ($templateData['question']->num_tries == 1) ? 'checked="checked"' : '';
                    } else {
                        echo 'checked="checked"';
                    }
                    ?>
                    />
                    <label data-class="btn-info" class="btn" for="tries1"><?php echo __('One'); ?></label>
                    <input autocomplete="off" id="tries2" type="radio" name="tries" value="2" <?php echo ((isset($templateData['question']) && $templateData['question']->num_tries == 2) ? 'checked="checked"' : '') ?>/>
                    <label data-class="btn-info" class="btn" for="tries2"><?php echo __('Many'); ?></label>
                </div>
            </div>
        </div>

        <div class="control-group">
            <label for="feedback" class="control-label"><?php echo __('Feedback'); ?></label>
            <div class="controls"><textarea id="feedback" name="feedback"><?php echo (isset($templateData['question']) ? $templateData['question']->feedback : ''); ?></textarea></div>
        </div>
    </fieldset>

    <div class="question-response-panel-group" id="accordion">
        <?php if(isset($templateData['question']) && count($templateData['question']->responses) > 0) { ?>
            <?php
                $responseIndex = 1;
                foreach($templateData['question']->responses as $response) {
                    $jsonResponse  = '{';
                    $jsonResponse .= '"id": "' . $response->id . '", ';
                    $jsonResponse .= '"response": "' . base64_encode(str_replace('&#43;', '+', $response->response)) . '", ';
                    $jsonResponse .= '"feedback": "' . base64_encode(str_replace('&#43;', '+', $response->feedback)) . '", ';
                    $jsonResponse .= '"correctness": "' . $response->is_correct . '", ';
                    $jsonResponse .= '"score": "' . $response->score . '", ';
                    $jsonResponse .= '"order": "' . $response->order . '"';
                    $jsonResponse .= '}';
            ?>
                <div class="panel sortable">
                    <input type="hidden" name="responses[]" value='<?php echo $jsonResponse; ?>'/>
                    <div class="panel-heading" class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#responseCollapse_<?php echo $response->id; ?>">
                        <label for="response_<?php echo $response->id; ?>"><?php echo __('Response'); ?></label>
                        <input type="text" class="response-input" id="response_<?php echo $response->id; ?>" value="<?php echo $response->response; ?>">
                        <button type="button" class="btn-remove-response btn btn-danger btn-small"><i class="icon-trash"></i></button>
                    </div>
                    <div id="responseCollapse_<?php echo $response->id; ?>" class="panel-collapse collapse">
                        <div class="panel-body">
                            <div class="control-group">
                                <label for="feedback_<?php echo $response->id; ?>" class="control-label"><?php echo __('Feedback'); ?></label>
                                <div class="controls"><input autocomplete="off" class="feedback-input" type="text" id="feedback_<?php echo $response->id; ?>" name="feedback_<?php echo $response->id; ?>" value="<?php echo $response->feedback; ?>"/></div>
                            </div>

                            <div class="control-group">
                                <label class="control-label"><?php echo __('Correctness'); ?></label>
                                <div class="controls">
                                    <div class="radio_extended btn-group">
                                        <input autocomplete="off" id="correctness1_<?php echo $response->id; ?>" type="radio" name="correctness_<?php echo $response->id; ?>" value="1" <?php echo ($response->is_correct == 1) ? 'checked="checked"' : ''; ?>/>
                                        <label data-class="btn-success" class="btn" for="correctness1_<?php echo $response->id; ?>"><?php echo __('Correct'); ?></label>
                                        <input autocomplete="off" id="correctness2_<?php echo $response->id; ?>" type="radio" name="correctness_<?php echo $response->id; ?>" value="2" <?php echo ($response->is_correct == 2) ? 'checked="checked"' : ''; ?>/>
                                        <label class="btn" for="correctness2_<?php echo $response->id; ?>"><?php echo __('Neutral'); ?></label>
                                        <input autocomplete="off" id="correctness0_<?php echo $response->id; ?>" type="radio" name="correctness_<?php echo $response->id; ?>" value="0" <?php echo ($response->is_correct == 0) ? 'checked="checked"' : ''; ?>/>
                                        <label data-class="btn-danger" class="btn" for="correctness0_<?php echo $response->id; ?>"><?php echo __('Incorrect'); ?></label>
                                    </div>
                                </div>
                            </div>

                            <div class="control-group">
                                <label for="score_<?php echo $response->id; ?>" class="control-label"><?php echo __('Score'); ?></label>
                                <div class="controls">
                                    <select autocomplete="off" class="score-select" id="score_<?php echo $response->id; ?>" name="score_<?php echo $response->id; ?>">
                                        <?php for ($j = -10; $j <= 10; $j++) { ?>
                                            <option value="<?php echo $j; ?>" <?php echo ($response->score == $j ? 'selected=""' : ''); ?>><?php echo $j; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <div class="control-group">
                                <label for="order_<?php echo $response->id; ?>" class="control-label"><?php echo __('Order'); ?></label>
                                <div class="controls">
                                    <select autocomplete="off" class="response-order-select" id="order_<?php echo $response->id; ?>" name="order_<?php echo $response->id; ?>">
                                        <?php for ($j = 1; $j <= count($templateData['question']->responses); $j++) { ?>
                                            <option value="<?php echo $j; ?>" <?php echo ($responseIndex == $j ? 'selected=""' : ''); ?>><?php echo $j; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php $responseIndex++; } ?>
        <?php } ?>
    </div>

    <div class="form-actions">
        <div class="pull-left">
            <button class="btn btn-info" type="button" id="addResponse"><i class="icon-plus-sign"></i>Add response</button>
        </div>
        <div class="pull-right">
            <input class="btn btn-primary btn-large question-save-btn" type="submit" name="Submit" value="Save changes">
        </div>
    </div>
</form>
<?php } ?>

<script type="text/javascript" src="<?php echo ScriptVersions::get(URL::base().'scripts/question.js'); ?>"></script>
<script type="text/javascript" src="<?php echo ScriptVersions::get(URL::base().'scripts/visualeditor/base64v1_0.js'); ?>"></script>