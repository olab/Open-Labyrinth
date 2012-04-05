<table width="100%" height="100%" cellpadding='6'>
    <tr>
        <td valign="top" bgcolor="#bbbbcb">
            <h4><?php echo __('create Labyrinth'); ?></h4>
            <p><?php echo __('there are four ways to create a new Labyrinth'); ?></p>
            <table width="100%" border="0" cellspacing="6" cellpadding="4" bgcolor="#ffffff">
                <tr align="left">
                    <td nowrap=""><p><a href=<?php echo URL::base().'labyrinthManager/addManual' ?>><?php echo __('add manually'); ?></a></p></td>
                </tr>
                <tr align="left">
                    <td nowrap="">
                        <p><a href="<?php echo URL::base(); ?>exportImportManager/importVUE"><?php echo __('import'); ?> Vue 
                                <img src="<?php echo URL::base(); ?>images/vuelogo.gif" alt="VUE" width="26" height="14" align="absmiddle" id="Img1" border="0"></a></p></td>
                </tr>
                <tr align="left">
                    <td nowrap=""> <p><a href="#"><?php echo __('import MedBiquitous Virtual Patient'); ?><img src="<?php echo URL::base(); ?>images/medbiq_logo_wee.gif" alt="MedBiq" width="53" height="24" align="absmiddle" id="Img2" border="0">
                            </a></p></td>
                </tr>
                <tr align="left">
                    <td nowrap=""><p><?php echo __('duplicate existing Labyrinth'); ?></p></td>
                </tr>
            </table>
        </td>
    </tr>
</table>
