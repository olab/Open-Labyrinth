<table width="100%" height="100%" cellpadding='6'>
    <tr>
        <td valign="top" bgcolor="#bbbbcb">
            <h4>Edit group</h4>
            <?php if (isset($templateData['group'])) { ?>
                <table width="100%" cellpadding="6">
                    <tr bgcolor="#ffffff">
                        <td>
                            <p><a href=<?php echo URL::base() . 'usermanager/deleteGroup/' . $templateData['group']->id; ?>>[delete]</a></p>
                            <form action=<?php echo URL::base() . 'usermanager/updateGroup/' . $templateData['group']->id; ?> method="post">
                                <table>
                                    <tr><td align="left"><p>group name</p></td>
                                        <td align="left"><input type="text" name="groupname" size="50" value="<?php echo $templateData['group']->name; ?>"></td></tr>

                                    <tr><td align="left"><p>&nbsp;</p></td><td align="left">
                                            <input type="submit" name="UpdateGroupSubmit" value="<?php echo __('submit'); ?>"></td></tr>
                                </table>
                            </form>
                            <p><strong>Members</strong></p>
                            <form action=<?php echo URL::base().'usermanager/addMemberToGroup/'.$templateData['group']->id; ?> method="post">
                                <?php if(isset($templateData['users'])) { ?>
                                <select name="userid">
                                    <?php foreach($templateData['users'] as $user) { ?>
                                    <option value="<?php echo $user->id; ?>"><?php echo $user->nickname; ?> (<?php echo $user->username; ?>)</option>
                                    <?php } ?>
                                </select>
                                <input type="submit" name="AddUserToGroupSubmit" value="<?php echo __('submit'); ?>">
                                <?php } ?>
                            </form>
                            
                            <?php if(isset($templateData['members'])) { ?>
                                <?php foreach($templateData['members'] as $member) { ?>
                                    <p><?php echo $member->nickname.'('.$member->username.')';?>[
                                        <a href=<?php echo URL::base().'usermanager/removeMemenber/'.$member->id.'/'.$templateData['group']->id; ?>>remove</a>]</p>
                                <?php } ?>
                            <?php } ?>
                        </td>
                    </tr>
                </table>
            <?php } ?>
        </td>
    </tr>
</table>
