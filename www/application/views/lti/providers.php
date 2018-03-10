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
    <?php echo __('Providers manager'); ?>
    <a class="btn btn-primary pull-right" href="<?php echo URL::base().'ltimanager/providerView'; ?>"><i class="icon-plus-sign icon-white"></i><?php echo __('Add a provider'); ?></a>
</h1>

<table class="table table-striped table-bordered">
    <thead>
    <tr>
        <th>Name</th>
        <th>Consumer Key</th>
        <th>Secret</th>
        <th>Launch url</th>
        <th>Action</th>
    </tr>
    </thead>
    <tbody>
    <?php
    if (!empty($templateData['providers'])){
        foreach ($templateData['providers'] as $provider) {
    ?>
            <tr>
                <td><?php echo $provider->name; ?></td>
                <td><?php echo $provider->consumer_key; ?></td>
                <td><?php echo $provider->secret; ?></td>
                <td><?php echo $provider->launch_url ?></td>
                <td>
                    <div class="btn-group">
                        <a class="btn btn-info" href="<?php echo URL::base().'ltimanager/providerView/'.$provider->id; ?>">
                            <i class="icon-edit icon-white"></i><?php echo __('Edit'); ?>
                        </a>
                        <a data-toggle="modal" href="javascript:void(0)" data-target="<?php echo '#deleteNode'.$provider->id; ?>" class="btn btn-danger">
                            <i class="icon-trash icon-white"></i><?php echo __('Delete'); ?>
                        </a>
                    </div>

                    <div class="modal hide alert alert-block alert-error fade in" id="<?php echo 'deleteNode'.$provider->id; ?>">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="alert-heading"><?php echo __('Caution! Are you sure?'); ?></h4>
                        </div>
                        <div class="modal-body">
                            <p>
                                <?php echo __('You have just clicked the delete button, are you certain that you wish to proceed with deleting "' . $provider->name . '" provider?'); ?>
                            </p>
                            <p>
                                <a class="btn btn-danger" href="<?php echo URL::base().'ltimanager/deleteProvider/'.$provider->id; ?>">
                                    <?php echo __('Delete'); ?>
                                </a>
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