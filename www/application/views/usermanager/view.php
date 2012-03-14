<table width="100%" height="100%">
    <tr>
        <td valign="top" bgcolor="#bbbbcb">
            <h4>Users</h4>
            <table width="100%" cellpadding="6">
                <tr bgcolor="#ffffff"><td>
                        <p><strong>users</strong>:&nbsp;<?php if (isset($templateData['userCount'])) echo $templateData['userCount']; ?>&nbsp;registered users&nbsp;[<a href=<?php echo URL::base() . 'usermanager/addUser' ?>>add user</a>]</p>
                        <table width="100%" cellpadding="2">
                            <?php
                            if (isset($templateData['users'])) {
                                $count = 1;
                                $currentIndex = 0;
                                $outTD = array();
                                $outTD[$currentIndex] = '';
                                foreach ($templateData['users'] as $user) {
                                    if ($count == 5) {
                                        $currentIndex++;
                                        $count = 1;
                                        $outTD[$currentIndex] = '';
                                    }

                                    $outTD[$currentIndex] .= '<td valign="top" width="20%" nowrap=""><p>';
                                    if ($user->id == $templateData['currentUserId']) {
                                        $outTD[$currentIndex] .= 'YOU:';
                                    }
                                    $outTD[$currentIndex] .= $user->username . '<br/>';
                                    $outTD[$currentIndex] .= __('type') . ':' . $user->type->name . '<br/>';
                                    $outTD[$currentIndex] .= $user->nickname . '<br/>';
                                    $outTD[$currentIndex] .= '<a href=' . URL::base() . 'usermanager/editUser/' . $user->id . '>[' . __('edit') . ']</a>';
                                    $outTD[$currentIndex] .= '</p></td>';
                                    $count++;
                                }

                                foreach ($outTD as $out) {
                                    echo '<tr>' . $out . '</tr>';
                                }
                            }
                            ?>
                        </table>
                        <hr>
                        <p><strong>groups</strong>&nbsp;[<a href=<?php echo URL::base().'usermanager/addGroup'; ?>>add group</a>]</p>
                        <table>
                        <?php
                        if (isset($templateData['groups'])) {
                            $count = 1;
                            $currentIndex = 0;
                            $outTD = array();
                            $outTD[$currentIndex] = '';
                            foreach ($templateData['groups'] as $group) {
                                if ($count == 5) {
                                    $currentIndex++;
                                    $count = 1;
                                    $outTD[$currentIndex] = '';
                                }

                                $outTD[$currentIndex] .= '<td valign="top" width="20%" nowrap=""><p>';
                                $outTD[$currentIndex] .= $group->name . '<br/>';
                                $outTD[$currentIndex] .= '<a href=' . URL::base() . 'usermanager/editGroup/' . $group->id . '>[' . __('edit') . ']</a>';
                                $outTD[$currentIndex] .= '</p></td>';
                                $count++;
                            }

                            foreach ($outTD as $out) {
                                echo '<tr>' . $out . '</tr>';
                            }
                        }
                        ?>
                            </table>
                    </td></tr>
            </table>
        </td>
    </tr>
</table>