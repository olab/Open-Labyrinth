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
<div class="page-header">
    <div class="pull-right">
        <div class="btn-group">
            <a class="btn btn-primary" href="<?php echo URL::base() . 'webinarManager/add'; ?>">
                <i class="icon-plus-sign icon-white"></i>
                Create Scenario
            </a>
        </div>
    </div>
    <h1><?php echo __('Scenarios'); ?></h1>
</div>
<table class="table table-striped table-bordered" id="my-labyrinths">
    <colgroup>
        <col style="width: 50%" />
        <col style="width: 20%" />
        <col style="width: 30%" />
        </colgroup>
    <thead>
        <tr>
            <th><?php echo __('Scenario Title'); ?></th>
            <th><?php echo __('Step'); ?></th>
            <th><?php echo __('Actions'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php if(isset($templateData['webinars']) && count($templateData['webinars']) > 0) { ?>
            <?php foreach($templateData['webinars'] as $webinar) { ?>
                <tr>
                    <td><a href="<?php echo URL::base(); ?>"><?php echo $webinar->title; ?></a></td>
                    <td><?php echo $webinar->current_step == null ? '-' : $webinar->current_step; ?></td>
                    <td class="center">
                        <div class="btn-group">
                            <a class="btn btn-success" href="<?php echo URL::base() . 'webinarManager/statistic/' . $webinar->id; ?>">
                                <i class="icon-play icon-white"></i>
                                <span class="visible-desktop">View statistic</span>
                            </a>
                            <?php if($webinar->forum_id > 0) { ?>
                            <a class="btn btn-info" href="<?php echo URL::base(); ?>dforumManager/viewForum/<?php echo $webinar->forum_id; ?>">
                                <i class="icon-list-alt"></i>
                                <span class="visible-desktop">DF thread</span>
                            </a>
                            <?php } ?>
                            <a data-toggle="modal" href="javascript:void(0)" data-target="#change-step-<?php echo $webinar->id; ?>" class="btn btn-info">
                                <i class="icon-edit icon-white"></i>
                                Change step
                            </a>
                            <a class="btn btn-info" href="<?php echo URL::base(); ?>webinarManager/edit/<?php echo $webinar->id; ?>">
                                <i class="icon-edit icon-white"></i>
                                Edit
                            </a>
                            <a data-toggle="modal" href="javascript:void(0)" data-target="#delete-node-<?php echo $webinar->id; ?>" class="btn btn-danger"><i class="icon-trash icon-white"></i><?php echo __('Delete'); ?></a>
                        </div>
                        <div class="modal hide alert alert-block alert-error fade in" id="delete-node-<?php echo $webinar->id; ?>">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="alert-heading"><?php echo __('Caution! Are you sure?'); ?></h4>
                            </div>
                            <div class="modal-body">
                                <p><?php echo __('You have just clicked the delete button, are you certain that you wish to proceed with deleting "' . $webinar->title . '"?'); ?></p>
                                <p>
                                    <a class="btn btn-danger" href="<?php echo URL::base(); ?>webinarManager/delete/<?php echo $webinar->id; ?>"><?php echo __('Delete'); ?></a> <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
                                </p>
                            </div>
                        </div>
                        <div class="modal hide fade in" id="change-step-<?php echo $webinar->id; ?>">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="alert-heading"><?php echo __('Select step'); ?></h4>
                            </div>
                            <div class="modal-body">
                                <p>
                                    <div><input class="current-step-<?php echo $webinar->id; ?>" type="radio" name="currentStep<?php echo $webinar->id; ?>" value="1" <?php if($webinar->current_step == 1) echo 'checked'; ?>> Step 1</div>
                                    <div><input class="current-step-<?php echo $webinar->id; ?>" type="radio" name="currentStep<?php echo $webinar->id; ?>" value="2" <?php if($webinar->current_step == 2) echo 'checked'; ?>> Step 2</div>
                                    <div><input class="current-step-<?php echo $webinar->id; ?>" type="radio" name="currentStep<?php echo $webinar->id; ?>" value="3" <?php if($webinar->current_step == 3) echo 'checked'; ?>> Step 3</div>
                                </p>
                            </div>
                            <div class="modal-footer">
                                <a class="btn change-step-btn" href="<?php echo URL::base(); ?>webinarManager/changeStep/<?php echo $webinar->id; ?>/" webinarId="<?php echo $webinar->id; ?>"><?php echo __('Change'); ?></a>
                                <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
                            </div>
                        </div>
                    </td>
                </tr>
            <?php } ?>
        <?php } else { ?>
            <tr class="info"><td colspan="4">There are no available webinars right now. You may add a webinar using the add button.</td> </tr>
        <?php } ?>
    </tbody>
</table>

<script>
    $(function() {
        $('.change-step-btn').click(function() {
            var webinarId = $(this).attr('webinarId'),
                step      = $('.current-step-' + webinarId + ':checked').val();

            $(this).attr('href', $(this).attr('href') + step);
        });
    })
</script>