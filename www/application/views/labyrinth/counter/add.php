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
<h1><?php echo __('Add Counter'); ?></h1>
<form class="form-horizontal" id="form1" name="form1" method="post" action="<?php echo URL::base().'counterManager/saveNewCounter/'.$templateData['map']->id; ?>">
    <fieldset class="fieldset">
        <legend><?php echo __('Counter Content'); ?></legend>
        <div class="control-group">
            <label class="control-label"><?php echo __('Counter name'); ?></label>
            <div class="controls">
                <input type="text" name="cName" value="" />
            </div>
        </div>
        <div class="control-group">
            <label class="control-label"><?php echo __('Counter description (optional)'); ?></label>
            <div class="controls">
                <textarea name="cDesc" rows="6" cols="40"></textarea>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label"><?php echo __('Counter image (optional)'); ?></label>
            <div class="controls">
                <select name="cIconId">
                    <option value="" selected="">no image</option>
                    <?php if(isset($templateData['images']) and count($templateData['images']) > 0) { ?>
                    <?php foreach($templateData['images'] as $image) { ?>
                        <option value="<?php echo $image->id; ?>"><?php echo $image->name; ?> (ID:<?php echo $image->id; ?>)</option>
                        <?php } ?>
                    <?php } ?>
                </select>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label"><?php echo __('Starting value (optional)'); ?></label>
            <div class="controls">
                <input type="text" name="cStartV" size="4" value="" />
            </div>
        </div>
        <div class="control-group">
            <label class="control-label"><?php echo __('Visible'); ?></label>
            <div class="controls">
                <select name="cVisible">
                    <option value="1"><?php echo __('show'); ?></option>
                    <option value="0"><?php echo __("don't show"); ?></option>
                    <option value="2"><?php echo __('custom'); ?></option>
                </select>
            </div>
        </div>
    </fieldset>
    <div class="control-group">
        <input class="btn btn-primary" type="submit" name="Submit" value="Submit" />
    </div>
</form>
<?php } ?>