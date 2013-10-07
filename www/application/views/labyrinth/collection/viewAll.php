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
    <h1><?php echo __('View Collection'); ?></h1>

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
                                <a href="<?php echo URL::base(); ?>renderLabyrinth/index/<?php echo $mp->map->id; ?>"><?php echo $mp->map->name; ?></a>
                            </td>
                            <td>
                                <a href="<?php echo URL::base(); ?>renderLabyrinth/index/<?php echo $mp->map->id; ?>" class="btn btn-success btn-small"><i class="icon-play"></i>Play</a>
                            </td>

                        </tr>
                    <?php } ?>
                    </tbody>
                </table>


            <?php } ?>

<?php } ?>