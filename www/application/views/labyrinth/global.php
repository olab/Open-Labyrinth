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
if (isset($templateData['map'])) {
    ?>

    <script language="javascript" type="text/javascript"
            src="<?php echo URL::base(); ?>scripts/tinymce/jscripts/tiny_mce/tiny_mce.js"
            xmlns="http://www.w3.org/1999/html"></script>
    <h1><?php echo __('Edit Labyrinth ') . '"' . $templateData['map']->name . '"'; ?></h1>

    <form class="form-horizontal" id="globalMapFrom" name="globalMapFrom" method="post"
          action=<?php echo URL::base() . 'labyrinthManager/saveGlobal/' . $templateData['map']->id; ?>>


    <fieldset class="fieldset">
        <legend><?php echo __('Labyrinth Details'); ?></legend>
        <div class="control-group" title="Please give a short title for your labyrinth.">
            <label class="control-label" for="mtitle"><?php echo __('Labyrinth Title'); ?></label>

            <div class="controls">
                <input name="title" type="text" id="mtitle" class="span6"
                       value="<?php echo $templateData['map']->name; ?>">
            </div>
        </div>

        <div class="control-group" title="Describe your labyrinth in detail">
            <label class="control-label" for="mdesc">
                <?php echo __('Labyrinth Description'); ?></label>

            <div class="controls">
                <textarea name="description" class="span6"
                          id="mdesc"><?php echo $templateData['map']->abstract; ?></textarea>
            </div>
        </div>

        <div class="control-group">
            <label class="control-label" for="keywords"><?php echo __('Labyrinth Keywords'); ?></label>

            <div class="controls">
                <input name="keywords" type="text" id="keywords" class="span6"
                       value="<?php echo $templateData['map']->keywords; ?>">
            </div>
        </div>
        <?php if (isset($templateData['types'])) { ?>
            <div class="control-group">
                <label class="control-label" for="type"><?php echo __('Labyrinth Type'); ?></label>

                <div class="controls">

                    <select name="type" id="type" class="span6">
                        <?php foreach ($templateData['types'] as $type) { ?>
                            <option
                                value="<?php echo $type->id; ?>" <?php if ($type->id == $templateData['map']->type_id) echo 'selected=""'; ?> ><?php echo $type->name; ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
        <?php } ?>


        <?php if (isset($templateData['skins'])) { ?>
            <div class="control-group">
                <label class="control-label" for="skin"><?php echo __('Labyrinth Skin'); ?></label>

                <div class="controls">
                    <select name="skin" id="skin" class="span6">
                        <?php foreach ($templateData['skins'] as $skin) { ?>
                            <option
                                value="<?php echo $skin->id; ?>" <?php if ($skin->id == $templateData['map']->skin_id) echo 'selected=""'; ?>><?php echo $skin->name; ?></option>
                        <?php } ?>
                    </select>

                </div>
            </div>
        <?php } ?>


    </fieldset>


    <fieldset class="fieldset">
        <legend><?php echo __('Labyrinth Contributors'); ?></legend>
        <div class="control-group">
            <label class="control-label">
                <span><?php echo __('Contributors'); ?></span>
                <?php   if (isset($templateData['contributors']) && count($templateData['contributors']) > 0) { ?>


                    <div class="pull-right">
                        <a class="btn btn-info"
                           href=<?php echo URL::base() . 'labyrinthManager/addContributor/' . $templateData['map']->id; ?>>
                            <i class="icon-plus"></i><?php echo __('Add'); ?></a>
                    </div>

                <?php } ?>
            </label>

            <?php   if (!isset($templateData['contributors'])) { ?>


                <div class="controls">
                    <a class="btn btn-info"
                       href=<?php echo URL::base() . 'labyrinthManager/addContributor/' . $templateData['map']->id; ?>>
                        <i class="icon-plus"></i> <?php echo __('Add'); ?></a>
                </div>

            <?php } ?>
            <?php if (isset($templateData['contributors'])) { ?>
                <div class="control-groupper">


                    <?php foreach ($templateData['contributors'] as $contributor) { ?>
                        <div class="control-subgroup">
                            <div class="control-group">
                                <label class="control-label" for="cname_<?php echo $contributor->id; ?>">
                                    <?php echo __('Name'); ?></label>

                                <div class="controls">
                                    <input type="text"

                                           id="cname_<?php echo $contributor->id; ?>"
                                           name="cname_<?php echo $contributor->id; ?>"
                                           value="<?php echo $contributor->name; ?>">
                                </div>
                            </div>
                            <div class="control-group">
                                <label class="control-label" for="cnorg_<?php echo $contributor->id; ?>">
                                    <?php echo __('Organization'); ?></label>

                                <div class="controls">
                                    <input type="text" name="cnorg_<?php echo $contributor->id; ?>"
                                           id="cnorg_<?php echo $contributor->id; ?>"
                                           value="<?php echo $contributor->organization; ?>">
                                </div>
                            </div>

                            <div class="control-group">
                                <label class="control-label" for="role_<?php echo $contributor->id; ?>">
                                    <?php echo __('Role'); ?></label>

                                <div class="controls">
                                    <?php if (isset($templateData['contributor_roles'])) { ?>



                                        <select name="role_<?php echo $contributor->id; ?>"
                                                id="role_<?php echo $contributor->id; ?>">
                                            <?php foreach ($templateData['contributor_roles'] as $role) { ?>
                                                <option
                                                    value="<?php echo $role->id; ?>" <?php if ($role->id == $contributor->role_id) echo 'selected=""'; ?>><?php echo $role->name; ?></option>
                                            <?php } ?>

                                        </select>


                                    <?php } ?>

                                </div>
                            </div>
                            <div class="pull-right">
                                <a class="btn btn-danger"
                                   href="<?php echo URL::base() . 'labyrinthManager/deleteContributor/' . $templateData['map']->id . '/' . $contributor->id; ?>">
                                    <i class="icon-trash"></i><?php echo __('Delete'); ?>
                                </a>
                            </div>
                        </div>

                    <?php } ?>
                </div>
            <?php } ?>
        </div>


        <div class="control-group">
            <label class="control-label"><?php echo __('Registered Labyrinth Authors'); ?>:</label>

            <div class="controls">
                <?php if (isset($templateData['regAuthors'])) { ?>
                    <?php foreach ($templateData['regAuthors'] as $user) { ?>

                        <?php echo $user->nickname . ', '; ?>

                    <?php
                    }
                } ?>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label"><?php echo __('Registered Labyrinth Learners'); ?></label>

            <div class="controls">
                <?php if (isset($templateData['regLearners'])) { ?>
                    <?php foreach ($templateData['regLearners'] as $user) { ?>
                        <?php echo $user->nickname . ', '; ?>
                    <?php
                    }
                } ?>
            </div>
        </div>

    </fieldset>


    <fieldset class="fieldset">
        <legend><?php echo __(' Labyrinth Timing');?> </legend>

        <div class="control-group" title="Select 'On' and define a delta for your labyrinth if you want your learners to
        navigate it in a certain time.">
            <label class="control-label"><?php echo __('Timing'); ?></label>

            <div class="controls">
                <label class="radio">
                    <?php echo __('On'); ?>
                    <input type="radio" name="timing" id="timing-on"
                           value=1 <?php if ($templateData['map']->timing) echo 'checked=""'; ?>>

                    <div class="control-group">
                        <label class="control-label" for="delta_time"><?php echo __('Timing Delta'); ?></label>

                        <div class="controls">
                            <input
                                <?php if (!$templateData['map']->timing) echo 'disabled'; ?>
                                name="delta_time" type="text" class="span1" id="delta_time"
                                value="<?php if ($templateData['map']->delta_time > 0 and $templateData['map']->timing) echo $templateData['map']->delta_time; ?>">
                            <span class="help-inline"><?php echo __('seconds'); ?></span>
                        </div>
                    </div>
                </label>


                <label class="radio">
                    <?php echo __('Off'); ?>
                    <input id="timing-off" type="radio" name="timing"
                           value=0 <?php if (!$templateData['map']->timing) echo 'checked=""'; ?>>
                </label>
            </div>
        </div>


    </fieldset>


    <fieldset class="fieldset">
        <legend><?php echo __('Labyrinth Security'); ?></legend>


        <?php if (isset($templateData['securities'])) { ?>

            <div class="control-group">
                <label class="control-label"><?php echo __('Security'); ?></label>

                <div class="controls">
                    <?php foreach ($templateData['securities'] as $security) { ?>
                        <label class="radio">
                            <input type="radio"
                                   name="security"
                                   value=<?php echo $security->id; ?> <?php if ($security->id == $templateData['map']->security_id) echo 'checked=""'; ?>>
                            <?php echo $security->name; ?>
                        </label>
                    <?php } ?>
                    <a <?php if ($templateData['map']->security_id !== '4') echo 'style="display:none;"'; ?>
                        id="edit_keys" class="btn btn-primary"
                        href="<?php echo URL::base() . 'labyrinthManager/editKeys/' . $templateData['map']->id; ?>">edit
                        keys</a>
                </div>


            </div>
        <?php } ?>




        <?php if (isset($templateData['sections'])) { ?>
            <div class="control-group">
                <label class="control-label"><?php echo __('Section Browsing'); ?></label>

                <div class=" controls">
                    <?php foreach ($templateData['sections'] as $section) { ?>
                        <label class="radio">
                            <input type="radio" name="section" value=<?php echo $section->id; ?>
                                <?php if ($section->id == $templateData['map']->section_id) echo 'checked=""'; ?>/> <?php echo $section->name; ?>
                        </label>
                    <?php } ?>

                </div>

            </div>
        <?php } ?>
    </fieldset>

    <fieldset class="fieldset">
        <legend><?php echo __('Labyrinth Link Function Style'); ?></legend>
        <?php if (isset($templateData['linkStyles'])) { ?>
            <div class="control-group">
                <label class="control-label"><?php echo __('Link Function Style'); ?></label>
                <div class="controls">
                    <?php $isFirst = true; foreach ($templateData['linkStyles'] as $linkStyle) { ?>
                        <label class="radio">
                            <input type="radio"
                                   name="linkStyle"
                                   value=<?php echo $linkStyle->id; ?>>
                            <?php echo $linkStyle->name; ?>
                        </label>
                    <?php } ?>
                </div>
            </div>
        <?php } ?>
    </fieldset>
    <?php
    echo Helper_Controller_Metadata::displayEditor($templateData["map"], "map");?>

    <div class="pull-right">
        <input type="submit" class="btn btn-primary btn-large" name="GlobalSubmit"
               value="<?php echo __('Save changes'); ?>"></div>

    </form>

<?php } ?>
