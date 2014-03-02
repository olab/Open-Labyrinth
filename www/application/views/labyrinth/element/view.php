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
        <a class="btn btn-primary" href="<?php echo URL::base(); ?>elementManager/addNewElement/<?php echo $templateData['map']->id; ?>"><i class="icon-plus-sign"></i><?php echo __('Add data element'); ?></a></div>
    <h1><?php echo __('Data elements for Labyrinth "') . $templateData['map']->name . '"'; ?></h1></div>
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
    <table class="table table-striped table-bordered">
        <thead>
        <tr>
            <th>Element</th>

            <th>Properties</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php if (isset($templateData['vpds']) and count($templateData['vpds']) > 0) { ?>

            <?php foreach ($templateData['vpds'] as $vpd) { ?>
                <tr>
                    <td>
                    <?php echo $vpd->type->label; ?>
                        (<?php echo $vpd->id; ?>)</td>
                    <td>
                        <?php if (count($vpd->elements) > 0) { ?>
                            <?php foreach ($vpd->elements as $element) { ?>
                                <?php echo $element->key; ?> = <?php echo $element->value; ?><br>
                            <?php } ?>
                        <?php } ?>

                    </td>
                   <td>
<div class="btn-group">
                       <a class="btn btn-info"
                           href="<?php echo URL::base(); ?>elementManager/editVpd/<?php echo $templateData['map']->id; ?>/<?php echo $vpd->id; ?>"><i class="icon-edit"></i><?php echo __('Edit'); ?></a>
                       <a class="btn btn-danger"
                           href="<?php echo URL::base(); ?>elementManager/deleteVpd/<?php echo $templateData['map']->id; ?>/<?php echo $vpd->id; ?>"><i class="icon-edit"></i><?php echo __('Delete'); ?></a>

                       </div>
                    </td>
                <tr>
            <?php } ?>
        <?php } else{ ?>
            <tr class="info"><td colspan="3">There are no elements yet. You may add an element by clicking the button below</td></tr>
        <?php } ?>



        </tbody>

    </table>

<?php } ?>