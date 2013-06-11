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

<form method="POST" action="<?php echo URL::base(); ?>systemManager/saveOAuth">
    <legend><?php echo __('OAuth providers'); ?></legend>
    <?php if(isset($templateData['oauthProviders']) && count($templateData['oauthProviders']) > 0) { ?>
        <?php foreach($templateData['oauthProviders'] as $provider) { ?>
            <div class="control-group">
                <label class="control-label" for="fromname<?php echo $provider->id; ?>"><?php echo $provider->name; ?> (id, secret)</label>
                <div class="controls">
                    <input type="text" class="span3" id="fromname<?php echo $provider->id; ?>" name="appId<?php echo $provider->id; ?>" value="<?php echo $provider->appId; ?>" placeholder="" />
                    <input type="text" class="span5" id="mailfrom<?php echo $provider->id; ?>" name="secret<?php echo $provider->id; ?>" value="<?php echo $provider->secret; ?>" placeholder=""/>
                </div>
            </div>
        <?php } ?>
    <?php } ?>

    <input type="submit" class="btn btn-primary" value="<?php echo __('Update Settings'); ?>" />
</form>