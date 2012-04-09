<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <link rel="stylesheet" type="text/css" href="<?php echo URL::base(); ?>css/basic.css" />
    </head>
    <body>
        <center>
            <?php if(isset($map)) { ?>
            <table bgcolor="#ffffff" cellpadding="6" width="80%">
                <tr>
                    <td valign="top">
                        <p>
                            <img src="<?php echo URL::base(); ?>images/notes.gif" border="0" alt="notes" align="absmiddle" />
                            <strong>author notes for Labyrinth21</strong>
                        </p>
                        <form action="<?php echo URL::base(); ?>labyrinthManager/updateDevNodes/<?php echo $map->id; ?>" method="POST">
                            <input type="hidden" name="mapid" value="21">
                                <textarea cols="60" rows="12" name="devnotes"><?php echo $map->dev_notes; ?></textarea>
                                <input type="submit" name="Submit" value="<?php echo __('submit'); ?>" />
                        </form>
                        <p>
                            <a href="javascript:window.close();"><?php echo __('close window'); ?></a>
                        </p>
                    </td>
                </tr>
            </table>
            <?php } ?>
        </center>
    </body>
</html>