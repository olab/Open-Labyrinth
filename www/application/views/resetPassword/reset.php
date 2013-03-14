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
<h1><?php echo __('Automated password recovery'); ?></h1>

<form class="form-horizontal" action="<?php echo URL::base(); ?>home/updateResetPassword" method="post">
    <fieldset class="fieldset">
        <legend>Enter your new password</legend>
        <div class="control-group">
            <label for="newpswd" class="control-label"><?php echo __('New password'); ?>:</label>
            <div class="controls">
                <input type="password" id="newpswd" name="newpswd" size="30" />
            </div>
        </div>
        <div class="control-group">
            <label for="pswd_confirm" class="control-label"><?php echo __('Confirm new password'); ?>:</label>
            <div class="controls">
                <input type="password" id="pswd_confirm" name="pswd_confirm" size="30" />
            </div>
        </div>
    </fieldset>
    <input type="submit" class="btn btn-primary" value="Submit"/>
    <input type="hidden" name="token" value="<?php echo Security::token(); ?>" />
    <input type="hidden" name="hashKey" value="<?php echo $templateData['hashKey']; ?>" />
</form>