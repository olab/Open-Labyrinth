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

<script language="javascript" type="text/javascript"
        src="<?php echo URL::base(); ?>scripts/tinymce/jscripts/tiny_mce/tiny_mce.js"></script>
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

<div class="page-header">
    <?php if(isset($templateData['tip'])) { ?>
    <div class="pull-right">
        <div class="btn-group">
            <a class="btn" href="<?php echo URL::base(); ?>TodayTipManager/archive/<?php echo $templateData['tip']->id; ?>">
                <?php echo __('Archive a tip'); ?></a>
        </div>
    </div>
    <?php } ?>
    <h1><?php echo isset($templateData['tip']) ? __("Edit tip") : __('Add tip'); ?></h1>
</div>

<form class="form-horizontal" id="form" name="form" method="post" action="<?php echo URL::base(); ?>TodayTipManager/saveTip">
    <input type="hidden" name="tipId" value="<?php if(isset($templateData['tip'])) echo $templateData['tip']->id; ?>"/>
    <div class="control-group">
        <label class="control-label"><?php echo __('Active'); ?></label>
        <div class="controls">
            <div class="radio_extended btn-group">
                <input autocomplete="off" id="tip_active_on" type="radio" value="1" name="active" <?php echo isset($templateData['tip']) ? $templateData['tip']->is_active ? 'checked="checked"' : '' : ''; ?>/>
                <label data-class="btn-success" class="btn" for="tip_active_on">Active</label>
                <input autocomplete="off" id="tip_active_off" type="radio" value="0" name="active" <?php echo isset($templateData['tip']) ? !$templateData['tip']->is_active ? 'checked="checked"' : '' : 'checked="checked"'; ?> />
                <label data-class="btn-danger" class="btn" for="tip_active_off">Inactive</label>
            </div>
        </div>
    </div>
    <fieldset class="fieldset">
        <div class="control-group">
            <label for="title" class="control-label"><?php echo __('Title'); ?></label>

            <div class="controls">
                <input class="span6" type="text" name="title" id="title" value="<?php if(isset($templateData['tip'])) echo $templateData['tip']->title; ?>"/>
            </div>
        </div>
    </fieldset>
    <fieldset class="fieldset">
        <div class="control-group">
            <label for="text" class="control-label"><?php echo __('Text'); ?></label>

            <div class="controls">
                <textarea name="text" id="text" class="mceEditor"><?php if(isset($templateData['tip'])) echo $templateData['tip']->text; ?></textarea>
            </div>
        </div>
    </fieldset>
    <fieldset class="fieldset">
        <div class="control-group">
            <label for="date" class="control-label"><?php echo __('Start date'); ?></label>

            <?php $date = isset($templateData['tip']) && $templateData['tip']->start_date != null ? date_parse($templateData['tip']->start_date) : null; ?>

            <div class="controls">
                <input class="datepicker" type="text" name="date" id="date" value="<?php if($date != null && isset($date['year']) && isset($date['month']) && isset($date['day'])) echo $date['month'] . '/' . $date['day'] . '/' . $date['year']; ?>"/>
                <?php echo __('Time'); ?> <input class="span1" type="text" name="hours" id="hours" value="<?php if($date != null && isset($date['hour'])) echo $date['hour'] < 10 ? '0' . $date['hour'] : $date['hour']; ?>"/>:<input class="span1" type="text" name="minute" id="minute" value="<?php if($date != null && isset($date['minute'])) echo $date['minute'] < 10 ? '0' . $date['minute'] : $date['minute']; ?>"/>
            </div>
        </div>
    </fieldset>
    <fieldset class="fieldset">
        <div class="control-group">
            <label for="dateEnd" class="control-label"><?php echo __('End date'); ?></label>

            <?php $date = isset($templateData['tip']) && $templateData['tip']->end_date != null ? date_parse($templateData['tip']->end_date) : null; ?>

            <div class="controls">
                <input class="datepicker" type="text" name="dateEnd" id="dateEnd" value="<?php if($date != null && isset($date['year']) && isset($date['month']) && isset($date['day'])) echo $date['month'] . '/' . $date['day'] . '/' . $date['year']; ?>"/>
                <?php echo __('Time'); ?> <input class="span1" type="text" name="hoursEnd" id="hoursEnd" value="<?php if($date != null && isset($date['hour'])) echo $date['hour'] < 10 ? '0' . $date['hour'] : $date['hour']; ?>"/>:<input class="span1" type="text" name="minuteEnd" id="minuteEnd" value="<?php if($date != null && isset($date['minute'])) echo $date['minute'] < 10 ? '0' . $date['minute'] : $date['minute']; ?>"/>
            </div>
        </div>
    </fieldset>
    <fieldset class="fieldset">
        <div class="control-group">
            <label for="weight" class="control-label"><?php echo __('Weight'); ?></label>

            <div class="controls">
                <input class="span6" type="text" name="weight" id="weight" value="<?php if(isset($templateData['tip'])) echo $templateData['tip']->weight; ?>"/>
            </div>
        </div>
    </fieldset>

    <div class="form-actions">
        <div class="pull-right">
            <input class="btn btn-primary btn-large" type="submit" name="Submit" value="Save changes">
        </div>
    </div>
</form>