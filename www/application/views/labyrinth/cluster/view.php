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
    <div class="page-header">
        <div class="pull-right">  <a class="btn btn-primary" href='<?php echo URL::base(); ?>clusterManager/addDam/<?php echo $templateData['map']->id; ?>'><i class="icon-plus-sign"></i> <?php echo __('Add data cluster'); ?></a></div>
    <h1><?php echo __('Data clusters for Labyrinth "') . $templateData['map']->name . '"'; ?></h1></div>
<p><?php echo __('Data clusters act as aggregating and bringing mechanisms between data and media elements'); ?></p>
    <?php if(isset($templateData['warningMessage'])){
        echo '<div class="alert alert-error">';
        echo $templateData['warningMessage'];
        if(isset($templateData['listOfUsedReferences']) && count($templateData['listOfUsedReferences']) > 0){
            echo '<ul class="nav nav-tabs nav-stacked">';
            foreach($templateData['listOfUsedReferences'] as $referense){
                list($map, $node) = $referense;
                echo '<li><a href="' . URL::base() . 'nodeManager/editNode/' . $node['node_id'] . '">'
                    .$map['map_name'].' / '.$node['node_title'].'('.$node['node_id'].')'.'</a></li>';
            }
            echo '</ul>';
        }
        echo '</div>';
    }
    ?>
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
                <td><div class="btn-group"><a class="btn btn-info" href='<?php echo URL::base(); ?>clusterManager/editCluster/<?php echo $templateData['map']->id; ?>/<?php echo $dam->id; ?>'><i class="icon-edit"></i> <?php echo __('Edit'); ?></a> <a class="btn btn-danger" href='<?php echo URL::base(); ?>clusterManager/deleteDam/<?php echo $templateData['map']->id; ?>/<?php echo $dam->id; ?>'><i class="icon-trash"></i><?php echo __('Delete'); ?></a>
                    </div>
                </td>


            </tr>
                            <?php } ?>
                            <?php } else{ ?>

        <tr class="info">
            <td colspan="2">There are no clusters yet. You can create one using the button above.</td>
        </tr>
                        <?php } ?>
        </tbody>


    </table>


<?php } ?>

