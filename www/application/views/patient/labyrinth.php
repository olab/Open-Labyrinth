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
$id_map                 = $templateData['map']->id;
$patients               = $templateData['patients'];
$selected_patient       = Arr::get($templateData, 'selected_patient', array());
$id_selected_patient    = 0;
$exist_data             = Arr::get($templateData, 'existing_data', FALSE);
$node_data              = FALSE;
$condition_data         = FALSE;
if ($selected_patient)
{
    $id_selected_patient = $selected_patient->id;
}
?>
<div class="page-header">
    <h1>Virtual patient</h1>
</div><?php
if ($patients) { ?>
<select id="choose-patient">
    <option data-href="<?php echo URL::base().'patient/labyrinth/'.$id_map; ?>">Select patient</option><?php
    foreach ($patients as $patient) {?>
    <option value="<?php echo $patient->id ?>" data-href="<?php echo URL::base().'patient/labyrinth/'.$id_map.'/'.$patient->id; ?>" <?php if($patient->id == $id_selected_patient) echo 'selected' ?>><?php echo $patient->name ?></option><?php
    } ?>
</select><?php
} else { ?>
Patients didn't assign for this labyrinth. <?php
}?>

<?php if($selected_patient) { ?>
<form action="<?php echo URL::base().'patient/condition/'.$id_selected_patient; ?>" method="POST">
    <h2>Conditions</h2>
    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th></th><?php
                foreach(Arr::get($templateData, 'patient_condition', array()) as $condition) { ?>
                <th style="width:155px;">
                    <?php echo __('Appear on node'); ?>
                    <a href="javascript:void(0)" class="btn btn-info btn-mini toggle-all-on" id="<?php echo $condition->id; ?>">all on</a>
                    <a href="javascript:void(0)" class="btn btn-info btn-mini toggle-all-off" id="<?php echo $condition->id; ?>">all off</a>
                    <a href="javascript:void(0)" class="btn btn-info btn-mini toggle-reverse" id="<?php echo $condition->id; ?>">reverse</a>
                </th><?php
                } ?>
            </tr>
        </thead>
        <tbody><?php
            foreach (Arr::get($templateData, 'nodes', array()) as $node) {
            if ($exist_data) $node_data = Arr::get($exist_data, $node->id, FALSE); ?>
            <tr>
                <td><p><?php echo $node->title; ?> [<?php echo $node->id; ?>]</p></td><?php
                foreach(Arr::get($templateData, 'patient_condition', array()) as $condition) {
                if ($node_data) $condition_data = Arr::get($node_data, $condition->id, FALSE);?>
                <td>
                    <div><?php echo $condition->name; ?></div>
                    <input class="input-small" type="text" size="5" name="<?php echo 'dbn['.$node->id.']['.$condition->id.'][value]';?>" value="<?php echo $condition_data ? $condition_data['value'] : 0 ?>">
                    <label>
                        <input class="condition_checkbox <?php echo 'chk_'.$condition->id ?>" type="checkbox" value="1" name="<?php echo 'dbn['.$node->id.']['.$condition->id.'][appear]';?>" <?php if(isset($condition_data['appear']) AND $condition_data['appear']) echo 'checked'; ?>><?php echo __("appear on node"); ?>
                    </label>
                </td><?php
                } ?>
            </tr><?php
            } ?>
        </tbody>
    </table>
    <div class="pull-right"><input class="btn btn-primary btn-large" type="submit" value="<?php echo __('Save changes'); ?>"></div>
</form><?php
} ?>