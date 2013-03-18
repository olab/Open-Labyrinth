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

<h4><?php echo __('Step 5. Edit Skin'); ?></h4>
<div>
    <div>
        <ul class="nav nav-pills">
            <li class="<?php if ($templateData['action'] == 'createSkin') echo 'active'; ?>"><a href="<?php echo URL::base() . 'labyrinthManager/caseWizard/6/createSkin/' . $templateData['map']->id; ?>" >Create a new skin</a></li>
            <li class="<?php if ($templateData['action'] == 'listSkins') echo 'active'; ?>"><a href="<?php echo URL::base() . 'labyrinthManager/caseWizard/6/listSkins/' . $templateData['map']->id . '/' . $templateData['map']->skin_id; ?>" >Select from a list of existing skins</a></li>
            <li class="<?php if ($templateData['action'] == 'uploadSkin') echo 'active'; ?>"><a href="<?php echo URL::base() . 'labyrinthManager/caseWizard/6/uploadSkin/' . $templateData['map']->id; ?>" >Upload a new skin</a></li>
        </ul>
    </div>
    <div class="wizard_body">
        <?php
        if ($templateData['action'] == 'createSkin') {
            if ($templateData['result'] == 'done') {
                ?>
                <div>
                    Creating of a new skin is completed successfully. Click "Save & Finish"
                </div>
            <?php } else { ?>
                <div>
                    Click <a style="text-decoration: underline;" href="<?php echo URL::base() . 'labyrinthManager/caseWizard/6/createNewSkin/' . $templateData['map']->id; ?>">here</a> to create new skin
                </div>
                <?php
            }
        } else {
            echo $templateData['content'];
        }
        ?>
    </div>
    <div>
        <a href="<?php echo URL::base(); ?>" style="float:right;" class="btn btn-primary">Save & Finish</a>
        <a href="<?php echo URL::base(); ?>" style="float:right;" class="btn btn-primary">Save & return later</a>
        <a href="<?php echo URL::base() . 'labyrinthManager/caseWizard/5/editNode/' . $templateData['map']->id; ?>" class="btn btn-primary">Return to step 5.</a>
    </div>
</div>

