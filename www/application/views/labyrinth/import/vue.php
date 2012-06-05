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
            <h4><?php echo __('Vue to Labyrinth upload'); ?></h4>
            <table bgcolor="#ffffff" width="100%"><tr align="left"><td>
                        <form method="POST" enctype="multipart/form-data" action="<?php echo URL::base(); ?>exportImportManager/uploadVUE">
                            <table border="0" width="100%">
                                <tr><td><p><?php echo __('Labyrinth name'); ?></p></td><td><input type="text" size="50" name="mapname"></td></tr>
                                <tr><td><p><?php echo __('select Vue file'); ?></p></td><td><input type="FILE" size="50" name="filename"></td></tr>
                                <tr><td></td><td><input type="submit" name="Submit" value="<?php echo __('submit'); ?>"></td></tr>
                            </table>
                        </form>
                        <hr>

                        <p><?php echo __('Vue is a free visual concept-mapping tool from Tufts University for both Windows and Mac. You can use it to create designs for Labyrinth by creating boxes and links between them. Note that although Vue supports many other features only the boxes, text and links will be imported'); ?></p>
                        <p><?php echo __('On import each box becomes a Labyrinth node and every line between boxes will become a link. Make sure you make your arrows between boxes point in the right direction as these are parsed in the upload process - directionless arrows are interpreted as bidirectional (from A to B and from B to A).'); ?></p>
                        <p><a href="http://vue.uit.tufts.edu/" target="_blank"><?php echo __('get Vue here'); ?></a></p>

                    </td></tr>
            </table>
        </td>
    </tr>
</table>


