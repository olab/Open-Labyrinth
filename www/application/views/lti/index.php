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
<h1 class="page-header">
    <?php echo __('Consumers manager'); ?>
    <a class="btn btn-primary pull-right" href="<?php echo URL::base().'ltimanager/userView'; ?>"><i class="icon-plus-sign icon-white"></i><?php echo __('Add a consumer'); ?></a>
</h1>

<div class="alert alert-success alert-dismissible" role="alert">
    <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span></button>
    Launch URL: <strong><?php echo URL::site(); ?></strong>
</div>

<table class="table table-striped table-bordered">
    <thead>
        <tr>
            <th>Name</th>
            <th>Consumer Key</th>
            <th>Available</th>
            <th>Enable from</th>
            <th>Enable until</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody><?php
    if (isset($templateData['users']) AND count($templateData['users'])){
        foreach ($templateData['users'] as $user) { ?>
        <tr>
            <td><?php echo $user->name; ?></td>
            <td><?php echo $user->consumer_key; ?></td>
            <td><?php echo $user->enabled ? 'Yes' : 'No'; ?></td>
            <td><?php echo $user->enable_from; ?></td>
            <td><?php echo $user->without_end_date ? 'Without end date' : $user->enable_until; ?></td>
            <td>
                <div class="btn-group">
                    <a class="btn btn-info" href="<?php echo URL::base().'ltimanager/userView/'.$user->id; ?>"><i class="icon-edit icon-white"></i><?php echo __('Edit'); ?></a>
                    <a data-toggle="modal" href="javascript:void(0)" data-target="<?php echo '#deleteNode'.$user->id; ?>" class="btn btn-danger"><i class="icon-trash icon-white"></i><?php echo __('Delete'); ?></a>
                </div>

                <div class="modal hide alert alert-block alert-error fade in" id="<?php echo 'deleteNode'.$user->id; ?>">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="alert-heading"><?php echo __('Caution! Are you sure?'); ?></h4>
                    </div>
                    <div class="modal-body">
                        <p><?php echo __('You have just clicked the delete button, are you certain that you wish to proceed with deleting "' . $user->name . '" user?'); ?></p>
                        <p>
                            <a class="btn btn-danger" href="<?php echo URL::base().'ltimanager/deleteUser/'.$user->id; ?>"><?php echo __('Delete'); ?></a>
                            <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
                        </p>
                    </div>
                </div>
            </td>
        </tr><?php
        }
    } else { ?>
        <tr class="info">
            <td colspan="8"><?php echo __('There are no records yet. Please click the button above to add one.'); ?></td>
        </tr><?php
    } ?>
    </tbody>
</table>