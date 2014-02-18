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
if(isset($templateData['patient'])) {
$id_patient = $templateData['patient']->id;
$name       = $templateData['patient']->name;
$r_patient  = Arr::get($templateData, 'r_patient', false);
?>
<div class="page-header"><?php
    if ($id_patient) { ?>
    <h1>Edit patient '<?php echo $name; ?>'</h1><?php
    } else { ?>
    <h1>Create patient</h1><?php
    } ?>
</div>

<form class="form-horizontal" action="<?php echo URL::base().'patient/update/'.$id_patient ?>" method="post">

    <div class="control-group">
        <label>
            <span class="f-col">Name</span>
            <input type="text" class="no-margin" placeholder="Name" name="name" value="<?php echo $name; ?>" required>
        </label>
    </div>

    <fieldset class="fieldset">
        <legend>Conditions</legend><?php
        if( ! $id_patient) { ?>
        <div class="condition-control-group">
            <input type="hidden" name="conditions[id][]" value="new">
            <label>
                <span class="f-col">Condition name</span>
                <input name="conditions[name][]" type="text" value="Condition name">
            </label>
            <label>
                <span class="f-col">Condition start value</span>
                <input name="conditions[value][]" type="text" value="0">
            </label>
        </div><?php
        }

        foreach (Arr::get($templateData, 'patient_conditions', array()) as $condition) { ?>
        <div class="condition-control-group">
            <input type="hidden" name="conditions[id][]" value="<?php echo $condition->id ?>">
            <label>
                <span class="f-col">Condition name</span>
                <input name="conditions[name][]" type="text" value="<?php echo $condition->name ?>">
            </label>
            <a href="<?php echo URL::base().'patient/delete_condition/'.$id_patient.'/'.$condition->id.'/condition'; ?>" class="btn btn-danger pull-right dra"><i class="icon-trash"></i>Delete</a>
            <label>
                <span class="f-col">Condition start value</span>
                <input name="conditions[value][]" type="text" value="<?php echo $condition->value ?>">
            </label>
        </div><?php
        }?>

        <button type="button" class="btn btn-info add-condition-js"><i class="icon-plus"></i>Add</button>
    </fieldset>

    <fieldset class="fieldset">
        <legend>Labyrinth</legend><?php
        if ( ! $id_patient OR ! $templateData['patient_id_maps']) { ?>
        <div class="condition-control-group">
            <label><?php
                if (isset($templateData['maps'])) { ?>
                    <span class="f-col">Select Labyrinth</span>
                    <select name="maps"><?php
                    foreach ($templateData['maps'] as $map) { ?>
                        <option value="<?php echo $map->id; ?>"><?php echo $map->name; ?></option><?php
                    } ?>
                    </select><?php
                } else { ?>
                    <span class="no-maps">To add labyrinth, you must create it.</span><?php
                } ?>
            </label>
        </div><?php
        }

        foreach (Arr::get($templateData, 'patient_id_maps') as $id=>$id_map) {?>
            <div class="condition-control-group">
            <a href="<?php echo URL::base().'patient/delete_from_edit/'.$id_patient.'/'.$id_map.'/labyrinth'; ?>" class="btn btn-danger pull-right"><i class="icon-trash"></i>Delete</a>
                <label>
                    <span class="f-col">Select Labyrinth</span>
                    <select name="maps[id<?php echo $id; ?>]"><?php
                        foreach ($templateData['maps'] as $map) { ?>
                            <option value="<?php echo $map->id; ?>" <?php if ($id_map == $map->id) echo 'selected' ?>><?php echo $map->name; ?></option><?php
                        } ?>
                    </select>
                </label>
            </div><?php
        } ?>
        <button type="button" class="btn btn-info add-labyrinth-js"><i class="icon-plus"></i>Add</button>
    </fieldset>

    <fieldset class="fieldset">
        <legend>Relation</legend><?php
        if ($r_patient) {?>
        <div class="condition-control-group">
            <a href="<?php echo URL::base().'patient/delete_from_edit/'.$id_patient.'/'.$r_patient->id.'/relation'; ?>" class="btn btn-danger pull-right"><i class="icon-trash"></i>Delete</a>
            <label>
                <span class="f-col">Assign relation</span>
                <select name="r_patient[<?php echo $r_patient->id; ?>]"><?php
                    foreach (Arr::get($templateData, 'all_patients', array()) as $patient) { ?>
                        <option value='<?php echo $patient->id; ?>' <?php if( $r_patient->id_second_patient == $patient->id) echo 'selected'; ?>><?php echo $patient->name; ?></option><?php
                    } ?>
                </select>
            </label>
        </div><?php
        } ?>
        <button type="button" class="btn btn-info add-r-patient-js" <?php if($r_patient) echo 'style="display: none;"'; ?>><i class="icon-plus"></i>Add</button>
    </fieldset>

    <button type="submit" class="btn btn-primary btn-large pull-right"><?php echo ($id_patient) ? 'Save' : 'Create'; ?></button>
</form>

<!-- Add condition block -->
<div class="condition-control-group add-condition-bl" style="display: none;">
    <input type="hidden" name="conditions[id][]" value="new">
    <label>
        <span class="f-col">Condition name</span>
        <input name="conditions[name][]" type="text" value="Condition name">
    </label>
    <button type="button" class="btn btn-danger pull-right remove-condition-js"><i class="icon-trash"></i>Delete</button>
    <label>
        <span class="f-col">Condition start value</span>
        <input name="conditions[value][]" type="text" value="0">
    </label>
</div>
<!-- End add condition block -->

<!-- Add labyrinth block -->
<div class="condition-control-group add-labyrinth-bl" style="display: none;">
    <button type="button" class="btn btn-danger pull-right remove-condition-js"><i class="icon-trash"></i>Delete</button>
    <label><?php
        if (isset($templateData['maps'])) { ?>
            <span class="f-col">Select Labyrinth</span>
            <select name="maps[]"><?php
            foreach ($templateData['maps'] as $map) { ?>
                <option value="<?php echo $map->id; ?>"><?php echo $map->name; ?></option><?php
            } ?>
            </select><?php
        } else { ?>
            <span class="no-maps">To add labyrinth, you must create it.</span><?php
        } ?>
    </label>
</div>
<!-- End add labyrinth block -->

<!-- Add relation block -->
<div class="condition-control-group r-patient-js" style="display: none;">
    <button type="button" class="btn btn-danger pull-right remove-relation-js"><i class="icon-trash"></i>Delete</button>
    <label>
        <span class="f-col">Assign relation</span>
        <select name="r_patient[]"><?php
            foreach (Arr::get($templateData, 'all_patients', array()) as $patient) { ?>
                <option value='<?php echo $patient->id; ?>'><?php echo $patient->name; ?></option><?php
            } ?>
        </select>
    </label>
</div>
<!-- End add relation block -->
<?php } ?>