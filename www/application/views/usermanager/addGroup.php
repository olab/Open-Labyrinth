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


<form class="form-horizontal" action="<?php echo URL::base() . 'usermanager/saveNewGroup'; ?>" method="post">
<fieldset class="fieldset">
    <legend><?php echo __('Create group'); ?></legend>
    <div class="control-group">
        <label for="groupname" class="control-label"><?php echo __('group name'); ?></label>

        <div class="controls">
            <input id="groupname" class="not-autocomplete" type="text" name="groupname" value="">
        </div>
    </div>
</fieldset>

<div class="form-actions">
    <div class="pull-right">
<input class="btn btn-primary btn-large" type="submit" name="AddGroupSubmit" value="<?php echo __('Submit'); ?>">
    </div>
</div>
</form>
