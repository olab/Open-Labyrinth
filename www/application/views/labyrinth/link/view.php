<?php if (isset($templateData['map'])) { ?>
    <table width="100%" height="100%" cellpadding="6">
        <tr>
            <td valign="top" bgcolor="#bbbbcb">
                <h4><?php echo __('edit links for Labyrinth "') . $templateData['map']->name . '"'; ?></h4>
                <p><?php __('links represent the options available to the user') ?></p>
                <table width="100%" cellpadding="6">
                    <tr bgcolor="#ffffff"><td>
                            <table border="0" width="100%" cellpadding="2" bgcolor="#eeeeee">
                                <tr><td><p>linked from</p></td><td><p>linked to</p></td></tr>
                                <?php if(isset($templateData['nodes']) and count($templateData['nodes']) > 0) { ?>
                                    <?php foreach($templateData['nodes'] as $node) { ?>
                                        <tr bgcolor="#ffffff">
                                            <td align="left">
                                                <a name="1"></a>
                                                <p>
                                                    <a name="1">linked from '<?php echo $node->title; ?>' (<?php echo $node->id; ?>) [</a>
                                                    <a href="<?php echo URL::base().'linkManager/editLinks/'.$templateData['map']->id.'/'.$node->id; ?>">add/edit links</a>],  [
                                                    <a href="mnode.asp?id=1">preview</a>]
                                                </p>
                                            </td>
                                            <td align="left">
                                                <?php if(count($node->links) > 0) { ?>
                                                    <?php foreach($node->links as $link) { ?>
                                                        <p>
                                                            linked to <?php echo $link->node_2->id; ?> ("<?php echo $link->node_2->title; ?>")
                                                            [<a href="mnode.asp?id=2">preview</a>],
                                                            [<a href="#2">links</a>]
                                                        </p>
                                                    <?php  } ?>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                <?php } ?>
                            </table>
                            <br>
                        </td></tr></table>
            </td>
        </tr>
    </table>
<?php } ?>


