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
if (isset($templateData['map'])) { ?>
<script type="text/javascript" src="<?php echo URL::base(); ?>scripts/rules.js"></script>
<div class="page-header">
    <div class="pull-right">
        <a class="btn btn-primary" href="<?php echo URL::base().'counterManager/addCommonRule/'.$templateData['map']->id; ?>">
            <i class="icon-plus-sign"></i> <?php echo __('Add rule'); ?>
        </a>
    </div>
    <h1><?php echo __('Rules for ').' "'.$templateData['map']->name.'"'; ?></h1>
</div>

<table class="table table-striped table-bordered">
    <colgroup>
        <col style="width: 5%">
        <col style="width: 20%">
        <col style="width: 75%">
        <col style="width: 20%">
    </colgroup>
    <thead>
        <tr>
            <th><?php echo __("#"); ?></th>
            <th><?php echo __("Correct"); ?></th>
            <th><?php echo __("Rule"); ?></th>
            <th><?php echo __("Actions"); ?></th>
        </tr>
    </thead>
    <tbody><?php
    if(isset($templateData['rules']) and count($templateData['rules'])) {
        foreach($templateData['rules'] as $rule) { ?>
        <tr>
            <td><?php echo $rule->id; ?></td>
            <td><?php echo ($rule->isCorrect == 1) ? 'Yes' : 'No'; ?><?php if ($rule->lightning == 1) echo ' | Lightning'; ?></td>
            <td class="changeCodeToText"><?php echo $rule->rule; ?></td>
            <td>
                <div class="btn-group">
                <a class="btn btn-info" href="<?php echo URL::base().'counterManager/editCommonRule/'.$templateData['map']->id.'/'.$rule->id; ?>">
                    <i class="icon-pencil icon-white"></i>
                    <?php echo __('Edit'); ?>
                </a>
                <a data-toggle="modal" href="#" data-target="#delete-counter-<?php echo $rule->id; ?>" class="btn btn-danger" href="javascript:void(0);">
                    <i class="icon-trash icon-white"></i>
                    <?php echo __('Delete'); ?>
                </a></div>
                <div class="modal hide alert alert-block alert-error fade in" id="delete-counter-<?php echo $rule->id; ?>">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="alert-heading"><?php echo __('Caution! Are you sure?'); ?></h4>
                    </div>
                    <div class="modal-body">
                        <p><?php echo __('You have just clicked the delete button, are you certain that you wish to proceed with deleting rule?'); ?></p>
                        <p>
                            <a class="btn btn-danger" href="<?php echo URL::base().'counterManager/deleteCommonRule/'.$templateData['map']->id.'/'.$rule->id; ?>"><?php echo __('Delete'); ?></a> <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>                        </p>
                    </div>
                </div>
            </td>
        </tr><?php
        }
    } else { ?>
        <tr class="info"><td colspan="4">There are no rules set yet. You may add a rule by clicking the button below</td></tr><?php
    } ?>
    </tbody>
</table>

<a id="availableNodesText" style="display:none;"><?php echo $templateData['nodes']['text']; ?></a>
<a id="availableNodesId" style="display:none;"><?php echo $templateData['nodes']['id']; ?></a>
<a id="availableCountersText" style="display:none;"><?php echo $templateData['counters']['text']; ?></a>
<a id="availableCountersId" style="display:none;"><?php echo $templateData['counters']['id']; ?></a>
<a id="availableConditionsText" style="display:none;"><?php echo $templateData['conditions']['text']; ?></a>
<a id="availableConditionsId" style="display:none;"><?php echo $templateData['conditions']['id']; ?></a>
<a id="availableStepsText" style="display:none;"><?php echo $templateData['steps']['text']; ?></a>
<a id="availableStepsId" style="display:none;"><?php echo $templateData['steps']['id']; ?></a>
<?php } ?>