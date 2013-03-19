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
if (isset($templateData['map'])) {
    ?>
    <script language="javascript" type="text/javascript"
            src="<?php echo URL::base(); ?>scripts/tinymce/jscripts/tiny_mce/tiny_mce.js"
            xmlns="http://www.w3.org/1999/html"></script>
    <script language="javascript">
        tinyMCE.init({
            // General options
            mode: "textareas",
            relative_urls: false,
            theme: "advanced",
            skin: "bootstrap",
            entity_encoding: "raw",
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
    <h1><?php echo __('NodeGrid "') . $templateData['map']->name . '"'; ?></h1></div>
    <form class="form-horizontal" action="<?php echo URL::base() . 'nodeManager/saveGrid/' . $templateData['map']->id; ?>" method="POST">

        <?php if (isset($templateData['nodes']) and count($templateData['nodes']) > 0) { ?>


                <?php foreach ($templateData['nodes'] as $node) { ?>
    <fieldset class="fieldset">
        <legend>Node with ID <?php echo $node->id; ?> <?php if ($node->type->name == 'root') echo __('(root)'); ?></legend>
            <div class="control-group">
                <label for="title_<?php echo $node->id; ?>" class="control-label"><?php echo __('Title'); ?></label>

                <div class="controls">
                    <input class="span6" type="text" id="title_<?php echo $node->id; ?>" name="title_<?php echo $node->id; ?>"
                           value="<?php echo $node->title; ?>">
                </div>
            </div>
        <div class="control-group">
            <label for="text_<?php echo $node->id; ?>" class="control-label"><?php echo __('Description'); ?></label>

            <div class="controls" >
                <textarea class="span6 mceEditor" id="text_<?php echo $node->id; ?>"
                          name="text_<?php echo $node->id; ?>"><?php echo $node->text; ?></textarea>
            </div>
        </div>
    </fieldset>

                <?php } ?>
        <?php } ?>
        <div class="form-actions">
            <div class="pull-right">
        <input class="btn btn-primary btn-large" type="submit" name="Submit" value="<?php echo __('Save changes'); ?>"></div>
        </div>

    </form>

<?php } ?>