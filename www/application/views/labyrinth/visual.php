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
if(isset($templateData['map'])) { ?>



            <p><strong><img src="<?php echo URL::base(); ?>images/openlabyrinth-powerlogo-wee.jpg" height="20" width="118" alt="OpenLabyrinth" border="0"> Visual Editor for Map <?php echo $templateData['map']->id; ?> "<?php echo $templateData['map']->name; ?>"</strong>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                [<a href="#" onclick="window.open('#', 'map viewer help', 'toolbar=no, directories=no, location=no, status=no, menubar=no, resizable=yes, scrollbars=yes, width=500, height=400'); return false">help</a>]
            </p>
            <object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,0,0" width="1400" height="1200" id="Object1" align="top">
                <param name="allowScriptAccess" value="sameDomain">
                <param name="FlashVars" value="dataXML=<?php echo URL::base(); ?>export/visual_editor/mapview_<?php echo $templateData['map']->id; ?>.xml">
                <param name="allowFullScreen" value="true">
                <param name="movie" value="<?php echo URL::base(); ?>documents/viewer.swf">
                <param name="quality" value="high">
                <embed src="<?php echo URL::base(); ?>documents/viewer.swf" flashvars="dataXML=<?php echo URL::base(); ?>export/visual_editor/mapview_<?php echo $templateData['map']->id; ?>.xml" quality="high" width="1400" height="1200" name="mapv<?php echo $templateData['map']->id; ?>" align="top" allowscriptaccess="sameDomain" allowfullscreen="true" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer">
            </object>


<?php } ?>