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
</h1>

<div style="margin-bottom: 10px">
    <div class="btn-group">
        <a class="btn btn-primary" href="<?php echo URL::base() . 'lrs/sendFailedLRSStatements'; ?>">
            <?php echo __('Send all'); ?>
        </a>

        <a data-toggle="modal" href="javascript:void(0)"
           data-target="#deleteFailedLRSStatements" class="btn btn-danger">
            <i class="icon-trash icon-white"></i>
            <?php echo __('Delete All'); ?>
        </a>
    </div>

    <div class="btn-group">
        <span class="btn btn-primary" id="sendSelectedFailedLRSStatements">
            <?php echo __('Send selected'); ?>
        </span>

        <a data-toggle="modal" href="javascript:void(0)"
           data-target="#deleteSelectedFailedLRSStatementsPopup" class="btn btn-danger">
            <i class="icon-trash icon-white"></i>
            <?php echo __('Delete selected'); ?>
        </a>
    </div>
</div>
<script>
    $(document).ready(function(){
        $('#sendSelectedFailedLRSStatements').on('click', function(){
            var lrs_statement_ids = getCheckboxesValues();
            if (lrs_statement_ids.length) {
                $.post('/lrs/sendSelectedFailedLRSStatements', {'lrs_statement_ids': lrs_statement_ids})
                    .done(function () {
                        window.location.reload();
                    })
                    .fail(function (jqXHR) {
                        loading.stop('body');
                        var response = JSON.parse(jqXHR.responseText),
                            message = 'Unexpected error. Please, try again';

                        if (response.hasOwnProperty('message')) {
                            message = response.message;
                        }

                        alert(message);
                    });
            } else {
                alert('Please select at least one item.');
            }
        });

        $('#deleteSelectedFailedLRSStatements').on('click', function() {
            var lrs_statement_ids = getCheckboxesValues();
            if (lrs_statement_ids.length) {
                $.post('/lrs/deleteSelectedFailedLRSStatements', {'lrs_statement_ids': lrs_statement_ids})
                    .done(function () {
                        window.location.reload();
                    })
                    .fail(function (jqXHR) {
                        loading.stop('body');
                        var response = JSON.parse(jqXHR.responseText),
                            message = 'Unexpected error. Please, try again';

                        if (response.hasOwnProperty('message')) {
                            message = response.message;
                        }

                        alert(message);
                    });
            } else {
                alert('Please select at least one item.');
            }
        });
    });
</script>

<table class="table table-striped table-bordered">
    <thead>
    <tr>
        <th></th>
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
                <td>
                    <input name="lrs_statements[]" class="_checkbox" type="checkbox" value="<?php echo $lrs_statement->id; ?>">
                </td>
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
        <td colspan="7"><?php echo __('There are no failed statements!'); ?></td>
        </tr><?php
    } ?>
    </tbody>
</table>

<div class="modal hide alert alert-block alert-error fade in"
     id="deleteFailedLRSStatements">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="alert-heading"><?php echo __('Caution! Are you sure?'); ?></h4>
    </div>
    <div class="modal-body">
        <p><?php echo __('You have just clicked the delete button, are you certain that you wish to proceed with deleting ?'); ?></p>

        <p>
            <a class="btn btn-danger"
               href="<?php echo URL::base() . 'lrs/deleteFailedLRSStatements'; ?>">
                <?php echo __('Delete'); ?>
            </a>
            <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
        </p>
    </div>
</div>

<div class="modal hide alert alert-block alert-error fade in"
     id="deleteSelectedFailedLRSStatementsPopup">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="alert-heading"><?php echo __('Caution! Are you sure?'); ?></h4>
    </div>
    <div class="modal-body">
        <p><?php echo __('You have just clicked the delete button, are you certain that you wish to proceed with deleting ?'); ?></p>

        <p>
            <span class="btn btn-danger" id="deleteSelectedFailedLRSStatements">
                <?php echo __('Delete'); ?>
            </span>
            <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
        </p>
    </div>
</div>