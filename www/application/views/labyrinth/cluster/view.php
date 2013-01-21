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
if (isset($templateData['map'])) { ?>
    <h1><?php echo __('data clusters for Labyrinth "') . $templateData['map']->name . '"'; ?></h1>
<p><?php echo __('data clusters act as aggregating and bringing mechanisms between data and media elements'); ?></p>
<table class="table table-striped table-bordered" id="my-labyrinths">



                            <?php if(isset($templateData['dams']) and count($templateData['dams']) > 0) { ?>
        <thead>
        <tr>
            <th><?php echo __('Name'); ?></th>
            <th><?php echo __('Actions'); ?></th>
        </tr>
        </thead>

        <tbody>
                            <?php foreach($templateData['dams'] as $dam) { ?>

            <tr>
                                <td><?php echo $dam->name; ?> (<?php echo $dam->id; ?>)</td>
                <td><a class="btn btn-primary" href='<?php echo URL::base(); ?>clusterManager/editCluster/<?php echo $templateData['map']->id; ?>/<?php echo $dam->id; ?>'><?php echo __('edit'); ?></a> <a class="btn btn-primary" href='<?php echo URL::base(); ?>clusterManager/deleteDam/<?php echo $templateData['map']->id; ?>/<?php echo $dam->id; ?>'><?php echo __('delete'); ?></a></td>


            </tr>
                            <?php } ?>
        </tbody>
                            <?php } ?>

    </table>

                            <a class="btn btn-primary" href='<?php echo URL::base(); ?>clusterManager/addDam/<?php echo $templateData['map']->id; ?>'><?php echo __('add data cluster'); ?></a>
<?php } ?>

