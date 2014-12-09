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

<script src="<?php echo ScriptVersions::get(URL::base().'scripts/editableselect.js'); ?>"></script>

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
            <label class="control-label"><?php echo __('Min value'); ?></label>
            <div class="controls">
                <input id="minValue" type="text" name="minValue" value="<?php echo (isset($templateData['question']) && isset($templateData['questionSettings']) ? $templateData['questionSettings']->minValue : ''); ?>"/>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label"><?php echo __('Max value'); ?></label>
            <div class="controls">
                <input id="maxValue" type="text" name="maxValue" value="<?php echo (isset($templateData['question']) && isset($templateData['questionSettings']) ? $templateData['questionSettings']->maxValue : ''); ?>"/>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label"><?php echo __('Default value'); ?></label>
            <div class="controls">
                <input id="defaultValue" type="text" name="defaultValue" value="<?php echo (isset($templateData['question']) && isset($templateData['questionSettings']) && property_exists($templateData['questionSettings'], 'defaultValue') ? $templateData['questionSettings']->defaultValue : ''); ?>"/>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label"><?php echo __('Step value'); ?></label>
            <div class="controls">
                <input id="stepValue" type="text" name="stepValue" value="<?php echo (isset($templateData['question']) && isset($templateData['questionSettings']) ? $templateData['questionSettings']->stepValue : ''); ?>"/>
            </div>
        </div>
        <div class="control-group">
            <label for="stem" class="control-label"><?php echo __('Stem'); ?></label>
            <div class="controls"><textarea id="stem" name="stem"><?php echo (isset($templateData['question']) ? $templateData['question']->stem : ''); ?></textarea></div>
        </div>
        <div class="control-group">
            <label for="feedback" class="control-label">Feedback</label>
            <div class="controls"><textarea id="feedback" name="fback"><?php if(isset($templateData['question'])) echo $templateData['question']->feedback; ?></textarea></div>
        </div>
        <div class="control-group">
            <label class="control-label"><?php echo __('Orientation'); ?></label>
            <div class="controls">
                <div class="radio_extended btn-group">
                    <input autocomplete="off" id="question_orientation_hor" type="radio" value="hor" name="question_orientation" <?php echo isset($templateData['questionSettings']) ? $templateData['questionSettings']->orientation == 'hor' ? 'checked="checked"' : '' : 'checked="checked"'; ?>/>
                    <label data-class="btn-info" class="btn" for="question_orientation_hor">horizontal</label>
                    <input autocomplete="off" id="question_orientation_ver" type="radio" value="ver" name="question_orientation" <?php echo (isset($templateData['questionSettings']) && $templateData['questionSettings']->orientation == 'ver') ? 'checked="checked"' : ''; ?> />
                    <label data-class="btn-info" class="btn" for="question_orientation_ver">vertical</label>
                </div>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label"><?php echo __('Show/hide chosen value'); ?></label>
            <div class="controls">
                <div class="radio_extended btn-group">
                    <input autocomplete="off" id="question_scv" type="radio" value="1" name="question_chosen_value" <?php echo (isset($templateData['questionSettings']) && $templateData['questionSettings']->showValue == 1) ? 'checked="checked"' : ''; ?>/>
                    <label data-class="btn-success" class="btn" for="question_scv">Show</label>
                    <input autocomplete="off" id="question_hcv" type="radio" value="0" name="question_chosen_value" <?php echo isset($templateData['questionSettings']) ? $templateData['questionSettings']->showValue == 0 ? 'checked="checked"' : '' : 'checked="checked"'; ?> />
                    <label data-class="btn-danger" class="btn" for="question_hcv">Hide</label>
                </div>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label"><?php echo __('Skins or appearances'); ?></label>
            <div class="controls">
                <select onchange="changeSkin(this.value);" name="sliderSkin">
                    <option value="" <?php echo (isset($templateData['question']) && isset($templateData['questionSettings']) && $templateData['questionSettings']->sliderSkin == '') ? 'selected="selected"' : ''; ?>>Default</option>
                    <option value="ball" <?php echo (isset($templateData['question']) && isset($templateData['questionSettings']) && $templateData['questionSettings']->sliderSkin == 'ball') ? 'selected="selected"' : ''; ?>>Ball</option>
                    <option value="zipper" <?php echo (isset($templateData['question']) && isset($templateData['questionSettings']) && $templateData['questionSettings']->sliderSkin == 'zipper') ? 'selected="selected"' : ''; ?>>Zipper</option>
                    <option value="arrow" <?php echo (isset($templateData['question']) && isset($templateData['questionSettings']) && $templateData['questionSettings']->sliderSkin == 'arrow') ? 'selected="selected"' : ''; ?>>Arrow</option>
                    <option value="arrowgreen" <?php echo (isset($templateData['question']) && isset($templateData['questionSettings']) && $templateData['questionSettings']->sliderSkin == 'arrowgreen') ? 'selected="selected"' : ''; ?>>Arrow green</option>
                    <option value="simplesilver" <?php echo (isset($templateData['question']) && isset($templateData['questionSettings']) && $templateData['questionSettings']->sliderSkin == 'simplesilver') ? 'selected="selected"' : ''; ?>>Simple Silver</option>
                    <option value="simplegray" <?php echo (isset($templateData['question']) && isset($templateData['questionSettings']) && $templateData['questionSettings']->sliderSkin == 'simplegray') ? 'selected="selected"' : ''; ?>>Simple Gray</option>
                    <option value="bar" <?php echo (isset($templateData['question']) && isset($templateData['questionSettings']) && $templateData['questionSettings']->sliderSkin == 'bar') ? 'selected="selected"' : ''; ?>>Bar</option>
                    <option value="dhx_skyblue" <?php echo (isset($templateData['question']) && isset($templateData['questionSettings']) && $templateData['questionSettings']->sliderSkin == 'dhx_skyblue') ? 'selected="selected"' : ''; ?>>SkyBlue</option>
                </select>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label"><?php echo __('Ability to directly input value'); ?></label>
            <div class="controls">
                <div class="radio_extended btn-group">
                    <input autocomplete="off" id="question_ability_yes" type="radio" value="1" name="question_ability_input" <?php echo (isset($templateData['questionSettings']) && $templateData['questionSettings']->abilityValue == 1) ? 'checked="checked"' : ''; ?>/>
                    <label data-class="btn-success" class="btn" for="question_ability_yes">Yes</label>
                    <input autocomplete="off" id="question_ability_no" type="radio" value="0" name="question_ability_input" <?php echo isset($templateData['questionSettings']) ? $templateData['questionSettings']->abilityValue == 0 ? 'checked="checked"' : '' : 'checked="checked"'; ?> />
                    <label data-class="btn-danger" class="btn" for="question_ability_no">No</label>
                </div>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label"><?php echo __('Counter'); ?></label>
            <div class="controls">
                <select name="counter">
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
            <label class="control-label" for="v"><?php echo __('Private'); ?>
            </label>
            <div class="controls">
                <input type="checkbox" name="is_private" <?php if(isset($templateData['question'])) { echo $templateData['question']->is_private ? 'checked=""' : '"checked"'; }?>>
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

    <div id="responsesContainer">
        <?php if(isset($templateData['question']) && count($templateData['question']->responses) > 0) { ?>
            <?php $index = 1; foreach($templateData['question']->responses as $response) { ?>
                <fieldset class="fieldset" id="fieldset_<?php echo $response->id; ?>">
                    <legend class="legend-title"><?php echo __('Response #') . $index; ?></legend>
                    <div class="control-group">
                        <label for="response_<?php echo $response->id; ?>" class="control-label"><?php echo __('Interval'); ?></label>
                        <div class="controls">
                            From: <input autocomplete="off" type="text" id="interval_from_<?php echo $response->id; ?>" name="interval_from_<?php echo $response->id; ?>" value="<?php echo $response->from; ?>"/>
                            To: <input autocomplete="off" type="text" id="interval_to_<?php echo $response->id; ?>" name="interval_to_<?php echo $response->id; ?>" value="<?php echo $response->to; ?>"/>
                        </div>
                    </div>

                    <div class="control-group">
                        <label for="score_<?php echo $response->id;  ?>" class="control-label"><?php echo __('Score'); ?></label>
                        <div class="controls">
                            <input type="text" id="score_<?php echo $response->id; ?>" name="score_<?php echo $response->id; ?>" value="<?php echo $response->score; ?>" selectBoxOptions="-10;-9;-8;-7;-6;-5;-4;-3;-2;-1;0;1;2;3;4;5;6;7;8;9;10">
                        </div>
                    </div>

                    <div class="form-actions">
                        <a class="btn btn-danger removeBtn" removeid="fieldset_<?php echo $response->id; ?>" href="#"><i class="icon-minus-sign"></i>Remove</a>
                    </div>
                </fieldset>
                <script>
                    createEditableSelect(document.getElementById('score_<?php echo $response->id; ?>'));
                </script>
                <?php $index++; } ?>
        <?php } ?>
    </div>

    <div class="form-actions">
        <div class="pull-left">
            <button class="btn btn-info" type="button" id="addResponse"><i class="icon-plus-sign"></i>Add response</button>
        </div>
        <div class="pull-right">
            <input class="btn btn-primary btn-large" type="submit" name="Submit" value="Save changes">
        </div>
    </div>
</form>

<script src="<?php echo ScriptVersions::get(URL::base().'scripts/sliderQuestion.js'); ?>"></script>