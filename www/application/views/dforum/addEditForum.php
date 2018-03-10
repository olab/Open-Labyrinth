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
<script language="javascript" type="text/javascript" src="<?php echo URL::base(); ?>scripts/tinymce/js/tinymce/tinymce.min.js"></script>
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
 /*   tinyMCE.init({
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
    });*/
</script>

<div class="page-header">
    <h1><?php echo (isset($templateData['name'])) ? __('Edit forum') . ' - "' .  $templateData['name'] . '"' : __('Add new forum'); ?></h1>
</div>

<form class="form-horizontal" id="form1" name="form1" method="post"
      action="<?php echo (!isset($templateData['name'])) ? URL::base() . 'dforumManager/saveNewForum/' : URL::base() . 'dforumManager/updateForum/'; ?>">

    <fieldset class="fieldset">
        <div class="control-group">
            <label for="forumname" class="control-label"><?php echo __('Forum name'); ?></label>

            <div class="controls">
                <input class="span6" type="text" name="forumname" id="forumname" value="<?php echo (isset($templateData['name'])) ? $templateData['name'] : ''; ?>" />
            </div>
        </div>

        <?php if (!isset($templateData['name'])){ ?>
        <div class="control-group">
            <label for="firstmessage" class="control-label"><?php echo __('First message'); ?></label>

            <div class="controls">
                <textarea name="firstmessage" id="firstmessage" class="mceEditor"></textarea>
            </div>
        </div>
        <?php } ?>
    </fieldset>
    <fieldset class="fieldset">
        <div class="control-group">
            <label class="control-label"><?php echo __('Security'); ?></label>

            <div class="controls">
                <label class="radio">
                    <input name="security" type="radio" value="0" <?php if (isset($templateData['security'])) { echo ($templateData['security'] == 0) ? 'checked="checked"' : ''; } else { echo 'checked="checked"'; } ?>><?php echo __('Open'); ?>
                </label>
            </div>
            <div class="controls">
                <label class="radio">
                    <input name="security" type="radio" value="1" <?php if (isset($templateData['security'])) { echo ($templateData['security'] == 1) ? 'checked="checked"' : ''; } ?>><?php echo __('Private'); ?>
                </label>

            </div>
        </div>

            <?php if (isset($templateData['status'])) { ?>
                <div class="control-group">
                    <label class="control-label"><?php echo __('Status'); ?></label>

                    <div class=" controls">
                        <?php foreach ($templateData['status'] as $status) { ?>
                            <label class="radio">
                                <input type="radio" name="status" value=<?php echo $status->id; ?>
                                <?php  if (isset($templateData['name'])) { ?>
                                    <?php if ($status->id == $templateData['forum']->status) echo 'checked=""'; ?>/> <?php echo $status->name; ?>
                                <?php } else {?>
                                    <?php if ($status->id == 1) echo 'checked=""'; ?>/> <?php echo $status->name; ?>
                                <?php }?>
                            </label>
                        <?php } ?>
                    </div>

                </div>
            <?php } ?>

        <?php if (!isset($templateData['name'])) { ?>
            <div class="control-group">
                <label class="control-label"><?php echo __('Send notifications'); ?></label>
                <div class=" controls">
                    <input type="checkbox" name="sentNotifications">
                </div>
            </div>
        <?php } ?>

        <?php if(isset($templateData['forum_id'])) { ?>
            <a class="btn btn-info" rel="tooltip" title="Add new topic" href="/dtopicManager/addTopic/<?php if (isset($templateData['name'])) echo $templateData['forum_id'];  ?> ">
                <i class="icon-plus-sign"></i>
                <span class="visible-desktop">Add topic</span>
            </a>
        <?php } ?>

            <h3>Topics</h3>
            <table id="topics" class="table table-striped table-bordered ">
                <colgroup>
                    <col style="width: 30%" />
                    <col style="width: 10%" />
                    <col style="width: 15%" />
                    <col style="width: 15%" />
                    <col style="width: 30%" />
                </colgroup>
                <thead>
                <tr>
                    <th style="text-align: left">Title</th>
                    <th style="text-align: left">Status</th>
                    <th style="text-align: left">Author</th>
                    <th style="text-align: left">Create Date</th>
                    <th style="text-align: left">Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php if(isset($templateData['topics']) && count(($templateData['topics'])) > 0 ) { ?>
                    <?php foreach ($templateData['topics'] as $topic) {?>
                        <tr>
                            <td>
                                <?php echo $topic['name']; ?>
                            </td>
                            <td>
                                <?php echo ($topic['security_id']) ? 'Hidden/' : 'Visibile/' ; echo $topic['status_name'][0]['name'];  ?>
                            </td>
                            <td>
                                <?php echo $topic['author_name']; ?>
                            </td>
                            <td>
                                <?php echo $topic['date']; ?>
                            </td>
                            <td>

                                    <a class="btn btn-small btn-info" href="<?php echo URL::base() ?>dTopicManager/editTopic/<?php echo $templateData['forum_id']; ?>/<?php echo $topic['id']; ?>" rel="tooltip" title="Edit this topic"><i class="icon-edit"></i> <?php echo __('Edit'); ?></a>
                                    <a data-toggle="modal" href="javascript:void(0);" data-target="#delete-topic-<?php echo $topic['id'];  ?>" rel="tooltip" title="Delete this topic" class="btn btn-small btn-danger"><i class="icon-trash"></i> <?php echo __('Delete Topic'); ?></a>

                                    <div class="modal hide alert alert-block alert-error fade in" id="delete-topic-<?php echo $topic['id'];  ?>">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            <h4 class="alert-heading"><?php echo __('Caution! Are you sure?'); ?></h4>
                                        </div>
                                        <div class="modal-body">
                                            <p><?php echo __('You have just clicked the delete button, are you certain that you wish to proceed with deleting this Topic '); ?></p>
                                            <p>
                                                <a class="btn btn-danger" href="<?php echo URL::base() . 'dtopicManager/deleteTopic/' . $templateData['forum_id'] . '/' . $topic['id'] . '/1' ?>"><?php echo __('Delete Topic'); ?></a> <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
                                            </p>
                                        </div>
                                    </div>
                            </td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr class="info">
                        <td colspan="5">There are no topics. You may add one, clicking the button above.</td>
                    </tr>
                <?php }?>
                </tbody>
            </table>

        <h3>Assign the users</h3>
        <table id="users" class="table table-striped table-bordered">
            <colgroup>
                <col style="width: 5%" />
                <col style="width: 5%" />
                <col style="width: 90%" />
            </colgroup>
            <thead>
                <tr>
                    <th style="text-align: center">Actions</th>
                    <th style="text-align: center">Auth type</th>
                    <th style="text-align: center">Experts</th>
                    <th><a href="javascript:void(0);">User<div class="pull-right"><i class="icon-chevron-down icon-white"></i></div></a></th>
                </tr>
            </thead>
            <tbody><?php
            foreach(Arr::get($templateData, 'existUsers', array()) as $existUser) { ?>
                <tr>
                    <td style="text-align: center"><input type="checkbox" name="users[]" value="<?php echo $existUser['id']; ?>" checked="checked"></td>
                    <?php $icon = ($existUser['icon'] != NULL) ? 'oauth/'.$existUser['icon'] : 'openlabyrinth-header.png' ; ?>
                    <td style="text-align: center;"> <img <?php echo ($existUser['icon'] != NULL) ? 'width="32"' : ''; ?> src=" <?php echo URL::base().'images/'.$icon ; ?>" border="0"/></td>
                    <td><?php echo $existUser['nickname']; ?></td>
                </tr><?php
            }
            foreach(Arr::get($templateData, 'users', array()) as $user) { ?>
                <tr><?php
                if( ! isset($templateData['name']) && $user['id'] == Auth::instance()->get_user()->id) continue; ?>
                    <td style="text-align: center"><input type="checkbox" name="users[]" value="<?php echo $user['id']; ?>"></td>
                    <?php $icon = ($user['icon'] != NULL) ? 'oauth/'.$user['icon'] : 'openlabyrinth-header.png' ; ?>
                    <td style="text-align: center;"> <img <?php echo ($user['icon'] != NULL) ? 'width="32"' : ''; ?> src=" <?php echo URL::base() . 'images/' . $icon ; ?>" border="0"/></td>
                    <td><?php echo $user['nickname']; ?></td>
                </tr><?php
            } ?>
            </tbody>
        </table>

        <h3>Assign the groups</h3>
        <table id="groups" class="table table-striped table-bordered">
            <colgroup>
                <col style="width: 5%" />
                <col style="width: 90%" />
            </colgroup>
            <thead>
            <tr>
                <th style="text-align: center">Actions</th>
                <th>
                    <a href="javascript:void(0);">
                        Group <div class="pull-right"><i class="icon-chevron-down icon-white"></i></div>
                    </a>
                </th>
            </tr>
            </thead>
            <tbody>
            <?php if(isset($templateData['existGroups']) and count($templateData['existGroups']) > 0) { ?>
                <?php foreach($templateData['existGroups'] as $existGroup) { ?>
                    <tr>
                        <td style="text-align: center"><input type="checkbox" name="groups[]" value="<?php echo $existGroup['id']; ?>" checked="checked"></td>
                        <td><?php echo $existGroup['name']; ?></td>
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
    </fieldset>
    <div class="form-actions">
        <div class="pull-right">
            <input class="btn btn-large btn-primary" type="submit" name="Submit"
                   value="<?php echo (isset($templateData['name'])) ? 'Edit forum' : 'Add forum'; ?>" onclick="return CheckForm();"></div>
        </div>
    </div>
    <?php if (isset($templateData['name'])) { ?>
    <input type="hidden" value="<?php echo $templateData['forum_id'] ?>" name="forum_id" id="forum_id" >
    <?php } ?>
</form>

<script>

    function CheckForm()
    {
        if(document.getElementById('forumname').value == '')
        {
            alert('Please enter you forum name');
            return false;
        }
        <?php if (!isset($templateData['name'])){ ?>
        if(tinyMCE.get("firstmessage").getContent() =='')
        {
            alert('Please enter you first message');
            return false;
        }
        <?php } ?>
    }

</script>
