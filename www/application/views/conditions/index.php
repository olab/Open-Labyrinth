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
 */ ?>
<form class="form-horizontal" id="conditionsForm" method="post" action="<?php echo URL::base().'webinarmanager/saveConditions'; ?>">
    <h1 class="page-header">
        <?php echo __('Conditions'); ?>
        <button class="btn btn-primary btn-large pull-right" type="submit"><?php echo __('Save changes'); ?></button>
        <button class="btn btn-primary btn-large pull-right add-condition-js" type="button"><?php echo __('Add condition'); ?></button>
    </h1>

    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>Embeddable</th>
                <th>Name</th>
                <th>Assigned to</th>
                <th>Action</th>
                <th>Starting value</th>
            </tr>
        </thead>
        <tbody><?php
        if(count($templateData['conditions'])) {
            foreach ($templateData['conditions'] as $condition) { ?>
            <tr>
                <td><input class="code" readonly="readonly" type="text" value="<?php echo '[[COND:'.$condition->id.']]'; ?>"></td>
                <td><input type="text" name="changedConditionsName[<?php echo $condition->id ?>]" value="<?php echo $condition->name; ?>"></td>
                <td><?php
                    $assigns = Arr::get($templateData['assign'], $condition->id, array());
                    foreach ($assigns as $assign) {
                        echo $assign.'<br>';
                    } ?>
                </td>
                <td>
                    <button type="button" class="btn btn-danger deleteChangedCondition" data-id="<?php echo $condition->id ?>">
                        <i class="icon-trash icon-white"></i>Delete
                    </button>
                    <a class="btn btn-info" href="<?php echo URL::base().'webinarmanager/editCondition/'.$condition->id; ?>">
                        <i class="icon-edit icon-white"></i>
                        <span class="visible-desktop">Edit</span>
                    </a>
                </td>
                <td><input class="input-small" type="text" name="changedConditionsValue[<?php echo $condition->id ?>]" value="<?php echo $condition->startValue; ?>"></td>
            </tr><?php
            }
        } else { ?>
            <tr>
                <td><input class="code" readonly="readonly" type="text" placeholder="none"></td>
                <td><input type="text" name="newConditionsName[]" placeholder="Type name"></td>
                <td></td>
                <td>
                    <button type="button" class="btn btn-danger">
                        <i class="icon-trash icon-white"></i>Delete
                    </button>
                </td>
                <td><input class="input-small" type="text" name="newConditionsValue[]" placeholder="0"></td>
            </tr><?php
        } ?>
            <!-- add condition block by jq -->
            <tr class="new-condition" style="display: none;">
                <td><input class="code" readonly="readonly" type="text" placeholder="none"></td>
                <td><input type="text" name="newConditionsName[]" placeholder="Type name"></td>
                <td></td>
                <td>
                    <button type="button" class="btn btn-danger deleteNewCondition">
                        <i class="icon-trash icon-white"></i>Delete
                    </button>
                </td>
                <td><input class="input-small" type="text" name="newConditionsValue[]" placeholder="0"></td>
            </tr>
            <!-- end condition add block by jq -->
        </tbody>
    </table>
    <button class="btn btn-primary btn-large pull-right" type="submit"><?php echo __('Save changes'); ?></button>
</form>

<script src="<?php echo ScriptVersions::get(URL::base().'scripts/conditions.js'); ?>"></script>