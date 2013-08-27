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
if (isset($templateData['webinar'])) { ?>
<div class="page-header">
    <h1><?php echo __('Statistics for ') . ' "' . $templateData['webinar']->title . '"'; ?></h1></div>


<table class="table table-striped table-bordered">
    <colgroup>
        <col style="width: 70%">
        <col style="width: 30%">
    </colgroup>
    <thead>
        <tr>
            <th><?php echo __("Date"); ?></th>
            <th><?php echo __("Actions"); ?></th>
        </tr>
    </thead>
<?php if(isset($templateData['history']) and count($templateData['history']) > 0) { ?>
    <?php foreach($templateData['history'] as $history) { ?>
        <tr>
            <td> <a href="<?php echo URL::base().'webinarManager/showStats/'.$templateData['webinar']->id.'/'.$history['webinar_step'] . '/' .$history['id'] ; ?>"> <?php echo date('Y-m-d H:i:s',$history['date_save']); ?></a></td>
            <td>
                <div class="btn-group">
                <a class="btn btn-info" href="<?php echo URL::base().'webinarManager/showStats/'.$templateData['webinar']->id.'/'.$history['webinar_step'] . '/' .$history['id']; ?>">
                    <i class="icon-pencil icon-white"></i>
                    <?php echo __('Show'); ?>
                </a>
                </div>
            </td>
        </tr>
        <?php } ?>
    <?php } else{ ?>
        <tr class="info"><td colspan="3">There are no statistics set yet.</td></tr>
    <?php } ?>
</table>


<?php } ?>