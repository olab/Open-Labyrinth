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
    <div class="page-header">
        <h1><?php echo __('Edit users of Labyrinth "') . $templateData['map']->name . '"'; ?></h1>
    </div>

    <h3><?php echo __('Authors'); ?></h3>
    <form class="form-horizontal" method="POST"
          action="<?php echo URL::base() . 'mapUserManager/addUser/' . $templateData['map']->id; ?>">
        <div class="btn-group users">
            <a class="btn btn-primary" href="<?php echo URL::base(); ?>mapUserManager/addAllAuthors/<?php echo $templateData['map']->id; ?>/<?php echo $templateData['authorOrder']; ?>/<?php echo $templateData['learnerOrder']; ?>">Add All</a>
            <a class="btn btn-danger" href="<?php echo URL::base(); ?>mapUserManager/removeAllAuthors/<?php echo $templateData['map']->id; ?>/<?php echo $templateData['authorOrder']; ?>/<?php echo $templateData['learnerOrder']; ?>">Remove All</a>
            <?php
                $isAuthorShowList = false;
                if (isset($templateData['admins']) and count($templateData['admins']) == 1) {
                    if ($templateData['admins'][0]->id == Auth::instance()->get_user()->id) {
                        $isAuthorShowList = false;
                    } else {
                        $isAuthorShowList = true;
                    }
                } else if(isset($templateData['admins']) and count($templateData['admins']) > 1) {
                    $isAuthorShowList = true;
                }

                if (isset($templateData['authors']) and count($templateData['authors']) == 1) {
                    if ($templateData['authors'][0]->id == Auth::instance()->get_user()->id) {
                        $isAuthorShowList = false;
                    } else {
                        $isAuthorShowList = true;
                    }
                } else if(isset($templateData['authors']) and count($templateData['authors']) > 1) {
                    $isAuthorShowList = true;
                }
            ?>
            <?php if ($isAuthorShowList) { ?>
                <select id="mapuserID" name="mapuserID">
                    <option value=""><?php echo __('select'); ?> ...</option>
                    <?php if (isset($templateData['admins']) and count($templateData['admins']) > 0) { foreach ($templateData['admins'] as $learner) { ?>
                        <?php if ($learner->id != Auth::instance()->get_user()->id) { ?>
                            <option value="<?php echo $learner->id; ?>"><?php echo $learner->nickname; ?></option>
                        <?php } ?>
                    <?php } } ?>
                    <?php if (isset($templateData['authors']) and count($templateData['authors']) > 0) { ?>
                        <?php foreach ($templateData['authors'] as $author) { ?>
                            <?php if ($author->id != Auth::instance()->get_user()->id) { ?>
                                <option value="<?php echo $author->id; ?>"><?php echo $author->nickname; ?></option>
                            <?php } ?>
                        <?php } ?>
                    <?php } ?>
                </select>
                <button class="btn btn-success" type="submit">Submit</button>
            <?php } ?>
        </div>
    </form>
    <?php if (isset($templateData['existAuthors']) and count($templateData['existAuthors']) > 0) { ?>
        <table class="table table-bordered table-striped">
            <thead>
            <tr>
                <th>
                    <a href="<?php echo URL::base(); ?>mapUserManager/index/<?php echo $templateData['map']->id; ?>/<?php echo $templateData['authorOrder'] == 0 ? 1 : 0; ?>/<?php echo $templateData['learnerOrder']; ?>">
                        User <div class="pull-right"><i class="icon-chevron-<?php if($templateData['authorOrder'] == 1) echo 'down';  else  echo 'up'; ?> icon-white"></i></div>
                    </a>
                </th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($templateData['existAuthors'] as $exUser) { ?>
                <tr>
                    <td><?php echo $exUser->nickname; ?></td>
                    <td>
                        <a class="btn btn-danger" href="<?php echo URL::base() . 'mapUserManager/deleteUser/' . $templateData['map']->id . '/' . $exUser->id; ?>">
                            <i class="icon-minus-sign"></i>Remove
                        </a>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    <?php } ?>

    <h3>Learners</h3>
    <form class="form-horizontal" method="POST"
          action="<?php echo URL::base() . 'mapUserManager/addUser/' . $templateData['map']->id; ?>">
        <div class="btn-group users">
            <a class="btn btn-primary" href="<?php echo URL::base(); ?>mapUserManager/addAllLearners/<?php echo $templateData['map']->id; ?>/<?php echo $templateData['authorOrder']; ?>/<?php echo $templateData['learnerOrder']; ?>">Add All</a>
            <a class="btn btn-danger" href="<?php echo URL::base(); ?>mapUserManager/removeAllLearners/<?php echo $templateData['map']->id; ?>/<?php echo $templateData['authorOrder']; ?>/<?php echo $templateData['learnerOrder']; ?>">Remove All</a>
            <?php if (isset($templateData['learners']) and count($templateData['learners']) > 0) { ?>
                <select name="mapuserID" id="mapuserID2">
                    <option value=""><?php echo __('select'); ?> ...</option>
                    <?php foreach ($templateData['learners'] as $learner) { ?>
                        <?php if ($learner->id != Auth::instance()->get_user()->id) { ?>
                            <option value="<?php echo $learner->id; ?>"><?php echo $learner->nickname; ?></option>
                        <?php } ?>
                    <?php } ?>
                </select>
                <button class="btn btn-success" type="submit">Submit</button>
            <?php } ?>
        </div>
    </form>
    <?php if (isset($templateData['existLearners']) and count($templateData['existLearners']) > 0) { ?>
        <table class="table table-bordered table-striped">
            <thead>
            <tr>
                <th>
                    <a href="<?php echo URL::base(); ?>mapUserManager/index/<?php echo $templateData['map']->id; ?>/<?php echo $templateData['authorOrder']; ?>/<?php echo $templateData['learnerOrder'] == 0 ? 1 : 0; ?>">
                        User <div class="pull-right"><i class="icon-chevron-<?php if($templateData['learnerOrder'] == 1) echo 'down';  else  echo 'up'; ?> icon-white"></i></div>
                    </a>
                </th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($templateData['existLearners'] as $exUser) { ?>
                <tr>
                    <td><?php echo $exUser->nickname; ?></td>
                    <td>
                        <a class="btn btn-danger" href="<?php echo URL::base() . 'mapUserManager/deleteUser/' . $templateData['map']->id . '/' . $exUser->id; ?>">
                            <i class="icon-minus-sign"></i>Remove
                        </a>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    <?php } ?>
<?php } ?>