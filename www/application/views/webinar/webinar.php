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

<h1>
    <?php echo isset($templateData['webinar']) ? 'Edit' : 'Create'; ?> <?php echo __("Scenario"); ?>
</h1>

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

    var $labyrinthContainers = [];
</script>

<form class="form-horizontal" id="webinarForm" name="webinarForm" method="post" action="<?php echo URL::base() ?>webinarmanager/save">
    <input type="hidden" name="webinarId" value="<?php if(isset($templateData['webinar'])) echo $templateData['webinar']->id; ?>"/>
    <fieldset class="fieldset">
        <legend><?php echo __('Scenario Details'); ?></legend>
        <div class="control-group">
            <label class="control-label" for="title"><?php echo __('Scenario Title'); ?></label>
            <div class="controls">
                <input type="text" class="span6" id="title" name="title" value="<?php if(isset($templateData['webinar'])) echo $templateData['webinar']->title; ?>" />
            </div>
        </div>

        <?php if(!isset($templateData['webinar'])) { ?>
        <div class="control-group">
            <label for="firstmessage" class="control-label"><?php echo __('First message'); ?></label>

            <div class="controls">
                <textarea name="firstmessage" id="firstmessage" class="mceEditor"></textarea>
            </div>
        </div>
        <?php } ?>
    </fieldset>

    <div id="steps-container">
        <?php if(isset($templateData['webinar']) && count($templateData['webinar']->steps) > 0) { ?>
            <?php foreach($templateData['webinar']->steps as $step) { ?>
                <fieldset class="fieldset step-container-<?php echo $step->id; ?>" stepId="<?php echo $step->id; ?>">
                    <legend>
                        Step - <input type="text" name="s<?php echo $step->id; ?>_name" value="<?php echo $step->name; ?>"/> <button class="btn btn-danger btn-remove-step"><i class="icon-trash"></i></button>
                        <input type="hidden" name="stepIDs[]" value="<?php echo $step->id; ?>"/>
                    </legend>
                    <div id="labyrinth-container-<?php echo $step->id; ?>" containerId="<?php echo $step->id; ?>">
                        <?php if(count($step->maps) > 0) { ?>
                            <?php $index = 1; foreach($step->maps as $stepMap) { ?>
                        <div class="control-group labyrinth-item-<?php echo $index; ?>" itemNumber="<?php echo $index ?>">
                                    <label for="s<?php echo $step->id; ?>-labyrinth-<?php echo $index; ?>" class="control-label">Labyrinth #<?php echo $index ?></label>
                            <div class="controls">
                                        <select id="s<?php echo $step->id; ?>-labyrinth-<?php echo $index; ?>" name="s<?php echo $step->id; ?>_labyrinths[]" class="span6">
                                    <?php if(isset($templateData['maps']) && count($templateData['maps']) > 0) { ?>
                                        <?php foreach($templateData['maps'] as $m) { ?>
                                                    <option value="<?php echo $m->id; ?>" <?php if($m->id == $stepMap->map_id) echo 'selected="selected"'; ?>><?php echo $m->name; ?></option>
                                        <?php } ?>
                                    <?php } ?>
                                </select>
                                <button class="btn btn-danger remove-map"><i class="icon-trash"></i></button>
                            </div>
                        </div>
                    <?php $index++; } ?>
                <?php } ?>
                    </div>

                    <div>
                        <button class="btn btn-info add-labyrinth-btn" type="button" containerId="<?php echo $step->id; ?>"><i class="icon-plus-sign"></i>Add Labyrinth</button>
                    </div>

                    <script>
                        $labyrinthContainers.push($('#labyrinth-container-<?php echo $step->id; ?>'));
                    </script>
                </fieldset>
                <?php } ?>
            <?php } ?>
        </div>

        <div>
        <button class="btn btn-info add-step-btn" type="button"><i class="icon-plus-sign"></i>Add Step</button>
        </div>

    <h3>Assign the users</h3>
    <table id="assign-users" class="table table-bordered table-striped">
        <colgroup>
            <col style="width: 5%" />
            <col style="width: 5%" />
            <col style="width: 90%" />
        </colgroup>
        <thead>
        <tr>
            <th style="text-align: center">Actions</th>
            <th style="text-align: center">Auth type</th>
            <th><a href="javascript:void(0);">User</a></th>
        </tr>
        </thead>
        <tbody>
        <?php if(isset($templateData['webinar']) and count($templateData['webinar']->users) > 0) { ?>
            <?php foreach($templateData['webinar']->users as $existUser) { ?>
                <tr>
                    <?php if($existUser->user_id == Auth::instance()->get_user()->id) { ?>
                        <td style="text-align: center">Author</td>
                        <input type="hidden" name="users[]" value="<?php echo $existUser->user_id; ?>">
                    <?php } else { ?>
                        <td style="text-align: center"><input type="checkbox" name="users[]" value="<?php echo $existUser->user_id; ?>" checked="checked"></td>
                    <?php } ?>
                    <?php $icon = (isset($templateData['usersMap'][$existUser->user_id]) && $templateData['usersMap'][$existUser->user_id]['icon'] != NULL) ? 'oauth/'.$templateData['usersMap'][$existUser->user_id]['icon'] : 'openlabyrinth-header.png' ; ?>
                    <td style="text-align: center;"> <img <?php echo (isset($templateData['usersMap'][$existUser->user_id]) && $templateData['usersMap'][$existUser->user_id]['icon'] != NULL) ? 'width="32"' : ''; ?> src=" <?php echo URL::base() . 'images/' . $icon ; ?>" border="0"/></td>
                    <td><?php echo $existUser->user->nickname; ?></td>
                </tr>
            <?php } ?>
        <?php } ?>
        <?php if(isset($templateData['users']) and count($templateData['users']) > 0) { ?>
            <?php foreach($templateData['users'] as $user) { ?>
                <?php if($user['id'] == Auth::instance()->get_user()->id) continue; ?>
                <tr>
                    <td style="text-align: center"><input type="checkbox" name="users[]" value="<?php echo $user['id']; ?>"></td>
                    <?php $icon = ($user['icon'] != NULL) ? 'oauth/'.$user['icon'] : 'openlabyrinth-header.png' ; ?>
                    <td style="text-align: center;"> <img <?php echo ($user['icon'] != NULL) ? 'width="32"' : ''; ?> src=" <?php echo URL::base() . 'images/' . $icon ; ?>" border="0"/></td>
                    <td><?php echo $user['nickname']; ?></td>
                </tr>
            <?php } ?>
        <?php } ?>
        </tbody>
    </table>

    <?php if((isset($templateData['groups']) and count($templateData['groups']) > 0) || (isset($templateData['webinar']) and count($templateData['webinar']->groups) > 0)) { ?>
    <h3>Assign the groups</h3>
    <table id="assign-users" class="table table-bordered table-striped">
        <colgroup>
            <col style="width: 5%" />
            <col style="width: 90%" />
        </colgroup>
        <thead>
        <tr>
            <th style="text-align: center">Actions</th>
            <th><a href="javascript:void(0);">Group</a></th>
        </tr>
        </thead>
        <tbody>
        <?php if(isset($templateData['webinar']) and count($templateData['webinar']->groups) > 0) { ?>
            <?php foreach($templateData['webinar']->groups as $existGroup) { ?>
                <tr>
                    <td style="text-align: center"><input type="checkbox" name="groups[]" value="<?php echo $existGroup->group_id; ?>" checked="checked"></td>
                    <td><?php echo $existGroup->group->name; ?></td>
                </tr>
            <?php } ?>
        <?php } ?>
        <?php if(isset($templateData['groups']) and count($templateData['groups']) > 0) { ?>
            <?php foreach($templateData['groups'] as $group) { ?>
                <tr>
                    <td style="text-align: center"><input type="checkbox" name="groups[]" value="<?php echo $group->id; ?>"></td>
                    <td><?php echo $group->name; ?></td>
                </tr>
            <?php } ?>
        <?php } ?>
        </tbody>
    </table>
    <?php } ?>

    <div class="form-actions">
        <div class="map-error-empty" style="float: left;margin-top: 12px;color: red;display: none;">Please select labyrinths for each step</div>
        <div class=" pull-right">
            <input type="submit" class="btn btn-primary btn-large submit-webinar-btn" name="submit" value="<?php echo isset($templateData['webinar']) ? 'Save' : 'Create'; ?> <?php echo __('Scenario'); ?>" />
        </div>
    </div>
</form>

<script>
    var mapsJSON = {<?php if(isset($templateData['maps']) && count($templateData['maps']) > 0) {
        echo 'maps: [';

        $mapsJSON = '';
        foreach($templateData['maps'] as $map) {
            $mapsJSON .= '{id: ' . $map->id . ', name: "' . base64_encode($map->name) . '"}, ';
        }

        if(strlen($mapsJSON) > 2) {
            echo substr($mapsJSON, 0, strlen($mapsJSON) - 2);
        }

        echo ']';
    } ?>};
</script>

<script src="<?php echo ScriptVersions::get(URL::base().'scripts/visualeditor/base64v1_0.js'); ?>"></script>
<script src="<?php echo ScriptVersions::get(URL::base().'scripts/webinar.js'); ?>"></script>