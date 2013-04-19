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
    <h1><?php echo __('Edit links for Labyrinth "') . $templateData['map']->name . '"'; ?></h1></div>
    <p><?php echo __('Links represent the options available to the user'); ?></p>

    <table class="table table-striped table-bordered">
        <thead>
        <tr>
            <th><?php echo __("Origin Node"); ?></th>
            <th><?php echo __("Linked Destinations"); ?></th>
            <th><?php echo __("Actions"); ?></th>
        </tr>

        </thead>
        <tbody>
        <?php if (isset($templateData['nodes']) and count($templateData['nodes']) > 0) { ?>
            <?php foreach ($templateData['nodes'] as $node) { ?>
                <tr>
                    <td>
                            <a href="<?php echo URL::base(); ?>renderLabyrinth/go/<?php echo $templateData['map']->id; ?>/<?php echo $node->id; ?>"><?php echo $node->title; ?></a>
                    </td>
                    <td>
                        <?php if (count($node->links) > 0) { ?>
                            <ul>
                            <?php foreach ($node->links as $link) { ?>


                               <li>
                                    <a href="<?php echo URL::base(); ?>renderLabyrinth/go/<?php echo $templateData['map']->id; ?>/<?php echo $link->node_2->id; ?>"><?php echo $link->node_2->title; ?></a>

                                    </li>
                            <?php } ?></ul>
                        <?php } ?>
                    </td>
                    <td>
                        <a class="btn btn-info" href="<?php echo URL::base() . 'linkManager/editLinks/' . $templateData['map']->id . '/' . $node->id; ?>"><i class="icon-link"></i>Manage links</a>
                    </td>
                </tr>
            <?php } ?>
        <?php } ?>


        </tbody>

    </table>

<?php } ?>


