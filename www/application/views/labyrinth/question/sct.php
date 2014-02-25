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

$title          = __('New SCT question for "').$templateData['map']->name.'"';
$idMap          = $templateData['map']->id;
$idType         = $templateData['type']->id;
$idQuestion     = '';
$stem           = '';
$numTries       = 1;
$responses      = array();
$type_display   = 0;

if(isset($templateData['question'])) {
    $title          = __('Edit SCT question "').$templateData['question']->stem.'"';
    $idQuestion     = $templateData['question']->id;
    $stem           = $templateData['question']->stem;
    $numTries       = $templateData['question']->num_tries;
    $type_display   = $templateData['question']->type_display;
    if (count($templateData['question']->responses) > 0) $responses = $templateData['question']->responses;
}?>
<div class="page-header">
    <h1><?php echo $title; ?></h1>
</div>

<form class="form-horizontal" method="POST" action="<?php echo URL::base().'questionManager/questionPOST/'.$idMap.'/'.$idType.'/'.$idQuestion; ?>">

    <fieldset class="fieldset">
        <div class="control-group">
            <label for="stem" class="control-label"><?php echo __('Stem'); ?></label>
            <div class="controls"><textarea id="stem" name="stem"><?php echo $stem; ?></textarea></div>
        </div>
        <div class="control-group">
            <label class="control-label"><?php echo __('Number of tries allowed'); ?></label>
            <div class="controls">
                <div class="radio_extended btn-group">
                    <input autocomplete="off" id="tries1" type="radio" name="tries" value="1" <?php if ($numTries == 1) echo 'checked'; ?>/>
                    <label data-class="btn-info" class="btn" for="tries1"><?php echo __('One'); ?></label>
                    <input autocomplete="off" id="tries2" type="radio" name="tries" value="2" <?php if ($numTries == 2) echo 'checked'; ?>/>
                    <label data-class="btn-info" class="btn" for="tries2"><?php echo __('Many'); ?></label>
                </div>
            </div>
        </div>
    </fieldset>

    <div class="control-group">
        <label class="control-label"><?php echo __('Layout of answers') ?></label>
        <div class="controls">
            <div class="radio_extended btn-group">
                <input autocomplete="off" id="typeDisplay0" type="radio" name="typeDisplay" value="0" <?php if ( ! $type_display) echo 'checked';?>/>
                <label data-class="btn-info" class="btn" for="typeDisplay0"><?php echo __('Vertical'); ?></label>
                <input autocomplete="off" id="typeDisplay1" type="radio" name="typeDisplay" value="1" <?php if ($type_display) echo 'checked';?>/>
                <label data-class="btn-info" class="btn" for="typeDisplay1"><?php echo __('Horizontal'); ?></label>
            </div>
        </div>
    </div>

    <div class="question-response-panel-group" id="accordion"><?php
        foreach($responses as $response) { $idResponse = $response->id; ?>
        <div class="panel sortable">
            <div class="panel-heading">
                <label><?php echo __('Response'); ?></label>
                <input type="text" class="response-input" name="responses[<?php echo 'id'.$idResponse; ?>]" value="<?php echo $response->response; ?>">
                <a href="<?php echo URL::base().'questionManager/deleteResponseSCT/'.$idResponse ?>" class="btn btn-danger btn-small"><i class="icon-trash"></i></a>
            </div>
        </div><?php
        } ?>
    </div>

    <div class="form-actions">
        <div class="pull-left"><button class="btn btn-info" type="button" id="addResponseSct"><i class="icon-plus-sign"></i>Add response</button></div>
        <div class="pull-right"><input class="btn btn-primary btn-large question-save-btn" type="submit" name="Submit" value="Save changes"></div>
    </div>
</form><?php
} ?>

<!-- add sct question answer -->
<div class="panel sortable sct-js" style="display: none;">
    <div class="panel-heading">
        <label><?php echo __('Response'); ?></label>
        <input type="text" class="response-input" name="responses[]">
        <button type="button" class="btn-remove-response btn btn-danger btn-small"><i class="icon-trash"></i></button>
    </div>
</div>
<!-- end add sct question answer -->

<script type="text/javascript" src="<?php echo ScriptVersions::get(URL::base().'scripts/question.js'); ?>"></script>
<script type="text/javascript" src="<?php echo ScriptVersions::get(URL::base().'scripts/visualeditor/base64v1_0.js'); ?>"></script>