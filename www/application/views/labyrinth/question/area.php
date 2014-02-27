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
<script type="text/javascript" src="<?php echo URL::base(); ?>scripts/rules-checker.js"></script>
<div class="page-header">
<h1><?php if(!isset($templateData['question'])){
        echo __('New question for"') . $templateData['map']->name . '"';
    } else {
        echo __('Edit question "') . $templateData['question']->stem . '"'; }?>
</h1></div>
<form class="form-horizontal" 
      method="POST" 
      action="<?php echo URL::base(); ?>questionManager/questionPOST/<?php echo $templateData['map']->id; ?>/<?php echo $templateData['type']->id; ?><?php echo (isset($templateData['question']) ? ('/' . $templateData['question']->id) : ''); ?>">
    <fieldset class="fieldset">
        <div class="control-group">
            <label for="qstem" class="control-label"><?php echo __('Stem'); ?>
            </label>
            <div class="controls">
                <textarea id="qstem" name="qstem"><?php if(isset($templateData['question'])) echo $templateData['question']->stem; ?></textarea>
            </div>
        </div>
        <div class="control-group">
            <label for="qwidth" class="control-label"><?php echo __('Width'); ?>
            </label>
            <div class="controls">
                <select  id="qwidth" name="qwidth">
                    <?php for($i = 10; $i <= 60; $i += 10) { ?>
                        <option value="<?php echo $i; ?>" <?php if(isset($templateData['question']) and $templateData['question']->width == $i) echo 'selected=""'; ?>><?php echo $i; ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>
        <div class="control-group">
            <label for="qheight" class="control-label"><?php echo __('Height'); ?>
            </label>
            <div class="controls">
                <select id="qheight" name="qheight">
                    <?php for($i = 2; $i <= 8; $i += 2) { ?>
                        <option value="<?php echo $i; ?>" <?php if(isset($templateData['question']) and $templateData['question']->height == $i) echo 'selected=""'; ?>><?php echo $i; ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>
        <div class="control-group">
            <label for="fback" class="control-label">
                <?php echo __('Prompt text'); ?>
                <p class="question-info-box"><?php echo __('Text will automatically appear in response area. Use to give learner a hint or further instruction.'); ?></p>
            </label>
            <div class="controls">
                <textarea id="fback" name="fback"><?php if(isset($templateData['question'])) echo $templateData['question']->feedback; ?></textarea>
            </div>
        </div>
        <div class="control-group">
            <label for="rule" class="control-label">
                <?php echo __('Rule'); ?>
            </label>
            <div class="controls">
                <textarea onkeypress="resetCheck();" id="code" name="settings"><?php if(isset($templateData['question'])) echo $templateData['question']->settings; ?></textarea>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="v"><?php echo __('Private'); ?>
            </label>
            <div class="controls">
                <input type="checkbox" name="is_private" <?php if(isset($templateData['question'])) { echo $templateData['question']->is_private ? 'checked=""' : '"checked"';} ?>>
            </div>
        </div>

        <div class="control-group">
            <label class="control-label"><?php echo __('Used'); ?>
            </label>
            <div class="controls">
                <input type="text" readonly value="<?php if(isset($templateData['used'])) { echo $templateData['used']; } ?>"/>
            </div>
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
            <input style="float:right;" id="submit_button" class="btn btn-primary btn-large hide" type="submit" name="Submit" value="<?php echo __('Save question'); ?>">
            <input style="float:right;" id="rule_submit_check" class="btn btn-primary btn-large" type="button" name="Check" value="<?php echo __('Save question'); ?>" onclick="return checkRule(1);">
        </div>

    </div>
    <input type="hidden" name="url" id="url" value="<?php echo URL::base().'counterManager/checkCommonRule'; ?>" />
    <input type="hidden" name="mapId" id="mapId" value="<?php echo $templateData['map']->id; ?>" />
    <input type="hidden" name="isCorrect" id="isCorrect" value="<?php if(isset($templateData['isCorrect'])) echo $templateData['isCorrect']; ?>" />
</form>
<?php } ?>


