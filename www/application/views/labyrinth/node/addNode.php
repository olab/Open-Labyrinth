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
    <<script language="javascript" type="text/javascript"
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
