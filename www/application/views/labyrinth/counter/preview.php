<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <link rel="stylesheet" type="text/css" href="<?php echo URL::base(); ?>css/basic.css" />
    </head>
    <body>
        <?php if(isset($templateData['counter']) and isset($templateData['map'])) { ?>
        <table cellspacing="6" cellpadding="6">
            <tr>
                <td bgcolor="#FFFFFF" align="center" valign="top"><img src="<?php echo URL::base().$templateData['counter']->icon->path; ?>" alt="preview counter" /></td>
                <td bgcolor="#FFFFFF" valign="top">
                    <h4><?php echo $templateData['counter']->name; ?></h4>
                </td>
                <td bgcolor="#FFFFFF" align="center" valign="top"><p><?php echo __('current value'); ?></p><p><font size="30"><strong><?php echo $templateData['counter']->start_value; ?></strong></font></td>
            </tr>
            <tr>
                <td colspan="3" bgcolor="#FFFFFF" align="center" valign="top">
                    <p>
                        <a href="<?php echo URL::base().'counterManager/index/'.$templateData['map']->id; ?>"><?php echo __('counters'); ?></a>
                    </p>
                </td>
            </tr>
        </table>
        <?php } ?>
    </body>
</html>