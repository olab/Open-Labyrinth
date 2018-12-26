<?php
// TODO: merge with addUser view.
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
if(isset($templateData['user'])) {
    $user = $templateData['user']; ?>
<script language="javascript" type="text/javascript" src="<?php echo URL::base(); ?>scripts/tinymce/js/tinymce/tinymce.min.js"></script>
<div class="page-header">
    <div class="pull-right"><?php
        if (Auth::instance()->get_user()->type->name != 'Director') {?>
        <a class="btn btn-danger" href="<?php echo URL::base().'usermanager/deleteUser/'.$templateData['user']->id; ?>">
            <i class="icon-trash"></i><?php echo __('Delete User'); ?>
        </a><?php
        }?>
    </div>
    <h1><?php echo __('Edit')." ".__('user account'); ?></h1>
</div>

    <?php if ($templateData['errorMsg'] != NULL){ echo '<div class="alert alert-danger">' . $templateData['errorMsg'] . '</div>'; } ?>

<form class="form-horizontal" action="<?php echo URL::base().'usermanager/saveOldUser/'.$templateData['user']->id; ?>" method="post">
    <fieldset class="fieldset">
        <legend><?php echo __("User Details");?></legend>
        <div class="control-group">
            <label class="control-label"><?php echo __('Username'); ?></label>
            <div class="controls" style="margin-top: 5px;"><?php echo $templateData['user']->username; ?></div>
        </div>
        <div class="control-group">
            <label for="upw" class="control-label"><?php echo __('New password'); ?></label>
            <div class="controls">
                <input id="upw"  type="password" name="upw" value="">
                <span class="help-inline">password will be changed if value is not empty</span>
            </div>
        </div>
        <div class="control-group">
            <label for="uname" class="control-label"><?php echo __('Name'); ?></label>
            <div class="controls">
                <input id="uname" class="not-autocomplete" type="text" name="uname"  value="<?php echo $templateData['user']->nickname; ?>">
            </div>
        </div>
        <div class="control-group">
            <label for="uemail" class="control-label"><?php echo __('e-mail'); ?></label>
            <div class="controls">
                <input class="not-autocomplete" id="uemail" type="text" name="uemail" value="<?php echo $templateData['user']->email; ?>">
            </div>
        </div>
        <div class="control-group">
            <label class="control-label"><?php echo __('Language'); ?></label>
            <div class="controls">
                <?php foreach($templateData['languages'] as $language) { ?>
                    <label class="radio">
                        <?php echo $language->name ?>
                        <input <?php if ($language->id == $templateData['user']->language_id){ ?>checked<?php } ?> type="radio" name="langID" value="<?php echo $language->id ?> ">
                    </label>
                <?php } ?>
            </div>
        </div>
        <div class="control-group">
            <label for="usertype" class="control-label"><?php echo __('User type'); ?></label>
            <div class="controls"><?php
                if (Auth::instance()->get_user()->type->name == 'superuser' ) { ?>
                <select id="usertype" name="usertype"><?php
                    foreach(Arr::get($templateData, 'types',array()) as $type) { ?>
                        <option value="<?php echo $type->id; ?>" <?php if($type->id == $templateData['user']->type_id) echo 'selected'; ?>><?php echo $type->name; ?></option><?php
                    } ?>
                </select><?php
                }
                else { ?>
                <div style="margin-top: 5px; text-transform: capitalize; "><?php echo Arr::get($templateData, 'userType'); ?></div><?php
                } ?>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label"><?php echo __('UI mode:'); ?></label>
            <div class="controls">
                <div class="helpPopupLine">
                    <div class="radio_extended btn-group">
                        <a href="<?php echo URL::base().'base/ui/easy/'.$user->id; ?>" data-class="btn-info" class="btn <?php if ($user->modeUI == 'easy') echo 'active btn-info'; ?>">Easy</a>
                        <a href="<?php echo URL::base().'base/ui/advanced/'.$user->id; ?>" data-class="btn-info" class="btn <?php if ($user->modeUI == 'advanced') echo 'active btn-info'; ?>">Advanced</a>
                    </div>
                </div>
            </div>
        </div>
    </fieldset>

    <?php echo Helper_Controller_Metadata::displayEditor($templateData["user"],"user");  ?>

    <div class="form-actions">
        <div class="pull-right">
            <input class="btn btn-primary btn-large" type="submit" name="EditUserSubmit" value="<?php echo __('Save changes'); ?>">
        </div>
    </div>
</form><?php
} ?>
