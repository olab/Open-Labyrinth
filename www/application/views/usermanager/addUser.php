<table width="100%" height="100%" cellpadding='6'>
    <tr>
        <td valign="top" bgcolor="#bbbbcb">
            <h4><?php echo __('Create user account'); ?></h4>
            <table width="100%" cellpadding="6">
                <tr bgcolor="#ffffff"><td>
                        <form action=<?php echo URL::base().'usermanager/newUserSummary'; ?> method="post">F
                            <table>
                                <tr><td align="left"><p><?php echo __('username'); ?></p></td><td align="left"><input type="text" name="uid" size="20" value=""></td></tr>
                                <tr><td align="left"><p><?php echo __('password'); ?></p></td><td align="left"><input type="password" name="upw" size="20" value=""></td></tr>
                                <tr><td align="left"><p><?php echo __('name'); ?></p></td><td align="left"><input type="text" name="uname" size="50" value=""></td></tr>
                                <tr><td align="left"><p><?php echo __('e-mail'); ?></p></td><td align="left"><input type="text" name="uemail" size="50" value=""></td></tr>

                                <tr><td align="left"><p>
                                            <?php echo __('user type'); ?>
                                        </p></td><td align="left">
                                        <select name="usertype">
                                            <?php if(isset($templateData['types'])) { ?>
                                                <?php foreach($templateData['types'] as $type) { ?>
                                                    <option value="<?php echo $type->id; ?>"><?php echo $type->name; ?></option>
                                                <?php } ?>
                                            <?php } ?>
                                        </select>
                                    </td></tr>

                                <tr><td align="left"><p><?php echo __('language'); ?></p></td><td align="left"><p>[english <input type="radio" name="langID" value="1">] [francais <input type="radio" name="langID" value="2">] </p></td></tr>

                                <tr><td align="left"><p>&nbsp;</p></td><td align="left"><input type="submit" name="SaveNewUserSubmit" value="<?php echo __('submit'); ?>"></td></tr>
                            </table>
                        </form>
                        <p><a href=<?php echo URL::base().'usermanager'; ?>><?php echo __('users'); ?></a></p>
                    </td></tr>
            </table>
        </td>
    </tr>
</table>