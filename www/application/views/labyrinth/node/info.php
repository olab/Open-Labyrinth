<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <link rel="stylesheet" type="text/css" href="<?php echo URL::base() ?>css/basic.css" />
    </head>
    <body>
        <center>
            <table bgcolor='#ffffff' cellpadding='6' width='80%'>
                <tr>
                    <td valign="top">
                        <img src='<?php echo URL::base(); ?>images/info_blak.gif' border='0' alt='info'>
                            <p>
                                <?php if(isset($info)) echo $info; ?>
                            </p>
                            <p><a href="javascript:window.close();"><?php echo __('close window'); ?></a></p>
                    </td>
                </tr>
            </table>
        </center>
    </body>
</html>