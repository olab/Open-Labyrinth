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
$patientsSame       = Arr::get($templateData, 'patientSame', array());
$patientsDifferent  = Arr::get($templateData, 'patientDifferent', array());
$connection         = Arr::get($templateData, 'connection', false);
$connectionId       = $connection ? $connection->id : '';
$connectionRule     = $connection ? $connection->rule : '';
?>
<script type="text/javascript" src="<?php echo ScriptVersions::get(URL::base().'scripts/rules-checker.js'); ?>"></script>
<div>
    <h3>Select patient to see his conditions</h3>
    <label><input type="radio" class="patient-type-js" name="ptj" data-type="same" checked>Same user</label>
    <label><input type="radio" class="patient-type-js" name="ptj" data-type="different">Different  users</label>
    <div class="patient-same">
        <label class="patient-condition">
            <select class="patient-condition-js"><?php
                foreach($patientsSame as $patient) { ?>
                <option value="<?php echo $patient->id; ?>"><?php echo $patient->name; ?></option><?php
                } ?>
            </select>
        </label>
        <label class="patient-condition">
            <select class="patient-condition-js"><?php
                foreach($patientsSame as $patient) { ?>
                <option value="<?php echo $patient->id; ?>"><?php echo $patient->name; ?></option><?php
                } ?>
            </select>
        </label>
    </div>
    <div class="patient-different">
        <label class="patient-condition">
            <select class="patient-condition-js"><?php
                foreach($patientsDifferent as $patient) { ?>
                <option value="<?php echo $patient->id; ?>"><?php echo $patient->name; ?></option><?php
                } ?>
            </select>
        </label>
        <label class="patient-condition">
            <select class="patient-condition-js"><?php
                foreach($patientsDifferent as $patient) { ?>
                <option value="<?php echo $patient->id; ?>"><?php echo $patient->name; ?></option><?php
                } ?>
            </select>
        </label>
    </div>
</div>

<div class="patient-hint">
    <h3>Rule hint</h3>
    IF [[COND:ID]] = N THEN DEACTIVATE [[NODE:ID]];<br>
    IF [[COND:ID]] = N THEN [[COND:ID]] = [[COND:ID]] + N;
</div>

<h3><?php echo __('Rule'); ?></h3>
<form class="form-horizontal" method="post" action="<?php echo URL::base().'patient/updateRule/'.$connectionId; ?>">
    <textarea style="width:100%; height:200px;" name="rule"><?php echo $connectionRule; ?></textarea>

    <div class="pull-right" style="margin-top:10px;">
        <input style="float:right;" type="submit" id="check_button" class="btn btn-primary btn-large" value="<?php echo __('Save rule'); ?>">
    </div>

    <div class="pull-left">
        <dl class="status-label dl-horizontal">
            <dt style="text-align: left;">Status</dt>
            <dd>
                <span class="label label-warning">The rule hasn't been checked.</span>
                <span class="label label-success hide">The rule is correct.</span>
                <span class="label label-important hide">The rule has error(s).</span>
            </dd>
        </dl>
        <input style="float:left;" type="button" id="check_rule_button" class="btn btn-primary btn-large" data-loading-text="Checking..." value="<?php echo __('Check rule'); ?>">
    </div>
    <input type="hidden" name="url" id="url" value="<?php echo URL::base().'counterManager/checkCommonRule'; ?>" />
    <input type="hidden" name="mapId" id="mapId" value="1" />
    <input type="hidden" name="isCorrect" id="isCorrect" value="1" />
</form>