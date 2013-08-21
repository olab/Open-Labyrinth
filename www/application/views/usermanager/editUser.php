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
<div class="page-header">
    <div class="pull-right">
        <a class="btn btn-danger" href="<?php echo URL::base().'usermanager/deleteUser/'.$templateData['user']->id; ?>">
            <i class="icon-trash"></i>
            <?php echo __('Delete User'); ?></a>
    </div>
    <h1><?php echo __('Edit')." ".__('user account'); ?></h1>
</div>

            <?php if(isset($templateData['user'])) { ?>

                        <form class="form-horizontal" action="<?php echo URL::base().'usermanager/saveOldUser/'.$templateData['user']->id; ?>" method="post">
                                <fieldset class="fieldset">
                                    <legend><?php echo __("User Details");?></legend>
                                    <div class="control-group">
                                        <label class="control-label"><?php echo __('Username'); ?></label>

                                        <div class="controls">
                                            <?php echo $templateData['user']->username; ?>
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <label for="upw" class="control-label"><?php echo __('New password'); ?></label>

                                        <div class="controls">
                                            <input id="upw"  type="password" name="upw" value="">
                                            <span class="help-inline">password will be changed if value is not empty</span>
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <label for="uname" class="control-label"><?php echo __('Name'); ?></label>

                                        <div class="controls">
                                            <input id="uname" class="not-autocomplete" type="text" name="uname"  value="<?php echo $templateData['user']->nickname; ?>">
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <label for="uemail" class="control-label"><?php echo __('e-mail'); ?></label>

                                        <div class="controls">
                                            <input class="not-autocomplete" id="uemail" type="text" name="uemail" value="<?php echo $templateData['user']->email; ?>">
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <label class="control-label"><?php echo __('Language'); ?></label>

                                        <div class="controls">
                                            <label class="radio">
                                                english   <input checked="checked" type="radio" name="langID" value="1" <?php if($templateData['user']->language_id == 1) echo 'checked=""'; ?>>
                                            </label>
                                            <label class="radio">
                                                francais <input type="radio" name="langID" value="2" <?php if($templateData['user']->language_id == 2) echo 'checked=""'; ?>>
                                            </label>

                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <label for="usertype" class="control-label"><?php echo __('User type'); ?></label>

                                        <div class="controls">

                                            <?php
                                            if (Auth::instance()->get_user()->type->name == 'superuser' ) {   ?>

                                            <select id="usertype" name="usertype">
                                                <?php if(isset($templateData['types'])) { ?>
                                                    <?php foreach($templateData['types'] as $type) { ?>
                                                        <option value="<?php echo $type->id; ?>" <?php if($type->id == $templateData['user']->type_id) echo 'selected'; ?>><?php echo $type->name; ?></option>
                                                    <?php } ?>
                                                <?php } ?>
                                            </select>

                                            <?php } else echo Auth::instance()->get_user()->type->name; ?>

                                        </div>
                                    </div>
                                </fieldset>
                            <?php
                            echo Helper_Controller_Metadata::displayEditor($templateData["user"],"user");?>
                                <?php if ($templateData['errorMsg'] != NULL){ ?>
                             <?php echo $templateData['errorMsg']; ?>
                                <?php } ?>

                            <div class="form-actions">
                                <div class="pull-right">
                          <input class="btn btn-primary btn-large" type="submit" name="EditUserSubmit" value="<?php echo __('Save changes'); ?>">
                            </div></div>
                        </form>


            <?php } ?>
