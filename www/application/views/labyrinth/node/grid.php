<?php if (isset($templateData['map'])) { ?>
    <table width="100%" height="100%" cellpadding="6">
        <tr>
            <td valign="top" bgcolor="#bbbbcb">
                <h4><?php echo __('NodeGrid "') . $templateData['map']->name . '"'; ?></h4>
                <table width="100%" cellpadding="3">
                    <tr bgcolor="#ffffff"><td align="left">
                            <form action="<?php echo URL::base().'nodeManager/saveGrid/'.$templateData['map']->id; ?>" method="POST">
                                <input type="hidden" name="mapid" value="6">
                                <p><input type="submit" name="Submit" value="submit"></p>
                                <?php if(isset($templateData['nodes']) and count($templateData['nodes']) > 0) { ?>
                                <table border="0" width="50%" cellpadding="3">
                                    <tr><td colspan="3"><hr><p><strong>ungrouped</strong></p></td></tr>
                                    <?php foreach($templateData['nodes'] as $node) { ?>
                                    <tr>
                                        <td valign="top"><p>ID:<?php echo $node->id; ?> <?php if($node->type->name == 'root') echo '[root]'; ?></p></td>
                                        <td valign="top"><p><input type="text" size="50" name="title_<?php echo $node->id; ?>" value="<?php echo $node->title; ?>"></p></td>
                                        <td valign="top"><p><textarea cols="40" rows="4" name="text_<?php echo $node->id; ?>"><?php echo $node->text; ?></textarea></p></td>
                                    </tr>
                                    <?php } ?>
                                </table>
                                <?php } ?>
                                <p><input type="submit" name="Submit" value="submit"></p>
                            </form>
                        </td></tr>
                </table>
            </td>
        </tr>
    </table>
<?php } ?>


