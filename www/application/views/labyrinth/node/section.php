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
                <h4><?php echo __('edit node sections for Labyrinth "') . $templateData['map']->name . '"'; ?></h4>
                <table width="100%" cellpadding="6">
                    <tr bgcolor="#ffffff">
                        <td align="left">
                            <table border="0" width="100%" cellpadding="1">
                                <tr>
                                    <td>
                                        <?php if(isset($templateData['node_sections'])) { ?>
                                        <table border="0" width="100%" cellpadding="1">
                                            <?php foreach($templateData['node_sections'] as $nodeSection) { ?>
                                            <tr>
                                                <td bgcolor="#ddddee" colspan="3" align="left">
                                                    <p>
                                                        <a name=""><br><strong>Section: <?php echo $nodeSection->name; ?></strong> [</a>
                                                        <a href="<?php echo URL::base().'nodeManager/editSection/'.$templateData['map']->id.'/'.$nodeSection->id; ?>">edit</a>]
                                                    </p>
                                                </td>
                                            </tr>
                                                <?php if(count($nodeSection->nodes) > 0) { ?>
                                            <tr>
                                                <td>
                                                    <p>
                                                    <?php foreach($nodeSection->nodes as $node) { ?>
                                                        "<?php echo $node->node->title; ?>" - ID:<?php echo $node->node->id; ?> - order:<?php echo $node->order; ?><br>
                                                    <?php } ?>
                                                    </p>
                                                </td>
                                            </tr>
                                                <?php } ?>
                                            <?php } ?>
                                        </table>
                                        <?php } ?>
                                        <hr>
                                        <p><?php echo __('add a node section'); ?>:</p>
                                        <form action="<?php echo URL::base().'nodeManager/addNodeSection/'.$templateData['map']->id; ?>" method="post">
                                            <input type="text" name="sectionname" size="20">
                                            <input type="submit" value="add a node section">
                                        </form>

                                        <hr>

                                        <form action="<?php echo URL::base().'nodeManager/updateSection/'.$templateData['map']->id; ?>" method="post">
                                            <?php if(isset($templateData['sections'])) { ?>
                                            <p>  
                                            <?php foreach($templateData['sections'] as $section) { ?>
                                                <?php echo $section->name; ?> <input type="radio" name="sectionview" value="<?php echo $section->id; ?>" <?php if($templateData['map']->section->id == $section->id) echo 'checked=""'; ?>> |
                                            <?php } ?>
                                                <input type="submit" value="<?php echo __('update'); ?>">
                                            <?php } ?>
                                            </p>
                                        </form>
                                        <br>
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