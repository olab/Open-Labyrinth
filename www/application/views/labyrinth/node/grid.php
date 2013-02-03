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
    <h1><?php echo __('NodeGrid "') . $templateData['map']->name . '"'; ?></h1>
    <form class="form-horizontal" action="<?php echo URL::base() . 'nodeManager/saveGrid/' . $templateData['map']->id; ?>" method="POST">

        <?php if (isset($templateData['nodes']) and count($templateData['nodes']) > 0) { ?>


                <?php foreach ($templateData['nodes'] as $node) { ?>
    <fieldset class="fieldset">
        <legend>Node with ID <?php echo $node->id; ?> <?php if ($node->type->name == 'root') echo __('(root)'); ?></legend>
            <div class="control-group">
                <label for="title_<?php echo $node->id; ?>" class="control-label"><?php echo __('Title'); ?></label>

                <div class="controls">
                    <input type="text" id="title_<?php echo $node->id; ?>" name="title_<?php echo $node->id; ?>"
                           value="<?php echo $node->title; ?>">
                </div>
            </div>
        <div class="control-group">
            <label for="title_<?php echo $node->id; ?>" class="control-label"><?php echo __('Description'); ?></label>

            <div class="controls">
                <textarea id="text_<?php echo $node->id; ?>"
                          name="text_<?php echo $node->id; ?>"><?php echo $node->text; ?></textarea>
            </div>
        </div>
    </fieldset>

                <?php } ?>
        <?php } ?>
        <input class="btn btn-primary" type="submit" name="Submit" value="<?php echo __('submit'); ?>">


    </form>

<?php } ?>