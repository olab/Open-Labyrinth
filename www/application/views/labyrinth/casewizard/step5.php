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
?>
<h1><?php echo __('Step 4. Add elements'); ?></h1>
<div>
    <div>
        <ul class="nav nav-pills">
            <li class="<?php if ($templateData['action'] == 'editNode') echo 'active'; ?>"><a
                    href="<?php echo URL::base() . 'labyrinthManager/caseWizard/4/editNode/' . $templateData['map']; ?>">Edit
                    nodes</a></li>
            <li class="<?php if ($templateData['action'] == 'addFile') echo 'active'; ?>"><a
                    href="<?php echo URL::base() . 'labyrinthManager/caseWizard/4/addFile/' . $templateData['map']; ?>">Add
                    image or file</a></li>
            <li class="<?php if ($templateData['action'] == 'addQuestion') echo 'active'; ?>"><a
                    href="<?php echo URL::base() . 'labyrinthManager/caseWizard/4/addQuestion/' . $templateData['map']; ?>">Add
                    question</a></li>
            <li class="<?php if ($templateData['action'] == 'addAvatar') echo 'active'; ?>"><a
                    href="<?php echo URL::base() . 'labyrinthManager/caseWizard/4/addAvatar/' . $templateData['map']; ?>">Add
                    avatar</a></li>
            <li class="<?php if ($templateData['action'] == 'addCounter') echo 'active'; ?>"><a
                    href="<?php echo URL::base() . 'labyrinthManager/caseWizard/4/addCounter/' . $templateData['map']; ?>">Add
                    counter</a></li>
        </ul>
    </div>
    <div class="container-fluid">
        <div class="row-fluid">
            <?php if ($templateData['type'] == 'editNode') { ?>
                <div class="span2">
                    <p class="header">Nodes:</p>
                    <ul class="nav nav-tabs nav-stacked">
                        <?php foreach ($templateData['nodes'] as $node) { ?>
                            <li>
                                <a href="<?php echo URL::base() . 'labyrinthManager/caseWizard/4/editNode/' . $templateData['map'] . '/' . $node->id ?>"
                                   class="
                            <?php if (isset($templateData['nodeId'])) {
                                       if ($templateData['nodeId'] == $node->id) echo 'selected';
                                   }
                                   ?>
                            "><?php echo $node->title; ?> <?php if ($node->type->name == 'root') echo '[root]'; ?>
                                    (<?php echo $node->id; ?>)</a></li>
                        <?php } ?>
                    </ul>
                </div>
                <div class="node-editor span10">
                    <?php if (isset($templateData['nodeData'])) {
                        echo $templateData['nodeData'];
                    } else {
                        echo 'Choose some node in left column.';
                    } ?>
                </div>
            <?php
            } else {
                echo $templateData['content'];
            } ?>
        </div>
        <div class="controls">
            <a  href="<?php echo URL::base() . 'labyrinthManager/caseWizard/6/createSkin/' . $templateData['map']; ?>"
               style="float:right;" class="btn btn-primary">Step 6 - Edit Skin</a>
            <a class="btn btn-primary" href="<?php echo URL::base(); ?>" style="float:right;" class="wizard_button">Save & return later</a>
            <a class="btn btn-primary" href="<?php echo URL::base() . 'labyrinthManager/caseWizard/4/' . $templateData['map']; ?>"
               style="float:left;" class="wizard_button">Return to step 4.</a>
        </div>
    </div>
</div>
