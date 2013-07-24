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
if (isset($templateData['maps'])) { ?>

                <h1><?php echo __('Keys Labyrinths') . ' (' . count($templateData['maps']) . ')'; ?></h1>

                            <table class="table table-bordered tabl-striped">
                                <?php foreach($templateData['maps'] as $map) { ?>
                                    <tr>
                                        <td><?php if(isset($templateData['rootNodeMap']) && isset($templateData['rootNodeMap'][$templateData['map']->id]) && $templateData['rootNodeMap'][$templateData['map']->id] != null) { ?>
                                                <a href="<?php echo URL::base() . 'renderLabyrinth/index/' . $templateData['map']->id; ?>" target="_blank"><?php echo $map->name; ?></a>
                                            <?php } else { ?>
                                                <a class="show-root-error" href="javascript:void(0)"><?php echo $map->name; ?></a>
                                            <?php } ?></td>
                                        <td></td><a href="<?php echo URL::base().'labyrinthManager/editMap/'.$map->id; ?>"><img src="<?php echo URL::base(); ?>images/editl.jpg" border="0" alt="edit"></a></td>
                                        <td><a href="<?php echo URL::base().'openLabyrinth/info/'.$map->id; ?>"><img src="<?php echo URL::base(); ?>images/infol.jpg" border="0" alt="get info"></a></td>
                                        <td>
                                                <?php if(count($map->contributors) > 0) { ?>
                                                <?php foreach($map->contributors as $contributor) { ?>
                                                    <?php echo $contributor->name ?>, (<?php echo $contributor->role->name; ?>)
                                                <?php } ?>
                                                <?php } ?>
                                            </td>
                                        <td><?php echo $map->abstract; ?></td>

                                    </tr>
                                <?php } ?>
                            </table>


<?php } ?>

