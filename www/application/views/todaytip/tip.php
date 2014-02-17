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
        src="<?php echo URL::base(); ?>scripts/tinymce/js/tinymce/tinymce.min.js"></script>
<script language="javascript" type="text/javascript">
    tinymce.init({
        selector: "textarea",
        theme: "modern",
        content_css: "<?php echo URL::base(); ?>scripts/tinymce/js/tinymce/plugins/rdface/css/rdface.css,<?php echo URL::base(); ?>scripts/tinymce/js/tinymce/plugins/rdface/schema_creator/schema_colors.css",
        entity_encoding: "raw",
        contextmenu: "link image inserttable | cell row column rdfaceMain",
        closed: /^(br|hr|input|meta|img|link|param|area|source)$/,
        valid_elements : "+*[*]",
        plugins: ["compat3x",
            "advlist autolink lists link image charmap print preview hr anchor pagebreak",
            "searchreplace wordcount visualblocks visualchars code fullscreen",
            "insertdatetime media nonbreaking save table contextmenu directionality",
            "emoticons template paste textcolor layer advtextcolor rdface imgmap"
        ],
        toolbar1: "insertfile undo redo | styleselect | bold italic | fontselect fontsizeselect | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent",
        toolbar2: " link image imgmap|print preview media | forecolor backcolor emoticons ltr rtl layer restoredraft | rdfaceMain",
        image_advtab: true,
        templates: [

        ]
    });
</script>

<div class="page-header">
    <?php if(isset($templateData['tip'])) { ?>
    <div class="pull-right">
        <div class="btn-group">
            <a class="btn" href="<?php echo URL::base(); ?>TodayTipManager/archive/<?php echo $templateData['tip']->id; ?>">
                <i class="icon-folder-close icon-white"></i>
                <?php echo __('Move to archive'); ?></a>
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
                <input class="datepicker" type="text" name="date" id="date" value="<?php if($date != null && isset($date['year']) && isset($date['month']) && isset($date['day'])) { echo $date['month'] . '/' . $date['day'] . '/' . $date['year']; } else { echo date('m/d/Y'); } ?>"/>
            </div>
        </div>
    </fieldset>
    <fieldset class="fieldset">
        <div class="control-group">
            <label for="dateEnd" class="control-label"><?php echo __('End date'); ?></label>

            <?php $date = isset($templateData['tip']) && $templateData['tip']->end_date != null ? date_parse($templateData['tip']->end_date) : null; ?>

            <div class="controls">
                <input class="datepicker" type="text" name="dateEnd" id="dateEnd" value="<?php if($date != null && isset($date['year']) && isset($date['month']) && isset($date['day'])) echo $date['month'] . '/' . $date['day'] . '/' . $date['year']; ?>"/>
                <input type="checkbox" name="withoutDate" value="1" <?php if($date == null) echo 'checked="checked"'; ?>/> without end date
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