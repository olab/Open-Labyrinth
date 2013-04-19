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
    <div class="page-header">
        <h1><?php echo __('Edit users of Labyrinth "') . $templateData['map']->name . '"'; ?></h1>
    </div>

    <h3><?php echo __('Authors'); ?></h3>

    <?php if (isset($templateData['existUsers']) and count($templateData['existUsers']) > 0) { ?>
        <table class="table table-bordered table-striped">
            <thead>
            <tr>
                <th>User</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td><?php echo Auth::instance()->get_user()->nickname; ?></td>
                <td><?php echo __('you cannot remove or add yourself'); ?></td>

            </tr>
            <?php foreach ($templateData['existUsers'] as $exUser) { ?>
                <?php if ($exUser->type->name == 'superuser' or $exUser->type->name == 'author') { ?>
                    <tr>
                        <td><?php echo $exUser->nickname; ?></td>
                        <td><a class="btn btn-danger"
                               href="<?php echo URL::base() . 'mapUserManager/deleteUser/' . $templateData['map']->id . '/' . $exUser->id; ?>">
                                <i class="icon-minus-sign"></i>
                                <?php echo __('Remove'); ?></a>
                        </td>
                    </tr>
                <?php } ?>
            <?php } ?>
            </tbody>
        </table>

    <?php } ?>

    <form class="form-horizontal" method="POST"
          action="<?php echo URL::base() . 'mapUserManager/addUser/' . $templateData['map']->id; ?>">
        <fieldset class="fieldset">
            <div class="control-group">
                <label class="control-label" for="mapuserID">Add author</label>

                <div class="controls">
                    <select id="mapuserID" name="mapuserID">
                        <option value=""><?php echo __('select'); ?> ...</option>
                        <?php if (isset($templateData['admins']) and count($templateData['admins']) > 0) { ?>
                            <?php foreach ($templateData['admins'] as $admin) { ?>
                                <?php if ($admin->id != Auth::instance()->get_user()->id) { ?>
                                    <option value="<?php echo $admin->id; ?>"><?php echo $admin->nickname; ?></option>
                                <?php } ?>
                            <?php } ?>
                        <?php } ?>
                        <?php if (isset($templateData['authors']) and count($templateData['authors']) > 0) { ?>
                            <?php foreach ($templateData['authors'] as $author) { ?>
                                <?php if ($author->id != Auth::instance()->get_user()->id) { ?>
                                    <option value="<?php echo $author->id; ?>"><?php echo $author->nickname; ?></option>
                                <?php } ?>
                            <?php } ?>
                        <?php } ?>
                    </select>
                </div>
            </div>
        </fieldset>
        <div class="form-actions">
            <input class="btn btn-primary" type="submit" name="Submit" value="<?php echo __('Submit'); ?>">
        </div>


    </form>
    <h3>Learners</h3>
    <?php if (isset($templateData['existUsers']) and count($templateData['existUsers']) > 0) { ?>
        <table class="table table-bordered table-striped">
            <thead>
            <tr>
                <th>User</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($templateData['existUsers'] as $exUser) { ?>
                <?php if ($exUser->type->name == 'learner') { ?>
                    <tr>
                        <td><?php echo $exUser->nickname; ?></td>
                        <td>
                            <a class="btn btn-danger" href="<?php echo URL::base() . 'mapUserManager/deleteUser/' . $templateData['map']->id . '/' . $exUser->id; ?>">
                                <i class="icon-minus-sign"></i>Remove</a>
                        </td>
                    </tr>
                <?php } ?>
            <?php } ?>

            </tbody>
        </table>
    <?php } ?>

    <form class="form-horizontal" method="POST"
          action="<?php echo URL::base() . 'mapUserManager/addUser/' . $templateData['map']->id; ?>">
        <fieldset class="fieldset">
            <div class="control-group">
                <label class="control-label" for="mapuserID2">Add learner</label>

                <div class="controls">
                    <select name="mapuserID" id="mapuserID2">
                        <option value=""><?php echo __('select'); ?> ...</option>
                        <?php if (isset($templateData['learners']) and count($templateData['learners']) > 0) { ?>
                            <?php foreach ($templateData['learners'] as $learner) { ?>
                                <?php if ($learner->id != Auth::instance()->get_user()->id) { ?>
                                    <option
                                        value="<?php echo $learner->id; ?>"><?php echo $learner->nickname; ?></option>
                                <?php } ?>
                            <?php } ?>
                        <?php } ?>
                    </select>
                </div>
            </div>
        </fieldset>
        <div class="form-actions"><input class="btn btn-primary" type="submit" name="Submit"
                                         value="<?php echo __('Submit'); ?>"></div>


    </form>

<?php } ?>


