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
<div class="page-header">
    <h1><?php echo __('Edit forum') . ' - "' .  $templateData['name'] . '"'; ?></h1>
</div>

<form class="form-horizontal" id="form1" name="form1" method="post"
      action="<?php echo URL::base() . 'dforumManager/updateForum/'; ?>">

    <fieldset class="fieldset">
        <div class="control-group">

            <label for="forumname" class="control-label"><?php echo __('Forum name'); ?></label>

            <div class="controls">
                <input class="span6" type="text" name="forumname" id="forumname" value="<?php echo $templateData['name']; ?>"/>
            </div>

        </div>
    </fieldset>
    <fieldset class="fieldset">
        <div class="control-group">
            <label class="control-label"><?php echo __('Security'); ?></label>

            <div class="controls">
                <label class="radio">
                    <input name="security" type="radio" value="0" <?php if ($templateData['security'] == 0) echo 'checked="checked"'; ?> ><?php echo __('Open'); ?>
                </label>
            </div>
            <div class="controls">
                <label class="radio">
                    <input name="security" type="radio" value="1" <?php if ($templateData['security'] == 1) echo 'checked="checked"'; ?>><?php echo __('Private'); ?>
                </label>

            </div>

            <input type="hidden" value="<?php echo $templateData['forum_id'] ?>" name="forum_id" id="forum_id" >
        </div>

    </fieldset>
    <div class="form-actions">
        <div class="pull-right">
            <input class="btn btn-large btn-primary" type="submit" name="Submit"
                   value="<?php echo __('Edit forum'); ?>" onclick="return CheckForm();"></div>
    </div>
</form>

<script>

    function CheckForm()
    {
        if(document.getElementById('forumname').value == '')
        {
            alert('Please enter you forum name');
            return false;
        }
    }

</script>