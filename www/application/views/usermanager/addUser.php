<?php
// TODO: merge with editUser view.
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
 */ ?>
<div class="page-header">
    <h1>Add new user</h1>
</div>
<form class="form-horizontal" action="<?php echo URL::base().'usermanager/saveNewUser'; ?>" method="post">
    <fieldset class="fieldset">
        <legend>User details</legend>
        <div class="control-group">
            <label for="uid" class="control-label"><?php echo __('User name'); ?></label>
            <div class="controls">
                <input id="uid"  type="text" name="uid" value="">
            </div>
        </div>
        <div class="control-group">
            <label for="upw" class="control-label"><?php echo __('Password'); ?></label>
            <div class="controls">
                <input id="upw"  type="password" name="upw" value="">
            </div>
        </div>
        <div class="control-group">
            <label for="uname" class="control-label"><?php echo __('Full name'); ?></label>
            <div class="controls">
                <input id="uname" class="not-autocomplete" type="text" name="uname" >
            </div>
        </div>
        <div class="control-group">
            <label for="uemail" class="control-label"><?php echo __('e-mail'); ?></label>
            <div class="controls">
                <input class="not-autocomplete" id="uemail" type="text" name="uemail">
            </div>
        </div>
        <div class="control-group">
            <label class="control-label"><?php echo __('language'); ?></label>
            <div class="controls">
                <label class="radio">
                    english   <input checked="checked" type="radio" name="langID" value="1" >
                </label>
                <label class="radio">
                    francais <input type="radio" name="langID" value="2" >
                </label>
            </div>
        </div>
        <div class="control-group">
            <label for="usertype" class="control-label"><?php echo __('user type'); ?></label>
            <div class="controls">
                <select id="usertype" name="usertype"><?php
                    if(isset($templateData['types'])) {
                        foreach($templateData['types'] as $type) { ?>
                            <option value="<?php echo $type->id; ?>"><?php echo $type->name; ?></option><?php
                        }
                    } ?>
                </select>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label"><?php echo __('Send information about user account by email'); ?></label>
            <div class="controls">
                <input id="sendEmail" checked="checked" type="checkbox" name="sendEmail" >
            </div>
        </div>
        <div class="control-group">
            <label class="control-label"><?php echo __('UI mode:'); ?></label>
            <div class="controls">
                <div class="btn-group">
                    <div class="radio_extended btn-group">
                        <input autocomplete="off" id="uiModeEasy" type="radio" value="easy" name="uiMode" checked>
                        <label data-class="btn-info" for="uiModeEasy" class="btn active btn-info">Easy</label>

                        <input autocomplete="off" id="uiModeAdvanced" type="radio" value="advanced" name="uiMode">
                        <label data-class="btn-info" for="uiModeAdvanced" class="btn">Advanced</label>
                    </div>
                </div>
            </div>
        </div>
    </fieldset><?php
    if ($templateData['errorMsg'] != NULL){ echo $templateData['errorMsg']; } ?>

    <div class="form-actions">
        <div class="pull-right">
            <input class="btn btn-large btn-primary" type="submit" name="SaveNewUserSubmit" value="<?php echo __('Save'); ?>">
        </div>
    </div>
</form>
