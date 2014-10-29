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
<script type="text/javascript" src="<?php echo ScriptVersions::get(URL::base().'scripts/tinymce/js/tinymce/tinymce.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo ScriptVersions::get(URL::base().'scripts/tinyMceInit.js'); ?>"></script>
<script type="text/javascript">
    var readOnly = <?php echo (isset($templateData['historyShowWarningPopup']) && ($templateData['historyShowWarningPopup'])) ? 1 : 0; ?>;
    tinyMceInit('textarea', 0);
</script>

<div class="page-header">

    <h1><?php echo __('Edit Message'); ?></h1></div>

<form class="form-horizontal" id="form1" name="form1" method="post"
      action="<?php echo URL::base() . 'dforumManager/updateMessage/'; ?>">

<fieldset class="fieldset">

    <div class="control-group">
        <label for="message" class="control-label"><?php echo __('Edit Message: '); ?></label>

        <div class="controls">
            <textarea name="message" id="message" class="mceEditor"><?php echo $templateData['message']; ?></textarea>
        </div>
    </div>
</fieldset>

    <div class="form-actions">
        <div class="pull-right">
            <input class="btn btn-large btn-primary" type="submit" name="Submit"
                   value="<?php echo __('Edit'); ?>" onclick="return CheckForm();"></div>
    </div>
    <input type="hidden" name="forum" id="forum" value="<?php echo $templateData['forum_id']; ?>">
    <input type="hidden" name="message_id" id="message_id" value="<?php echo $templateData['message_id']; ?>">

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