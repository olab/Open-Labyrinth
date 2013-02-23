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
    <h1><?php echo __('data elements for Labyrinth "') . $templateData['map']->name . '"'; ?></h1>
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

                       <a class="btn btn-primary"
                           href="<?php echo URL::base(); ?>elementManager/editVpd/<?php echo $templateData['map']->id; ?>/<?php echo $vpd->id; ?>"><?php echo __('edit'); ?></a>
                       <a class="btn btn-primary"
                           href="<?php echo URL::base(); ?>elementManager/deleteVpd/<?php echo $templateData['map']->id; ?>/<?php echo $vpd->id; ?>"><?php echo __('delete'); ?></a>


                    </td>
                <tr>
            <?php } ?>
        <?php } else{ ?>
            <tr class="info"><td colspan="3">There are no elements yet. You may add an element by clicking the button below</td></tr>
        <?php } ?>



        </tbody>

    </table>
    <a class="btn btn-primary" href="<?php echo URL::base(); ?>elementManager/addNewElement/<?php echo $templateData['map']->id; ?>"><?php echo __('add a new data element'); ?></a>
<?php } ?>