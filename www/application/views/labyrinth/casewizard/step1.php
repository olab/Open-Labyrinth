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
            <h4><?php echo __('Step 1. What kind of VP do you what?'); ?></h4>
            <div style="width:100%; min-height:400px; background:#FFFFFF; position: relative;">
                <div class="wizard_body">
                    <ul class="main">
                        <li>
                            <a href="javascript:void(0);" id="6" class="wizard_button li">Leniar
                                <img style="max-width:240px;" src="<?php echo URL::base(); ?>images/labyrinth_preview/leniar.png" />
                            </a>
                        </li>
                        <li>
                            <a href="javascript:void(0);" id="7" class="wizard_button li">HEIDR
                                <img style="max-width:240px;" src="<?php echo URL::base(); ?>images/labyrinth_preview/heidr.png" />
                            </a>
                        </li>
                        <li>
                            <a href="javascript:void(0);" id="8" class="wizard_button li">Semi-leniar
                                <img style="max-width:240px;" src="<?php echo URL::base(); ?>images/labyrinth_preview/semi-leniar.png" />
                            </a>
                        </li>
                        <li>
                            <a href="javascript:void(0);" id="9" class="wizard_button li">Branched
                                <img style="max-width:240px;" src="<?php echo URL::base(); ?>images/labyrinth_preview/branch.png" />
                            </a>
                        </li>
                        <li>
                            <a href="javascript:void(0);" id="10" class="wizard_button li">Make My Own
                                <img style="max-width:240px;" src="<?php echo URL::base(); ?>images/labyrinth_preview/own.png" />
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="wizard_footer">
                    <a href="javascript:void(0)" style="float:right;" id="step1_w_button" class="wizard_button">Step 2 - Add Story</a>
                </div>
                <form id="step1_form" action="<?php echo URL::base().'labyrinthManager/caseWizard/1/labyrinthType' ?>" method="post">
                    <input autocomplete="off" type="hidden" name="labyrinthType" id="labyrinthType" value="" />
                </form>
            </div>
        </td>
    </tr>
</table>
