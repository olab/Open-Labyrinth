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
                <h4><?php echo __('Labyrinth information') . ' "' . $templateData['map']->name . '"'; ?></h4>
                <table width="100%" border="0" cellspacing="0" cellpadding="4" bgcolor="#ffffff">
                    <tr>
                        <td width="33%" align="right"><p><?php echo __('title'); ?></p></td>
                        <td width="50%" align="left"><p><?php echo $templateData['map']->name; ?>&nbsp;</p></td>
                    </tr>
                    <tr>
                        <td align="right"><p><?php echo __('authors'); ?></p></td>
                        <td align="left"><p>
                                <?php if(count($templateData['map']->authors) > 0) { ?>
                                <?php foreach($templateData['map']->authors as $author) { ?>
                                    <?php echo $author->user->nickname; ?> (<?php echo $author->user->username; ?>), 
                                <?php } ?>
                                <?php } ?>
                                &nbsp;</p></td>
                    </tr>
                    <tr>
                        <td align="right"><p><?php echo __('keywords'); ?></p></td>
                        <td align="left"><p><?php echo $templateData['map']->keywords; ?>&nbsp;</p></td>
                    </tr>
                    <tr>
                        <td align="right"><p><?php echo __('Labyrinth type'); ?></p></td>
                        <td align="left"><p><?php echo $templateData['map']->type->name; ?>&nbsp;</p></td>
                    </tr>
                    <tr>
                        <td align="right"><p><?php echo __('security'); ?></p></td>
                        <td align="left"><p><?php echo $templateData['map']->security->name; ?>&nbsp;</p></td>
                    </tr>
                    <tr>
                        <td align="right"><p><?php echo __('number of nodes'); ?></p></td>
                        <td align="left"><p>
                                <?php 
                                if(count($templateData['map']->nodes) > 0) { 
                                    echo count($templateData['map']->nodes);
                                } else {
                                    echo '0';
                                }
                                ?>
                                &nbsp;</p></td>
                    </tr>
                    <tr>
                        <td align="right"><p><?php echo __('number of links'); ?></p></td>
                        <td align="left"><p>0&nbsp;</p></td>
                    </tr>
                                    <tr>
                    <td>
                    <?php


                    $vars = $templateData["map"]->as_array();

                    foreach ($vars as $property):?>
                        <?php if (Helper_Controller_Metadata::isMetadataRecord($property)): ?>
                            <?php echo Helper_Controller_Metadata::getView($property); ?>
                            <?php endif; ?>
                        <?php endforeach;?>
                    </td>
                </tr>
                </table>
            </td>
        </tr>
    </table>
<?php } ?>

