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
<h1><?php echo __("Scenario"); ?> '<?php echo $templateData['webinar']->title; ?>'</h1>

<form class="form-horizontal" id="addManualForm" name="addManualForm" method="post" action="<?php echo URL::base() ?>">
    <?php if(isset($templateData['webinar']) && $templateData['webinar']->steps != null  && count($templateData['webinar']->steps) > 0) { ?>
        <?php foreach($templateData['webinar']->steps as $webinarStep) { ?>
            <fieldset class="fieldset">
                <legend>
                    <?php echo $templateData['webinar']->current_step == $webinarStep->id ? ('<b style="color: #0088cc;">' . $webinarStep->name . '</b>') : $webinarStep->name; ?>
                </legend>
                <?php if($webinarStep->maps != null && count($webinarStep->maps) > 0) { ?>
                    <?php $index = 1; foreach($webinarStep->maps as $webinarMap) { ?>
                        <div class="control-group">
                            <label class="control-label" for="title"><?php echo '#' . $index; ?></label>
                            <div style="margin-top:3px;" class="controls">
                                <span><?php echo $webinarMap->map->name; ?></span>
                                <?php if($templateData['webinar']->current_step == $webinarStep->id && isset($templateData['mapsMap'][$webinarStep->id][$webinarMap->map_id]) && ($templateData['mapsMap'][$webinarStep->id][$webinarMap->map_id] == 0 || $templateData['mapsMap'][$webinarStep->id][$webinarMap->map_id] == 1)) { ?>
                                    <a href="<?php echo URL::base(); ?>webinarManager/play/<?php echo $templateData['webinar']->id; ?>/<?php echo $templateData['webinar']->current_step; ?>/<?php echo $webinarMap->map_id; ?>" class="btn btn-success btn-small"><i class="icon-play"></i>Play</a>
                                <?php } ?>
                            </div>
                        </div>
                    <?php $index++; } ?>
                <?php } ?>
            </fieldset>
        <?php } ?>
    <?php } ?>

    <?php if(isset($templateData['webinar']) && $templateData['webinar']->forum_id > 0) { ?>
        <div class="form-actions">
            <div class="pull-right">
                <a class="btn btn-info" href="<?php echo URL::base(); ?>dforumManager/viewForum/<?php echo $templateData['webinar']->forum_id;?>">
                    <i class="icon-comment icon-white"></i>
                    <?php echo __('Go to the Forum Topic'); ?>
                </a>
            </div>
        </div>
    <?php } ?>
</form>

