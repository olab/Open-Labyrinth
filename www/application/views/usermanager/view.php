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

<h1><?php echo __('Users & Groups'); ?></h1>

<h2>Users</h2>
<div class="control-group">

    <a class="btn btn-primary"
       href=<?php echo URL::base() . 'usermanager/addUser' ?>><?php echo __('add') . ' ' . __('user'); ?></a></div>

<p>
    <strong><?php echo __('Users'); ?></strong>:&nbsp;<?php if (isset($templateData['userCount'])) echo $templateData['userCount']; ?>
    &nbsp;<?php echo __('registered users'); ?>&nbsp;[]</p>


<table class="table table-striped table-bordered">
    <colgroup>
        <col/>
        <col/>
        <col/>
        <col/>
    </colgroup>
    <thead>
    <tr>
        <th>
            <?php echo __('Username'); ?>
        </th>
        <th>
            <?php echo __('Name'); ?>
        </th>
        <th>
            <?php echo __('Type'); ?>
        </th>
        <th>
            <?php echo __('Password Recovery'); ?>
        </th>
        <th>
            <?php echo __('Actions'); ?>
        </th>

    </tr>
    </thead>

    <tbody>


    <?php if (isset($templateData['users']) and count($templateData['users']) > 0) { ?>
        <?php foreach ($templateData['users'] as $user) { ?>
            <tr>
                <td><?php echo $user->username;?></td>
                <td><?php echo $user->nickname;?></td>
                <td><?php echo $user->type->name;?></td>
                <td><?php

                    if ($user->resetAttempt != NULL) {
                        echo $user->resetAttempt;
                    } else {
                        echo __('No attempts');
                    }
                    if ($user->resetTimestamp != NULL) {
                        echo __('Last password recovery') . ':&nbsp;' . $user->resetTimestamp;
                    }

                    echo $user->resetAttempt;?></td>
                <td><a class="btn btn-primary" href="<?php echo URL::base() . 'usermanager/editUser/' . $user->id; ?>">edit</a>
                </td>
            </tr>

        <?php } ?>
    <?php }?>

    </tbody>
</table>


<h3>Groups</h3>
<div class="control-group">
    <a class="btn btn-primary" href=<?php echo URL::base() . 'usermanager/addGroup'; ?>>add group</a></div>


<table class="table table-striped table-bordered">
    <thead>
    <tr>
        <th><?php echo __('Title'); ?></th>
        <th><?php echo __('Actions'); ?></th>
    </tr>
    </thead>
    <tbody>
    <?php if (isset($templateData['groups']) and count($templateData['groups']) > 0) { ?>
        <?php foreach ($templateData['groups'] as $group) { ?>
            <tr>
                <td><?php echo $group->name; ?></td>
                <td><a class="btn btn-primary"
                       href="<?php echo  URL::base() . 'usermanager/editGroup/' . $group->id; ?>"><?php echo __('edit');?> </a>
                </td>
            </tr>
        <?php } ?>
    <?php } ?>
    </tbody>
</table>