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
    <h1><?php echo __('Edit node sections for Labyrinth "') . $templateData['map']->name . '"'; ?></h1>
    </div>
    <?php if (isset($templateData['node_sections'])) { ?>
        <table class="table table-striped table-bordered">
            <thead>
            <tr>
                <th>Section title</th>
                <th>Nodes</th>
                <th>Operations</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($templateData['node_sections'] as $nodeSection) { ?>

                <tr>
                    <td>

                            <?php echo $nodeSection->name; ?>


                    </td>
                    <td>
                        <?php if (count($nodeSection->nodes) > 0) { ?>


                        <?php foreach ($nodeSection->nodes as $node) { ?>
                            "<?php echo $node->node->title; ?>" - ID:<?php echo $node->node->id; ?> - order:<?php echo $node->order; ?>

                        <?php } ?><?php } ?>
                    </td>
                    <td>
                        <div class="btn-group">
                        <a class="btn btn-info" href="<?php echo URL::base() . 'nodeManager/editSection/' . $templateData['map']->id . '/' . $nodeSection->id; ?>">
                            <i class="icon-edit"></i>
                            Edit</a>
                            <a class="btn btn-danger" href="<?php echo URL::base() . 'nodeManager/deleteNodeSection/' . $templateData['map']->id . '/' . $nodeSection->id; ?>">
                                <i class="icon-trash"></i>
                                <?php echo __('delete'); ?></a></div></td>
                </tr>

            <?php } ?>
            </tbody>
        </table>
    <?php } ?>


    <form class="form-horizontal" action="<?php echo URL::base() . 'nodeManager/addNodeSection/' . $templateData['map']->id; ?>" method="post">
        <fieldset class="fieldset">
            <legend><?php echo __('Add a node section'); ?></legend>
        <div class="control-group">
            <label for="sectionname" class="control-label"><?php echo __('Title'); ?></label>

            <div class="controls">
                <input type="text" id="sectionname" name="sectionname">
            </div>
        </div>

        </fieldset>

        <div class="form-actions">
        <input class="btn btn-primary" type="submit" value="Add section"></div>
    </form>



    <form class="form-horizontal" action="<?php echo URL::base() . 'nodeManager/updateSection/' . $templateData['map']->id; ?>" method="post">
        <fieldset class="fieldset">
            <legend>Section Headers Visibility</legend>
            <?php if(isset($templateData['sections'])) { ?>

            <div class="control-group">
                <label class="control-label"> <?php echo __('Visibility'); ?></label>

                <div class="controls">
                    <?php foreach ($templateData['sections'] as $section) { ?>

                        <label class="radio"><?php echo $section->name; ?>
                            <input type="radio" name="sectionview" value="<?php echo $section->id; ?>" <?php if ($templateData['map']->section->id == $section->id) echo 'checked=""'; ?>/>
                        </label>
                    <?php } ?>
                </div>
            </div>
            <div class="form-actions">
            <input class="btn btn-primary" type="submit" value="<?php echo __('Save options'); ?>"></div>
            <?php } ?>
       </fieldset>
    </form>

<?php } ?>