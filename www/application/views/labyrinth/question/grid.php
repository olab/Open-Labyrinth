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

$q = isset($templateData['question']);
$mapId = $templateData['map']->id;
$type = $templateData['type']->id;
$questionId = $q ? $templateData['question']->id : '';
$stem = $q ? $templateData['question']->stem : '';
$subQuestions = isset($templateData['question']) ? $templateData['question']->subQuestions : [];
$responses = isset($templateData['question']) ? $templateData['question']->responses : [];
$attributes = isset($templateData['attributes']) ? $templateData['attributes'] : [];
$correctness = [
    [
        'value' => 1,
        'name' => __('Correct'),
    ],
    [
        'value' => 2,
        'name' => __('Neutral'),
    ],
    [
        'value' => 0,
        'name' => __('Incorrect'),
    ],
];
?>

<div class="page-header">
    <h1><?php echo $q ? (__('Edit question "') . $stem . '"') : (__('New question for "') . $templateData['map']->name . '"'); ?></h1>
</div>

<form class="form-horizontal" method="POST"
      action="<?php echo URL::base() . 'questionManager/questionGridPOST/' . $mapId . '/' . $type . '/' . $questionId; ?>">
    <fieldset class="fieldset">

        <div class="control-group">
            <label class="control-label"><?php echo __('Question type'); ?></label>
            <div class="controls">
                <div class="radio_extended btn-group">
                    <input autocomplete="off" id="question_type_mcq" type="radio"
                           value="<?php echo Model_Leap_Map_Question::ENTRY_TYPE_MCQ_GRID ?>"
                           name="question_type"<?php echo ($type == Model_Leap_Map_Question::ENTRY_TYPE_MCQ_GRID) ? 'checked="checked"' : ''; ?> />
                    <label data-class="btn-info" class="btn" for="question_type_mcq">Multiple choice grid</label>

                    <input autocomplete="off" id="question_type_pcq" type="radio"
                           value="<?php echo Model_Leap_Map_Question::ENTRY_TYPE_PCQ_GRID ?>"
                           name="question_type" <?php echo ($type == Model_Leap_Map_Question::ENTRY_TYPE_PCQ_GRID) ? 'checked="checked"' : ''; ?> />
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
                        <?php if (isset($templateData['question'])) {
                            echo ($templateData['question']->show_answer == 1) ? 'checked="checked"' : '';
                        } else {
                            echo 'checked="checked"';
                        }
                        ?>
                    />
                    <label data-class="btn-info" class="btn" for="showAnswer1"><?php echo __('Show'); ?></label>
                    <input autocomplete="off" id="showAnswer0" type="radio" name="showAnswer"
                           value="0" <?php echo((isset($templateData['question']) && $templateData['question']->show_answer == 0) ? 'checked="checked"' : '') ?>/>
                    <label data-class="btn-info" class="btn" for="showAnswer0"><?php echo __('Do not show'); ?></label>
                </div>
            </div>
        </div>

        <div class="control-group">
            <label class="control-label"><?php echo __('Show submit button') ?></label>
            <div class="controls">
                <div class="radio_extended btn-group">
                    <input autocomplete="off" type="radio" id="showSubmit" name="showSubmit"
                           value="1" <?php echo((isset($templateData['question']) && $templateData['question']->show_submit == 1) ? 'checked="checked"' : '') ?>/>
                    <label data-class="btn-info" class="btn" for="showSubmit"><?php echo __('Show'); ?></label>

                    <input autocomplete="off" type="radio" id="hideSubmit" name="showSubmit" value="0"
                        <?php if (isset($templateData['question'])) {
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

        <div
            class="control-group submitSettingsContainer <?php echo((isset($templateData['question']) && $templateData['question']->show_submit == 1) ? '' : 'hide') ?>">
            <label class="control-label"><?php echo __('Submit button text') ?></label>
            <div class="controls">
                <input autocomplete="off" type="text" name="submitButtonText"
                       value="<?php echo((isset($templateData['question']) && $templateData['question']->submit_text != null) ? $templateData['question']->submit_text : 'Submit'); ?>"/>
            </div>
        </div>

        <div class="control-group">
            <label for="counter" class="control-label"><?php echo __('Track score with existing counter'); ?></label>
            <div class="controls">
                <select id="counter" name="counter">
                    <option value="0">no counter</option>
                    <?php if (isset($templateData['counters']) && count($templateData['counters']) > 0) { ?>
                        <?php foreach ($templateData['counters'] as $counter) { ?>
                            <option
                                value="<?php echo $counter->id; ?>" <?php echo((isset($templateData['question']) && $templateData['question']->counter_id == $counter->id) ? 'selected="selected"' : ''); ?>>
                                <?php echo $counter->name; ?>
                            </option>
                        <?php } ?>
                    <?php } ?>
                </select>
            </div>
        </div>

        <div class="control-group">
            <label for="feedback" class="control-label"><?php echo __('General feedback'); ?></label>
            <div class="controls"><textarea id="feedback"
                                            name="feedback"><?php echo $q ? $templateData['question']->feedback : ''; ?></textarea>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="v"><?php echo __('Private'); ?>
            </label>
            <div class="controls">
                <input type="checkbox" name="is_private" <?php if (isset($templateData['question'])) {
                    echo $templateData['question']->is_private ? 'checked=""' : '"checked"';
                } ?>>
            </div>
        </div>

        <div class="control-group">
            <label class="control-label"><?php echo __('Used'); ?>
            </label>
            <div class="controls">
                <input type="text" readonly value="<?php if (isset($templateData['used'])) {
                    echo $templateData['used'];
                } ?>"/>
            </div>
        </div>

    </fieldset>

    <h3>Sub-questions</h3>
    <table id="sub_questions_list" class="table table-bordered table-striped table-condensed" style="width: 60%">
        <thead>
        <tr>
            <th style="width:2%;">#</th>
            <th>Value</th>
            <th style="width:8%;">Order</th>
            <th style="width:2%;"></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($subQuestions as $subQuestion) { ?>
            <tr>
                <td>
                    <?php echo isset($counter2) ? ++$counter2 : $counter2 = 1; ?>
                </td>
                <td>
                    <input type="text" name="existingSubQuestions[<?php echo $subQuestion->id ?>]"
                           value="<?php echo htmlspecialchars($subQuestion->stem) ?>">
                </td>
                <td>
                    <input type="text" name="existingSubQuestionsOrder[<?php echo $subQuestion->id ?>]"
                           value="<?php echo $subQuestion->order ?>">
                </td>
                <td>
                    <span class="btn btn-danger remove-macros"><i class="icon-trash"></i></span>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>

    <div>
        <span class="btn btn-info addSubQuestion"><i class="icon-plus-sign"></i>Add sub-question</span>
    </div>

    <hr>

    <h3>Responses</h3>
    <table id="responses_list" class="table table-bordered table-striped table-condensed" style="width: 60%">
        <thead>
        <tr>
            <th style="width:2%;">#</th>
            <th>Value</th>
            <th style="width:8%;">Order</th>
            <th style="width:2%;"></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($responses as $response) { ?>
            <tr>
                <td>
                    <?php echo isset($counter1) ? ++$counter1 : $counter1 = 1; ?>
                </td>
                <td>
                    <input type="text" name="existingResponses[<?php echo $response->id ?>]"
                           value="<?php echo htmlspecialchars($response->response) ?>">
                </td>
                <td>
                    <input type="text" name="existingResponsesOrder[<?php echo $response->id ?>]"
                           value="<?php echo $response->order ?>">
                </td>
                <td>
                    <span class="btn btn-danger remove-macros"><i class="icon-trash"></i></span>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>

    <div>
        <span class="btn btn-info addResponseGrid"><i class="icon-plus-sign"></i>Add response</span>
    </div>

    <hr>

    <div>
        <input class="btn btn-primary btn-large question-save-btn" type="submit" name="goToAttributes" value="Save changes and proceed to attributes">
    </div>

    <h3 id="goToAttributes">Attributes</h3>
    <?php if (count($responses) > 0 && count($subQuestions) > 0) { ?>
        <table id="subQuestionAttributes" class="table table-bordered table-striped table-condensed">
            <thead>
            <tr>
                <th></th>
                <?php foreach ($responses as $response) { ?>
                    <th>
                        <?php echo $response->response ?>
                    </th>
                <?php } ?>
            </tr>
            </thead>
            <tbody>

            <?php foreach ($subQuestions as $subQuestion) { ?>
                <tr>
                    <td>
                        <?php echo $subQuestion->stem ?>
                    </td>

                    <?php foreach ($responses as $response) { ?>
                        <td>
                            <div class="control-group">
                                    <input
                                        placeholder="Feedback"
                                        name="attributes[<?php echo $subQuestion->id ?>][<?php echo $response->id ?>][feedback]"
                                        value="<?php if(isset($attributes[$subQuestion->id][$response->id]['feedback'])) echo $attributes[$subQuestion->id][$response->id]['feedback'] ?>">
                            </div>

                            <div class="control-group">
                                    <select name="attributes[<?php echo $subQuestion->id ?>][<?php echo $response->id ?>][correctness]">
                                        <option value=""> - Select correctness - </option>
                                        <?php foreach ($correctness as $variant) { ?>
                                            <option value="<?php echo $variant['value']?>" <?php if(isset($attributes[$subQuestion->id][$response->id]['correctness']) && $attributes[$subQuestion->id][$response->id]['correctness'] == $variant['value']) echo 'selected'; ?>>
                                                Correctness: <?php echo $variant['name']?>
                                            </option>
                                        <?php } ?>
                                    </select>
                            </div>

                            <div class="control-group">
                                    <select name="attributes[<?php echo $subQuestion->id ?>][<?php echo $response->id ?>][score]">
                                        <option value="0"> - Select score - </option>
                                        <?php for ($j = -10; $j <= 10; $j++) { ?>
                                            <option value="<?php echo $j; ?>" <?php if(isset($attributes[$subQuestion->id][$response->id]['score']) && $attributes[$subQuestion->id][$response->id]['score'] == $j) echo 'selected'; ?>>Score: <?php echo $j; ?></option>
                                        <?php } ?>
                                    </select>
                            </div>
                        </td>
                    <?php } ?>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    <?php } else { ?>
        <div class="alert alert-info">
            Please add and save sub-questions and responses first.
        </div>
    <?php } ?>

    <div class="form-actions">
        <div class="pull-right">
            <input class="btn btn-primary btn-large question-save-btn" type="submit" name="Submit" value="Save changes">
        </div>
    </div>
</form>

<script type="text/javascript" src="<?php echo ScriptVersions::get(URL::base() . 'scripts/question.js'); ?>"></script>
<script type="text/javascript"
        src="<?php echo ScriptVersions::get(URL::base() . 'scripts/visualeditor/base64v1_0.js'); ?>"></script>