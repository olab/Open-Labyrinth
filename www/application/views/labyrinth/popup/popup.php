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

<script>
    var labyrinthAssignTypeId = <?php echo Popup_Assign_Types::LABYRINTH; ?>,
        nodeAssignTypeId      = <?php echo Popup_Assign_Types::NODE; ?>,
        sectionAssignTypeId   = <?php echo Popup_Assign_Types::SECTION; ?>;
</script>
<script language="javascript" type="text/javascript"
        src="<?php echo URL::base(); ?>scripts/tinymce/jscripts/tiny_mce/tiny_mce.js"></script>
<script src="<?php echo ScriptVersions::get(URL::base().'scripts/farbtastic/farbtastic.js'); ?>"></script>
<script src="<?php echo ScriptVersions::get(URL::base().'scripts/popup.js'); ?>"></script>
<script src="<?php echo ScriptVersions::get(URL::base().'scripts/editableselect.js'); ?>"></script>

<script language="javascript" type="text/javascript">
    tinyMCE.init({
        // General options
        mode: "textareas",
        relative_urls: false,
        theme: "advanced",
        skin: "bootstrap",
        plugins: "autolink,lists,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,wordcount,advlist,autosave,imgmap",
        // Theme options
        theme_advanced_buttons1: "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",
        theme_advanced_buttons2: "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
        theme_advanced_buttons3: "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
        theme_advanced_buttons4: "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak,restoredraft,|,imgmap",
        theme_advanced_toolbar_location: "top",
        theme_advanced_toolbar_align: "left",
        theme_advanced_statusbar_location: "bottom",
        theme_advanced_resizing: true,
        editor_selector: "mceEditor"
    });
</script>

<h1>
    <?php echo isset($templateData['popup']) ? (__('Edit message') . ' - "' .  $templateData['popup']->title . '"') : __('Add new message'); ?>
</h1>

