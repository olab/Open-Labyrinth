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

    <h1><?php echo __('Edit nodes of Labyrinth') . ' "' . $templateData['map']->name . '"'; ?></h1>
    <div class="control-group"> <a class="btn btn-info" href="<?php echo URL::base() . 'nodeManager/addNode/' . $templateData['map']->id; ?>"><?php echo __('Add a node'); ?></a></div>

    <?php if (isset($templateData['nodes'])) { ?>
        <table class="table table-striped table-bordered">
            <colgroup>
                <col style="width: 5%">
                <col style="width: 40%">
                <col style="width: 30%">
                <col style="width: 20%">
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
                    <td><?php echo $node->id; ?><?php if ($node->type->name == 'root') echo '[root]'; ?></td>
                <td><a href="<?php echo URL::base() . 'renderLabyrinth/go/' . $templateData['map']->id.'/'. $node->id; ?>"><?php echo $node->title; ?></a></td>


                <td>
                    <a class="btn btn-info" href="<?php echo URL::base() . 'nodeManager/editNode/' . $node->id; ?>"><?php echo __('Edit'); ?></a>
                    <a class="btn btn-success" href="<?php echo URL::base() . 'renderLabyrinth/go/' . $templateData['map']->id.'/'. $node->id; ?>"><?php echo __('View'); ?></a>
                    <a class="btn btn-success" href="<?php echo URL::base() . '' . $templateData['map']->id.'/'. $node->id; ?>"><?php echo __('Delete'); ?></a>
                </td>
                <td><a class="btn btn-info" href="<?php echo URL::base() . 'linkManager/index/' . $templateData['map']->id; ?>"><?php echo __('Links'); ?></a></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    <?php } ?>

    <div class="control-group">
    <a class="btn btn-info" href="<?php echo URL::base() . 'nodeManager/sections/' . $templateData['map']->id; ?>">Sections</a>
    <a class="btn btn-info" href="<?php echo URL::base() . 'nodeManager/grid/' . $templateData['map']->id; ?>">Nodegrid</a>
    </div>



<?php } ?>


