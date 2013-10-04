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
if (isset($templateData['map']) and isset($templateData['node'])) { ?>
<script language="javascript" type="text/javascript" src="<?php echo URL::base(); ?>scripts/tinymce/jscripts/tiny_mce/tiny_mce.js"></script>
<script language="javascript" type="text/javascript">
    tinyMCE.init({
        // General options
        mode: "textareas",
        relative_urls : false,
        entity_encoding: "raw",
        theme: "advanced",
        plugins: "autolink,lists,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,wordcount,advlist,autosave,imgmap",
        // Theme options
        theme_advanced_buttons1: "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull",
        theme_advanced_buttons2: "styleselect,formatselect,fontselect,fontsizeselect",
        theme_advanced_buttons3: "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo",
        theme_advanced_buttons4: "link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
        theme_advanced_buttons5: "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup",
        theme_advanced_buttons6: "charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
        theme_advanced_buttons7: "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak,restoredraft,|,imgmap",
        theme_advanced_toolbar_location: "top",
        theme_advanced_toolbar_align: "left",
        theme_advanced_statusbar_location: "bottom",
        theme_advanced_resizing: true,
        editor_selector: "mceEditor"
    });
</script>

                <form id="form1" class="form-horizontal" name="form1" method="post" action="<?php echo URL::base() . 'labyrinthManager/caseWizard/4/updateNode/'. $templateData['node']->id; ?>">
                <fieldset class="fieldset">
                    <legend>Node Content</legend>

                    <div class="control-group">
                        <label for="mnodetitle" class="control-label"><?php echo __('Title'); ?></label>

                        <div class="controls">
                            <input type="text" id="mnodetitle" name="mnodetitle" class="span6"
                                   value="<?php echo $templateData['node']->title; ?>">
                        </div>
                    </div>

                    <div class="control-group">
                        <label for="mnodetext" class="control-label"><?php echo __('Node Content'); ?></label>

                        <div class="controls">
                            <textarea name="mnodetext" cols='60' id="mnodetext"
                                      rows='10' <?php if (isset($templateData['editMode']) && $templateData['editMode'] == 'w') echo 'class="mceEditor"'; ?>><?php echo $templateData['node']->text; ?></textarea>
                        </div>
                    </div>

                    <div class="control-group">
                        <label for="mnodeinfo" class="control-label"><?php echo __('Supporting Information'); ?></label>

                        <div class="controls">
                            <textarea name="mnodeinfo" cols='60' id="mnodeinfo"
                                      rows='10' <?php if (isset($templateData['editMode']) && $templateData['editMode'] == 'w') echo 'class="mceEditor"'; ?>><?php echo $templateData['node']->info; ?></textarea>
                        </div>
                    </div>


                </fieldset>




                <fieldset class="fieldset">
                    <legend>Node Settings</legend>
                    <div class="control-group">
                        <label class="control-label"> <?php echo __('Exit Node Probability'); ?></label>

                        <div class="controls">
                            <label class="radio">
                                <?php echo __('On'); ?><input name="mnodeprobability" type="radio"
                                                              value="1" <?php if ($templateData['node']->probability) echo 'checked=""'; ?>>
                            </label>

                            <label class="radio">
                                <?php echo __('Off'); ?>
                                <input name="mnodeprobability" type="radio"
                                       value="0" <?php if (!$templateData['node']->probability) echo 'checked=""'; ?>>
                            </label>
                        </div>
                    </div>





                    <div class="control-group">
                        <label class="control-label"><?php echo __('Link Function Style'); ?></label>

                        <div class="controls">
                            <?php if (isset($templateData['linkStyles'])) { ?>
                                <?php foreach ($templateData['linkStyles'] as $linkStyle) { ?>
                                    <label class="radio">
                                        <input type="radio" name="linkstyle"
                                               value="<?php echo $linkStyle->id ?>" <?php if ($linkStyle->id == $templateData['node']->link_style_id) echo 'checked=""'; ?>><?php echo __($linkStyle->name); ?>
                                    </label>
                                <?php } ?>
                            <?php } ?>
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label"><?php echo __('Node Priorities'); ?></label>

                        <div class="controls">
                            <?php if (isset($templateData['priorities'])) { ?>
                                <?php foreach ($templateData['priorities'] as $priority) { ?>
                                    <label class="radio">
                                        <input type="radio" name="priority"
                                               value="<?php echo $priority->id ?>" <?php if ($priority->id == $templateData['node']->priority_id) echo 'checked=""'; ?>><?php echo $priority->name; ?>
                                    </label>
                                <?php } ?>
                            <?php } ?>
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label"><?php echo __('Prevent Revisit'); ?></label>

                        <div class="controls">
                            <label class="radio">
                                <span><?php echo __('Enabled'); ?></span>
                                <input name="mnodeUndo" type="radio"
                                       value="1" <?php if ($templateData['node']->undo) echo 'checked=""'; ?>></label>
                            <label class="radio">
                                <span><?php echo __('Disabled'); ?></span>
                                <input name="mnodeUndo" type="radio"
                                       value="0" <?php if (!$templateData['node']->undo) echo 'checked=""'; ?>>
                            </label>
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label"><?php echo __('Link to end and report from this node'); ?></label>

                        <div class="controls">
                            <label class="radio">
                        <span><?php echo __('Off'); ?>
                            (<?php echo __('default'); ?>)</span>
                                <input type="radio" name="ender"
                                       value="0" <?php if (!$templateData['node']->end) echo 'checked=""'; ?>></label>

                            <label class="radio">
                                <span><?php echo __('On'); ?></span>
                                <input type="radio" name="ender"
                                       value="1" <?php if ($templateData['node']->end) echo 'checked=""'; ?>>
                            </label>
                        </div>
                    </div>


                </fieldset>



                <div class="pull-right">
                    <input class="btn btn-large btn-primary" type="submit" name="Submit"
                           value="<?php echo __('Save changes'); ?>"></div>
                </form>

<?php } ?>