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
                <h4><?php echo __('data elements for Labyrinth "') . $templateData['map']->name . '"'; ?></h4>
                <table bgcolor="#ffffff" width="100%"><tr><td align="left">
                            <?php if(isset($templateData['vpds']) and count($templateData['vpds']) > 0) { ?>
                            <p>data elements:</p>
                            <?php foreach($templateData['vpds'] as $vpd) { ?>
                                <p>
                                    <strong><img src="<?php echo URL::base(); ?>images/OL_element_wee.gif" alt="elements" align="absmiddle" border="0">&nbsp;<?php echo $vpd->type->label; ?> (<?php echo $vpd->id; ?>)</strong> 
                                    - <a href="<?php echo URL::base(); ?>elementManager/editVpd/<?php echo $templateData['map']->id; ?>/<?php echo $vpd->id; ?>"><?php echo __('edit'); ?></a> 
                                    - <a href="<?php echo URL::base(); ?>elementManager/deleteVpd/<?php echo $templateData['map']->id; ?>/<?php echo $vpd->id; ?>"><?php echo __('delete'); ?></a>
                                    <br>
                                    <?php if(count($vpd->elements) > 0) { ?>
                                        <?php foreach($vpd->elements as $element) { ?>
                                            <?php echo $element->key; ?> = <?php echo $element->value; ?><br>
                                        <?php } ?>
                                    <?php } ?>
                                </p>
                                <hr>
                            <?php } ?>
                            <?php } ?>
                            <hr>
                            <p><a href="<?php echo URL::base(); ?>elementManager/addNewElement/<?php echo $templateData['map']->id; ?>"><?php echo __('add a new data element'); ?></a></p>
                        </td></tr>
                </table>
            </td>
        </tr>
    </table>
<?php } ?>