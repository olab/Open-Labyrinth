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
            <h4><?php echo __('MVP to Labyrinth upload'); ?></h4>
            <table width="100%" bgcolor="#ffffff"><tbody><tr><td>
                <form action="<?php echo URL::base(); ?>exportImportManager/uploadMVP" enctype="multipart/form-data" method="POST">
                    <table width="20%" border="0">
                        <tbody>
                        <tr>
                            <td>
                                <input type="file" name="filename" size="50">
                            </td>
                            <td align="center">
                                <input type="submit" value="<?php echo __('submit'); ?>" name="Submit">
                            </td>
                        </tr>
                        <tr>
                            <td align="center"><span style="font-size: 14px; color: #333333;"><?php echo __('The maximum file upload size is 20.00 MB.'); ?></span></td>
                            <td></td>
                        </tr>
                        </tbody></table>
                </form>
                <hr>
                <table><tbody><tr><td>
                    <img width="105" height="47" border="0" align="absmiddle" id="Img2" alt="MVP" src="<?php echo URL::base(); ?>images/medbiq_logo.gif">
                </td><td align="left"><p><?php echo __('OpenLabyrinth imports and exports to the MedBiquitous virtual patient data specification. For more information see'); ?> <a target="_blank" style="text-decoration: underline;" href="http://www.medbiq.org/working_groups/virtual_patient/index.html"><?php echo __('MedBiquitous VPWG'); ?></a>.
                </p></td></tr></tbody></table>
            </td></tr></tbody></table>
        </td>
    </tr>
</table>


