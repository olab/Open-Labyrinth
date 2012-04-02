<?php if(isset($templateData['presentation'])) { ?>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>OpenLabyrinth</title>
        <link rel="stylesheet" type="text/css" href="<?php echo URL::base(); ?>css/basic.css">
    </head>
    <body>
        <center>
            <table bgcolor="#ffffff" cellpadding="6" width="100%">
                <tr>
                    <td valign="top" width="20%">
                        <table width="100%" cellpadding="6">
                            <tr bgcolor="#ffffff">
                                <td align="left">
                                    <table border="0" width="100%" cellpadding="1">
                                        <tr>
                                            <td valign="top">
                                                <h4><?php echo $templateData['presentation']->title; ?></h4>
                                                <p><?php echo $templateData['presentation']->header; ?></p>
                                                <hr>
                                                    <?php foreach($templateData['presentation']->maps as $map) { ?>
                                                    <p><a href="<?php echo URL::base(); ?>renderLabyrinth/index/<?php echo $map->map->id; ?>"><?php echo $map->map->name; ?></a></p>
                                                    <?php } ?>
                                                    <hr />
                                                    <p><?php echo $templateData['presentation']->footer; ?></p>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </center>

        <span id="skype_highlighting_settings" display="none" autoextractnumbers="1"></span>
        <object id="skype_plugin_object" location.href="http://localhost:8081/openlabyrinth/presentation.asp?pid=1" location.hostname="localhost" style="position: absolute; visibility: hidden; left: -100px; top: -100px; " width="0" height="0" type="application/x-vnd.skype.click2call.chrome.5.7.0"></object>
    </body>
</html>
<?php } ?>
