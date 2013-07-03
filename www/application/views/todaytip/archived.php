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
            <a class="btn btn-primary" href="<?php echo URL::base(); ?>TodayTipManager/addTip">
                <i class="icon-plus-sign icon-white"></i>
                <?php echo __('Add a tip'); ?></a>
        </div>
    </div>
    <h1><?php echo __('Archived Today\'s Tips'); ?></h1>
</div>

<table class="table table-striped table-bordered">
    <thead>
    <tr>
        <th><?php echo __('Id'); ?></th>
        <th><?php echo __('Title'); ?></th>
        <th><?php echo __('Start Date'); ?></th>
        <th><?php echo __('End Date'); ?></th>
        <th><?php echo __('Weight'); ?></th>
        <th><?php echo __('Active'); ?></th>
        <th><?php echo __('Actions'); ?></th>
    </tr>
    </thead>

    <body>
    <?php if(isset($templateData['archivedTips']) && count($templateData['archivedTips']) > 0) { ?>
        <?php foreach($templateData['archivedTips'] as $tip) { ?>
            <tr>
                <td><?php echo $tip->id; ?></td>
                <td><?php echo $tip->title; ?></td>
                <td><?php echo $tip->start_date; ?></td>
                <td><?php echo $tip->end_date; ?></td>
                <td><?php echo $tip->weight; ?></td>
                <td><?php echo $tip->is_active ? 'Active' : 'Inactive'; ?></td>
                <td>
                    <div class="btn-group">
                        <a class="btn btn-info" href="<?php echo URL::base(); ?>TodayTipManager/editTip/<?php echo $tip->id; ?>"><i class="icon-edit icon-white"></i><?php echo __('Edit'); ?></a>
                        <a class="btn" href="<?php echo URL::base(); ?>TodayTipManager/unarchive/<?php echo $tip->id; ?>"><i class="icon-folder-open icon-white"></i>Unarchive</a>
                        <a class="btn btn-danger" data-toggle="modal" href="javascript:void(0)" data-target="#delete-tip-<?php echo $tip->id; ?>"><i class="icon-trash icon-white"></i><?php echo __('Delete'); ?></a>
                    </div>
                    <div class="modal hide alert alert-block alert-error fade in" id="delete-tip-<?php echo $tip->id; ?>">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="alert-heading"><?php echo __('Caution! Are you sure?'); ?></h4>
                        </div>
                        <div class="modal-body">
                            <p><?php echo __('You have just clicked the delete button, are you certain that you wish to proceed with deleting "' . $tip->title . '" tip?'); ?></p>
                            <p>
                                <a class="btn btn-danger" href="<?php echo URL::base() . 'TodayTipManager/deleteArchiveTip/' . $tip->id; ?>"><?php echo __('Delete'); ?></a> <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
                            </p>
                        </div>
                    </div>
                </td>
            </tr>
        <?php } ?>
    <?php } else { ?>
        <tr class="info">
            <td colspan="7"><?php echo __('There are no tips yet. Please click the button above to add one.'); ?></td>
        </tr>
    <?php } ?>
    </body>
</table>