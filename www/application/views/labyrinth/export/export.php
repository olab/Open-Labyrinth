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

                <h1><?php echo __('export Labyrinth') . ' "' . $templateData['map']->name . '"'; ?></h1>

                            <p><strong>MedBiquitous Virtual Patient Export</strong></p>
                            <form class="form-horizontal">
                                <p>To export to a MVP package first select a licence for this package:</p>
                                <select name="licence">
                                    <option value="">select from the following ...</option>
                                    <option value="public_domain">public domain (no restrictions)</option>
                                    <option value="GNU Public Licence v2">GNU Public Licence v2</option>
                                    <option value="GNU Public Licence v3">GNU Public Licence v3</option>
                                    <option value="Creative Commons: Attribution">Creative Commons: Attribution</option>
                                    <option value="Creative Commons: Attribution Share Alike">Creative Commons: Attribution Share Alike</option>
                                    <option value="Creative Commons: Attribution No Derivatives">Creative Commons: Attribution No Derivatives</option>
                                    <option value="Creative Commons: Attribution Non-Commercial">Creative Commons: Attribution Non-Commercial</option>
                                    <option value="Creative Commons: Attribution Non-Commercial Share Alike">Creative Commons: Attribution Non-Commercial Share Alike</option>
                                    <option value="Creative Commons: Attribution Non-Commercial No Derivatives">Creative Commons: Attribution Non-Commercial No Derivatives</option>
                                    <option value="all_rights_reserved">all rights reserved</option>
                                </select>
                                <input type="submit" name="Submit" value="<?php echo __('submit'); ?>">
                            </form>
                            <hr>
                            <p><strong>Vue Export and Sync</strong></p>
                            <p><img src="<?php echo URL::base(); ?>images/vuelogo.gif" alt="VUE" width="26" height="14" align="absmiddle" id="Img1" border="0"> <a href="<?php echo URL::base(); ?>exportImportManager/exportVUE/<?php echo $templateData['map']->id; ?>">export to a Vue file</a></p>
                            <p>note the characters \\\---/// indicate the separation between title and body text</p><p>retain but do not duplicate for the title and body to resolve properly on resync</p>
                            <hr>
                            <form method="POST" enctype="multipart/form-data" action="#">
                                <p><img src="<?php echo URL::base(); ?>images/vuelogo.gif" alt="VUE" width="26" height="14" align="absmiddle" id="Img2" border="0"> resync Vue file with this Labyrinth</p>
                                <p>CAUTION: this will add/remove nodes and links and change content to reflect the uploaded Vue file topology</p>
                                <input type="FILE" size="50" name="FILE1">
                                <input type="submit" name="Submit" value="<?php echo __('submit'); ?>">
                            </form>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
<?php } ?>