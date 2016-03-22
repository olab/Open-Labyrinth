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
?>
<h1 class="page-header">
    <?php echo __('LRS manager'); ?>
    <a class="btn btn-primary pull-right" href="<?php echo URL::base() . 'lrs/create'; ?>">
        <i class="icon-plus-sign icon-white"></i>
        <?php echo __('Add LRS'); ?>
    </a>
</h1>

<h4>Post-hoc report</h4>
<form action="/lrs/sendReportSubmit" class="form-inline left" method="post">
    <fieldset>
        <div class="control-group">
            <input class="datepicker" type="text" name="date_from" id="date_from"
                   value="<?php echo date('m/d/Y') ?>"/>
            -
            <input class="datepicker" type="text" name="date_to" id="date_to" value="<?php echo date('m/d/Y') ?>"/>
            <button type="submit" class="btn btn-primary _xapi-report">Send to all enabled LRS</button>
        </div>
    </fieldset>
</form>
<script>
    showWaitPopup('._xapi-report');
</script>

<hr>

<table class="table table-striped table-bordered">
    <thead>
    <tr>
        <th>ID</th>
        <th>Enabled</th>
        <th>Name</th>
        <th>URL</th>
        <th>Username</th>
        <th>Password</th>
        <th>API version</th>
        <th>Action</th>
    </tr>
    </thead>
    <tbody><?php
    if (count($templateData['lrs_list']) > 0) {
        /** @var Model_Leap_LRS $lrs */
        foreach ($templateData['lrs_list'] as $lrs) {
           ?>
            <tr>
                <td><?php echo $lrs->id; ?></td>
                <td><?php echo $lrs->is_enabled ? 'Yes' : 'No'; ?></td>
                <td><?php echo $lrs->name; ?></td>
                <td><?php echo $lrs->url; ?></td>
                <td><?php echo $lrs->username; ?></td>
                <td><?php echo $lrs->password; ?></td>
                <td><?php echo $lrs->getAPIVersionName(); ?></td>
                <td>
                    <div class="btn-group">
                        <a class="btn btn-info" href="<?php echo URL::base() . 'lrs/update/' . $lrs->id; ?>">
                            <i class="icon-edit icon-white"></i>
                            <?php echo __('Edit'); ?>
                        </a>

                        <a data-toggle="modal" href="javascript:void(0)"
                           data-target="<?php echo '#deleteNode' . $lrs->id; ?>" class="btn btn-danger">
                            <i class="icon-trash icon-white"></i>
                            <?php echo __('Delete'); ?>
                        </a>
                    </div>

                    <div class="modal hide alert alert-block alert-error fade in"
                         id="<?php echo 'deleteNode' . $lrs->id; ?>">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="alert-heading"><?php echo __('Caution! Are you sure?'); ?></h4>
                        </div>
                        <div class="modal-body">
                            <p><?php echo __('You have just clicked the delete button, are you certain that you wish to proceed with deleting "' . $lrs->name . '" ?'); ?></p>

                            <p>
                                <a class="btn btn-danger"
                                   href="<?php echo URL::base() . 'lrs/delete/' . $lrs->id; ?>">
                                    <?php echo __('Delete'); ?>
                                </a>
                                <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
                            </p>
                        </div>
                    </div>
                </td>
            </tr>
        <?php
        }
    } else { ?>
        <tr class="info">
        <td colspan="8"><?php echo __('There are no records yet. Please click the button above to add one.'); ?></td>
        </tr><?php
    } ?>
    </tbody>
</table>