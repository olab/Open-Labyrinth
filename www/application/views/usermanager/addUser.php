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

                        <form class="form-horizontal" action="<?php echo URL::base().'usermanager/saveNewUser'; ?>" method="post">
                            <fieldset class="fieldset">
                                <legend>Add new user</legend>
                                <div class="control-group">
                                    <label for="uid" class="control-label"><?php echo __('User name'); ?></label>

                                    <div class="controls">
                                        <input id="uid"  type="text" name="uid" value="">

                                    </div>
                                </div>
                                <div class="control-group">
                                    <label for="upw" class="control-label"><?php echo __('Password'); ?></label>

                                    <div class="controls">
                                        <input id="upw"  type="password" name="upw" value="">

                                    </div>
                                </div>
                                <div class="control-group">
                                    <label for="uname" class="control-label"><?php echo __('Nickname'); ?></label>

                                    <div class="controls">
                                        <input id="uname" class="not-autocomplete" type="text" name="uname" >
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label for="uemail" class="control-label"><?php echo __('e-mail'); ?></label>

                                    <div class="controls">
                                        <input class="not-autocomplete" id="uemail" type="text" name="uemail">
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label class="control-label"><?php echo __('language'); ?></label>

                                    <div class="controls">
                                        <label class="radio">
                                            english   <input type="radio" name="langID" value="1" >
                                        </label>
                                        <label class="radio">
                                            francais <input type="radio" name="langID" value="2" >
                                        </label>

                                    </div>
                                </div>
                                <div class="control-group">
                                    <label for="usertype" class="control-label"><?php echo __('user type'); ?></label>

                                    <div class="controls">
                                        <select id="usertype" name="usertype">
                                            <?php if(isset($templateData['types'])) { ?>
                                                <?php foreach($templateData['types'] as $type) { ?>
                                                    <option value="<?php echo $type->id; ?>"><?php echo $type->name; ?></option>
                                                <?php } ?>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </fieldset>

                            <?php if ($templateData['errorMsg'] != NULL){ ?>
                              <?php echo $templateData['errorMsg']; ?>
                                <?php } ?>
                               <input class="btn btn-primary" type="submit" name="SaveNewUserSubmit" value="<?php echo __('submit'); ?>">

                        </form>
                        <a class="btn btn-primary" href=<?php echo URL::base().'usermanager'; ?>><?php echo __('users'); ?></a>
