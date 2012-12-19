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
<h1><?php echo __('Edit skin of Labyrinth "').$templateData['map']->name.'"'; ?></h1>
<div class="member-box round-all">
    <?php echo $templateData['navigation']; ?>
    <form class="form-horizontal" id="form1" name="form1" method="post" action="<?php echo URL::base().'skinManager/saveSkin/'.$templateData['map']->id; ?>">
        <fieldset class="fieldset">
            <legend><?php echo __('Create a new skin'); ?></legend>
            <div class="control-group">
                <label class="control-label" for="skin_name"><?php echo __('Enter skin name:'); ?></label>
                <div class="controls">
                    <input id="skin_name" class="not-autocomplete" autocomplete="off" type="text" name="skin_name" value="" />
                </div>
            </div>
            <div class="control-group">
                <div class="controls">
                    <input class="btn btn-primary" type="submit" name="save" value="<?php echo __('Save name and go to skin editor'); ?>" />
                </div>
            </div>
        </fieldset>
    </form>
</div>
<?php } ?>