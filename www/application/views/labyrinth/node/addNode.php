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

        tinyMCE.init({
            // General options
            mode: "textareas",
            relative_urls: false,
            theme: "advanced",
            skin: "bootstrap",
            plugins: "autolink,lists,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,wordcount,advlist,autosave,imgmap",
            // Theme options
            theme_advanced_buttons1: "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",
            theme_advanced_buttons2: "cut,copy,paste,pastetext,pasteword,|,bullist,numlist,|,outdent,indent,blockquote,|,link,unlink,anchor,image,code,|,forecolor,backcolor",
            theme_advanced_buttons3: "sub,sup,|,charmap,iespell,media,advhr,|,fullscreen,del,ins,attribs,|,visualchars,nonbreaking,template",
            theme_advanced_toolbar_location: "top",
            theme_advanced_toolbar_align: "left",
            theme_advanced_statusbar_location: "bottom",
            theme_advanced_resizing: true,
            editor_selector: "mceEditorLite",
            entity_encoding: "raw"
        });
    </script>
<div class="page-header">
    <h1><?php echo __('Add new node in Labyrinth ') . '"' . $templateData['map']->name . '"'; ?></h1>
    </div>

    <form class="form-horizontal" id="form1" name="form1" method="post"
          action="<?php echo URL::base() . 'nodeManager/createNode/' . $templateData['map']->id; ?>">

        <fieldset class="fieldset">
            <div class="control-group">
                <label for="mnodetitle" class="control-label"><?php echo __('Title'); ?></label>

                <div class="controls">
                    <input class="span6" type="text" name="mnodetitle" id="mnodetitle"/>
                </div>
            </div>


            <div class="control-group">
                <label for="mnodetext" class="control-label"><?php echo __('Node content'); ?></label>

                <div class="controls">
                    <textarea name="mnodetext"
                              id="mnodetext"  <?php if (isset($templateData['editMode']) && $templateData['editMode'] == 'w') echo 'class="mceEditor"'; ?>></textarea>
                </div>
            </div>
            <div class="control-group">
                <label for="mnodeinfo" class="control-label"><?php echo __('Supporting Information content'); ?></label>

                <div class="controls">
                    <textarea name="mnodeinfo"
                              id="mnodeinfo" <?php if (isset($templateData['editMode']) && $templateData['editMode'] == 'w') echo 'class="mceEditor"'; ?>></textarea>
                </div>
            </div>

            <div class="control-group">
                <label for="show_info"
                       class="control-label"><?php echo __('Show "Supporting Information" button in the bottom of node'); ?></label>

                <div class="controls">
                    <input id="show_info" name="show_info" name="show_info" type="checkbox"/>
                </div>
            </div>

            <div class="control-group">
                <label for="annotation"
                       class="control-label"><?php echo __('Annotation'); ?></label>
                <div class="controls">
                    <textarea class="mceEditorLite" name="annotation" id="annotation"></textarea>
                </div>
            </div>
        </fieldset>
        <fieldset class="fieldset">
            <div class="control-group">
                <label class="control-label"><?php echo __('Exit Node Probability'); ?></label>

                <div class="controls">
                    <label class="radio">
                        <input name="mnodeprobability" type="radio" value="1"><?php echo __('on'); ?>
                    </label>
                </div>
                <div class="controls">
                    <label class="radio">
                        <input name="mnodeprobability" type="radio" value="0"><?php echo __('off'); ?>
                    </label>

                </div>
            </div>

            <div class="control-group">
                <label class="control-label"><?php echo __('Link function style'); ?></label>
                <?php if (isset($templateData['linkStyles'])) { ?>
                    <?php foreach ($templateData['linkStyles'] as $linkStyle) { ?>

                        <div class="controls">
                            <label class="radio">
                                <input type="radio" name="linkstyle"
                                       value="<?php echo $linkStyle->id ?>"><?php echo $linkStyle->name; ?>
                            </label>
                        </div>
                    <?php } ?>
                <?php } ?>

            </div>
            <div class="control-group">
                <label class="control-label"><?php echo __('Node priority'); ?></label>
                <?php if (isset($templateData['priorities'])) { ?>
                    <?php foreach ($templateData['priorities'] as $priority) { ?>
                        <div class="controls">
                            <label class="radio">
                                <input type="radio" name="priority"
                                       value="<?php echo $priority->id ?>"><?php echo $priority->name; ?>
                            </label>
                        </div>
                    <?php } ?>
                <?php } ?>

            </div>
            <div class="control-group">
                <label class="control-label"><?php echo __('Prevent Revisit'); ?></label>

                <div class="controls">
                    <label class="radio">
                        <input name="mnodeUndo" type="radio" value="1"><?php echo __('on'); ?>
                    </label>
                </div>
                <div class="controls">
                    <label class="radio">
                        <input name="mnodeUndo" type="radio" value="0"><?php echo __('off'); ?>
                    </label>

                </div>
            </div>
            <div class="control-group">
                <label class="control-label"><?php echo __('Link to end and report from this node'); ?></label>

                <div class="controls">
                    <label class="radio">
                        <input type="radio" name="ender" value="1"><?php echo __('on'); ?>
                    </label>
                </div>
                <div class="controls">
                    <label class="radio">
                        <input type="radio" name="ender" value="0" checked=""><?php echo __('off'); ?>
                    </label>

                </div>
            </div>
        </fieldset>
        <div class="form-actions">
            <div class="pull-right">
                <input class="btn btn-large btn-primary" type="submit" name="Submit"
                       value="<?php echo __('Add node'); ?>"></div>
        </div>
    </form>


<?php } ?>
