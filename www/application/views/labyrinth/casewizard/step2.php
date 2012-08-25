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
            <h4><?php echo __('Step 2. Add global information'); ?></h4>
            <div style="width:100%; min-height:400px; background:#FFFFFF; position: relative;">
                <div class="wizard_body">
                    <form id="step2_form" method="post" action="<?php echo URL::base().'labyrinthManager/caseWizard/2/addNewLabyrinth' ?>">
                    <table width="100%" cellspacing="0" cellpadding="4" border="0">
                        <tbody><tr>
                            <td width="33%" align="right"><p>Title: </p></td>
                            <td width="50%"><p><input type="text" value="" size="40" id="mtitle" name="title"></p></td>
                        </tr>
                        <tr>
                            <td align="right"><p>Description: </p></td>
                            <td align="left"><p><textarea id="mdesc" rows="5" cols="40" name="description"></textarea></p></td>
                        </tr>
                        <tr>
                            <td align="right"><p>Keywords: </p></td>
                            <td align="left"><p>
                                <input type="text" value="" size="40" id="keywords" name="keywords">
                            </p>
                            </td>
                        </tr>
                        <tr>
                            <td align="right"><p>Timing: </p></td>
                            <td align="left"><p>
                                timing off<input type="radio" checked="" value="0" name="timing"> : timing on<input type="radio" value="1" name="timing">
                                <br><br>
                                time delta (seconds)<input type="text" size="6" value="" id="delta_time" name="delta_time">
                            </p>
                            </td>
                        </tr>
                        <tr>
                            <td align="right"><p>Security: </p></td>
                            <td align="left">
                                <p>
                                    <input type="radio" value="1" name="security">open access<br>
                                    <input type="radio" value="2" name="security">closed (only logged in Labyrinth users can see it)<br>
                                    <input type="radio" value="3" name="security">private (only registered authors and users can see it)<br>
                                    <input type="radio" value="4" name="security">keys (a key is required to access this Labyrinth) - <a href="editKeys">edit</a>
                                    </p>
                            </td>
                        </tr>
                        <tr>
                            <td align="right"><p>Section browsing: </p></td>
                            <td align="left">
                                <p>
                                don't show<input type="radio" value="1" name="section"> |
                                visible<input type="radio" value="2" name="section"> |
                                navigable<input type="radio" value="3" name="section"> |
                                </p>
                            </td>
                        </tr>
                        </tbody></table>
                    </form>
                </div>
                <div class="wizard_footer">
                    <a id="step2_w_button" href="javascript:void(0)" style="float:right;" class="wizard_button">Step 3 - Add your story</a>
                    <a href="<?php echo URL::base(); ?>" style="float:right;" class="wizard_button">Save & return later</a>
                    <a href="<?php echo URL::base().'labyrinthManager/caseWizard/1'; ?>" style="float:left;" class="wizard_button">Return to step 1.</a>
                </div>
            </div>
        </td>
    </tr>
</table>
