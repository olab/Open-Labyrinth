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
if (isset($templateData['map']) and isset($templateData['dam'])) { ?>
    <table width="100%" height="100%" cellpadding='6'>
        <tr>
            <td valign="top" bgcolor="#bbbbcb">
                <h4><?php echo __('edit a Labyrinth Data Cluster "') . $templateData['dam']->name . '"'; ?></h4>
                <table width="100%" bgcolor="#ffffff">
                    <tr>
                        <td align="left">
                            <p><strong><img src="<?php echo URL::base(); ?>images/OL_cluster_wee.gif" alt="clusters" align = "absmiddle" border="0" />&nbsp;Preview</strong></p>
                            <table border="2"><tr><td><p><?php if(isset($templateData['preview'])) echo $templateData['preview']; ?></p></td></tr></table>
                            <hr />
                            <p>
                                <p><strong><img src="<?php echo URL::base(); ?>images/OL_cluster_wee.gif" alt="clusters" align = "absmiddle" border="0" />&nbsp;Data Cluster: <?php echo $templateData['dam']->name; ?></strong></p>
                                <?php if(count($templateData['dam']->elements) > 0) { ?>
                                <table>
                                    <?php foreach($templateData['dam']->elements as $element) { ?>
                                    <?php if($element->element_type == 'vpd') { ?>
                                    <form method="post" action="<?php echo URL::base(); ?>clusterManager/updateDamElement/<?php echo $templateData['map']->id; ?>/<?php echo $templateData['dam']->id; ?>/<?php echo $element->id; ?>">
                                        <tr>
                                            <td><p>VPD (<?php echo $element->vpd->type->label; ?>) element (<?php echo $element->element_id; ?>)</p></td>
                                            <td>
                                                <select name='trigger'>
                                                    <option value='immediately' <?php if($element->display == 'immediately') echo 'selected=""'; ?>>immediately</option>
                                                    <option value='ontrigger' <?php if($element->display == 'ontrigger') echo 'selected=""'; ?>>ontrigger</option>
                                                    <option value='delayed' <?php if($element->display == 'delayed') echo 'selected=""'; ?>>delayed</option>
                                                    <option value='ifrequested' <?php if($element->display == 'ifrequested') echo 'selected=""'; ?>>ifrequested</option>
                                                </select>
                                            </td>
                                            <td>
                                                <select name='order'>
                                                    <?php for($i = 1; $i <= count($templateData['dam']->elements); $i++) { ?>
                                                    <option value="<?php echo $i; ?>" <?php if($element->order == $i) echo 'selected=""'; ?>><?php echo $i; ?></option>
                                                    <?php } ?>
                                                </select>
                                            </td>
                                            <td><p><input type="submit" value="update"></p></td>
                                            <td><p><a href="<?php echo URL::base(); ?>clusterManager/removeElementFormDam/<?php echo $templateData['map']->id; ?>/<?php echo $templateData['dam']->id; ?>/<?php echo $element->id; ?>">delete</a></p></td>
                                        </tr>
                                    </form>
                                    <?php } else if($element->element_type == 'mr') { ?>
                                    <form method="post" action="<?php echo URL::base(); ?>clusterManager/updateDamElement/<?php echo $templateData['map']->id; ?>/<?php echo $templateData['dam']->id; ?>/<?php echo $element->id; ?>">
                                        <tr>
                                            <td><p>MR (<?php echo $element->element->name; ?>) element (<?php echo $element->element_id; ?>)</p></td>
                                            <td></td>
                                            <td>
                                                <select name='order'>
                                                    <?php for($i = 1; $i <= count($templateData['dam']->elements); $i++) { ?>
                                                    <option value="<?php echo $i; ?>" <?php if($element->order == $i) echo 'selected=""'; ?>><?php echo $i; ?></option>
                                                    <?php } ?>
                                                </select>
                                            </td>
                                            <td><p><input type="submit" value="update"></p></td>
                                            <td><p><a href="<?php echo URL::base(); ?>clusterManager/removeElementFormDam/<?php echo $templateData['map']->id; ?>/<?php echo $templateData['dam']->id; ?>/<?php echo $element->id; ?>">delete</a></p></td>
                                        </tr>
                                    </form>    
                                    <?php } else if($element->element_type == 'dam') { ?>
                                    <form method="post" action="<?php echo URL::base(); ?>clusterManager/updateDamElement/<?php echo $templateData['map']->id; ?>/<?php echo $templateData['dam']->id; ?>/<?php echo $element->id; ?>">
                                        <tr>
                                            <td><p>DAM (<?php echo $element->edam->name; ?>) element (<?php echo $element->element_id; ?>)</p></td>
                                            <td></td>
                                            <td>
                                                <select name='order'>
                                                    <?php for($i = 1; $i <= count($templateData['dam']->elements); $i++) { ?>
                                                    <option value="<?php echo $i; ?>" <?php if($element->order == $i) echo 'selected=""'; ?>><?php echo $i; ?></option>
                                                    <?php } ?>
                                                </select>
                                            </td>
                                            <td><p><input type="submit" value="update"></p></td>
                                            <td><p><a href="<?php echo URL::base(); ?>clusterManager/removeElementFormDam/<?php echo $templateData['map']->id; ?>/<?php echo $templateData['dam']->id; ?>/<?php echo $element->id; ?>">delete</a></p></td>
                                        </tr>
                                    </form> 
                                    <?php } ?>
                                    <?php } ?>
                                </table>
                                <?php } ?>
                            </p>
                            <table width='100%' border='0' cellspacing='6'>
                                <tr><td><hr />
                                        <form method='post' action='<?php echo URL::base(); ?>clusterManager/updateDamName/<?php echo $templateData['map']->id; ?>/<?php echo $templateData['dam']->id; ?>'>
                                            <p>Data cluster name: <input name='damname' type='text' value='<?php echo $templateData['dam']->name; ?>' />&nbsp;<input type='submit' value='update' />
                                        </form>
                                    </td></tr>
                                <tr><td><hr />
                                        <?php if($templateData['vpds'] != NULL and count($templateData['vpds']) > 0) { ?>
                                        <form method='post' action='<?php echo URL::base(); ?>clusterManager/addElementToDam/<?php echo $templateData['map']->id; ?>/<?php echo $templateData['dam']->id; ?>'>    
                                            <p>add a data element: <select name='vpdid' size='1'>
                                                    <?php foreach($templateData['vpds'] as $vpd) { ?>
                                                    <option value="<?php echo $vpd->id; ?>"><?php echo $vpd->type->label; ?> (<?php echo $vpd->id; ?>)</option>
                                                    <?php } ?>
                                                </select><input type='submit' value='add' />
                                        </form>
                                        <?php } else { ?>
                                        <p>no elements to add</p>
                                        <?php } ?>
                                    </td></tr>
                                <tr><td>
                                        <hr />
                                        <?php if($templateData['files'] != NULL and count($templateData['files']) > 0) { ?>
                                        <form method='post' action='<?php echo URL::base(); ?>clusterManager/addFileToDam/<?php echo $templateData['map']->id; ?>/<?php echo $templateData['dam']->id; ?>'>
                                            <p>add a media resource: <select name='mrid' size='1'>
                                                    <?php foreach($templateData['files'] as $file) { ?>
                                                    <option value="<?php echo $file->id; ?>"><?php echo $file->name; ?></option>
                                                    <?php } ?>
                                                </select><input type='submit' value='add' />
                                        </form>
                                        <?php } else { ?>
                                        <p>no media resources to add</p>
                                        <?php } ?>
                                    </td></tr>
                                <tr><td>
                                        <hr />
                                        <?php if($templateData['dams'] != NULL and count($templateData['dams']) > 0) { ?>
                                        <form method='post' action='<?php echo URL::base(); ?>clusterManager/addDamToDam/<?php echo $templateData['map']->id; ?>/<?php echo $templateData['dam']->id; ?>'>
                                            <p>add a data cluster to this data cluster: <select name='adamid' size='1'>
                                                    <?php foreach($templateData['dams'] as $dam) { ?>
                                                    <option value="<?php echo $dam->id; ?>"><?php echo $dam->name; ?></option>
                                                    <?php } ?>
                                                </select><input type='submit' value='add' />
                                        </form>
                                        <?php } else { ?>
                                        <p>no clusters to add</p>
                                        <?php } ?>
                                    </td></tr>
                            </table>
                            <hr />
                            <br />
                        </td></tr>
                </table>
            </td>
        </tr>
    </table>
<?php } ?>

