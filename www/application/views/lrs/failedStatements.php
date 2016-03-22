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
    <?php echo __('Failed xAPI Statements by LRS'); ?>
    <a class="btn btn-primary pull-right" href="<?php echo URL::base() . 'lrs/sendFailedLRSStatements'; ?>">
        <?php echo __('Send all again'); ?>
    </a>
</h1>

<table class="table table-striped table-bordered">
    <thead>
    <tr>
        <th>ID</th>
        <th>Statement ID</th>
        <th>Verb</th>
        <th>LRS name</th>
        <th>Created At</th>
        <th>Action</th>
    </tr>
    </thead>
    <tbody><?php
    if (count($templateData['lrs_statements']) > 0) {
        /** @var Model_Leap_LRSStatement $lrs_statement */
        foreach ($templateData['lrs_statements'] as $lrs_statement) {
            ?>
            <tr>
                <td><?php echo $lrs_statement->id; ?></td>
                <td><?php echo $lrs_statement->statement_id; ?></td>
                <td>
                    <?php echo json_decode($lrs_statement->statement->statement, true)['verb']['id'] ?>
                </td>
                <td><?php echo $lrs_statement->lrs->name; ?></td>
                <td><?php echo DateTime::createFromFormat('U', $lrs_statement->created_at)->format('m/d/Y H:i:s') ?></td>
                <td>
                    <div class="btn-group">
                        <a data-toggle="modal" href="javascript:void(0)"
                           data-target="<?php echo '#deleteNode' . $lrs_statement->id; ?>" class="btn btn-danger">
                            <i class="icon-trash icon-white"></i>
                            <?php echo __('Delete'); ?>
                        </a>
                    </div>

                    <div class="modal hide alert alert-block alert-error fade in"
                         id="<?php echo 'deleteNode' . $lrs_statement->id; ?>">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="alert-heading"><?php echo __('Caution! Are you sure?'); ?></h4>
                        </div>
                        <div class="modal-body">
                            <p><?php echo __('You have just clicked the delete button, are you certain that you wish to proceed with deleting "' . $lrs_statement->id . '" ?'); ?></p>

                            <p>
                                <a class="btn btn-danger"
                                   href="<?php echo URL::base() . 'lrs/deleteLRSStatement/' . $lrs_statement->id; ?>">
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
        <td colspan="6"><?php echo __('There are no failed statements!'); ?></td>
        </tr><?php
    } ?>
    </tbody>
</table>