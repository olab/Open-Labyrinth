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
if (isset($templateData['map'])) {
    ?>
    <div class="page-header">
    <div class="pull-right">
        <div class="btn-group">
            <a class="btn btn-primary" href="<?php echo URL::base() . 'nodeManager/addNode/' . $templateData['map']->id; ?>">
                <i class="icon-plus-sign icon-white"></i>
                <?php echo __('Add a node'); ?></a>
        </div>
    </div>
        <h1><?php echo __('Edit nodes of Labyrinth') . ' "' . $templateData['map']->name . '"'; ?></h1>
    </div>
    <?php if(isset($templateData['warningMessage'])){ ?>
    <span style ="color:red;"><?php echo $templateData['warningMessage']; ?></span>
    <?php }?>

    <?php if (isset($templateData['nodes'])) { ?>
        <table class="table table-striped table-bordered">
            <colgroup>
                <col style="width: 5%">
                <col style="width: 45%">
                <col style="width: 30%">
                <col style="width: 15%">
            </colgroup>
            <thead>
            <tr>
                <th>#</th>
                <th>Title</th>
                <th>Actions</th>
                <th>Outgoing Links</th>

            </tr>
            </thead>

            <tbody>
            <?php foreach ($templateData['nodes'] as $node) { ?>
                <tr>
                    <td><?php echo $node->id; ?><?php if ($node->type->name == 'root') echo '(root)'; ?></td>
                <td><a href="<?php echo URL::base() . 'renderLabyrinth/go/' . $templateData['map']->id.'/'. $node->id; ?>"><?php echo $node->title; ?></a></td>


                <td>
                    <div class="btn-group">
                    <a class="btn btn-info" href="<?php echo URL::base() . 'nodeManager/editNode/' . $node->id; ?>">
                        <i class="icon-edit icon-white"></i><?php echo __('Edit'); ?></a>
                    <a target="_blank" class="btn btn-success" href="<?php echo URL::base() . 'renderLabyrinth/go/' . $templateData['map']->id.'/'. $node->id; ?>">
                        <i class="icon-eye-open icon-white"></i>
                        <?php echo __('View'); ?></a>
                    <a data-toggle="modal" href="javascript:void(0)" data-target="#delete-node-<?php echo $node->id; ?>" class="btn btn-danger"><i class="icon-trash icon-white"></i><?php echo __('Delete'); ?></a>
                    </div>
                    <div class="modal hide alert alert-block alert-error fade in" id="delete-node-<?php echo $node->id; ?>">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="alert-heading"><?php echo __('Caution! Are you sure?'); ?></h4>
                        </div>
                        <div class="modal-body">
                            <p><?php echo __('You have just clicked the delete button, are you certain that you wish to proceed with deleting "' . $node->title . '" node?'); ?></p>
                            <p>
                                <a class="btn btn-danger" href="<?php echo URL::base() . 'nodeManager/deleteNode/' . $templateData['map']->id.'/'. $node->id; ?>"><?php echo __('Delete'); ?></a> <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>                        </p>
                        </div>
                    </div>
                </td>
                <td><a class="btn btn-info" href="<?php echo URL::base() . 'linkManager/editLinks/' . $templateData['map']->id .'/' . $node->id; ?>">
                        <i class="icon-link icon-white"></i><?php echo __('Links'); ?></a></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    <?php } ?>

    <!--div class="control-group">
    <a class="btn btn-info" href="<?php echo URL::base() . 'nodeManager/sections/' . $templateData['map']->id; ?>">Sections</a>
    <a class="btn btn-info" href="<?php echo URL::base() . 'nodeManager/grid/' . $templateData['map']->id; ?>">Nodegrid</a>
    </div-->
<?php } ?>
