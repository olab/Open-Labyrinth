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
$patients   = Arr::get($templateData,'patients', array());
$scenarios  = Arr::get($templateData,'scenarios', array()); ?>
<div class="page-header">
    <h1>
        Virtual patients manager
        <a class="btn btn-primary pull-right" href="<?php echo URL::base().'patient/management'; ?>">Create virtual patient</a>
    </h1>
</div>

<table class="table table-striped table-bordered patient-t">
    <thead>
        <tr>
            <th>Id</th>
            <th>Set assign</th>
            <th>Name</th>
            <th>Set type</th>
            <th>Operations</th>
        </tr>
    </thead>
    <tbody><?php
    if ($patients) {
        foreach ($patients as $patient) {
            $idPatient = $patient['id']; ?>
        <tr>
            <td><?php echo $idPatient; ?></td>
            <td><?php
            $assigns = Arr::get($scenarios, $idPatient, array());
            if($assigns)
            {
                foreach ($assigns as $string) echo $string.'<br>';
            }
            else echo 'Not assigned yet.'; ?>
            </td>
            <td><?php echo $patient['name']; ?></td>
            <td><?php echo $patient['type']; ?></td>
            <td>
                <a class="btn btn-info" href="<?php echo URL::base().'patient/management/'.$idPatient; ?>"><i class="icon-edit"></i>Edit</a>
                <a class="btn btn-danger" href="<?php echo URL::base().'patient/delete_patient/'.$idPatient; ?>"><i class="icon-trash"></i>Delete</a>
                <a class="btn btn-warning" href="<?php echo URL::base().'patient/resetPatient/'.$idPatient; ?>"><i class="icon-refresh icon-white"></i><?php echo __('Reset'); ?></a>
            </td>
        </tr><?php
        }
    }
    else {?>
        <tr class="info"><td colspan="5">There are no available displays right now. You may add a displays using the menu above.</td></tr><?php
    } ?>
    </tbody>
</table>