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
    </body>
</html>
<?php } ?>
