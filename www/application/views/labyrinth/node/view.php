<?php if (isset($templateData['map'])) { ?>
    <table width="100%" height="100%" cellpadding="6">
        <tr>
            <td valign="top" bgcolor="#bbbbcb">
                <h4><?php echo __('edit nodes of Labyrinth') . ' "' . $templateData['map']->name . '"'; ?></h4>
                <table width="100%" cellpadding="3">
                    <tr bgcolor="#ffffff"><td align="left">
                            <p><br>[<a href="<?php echo URL::base().'nodeManager/addNode/'.$templateData['map']->id; ?>"><?php echo __('add a node'); ?></a>]&nbsp;[<a href="<?php echo URL::base().'nodeManager/sections/'.$templateData['map']->id; ?>">sections</a>]&nbsp;[<a href="<?php echo URL::base().'nodeManager/grid/'.$templateData['map']->id; ?>">nodegrid</a>]</p>
                            <table border="0" width="50%" cellpadding="3">
                                <tr><td colspan="3"><hr></td></tr>
                                <?php if(isset($templateData['nodes'])) { ?>
                                <?php foreach($templateData['nodes'] as $node) { ?>
                                    <tr>
                                        <td align="left">
                                            <p><?php echo $node->title; ?> <?php if($node->type->name == 'root') echo '[root]'; ?> (<?php echo $node->id; ?>)</p>
                                        </td>
                                        <td><p><a href="<?php echo URL::base().'nodeManager/editNode/'.$node->id; ?>">edit</a></p></td>
                                        <td><p><a href="<?php echo URL::base().'linkManager/index/'.$templateData['map']->id; ?>">links</a></p></td>
                                    </tr>
                                <?php } ?>
                                <?php } ?>
                            </table>
                        </td></tr>
                </table>
            </td>
        </tr>
    </table>
<?php } ?>


