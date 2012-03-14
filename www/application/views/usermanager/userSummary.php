<table width="100%" height="100%">
    <tr>
        <td valign="top" bgcolor="#bbbbcb">
            <h4><?php echo __('User account'); ?></h4>
            <table width="100%" cellpadding="6">
                <tr bgcolor="#ffffff"><td>
                        <p>username: <strong><?php if(isset($templateData['newUser']['uid'])) echo $templateData['newUser']['uid']; ?></strong><br>
                           password: <strong><?php if(isset($templateData['newUser']['upw'])) echo $templateData['newUser']['upw']; ?></strong><br>
                           name: <strong><?php if(isset($templateData['newUser']['uname'])) echo $templateData['newUser']['uname']; ?></strong><br>
                           e-mail: <strong><?php if(isset($templateData['newUser']['uemail'])) echo $templateData['newUser']['uemail']; ?></strong><br>
                           user type: <strong><?php if(isset($templateData['newUser']['usertype'])) echo $templateData['newUser']['usertype']; ?></strong><br>
                           language: <strong><?php if(isset($templateData['newUser']['langID'])) echo $templateData['newUser']['langID']; ?></strong></p>
                        <p><a href=<?php echo URL::base().'usermanager'; ?>>users</a></p>
                        <p><a href=<?php echo URL::base().'usermanager/saveNewUser'; ?>>add user</a></p>
                    </td></tr>
            </table>
        </td>
    </tr>
</table>