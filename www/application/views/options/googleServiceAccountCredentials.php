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

/** @var string $googleServiceAccountCredentials */
?>

<h2 class="page-header">
    <?php echo __('Settings'); ?>
</h2>

<form class="form-horizontal" action="<?php echo URL::base(true) ?>options/saveAll" method="post">
    <fieldset>
        <div class="control-group">
            <label for="google_service_account_credentials" class="control-label">
                <?php echo __('Google service account credentials'); ?>
            </label>
            <div class="controls">
        <textarea
            style="width:700px;height:500px;"
            id="google_service_account_credentials"
            name="google_service_account_credentials"><?php echo $googleServiceAccountCredentials ?></textarea>
            </div>
        </div>
        <button type="submit" class="btn btn-primary btn-large pull-right">Save</button>
    </fieldset>
</form>