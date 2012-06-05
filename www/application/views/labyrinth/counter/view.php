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
    <table width="100%" height="100%" cellpadding="6">
        <tr>
            <td valign="top" bgcolor="#bbbbcb">
                <h4><?php echo __('counters') . ' "' . $templateData['map']->name . '"'; ?></h4>
                <table width="100%" cellpadding="6">
                    <tr bgcolor="#ffffff">
                        <td>
                            <table border="0" width="100%" cellpadding="1">
                                <tr>
                                    <td>
                                        <?php if(isset($templateData['counters']) and count($templateData['counters']) > 0) { ?>
                                            <?php foreach($templateData['counters'] as $counter) { ?>
                                                <p>
                                                    <?php echo $counter->name; ?> 
                                                    [<a href="<?php echo URL::base().'counterManager/editCounter/'.$templateData['map']->id.'/'.$counter->id; ?>"><?php echo __('edit'); ?></a> 
                                                    - <a href="<?php echo URL::base().'counterManager/previewCounter/'.$templateData['map']->id.'/'.$counter->id; ?>"><?php echo __('preview'); ?></a> 
                                                    - <a href="<?php echo URL::base().'counterManager/grid/'.$templateData['map']->id.'/'.$counter->id; ?>">grid</a>  
                                                    - <a href="<?php echo URL::base().'counterManager/deleteCounter/'.$templateData['map']->id.'/'.$counter->id; ?>"><?php echo __('delete'); ?></a>]
                                                </p>
                                            <?php } ?>
                                        <?php } ?>
                                        <hr>
                                        <p><a href="<?php echo URL::base().'counterManager/addCounter/'.$templateData['map']->id; ?>"><?php echo __('add counter'); ?></a></p>
                                        <p><a href="<?php echo URL::base().'counterManager/grid/'.$templateData['map']->id; ?>"><?php echo __('counter grid'); ?></a></p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
<?php } ?>