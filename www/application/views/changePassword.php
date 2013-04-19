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
if(isset($templateData['user'])) { ?>

            <h1><?php echo __('Change Labyrinth User Password'); ?></h1>

                        <form class="form-horizontal" action="<?php echo URL::base(); ?>home/updatePassword" method="post" id="form1" name="form1">
                            <fieldset class="fieldset">
                                <legend></legend>
                                <div class="control-group">
                                    <label for="uid_display" class="control-label">User ID</label>

                                    <div class="controls">
                                        <input id="uid_display" type="text" name="uid_display"  disabled="disabled" value="<?php echo $templateData['user']->nickname; ?>">
                                    </div>
                                </div>

                                <div class="control-group">
                                    <label for="upw" class="control-label">Current Password</label>

                                    <div class="controls">
                                        <input type="password" name="upw"  id="upw">
                                    </div>
                                </div>

                                <div class="control-group">
                                    <label for="newpswd" class="control-label">New password</label>

                                    <div class="controls">
                                        <input id="newpswd" type="password" name="newpswd">
                                    </div>
                                </div>

                                <div class="control-group">
                                    <label for="pswd_confirm" class="control-label">Confirm new password</label>

                                    <div class="controls">
                                        <input type="password" name="pswd_confirm" id="pswd_confirm">
                                    </div>
                                </div>
                            </fieldset>



                                <input class="btn btn-primary" type="submit" value="Submit">

                        </form>

<?php } ?>