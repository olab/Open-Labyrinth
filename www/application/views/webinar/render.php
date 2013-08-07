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
    <fieldset class="fieldset">
        <legend>
            <?php echo $templateData['webinar']->current_step == 1 ? ('<b>' . __('First step') . '</b>') : __('First step'); ?>
        </legend>
        <?php if(isset($templateData['webinar']) && count($templateData['webinar']->maps) > 0) { ?>
            <?php $index = 1; foreach($templateData['webinar']->maps as $map) { ?>
                <?php if($map->step == 1) { ?>
                <div class="control-group">
                    <label class="control-label" for="title"><?php echo '#' . $index; ?></label>
                    <div style="margin-top:3px;" class="controls">
                        <span><?php echo $map->map->name; ?></span>
                        <?php if($templateData['webinar']->current_step == 1 && isset($templateData['mapsMap'][1][$map->map_id])) { ?>
                            <?php if($templateData['mapsMap'][1][$map->map_id] == 0 || $templateData['mapsMap'][1][$map->map_id] == 1) { ?>
                                <a href="<?php echo URL::base(); ?>webinarManager/play/<?php echo $templateData['webinar']->id; ?>/1/<?php echo $map->map_id; ?>" class="btn btn-success btn-small"><i class="icon-play"></i>Play</a>
                            <?php } ?>
                        <?php } ?>
                    </div>
                </div>
                <?php $index++; } ?>
            <?php } ?>
        <?php } ?>
    </fieldset>

    <fieldset class="fieldset">
        <legend>
            <?php echo $templateData['webinar']->current_step == 2 ? ('<b>' . __('Second step') . '</b>') : __('Second step'); ?>
        </legend>
        <?php if(isset($templateData['webinar']) && count($templateData['webinar']->maps) > 0) { ?>
            <?php $index = 1; foreach($templateData['webinar']->maps as $map) { ?>
                <?php if($map->step == 2) { ?>
                    <div class="control-group">
                        <label class="control-label" for="title"><?php echo '#' . $index; ?></label>
                        <div style="margin-top:3px;" class="controls">
                            <span><?php echo $map->map->name; ?></span>
                            <?php if($templateData['webinar']->current_step == 2 && isset($templateData['mapsMap'][2][$map->map_id])) { ?>
                                <?php if($templateData['mapsMap'][2][$map->map_id] == 0 || $templateData['mapsMap'][2][$map->map_id] == 1) { ?>
                                    <a href="<?php echo URL::base(); ?>webinarManager/play/<?php echo $templateData['webinar']->id; ?>/2/<?php echo $map->map_id; ?>" class="btn btn-success btn-small"><i class="icon-play"></i>Play</a>
                                <?php } else if($templateData['mapsMap'][2][$map->map_id] == 2) { ?>
                                    <a href="<?php echo URL::base(); ?>webinarManager/mapReport/<?php echo $templateData['webinar']->id; ?>/<?php echo $map->map_id; ?>" class="btn btn-info btn-small"><i class="icon-eye-open"></i>View 4R</a>
                                <?php } ?>
                            <?php } ?>
                        </div>
                    </div>
                    <?php $index++; } ?>
            <?php } ?>
        <?php } ?>
    </fieldset>

    <fieldset class="fieldset">
        <legend>
            <?php echo $templateData['webinar']->current_step == 3 ? ('<b>' . __('Third step') . '</b>') : __('Third step'); ?>
        </legend>
        <?php if(isset($templateData['webinar']) && count($templateData['webinar']->maps) > 0) { ?>
            <?php $index = 1; foreach($templateData['webinar']->maps as $map) { ?>
                <?php if($map->step == 3) { ?>
                    <div class="control-group">
                        <label class="control-label" for="title"><?php echo '#' . $index; ?></label>
                        <div style="margin-top:3px;" class="controls">
                            <span><?php echo $map->map->name; ?></span>
                            <?php if($templateData['webinar']->current_step == 3 && isset($templateData['mapsMap'][3][$map->map_id])) { ?>
                                <?php if($templateData['mapsMap'][3][$map->map_id] == 0 || $templateData['mapsMap'][3][$map->map_id] == 1) { ?>
                                    <a href="<?php echo URL::base(); ?>webinarManager/play/<?php echo $templateData['webinar']->id; ?>/3/<?php echo $map->map_id; ?>" class="btn btn-success btn-small"><i class="icon-play"></i>Play</a>
                                <?php } else if($templateData['mapsMap'][3][$map->map_id] == 2) { ?>
                                    <a href="<?php echo URL::base(); ?>webinarManager/mapReport/<?php echo $templateData['webinar']->id; ?>/<?php echo $map->map_id; ?>" class="btn btn-info btn-small"><i class="icon-eye-open"></i>View 4R</a>
                                <?php } ?>
                            <?php } ?>
                        </div>
                    </div>
                    <?php $index++; } ?>
            <?php } ?>
        <?php } ?>
    </fieldset>

    <?php if(isset($templateData['webinar']) && $templateData['webinar']->forum_id > 0) { ?>
        <div class="pull-right">
            <a class="btn btn-primary btn-large" href="<?php echo URL::base(); ?>dforumManager/viewForum/<?php echo $templateData['webinar']->forum_id; ?>"><?php echo __('Go to Discussion Forum thread'); ?></a>
        </div>
    <?php } ?>
</form>

