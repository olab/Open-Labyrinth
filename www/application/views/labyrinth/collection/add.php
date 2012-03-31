<table width="100%" height="100%" cellpadding='6'>
    <tr>
        <td valign="top" bgcolor="#bbbbcb">
            <h4><?php echo __('add Collection'); ?></h4>
            <p><a href="<?php echo URL::base(); ?>collectionManager">Collections</a></p>
            <table width="100%" cellpadding="6">
                <tr bgcolor="#ffffff"><td>
                        <table>
                            <form method="POST" action="<?php echo URL::base(); ?>collectionManager/saveNewCollection">
                            <tr><td>
                                    <p>colection name</p></td>
                                <td><input type="text" name="colname" value="">
                                    <input type="submit" value="submit">
                                </td></tr>
                            </form>
                        </table>
                    </td></tr>
            </table>
        </td>
    </tr>
</table>


