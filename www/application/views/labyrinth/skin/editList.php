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
        <tbody>
        <?php foreach($templateData['skinList'] as $skin){
        $haveOne = true;
        ?>
        <tr>
            <td>
                <p><?php echo $skin->id;  ?></p>
            </td>
            <td>
                <p><?php echo $skin->name; ?></p>
            </td>
            <td class="center">
                <a href="<?php echo URL::base().'skinManager/editSkins/'.$templateData['map']->id.'/'.$skin->id; ?>" class="btn btn-info">
                    <i class="icon-edit icon-white"></i>
                    <?php echo __('Edit'); ?>
                </a>
            </td>
        </tr>
        <?php } ?>
        </tbody>
    </table>
    <?php if (!$haveOne){
        echo '<div class="alert alert-info">'.__("You don't have your own skins. Please create at least one skin.").'</div>';
    } ?>
</div>
<?php } ?>