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
            <h4><?php echo __('Step 3. Add your story'); ?></h4>
            <div style="width:100%; min-height:400px; background:#FFFFFF; position: relative;">
                <div class="wizard_body">
                    <div class="instractions">
                        <p class="header">Instructions:</p>
                        <p class="li">This is hight level editing. Just get the main points of your story down one idea per node.</p>
                    </div>
                    <div class="visual-editor">
                        <object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,0,0" width="100%" height="100%" id="Object1" align="top">
                            <param name="allowScriptAccess" value="sameDomain">
                            <param name="FlashVars" value="dataXML=<?php echo URL::base(); ?>export/visual_editor/mapview_<?php echo $templateData['map']; ?>.xml&ControllerName=labyrinthManager&ActionName=caseWizard/3/updateVisualEditor/<?php echo $templateData['map']; ?>">
                            <param name="allowFullScreen" value="true">
                            <param name="movie" value="<?php echo URL::base(); ?>documents/viewer.swf">
                            <param name="quality" value="high">
                            <embed src="<?php echo URL::base(); ?>documents/viewer.swf" flashvars="dataXML=<?php echo URL::base(); ?>export/visual_editor/mapview_<?php echo $templateData['map']; ?>.xml&ControllerName=labyrinthManager&ActionName=caseWizard/3/updateVisualEditor/<?php echo $templateData['map']; ?>" quality="high" width="100%" height="100%" name="mapv<?php echo $templateData['map']; ?>" align="top" allowscriptaccess="sameDomain" allowfullscreen="true" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer">
                        </object>
                    </div>
                </div>
                <div class="wizard_footer">
                    <a href="<?php echo URL::base().'labyrinthManager/caseWizard/4/editNode/'.$templateData['map']; ?>" style="float:right;" class="wizard_button">Step 4 - Add other elements</a>
                    <a href="<?php echo URL::base(); ?>" style="float:right;" class="wizard_button">Save & return later</a>
                    <a href="<?php echo URL::base().'labyrinthManager/caseWizard/2/'.$templateData['map']; ?>" style="float:left;" class="wizard_button">Return to step 2.</a>
                </div>
            </div>
        </td>
    </tr>
</table>
