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

<?php if(isset($templateData['map'])) { ?>
<div class="page-header">
    <div class="pull-right">
        <div class="btn-group">
            <a class="btn btn-primary" href="<?php echo URL::base(); ?>visualdisplaymanager/display/<?php echo $templateData['map']->id; ?>">
                <i class="icon-plus-sign icon-white"></i>
                <?php echo __('Add a display'); ?></a>
        </div>
    </div>
        <h1><?php echo __('Edit displays of Labyrinth') . ' "' . $templateData['map']->name . '"'; ?></h1>
    </div>

<table class="table table-striped table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if(isset($templateData['displays']) && count($templateData['displays']) > 0) { ?>
        <?php foreach($templateData['displays'] as $display) { ?>
        <tr>
            <td><input type="text" value="[[VD:<?php echo $display->id; ?>]]"/></td>
            <td>
                <a class="btn btn-info" href="<?php echo URL::base(); ?>visualdisplaymanager/display/<?php echo $templateData['map']->id; ?>/<?php echo $display->id; ?>">
                    <i class="icon-pencil icon-white"></i>
                    <?php echo __('Edit'); ?>
                </a>
                <a class="btn btn-danger" href="<?php echo URL::base(); ?>visualdisplaymanager/deleteDisplay/<?php echo $templateData['map']->id; ?>/<?php echo $display->id; ?>">
                    <i class="icon-trash"></i>
                    <?php echo __('Delete'); ?>
                </a>
            </td>
        </tr>
        <?php } ?>
        <?php } else { ?>
        <tr class="info"><td colspan="2"><?php echo __('There are no available displays right now. You may add a displays using the menu below.'); ?></td></tr>
        <?php } ?>
    </tbody>
</table>
<?php } ?>