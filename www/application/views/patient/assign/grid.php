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
    <h1>
        Assign virtual patients manager
        <a class="btn btn-primary pull-right" href="<?php echo URL::base().'patient/assign_management'; ?>">Assign virtual patient</a>
    </h1>
</div>

<table class="table table-striped table-bordered assign-t">
    <thead>
    <tr>
        <th>Patient</th>
        <th>Who assign</th>
        <th>Assign type</th>
        <th>Operations</th>
    </tr>
    </thead>
    <tbody><?php
    if ($templateData['assign']) {
        foreach ($templateData['assign'] as $id_assign=>$assign) { ?>
            <tr>
            <td><?php echo Arr::get($assign, 'patient') ?></td>
            <td><?php
                foreach(Arr::get($assign, 'who', array()) as $who){
                    echo $who.'<br>';
                } ?>
            </td>
            <td><?php echo Arr::get($assign, 'type') ?></td>
            <td>
                <a class="btn btn-info" href="<?php echo URL::base().'patient/assign_management/'.$id_assign; ?>"><i class="icon-edit"></i>Edit</a>
                <a class="btn btn-danger" href="<?php echo URL::base().'patient/assign_delete/'.$id_assign; ?>"><i class="icon-trash"></i>Delete</a>
            </td>
            </tr><?php
        }
    }
    else {?>
        <tr class="info"><td colspan="4">There are no available displays right now. You may add a displays using the menu above.</td></tr><?php
    } ?>
    </tbody>
</table>