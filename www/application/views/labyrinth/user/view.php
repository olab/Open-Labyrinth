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
    <link rel="stylesheet" type="text/css" href="http://ajax.aspnetcdn.com/ajax/jquery.dataTables/1.9.4/css/jquery.dataTables.css"/>
    <script type="text/javascript" charset="utf8" src="http://ajax.aspnetcdn.com/ajax/jquery.dataTables/1.9.4/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="<?php echo URL::base().'scripts/olab/dataTablesTB.js'; ?>"></script>

    <div class="page-header"><h1><?php echo __('Edit users of Labyrinth "').$templateData['map']->name.'"'; ?></h1></div>

    <form class="form-horizontal" method="POST" action="<?php echo URL::base().'mapUserManager/addUser/'.$templateData['map']->id.(isset($templateData['authorOrder']) ? '/'.$templateData['authorOrder'] : '') . (isset($templateData['learnerOrder']) ? '/'.$templateData['learnerOrder'] : ''); ?>">
        <div class="pull-right"><input type="submit" class="btn btn-primary btn-large" name="GlobalSubmit" value="<?php echo __('Save changes'); ?>"></div>

        <h3><?php echo __('Groups'); ?></h3>
        <div class="btn-group users" style="margin-bottom: 10px">
            <a class="btn btn-primary" href="<?php echo URL::base(); ?>mapUserManager/addAllGroups/<?php echo $templateData['map']->id; ?>/<?php echo $templateData['authorOrder']; ?>/<?php echo $templateData['learnerOrder']; ?>">Add All</a>
            <a class="btn btn-danger" href="<?php echo URL::base(); ?>mapUserManager/removeAllGroups/<?php echo $templateData['map']->id; ?>/<?php echo $templateData['authorOrder']; ?>/<?php echo $templateData['learnerOrder']; ?>">Remove All</a>
        </div>
        <table id="groups" class="table table-striped table-bordered">
            <colgroup>
                <col style="width: 5%" />
                <col style="width: 90%" />
            </colgroup>
            <thead>
            <tr>
                <th style="text-align: center">Actions</th>
                <th>
                    <a href="<?php echo URL::base().'mapUserManager/index/'.$templateData['map']->id.'/'.$templateData['authorOrder'].'/'.$templateData['learnerOrder'].'/'.$templateData['reviewerOrder'].'/'.(1 - $templateData['groupOrder']); ?>">
                        Users <div class="pull-right"><i class="icon-chevron-<?php if($templateData['groupOrder'] == 1) echo 'down';  else  echo 'up'; ?> icon-white"></i></div>
                </th>
            </tr>
            </thead>
            <tbody>
            <?php if(isset($templateData['groups']) and count($templateData['groups']) > 0) { ?>
                <?php foreach($templateData['groups'] as $group) { ?>
                    <tr>
                        <td style="text-align: center">
                            <input type="checkbox" name="groups[]" value="<?php echo $group->id; ?>" <?php if(in_array($group->id, $templateData['existGroupsIds'])) echo 'checked' ?>>
                        </td>
                        <td><?php echo $group->name; ?></td>
                    </tr>
                <?php } ?>
            <?php } ?>
            </tbody>
        </table>

        <div class="pull-right"><input type="submit" class="btn btn-primary btn-large" name="GlobalSubmit" value="<?php echo __('Save changes'); ?>"></div>

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
                    <a href="<?php echo URL::base().'mapUserManager/index/'.$templateData['map']->id.'/'.(1 - $templateData['authorOrder']).'/'.$templateData['learnerOrder'].'/'.$templateData['reviewerOrder'].'/'.$templateData['groupOrder']; ?>">
                        Users <div class="pull-right"><i class="icon-chevron-<?php if($templateData['authorOrder'] == 1) echo 'down';  else  echo 'up'; ?> icon-white"></i></div>
                    </a>
                </th>
            </tr>
            </thead>
            <tbody><?php
            foreach(Arr::get($templateData, 'existAuthors', array()) as $author) { ?>
                <tr>
                    <td style="text-align: center"><input type="checkbox" name="user<?php echo $author->id; ?>" checked="checked"></td>
                    <td><?php echo $author->nickname; ?></td>
                </tr><?php
            }
            foreach(Arr::get($templateData, 'allAdmins', array()) as $admin) {
                if($admin->id == Auth::instance()->get_user()->id) continue; ?>
                <tr>
                    <td style="text-align: center"><input type="checkbox" name="user<?php echo $admin->id; ?>"></td>
                    <td><?php echo $admin->nickname; ?></td>
                </tr><?php
            } ?>
            </tbody>
        </table>

        <div class="pull-right"><input type="submit" class="btn btn-primary btn-large" name="GlobalSubmit" value="<?php echo __('Save changes'); ?>"></div>

        <h3><?php echo __('Reviewers'); ?></h3>
        <div class="btn-group users" style="margin-bottom: 10px">
            <a class="btn btn-primary" href="<?php echo URL::base().'mapUserManager/addAllReviewers/'.$templateData['map']->id; ?>">Add All</a>
            <a class="btn btn-danger" href="<?php echo URL::base().'mapUserManager/removeAllReviewers/'.$templateData['map']->id; ?>">Remove All</a>
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
                    <a href="<?php echo URL::base().'mapUserManager/index/'.$templateData['map']->id.'/'.$templateData['authorOrder'].'/'.$templateData['learnerOrder'].'/'.(1 - $templateData['reviewerOrder']).'/'.$templateData['groupOrder']; ?>">
                        Users <div class="pull-right"><i class="icon-chevron-<?php echo ($templateData['reviewerOrder'] == 1) ? 'down' : 'up'; ?> icon-white"></i></div>
                    </a>
                </th>
            </tr>
            </thead>
            <tbody><?php
            foreach(Arr::get($templateData, 'reviewers', array()) as $reviewer) { $idReviewer = $reviewer->id ?>
                <tr>
                    <td style="text-align: center"><input type="checkbox" name="reviewer[]" value="<?php echo $idReviewer; ?>" <?php if (in_array($idReviewer, $templateData['tiedUsers'])) echo 'checked'; ?>></td>
                    <td><?php echo $reviewer->nickname; ?></td>
                </tr><?php
            } ?>
            </tbody>
        </table>

        <div class="pull-right"><input type="submit" class="btn btn-primary btn-large" name="GlobalSubmit" value="<?php echo __('Save changes'); ?>"></div>

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
                    <a href="<?php echo URL::base().'mapUserManager/index/'.$templateData['map']->id.'/'.$templateData['authorOrder'].'/'.(1 - $templateData['learnerOrder']).'/'.$templateData['reviewerOrder'].'/'.$templateData['groupOrder']; ?>">
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