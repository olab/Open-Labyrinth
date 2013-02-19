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

            <?php if (isset($templateData['group'])) { ?>

                            <a class="btn btn-danger" href=<?php echo URL::base() . 'usermanager/deleteGroup/' . $templateData['group']->id; ?>>delete</a>
                            <form class="form-horizontal" action="<?php echo URL::base() . 'usermanager/updateGroup/' . $templateData['group']->id; ?>" method="post">
                                                <fieldset class="fieldset">
                                                    <legend>Edit group</legend>
                                                    <div class="control-group">
                                                        <label for="groupname" class="control-label">group name</label>

                                                        <div class="controls">
                                                            <input id="groupname" class="not-autocomplete" type="text" name="groupname" size="50" value="<?php echo $templateData['group']->name; ?>">
                                                        </div>
                                                    </div>
                                                </fieldset>



                                            <input class="btn btn-primary" type="submit" name="UpdateGroupSubmit" value="<?php echo __('submit'); ?>">
                            </form>

    <h3>Members</h3>

                            <form action="<?php echo URL::base().'usermanager/addMemberToGroup/'.$templateData['group']->id; ?>" method="post">

                                <fieldset class="fieldset">
                                    <legend></legend>
                                    <div class="control-group">
                                        <label for="userid" class="control-label">User</label>

                                        <div class="controls">
                                            <select id="userid" name="userid">
                                                <?php foreach($templateData['users'] as $user) { ?>
                                                    <option value="<?php echo $user->id; ?>"><?php echo $user->nickname; ?> (<?php echo $user->username; ?>)</option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                </fieldset>


                                <?php if(isset($templateData['users'])) { ?>

                                <input class="btn btn-primary" type="submit" name="AddUserToGroupSubmit" value="<?php echo __('submit'); ?>">
                                <?php } ?>
                            </form>
    <table class="table table-border table-striped">
        <thead>
        <tr>
            <th>User</th>
            <th>Operations</th>
        </tr>
        </thead>
        <tbody>
        <?php if(isset($templateData['members'])) { ?>
            <?php foreach($templateData['members'] as $member) { ?>
                <tr>
                    <td><?php echo $member->nickname.'('.$member->username.')';?></td>
                    <td><a href=<?php echo URL::base().'usermanager/removeMember/'.$member->id.'/'.$templateData['group']->id; ?>>remove</a></td>
                </tr>

            <?php } ?>
        <?php } ?>

        </tbody>
    </table>


            <?php } ?>
