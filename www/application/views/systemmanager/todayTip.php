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
<script language="javascript" type="text/javascript" src="<?php echo URL::base(); ?>scripts/tinymce4/js/tinymce/tinymce.min.js"></script>
<script language="javascript" type="text/javascript">
    tinymce.init({
        selector: ".mceEditor",
        theme: "modern",
        valid_elements: "+*[*]",

        plugins: [
            "advlist autolink lists link image charmap print preview anchor",
            "searchreplace visualblocks code fullscreen",
            "insertdatetime media table  contextmenu paste "
        ],
        toolbar1: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image",
        toolbar2: "print preview media | forecolor backcolor  ",
        image_advtab: true,
        entity_encoding: "raw",
        contextmenu: "link image inserttable | cell row column ",
        closed: /^(br|hr|input|meta|img|link|param|area|source)$/
    });
</script>
<form class="form-horizontal" name="todaytip_form" action="<?php echo URL::base() . 'systemManager/updateTodayTip/'; ?>" method="post">
    <input type="hidden" name="token" value="<?php echo $templateData['token']; ?>" />
    <fieldset class="fieldset">
        <legend><?php echo __("Today's Tip"); ?></legend>
        <div class="control-group">
            <label class="control-label" for="title"><?php echo __('Title'); ?></label>
            <div class="controls">
                <input class="span5" id="title" name="title" value="<?php echo $templateData['todayTip']->title; ?>" />
                <span class="help-block">
                    <small>
                        <span class="label label-info"><?php echo __('Title will not be shown if title field is empty.'); ?></span>
                    </small>
                </span>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="text"><?php echo __('Tip text'); ?></label>
            <div class="controls">
                <textarea name="text" cols='60' id="text" rows='10' class='mceEditor'><?php echo $templateData['todayTip']->text; ?></textarea>
                <span class="help-block">
                    <small>
                        <span class="label label-info"><?php echo __('Today tips will not be shown if tip text is empty.'); ?></span>
                    </small>
                </span>
            </div>
        </div>
        <input type="submit" class="btn btn-primary" value="<?php echo __('Update Settings'); ?>" />
    </fieldset>
</form>