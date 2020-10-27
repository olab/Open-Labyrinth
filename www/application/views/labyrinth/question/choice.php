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
$isDropDown = ($type == Model_Leap_Map_Question::ENTRY_TYPE_DROP_DOWN);
$questionId = $q ? $templateData['question']->id : '';
$stem       = $q ? $templateData['question']->stem : '';
$correctness = $templateData['correctness'];

?>

<div class="page-header">
    <h1><?php echo $q ? (__('Edit question "').$stem.'"') : (__('New question for "').$templateData['map']->name.'"'); ?></h1>
</div>

<form class="form-horizontal" method="POST" action="<?php echo URL::base().'questionManager/questionPOST/'.$mapId.'/'.$type.'/'.$questionId; ?>">
    <fieldset class="fieldset">

        <?php if(!$isDropDown){ ?>
        <div class="control-group">
            <label class="control-label"><?php echo __('Question type'); ?></label>
            <div class="controls">
                <div class="radio_extended btn-group">
                    <input autocomplete="off" id="question_type_mcq" type="radio" value="3" name="question_type"<?php echo ($type == 3) ? 'checked="checked"' : ''; ?> />
                    <label data-class="btn-info" class="btn" for="question_type_mcq">Multiple choice</label>

                    <input autocomplete="off" id="question_type_pcq" type="radio" value="4" name="question_type" <?php echo ($type == 4) ? 'checked="checked"' : ''; ?> />
                    <label data-class="btn-info" class="btn" for="question_type_pcq">Pick choice</label>

                    <input autocomplete="off" id="question_type_sct" type="radio" value="7" name="question_type" <?php echo ($type == 7) ? 'checked="checked"' : ''; ?> />
                    <label data-class="btn-info" class="btn" for="question_type_sct">SCT</label>
                </div>
            </div>
        </div>
        <?php } ?>

        <div class="control-group">
            <label for="stem" class="control-label"><?php echo __('Stem'); ?></label>
            <div class="controls"><textarea id="stem" name="stem"><?php echo $stem; ?></textarea></div>
        </div>

        <?php if($isDropDown){ ?>
        <div class="control-group">
            <label class="control-label"><?php echo __('Allow free text') ?></label>
            <div class="controls">
                <div class="radio_extended btn-group">
                    <input autocomplete="off" id="isFreeTextAllowed1" type="radio" name="settingsJSON[isFreeTextAllowed]" value="1"
                    <?php if (isset($templateData['question'])){
                        echo ($templateData['question']->isFreeTextAllowed()) ? 'checked="checked"' : '';
                    } else {
                        echo 'checked="checked"';
                    }
                    ?>
                    />
                    <label data-class="btn-info" class="btn" for="isFreeTextAllowed1"><?php echo __('Yes'); ?></label>
                    <input autocomplete="off" id="isFreeTextAllowed0" type="radio" name="settingsJSON[isFreeTextAllowed]" value="0" <?php echo ((isset($templateData['question']) && !$templateData['question']->isFreeTextAllowed()) ? 'checked="checked"' : '') ?>/>
                    <label data-class="btn-info" class="btn" for="isFreeTextAllowed0"><?php echo __('No'); ?></label>
                </div>
            </div>
        </div>
        <?php } ?>

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

        <?php if(!$isDropDown){ ?>
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
        <?php } ?>

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

        <?php if(!$isDropDown){ ?>
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

        <?php if(!$isDropDown){ ?>
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
        <?php } ?>

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

    <hr>

    <h3><?php echo __('Responses') ?></h3>

    <?php include DOCROOT . 'application/views/labyrinth/question/_googleServiceAccountCredentials.php'?>

    <div class="question-response-panel-group" id="accordion">
        <?php
        if(isset($templateData['question']) AND count($templateData['question']->responses) > 0) {
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
                <div class="panel-heading response-labels">
                    <?php echo __('Response'); ?>
                    <input type="text" class="response-input" id="response_<?php echo $response->id; ?>" value="<?php echo $response->response; ?>">

                    <?php echo __('Feedback'); ?>
                    <input autocomplete="off" class="feedback-input" type="text" id="feedback_<?php echo $response->id; ?>" name="feedback_<?php echo $response->id; ?>" value="<?php echo $response->feedback; ?>"/>

                    <?php echo __('Correctness'); ?>
                    <select class="correctness-select"  id="correctness_<?php echo $response->id; ?>" name="correctness_<?php echo $response->id; ?>">
                        <option value="<?php echo Model_Leap_Map_Question_Response::IS_CORRECT_NEUTRAL?>">
                            - Select -
                        </option>
                        <?php foreach ($correctness as $variant) { ?>
                            <option id="correctness<?php echo $variant['value']?>_<?php echo $response->id; ?>" value="<?php echo $variant['value']?>" <?php if($response->is_correct == $variant['value']) echo 'selected'; ?>>
                                <?php echo __($variant['name']); ?>
                            </option>
                        <?php } ?>
                    </select>

                    <?php echo __('Score'); ?>
                    <select class="score-select number-select" id="score_<?php echo $response->id; ?>" name="score_<?php echo $response->id; ?>">
                        <?php for ($j = -10; $j <= 10; $j++) { ?>
                            <option value="<?php echo $j; ?>" <?php echo ($response->score == $j ? 'selected=""' : ''); ?>><?php echo $j; ?></option>
                        <?php } ?>
                    </select>

                    <?php echo __('Order'); ?>
                    <select class="response-order-select number-select" id="order_<?php echo $response->id; ?>" name="order_<?php echo $response->id; ?>">
                        <?php for ($j = 1; $j <= count($templateData['question']->responses); $j++) { ?>
                            <option value="<?php echo $j; ?>" <?php echo ($responseIndex == $j ? 'selected=""' : ''); ?>><?php echo $j; ?></option>
                        <?php } ?>
                    </select>

                    <button type="button" class="btn-remove-response btn btn-danger btn-small"><i class="icon-trash"></i></button>
                </div>
            </div>

                <?php
            $responseIndex++;
            }
        } ?>
    </div><?php
    } ?>

    <div class="form-actions">
        <div class="pull-left">
            <button class="btn btn-info" type="button" id="addResponse"><i class="icon-plus-sign"></i>Add response</button>
        </div>
        <div class="pull-right">
            <input class="btn btn-primary btn-large question-save-btn" type="submit" name="Submit" value="Save changes">
        </div>
    </div>
</form>

<script type="text/javascript" src="<?php echo ScriptVersions::get(URL::base().'scripts/question.js'); ?>"></script>
<script type="text/javascript" src="<?php echo ScriptVersions::get(URL::base().'scripts/visualeditor/base64v1_0.js'); ?>"></script>