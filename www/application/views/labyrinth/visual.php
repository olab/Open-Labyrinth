<?php if(isset($templateData['map'])) { ?>
<table bgcolor="#ffffff" cellpadding="6" width="100%">
    <tr>
        <td valign="top" width="20%">

            <p><strong><img src="<?php echo URL::base(); ?>images/openlabyrinth-powerlogo-wee.jpg" height="20" width="118" alt="OpenLabyrinth" border="0"> Visual Editor for Map <?php echo $templateData['map']->id; ?> "<?php echo $templateData['map']->name; ?>"</strong>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                [<a href="mapviewhelp.asp" onclick="window.open('mapviewhelp.asp', 'map viewer help', 'toolbar=no, directories=no, location=no, status=no, menubar=no, resizable=yes, scrollbars=yes, width=500, height=400'); return false">help</a>]
            </p>
            <object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,0,0" width="1400" height="1200" id="Object1" align="top">
                <param name="allowScriptAccess" value="sameDomain">
                <param name="FlashVars" value="dataXML=<?php echo URL::base(); ?>export/visual_editor/mapview_<?php echo $templateData['map']->id; ?>.xml">
                <param name="allowFullScreen" value="true">
                <param name="movie" value="<?php echo URL::base(); ?>documents/viewer.swf">
                <param name="quality" value="high">
                <embed src="<?php echo URL::base(); ?>documents/viewer.swf" flashvars="dataXML=<?php echo URL::base(); ?>export/visual_editor/mapview_<?php echo $templateData['map']->id; ?>.xml" quality="high" width="1400" height="1200" name="mapv<?php echo $templateData['map']->id; ?>" align="top" allowscriptaccess="sameDomain" allowfullscreen="true" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer">
            </object>

        </td>
    </tr>
</table>
<?php } ?>