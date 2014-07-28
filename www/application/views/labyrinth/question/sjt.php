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
$show_submit = $question ? $question->show_submit : 0;
$responses   = Arr::get($templateData, 'responses', array());
$score       = Arr::get($templateData, 'score', array()); ?>
<div class="page-header">
    <h1><?php echo $question ? __('Edit question "').$question->stem . '"' : __('New question for "').$map->name.'"'; ?></h1>
</div>
<form class="form-horizontal" method="POST" action="<?php echo URL::base().'questionManager/questionSJT/'.$map->id; ?><?php echo $question ? '/'.$question->id : ''; ?>">
    <fieldset class="fieldset">
        <div class="control-group">
            <label for="stem" class="control-label"><?php echo __('Stem'); ?></label>
            <div class="controls">
                <textarea id="stem" name="stem"><?php echo $question ? $question->stem : ''; ?></textarea>
            </div>
        </div>

        <div class="control-group">
            <label for="feedback" class="control-label"><?php echo __('Feedback'); ?></label>
            <div class="controls"><textarea id="feedback" name="feedback"><?php echo (isset($templateData['question']) ? $templateData['question']->feedback : ''); ?></textarea></div>
        </div>
    </fieldset><?php

    for ($i = 0; $i<5; $i++) {
        $responseObj = Arr::get($responses, $i, false);
        $responseId  = $responseObj ? $responseObj->id : '';
        $response    = $responseObj ? $responseObj->response : ''; ?>
    <div class="question-response-draggable-panel-group">
        <div class="response-panel sortable">
            <label>Response</label>
            <input type="text" class="response-input" name="responses[<?php echo 'i'.$responseId; ?>]" value="<?php echo $response; ?>">
            <span class="score-for-response-l">Score for position</span><?php
            for ($j = 0; $j<5; $j++) { ?>
                <input type="text" class="score-for-response" name="score[<?php echo 'i'.$responseId; ?>][]" value="<?php if (isset($score[$responseId][$j])) echo $score[$responseId][$j]->points; ?>"><?php
            } ?>
        </div>
    </div><?php
    } ?>

    <div class="form-actions">
        <div class="pull-right">
            <input class="btn btn-primary btn-large question-save-btn" type="submit" name="Submit" value="Save changes">
        </div>
    </div>
</form>