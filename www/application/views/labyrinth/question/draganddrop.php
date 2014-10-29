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
$map         = Arr::get($templateData, 'map');
$question    = Arr::get($templateData, 'question', false);
$type        = $templateData['type']->id;
$show_submit = $question ? $question->show_submit : 0; ?>
<div class="page-header">
    <h1><?php echo $question ? __('Edit question "').$question->stem.'"' : __('New question for "').$map->name.'"'; ?></h1>
</div>
<form class="form-horizontal" method="POST" action="<?php echo URL::base().'questionManager/questionPOST/'.$map->id.'/'.$templateData['type']->id; ?><?php echo $question ? '/'.$question->id : ''; ?>">
    <fieldset class="fieldset">
        <div class="control-group">
            <label class="control-label">Question type</label>
            <div class="controls">
                <div class="radio_extended btn-group">
                    <input autocomplete="off" id="question_type_dd" type="radio" value="6" name="question_type" <?php echo ($type == 6) ? 'checked' : ''; ?>>
                    <label data-class="btn-info" for="question_type_dd" class="btn">Drag and drop</label>

                    <input autocomplete="off" id="question_type_sjt" type="radio" value="8" name="question_type" <?php echo ($type == 8) ? 'checked' : ''; ?>>
                    <label data-class="btn-info" for="question_type_sjt" class="btn">Situational Judgement Testing</label>
                </div>
            </div>
        </div>
        <div class="control-group">
            <label for="stem" class="control-label"><?php echo __('Stem'); ?></label>
            <div class="controls">
                <textarea id="stem" name="stem"><?php echo $question ? $question->stem : ''; ?></textarea>
            </div>
        </div>

        <div class="control-group">
            <label class="control-label"><?php echo __('Show submit button') ?></label>
            <div class="controls">
                <div class="radio_extended btn-group">
                    <input autocomplete="off" type="radio" id="showSubmit" name="showSubmit" value="1" <?php if ($show_submit) echo 'checked'; ?>/>
                    <label data-class="btn-info" class="btn" for="showSubmit"><?php echo __('Show'); ?></label>
                    <input autocomplete="off" type="radio" id="hideSubmit" name="showSubmit" value="0" <?php
                    if ($question) {
                        if ($show_submit) echo 'checked';
                    } else {
                        echo 'checked';
                    }?> />
                    <label data-class="btn-info" class="btn" for="hideSubmit"><?php echo __('Do not show'); ?></label>
                </div>
            </div>
        </div>

        <div class="control-group submitSettingsContainer <?php echo $show_submit ? '' : 'hide'; ?>">
            <label class="control-label"><?php echo __('Submit button text'); ?></label>
            <div class="controls">
                <input autocomplete="off" type="text" name="submitButtonText" value="<?php echo ((isset($templateData['question']) && $templateData['question']->submit_text != null) ? $templateData['question']->submit_text : 'Submit'); ?>"/>
            </div>
        </div>

        <div class="control-group">
            <label for="feedback" class="control-label"><?php echo __('Feedback'); ?></label>
            <div class="controls"><textarea id="feedback" name="feedback"><?php echo (isset($templateData['question']) ? $templateData['question']->feedback : ''); ?></textarea></div>
        </div>
        <div class="control-group">
            <label class="control-label" for="v"><?php echo __('Private'); ?>
            </label>
            <div class="controls">
                <input type="checkbox" name="is_private" <?php if(isset($templateData['question'])) echo $templateData['question']->is_private ? '' : 'checked'; ?>>
            </div>
        </div>

        <div class="control-group">
            <label class="control-label"><?php echo __('Used'); ?>
            </label>
            <div class="controls">
                <input type="text" readonly value="<?php if(isset($templateData['used'])) echo $templateData['used']; ?>"/>
            </div>
        </div>
    </fieldset>

    <div class="question-response-draggable-panel-group"><?php
        if(isset($templateData['question']) && count($templateData['question']->responses) > 0) {
            foreach($templateData['question']->responses as $response) {
                $jsonResponse  = '{"id": "'.$response->id.'", "response": "'.base64_encode(str_replace('&#43;', '+', $response->response)).'", "order": "'.$response->order.'"}'; ?>
                <div class="response-panel sortable">
                    <input type="hidden" name="responses[]" value='<?php echo $jsonResponse; ?>'/>
                    <label for="response"><?php echo __('Response'); ?></label>
                    <input type="text" class="response-input" value="<?php echo $response->response; ?>"/>
                    <button type="button" class="btn-remove-response btn btn-danger btn-small"><i class="icon-trash"></i></button>
                    <span><?php echo 'Response id: '.$response->id ?></span>
                </div><?php
            }
        } ?>
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

<script type="text/javascript" src="<?php echo ScriptVersions::get(URL::base().'scripts/visualeditor/base64v1_0.js'); ?>"></script>
<script type="text/javascript" src="<?php echo ScriptVersions::get(URL::base().'scripts/dragQuestion.js'); ?>"></script>