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
        src="<?php echo URL::base(); ?>scripts/tinymce4/js/tinymce/tinymce.min.js"></script>
<script src="<?php echo ScriptVersions::get(URL::base().'scripts/farbtastic/farbtastic.js'); ?>"></script>
<script src="<?php echo ScriptVersions::get(URL::base().'scripts/popup.js'); ?>"></script>
<script src="<?php echo ScriptVersions::get(URL::base().'scripts/editableselect.js'); ?>"></script>


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

    <?php if (isset($templateData['message'])){ ?>
        <h1><?php echo __('Edit message') . ' - "' .  $templateData['message']->title . '"'; ?></h1>
    <?php } else { ?>
        <h1><?php echo __('Add new message'); ?></h1>
    <?php } ?>

<form class="form-horizontal" id="form" name="form" method="post"
      action="<?php echo (!isset($templateData['message'])) ? URL::base() . 'popupManager/createMessage/' . $templateData['map']->id : URL::base() . 'popupManager/updateMessage/' . $templateData['map']->id . '/' . $templateData['message']->id; ?>">

    <fieldset class="fieldset">
        <legend><?php echo __('Message Details'); ?></legend>
        <div class="control-group">
            <label for="title" class="control-label"><?php echo __('Title'); ?></label>

            <div class="controls">
                <input class="span6" type="text" name="title" id="title" value="<?php echo (isset($templateData['message'])) ? $templateData['message']->title : ''; ?>" />
            </div>
        </div>

            <div class="control-group">
                <label for="text" class="control-label"><?php echo __('Text'); ?></label>

                <div class="controls">
                    <textarea name="text" id="text" class="mceEditor"><?php echo (isset($templateData['message'])) ? $templateData['message']->text : '';  ?></textarea>
                </div>
            </div>

        <div class="control-group">
            <label class="radio">
                <?php echo __('Inside Node Area'); ?>
                <input type="radio" name="position_type" id="position_type_inside"
                       value=0 <?php if (isset($templateData['message']) && !$templateData['message']->position_type) echo 'checked=""'; else echo 'checked=""'; ?>>
            </label>

            <label class="radio">
                <?php echo __('Outside Node Area'); ?>
                <input type="radio" name="position_type" id="position_type_outside"
                       value=1 <?php if (isset($templateData['message']) && $templateData['message']->position_type) echo 'checked=""'; ?>>
            </label>
        </div>

        <div class="control-group">
            <label for="position" class="control-label"><?php echo __('Position'); ?>
            </label>
            <div class="controls">
                <select  id="position" name="position">
                    <?php if(isset($templateData['positions'])) { ?>
                        <?php foreach($templateData['positions'] as $position) { ?>
                            <option value="<?php echo $position->id; ?>" <?php if( isset($templateData['message']) && $position->id == $templateData['message']->position) echo 'selected'; ?>><?php echo $position->name; ?></option>
                        <?php } ?>
                    <?php } ?>
                </select>
            </div>
        </div>
    </fieldset>

    <fieldset class="fieldset">
        <legend><?php echo __(' Message Styling');?> </legend>
        <div class="control-group">
            <label class="control-label"><?php echo __('Color') ?></label>
            <div class="controls">
                <div class="radio_extended btn-group">
                    <input autocomplete="off" type="radio" id="color_default" name="color_default" value="1" <?php echo ((isset($templateData['message']) && $templateData['message']->color_custom == '') ? 'checked="checked"' : '') ?>
                        <?php if(!isset($templateData['message'])) echo 'checked="checked"';  ?>  />
                    <label data-class="btn-info" class="btn" for="color_default"><?php echo __('Default'); ?></label>

                    <input autocomplete="off" type="radio" id="color_custom" name="color_default" value="0" <?php echo ((isset($templateData['message']) && $templateData['message']->color_custom != '') ? 'checked="checked"' : '') ?>/>
                    <label data-class="btn-info" class="btn" for="color_custom"><?php echo __('Custom'); ?></label>
                </div>
            </div>
        </div>
        <div class="control-group submitSettingsContainerColorDefault <?php echo ((isset($templateData['message']) && $templateData['message']->color_custom == '') || (!isset($templateData['message'])) ? '' : 'hide') ?>">
            <label class="control-label"><?php echo __('Select default color') ?></label>
            <div class="controls">
                <select  id="color" name="color">
                <?php if(isset($templateData['styles'])) { ?>
                    <?php foreach($templateData['styles'] as $style) { ?>
                        <option value="<?php echo $style->id; ?>" <?php if( isset($templateData['message']) && $style->id == $templateData['message']->color) echo 'selected'; ?>><?php echo $style->name; ?></option>
                    <?php } ?>
                <?php } ?>
                </select>
            </div>
        </div>

        <div class="control-group submitSettingsContainerColorCustom <?php echo ((isset($templateData['message']) && $templateData['message']->color_custom != '') ? '' : 'hide') ?>">
            <label class="control-label"><?php echo __('Select custom color') ?></label>
            <div class="controls" id="center">
                    <input id="color_code" name="color_code" type="text" style="" value="<?php  echo ((isset($templateData['message']) && $templateData['message']->color_custom != '') ? $templateData['message']->color_custom : '#333333') ?>" />
                    <div id="font_color_cntr"></div>
            </div>
        </div>

    </fieldset>

    <fieldset class="fieldset">
        <legend><?php echo __(' Message Timing');?> </legend>

        <div class="controls">
            <label class="radio">
                <?php echo __('On'); ?>
                <input type="radio" name="enabled" id="timing-on"
                       value=1 <?php if (isset($templateData['message']) && $templateData['message']->enabled) echo 'checked=""'; ?>>

            <div class="control-group">
                <br />
                <label class="control-label" for="time_before"><?php echo __('Time before appearance'); ?></label>
                <div class="controls">
                    <input
                        <?php if ( isset($templateData['message']) && !$templateData['message']->enabled) echo 'disabled';
                              if (!isset($templateData['message'])) echo 'disabled'; ?>
                        name="time_before" type="text" class="span1" id="time_before"
                        value="<?php if (isset($templateData['message'])) echo $templateData['message']->time_before; ?>" selectBoxOptions="1;5;10;15;20;25;30;35;40;45;50;55;60">
                 </div>
                <br />
                <label class="control-label" for="time_length"><?php echo __('Timed length of appearance'); ?></label>
                <div class="controls">
                    <input
                        <?php if ( isset($templateData['message']) && !$templateData['message']->enabled) echo 'disabled';
                              if (!isset($templateData['message'])) echo 'disabled';  ?>
                        name="time_length" type="text" class="span1" id="time_length"
                        value="<?php if (isset($templateData['message'])) echo $templateData['message']->time_length; ?>" selectBoxOptions="1;5;10;15;20;25;30;35;40;45;50;55;60">
                </div>
            </div>
            </label>

            <label class="radio">
                <?php echo __('Off'); ?>
                <input id="timing-off" type="radio" name="enabled"
                    value=0 <?php if (isset($templateData['message']) && !$templateData['message']->enabled) echo 'checked=""'; ?>
                            <?php if (!isset($templateData['message'])) echo 'checked=""';?>>
            </label>
        </div>

    </fieldset>

    <fieldset class="fieldset">
        <legend><?php echo __(' Message Assign');?></legend>

        <div class="control-group">
            <label class="control-label"><?php echo __('Assign to') ?></label>
            <div class="controls">
                <div class="radio_extended btn-group">
                    <input autocomplete="off" type="radio" id="labyrinth" name="assign_to_node" value="0"
                        <?php if (isset($templateData['message'])){
                            echo ($templateData['message']->assign_to_node == 0) ? 'checked="checked"' : '';
                        } else {
                            echo 'checked="checked"';
                        }
                        ?>
                        />
                    <label data-class="btn-info" class="btn" for="labyrinth"><?php echo __('Labyrinth'); ?></label>

                    <input autocomplete="off" type="radio" id="node" name="assign_to_node" value="1" <?php echo ((isset($templateData['message']) && $templateData['message']->assign_to_node == 1) ? 'checked="checked"' : '') ?>/>
                    <label data-class="btn-info" class="btn" for="node"><?php echo __('Node'); ?></label>
                </div>
            </div>
        </div>
        <div class="control-group submitSettingsContainer <?php echo ((isset($templateData['message']) && $templateData['message']->assign_to_node == 1) ? '' : 'hide') ?>">
            <label class="control-label"><?php echo __('Select node') ?></label>
            <div class="controls">
                <div class="controls">
                    <select  id="node_id" name="node_id">
                        <?php if(isset($templateData['nodes'])) { ?>
                            <?php foreach($templateData['nodes'] as $node) { ?>
                                <option value="<?php echo $node->id; ?>" <?php if( isset($templateData['message']) && $node->id == $templateData['message']->node_id) echo 'selected'; ?>><?php echo $node->title; ?></option>
                            <?php } ?>
                        <?php } ?>
                    </select>
                </div>
            </div>
        </div>

    </fieldset>

    <div class="form-actions">
        <div class="pull-right">
            <input class="btn btn-large btn-primary" type="submit" name="Submit"
                   value="<?php echo (isset($templateData['message'])) ? 'Edit message' : 'Add message'; ?>" onclick="return CheckForm();">
        </div>
    </div>

</form>

<script>
    createEditableSelect(document.getElementById('time_before'));
    createEditableSelect(document.getElementById('time_length'));
</script>
