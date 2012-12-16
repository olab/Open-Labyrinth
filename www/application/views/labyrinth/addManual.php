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
<h1><?php echo __("Create Manually"); ?></h1>

<form class="form-horizontal" id="addManualForm" name="addManualForm" method="post" action="<?php echo URL::base() . 'labyrinthManager/addNewMap' ?>">
    <fieldset class="fieldset">
        <legend><?php echo __('Labyrinth Details'); ?></legend>
        <div class="control-group">
            <label class="control-label" for="title"><?php echo __('Labyrinth Title'); ?></label>
            <div class="controls">
                <input type="text" class="span6" id="title" name="title" value="" />
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="description"><?php echo __('Labyrinth Description'); ?></label>
            <div class="controls">
                <textarea class="span6" id="description" name="description"></textarea>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="keywords"><?php echo __('Labyrinth Keywords'); ?></label>
            <div class="controls">
                <input type="text" class="span6" id="keywords" name="keywords" value="" />
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="type"><?php echo __('Labyrinth Type'); ?></label>
            <div class="controls">
                <select id="type" name="type" class="span6">
                <?php
                if (isset($templateData['types'])) {
                    foreach ($templateData['types'] as $type) {
                        ?>
                        <option value="<?php echo $type->id; ?>"><?php echo $type->name; ?></option>
                        <?php
                    }
                }
                ?>
                </select>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="skin"><?php echo __('Labyrinth Skin'); ?></label>
            <div class="controls">
                <select id="skin" name="skin" class="span6">
                <?php
                if (isset($templateData['skins'])) {
                    foreach ($templateData['skins'] as $skin) {
                        ?>
                        <option value="<?php echo $skin->id; ?>"><?php echo $skin->name; ?></option>
                        <?php
                    }
                }
                ?>
                </select>
            </div>
        </div>
    </fieldset>
    <fieldset class="fieldset">
        <legend><?php echo __('Labyrinth Timing'); ?></legend>
        <div class="control-group">
            <label class="control-label"><?php echo __('Timing'); ?></label>
            <div class="controls">
                <label class="radio">
                    <input type="radio" id="timing-on" name="timing" value="1"> <?php echo __('On'); ?>
                </label>
                <label class="radio">
                    <input type="radio" id="timing-off" name="timing" value="0" checked> <?php echo __('Off'); ?>
                </label>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="delta_time"><?php echo __('Timing Delta'); ?></label>
            <div class="controls">
                <input type="text" class="span1" id="delta_time" name="delta_time" value="" />
                <span class="help-inline"><?php echo __('Seconds'); ?></span>
            </div>
        </div>
    </fieldset>
    <fieldset class="fieldset">
        <legend><?php echo __('Labyrinth Security'); ?></legend>
        <div class="control-group">
            <label class="control-label"><?php echo __('Security'); ?></label>
            <div class="controls">
            <?php
            if (isset($templateData['securities'])) {
                foreach ($templateData['securities'] as $security) {
                    ?>
                    <label class="radio">
                        <input type="radio" id="security-<?php echo $security->id; ?>" name="security" value="<?php echo $security->id; ?>" /> <?php echo __($security->name); ?>
                    </label>
                    <?php
                }
            }
            ?>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label"><?php echo __('Section Browsing'); ?></label>
            <div class="controls">
            <?php
            if (isset($templateData['sections'])) {
                foreach ($templateData['sections'] as $section) {
                    ?>
                    <label class="radio">
                        <input type="radio" id="section-<?php echo $section->id; ?>" name="section" value="<?php echo $section->id; ?>" /> <?php echo __($section->name); ?>
                    </label>
                    <?php
                }
            }
            ?>
            </div>
        </div>
    </fieldset>

    <div class="pull-right">
        <input type="submit" class="btn btn-primary btn-large" name="AddManualMapSubmit" value="<?php echo __('Create Labyrinth'); ?>" />
    </div>
</form>

