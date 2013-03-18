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
            src="<?php echo URL::base(); ?>scripts/tinymce/jscripts/tiny_mce/tiny_mce.js"
            xmlns="http://www.w3.org/1999/html"></script>
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

<h1><?php echo __('Step 3. VP creator'); ?> - <?php if (isset($templateData['map'])) echo $templateData['map']->type->name; ?></h1>

<div class="span6">
    <form class="form-horizontal left" method="post" action="<?php echo URL::base(); ?>labyrinthManager/caseWizard/3/<?php
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
                    <input type="text" id="endTitle" name="endTitle"/>
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

