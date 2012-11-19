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
if (isset($templateData['map']) and isset($templateData['nodes'])) { ?>
    <table width="100%" height="100%" cellpadding="6">
        <tr>
            <td valign="top" bgcolor="#bbbbcb">
                <h4><?php echo __('counter grid'); ?></h4>
                <table width="100%" cellpadding="6">
                    <tr bgcolor="#ffffff">
                        <td>
                            <?php if(isset($templateData['oneCounter'])) { ?>
                                <form action="<?php echo URL::base().'counterManager/updateGrid/'.$templateData['map']->id.'/'.$templateData['counters'][0]->id; ?>" method="POST">
                            <?php } else { ?>
                                <form action="<?php echo URL::base().'counterManager/updateGrid/'.$templateData['map']->id; ?>" method="POST">
                            <?php } ?>
                                <table border="0" cellpadding="1">
                                    <?php if (count($templateData['nodes']) > 0) { ?>
                                        <?php foreach ($templateData['nodes'] as $node) { ?>
                                            <tr>
                                                <td><p><?php echo $node->title; ?> [<?php echo $node->id; ?>]</p></td>
                                                <?php if(isset($templateData['counters']) and count($templateData['counters']) > 0) { ?>
                                                    <?php foreach($templateData['counters'] as $counter) { ?>
                                                        <td style="min-width: 115px; border-bottom: 1px solid #000;">
                                                            <p style="margin-bottom: 0px;"><?php echo $counter->name; ?> <input type="text" size="5" name="nc_<?php echo $node->id; ?>_<?php echo $counter->id; ?>" value="<?php $c = $node->getCounter($counter->id); if($c != NULL) echo $c->function; ?>"></p>
                                                            <p style="margin-top:0px; text-align: center;"><input type="checkbox" value="1" name="ch_<?php echo $node->id; ?>_<?php echo $counter->id; ?>" <?php if ($c != NULL) {if($c->display == 1) echo 'checked="checked"';}else{echo 'checked="checked"';} ?> /> <?php echo __("appear on node"); ?></p>
                                                        </td>
                                                    <?php } ?>
                                                <?php } ?>
                                            </tr>
                                        <?php } ?>
                                    <?php } ?>
                                    <tr><td colspan="1"><input type="submit" name="Submit" value="<?php echo __('submit'); ?>"></td></tr>
                                </table>
                            </form>
                        </td></tr>
                </table> 
            </td>
        </tr>
    </table>
<?php } ?>
