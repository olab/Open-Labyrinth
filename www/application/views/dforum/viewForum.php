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
<script type="text/javascript" src="<?php echo URL::base(); ?>scripts/tinymce/jscripts/tiny_mce/tiny_mce.js"></script>

<script language="javascript" type="text/javascript">
    tinyMCE.init({
        // General options
        mode: "textareas",
        document_base_url : "<?php echo URL::base(true); ?>",
        relative_urls: false,
        convert_urls : false,
        remove_script_host : false,
        theme: "advanced",
        skin: "bootstrap",
        plugins: "autolink,lists,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,wordcount,advlist,imgmap",
        // Theme options
        theme_advanced_buttons1: "newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",
        theme_advanced_buttons2: "undo,redo,|,cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,sub,sup,|,link,unlink,anchor,image,|,forecolor,backcolor,|,code",
        theme_advanced_toolbar_location: "top",
        theme_advanced_toolbar_align: "left",
        theme_advanced_statusbar_location: "bottom",
        theme_advanced_resizing: true,
        editor_selector: "mceEditor"
    });
</script>

<div class="page-header">

    <h1><?php echo __($templateData['forum']['name']); ?></h1></div>

<table class="table table-striped table-bordered" id="message-table">
    <tbody>
    <?php
    if(isset($templateData['forum']) and isset($templateData['forum']['messages']) and count($templateData['forum']['messages']) > 0) {
    foreach($templateData['forum']['messages'] as $message) {
    ?>
        <tr id="m-<?php echo $message['id'] ; ?>" class="message">
            <td width='165px' style="vertical-align: top;">
               <a href="<?php echo URL::base().'usermanager/viewUser/' . $message['author_id']; ?>"><?php echo $message['author_name'] . '</a>'; ?>
               <br />
               <?php echo $message['date'];?>
               <br />
               <br />
               <?php if (Auth::instance()->get_user()->type->name == 'superuser' || Auth::instance()->get_user()->id == $message['author_id']) { ?>
                    <a href="<?php echo URL::base() . 'dforumManager/editMessage/' . $message['id'] . '/' .$templateData['forum']['id'] ; ?>" rel="tooltip" title="Edit message"><i class="icon-edit"></i> <?php echo __('Edit'); ?></a>
                    <a data-toggle="modal" href="javascript:void(0)" data-target="#delete-message-<?php echo $message['id']; ?>" rel="tooltip" title="Delete message"><i class="icon-trash"></i> <?php echo __('Delete'); ?></a>
                    <div class="modal hide alert alert-block alert-error fade in" id="delete-message-<?php echo $message['id']; ?>">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="alert-heading"><?php echo __('Caution! Are you sure?'); ?></h4>
                        </div>
                        <div class="modal-body">
                            <p><?php echo __('You have just clicked the delete button, are you certain that you wish to proceed with deleting this message from Forum?'); ?></p>
                            <p>
                                <a class="btn btn-danger" href="<?php echo URL::base() . 'dforumManager/deleteMessage/' . $message['id'] . '/' .$templateData['forum']['id'] ; ?>"><?php echo __('Delete message'); ?></a> <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
                            </p>
                        </div>
                    </div>
               <?php }?>
               <a href="javascript:void(0);" class="add-quote-btn" msgId="<?php echo $message['id'] ; ?>" msgDate="<?php echo $message['date'];?>" msgAuthor="<?php echo $message['author_name']; ?>" rel="tooltip" title="Add quote"><i class="icon-quote-right"></i> <?php echo __('Quote'); ?></a>
            </td>
            <td id="message-text-<?php echo $message['id'] ; ?>">
                <?php echo $message['text'];?>
            </td>
        </tr>
    <?php }
    } else { ?>
        <tr class="info"><td colspan="4">There are no forum messages right now. You may add a message using the add message button.</td> </tr>
    <?php } ?>
    </tbody>
</table>

<form class="form-horizontal" id="form1" name="form1" method="post"
      action="<?php echo URL::base() . 'dforumManager/addMessage/'; ?>">
    <fieldset class="fieldset">
        <div class="control-group">
            <label for="message" class="control-label"><?php echo __('Message: '); ?></label>

            <div class="controls">
                <textarea name="message" id="message" class="mceEditor"></textarea>
            </div>
        </div>
    </fieldset>

    <div class="form-actions">
        <div class="form-actions">
            <div class="pull-right">
                <input class="btn btn-large btn-primary" type="submit" name="Submit"
                       value="<?php echo __('Add message'); ?>" onclick="return CheckForm();"></div>
        </div>
    </div>
    <input type="hidden" name="forum" id="forum" value="<?php echo $templateData['forum']['id']; ?>" />
    <input type="hidden" id="lastMessageId" value="<?php echo (isset($message['id'])) ? $message['id'] : null; ?>" />
    <input type="hidden" id="url" value="<?php echo URL::base(); ?>" />
</form>


<script>

    function CheckForm()
    {
        if(tinyMCE.get("message").getContent() =='')
        {
            alert('Please enter you message!');
            return false;
        }
    }

</script>

<script src="<?php echo ScriptVersions::get(URL::base().'scripts/dforum.js'); ?>"></script>