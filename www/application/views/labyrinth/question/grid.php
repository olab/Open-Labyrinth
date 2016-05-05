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
if(isset($templateData['map']) AND isset($templateData['type'])) {
$q          = isset($templateData['question']);
$mapId      = $templateData['map']->id;
$type       = $templateData['type']->id;
$questionId = $q ? $templateData['question']->id : '';
$stem       = $q ? $templateData['question']->stem : '';
?>

<div class="page-header">
    <h1><?php echo $q ? (__('Edit question "').$stem.'"') : (__('New question for "').$templateData['map']->name.'"'); ?></h1>
</div>

<form class="form-horizontal" method="POST" action="<?php echo URL::base().'questionManager/questionPOST/'.$mapId.'/'.$type.'/'.$questionId; ?>">
    <fieldset class="fieldset">

        <div class="control-group">
            <label class="control-label"><?php echo __('Question type'); ?></label>
            <div class="controls">
                <div class="radio_extended btn-group">
                    <input autocomplete="off" id="question_type_mcq" type="radio" value="<?php echo Model_Leap_Map_Question::ENTRY_TYPE_MCQ_GRID ?>" name="question_type"<?php echo ($type == Model_Leap_Map_Question::ENTRY_TYPE_MCQ_GRID) ? 'checked="checked"' : ''; ?> />
                    <label data-class="btn-info" class="btn" for="question_type_mcq">Multiple choice grid</label>

                    <input autocomplete="off" id="question_type_pcq" type="radio" value="<?php echo Model_Leap_Map_Question::ENTRY_TYPE_PCQ_GRID ?>" name="question_type" <?php echo ($type == Model_Leap_Map_Question::ENTRY_TYPE_PCQ_GRID) ? 'checked="checked"' : ''; ?> />
                    <label data-class="btn-info" class="btn" for="question_type_pcq">Pick choice grid</label>
                </div>
            </div>
        </div>

        <div class="control-group">
            <label for="stem" class="control-label"><?php echo __('Stem'); ?></label>
            <div class="controls"><textarea id="stem" name="stem"><?php echo $stem; ?></textarea></div>
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
            <label for="feedback" class="control-label"><?php echo __('General feedback'); ?></label>
            <div class="controls"><textarea id="feedback" name="feedback"><?php echo $q ? $templateData['question']->feedback : ''; ?></textarea></div>
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

    <h3>Responses</h3>
    <table id="responses_list" class="table table-bordered table-striped table-condensed">
        <thead>
        <tr>
            <th>#</th>
            <th>Value</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php
        if(isset($templateData['question']) AND count($templateData['question']->responses) > 0) {
            $responseIndex = 1;

            foreach($templateData['question']->responses as $response) { ?>
                
            <tr>
                <td>
                    <?php echo isset($macros_counter) ? ++$macros_counter : $macros_counter = 1; ?>
                </td>
                <td>
                    <input type="text" name="macros_text[]" value="<?php echo htmlspecialchars($macros->text) ?>">
                </td>
                <td>
                    <input type="text" name="macros_hot_keys[]" value="<?php echo htmlspecialchars($macros->hot_keys) ?>">
                </td>
                <td>
                    <span class="btn btn-danger remove-macros"><i class="icon-trash"></i></span>
                </td>
            </tr>

                <?php
                $responseIndex++;
            }
        } ?>
        </tbody>
    </table>
    <?php } ?>

    <div class="form-actions">
        <div class="pull-left">
            <button class="btn btn-info addResponseGrid" type="button"><i class="icon-plus-sign"></i>Add response</button>
        </div>
        <div class="pull-right">
            <input class="btn btn-primary btn-large question-save-btn" type="submit" name="Submit" value="Save changes">
        </div>
    </div>
</form>

<script type="text/javascript" src="<?php echo ScriptVersions::get(URL::base().'scripts/question.js'); ?>"></script>
<script type="text/javascript" src="<?php echo ScriptVersions::get(URL::base().'scripts/visualeditor/base64v1_0.js'); ?>"></script>