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
$condition = $templateData['condition']; ?>
<h1 class="page-header">
    <?php echo __('Assign for ').$condition->name; ?>
    <button class="btn btn-primary btn-large pull-right addConditionAssign" type="button"><?php echo __('Add assign'); ?></button>
</h1>
<form id="assignConditionsForm" action="<?php echo URL::base().'webinarmanager/editConditionSave/'.$condition->id; ?>" method="POST">
    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>Assign to</th>
                <th>Current value</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody><?php
        if (count($templateData['assign'])) {
            foreach ($templateData['assign'] as $assign){ ?>
            <tr class="conditionAssignBl">
                <td>
                    <select name="changedConditionAssign[<?php echo $assign->id; ?>]" class="conditionAssign"><?php
                        foreach ($templateData['scenarios'] as $scenario) { ?>
                            <option value="<?php echo $scenario->id; ?>" <?php if($scenario->id == $assign->scenario_id) echo 'selected'; ?>><?php
                                echo $scenario->title; ?>
                            </option><?php
                        } ?>
                    </select>
                </td>
                <td><?php echo $assign->value; ?></td>
                <td>
                    <a class="btn btn-warning" href="<?php echo URL::base().'webinarManager/resetCondition/'.$assign->id.'/'.$condition->id; ?>">
                        <i class="icon-refresh icon-white"></i>
                        <span class="visible-desktop">Reset value</span>
                    </a>
                    <a class="btn btn-info" href="<?php echo URL::base().'webinarManager/mapsGrid/'.$assign->scenario_id.'/'.$assign->condition_id; ?>">
                        <i class="icon-list icon-white"></i>
                        <span class="visible-desktop">Maps Grid</span>
                    </a>
                    <button class="btn btn-danger deleteExistingAssign" type="button" data-id="<?php echo $assign->id; ?>"><i class="icon-trash"></i></button>
                </td>
            </tr><?php
            }
        } else { ?>
            <tr class="conditionAssignBl">
                <td>
                    <select name="newConditionAssign[]" class="conditionAssign">
                        <option>None</option><?php
                        foreach ($templateData['scenarios'] as $scenario) { ?>
                        <option value="<?php echo $scenario->id; ?>"><?php echo $scenario->title; ?></option><?php
                    } ?>
                    </select>
                </td>
                <td><?php echo $condition->startValue; ?></td>
                <td>
                    <button class="btn btn-danger" type="button"><i class="icon-trash"></i></button>
                </td>
            </tr><?php
        } ?>
            <!-- scenarios block -->
            <tr class="conditionAssignBl" style="display: none;">
                <td>
                    <select name="newConditionAssign[]" class="conditionAssign">
                        <option>None</option><?php
                        foreach ($templateData['scenarios'] as $scenario) { ?>
                            <option value="<?php echo $scenario->id; ?>"><?php echo $scenario->title; ?></option><?php
                        } ?>
                    </select>
                </td>
                <td><?php echo $condition->startValue; ?></td>
                <td>
                    <button class="btn btn-danger deleteAssign" type="button"><i class="icon-trash"></i></button>
                </td>
            </tr>
            <!-- end scenarios block -->
        </tbody>
    </table>
    <button class="btn btn-primary btn-large pull-right" type="submit"><?php echo __('Save changes'); ?></button>
</form>
<script src="<?php echo ScriptVersions::get(URL::base().'scripts/conditions.js'); ?>"></script>