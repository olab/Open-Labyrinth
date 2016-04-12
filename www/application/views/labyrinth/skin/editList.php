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
if (isset($templateData['map'])) {
    $haveOne = false;
    ?>
<h1><?php echo __('Skin for "').$templateData['map']->name.'"'; ?></h1>
<div class="member-box round-all">
    <?php echo $templateData['navigation']; ?>
    <legend><?php echo __('Edit my skins'); ?></legend>
    <table style="background: #FFFFFF;" cellspacing="0" cellpadding="0" border="0" class="table table-striped table-bordered">
        <colgroup>
            <col style="width: 5%">
            <col style="width: 50%">
            <col style="width: 45%">
        </colgroup>
        <thead>
            <tr>
                <th><?php echo __('ID'); ?></th>
                <th><?php echo __('Name'); ?></th>
                <th><?php echo __('Actions'); ?></th>
            </tr>
        </thead>
        <tbody><?php foreach($templateData['skinList'] as $skin){
        $haveOne = true; ?>
        <tr>
            <td><p><?php echo $skin->id;  ?></p></td>
            <td><p><?php echo $skin->name; ?></p></td>
            <td class="center">
                <a href="<?php echo URL::base().'skinManager/editSkins/'.$templateData['map']->id.'/'.$skin->id; ?>" class="btn btn-info">
                    <i class="icon-edit icon-white"></i>
                    <?php echo __('Edit'); ?>
                </a>
                <?php if ($skin->data) { ?>
                <a href="<?php echo URL::base().'skinManager/exportSkins/'.$skin->id; ?>" class="btn btn-info">
                    <i class="icon-trash icon-white"></i>
                    <?php echo __('Export skin'); ?>
                </a>
                <?php } ?>
                <a data-toggle="modal" href="#" data-target="#delete-skin-<?php echo $skin->id; ?>" class="btn btn-danger">
                    <i class="icon-trash icon-white"></i>
                    <?php echo __('Delete'); ?>
                </a>
                <div class="modal hide alert alert-block alert-error fade in" id="delete-skin-<?php echo $skin->id; ?>">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="alert-heading"><?php echo __('Caution! Are you sure?'); ?></h4>
                    </div>
                    <div class="modal-body">
                        <p><?php printf(__('You have just clicked the delete button, are you certain that you wish to proceed with deleting "%s" skin?'),$skin->name); ?></p>
                        <p>
                            <a class="btn btn-danger" href="<?php echo URL::base().'skinManager/deleteSkin/'.$templateData['map']->id.'/'.$skin->id; ?>"><?php echo __('Delete'); ?></a>
                            <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
                        </p>
                    </div>
                </div>
            </td>
        </tr><?php
        } ?>
        </tbody>
    </table><?php
    if ( ! $haveOne) {
        echo '<div class="alert alert-info">'.__("You don't have your own skins. Please create at least one skin.").'</div>';
    } ?>
</div><?php
} ?>