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
$ltiUser = Arr::get($templateData, 'user');
$ltiUserId = $ltiUser ? $ltiUser->id : 0;
$ltiUserEnabled = ($ltiUser AND $ltiUser->enabled) ? 'checked' : '';
$ltiUserRole = $ltiUser ? $ltiUser->role : 0;
$ltiUserName = $ltiUser ? $ltiUser->name : '';
$withoutEndDate = $ltiUser ? $ltiUser->without_end_date : 0;
?>

<script type="text/javascript" src="<?php echo ScriptVersions::get(URL::base().'scripts/jquery-ui-1.9.1.custom.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo ScriptVersions::get(URL::base().'scripts/application.js'); ?>"></script>

<h1><?php echo $ltiUser ? __("Edit user") : __('Add user'); ?></h1>
<form class="form-horizontal left" method="post" action="<?php echo URL::base().'lti/saveUser'; ?>">
    <input type="hidden" name="id" value="<?php echo $ltiUserId; ?>">
    <fieldset>
        <legend>Consumer Details</legend>
        <div class="control-group">
            <label class="control-label"><?php echo __('Active'); ?></label>
            <div class="controls">
                <div class="radio_extended btn-group">
                    <input autocomplete="off" type="radio" id="inactive" name="active" value="0" <?php   if( ! $ltiUserEnabled) echo 'checked'; ?>>
                    <label data-class="btn-danger" data-value="inactive" for="inactive" class="btn">Inactive</label>

                    <input autocomplete="off" type="radio" id="active" name="active" value="1" <?php echo $ltiUserEnabled; ?>>
                    <label data-class="btn-success" data-value="active" for="active" class="btn">Active</label>
                </div>
            </div>
        </div>

        <div class="control-group">
            <label class="control-label"><?php echo __('Permissions'); ?></label>
            <div class="controls">
                <div class="radio_extended btn-group">
                    <input autocomplete="off" type="radio" id="permission1" name="permission" value="4" <?php echo ($ltiUserRole == 4) ? 'checked' : ''; ?>>
                    <label data-class="btn-info" for="permission1" class="btn">Superuser</label>

                    <input autocomplete="off" type="radio" id="permission2" name="permission" value="2" <?php echo ($ltiUserRole == 2) ? 'checked' : ''; ?>>
                    <label data-class="btn-info" for="permission2" class="btn">Author</label>

                    <input autocomplete="off" type="radio" id="permission3" name="permission" value="1" <?php echo ($ltiUserRole == 1) ? 'checked' : ''; ?>>
                    <label data-class="btn-info" for="permission3" class="btn">Learner</label>
                </div>
            </div>
        </div>

        <div class="control-group">
            <label class="control-label" for="name"><?php echo __('Name'); ?></label>
            <div class="controls">
                <input name="name" type="text" id="name" class="span6" value="<?php echo $ltiUserName; ?>">
            </div>
        </div>
        <?php
        if($ltiUser) { ?>
            <div class="control-group">
                <label for="key" class="control-label"><?php echo __('Consumer Key'); ?></label>
                <div class="controls">
                    <input class="span6" type="text" name="key" id="key" readonly="readonly" value="<?php echo $ltiUser->consumer_key; ?>"/>
                </div>
            </div>
            <div class="control-group">
                <label for="secret" class="control-label"><?php echo __('Secret'); ?></label>
                <div class="controls">
                    <input class="span6" type="text" name="secret" id="secret" value="<?php echo $ltiUser->secret; ?>"/>
                </div>
            </div><?php
        }?>

        <div class="control-group">
            <label for="date" class="control-label"><?php echo __('Start date'); ?></label>
            <?php $date = ($ltiUser AND $ltiUser->enable_from != null) ? date_parse($ltiUser->enable_from) : null; ?>
            <div class="controls">
                <input class="datepicker" type="text" name="date" id="date" value="<?php if($date != null && isset($date['year']) && isset($date['month']) && isset($date['day'])) echo $date['month'].'/'.$date['day'].'/'.$date['year']; ?>"/>
            </div>
        </div>

        <div class="control-group">
            <label for="dateEnd" class="control-label"><?php echo __('End date'); ?></label>
            <?php $date = ($ltiUser AND $ltiUser->enable_until != null) ? date_parse($ltiUser->enable_until) : null; ?>
            <div class="controls">
                <input class="datepicker" type="text" name="dateEnd" id="dateEnd" value="<?php if($date != null && isset($date['year']) && isset($date['month']) && isset($date['day'])) echo $date['month'].'/'.$date['day'].'/'.$date['year']; ?>"/>
                <label style="display: inline-block; margin-left: 10px;">
                    <input type="checkbox" value="1" name="without_end_date" <?php if($withoutEndDate) echo 'checked'; ?>>
                    <span style="position: relative; top: 3px;"><?php echo __('without end date'); ?></span>
                </label>
            </div>
        </div>

        <button type="submit" class="btn btn-primary btn-large pull-right">Save changes</button>
    </fieldset>
</form>
