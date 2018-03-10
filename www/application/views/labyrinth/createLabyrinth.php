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
?>
<table width="100%" height="100%" cellpadding='6'>
    <tr>
        <td valign="top" bgcolor="#bbbbcb">
            <h4><?php echo __('create Labyrinth'); ?></h4>
            <p><?php echo __('there are five ways to create a new Labyrinth'); ?></p>
            <table width="100%" border="0" cellspacing="6" cellpadding="4" bgcolor="#ffffff">
                <tr align="left">
                    <td nowrap=""><p><a href="<?php echo URL::base().'labyrinthManager/caseWizard' ?>"><?php echo __('Step-by-Step Case Wizard'); ?></a></p></td>
                </tr>
                <tr align="left">
                    <td nowrap=""><p><a href="<?php echo URL::base().'labyrinthManager/addManual' ?>"><?php echo __('add manually'); ?></a></p></td>
                </tr>
                <tr align="left">
                    <td nowrap="">
                        <p><a href="<?php echo URL::base(); ?>exportImportManager/importVUE"><?php echo __('import'); ?> Vue 
                                <img src="<?php echo URL::base(); ?>images/vuelogo.gif" alt="VUE" width="26" height="14" align="absmiddle" id="Img1" border="0"></a></p></td>
                </tr>
                <tr align="left">
                    <td nowrap=""> <p><a href="<?php echo URL::base(); ?>exportImportManager/import"><?php echo __('import MedBiquitous Virtual Patient'); ?><img src="<?php echo URL::base(); ?>images/medbiq_logo_wee.gif" alt="MedBiq" width="53" height="24" align="absmiddle" id="Img2" border="0">
                            </a></p></td>
                </tr>
                <tr align="left">
                    <td nowrap=""><p><?php echo __('duplicate existing Labyrinth'); ?></p></td>
                </tr>
            </table>
        </td>
    </tr>
</table>
