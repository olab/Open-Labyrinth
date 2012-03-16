<table width="100%" height="100%" cellpadding='6'>
    <tr>
        <td valign="top" bgcolor="#bbbbcb">
            <h4><?php echo __('User account'); ?></h4>
            <table width="100%" cellpadding="6">
                <tr bgcolor="#ffffff"><td>
                        <p><?php echo __('username'); ?>: <strong><?php if(isset($templateData['newUser']['uid'])) echo $templateData['newUser']['uid']; ?></strong><br>
                           <?php echo __('password'); ?>: <strong><?php if(isset($templateData['newUser']['upw'])) echo $templateData['newUser']['upw']; ?></strong><br>
                           <?php echo __('name'); ?>: <strong><?php if(isset($templateData['newUser']['uname'])) echo $templateData['newUser']['uname']; ?></strong><br>
                           <?php echo __('e-mail'); ?>: <strong><?php if(isset($templateData['newUser']['uemail'])) echo $templateData['newUser']['uemail']; ?></strong><br>
                           <?php echo __('user type'); ?>: <strong><?php if(isset($templateData['newUser']['usertype'])) echo $templateData['newUser']['usertype']; ?></strong><br>
                           <?php echo __('language'); ?>: <strong><?php if(isset($templateData['newUser']['langID'])) echo $templateData['newUser']['langID']; ?></strong></p>
                        <p><a href=<?php echo URL::base().'usermanager'; ?>><?php echo __('users'); ?></a></p>
                        <p><a href=<?php echo URL::base().'usermanager/saveNewUser'; ?>><?php echo __('add').' '.__('user'); ?></a></p>
                    </td></tr>
            </table>
        </td>
    </tr>
</table>