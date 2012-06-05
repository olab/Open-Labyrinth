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
    <table width="100%" height="100%" cellpadding='6'>
        <tr>
            <td valign="top" bgcolor="#bbbbcb">
                <h4><?php echo __('data clusters for Labyrinth "') . $templateData['map']->name . '"'; ?></h4>
                <table bgcolor='#ffffff'><tr><td>
                            <p><?php echo __('data clusters act as aggregating and bringing mechanisms between data and media elements'); ?>:</p>
                            <?php if(isset($templateData['dams']) and count($templateData['dams']) > 0) { ?>
                            <?php foreach($templateData['dams'] as $dam) { ?>
                                <p>
                                    <img src='<?php echo URL::base(); ?>images/OL_cluster_wee.gif' alt='clusters' align='absmiddle' border='0' />&nbsp;Name: <?php echo $dam->name; ?> (<?php echo $dam->id; ?>)
                                    - [<a href='<?php echo URL::base(); ?>clusterManager/editCluster/<?php echo $templateData['map']->id; ?>/<?php echo $dam->id; ?>'><?php echo __('edit'); ?></a>] - [<a href='<?php echo URL::base(); ?>clusterManager/deleteDam/<?php echo $templateData['map']->id; ?>/<?php echo $dam->id; ?>'><?php echo __('delete'); ?></a>]</p>
                            <?php } ?>
                            <?php } ?>
                            <hr />
                            <p><a href='<?php echo URL::base(); ?>clusterManager/addDam/<?php echo $templateData['map']->id; ?>'><?php echo __('add data cluster'); ?></a><br /><br /></p>
                        </td></tr>
                </table>
            </td>
        </tr>
    </table>
<?php } ?>

