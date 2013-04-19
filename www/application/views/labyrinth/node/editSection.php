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
if (isset($templateData['section']) and isset($templateData['map'])) {
    ?>

    <div class="page-header">
    <h1><?php echo __('Edit node sections "') . $templateData['section']->name . '"'; ?></h1></div>

    <form class="form-horizontal"
        action="<?php echo URL::base() . 'nodeManager/updateNodeSection/' . $templateData['map']->id . '/' . $templateData['section']->id; ?>"
        method="post">
        <fieldset>
            <legend>Section Details</legend>
        <div class="control-group">
            <label for="sectiontitle" class="control-label"><?php echo __('Section title'); ?>
            </label>
            <div class="controls">
                <input id="sectiontitle" type="text" name="sectiontitle" class="span6"
                       value="<?php echo $templateData['section']->name; ?>">

            </div>
        </div>
        <div class="form-actions">
        <input class="btn btn-primary" type="submit" name="Submit" value="<?php echo __('Save details'); ?>"></div>
        </fieldset>
    </form>

    <form class="form-horizontal"
        action="<?php echo URL::base() . 'nodeManager/updateSectionNodes/' . $templateData['map']->id . '/' . $templateData['section']->id; ?>"
        method="post">

        <fieldset><legend><?php echo __('Section Nodes'); ?></legend>


            <?php if (count($templateData['section']->nodes) > 0) { ?>
                <?php foreach ($templateData['section']->nodes as $node) { ?>


                    <div class="control-group">
                        <label for="node_<?php echo $node->id; ?>" class="control-label"> <?php echo $node->node->title; ?>
                            - <?php echo __('node conditional'); ?>: <?php echo $node->order; ?> - <?php echo __('ordered'); ?>
                        </label>
                        <div class="controls">
                            <div class="btn-group">
                            <select  id="node_<?php echo $node->id; ?>" name="node_<?php echo $node->id; ?>">
                                <?php for ($i = 0; $i < count($templateData['section']->nodes); $i++) { ?>
                                    <option
                                        value="<?php echo $i; ?>" <?php if ($i == $node->order) echo 'selected=""'; ?>><?php echo $i; ?></option>
                                <?php } ?>
                            </select><a class="btn btn-danger"
                                href="<?php echo URL::base() . 'nodeManager/deleteNodeBySection/' . $templateData['map']->id . '/' . $templateData['section']->id . '/' . $node->node->id; ?>">
                                    <i class="icon-trash"></i>
                                    <?php echo __('Remove'); ?></a>
                        </div>
                        </div>
                    </div>

                <?php } ?>
            <?php } ?>
        </fieldset>
        <div class="form-actions">
        <input class="btn btn-primary" type="submit" name="Submit" value="<?php echo __('Save order'); ?>"></div>

    </form>



    <form class="form-horizontal"
        action="<?php echo URL::base() . 'nodeManager/addNodeInSection/' . $templateData['map']->id . '/' . $templateData['section']->id; ?>"
        method="post">

        <fieldset>
            <legend><?php echo __("Add node to section") ?></legend>
            <div class="control-group">
                <label for="mnodeID" class="control-label"><?php echo __('Add an unallocated node to this section'); ?>
                </label>
                <div class="controls">
                    <select name="mnodeID" id="mnodeID">
                        <?php if (isset($templateData['nodes']) and count($templateData['nodes']) > 0) { ?>
                            <?php foreach ($templateData['nodes'] as $node) { ?>
                                <option value="<?php echo $node->id; ?>"><?php echo $node->title; ?></option>
                            <?php } ?>
                        <?php } ?>
                    </select>

                </div>
            </div>






        </fieldset>
<div class="form-actions">
        <input class="btn btn-primary" type="submit" name="Submit" value="<?php echo __('Add node'); ?>"></div>

    </form>

<?php } ?>