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
if (isset($templateData['map'])) { ?>
<h3><?php echo __('Add Counter'); ?></h3>
<form id="form1" name="form1" method="post" action="<?php echo URL::base().'labyrinthManager/caseWizard/5/saveNewCounter/'.$templateData['map']->id; ?>" class="form-horizontal">
    <fieldset class="fieldset">
        <div class="control-group">
            <label for="cName"  class="control-label"><?php echo __('Counter Name'); ?>
            </label>
            <div class="controls">
                <input type="text" id="cName" name="cName"  value="">
            </div>
        </div>

        <div class="control-group">
            <label for="cDesc"  class="control-label"><?php echo __('Counter description (optional)'); ?>
            </label>
            <div class="controls">
                <textarea name="cDesc" id="cDesc" rows="6" cols="40"></textarea>
            </div>
        </div>

        <div class="control-group">
            <label for="cIconId"  class="control-label"><?php echo __('Counter image (optional'); ?>
            </label>
            <div class="controls">
                <select name="cIconId" id="cIconId">
                    <option value="" selected="">no image</option><?php
                    foreach(Arr::get($templateData, 'images', array()) as $image) { ?>
                    <option value="<?php echo $image->id; ?>"><?php echo $image->name; ?> (ID:<?php echo $image->id; ?>)</option><?php
                    } ?>
                </select>
            </div>
        </div>

        <div class="control-group">
            <label for="cStartV"  class="control-label"><?php echo __('Starting value (optional)'); ?>
            </label>
            <div class="controls">
                <input type="text" name="cStartV" id="cStartV" size="4" value="">
            </div>
        </div>

        <div class="control-group">
            <label for="cVisible"  class="control-label"><?php echo __('Visibility'); ?>
            </label>
            <div class="controls">
                <select id="cVisible" name="cVisible"><option value="1" selected=""><?php echo __('show'); ?></option><option value="0"><?php echo __('don\'t show'); ?></option></select>
            </div>
        </div>
    </fieldset>
    <input class="btn btn-primary" type="submit" name="Submit" value="<?php echo __('submit'); ?>">
</form><?php
} ?>