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
    <h1><?php echo __('My Scenario'); ?></h1>
</div>
<table class="table table-striped table-bordered" id="my-labyrinths">
    <colgroup>
        <col style="width: 50%" />
        <col style="width: 20%" />
        <col style="width: 30%" />
    </colgroup>
    <thead>
    <tr>
        <th><?php echo __('Scenario Title'); ?></th>
        <th><?php echo __('Step'); ?></th>
        <th><?php echo __('Actions'); ?></th>
    </tr>
    </thead>
    <tbody>
    <?php if(isset($templateData['webinars']) && count($templateData['webinars']) > 0) { ?>
        <?php foreach($templateData['webinars'] as $webinar) { ?>
            <tr>
                <td><a href="<?php echo URL::base(); ?>"><?php echo $webinar->title; ?></a></td>
                <td>
                    <?php
                    if($webinar->current_step != null && $webinar->steps != null && count($webinar->steps) > 0) {
                        foreach($webinar->steps as $webinarStep) {
                            if($webinarStep->id == $webinar->current_step) {
                                echo $webinarStep->name;
                                break;
                            }
                        }
                    } else {
                        echo '-';
                    }
                    ?>
                </td>
                <td class="center">
                    <div class="btn-group">
                        <a class="btn btn-info" href="<?php echo URL::base() . 'webinarManager/render/' . $webinar->id; ?>">
                            <i class="icon-folder-open icon-white"></i>
                            <span class="visible-desktop">Open</span>
                        </a>
                        &nbsp;
                        <a class="btn btn-info" href="<?php echo URL::base() . 'webinarManager/progress/' . $webinar->id; ?>">
                            <i class="icon-calendar icon-white"></i>
                            <span class="visible-desktop">Show progress</span>
                        </a>
                    </div>
                </td>
            </tr>
        <?php } ?>
    <?php } else { ?>
        <tr class="info"><td colspan="4">There are no available Scenarios right now.</td> </tr>
    <?php } ?>
    </tbody>
</table>