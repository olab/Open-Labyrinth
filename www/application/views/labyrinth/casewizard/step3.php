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

<h1><?php echo __('Step 3. VP creator'); ?> - <?php if (isset($templateData['map'])) echo $templateData['map']->type->name; ?></h1>

<div class="span6">
    <form class="form-horizontal left" method="post" action="<?php echo URL::base(); ?>labyrinthManager/caseWizard/4<?php
    $action = '';
    if($templateData['map']->type_id == 6) {
        $action = 'createLinear';
    } else if($templateData['map']->type_id == 9) {
        $action = 'createBranched';
    }

    echo $action;
    ?>/<?php echo $templateData['map']->id; ?>">
        <input type="hidden" name="nodesCount" id="formNodeCount" value="0"/>
        <div class="block">
            <div class="block" data-toggle="buttons-radio" id="nodeCountContainer">
                <button type="button" class="btn" value="6">6</button>
                <button type="button" class="btn" value="12">12</button>
                <button type="button" class="btn" value="18">18</button>
                <button type="button" class="btn" value="24">24</button>
                <button type="button" class="btn" value="Custom" id="nodeCountCustom">Custom</button>
                <input type="text" style="width: 40px;" id="nodeCount" disabled/>
                <button class="btn" id="applyCount">Apply</button>
            </div>
        </div>
        <br/>
        <div id="entryPointContainer">
            <legend><?php echo __('Entry Point'); ?></legend>
            <div class="control-group">
                <label for="rootTitle" class="control-label"><?php echo 'Title';?></label>

                <div class="controls">
                    <input type="text" id="rootTitle" value="new node" name="rootTitle"/>
                </div>
            </div>
            <div class="control-group">
                <label for="rootContent" class="control-label"><?php echo 'Content';?></label>

                <div class="controls">
                    <textarea id="rootContent" class="mceEditor" name="rootContent"></textarea>
                </div>
            </div>
        </div>

        <div id="nodesContainer">

        </div>

        <div id="endPointContainer">
            <legend><?php echo __('End Point'); ?></legend>
            <div class="control-group">
                <label for="endTitle" class="control-label"><?php echo 'Title';?></label>

                <div class="controls">
                    <input type="text" id="endTitle" value="new node" name="endTitle"/>
                </div>
            </div>
            <div class="control-group">
                <label for="endContent" class="control-label"><?php echo 'Content';?></label>

                <div class="controls">
                    <textarea id="endContent" class="mceEditor" name="endContent"></textarea>
                </div>
            </div>
        </div>

        <footer>
            <div class="pull-right"><button type="submit" id="step3_submit" class="btn btn-primary wizard_button">Step 4 - Edit Story</button></div>
            <a href="<?php echo URL::base() . 'labyrinthManager/caseWizard/2/' . $templateData['map']->id; ?>" style="float:left;" class="btn btn-primary wizard_button">Return to step 2</a>
        </footer>

    </form>
</div>

<?php if(isset($templateData['map']) && $templateData['map']->type_id == 6) { ?>
<script type="text/javascript" src="<?php echo URL::base(); ?>scripts/linear.js"></script>
<?php } else if(isset($templateData['map']) && $templateData['map']->type_id == 9) { ?>
<script type="text/javascript" src="<?php echo URL::base(); ?>scripts/linear.js"></script>
<?php } ?>

