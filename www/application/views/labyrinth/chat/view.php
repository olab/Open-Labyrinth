<?php if (isset($templateData['map'])) { ?>
    <table width="100%" height="100%" cellpadding="6">
        <tr>
            <td valign="top" bgcolor="#bbbbcb">
                <h4><?php echo __('Chats "') . $templateData['map']->name . '"'; ?></h4>
                <table width="100%" cellpadding="6">
                    <tr bgcolor="#ffffff">
                        <td>
                            <table border="0" width="100%" cellpadding="1">
                                <tr>
                                    <td align="left">
                                        <?php if(isset($templateData['chats']) and count($templateData['chats']) > 0) { ?>
                                        <?php foreach($templateData['chats'] as $chat) { ?>
                                            <p><input type="text" value="[[CHAT:<?php echo $chat->id; ?>]]"> <?php echo $chat->stem; ?> 
                                                [<a href="<?php echo URL::base().'chatManager/editChat/'.$templateData['map']->id.'/'.$chat->id.'/'.count($chat->elements); ?>"><?php echo __('edit'); ?></a> 
                                                - <a href="<?php echo URL::base().'chatManager/deleteChat/'.$templateData['map']->id.'/'.$chat->id; ?>"><?php echo __('delete'); ?></a>]</p>
                                        <?php } ?>
                                        <?php } ?>
                                    </td>
                                </tr>
                                <tr><td align="left"><p><a href="<?php echo URL::base().'chatManager/addChat/'.$templateData['map']->id.'/2'; ?>"><?php echo __('Add Chat'); ?></a></p></td></tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
<?php } ?>