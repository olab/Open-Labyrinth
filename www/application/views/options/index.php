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

/** @var array $templateData */

$curios_video_player_domains_string = implode(',', $templateData['curios_video_player_domains']);
?>

<h1 class="page-header">
    <?php echo __('Settings'); ?>
</h1>

<form class="form-horizontal" action="<?php echo URL::base(true) ?>options/save" method="post">
    <fieldset>
        <div class="control-group">
            <label class="control-label" for="curios_video_player_domains"><?php echo __('CURIOS video player domains'); ?></label>
            <div class="controls">
                <input placeholder="example.com,example2.net" type="text" id="curios_video_player_domains" name="curios_video_player_domains" class="span6" value="<?php echo $curios_video_player_domains_string ?>">
                <span class="help-inline">Separate domains with commas</span>
            </div>
        </div>
        <button type="submit" class="btn btn-primary btn-large pull-right">Save</button>
    </fieldset>
</form>