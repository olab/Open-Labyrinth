<?php if(isset($templateData['user'])) { ?>
<table width="100%" height="100%" cellpadding='6'>
    <tr>
        <td valign="top" bgcolor="#bbbbcb">
            <h4><?php echo __('Change Labyrinth User Password'); ?></h4>
            <table width="100%" cellpadding="6">
                <tr bgcolor="#ffffff"><td>
                        <form action="<?php echo URL::base(); ?>home/updatePassword" method="post" id="form1" name="form1">
                            <table cellspacing="10" cellpadding="0" border="0">
                                <tr>
                                    <td><p>User ID</p></td>
                                    <td>
                                        <input type="text" name="uid_display" size="30" disabled="disabled" value="<?php echo $templateData['user']->nickname; ?>">
                                    </td>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr><td><p>Current Password</p></td><td><input type="password" name="upw" size="30"></td><td><div id="divMsg1" style="color:#FF0000;"></div></td></tr>
                                <tr><td colspan="3"><hr></td></tr>
                                <tr><td><p>New password</p></td><td><input type="password" name="newpswd" size="30"></td><td><div id="divMsg2" style="color:#FF0000;"></div></td></tr>
                                <tr><td><p>Confirm new password</p></td><td><input type="password" name="pswd_confirm" size="30"></td><td valign="top"><div id="divMsg3" style="color:#FF0000;"></div></td></tr>
                                <tr><td colspan="3"><hr></td></tr>
                                <tr><td colspan="3"><input type="submit" value="Submit"></td></tr>
                            </table>
                        </form>
                    </td></tr>
            </table>
        </td>
    </tr>
</table>
<?php } ?>