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


if (isset($templateData['map'])) { ?>
    <link rel="stylesheet" type="text/css"
          href="http://ajax.aspnetcdn.com/ajax/jquery.dataTables/1.9.4/css/jquery.dataTables.css"/>
    <script type="text/javascript" charset="utf8"
            src="http://ajax.aspnetcdn.com/ajax/jquery.dataTables/1.9.4/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="<?php echo URL::base(); ?>scripts/olab/dataTablesTB.js"></script>

    <div class="page-header">
        <h1><?php echo __('Edit users of Labyrinth "') . $templateData['map']->name . '"'; ?></h1>
    </div>
    <form class="form-horizontal" method="POST"
          action="<?php echo URL::base() . 'mapUserManager/addUser/' . $templateData['map']->id . (isset($templateData['authorOrder']) ? '/'.$templateData['authorOrder'] : '') . (isset($templateData['learnerOrder']) ? '/'.$templateData['learnerOrder'] : ''); ?>">

    <h3><?php echo __('Authors'); ?></h3>
        <div class="btn-group users" style="margin-bottom: 10px">
            <a class="btn btn-primary" href="<?php echo URL::base(); ?>mapUserManager/addAllAuthors/<?php echo $templateData['map']->id; ?>/<?php echo $templateData['authorOrder']; ?>/<?php echo $templateData['learnerOrder']; ?>">Add All</a>
            <a class="btn btn-danger" href="<?php echo URL::base(); ?>mapUserManager/removeAllAuthors/<?php echo $templateData['map']->id; ?>/<?php echo $templateData['authorOrder']; ?>/<?php echo $templateData['learnerOrder']; ?>">Remove All</a>
        </div>

        <table class="table table-bordered table-striped">
            <colgroup>
                <col style="width: 5%" />
                <col style="width: 80%" />
            </colgroup>
            <thead>
            <tr>
                <th style="text-align: center">Actions</th>
                <th>
                    <a href="<?php echo URL::base(); ?>mapUserManager/index/<?php echo $templateData['map']->id; ?>/<?php echo $templateData['authorOrder'] == 0 ? 1 : 0; ?>/<?php echo $templateData['learnerOrder']; ?>">
                        Users <div class="pull-right"><i class="icon-chevron-<?php if($templateData['authorOrder'] == 1) echo 'down';  else  echo 'up'; ?> icon-white"></i></div>
                    </a>
                </th>
            </tr>
            </thead>
            <tbody>
            <?php if(isset($templateData['existAuthors']) and count($templateData['existAuthors']) > 0) { ?>
                <?php foreach($templateData['existAuthors'] as $author) { ?>
                    <tr>
                        <td style="text-align: center"><input type="checkbox" name="user<?php echo $author->id; ?>" checked="checked"></td>
                        <td><?php echo $author->nickname; ?></td>
                    </tr>
                <?php } ?>
            <?php } ?>
            <?php if(isset($templateData['allAdmins']) and count($templateData['allAdmins']) > 0) { ?>
                <?php foreach($templateData['allAdmins'] as $admin) { ?>
                    <?php if($admin->id == Auth::instance()->get_user()->id) continue; ?>
                    <tr>
                        <td style="text-align: center"><input type="checkbox" name="user<?php echo $admin->id; ?>"></td>
                        <td><?php echo $admin->nickname; ?></td>
                    </tr>
                <?php } ?>
            <?php } ?>
            </tbody>
        </table>

    <h3>Learners</h3>
        <div class="btn-group users" style="margin-bottom: 10px">
            <a class="btn btn-primary" href="<?php echo URL::base(); ?>mapUserManager/addAllLearners/<?php echo $templateData['map']->id; ?>/<?php echo $templateData['authorOrder']; ?>/<?php echo $templateData['learnerOrder']; ?>">Add All</a>
            <a class="btn btn-danger" href="<?php echo URL::base(); ?>mapUserManager/removeAllLearners/<?php echo $templateData['map']->id; ?>/<?php echo $templateData['authorOrder']; ?>/<?php echo $templateData['learnerOrder']; ?>">Remove All</a>
        </div>

    <table class="table table-bordered table-striped">
        <colgroup>
            <col style="width: 5%" />
            <col style="width: 80%" />
        </colgroup>
        <thead>
        <tr>
            <th style="text-align: center">Actions</th>
            <th>
                <a href="<?php echo URL::base(); ?>mapUserManager/index/<?php echo $templateData['map']->id; ?>/<?php echo $templateData['authorOrder']; ?>/<?php echo $templateData['learnerOrder'] == 0 ? 1 : 0; ?>">
                    Users <div class="pull-right"><i class="icon-chevron-<?php if($templateData['learnerOrder'] == 1) echo 'down';  else  echo 'up'; ?> icon-white"></i></div>
                </a>
            </th>
        </tr>
        </thead>
        <tbody>
        <?php if(isset($templateData['existLearners']) and count($templateData['existLearners']) > 0) { ?>
            <?php foreach($templateData['existLearners'] as $learner) { ?>
                <tr>
                    <td style="text-align: center"><input type="checkbox" name="user<?php echo $learner->id; ?>" checked="checked"></td>
                    <td><?php echo $learner->nickname; ?></td>
                </tr>
            <?php } ?>
        <?php } ?>
        <?php if(isset($templateData['learners']) and count($templateData['learners']) > 0) { ?>
            <?php foreach($templateData['learners'] as $learner) { ?>
                <tr>
                    <td style="text-align: center"><input type="checkbox" name="user<?php echo $learner->id; ?>"></td>
                    <td><?php echo $learner->nickname; ?></td>
                </tr>
            <?php } ?>
        <?php } ?>
        </tbody>
    </table>

        <div class="pull-right"><input type="submit" class="btn btn-primary btn-large" name="GlobalSubmit" value="<?php echo __('Save changes'); ?>"></div>
    </form>
<?php } ?>