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
<form class="form-horizontal" name="support_form" action="<?php echo URL::base() . 'systemManager/updateSupportEmails'; ?>" method="post">
    <input type="hidden" name="token" value="<?php echo $templateData['token']; ?>" />
    <fieldset class="fieldset">
        <legend><?php echo __("Emails"); ?></legend>
        <div class="control-group">
            <label class="control-label" for="short_text"><?php echo __('Support email'); ?></label>
            <div class="controls">
                <input type="text" name="supportEmail" value="<?php echo $templateData['support']['support']['main_support_email']; ?>"/>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="short_text"><?php echo __('Emails'); ?></label>
            <div class="controls">
                <textarea style="width:680px; height: 150px;" id="emails" name="emails"><?php echo $templateData['support']['support']['email']; ?></textarea>
                <span class="help-block">
                    <small>
                        <span class="label label-info">Help:</span>
                        <a href="#" rel="tooltip" title="<?php echo __('Please enter your emails separated by commas.'); ?>"><?php echo __('Please enter your emails separated by commas'); ?>:</a>
                        <a href="#" rel="tooltip" title="<?php echo __('support@support.su'); ?>"><?php echo __('support@support.su'); ?></a>,
                        <a href="#" rel="tooltip" title="<?php echo __('support2@support.su'); ?>"><?php echo __('support2@support.su'); ?></a>
                    </small>
                </span>
            </div>
        </div>
        <input type="submit" class="btn btn-primary" value="<?php echo __('Update Settings'); ?>" />
    </fieldset>
</form>