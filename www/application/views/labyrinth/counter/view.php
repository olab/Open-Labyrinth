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
    <div class="page-header">   <div class="pull-right">
            <div class="btn-group">
                <a class="btn btn-primary" href="<?php echo URL::base().'counterManager/addCounter/'.$templateData['map']->id; ?>">
                    <i class="icon-plus-sign"></i>
                    <?php echo __('Add counter'); ?></a>
              </div>
        </div>
<h1><?php echo __('Counters') . ' "' . $templateData['map']->name . '"'; ?></h1>
     </div>
<table class="table table-striped table-bordered">
    <colgroup>
        <col style="width: 5%">
        <col style="width: 55%">
        <col style="width: 40%">
    </colgroup>
    <thead>
        <tr>
            <th><?php echo __("Embeddable"); ?></th>
            <th><?php echo __("Name"); ?></th>
            <th><?php echo __("Actions"); ?></th>
        </tr>
    </thead>
<?php if(isset($templateData['counters']) and count($templateData['counters']) > 0) { ?>
    <?php foreach($templateData['counters'] as $counter) { ?>
        <tr>
            <td>
                <label><input class="code" readonly="readonly" type="text" value="[[CR:<?php echo $counter->id; ?>]]"></label>
            </td>
            <td><?php echo $counter->name; ?></td>
            <td>
                <div class="btn-group">
                <a class="btn btn-info" href="<?php echo URL::base().'counterManager/editCounter/'.$templateData['map']->id.'/'.$counter->id; ?>">
                    <i class="icon-pencil icon-white"></i>
                    <?php echo __('Edit'); ?>
                </a>
                <a class="btn btn-success" href="<?php echo URL::base().'counterManager/previewCounter/'.$templateData['map']->id.'/'.$counter->id; ?>">
                    <i class="icon-play icon-white"></i>
                    <?php echo __('Preview'); ?>
                </a>
                <a class="btn btn-info" href="<?php echo URL::base().'counterManager/grid/'.$templateData['map']->id.'/'.$counter->id; ?>">
                    <i class="icon-th icon-white"></i>
                    <?php echo __('Grid'); ?>
                </a>
                <a data-toggle="modal" href="#" data-target="#delete-counter-<?php echo $counter->id; ?>" class="btn btn-danger" href="javascript:void(0);">
                    <i class="icon-trash icon-white"></i>
                    <?php echo __('Delete'); ?>
                </a></div>
                <div class="modal hide alert alert-block alert-error fade in" id="delete-counter-<?php echo $counter->id; ?>">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="alert-heading"><?php echo __('Caution! Are you sure?'); ?></h4>
                    </div>
                    <div class="modal-body">
                        <p><?php echo __('You have just clicked the delete button, are you certain that you wish to proceed with deleting "' . $counter->name . '" counter?'); ?></p>
                        <p>
                            <a class="btn btn-danger" href="<?php echo URL::base().'counterManager/deleteCounter/'.$templateData['map']->id.'/'.$counter->id; ?>"><?php echo __('Delete'); ?></a> <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>                        </p>
                    </div>
                </div>
            </td>
        </tr>
        <?php } ?>
    <?php } else{ ?>
        <tr class="info">
            <td colspan="3">There are no counters in this labyrinth, yet. You may add one, clicking the button above.</td>
        </tr>
    <?php } ?>

<?php } ?>