<form class="form-horizontal" id="form" name="form" method="post" action="<?php echo URL::base(); ?>popupManager/savePopup/<?php echo $templateData['map']->id; ?><?php if(isset($templateData['popup'])) echo '/' . $templateData['popup']->id; ?>">
    <?php if(isset($templateData['popup'])) { ?>
        <input type="hidden" value="<?php echo $templateData['popup']->id; ?>" name="popupId"/>
    <?php } ?>
    <fieldset class="fieldset">
        <legend><?php echo __('Message Details'); ?></legend>
        <div class="control-group">
            <label for="title" class="control-label"><?php echo __('Title'); ?></label>

            <div class="controls">
                <input class="span6" type="text" name="title" id="title" value="<?php if(isset($templateData['popup'])) echo $templateData['popup']->title; ?>" />
            </div>
        </div>

        <div class="control-group">
            <label for="text" class="control-label"><?php echo __('Text'); ?></label>

            <div class="controls">
                <textarea name="text" id="text" class="mceEditor"><?php if(isset($templateData['popup'])) echo $templateData['popup']->text;  ?></textarea>
            </div>
        </div>

        <?php if(isset($templateData['popupPositionTypes']) && count($templateData['popupPositionTypes']) > 0) { ?>
        <div class="control-group">
            <label class="control-label"><?php echo __('Position type') ?></label>
            <div class="controls">
                <div class="radio_extended btn-group">
                    <?php foreach($templateData['popupPositionTypes'] as $positionType) { ?>
                        <input type="radio"
                               autocomplete="off"
                               id="positionType_<?php echo $positionType->id; ?>"
                               name="positionType"
                               value="<?php echo $positionType->id; ?>"
                               <?php if(isset($templateData['popup']) && $templateData['popup']->position_type == $positionType->id) { echo 'checked="checked"'; } ?>
                        />
                        <label data-class="btn-info" class="btn" for="positionType_<?php echo $positionType->id; ?>"><?php echo $positionType->title; ?></label>
                    <?php } ?>
                </div>
            </div>
        </div>
        <?php } ?>

        <?php if(isset($templateData['popupPositions']) && count($templateData['popupPositions']) > 0) { ?>
            <div class="control-group">
                <label for="position" class="control-label"><?php echo __('Position'); ?>
                </label>
                <div class="controls">
                    <select  id="position" name="position">
                        <?php foreach($templateData['popupPositions'] as $popupPositions) { ?>
                            <option value="<?php echo $popupPositions->id; ?>" <?php if( isset($templateData['popup']) && $popupPositions->id == $templateData['popup']->position_id) echo 'selected'; ?>><?php echo $popupPositions->title; ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
        <?php } ?>
    </fieldset>

    <fieldset class="fieldset">
        <legend><?php echo __('Message Styling');?> </legend>
        <div class="control-group">
            <label class="control-label"><?php echo __('Font color'); ?></label>
            <div class="controls">
                <input type="text" id="fontColor" name="fontColor" value="<?php echo isset($templateData['popup']) ? $templateData['popup']->style->font_color : '#000000'; ?>" style="background-color: <?php echo isset($templateData['popup']) ? $templateData['popup']->style->font_color : '#000000'; ?>;"/>
                <div class="fontColorContainer"></div>
            </div>
        </div>

        <div class="control-group">
            <label class="control-label"><?php echo __('Border color'); ?></label>
            <div class="controls">
                <input type="text" id="borderColor" name="borderColor" value="<?php echo isset($templateData['popup']) ? $templateData['popup']->style->border_color : '#ffffff'; ?>" style="background-color: <?php echo isset($templateData['popup']) ? $templateData['popup']->style->border_color : '#ffffff'; ?>;"/>
                <input type="checkbox" id="isBorderTransparent" name="isBorderTransparent" style="margin: 0;" <?php if(isset($templateData['popup']) && $templateData['popup']->style->is_border_transparent) { echo 'checked="checked"'; } ?>> Transparent
                <div class="borderColorContainer"></div>
            </div>
        </div>

        <div class="control-group">
            <label class="control-label"><?php echo __('Background color'); ?></label>
            <div class="controls">
                <div class="radio_extended btn-group">
                    <input autocomplete="off" type="radio" id="backgroundColorDefault" name="isDefaultBackgroundColor" value="1" <?php if(isset($templateData['popup']) && $templateData['popup']->style->is_default_background_color) { echo 'checked="checked"'; } elseif(!isset($templateData['popup'])) { echo 'checked="checked"'; } ?>/>
                    <label data-class="btn-info" class="btn" for="backgroundColorDefault"><?php echo __('Default'); ?></label>

                    <input autocomplete="off" type="radio" id="backgroundColorCustom" name="isDefaultBackgroundColor" value="0" <?php if(isset($templateData['popup']) && !$templateData['popup']->style->is_default_background_color) { echo 'checked="checked"'; } ?>/>
                    <label data-class="btn-info" class="btn" for="backgroundColorCustom"><?php echo __('Custom'); ?></label>
                </div>
            </div>
        </div>

        <div class="control-group defaultBackgroundColors <?php if(isset($templateData['popup']) && !$templateData['popup']->style->is_default_background_color) { echo 'hide'; } ?>">
            <label class="control-label">&nbsp;</label>
            <div class="controls">
                <select id="defaultBackgroundColor" name="defaultBackgroundColor">
                    <option value="#ffff00" <?php if(isset($templateData['popup']) && $templateData['popup']->style->background_color == '#ffff00') { echo 'selected="selected"'; } ?>><?php echo __('yellow'); ?></option>
                    <option value="#ff0000" <?php if(isset($templateData['popup']) && $templateData['popup']->style->background_color == '#ff0000') { echo 'selected="selected"'; } ?>><?php echo __('red'); ?></option>
                    <option value="#00ff00" <?php if(isset($templateData['popup']) && $templateData['popup']->style->background_color == '#00ff00') { echo 'selected="selected"'; } ?>><?php echo __('green'); ?></option>
                    <option value="#0000ff" <?php if(isset($templateData['popup']) && $templateData['popup']->style->background_color == '#0000ff') { echo 'selected="selected"'; } ?>><?php echo __('blue'); ?></option>
                </select>
            </div>
        </div>

        <div class="control-group customBackgroundColors <?php if(isset($templateData['popup']) && $templateData['popup']->style->is_default_background_color) { echo 'hide'; } elseif(!isset($templateData['popup'])) { echo 'hide'; } ?>">
            <label class="control-label">&nbsp;</label>
            <div class="controls">
                <input type="text" id="customBackgroundColor" name="customBackgroundColor" value="<?php echo isset($templateData['popup']) && !$templateData['popup']->style->is_default_background_color ? $templateData['popup']->style->background_color : '#ffff00'; ?>" style="background-color: <?php echo isset($templateData['popup']) && !$templateData['popup']->style->is_default_background_color ? $templateData['popup']->style->background_color : '#ffff00'; ?>;" />
                <input type="checkbox" id="isBorderTransparent" name="isBackgroundTransparent" style="margin: 0;" <?php if(isset($templateData['popup']) && $templateData['popup']->style->is_background_transparent) { echo 'checked="checked"'; } ?>> Transparent
                <div class="customBackgroundColorContainer"></div>
            </div>
        </div>
    </fieldset>

    <fieldset class="fieldset">
        <legend><?php echo __('Message Timing');?> </legend>
        <div class="controls">
            <label class="radio">
                <?php echo __('On'); ?>
                <input type="radio" name="enabled" id="timing-on"
                       value=1 <?php if (isset($templateData['popup']) && $templateData['popup']->is_enabled) echo 'checked=""'; ?>>

                <div class="control-group">
                    <div class="control-group">
                        <label class="control-label"><?php echo __('Time before appearance'); ?></label>
                        <div class="controls">
                            <input
                                <?php if ( isset($templateData['popup']) && !$templateData['popup']->is_enabled) echo 'disabled';
                                if (!isset($templateData['popup'])) echo 'disabled'; ?>
                                name="timeBefore" type="text" class="span1" id="timeBefore"
                                value="<?php if (isset($templateData['popup'])) echo $templateData['popup']->time_before; ?>" selectBoxOptions="1;5;10;15;20;25;30;35;40;45;50;55;60">
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label"><?php echo __('Timed length of appearance'); ?></label>
                        <div class="controls">
                            <input
                                <?php if ( isset($templateData['popup']) && !$templateData['popup']->is_enabled) echo 'disabled';
                                if (!isset($templateData['popup'])) echo 'disabled';  ?>
                                name="timeLength" type="text" class="span1" id="timeLength"
                                value="<?php if (isset($templateData['popup'])) echo $templateData['popup']->time_length; ?>" selectBoxOptions="1;5;10;15;20;25;30;35;40;45;50;55;60">
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label"><?php echo __('Redirect'); ?></label>
                        <div class="controls">
                            <div class="radio_extended btn-group redirect-options-container">
                                <input autocomplete="off" type="radio" id="redirectTypeNone" name="redirectType" value="<?php echo Popup_Redirect_Types::NONE; ?>" <?php if(isset($templateData['popup']) && $templateData['popup']->assign->redirect_type_id == Popup_Redirect_Types::NONE) { echo 'checked="checked"'; } ?>/>
                                <label data-class="btn" class="btn" for="redirectTypeNone"><?php echo __('None'); ?></label>

                                <input autocomplete="off" type="radio" id="redirectTypeNode" name="redirectType" value="<?php echo Popup_Redirect_Types::NODE; ?>" <?php if(isset($templateData['popup']) && $templateData['popup']->assign->redirect_type_id == Popup_Redirect_Types::NODE) { echo 'checked="checked"'; } ?>/>
                                <label data-class="btn-info" show-nodes="1" class="btn" for="redirectTypeNode"><?php echo __('Node'); ?></label>

                                <input autocomplete="off" type="radio" id="redirectTypeReport" name="redirectType" value="<?php echo Popup_Redirect_Types::REPORT; ?>" <?php if(isset($templateData['popup']) && $templateData['popup']->assign->redirect_type_id == Popup_Redirect_Types::REPORT) { echo 'checked="checked"'; } ?>/>
                                <label data-class="btn-danger" class="btn" for="redirectTypeReport"><?php echo __('Report'); ?></label>
                            </div>
                        </div>
                    </div>

                    <?php if(isset($templateData['nodes']) && count($templateData['nodes']) > 0) { ?>
                        <div class="control-group <?php if(isset($templateData['popup']) && $templateData['popup']->assign->redirect_type_id != Popup_Redirect_Types::NODE) { echo 'hide'; } ?> redirect-nodes-container">
                            <label class="control-label">&nbsp;</label>
                            <div class="controls">
                                <select name="redirectNodeId" id="redirectNodeId">
                                    <?php foreach($templateData['nodes'] as $node) { ?>
                                        <option value="<?php echo $node->id; ?>" <?php if(isset($templateData['popup']) && $templateData['popup']->assign->redirect_to_id == $node->id) { echo 'selected="selected"'; } ?>><?php echo $node->title; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </label>

            <label class="radio">
                <?php echo __('Off'); ?>
                <input id="timing-off" type="radio" name="enabled"
                       value=0 <?php if (isset($templateData['popup']) && !$templateData['popup']->is_enabled) echo 'checked=""'; ?>
                    <?php if (!isset($templateData['popup'])) echo 'checked=""';?>>
            </label>
        </div>
    </fieldset>

    <?php if(isset($templateData['popupAssignTypes']) && count($templateData['popupAssignTypes']) > 0) { ?>
    <fieldset class="fieldset">
        <legend><?php echo __('Message Assign');?></legend>

        <div class="control-group">
            <label class="control-label"><?php echo __('Assign to') ?></label>
            <div class="controls">
                <div class="radio_extended btn-group">
                    <?php foreach($templateData['popupAssignTypes'] as $popupAssignType) { ?>
                        <input autocomplete="off" type="radio" id="assignType_<?php echo $popupAssignType->id; ?>" name="assignType" value="<?php echo $popupAssignType->id; ?>" <?php if(isset($templateData['popup']) && $templateData['popup']->assign->assign_type_id == $popupAssignType->id) { echo 'checked="checked"'; } ?>/>
                        <label data-class="btn-info" class="btn" for="assignType_<?php echo $popupAssignType->id; ?>"><?php echo $popupAssignType->title; ?></label>
                    <?php } ?>
                </div>
            </div>
        </div>

        <?php if(isset($templateData['nodes']) && count($templateData['nodes']) > 0) { ?>
            <div class="control-group popup-assign-<?php echo Popup_Assign_Types::NODE; ?>-container <?php if(isset($templateData['popup']) && $templateData['popup']->assign->assign_type_id != Popup_Assign_Types::NODE) { echo 'hide'; } else if(!isset($templateData['popup'])) { echo 'hide'; } ?>">
                <label class="control-label">&nbsp;</label>
                <div class="controls">
                    <select name="node">
                        <?php foreach($templateData['nodes'] as $node) { ?>
                            <option value="<?php echo $node->id; ?>" <?php if(isset($templateData['popup']) && $templateData['popup']->assign->assign_to_id == $node->id) { echo 'selected="selected"'; } ?>><?php echo $node->title; ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
        <?php } ?>

        <?php if(isset($templateData['sections']) && count($templateData['sections']) > 0) { ?>
            <div class="control-group popup-assign-<?php echo Popup_Assign_Types::SECTION; ?>-container <?php if(isset($templateData['popup']) && $templateData['popup']->assign->assign_type_id != Popup_Assign_Types::SECTION) { echo 'hide'; } else if(!isset($templateData['popup'])) { echo 'hide'; } ?>">
                <label class="control-label">&nbsp;</label>
                <div class="controls">
                    <select name="section">
                        <?php foreach($templateData['sections'] as $section) { ?>
                            <option value="<?php echo $section->id; ?>" <?php if(isset($templateData['popup']) && $templateData['popup']->assign->assign_to_id == $section->id) { echo 'selected="selected"'; } ?>><?php echo $section->name; ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
        <?php } ?>
    </fieldset>
    <?php } ?>

    <div class="form-actions">
        <div class="pull-right">
            <input class="btn btn-large btn-primary" type="submit" name="Submit" value="<?php echo (isset($templateData['popup'])) ? __('Save message') : __('Add message'); ?>">
        </div>
    </div>
</form>

<script>
    createEditableSelect(document.getElementById('timeBefore'));
    createEditableSelect(document.getElementById('timeLength'));
</script>