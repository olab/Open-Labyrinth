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

$id_type    = Arr::get($templateData, 's_type', false);
$id_assign  = Arr::get($templateData, 'id_assign');
$same       = (($id_type === false) OR ($id_type == 1) OR ($id_type == 3));
?>

<div class="page-header"><?php
    if ($id_type) { ?>
        <h1>Edit assign</h1><?php
    } else { ?>
        <h1>Create assign</h1><?php
    } ?>
</div>

<form class="form-horizontal" action="<?php echo URL::base().'patient/assign_save'; ?>" method="post">

    <div class="control-group">
        <label>
            <span class="f-col">Patient</span>
            <select name="patient" required><?php
                foreach (Arr::get($templateData, 'patients', array()) as $patient) { ?>
                    <option value='<?php echo $patient->id; ?>' <?php if(Arr::get($templateData, 's_patient', false) == $patient->id) echo 'selected'; ?>><?php echo $patient->name; ?></option><?php
                } ?>
            </select>
        </label>
        <label>
            <span class="f-col">Type</span>
            <select name="type" id="assign-type" required><?php
                foreach (Arr::get($templateData, 'patient_type', array()) as $type) { ?>
                    <option value='<?php echo $type->id; ?>' <?php if($id_type == $type->id) echo 'selected'; ?>><?php echo $type->type; ?></option><?php
                } ?>
            </select>
        </label>
    </div>

    <fieldset class="fieldset assign-bl-js <?php if ($same) echo 'assign-same'; ?>">
        <legend>Assign</legend><?php

        if( ! $id_assign) { ?>
        <div class="condition-control-group assign-same">
            <label>
                <span class="f-col"></span>
                <input class="assign-type" type="radio" name="assign-type" value="users">Users
            </label>
            <label>
                <span class="f-col"></span>
                <input class="assign-type" type="radio" name="assign-type" value="groups">Groups
            </label>
        </div><?php
        }

        foreach (Arr::get($templateData, 's_assign', array()) as $record) {
        $user_or_not = $record->user_or_group == 'user';
        $id_u_or_group = ($user_or_not) ? $record->id_user : $record->id_group; ?>
        <div class="condition-control-group">
            <label>
                <span class="f-col"></span>
                <input class="assign-type" type="radio" name="<?php echo $record->id.'-assign-type'; ?>" value="users" <?php if ($user_or_not) echo 'checked'; ?>>Users
            </label>
            <label>
                <span class="f-col"></span>
                <input class="assign-type" type="radio" name="<?php echo $record->id.'-assign-type'; ?>" value="groups" <?php if ( ! $user_or_not) echo 'checked'; ?>>Groups
            </label>
            <a class="btn btn-danger pull-right dra" href="<?php echo URL::base().'patient/delete_assign_record/'.$id_assign.'/'.$id_u_or_group.'/'.$user_or_not; ?>"><i class="icon-trash"></i>Delete</a><?php
            if ($user_or_not) { ?>
            <label class="assign-user"><?php
                if (isset($templateData['users'])) { ?>
                    <span class="f-col">Select User</span>
                    <select name="assign[][user]"><?php
                    foreach ($templateData['users'] as $user) { ?>
                        <option value="<?php echo $user->id; ?>" <?php if($id_u_or_group == $user->id) echo 'selected'; ?>><?php echo $user->username; ?></option><?php
                    } ?>
                    </select><?php
                } else { ?>
                    <span class="assign-empty">For assign user, please create it.</span><?php
                } ?>
            </label><?php
            } else { ?>
            <label class="assign-group"><?php
                if (isset($templateData['groups'])) { ?>
                    <span class="f-col">Select Group</span>
                    <select name="assign[][group]"><?php
                    foreach ($templateData['groups'] as $group) { ?>
                        <option value="<?php echo $group->id; ?>" <?php if($id_u_or_group == $group->id) echo 'selected'; ?>><?php echo $group->name; ?></option><?php
                    } ?>
                    </select><?php
                } else { ?>
                    <span class="assign-empty">For assign group, please create it.</span><?php
                } ?>
            </label><?php
            } ?>
        </div><?php
        } ?>

        <button type="button" class="btn btn-info add-assign-js"><i class="icon-plus"></i>Add</button>
    </fieldset>

    <button type="submit" class="btn btn-primary btn-large pull-right"><?php echo (true) ? 'Save' : 'Create'; ?></button>
</form>

<!-- Add assign block -->
<div class="condition-control-group add-assign-bl" style="display: none;">
    <label>
        <span class="f-col"></span>
        <input class="assign-type" type="radio" name="assign-type" value="users">Users
    </label>
    <label>
        <span class="f-col"></span>
        <input class="assign-type" type="radio" name="assign-type" value="groups">Groups
    </label>
    <button type="button" class="btn btn-danger pull-right remove-condition-js"><i class="icon-trash"></i>Delete</button>
</div>

<label class="assign-user" style="display: none;"><?php
    if (isset($templateData['users'])) { ?>
        <span class="f-col">Select User</span>
        <select name="assign[][user]"><?php
        foreach ($templateData['users'] as $user) { ?>
            <option value="<?php echo $user->id; ?>"><?php echo $user->username; ?></option><?php
        } ?>
        </select><?php
    } else { ?>
        <span class="assign-empty">For assign user, please create it.</span><?php
    } ?>
</label>

<label class="assign-group" style="display: none;"><?php
    if (isset($templateData['groups'])) { ?>
        <span class="f-col">Select Group</span>
        <select name="assign[][group]"><?php
        foreach ($templateData['groups'] as $group) { ?>
            <option value="<?php echo $group->id; ?>"><?php echo $group->name; ?></option><?php
        } ?>
        </select><?php
    } else { ?>
        <span class="assign-empty">For assign group, please create it.</span><?php
    } ?>
</label>
<!-- End add assign block -->