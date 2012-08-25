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
            <h4><?php echo __('Step 5. Edit Skin'); ?></h4>
            <div style="width:100%; min-height:400px; background:#FFFFFF; position: relative;">
                <div class="wizard_header">
                    <ul>
                        <li><a href="<?php echo URL::base().'labyrinthManager/caseWizard/5/createSkin/'.$templateData['map']; ?>" class="wizard_button small width_auto selected">Create a new skin</a></li>
                        <!--<li><a href="<?php echo URL::base().'labyrinthManager/caseWizard/5/uploadSkin/'; ?>" class="wizard_button small width_auto">Upload a new skin</a></li>
                        <li><a href="<?php echo URL::base().'labyrinthManager/caseWizard/5/listSkins/'; ?>" class="wizard_button small width_auto">Select from a list of existing skins</a></li>-->
                    </ul>
                </div>
                <div class="wizard_body">
                    <?php if ($templateData['result'] == 'done'){ ?>
                    <div style="text-align: center; position: relative; top:100px;">
                        Creating of a new skin is complited successfully. Click "Save & Finish"
                    </div>
                    <?php }else{ ?>
                    <div style="text-align: center; position: relative; top:100px;">
                        Click <a style="text-decoration: underline;" href="<?php echo URL::base().'labyrinthManager/caseWizard/5/createNewSkin/'.$templateData['map']; ?>">here</a> to create new skin
                    </div>
                    <?php } ?>
                </div>
                <div class="wizard_footer">
                    <a href="<?php echo URL::base(); ?>" style="float:right;" class="wizard_button">Save & Finish</a>
                    <a href="<?php echo URL::base(); ?>" style="float:right;" class="wizard_button">Save & return later</a>
                    <a href="<?php echo URL::base().'labyrinthManager/caseWizard/4/editNode/'.$templateData['map']; ?>" style="float:left;" class="wizard_button">Return to step 4.</a>
                </div>
            </div>
        </td>
    </tr>
</table>
