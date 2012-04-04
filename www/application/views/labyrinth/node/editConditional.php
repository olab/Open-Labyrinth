<?php if (isset($templateData['map']) && isset($templateData['node'])) { ?>
    <table width="100%" height="100%" cellpadding="6">
        <tr>
            <td valign="top" bgcolor="#bbbbcb">
                <h4><?php echo __('conditional rules for "') . $templateData['node']->title . '"'; ?></h4>
                <table width="100%" cellpadding="6">
                    <tr bgcolor="#ffffff"><td>
                            <p>previously: <?php echo $templateData['node']->conditional; ?></p>
                            <form action="<?php echo URL::base().'nodeManager/saveConditional/'.$templateData['node']->id.'/'.$templateData['countOfCondidtionFiled']; ?>" method="post">
                                <table width="100%" cellpadding="6" border="0">
                                    <tr><td align="right" width="30%"><p>first add enough nodes</p></td><td><p></p><p>currently=<?php echo $templateData['countOfCondidtionFiled']; ?>, [<a href="<?php echo URL::base().'nodeManager/addConditionalCount/'.$templateData['node']->id.'/'.$templateData['countOfCondidtionFiled']; ?>"><?php echo __('add'); ?></a>] <?php if($templateData['countOfCondidtionFiled'] > 0) { ?>[<a href="<?php echo URL::base().'nodeManager/deleteConditionalCount/'.$templateData['node']->id.'/'.$templateData['countOfCondidtionFiled']; ?>"><?php echo __('delete'); ?></a>]<?php } ?></p><p></p></td></tr>
                                    <tr><td align="right" width="30%"><p>then select which nodes are required</p></td><td><p></p><p>this node requires user to have visited<br>
                                                <?php if(count($templateData['nodes']) > 0) { ?>
                                                <?php for($i = 0; $i < $templateData['countOfCondidtionFiled']; $i++) { ?>
                                                <select name="el_<?php echo $i; ?>">
                                                    <?php foreach($templateData['nodes'] as $node) { ?>
                                                    <option value="<?php echo $node->id; ?>"><?php echo $node->title; ?> [<?php echo $node->id; ?>]</option>
                                                    <?php } ?>
                                                </select>
                                                <br/>
                                                <?php } } ?>
                                                </p><p></p></td></tr>
                                    <tr><td align="right" width="30%"><p>then select the Boolean operator 'and' or 'or' - note that 'and' means all of these nodes must already have been visited and 'or' means that at least one of these nodes should have been visited</p></td><td><p><select name="operator"><option value="and">and</option><option value="or">or</option></select></p></td></tr>
                                    <tr><td align="right" width="30%"><p>then add the message given to the user if they haven't met these conditions</p></td><td><p><textarea name="abs" cols="50" rows="4"><?php echo $templateData['node']->conditional_message; ?></textarea></p></td></tr>
                                    <tr><td align="right" width="30%"><p>and then ...</p></td><td><p><input type="submit" name="Submit" value="<?php echo __('submit'); ?>"></p></td></tr>
                                </table>
                            </form></td></tr>
                </table>
            </td>
        </tr>
    </table>
<?php } ?>