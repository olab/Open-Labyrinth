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

            <h1><?php echo __('Vue to Labyrinth upload'); ?></h1>

                        <form class="form-horizontal" method="POST" enctype="multipart/form-data" action="<?php echo URL::base(); ?>exportImportManager/uploadVUE">

                            <div class="control-group">
                                <label for="mapname"  class="control-label"><?php echo __('Labyrinth name'); ?>
                                </label>
                                <div class="controls">
                                    <input type="text" id="mapname" name="mapname"/>
                                </div>
                            </div>

                            <div class="control-group">
                                <label class="control-label"><?php echo __('Select Vue file'); ?>
                                </label>
                                <div class="controls">
                                    <input type="file"  name="filename">
                                </div>
                            </div>




                            <input class="btn btn-primary" type="submit" name="Submit" value="<?php echo __('submit'); ?>">
                        </form>


                        <p><?php echo __('Vue is a free visual concept-mapping tool from Tufts University for both Windows and Mac. You can use it to create designs for Labyrinth by creating boxes and links between them. Note that although Vue supports many other features only the boxes, text and links will be imported'); ?></p>
                        <p><?php echo __('On import each box becomes a Labyrinth node and every line between boxes will become a link. Make sure you make your arrows between boxes point in the right direction as these are parsed in the upload process - directionless arrows are interpreted as bidirectional (from A to B and from B to A).'); ?></p>
                        <p><a href="http://vue.uit.tufts.edu/" target="_blank"><?php echo __('get Vue here'); ?></a></p>


