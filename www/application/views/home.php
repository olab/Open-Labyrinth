<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title><?php if(isset($templateData['title'])) echo $templateData['title']; ?></title>
        <link rel="stylesheet" type="text/css" href="<?php echo URL::base(); ?>css/basic.css" />
    </head>

    <body>
        <center>
            <table bgcolor='#ffffff' cellpadding='6' width='100%'>
                <tr>
                    <td valign="top" width="20%">
                        <a href="<?php echo URL::base(); ?>"><img src="<?php echo URL::base(); ?>images/openlabyrinth-logo.jpg" alt="Labyrinth Identity" border="0" width="150" height="150"></a>
                        <h5> <font color="#000000">OpenLabytinth</font></h5>
                        <?php if(isset($templateData['left'])) echo $templateData['left']; ?>
                        <hr />

                        <h5><font color="#000000">3.0</font></h5>
                    </td>
                    <td bgcolor='#ffffff' align='left'width="60%" valign="top">
                        <?php if(isset($templateData['center'])) echo $templateData['center']; ?>
                    </td>
                    <td width="20%" valign="top">
                        <?php if(isset($templateData['right'])) echo $templateData['right']; ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="3"><?php if(isset($templateData['error'])) echo $templateData['error']; ?></td>
                </tr>
            </table>
        </center>
    </body>
</html>
