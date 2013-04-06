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
            <label for="stem" class="control-label"><?php echo __('Stem'); ?></label>
            <div class="controls"><textarea id="stem" name="stem"><?php echo (isset($templateData['question']) ? $templateData['question']->stem : ''); ?></textarea></div>
        </div>
        
        <div class="control-group">
            <label class="control-label"><?php echo __('Show answer to user') ?></label>
            <div class="controls">
                <label class="radio">
                    <input type="radio" name="showAnswer" value="1" <?php echo ((isset($templateData['question']) && $templateData['question']->show_answer == 1) ? 'checked=""' : '') ?>/> 
                    <?php echo __('show'); ?>
                </label>
                <label class="radio">
                    <input type="radio" name="showAnswer" value="0" <?php echo ((isset($templateData['question']) && $templateData['question']->show_answer == 0) ? 'checked=""' : '') ?>/> 
                    <?php echo __('do not show'); ?>
                </label>
            </div>
        </div>
        
        <div class="control-group">
            <label class="control-label"><?php echo __('Show submit button') ?></label>
            <div class="controls">
                <label class="radio">
                    <input type="radio" id="showSubmit" name="showSubmit" value="1" <?php echo ((isset($templateData['question']) && $templateData['question']->show_submit == 1) ? 'checked=""' : '') ?>/> 
                    <?php echo __('show'); ?>
                </label>
                <label class="radio">
                    <input type="radio" id="hideSubmit" name="showSubmit" value="0" <?php echo ((isset($templateData['question']) && $templateData['question']->show_submit == 0) ? 'checked=""' : '') ?>/> 
                    <?php echo __('do not show'); ?>
                </label>
            </div>
        </div>
        
        <div class="control-group sbumitSettingsContainer <?php echo ((isset($templateData['question']) && $templateData['question']->show_submit == 1) ? '' : 'hide') ?>">
            <label class="control-label"><?php echo __('Submit button text') ?></label>
            <div class="controls">
                <input type="text" name="submitButtonText" value="<?php echo ((isset($templateData['question']) && $templateData['question']->submit_text != null) ? $templateData['question']->submit_text : 'Submit'); ?>"/>
            </div>
        </div>
        
        <?php if(isset($templateData['nodes']) && count($templateData['nodes']) > 0) { ?>
        <div class="control-group sbumitSettingsContainer <?php echo ((isset($templateData['question']) && $templateData['question']->show_submit == 1) ? '' : 'hide') ?>">
            <label class="control-label"><?php echo __('Redirect Node') ?></label>
            <div class="controls">
                <select name="redirectNode">
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
                <select id="counter" name="counter">
                    <option value="0">no counter</option>
                    <?php if(isset($templateData['counters']) && count($templateData['counters']) > 0) { ?>
                        <?php foreach($templateData['counters'] as $counter) { ?>
                            <option value="<?php echo $counter->id; ?>" <?php echo ((isset($templateData['question']) && $templateData['question']->counter_id == $counter->id) ? 'selected=""' : ''); ?>>
                                <?php echo $counter->name; ?>
                            </option>
                        <?php } ?>
                    <?php } ?>
                </select>
            </div>
        </div>
        
        <div class="control-group">
            <label for="tries" class="control-label"><?php echo __('Number of tries allowed'); ?></label>
            <div class="controls"><input type="text" id="tries" name="tries" value="<?php echo (isset($templateData['question']) ? $templateData['question']->num_tries : ''); ?>"/></div>
        </div>
        
        <div class="control-group">
            <label for="feedback" class="control-label"><?php echo __('Feedback'); ?></label>
            <div class="controls"><textarea id="feedback" name="feedback"><?php echo (isset($templateData['question']) ? $templateData['question']->feedback : ''); ?></textarea></div>
        </div>
    </fieldset>
    
    <div id="responsesContainer">
        <?php if(isset($templateData['question']) && count($templateData['question']->responses) > 0) { ?>
            <?php $index = 1; foreach($templateData['question']->responses as $response) { ?>
                <fieldset class="fieldset" id="fieldset_<?php echo $response->id; ?>">
                    <legend class="legend-title"><?php echo __('Response #') . $index; ?></legend>
                    <div class="control-group">
                        <label for="response_<?php echo $response->id; ?>" class="control-label"><?php echo __('Response'); ?></label>
                        <div class="controls"><input type="text" id="response_<?php echo $response->id; ?>" name="response_<?php echo $response->id; ?>" value="<?php echo $response->response; ?>"/></div>
                    </div>
                    
                    <div class="control-group">
                        <label for="feedback_<?php echo $response->id; ?>" class="control-label"><?php echo __('Feedback'); ?></label>
                        <div class="controls"><input type="text" id="feedback_<?php echo $response->id; ?>" name="feedback_<?php echo $response->id; ?>" value="<?php echo $response->feedback; ?>"/></div>
                    </div>
                    
                    <div class="control-group">
                        <label class="control-label"><?php echo __('Correctness'); ?></label>
                        <div class="controls">
                            <label class="radio">
                                <input type="radio" name="correctness_<?php echo $response->id; ?>" value="1" <?php echo ($response->is_correct == 1 ? 'checked=""' : ''); ?>/> <?php echo __('correct'); ?>
                            </label>
                            <label class="radio">
                                <input type="radio" name="correctness_<?php echo $response->id; ?>" value="1" <?php echo ($response->is_correct == 0 ? 'checked=""' : ''); ?>/> <?php echo __('incorrect'); ?>
                            </label>
                        </div>
                    </div>
                    
                    <div class="control-group">
                        <label for="score_<?php echo $response->id;  ?>" class="control-label"><?php echo __('Score'); ?></label>
                        <div class="controls">
                            <select id="score_<?php echo $response->id; ?>" name="score_<?php echo $response->id; ?>">
                                <?php for ($j = -10; $j <= 10; $j++) { ?>
                                    <option value="<?php echo $j; ?>" <?php echo ($response->score == $j ? 'selected=""' : ''); ?>><?php echo $j; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <a class="btn btn-danger removeBtn" removeid="fieldset_<?php echo $response->id; ?>" href="#"><i class="icon-minus-sign"></i>Remove</a>
                    </div>
                </fieldset>
            <?php $index++; } ?>
        <?php } ?>
    </div>
    
    <div class="form-actions">
        <button class="btn btn-primary" type="button" id="addResponse">Add response</button>
    </div>
    <div class="form-actions">
        <div class="pull-right">
            <input class="btn btn-primary btn-large" type="submit" name="Submit" value="Save changes">
        </div>
    </div>
</form>
<?php } ?>

<script type="text/javascript" src="<?php echo ScriptVersions::get(URL::base().'scripts/question.js'); ?>"></script>