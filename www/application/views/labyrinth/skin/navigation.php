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
<ul class="nav nav-pills">
    <li <?php if ($templateData['action'] == 'createSkin') echo 'class="active"'; ?>>
        <a href="<?php echo URL::base().'skinManager/createSkin/'.$templateData['map']->id; ?>">
            <?php echo __('Create a new skin'); ?>
        </a>
    </li>
    <li <?php if ($templateData['action'] == 'editSkins') echo 'class="active"'; ?>>
        <a href="<?php echo URL::base().'skinManager/editSkins/'.$templateData['map']->id; ?>">
            <?php echo __('Edit my skins'); ?>
        </a>
    </li>
    <li <?php if ($templateData['action'] == 'listSkins') echo 'class="active"'; ?>>
        <a href="<?php echo URL::base().'skinManager/listSkins/'.$templateData['map']->id.'/'.$templateData['map']->skin_id; ?>">
            <?php echo __('Select from a list of existing skins'); ?>
        </a>
    </li>
    <li <?php if ($templateData['action'] == 'uploadSkin') echo 'class="active"'; ?>>
        <a href="<?php echo URL::base().'skinManager/uploadSkin/'.$templateData['map']->id; ?>">
            <?php echo __('Upload a new skin'); ?>
        </a>
    </li>
</ul>