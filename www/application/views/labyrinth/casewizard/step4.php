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
<table width="100%" height="100%" cellpadding='6'>
    <tr>
        <td valign="top" bgcolor="#bbbbcb">
            <h4><?php echo __('Step 4. Add elements'); ?></h4>
            <div style="width:100%; min-height:400px; background:#FFFFFF; position: relative;">
                <div class="wizard_header">
                    <ul>
                       <li><a href="<?php echo URL::base().'labyrinthManager/caseWizard/4/editNode/'.$templateData['map']; ?>" class="wizard_button small width_auto <?php if ($templateData['action'] == 'editNode') echo 'selected'; ?>">Edit nodes</a></li>
                       <li><a href="<?php echo URL::base().'labyrinthManager/caseWizard/4/addFile/'.$templateData['map']; ?>" class="wizard_button small width_auto <?php if ($templateData['action'] == 'addFile') echo 'selected'; ?>">Add image or file</a></li>
                       <li><a href="<?php echo URL::base().'labyrinthManager/caseWizard/4/addQuestion/'.$templateData['map']; ?>" class="wizard_button small width_auto <?php if ($templateData['action'] == 'addQuestion') echo 'selected'; ?>">Add question</a></li>
                       <li><a href="<?php echo URL::base().'labyrinthManager/caseWizard/4/addAvatar/'.$templateData['map']; ?>" class="wizard_button small width_auto <?php if ($templateData['action'] == 'addAvatar') echo 'selected'; ?>">Add avatar</a></li>
                       <li><a href="<?php echo URL::base().'labyrinthManager/caseWizard/4/addCounter/'.$templateData['map']; ?>" class="wizard_button small width_auto <?php if ($templateData['action'] == 'addCounter') echo 'selected'; ?>">Add counter</a></li>
                    </ul>
                </div>
                <div class="wizard_body">
                    <?php if ($templateData['type'] == 'editNode'){ ?>
                    <div class="instractions">
                        <p class="header">Nodes:</p>
                        <ul class="nodes">
                            <?php foreach($templateData['nodes'] as $node) { ?>
                            <li><a href="<?php echo URL::base().'labyrinthManager/caseWizard/4/editNode/'.$templateData['map'].'/'.$node->id ?>" class="wizard_button small
                            <?php if (isset($templateData['nodeId'])) {
                                if ($templateData['nodeId'] == $node->id) echo 'selected';
                                }
                            ?>
                            "><?php echo $node->title; ?> <?php if($node->type->name == 'root') echo '[root]'; ?> (<?php echo $node->id; ?>)</a></li>
                            <?php } ?>
                        </ul>
                    </div>
                    <div class="node-editor">
                        <?php if (isset($templateData['nodeData'])){
                            echo $templateData['nodeData'];
                        }else {
                            echo 'Choose some node in left column.';
                        } ?>
                    </div>
                    <?php }else{

                    echo $templateData['content'];

                    } ?>
                </div>
                <div class="wizard_footer">
                    <a href="<?php echo URL::base().'labyrinthManager/caseWizard/5/createSkin/'.$templateData['map']; ?>" style="float:right;" class="wizard_button">Step 5 - Edit Skin</a>
                    <a href="<?php echo URL::base(); ?>" style="float:right;" class="wizard_button">Save & return later</a>
                    <a href="<?php echo URL::base().'labyrinthManager/caseWizard/3/'.$templateData['map']; ?>" style="float:left;" class="wizard_button">Return to step 3.</a>
                </div>
            </div>
        </td>
    </tr>
</table>
