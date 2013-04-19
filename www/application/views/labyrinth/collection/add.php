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
<h1><?php echo __('Add Collection'); ?></h1>
<form method="POST" action="<?php echo URL::base(); ?>collectionManager/saveNewCollection">



    <fieldset class="fieldset">

        <div class="control-group">
            <label for="colname" class="control-label"><?php echo __('Collection name'); ?></label>

            <div class="controls">
                <input type="text" name="colname" value="" id="colname">

            </div>
        </div>

    </fieldset> <input class="btn btn-primary" type="submit" value="<?php echo __('submit'); ?>">
</form>



