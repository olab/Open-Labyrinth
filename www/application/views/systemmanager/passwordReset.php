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
<form class="form-horizontal" action="<?php echo URL::base() . 'systemManager/updatePasswordResetSettings/'; ?>" method="post">
    <input type="hidden" name="token" value="<?php echo $templateData['token']; ?>" />

    <fieldset class="fieldset">
        <legend><?php echo __('Password Recovery Settings'); ?></legend>
        <div class="control-group">
            <label class="control-label" for="fromname"><?php echo __('From E-Mail Address'); ?></label>
            <div class="controls">
                <input type="text" class="span3" id="fromname" name="fromname" value="<?php echo $templateData['email_config']['fromname']; ?>" placeholder="From Name" />
                &lt; <input type="text" class="span5" id="mailfrom" name="mailfrom" value="<?php echo $templateData['email_config']['mailfrom']; ?>" placeholder="email@address.org"/> &gt;
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="email_password_reset_subject"><?php echo __('Reset E-Mail Subject'); ?></label>
            <div class="controls">
                <input type="text" class="span8" id="email_password_reset_subject" name="email_password_reset_subject" value="<?php echo $templateData['email_config']['email_password_reset_subject']; ?>" />
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="email_password_reset_body"><?php echo __('Reset E-Mail Body'); ?></label>
            <div class="controls">
                <textarea class="span8" rows="10" id="email_password_reset_body" name="email_password_reset_body"><?php echo $templateData['email_config']['email_password_reset_body']; ?></textarea>
                <span class="help-block">
                    <small>
                        <span class="label label-info">Available Tags:</span>
                        <a href="#" rel="tooltip" title="<?php echo __('Tag that inserts name into email body.'); ?>"><?php echo __('&lt;%name%&gt;'); ?></a>,
                        <a href="#" rel="tooltip" title="<?php echo __('Tag that inserts user name into email body.'); ?>"><?php echo __('&lt;%username%&gt;'); ?></a>,
                        <a href="#" rel="tooltip" title="<?php echo __('Tag that inserts link to new password page into email body.'); ?>"><?php echo __('&lt;%link%&gt;'); ?></a>
                    </small>
                </span>
            </div>
        </div>
        <input type="submit" class="btn btn-primary" value="<?php echo __('Update Settings'); ?>" />
    </fieldset>
</form>