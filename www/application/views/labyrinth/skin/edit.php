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
    $haveOne = false;
?>
<h1><?php echo __('Edit skin of Labyrinth "').$templateData['map']->name.'"'; ?></h1>
<div class="member-box round-all">
    <?php echo $templateData['navigation']; ?>
    <form class="form-horizontal" id="form1" name="form1" method="post" action="<?php echo URL::base().'skinManager/skinsSaveChanges/'.$templateData['map']->id; ?>">
        <fieldset class="fieldset">
            <legend><?php echo __('Edit my skins'); ?></legend>
            <?php if ($templateData['skinError'] != NULL){ ?>
            <div class="alert alert-error">
                <button data-dismiss="alert" class="close" type="button">&times;</button>
                <strong><?php echo __('Error!') ?></strong>&nbsp;<?php echo $templateData['skinError']; ?>
            </div>
            <?php } ?>
            <div class="control-group">
                <label class="control-label"><?php echo __('Name:'); ?></label>
                <div class="controls">
                    <input class="not-autocomplete" type="text" name="name" value="<?php echo $templateData['skinData']->name; ?>" />
                </div>
            </div>
            <div class="control-group">
                <label class="control-label"><?php echo __('CSS file:'); ?></label>
                <div class="controls">
                    <textarea class="not-autocomplete" name="css" style="width:600px; height:300px;"><?php echo $templateData['css_content']; ?></textarea>
                </div>
            </div>
            <div class="control-group">
                <div class="controls">
                    <input class="btn btn-primary" type="submit" name="Submit" value="<?php echo __('Save changes'); ?>" />
                </div>
            </div>
        </fieldset>
        <input type="hidden" name="skinId" value="<?php echo $templateData['skinData']->id; ?>" />
    </form>
</div>
<?php } ?>