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
/** @var Model_Leap_LRS|null $model */

$model = Arr::get($templateData, 'model');

if(!empty($model)){
    $id = $model->id;
    $is_enabled = $model->is_enabled;
    $name = $model->name;
    $url = $model->url;
    $username = $model->username;
    $password = $model->password;
    $api_version = $model->api_version;
}else{
    $id = '';
    $is_enabled = false;
    $name = '';
    $url = '';
    $username = '';
    $password = '';
    $api_version = '';
}
?>

<script type="text/javascript" src="<?php echo ScriptVersions::get(URL::base().'scripts/jquery-ui-1.9.1.custom.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo ScriptVersions::get(URL::base().'scripts/application.js'); ?>"></script>

<h1><?php echo $model ? __("Edit LRS") : __('Add LRS'); ?></h1>
<form class="form-horizontal left" method="post" action="<?php echo URL::base().'lrs/save'; ?>">
    <input type="hidden" name="id" value="<?php echo $id; ?>">
    <fieldset>
        <legend>LRS Details</legend>

        <div class="control-group">
            <label class="control-label" for="name"><?php echo __('Name'); ?></label>
            <div class="controls">
                <input name="name" type="text" id="name" class="span6" value="<?php echo $name; ?>" required>
            </div>
        </div>

        <div class="control-group">
            <label class="control-label" for="url"><?php echo __('URL'); ?></label>
            <div class="controls">
                <input name="url" type="text" id="url" class="span6" value="<?php echo $url; ?>">
            </div>
        </div>

        <div class="control-group">
            <label class="control-label" for="username"><?php echo __('Username'); ?></label>
            <div class="controls">
                <input name="username" type="text" id="username" class="span6" value="<?php echo $username; ?>">
            </div>
        </div>

        <div class="control-group">
            <label class="control-label" for="password"><?php echo __('Passord'); ?></label>
            <div class="controls">
                <input name="password" type="text" id="password" class="span6" value="<?php echo $password; ?>">
            </div>
        </div>

        <div class="control-group">
            <label class="control-label" for="api_version"><?php echo __('API Version'); ?></label>
            <div class="controls">
                <select name="api_version" id="api_version" class="span6" required>
                    <?php foreach(Model_Leap_LRS::$api_versions as $key => $version){ ?>
                        <option value="<?php echo $key ?>" <?php if($key === $api_version){?>selected<?php } ?>>
                            <?php echo $version ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
        </div>

        <div class="control-group">
            <label class="control-label" for="is_enabled"><?php echo __('Enable'); ?></label>
            <div class="controls">
                <input name="is_enabled" type="checkbox" id="is_enabled" value="1" <?php if($is_enabled){?>checked<?php } ?>>
            </div>
        </div>

        <button type="submit" class="btn btn-primary btn-large pull-right">Save</button>
    </fieldset>
</form>
