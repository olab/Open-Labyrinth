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
if (isset($templateData['map']) && isset($templateData['file']) && (strstr($templateData['file']->mime, 'image'))) {
    $src = URL::base().$templateData['file']->path;
    $size = getimagesize(DOCROOT.$src);
    ?>
<script type="text/javascript" src="<?php echo URL::base(); ?>scripts/jquery.cropzoom.js"></script>
<script type="text/javascript">
    jQuery(document).ready(function(){
        var cropzoom = $('#image_editor').cropzoom({
            width:1000,
            height:600,
            bgColor: '#CCC',
            enableRotation:true,
            enableZoom:true,
            zoomSteps:10,
            rotationSteps:10,
            selector:{
                centered:true,
                borderColor:'blue',
                borderColorHover:'yellow'
            },
            image:{
                source:'<?php echo $src.'?'.time(); ?>',
                width:<?php echo $size[0]; ?>,
                height:<?php echo $size[1]; ?>,
                snapToContainer:false,
                minZoom:50,
                maxZoom:500
            }
        });
        jQuery('#submit').click(function(){
            jQuery('#buttons').css('display', 'none');
            jQuery('#processing').css('display', 'block');
            cropzoom.send('<?php echo URL::base().'fileManager/imageEditorPost/'.$templateData['map']->id.'/'.$templateData['file']->id; ?>','POST',{'filesrc':'<?php echo $src; ?>'},function(rta){
                window.location.href = '<?php echo URL::base().'fileManager/index/'.$templateData['map']->id; ?>';
            });
        });
        jQuery('#restore').click(function(){
            cropzoom.restore();
        });
    });
</script>
<table width="100%" height="100%" cellpadding="6">
    <tr>
        <td valign="top" bgcolor="#bbbbcb">
            <h4><?php echo __('Image Editor: "') . $templateData['file']->name . '"'; ?></h4>
            <table width="100%" border="0" cellspacing="6" bgcolor="#ffffff">
                <tr>
                    <td>
                        <div id="image_editor"></div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <p id="buttons">
                            <input id="submit" type="submit" name="Submit" value="<?php echo __('Crop and Save'); ?>" />
                            <input id="restore" type="button" value="<?php echo __('Undo'); ?>" />
                        </p>
                        <p style="display:none;" id="processing"><?php echo __("Processing...") ?></p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<?php } ?>