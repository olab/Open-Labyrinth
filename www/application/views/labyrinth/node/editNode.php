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
if (isset($templateData['map']) and isset($templateData['node'])) {
    ?>
    <script language="javascript" type="text/javascript"
            src="<?php echo URL::base(); ?>scripts/tinymce/js/tinymce/tinymce.min.js"></script>
    <script language="javascript" type="text/javascript">
        tinymce.init({
            selector: ".mceEditor",
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

        tinymce.init({
            selector: ".mceEditorLite",
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
        <div class="pull-right">
            <a class="btn btn-primary" href="<?php echo URL::base() . 'nodeManager/setRootNode/' . $templateData['map']->id . '/' . $templateData['node']->id; ?>">
                <i class="icon-sitemap"></i>
                <?php echo __('Set as Root'); ?></a>
        </div>

        <h1><?php echo __('Edit "') . $templateData['node']->title . __('" in Labyrinth ') . '"' . $templateData['map']->name . '"'; ?></h1>

        <?php if($templateData['map']->assign_forum_id != null) { ?>
            <div class="pull-right" style="margin-top: 20px;">
                <?php if($templateData['node']->notes != null && count($templateData['node']->notes) == 1) { ?>
                    <a class="btn" target="_blank" href="<?php echo URL::base(); ?>dtopicManager/viewTopic/<?php echo $templateData['node']->notes[0]->id; ?>"><?php echo __('Edit note'); ?></a>
                <?php } else { ?>
                    <a class="btn" target="_blank" href="<?php echo URL::base(); ?>nodeManager/addNodeNote/<?php echo $templateData['node']->id; ?>"><?php echo __('Add note'); ?></a>
                <?php } ?>
            </div>
        <?php } else { ?>
            <div class="pull-right" style="margin-top: 20px;">
                <a class="btn" href="javascript:void(0)" data-toggle="modal" data-target="#assign-forum-modal"><?php echo __('Add note'); ?></a>
                <div class="modal block hide" id="assign-forum-modal">
                    <div class="modal-header block">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h3>Assign forum</h3>
                    </div>

                    <div class="modal-body block">
                        <p>Please assign forum for this labyrinth in "Details" menu.</p>
                    </div>

                    <div class="modal-footer block">
                        <a href="<?php echo URL::base(); ?>labyrinthManager/global/<?php echo $templateData['map']->id; ?>" class="btn">Assign</a>
                        <a href="javascript:void(0);" class="btn" data-dismiss="modal">Close</a>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>

    <form id="form1" name="form1" method="post" class="form-horizontal"
          action="<?php echo URL::base() . 'nodeManager/updateNode/' . $templateData['node']->id; ?>">
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

            <div class="control-group">
                <label for="mnodekeyword"
                       class="control-label"><?php echo __('Supporting Information Keyword'); ?></label>

                <div class="controls">
                    <input readonly="readonly" id="mnodekeyword" class="span6 code" type="text"
                           value="[[INFO:<?php echo $templateData['node']->id; ?>]]"/>
                </div>
            </div>

            <div class="control-group">
                <label for="mnodekeyword"
                       class="control-label"><?php echo __('Show "Supporting Information" button in the bottom of node'); ?></label>

                <div class="controls">
                    <input id="show_info" name="show_info" type="checkbox" <?php if($templateData['node']->show_info == 1) echo 'checked="checked"'; ?>/>
                </div>
            </div>

            <div class="control-group">
                <label for="annotation"
                       class="control-label"><?php echo __('Annotation'); ?></label>
                <div class="controls">
                    <textarea class="mceEditorLite" name="annotation" id="annotation"><?php echo $templateData['node']->annotation; ?></textarea>
                </div>
            </div>
        </fieldset>

        <fieldset class="fieldset">
            <legend>Counters</legend>
            <?php if (isset($templateData['counters']) and count($templateData['counters']) > 0) { ?>
                <?php foreach ($templateData['counters'] as $counter) { ?>
                    <?php echo __('counter function for'); ?> "<a href="<?php  echo URL::base() . 'counterManager/editCounter/' . $templateData['map']->id.'/'.$counter->id;?>"><?php echo $counter->name; ?></a>"
                    <div class="control-group">
                        <label for="cfunc_<?php echo $counter->id; ?>"
                               class="control-label"><?php echo __('Counter Function'); ?></label>

                        <div class="controls">
                            <input type="text" id="cfunc_<?php echo $counter->id; ?>"
                                   name="cfunc_<?php echo $counter->id; ?>"
                                   value="<?php $c = $templateData['node']->getCounter($counter->id); if ($c != NULL) echo $c->function; ?>">
                            <span>type +, - or = an integer - e.g. '+1' or '=32'</span>
                        </div>
                    </div>

                    <div class="control-group">
                        <label for="cfunc_ch_<?php echo $counter->id; ?>"
                               class="control-label"><?php echo __('Appear on node'); ?></label>

                        <div class="controls">
                            <input type="checkbox" value="1"
                                   id="cfunc_ch_<?php echo $counter->id; ?>"
                                   name="cfunc_ch_<?php echo $counter->id; ?>" <?php if ($c != NULL) {
                                if ($c->display == 1) echo 'checked="checked"';
                            } else {
                                echo 'checked="checked"';
                            } ?> />

                        </div>
                    </div>
                <?php } ?>
            <?php } ?>
            <div class="form-actions">

                <a class="btn btn-info" href="<?php  echo URL::base() . 'counterManager/index/' . $templateData['map']->id;?>">
                    <i class="icon-dashboard"></i>
                    <?php echo __("Manage"); ?></a>
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
                <label class="control-label"><?php echo __('Node Conditional'); ?></label>

                <div class="controls">
                    <?php echo $templateData['node']->conditional; ?>
                    <?php echo $templateData['node']->conditional_message; ?>&nbsp;<i></i>
                    <a class="btn btn-info"
                       href="<?php echo URL::base() . 'nodeManager/editConditional/' . $templateData['node']->id; ?>"><?php echo __('Edit'); ?></a>
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
        <?php
        echo Helper_Controller_Metadata::displayEditor($templateData["node"],"map_node");?>
        <div class="form-actions">
            <div class="pull-right">
            <input class="btn btn-large btn-primary" type="submit" name="Submit"
                   value="<?php echo __('Save changes'); ?>"></div></div>
    </form>

<?php } ?>