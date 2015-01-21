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
if (isset($templateData['map'])) {
    $q          = isset($templateData['question']);
    $mapId      = $templateData['map']->id;
    $h1         = $q ? __('Edit question "') . $templateData['question']->stem . '"' : __('New question for "') . $templateData['map']->name . '"';
    $questionId = $q ? $templateData['question']->id : '';
    $stem       = $q ? $templateData['question']->stem : '';
    $feedback   = $q ? $templateData['question']->feedback : '';
    $prompt     = $q ? $templateData['question']->prompt : '';
    $settings   = $q ? $templateData['question']->settings : '';

    $validatorsList     = Arr::get($templateData, 'validators', array());
    $validationObj      = Arr::get($templateData, 'validation', array());
    $validatorSelected  = $validationObj ? $validationObj->validator : '';
    $secondParameter    = $validationObj ? $validationObj->second_parameter : '';
    $errorMessage       = $validationObj ? $validationObj->error_message : '';
?>
<script type="text/javascript" src="<?php echo URL::base(); ?>scripts/rules-checker.js"></script>
<div class="page-header">
    <h1><?php echo $h1; ?></h1>
</div>
<form class="form-horizontal" method="POST" action="<?php echo URL::base().'questionManager/questionPOST/'.$mapId.'/'.$templateData['type']->id.'/'.$questionId ?>">
    <fieldset>
        <div class="control-group">
            <label for="qstem" class="control-label"><?php echo __('Stem'); ?></label>
            <div class="controls">
                <textarea id="qstem" name="qstem"><?php echo $stem; ?></textarea>
            </div>
        </div>
        <div class="control-group">
            <label for="qwidth" class="control-label"><?php echo __('Width'); ?></label>
            <div class="controls">
                <select  id="qwidth" name="qwidth">
                    <?php for($i = 10; $i <= 60; $i += 10) { ?>
                        <option value="<?php echo $i; ?>" <?php if(isset($templateData['question']) and $templateData['question']->width == $i) echo 'selected=""'; ?>><?php echo $i; ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>
        <div class="control-group">
            <label for="feedback" class="control-label">Feedback</label>
            <div class="controls"><textarea id="feedback" name="fback"><?php echo $feedback; ?></textarea></div>
        </div>
        <div class="control-group">
            <label for="fback" class="control-label">
                <?php echo __('Prompt text'); ?>
                <p class="question-info-box"><?php echo __('Text will automatically appear in response area. Use to give learner a hint or further instruction.'); ?></p>
            </label>
            <div class="controls">
                <textarea id="fback" name="prompt"><?php echo $prompt; ?></textarea>
            </div>
        </div>
        <div class="control-group">
            <label for="rule" class="control-label">
                <?php echo __('Rule'); ?>
            </label>
            <div class="controls">
                <textarea onkeypress="resetCheck();" id="code" name="settings"><?php echo $settings; ?></textarea>
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
        <div class="control-group">
            <label class="control-label" for="v"><?php echo __('Private'); ?></label>
            <div class="controls">
                <input type="checkbox" name="is_private" <?php if(isset($templateData['question'])) { echo $templateData['question']->is_private ? 'checked=""' : '"checked"';} ?>>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label"><?php echo __('Used'); ?></label>
            <div class="controls">
                <input type="text" readonly value="<?php echo Arr::get($templateData, 'used'); ?>"/>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label"><?php echo __('Validator'); ?></label>
            <div class="controls">
                <select class="validator" name="validator">
                    <option>no validator</option><?php
                    foreach ($validatorsList as $validator=>$parameter) { ?>
                    <option data-parameter="<?php echo $parameter; ?>" <?php if ($validatorSelected == $validator) echo 'selected'; ?>><?php echo $validator; ?></option><?php
                    } ?>
                </select><?php
                if ($secondParameter) { ?>
                <input class="second_parameter" type="text" name="second_parameter" placeholder="Enter <?php echo Arr::get($validatorsList, $validatorSelected, ''); ?>" value="<?php echo $secondParameter; ?>"><?php
                } ?>
            </div>
        </div>
        <div class="control-group" style="display: <?php echo $validatorSelected ? 'block' : 'none'; ?>">
            <label class="control-label"><?php echo __('Validator error message'); ?></label>
            <div class="controls"><input type="text" name="error_message" value="<?php echo $errorMessage; ?>"></div>
        </div>
    </fieldset>
    <div class="form-actions">
        <div class="pull-left" style="margin-left: -250px; margin-top: -20px;">
            <dl class="status-label dl-horizontal">
                <dt>Status</dt>
                <dd><span class="label label-warning">The rule hasn't been checked.</span><span class="hide label label-success">The rule is correct.</span><span class="hide label label-important">The rule has error(s).</span></dd>
            </dl>
            <input style="float:right;" id="check_rule_button" type="button" class="btn btn-primary btn-large" name="check-rule" data-loading-text="Checking..." value="<?php echo __('Check rule'); ?>">
        </div>
        <div class="pull-right">
            <input style="float:right;" id="submit_button" class="btn btn-large btn-primary hide" type="submit" name="Submit" value="Save question">
            <input style="float:right;" id="rule_submit_check" class="btn btn-large btn-primary" type="button" name="Check" value="Save question" data-loading-text="Checking rule...">
        </div>
    </div>
    <input type="hidden" name="url" id="url" value="<?php echo URL::base().'counterManager/checkCommonRule'; ?>" />
    <input type="hidden" name="mapId" id="mapId" value="<?php echo $mapId; ?>" />
    <input type="hidden" name="isCorrect" id="isCorrect" value="<?php echo Arr::get($templateData, 'isCorrect', 0); ?>" />
</form>
<?php } ?>

<script type="text/javascript" src="<?php echo ScriptVersions::get(URL::base().'scripts/question.js'); ?>"></script>


