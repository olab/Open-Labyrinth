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
if (isset($templateData['collection'])) {
    ?>
    <h1><?php echo __('edit Collection'); ?></h1>

    <form method="POST"
          action="<?php echo URL::base(); ?>collectionManager/updateName/<?php echo $templateData['collection']->id; ?>">


        <fieldset class="fieldset">

            <div class="control-group">
                <label for="colname" class="control-label"><?php echo __('Collection name'); ?></label>

                <div class="controls">
                    <input type="text" name="colname" value="<?php if (isset($templateData['collection'])) echo $templateData['collection']->name; ?>" id="colname">

                </div>
            </div>

        </fieldset>
        <input class="btn btn-primary" type="submit" value="<?php echo __('save'); ?>">
    </form>
    <form method="POST"
          action="<?php echo URL::base(); ?>collectionManager/addMap/<?php echo $templateData['collection']->id; ?>">


        <fieldset class="fieldset">
            <legend>Labyrinths in Collection</legend>

            <?php if (count($templateData['collection']->maps) > 0) { ?>
                <table class="table table-striped table-bordered">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($templateData['collection']->maps as $mp) { ?>
                        <tr>
                            <td>
                                <a href="<?php echo URL::base(); ?>labyrinthManager/editMap/<?php echo $mp->map->id; ?>"><?php echo $mp->map->name; ?></a>
                            </td>
                            <td>
                                <a class="btn btn-primary"
                                   href="<?php echo URL::base(); ?>labyrinthManager/editMap/<?php echo $mp->map->id; ?>"><?php echo __('Edit'); ?></a>
                                <a class="btn btn-primary"
                                   href="<?php echo URL::base(); ?>collectionManager/deleteMap/<?php echo $templateData['collection']->id; ?>/<?php echo $mp->map->id; ?>"><?php echo __('Delete'); ?></a>
                            </td>

                        </tr>
                    <?php } ?>
                    </tbody>
                </table>


            <?php } ?>


            <div class="control-group">
                <label for="mapid" class="control-label"><?php echo __('Add labyrinth to collection'); ?></label>

                <div class="controls">
                    <select name="mapid" id="mapid">
                        <?php if (isset($templateData['maps']) and count($templateData['maps']) > 0) { ?>
                            <?php foreach ($templateData['maps'] as $map) { ?>
                                <option value="<?php echo $map->id; ?>"><?php echo $map->name; ?></option>
                            <?php } ?>
                        <?php } ?>
                    </select>

                </div>
            </div>

        </fieldset>
        <input class="btn btn-primary" type="submit" value="<?php echo __('add'); ?>">
    </form>

<?php } ?>