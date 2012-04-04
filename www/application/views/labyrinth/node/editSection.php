<?php if (isset($templateData['section']) and isset($templateData['map'])) { ?>
    <table width="100%" height="100%" cellpadding="6">
        <tr>
            <td valign="top" bgcolor="#bbbbcb">
                <h4><?php echo __('edit node sections "') . $templateData['section']->name . '"'; ?></h4>
                <table width="100%" cellpadding="6">
                    <tr bgcolor="#ffffff"><td>
                            <form action="<?php echo URL::base().'nodeManager/updateNodeSection/'.$templateData['map']->id.'/'.$templateData['section']->id; ?>" method="post">
                            <p><?php echo __('section title'); ?>: <input type="text" name="sectiontitle" size="20" value="<?php echo $templateData['section']->name; ?>"> 
                                [<a href="<?php echo URL::base().'nodeManager/deleteNodeSection/'.$templateData['map']->id.'/'.$templateData['section']->id; ?>"><?php echo __('delete'); ?></a>]
                            </p>
                            <input type="submit" name="Submit" value="<?php echo __('submit'); ?>"><hr>
                            </form>
                            <p><?php echo __('nodes'); ?>:</p>
                            <form action="<?php echo URL::base().'nodeManager/updateSectionNodes/'.$templateData['map']->id.'/'.$templateData['section']->id; ?>" method="post">
                            <?php if(count($templateData['section']->nodes) > 0) { ?>
                                <?php foreach($templateData['section']->nodes as $node) { ?>
                                    <p>
                                        <?php echo $node->node->title; ?>
                                        - <?php echo __('node conditional'); ?>: <?php echo $node->order; ?> - <?php echo __('ordered'); ?>
                                        <select name="node_<?php echo $node->id; ?>">
                                            <?php for($i = 0; $i < count($templateData['section']->nodes); $i++) { ?>
                                                <option value="<?php echo $i; ?>" <?php if($i == $node->order) echo 'selected=""'; ?>><?php echo $i; ?></option>
                                            <?php } ?>
                                        </select> [<a href="<?php echo URL::base().'nodeManager/deleteNodeBySection/'.$templateData['map']->id.'/'.$templateData['section']->id.'/'.$node->node->id; ?>"><?php echo __('delete'); ?></a>]
                                    </p>
                                <?php } ?>
                            <?php } ?>
                            <input type="submit" name="Submit" value="<?php echo __('submit'); ?>">
                            </form>
                            <hr>
                            <form action="<?php echo URL::base().'nodeManager/addNodeInSection/'.$templateData['map']->id.'/'.$templateData['section']->id; ?>" method="post">
                                <p><?php echo __('add an unallocated node to this section'); ?>: 
                                    <select name="mnodeID">
                                        <?php if(isset($templateData['nodes']) and count($templateData['nodes']) > 0) { ?>
                                            <?php foreach($templateData['nodes'] as $node) { ?>
                                                <option value="<?php echo $node->id; ?>"><?php echo $node->title; ?></option>
                                            <?php } ?>
                                        <?php } ?>
                                    </select></p>
                                <input type="submit" name="Submit" value="<?php echo __('submit'); ?>"><br><table border="0" width="100%" cellpadding="1">
                            </form>
                            </table>
                        </td></tr>
                </table>
            </td>
        </tr>
    </table>
<?php } ?>