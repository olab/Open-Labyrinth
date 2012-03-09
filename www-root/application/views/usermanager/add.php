<form action="../usermanager/addNewUser" method="post">
    <table>

        <tr><td align="left"><p>username</p></td><td align="left"><input type="text" name="uname" size="20" value="" /></td></tr>
        <tr><td align="left"><p>password</p></td><td align="left"><input type="password" name="upw" size="20" value="" /></td></tr>
        <tr><td align="left"><p>name</p></td><td align="left"><input type="text" name="udname" size="50" value="" /></td></tr>
        <tr><td align="left"><p>e-mail</p></td><td align="left"><input type="text" name="uemail" size="50" value="" /></td></tr>

        <tr><td align="left"><p>
                    user type
                </p></td><td align="left">
                <select name='usertype'>
                    <?php foreach($roles as $role) { ?>
                    <option value="<?php echo $role->id; ?>"><?php echo $role->name; ?></option>
                    <?php } ?>
                </select>
            </td></tr>

        <tr><td align="left"><p>language</p></td>
            <td align="left">
                <select name='userlang'>
                    <?php foreach ($langs as $lang) { ?>
                        <option value="<?php echo $lang->id; ?>"><?php echo $lang->name; ?></option>
                    <?php } ?>
                </select>
            </td>
        </tr>

        <tr><td align="left"><p>&nbsp;</p></td><td align="left"><input type="submit" name="Submit" value="submit" /></td></tr>
    </table>
</form>