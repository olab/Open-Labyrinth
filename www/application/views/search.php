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

            <h1><?php echo __('Search on term "'); ?><?php if(isset($term)) echo $term; ?>"</h1>

                       <div>Found <?php if(isset($maps)) { echo count($maps); } else { echo 0; } ?> labyrinths</div>
                        <table class="table table-striped table-bordered">
                            <?php if(isset($maps) and count($maps) > 0) { ?>
                            <?php foreach($maps as $map) { ?>
                            <tr>
                                <td><a href="<?php echo URL::base(); ?>renderLabyrinth/index/<?php echo $map->id; ?>"><?php echo $map->name; ?></a></td>
                                <td><a class="btn btn-primary" href="<?php echo URL::base(); ?>labyrinthManager/editMap/<?php echo $map->id; ?>">edit</a></td>
                                <td><a class="btn btn-info" href="<?php echo URL::base(); ?>openLabyrinth/info/<?php echo $map->id; ?>">info</a></td>
                                <td><p><?php if(count($map->contributors) > 0) { ?>
                                                <?php foreach($map->contributors as $contributor) { ?>
                                                    <?php echo $contributor->name ?>, (<?php echo $contributor->role->name; ?>)
                                                <?php } ?>
                                                <?php } ?>
                                    </p></td>
                                <td><p><?php echo $map->abstract; ?></p></td>
                            </tr>
                            <?php } ?>
                            <?php } ?>
                        </table>
