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

            <h1><?php echo __('MVP to Labyrinth upload'); ?></h1>

                <form class="form-horizontal" action="<?php echo URL::base(); ?>exportImportManager/uploadMVP" enctype="multipart/form-data" method="POST">
                    <?php if(count(Notice::get()) > 0) { ?>
                    <div class="alert alert-success">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        <?php $m = Notice::get(); echo $m[0]; ?>
                    </div>
                    <?php } ?>
                    <fieldset class="fieldset">
                        <div class="control-group">
                            <label class="control-label"><?php echo __('File to upload'); ?>
                            </label>
                            <div class="controls">
                                <input type="file" name="filename">
                                <div><span style="font-size: 14px; color: #333333;"><?php echo __('The maximum file upload size is 20.00 MB.'); ?></span></div>
                            </div>
                        </div>


                    </fieldset>
                    <div class="form-actions">
                        <div class="pull-right">
                            <input class="btn btn-large btn-primary" type="submit" value="<?php echo __('Upload'); ?>" name="Submit">
                        </div>
                    </div>



                </form>

                    <img width="105" height="47" id="Img2" alt="MVP" src="<?php echo URL::base(); ?>images/medbiq_logo.gif">
              <p><?php echo __('OpenLabyrinth imports and exports to the MedBiquitous virtual patient data specification. For more information see'); ?> <a target="_blank" style="text-decoration: underline;" href="http://www.medbiq.org/working_groups/virtual_patient/index.html"><?php echo __('MedBiquitous VPWG'); ?></a>.
                </p>



