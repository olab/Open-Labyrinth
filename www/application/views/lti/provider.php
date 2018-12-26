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
$provider = Arr::get($templateData, 'provider');
$providerId = !empty($provider) ? $provider->id : 0;
$providerName = !empty($provider) ? $provider->name : '';
$consumerKey = !empty($provider) ? $provider->consumer_key : '';
$secret = !empty($provider) ? $provider->secret : '';
$launch_url = !empty($provider) ? $provider->launch_url : '';
?>

<script type="text/javascript" src="<?php echo ScriptVersions::get(URL::base().'scripts/jquery-ui-1.9.1.custom.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo ScriptVersions::get(URL::base().'scripts/application.js'); ?>"></script>

<h1><?php echo $provider ? __("Edit provider") : __('Add provider'); ?></h1>
<form class="form-horizontal left" method="post" action="<?php echo URL::base().'ltimanager/saveProvider'; ?>">
    <input type="hidden" name="id" value="<?php echo $providerId; ?>">
    <fieldset>
        <legend>Provider Details</legend>

        <div class="control-group">
            <label class="control-label" for="name"><?php echo __('Provider Name'); ?></label>
            <div class="controls">
                <select name="name" id="name" class="span6" required>
                    <option value="video service">Video service</option>
                </select>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="consumer_key"><?php echo __('Consumer Key'); ?></label>
            <div class="controls">
                <input name="consumer_key" type="text" id="consumer_key" class="span6" value="<?php echo $consumerKey; ?>" required>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="secret"><?php echo __('Shared Secret'); ?></label>
            <div class="controls">
                <input name="secret" type="text" id="secret" class="span6" value="<?php echo $secret; ?>" required>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="launch_url"><?php echo __('Launch URL'); ?></label>
            <div class="controls">
                <input name="launch_url" type="text" id="launch_url" class="span6" value="<?php echo $launch_url; ?>" required>
            </div>
        </div>

        <button type="submit" class="btn btn-primary btn-large pull-right">Save changes</button>
    </fieldset>
</form>
